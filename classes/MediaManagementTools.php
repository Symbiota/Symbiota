<?php
include_once($SERVER_ROOT.'/classes/Manager.php');
class MediaManagementTools extends Manager {

	//Archiver variables
	private $mediaIddArr;
	private $archiveImages = false;
	private $archiveDir;
	private $deleteThumbnail = false;
	private $deleteWeb = false;
	private $deleteOriginal = false;

	//Image migration variables
	private $collid;
	private $collMetaArr;
	private $transferThumbnail = false;
	private $transferWeb = false;
	private $transferLarge = false;
	private $matchTermThumbnail;
	private $matchTermWeb;
	private $matchTermLarge;
	private $deleteSource = false;
	private $imgRootUrl;
	private $imgRootPath;
	private $imgSubPath;
	private $sourcePathPrefix;
	private $copyOverExistingImages = false;

	private $debugMode = false;

	function __construct() {
		parent::__construct(null,'write');
	}

	function __destruct(){
		parent::__destruct();
	}

	//Archiver functions
	public function archiveImageFiles($mediaIddStart, $limit){
		set_time_limit(1200);
		$this->verboseMode = 3;
		$logPath = $GLOBALS['SERVER_ROOT'] . '/content/logs/imageprocessing/';
		if(!file_exists($logPath)) mkdir($logPath);
		$logPath .= 'imgArchive_' . date('Ym') . '.log';
		$this->setLogFH($logPath);
		if(!$mediaIdStart) $mediaIdStart = 0;
		if(!$this->mediaIdArr){
			$this->logOrEcho('ABORTED: Image ids (mediaId) not supplied');
			return false;
		}
		$this->archiveDir = $GLOBALS['MEDIA_ROOT_PATH'].'/archive_'.date('Y-m-d');
		if(!file_exists($this->archiveDir)){
			if(!mkdir($this->archiveDir)) {
				$this->logOrEcho('ABORTED: unalbe to create archive directory ('.$this->archiveDir.')');
				return false;
			}
		}
		$createHeader = true;
		if(file_exists($this->archiveDir.'/mediaArchiveReport.csv')) $createHeader = false;
		$csvReportFH = fopen($this->archiveDir.'/mediaArchiveReport.csv', 'a');
		if(!$csvReportFH){
			$this->logOrEcho('ABORTED: unalbe to create archive file ('.$this->archiveDir.')');
			return false;
		}
		if($createHeader) fputcsv($csvReportFH, array('mediaId','insertSQL'));
		//Remove images
		$mediaIdFinal = $mediaIdStart;
		$cnt = 0;
		$paramArr = $this->mediaIdArr;
		$typeStr = str_repeat('i', count($paramArr));
		$paramArr[] = $mediaIdStart;
		$typeStr .= 'i';
		$sql = 'SELECT m.* FROM media m ';
		if($this->collid){
			$sql .= 'INNER JOIN omoccurrences o ON m.occid = o.occid ';
		}
		$sql .= 'WHERE (m.mediaID IN(' . trim(str_repeat('?,', count($paramArr)), ' ?') . ')) AND m.mediaType = "image" AND (m.mediaID > ?) ';
		if($this->collid){
			$sql .= 'AND (o.collid = ?) ';
			$paramArr[] = $this->collid;
			$typeStr .= 'i';
		}
		$sql .= 'ORDER BY m.mediaID LIMIT ?';
		$paramArr[] = $limit;
		$typeStr .= 'i';
		//echo $sql;
		if($stmt = $this->conn->prepare){

		}

		$rs = $this->conn->query($sql);
		echo '<ul>';
		while($r = $rs->fetch_assoc()){
			$mediaId = $r['mediaID'];
			$derivArr = array('tn'=>1,'web'=>1,'lg'=>1);
			$delArr = array();
			if(!$r['thumbnailurl']) unset($derivArr['tn']);
			if(!$r['url']) unset($derivArr['web']);
			if(!$r['originalurl']) unset($derivArr['lg']);
			//Transfer images to archive folder
			if($this->deleteThumbnail && isset($derivArr['tn'])){
				if($this->archiveImage($r['thumbnailurl'], $mediaId)){
					$delArr['tn'] = 1;
					unset($derivArr['tn']);
				}
			}
			if($this->deleteWeb && isset($derivArr['web'])){
				if($this->archiveImage($r['url'], $mediaId)){
					$delArr['web'] = 1;
					unset($derivArr['web']);
				}
			}
			if($this->deleteOriginal && isset($derivArr['lg'])){
				if($this->archiveImage($r['originalurl'], $mediaId)){
					$delArr['lg'] = 1;
					unset($derivArr['lg']);
				}
			}
			//Place INSERT sql into file in case record needs to be reintalled
			$insertArr = $r;
			unset($insertArr['mediaID']);
			unset($insertArr['initialtimestamp']);
			$insertStr = '';
			foreach($insertArr as $v){
				if($v) $insertStr .= ', "'.$v.'"';
				else $insertStr .= ', NULL';
			}
			$insSql = 'INSERT INTO media ('.implode(',', array_keys($insertArr)).') VALUES('.substr($insertStr,1).');';
			fputcsv($csvReportFH,array($mediaId,'record deleted',$insSql));
			//Adjust database record
			$sqlImg = '';
			if($derivArr){
				if(isset($delArr['tn'])) $sqlImg .= ', thumbnailurl = NULL';
				if(isset($delArr['web'])) $sqlImg .= ', url = "empty"';
				if(isset($delArr['lg'])) $sqlImg .= ', originalurl = NULL';
				if($sqlImg) $sqlImg = 'UPDATE media SET '.substr($sqlImg,1).' WHERE mediaID = '.$mediaId;
			}
			else{
				$sqlImg = 'DELETE FROM media WHERE mediaID = '.$mediaId;
			}
			if($sqlImg){
				if(!$this->conn->query($sqlImg)){
					$this->logOrEcho('ERROR: '.$this->conn->error,1);
					$this->logOrEcho('sqlImg: '.$sqlImg,2);
				}
			}
			if($cnt && $cnt%100 == 0){
				$this->logOrEcho($cnt.' media checked');
				ob_flush();
				flush();
			}
			$cnt++;
			$mediaIdFinal = $mediaId;
		}
		echo '</ul>';
		$rs->free();
		fclose($csvReportFH);
		$this->logOrEcho('Done! '.$cnt.' media handled');
		return $mediaIdFinal;
	}

	private function archiveImage($imgFilePath, $mediaId){
		$status = false;
		if($imgFilePath){
			if(substr($imgFilePath,0,4) == 'http') {
				$imgFilePath = substr($imgFilePath,strpos($imgFilePath,"/",9));
			}
			$path = str_replace($GLOBALS['MEDIA_ROOT_URL'], $GLOBALS['MEDIA_ROOT_PATH'], $imgFilePath);
			if(is_writable($path)){
				if($this->archiveImages){
					$fileName = substr($path, strrpos($path, '/'));
					if(rename($path,$this->archiveDir.'/'.$fileName)) $status = true;
				}
				else{
					if(unlink($path)) $status = true;
				}
			}
			else{
				$this->logOrEcho('ERROR: image unwritable (mediaId: <a href="' . $GLOBALS['CLIENT_ROOT'] . '/imagelib/imgdetails.php?mediaid=' . $mediaId . '" target="_blank">' . $mediaId . '</a>, path: ' . htmlspecialchars($path, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . ')');
			}
		}
		return $status;
	}

	//Image migration functions
	public function migrateFieldDerivatives($mediaIdStart, $limit){
		set_time_limit(1200);
		$this->verboseMode = 3;
		$logPath = $GLOBALS['SERVER_ROOT'] . '/content/logs/imageprocessing/';
		if(!file_exists($logPath)) mkdir($logPath);
		$logPath .= 'fieldDerivativeMigration_' . date('Ym') . '.log';
		$this->setLogFH($logPath);
		//Needs to be reworked
		$this->debugMode = true;
		$mediaId = 0;
		if(is_numeric($limit) && is_numeric($this->collid) && $this->imgRootUrl && $this->imgRootPath){
			if($this->transferThumbnail && $this->transferWeb && $this->transferLarge){
				if($this->matchTermThumbnail || $this->matchTermWeb || $this->matchTermLarge){
					echo '<ul>';
					$this->setTargetPaths();
					$dirCnt = 0;
					do{
						$mediaArr = array();
						$pathFrag = date('Ym');
						if(!file_exists($this->imgRootPath.$pathFrag)) mkdir($this->imgRootPath.$pathFrag);
						$subDir = str_pad($dirCnt,4,'0',STR_PAD_LEFT);
						while(file_exists($this->imgRootPath.$pathFrag.'/'.$subDir)){
							$dirCnt ++;
							$subDir = str_pad($dirCnt,4,'0',STR_PAD_LEFT);
						}
						$pathFrag .= '/'.$subDir;
						$dirCnt ++;
						$sql = 'SELECT mediaID, thumbnailurl, url, originalurl FROM media WHERE occid IS NULL ';
						if($this->collid) $sql = 'SELECT m.thumbnailurl, m.url, m.originalurl FROM media m INNER JOIN omoccurrences o ON m.occid = o.occid WHERE o.collid = '.$this->collid;
						if($this->matchTermThumbnail) $sql .= ' AND thumbnailurl LIKE "'.$this->matchTermThumbnail.'%" ';
						if($this->matchTermWeb) $sql .= ' AND url LIKE "'.$this->matchTermWeb.'%" ';
						if($this->matchTermLarge) $sql .= ' AND originalurl LIKE "'.$this->matchTermLarge.'%" ';
						if($mediaIdStart && is_numeric($mediaIdStart)) $sql .= 'AND mediaID > '.$mediaIdStart.' ';
						$sql .= 'ORDER BY mediaID ';
						$sql .= 'LIMIT 1000';
						echo $sql.'<br/>';
						$rs = $this->conn->query($sql);
						while($r = $rs->fetch_object()){
							$mediaId = $r->mediaID;
							if($this->transferThumbnail){
								$filePath = $pathFrag;
								if(substr($r->thumbnailurl,-1) != '/') $filePath .= '/';
								echo $r->thumbnailurl.' => '.$this->imgRootPath.$filePath.'<br/>';
							}
							if($this->transferWeb){
								$filePath = $pathFrag;
								if(substr($r->url,-1) != '/') $filePath .= '/';
								echo $r->url.' => '.$this->imgRootPath.$filePath.'<br/>';
							}
							if($this->transferLarge){
								$filePath = $pathFrag;
								if(substr($r->originalurl,-1) != '/') $filePath .= '/';
								echo $r->originalurl.' => '.$this->imgRootPath.$filePath.'<br/>';
							}
							$limit--;
							if($limit < 1) break;
						}
						$rs->free();
						$this->databaseMediaRecord($mediaArr);
						$cnt = count($mediaArr);
						$this->logOrEcho($cnt.' image records remapped');
						unset($mediaArr);
					}while($cnt && $limit);
					echo '</ul>';
				}
			}
		}
		return $mediaId;
	}

	public function migrateCollectionDerivatives($mediaIdStart, $limit){
		//Migrates images based on catalog number; NULL or weak catalogNumbers are skipped
		set_time_limit(1200);
		$this->verboseMode = 3;
		$logPath = $GLOBALS['SERVER_ROOT'] . '/content/logs/imageprocessing/';
		if(!file_exists($logPath)) mkdir($logPath);
		$logPath .= 'imgMigration_' . date('Ym') . '.log';
		$this->setLogFH($logPath);
		if($this->collid && is_numeric($limit) && $this->imgRootUrl && $this->imgRootPath){
			if($this->transferThumbnail || $this->transferWeb || $this->transferLarge){
				if($this->matchTermThumbnail || $this->matchTermWeb || $this->matchTermLarge){
					echo '<ul>';
					$this->setTargetPaths();
					$processingCnt = 0;
					$sqlBase = 'FROM media m INNER JOIN omoccurrences o ON m.occid = o.occid WHERE o.collid = ' . $this->collid . ' AND m.mediaType = "image" ';
					if($this->matchTermThumbnail) $sqlBase .= 'AND thumbnailurl LIKE "'.$this->matchTermThumbnail.'%" ';
					if($this->matchTermWeb) $sqlBase .= 'AND url LIKE "'.$this->matchTermWeb.'%" ';
					if($this->matchTermLarge) $sqlBase .= 'AND originalurl LIKE "'.$this->matchTermLarge.'%" ';
					$targetCount = 0;
					$sqlCount = 'SELECT COUNT(m.mediaID) as cnt '.$sqlBase.' ';
					if($mediaIdStart && is_numeric($mediaIdStart)) $sqlCount .= 'AND mediaID > '.$mediaIdStart.' ';
					$rsCount = $this->conn->query($sqlCount);
					while($rCount = $rsCount->fetch_object()){
						$targetCount = $rCount->cnt;
					}
					$rsCount->free();
					$this->logOrEcho('Starting remapping of '.$limit.' out of '.$targetCount.' possible target media ');
					do{
						$mediaArr = array();
						$sql = 'SELECT m.mediaID, m.thumbnailurl, m.url, m.originalurl, o.catalognumber, o.occid '.$sqlBase;
						if($mediaIdStart && is_numeric($mediaIdStart)) $sql .= 'AND mediaID > '.$mediaIdStart.' ';
						$sql .= 'ORDER BY mediaID LIMIT 100';
						//$this->logOrEcho('sql used: '. $sql);
						$rs = $this->conn->query($sql);
						while($r = $rs->fetch_object()){
							$mediaIdStart = $r->mediaID;
							$pathFrag = '';
							if(preg_match('/^(\D*).*(\d{4,})/', $r->catalognumber, $m)){
								$catNum = $m[2];
								if($catNum){
									if(strlen($catNum)<8) $catNum = str_pad($catNum,8,'0',STR_PAD_LEFT);
									$pathFrag = $m[1].substr($catNum,0,strlen($catNum)-4).'/';
								}
							}
							if(!$pathFrag) $pathFrag = date('Ymd').'/';
							if(!file_exists($this->imgRootPath.$pathFrag)) mkdir($this->imgRootPath.$pathFrag);
							$this->logOrEcho($processingCnt.': Processing: <a href="../../individual/index.php?occid=' . htmlspecialchars($r->occid, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '" target="_blank">' . htmlspecialchars($r->occid, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</a>');
							if($this->transferThumbnail && $r->thumbnailurl){
								$fileName = basename($r->thumbnailurl);
								$targetPath = $this->imgRootPath.$pathFrag.$fileName;
								$targetUrl = $this->imgRootUrl.$pathFrag.$fileName;
								$thumbPath = $this->getLocalPath($r->thumbnailurl);
								if(file_exists($thumbPath)){
									if($this->copyOverExistingImages || !file_exists($targetPath)){
										if(copy($thumbPath, $targetPath)){
											$mediaArr[$r->mediaID]['thumbnailUrl'] = $targetUrl;
											$this->logOrEcho('Copied: '.$thumbPath.' => '.$targetPath,1);
											if($this->deleteSource){
												if(unlink($thumbPath)){
													$this->logOrEcho('Source deleted: '.$thumbPath,1);
												}
												else{
													$this->logOrEcho('ERROR deleting source (file permissions?): '.$thumbPath,1);
												}
											}
										}
									}
									else{
										$this->logOrEcho('Skipped: target file already exists (' . $targetPath . ')', 1);
									}
								}
								else{
									$this->logOrEcho('Skipped: source thumbnail does not exist (' . $thumbPath . ')', 1);
								}
							}
							if($this->transferWeb && $r->url){
								$fileName = basename($r->url);
								$targetPath = $this->imgRootPath.$pathFrag.$fileName;
								$targetUrl = $this->imgRootUrl.$pathFrag.$fileName;
								$urlPath = $this->getLocalPath($r->url);
								if(file_exists($urlPath)){
									if($this->copyOverExistingImages || !file_exists($targetPath)){
										if(copy($urlPath, $targetPath)){
											$mediaArr[$r->mediaID]['url'] = $targetUrl;
											$this->logOrEcho('Copied: '.$urlPath.' => '.$targetPath,1);
											if($this->deleteSource){
												if(unlink($urlPath)){
													$this->logOrEcho('Source delete: '.$urlPath,1);
												}
												else{
													$this->logOrEcho('ERROR deleting source (file permissions?): '.$urlPath,1);
												}
											}
										}
									}
									else{
										$this->logOrEcho('Skipped: target file already exists (' . $targetPath . ')', 1);
									}
								}
								else{
									$this->logOrEcho('Skipped: source file does not exist (' . $urlPath . ')', 1);
								}
							}
							if($this->transferLarge && $r->originalurl){
								$fileName = basename($r->originalurl);
								$targetPath = $this->imgRootPath.$pathFrag.$fileName;
								$targetUrl = $this->imgRootUrl.$pathFrag.$fileName;
								$origPath = $this->getLocalPath($r->originalurl);
								if(file_exists($origPath)){
									if($this->copyOverExistingImages || !file_exists($targetPath)){
										if(copy($origPath, $targetPath)){
											$mediaArr[$r->mediaID]['originalUrl'] = $targetUrl;
											$this->logOrEcho('Copied: '.$origPath.' => '.$targetPath,1);
											if($this->deleteSource){
												if(unlink($origPath)){
													$this->logOrEcho('Source deleted: '.$origPath,1);
												}
												else{
													$this->logOrEcho('ERROR deleting source (file permissions?): '.$origPath,1);
												}
											}
										}
									}
									else{
										$this->logOrEcho('Skipped: target file already exists (' . $targetPath . ')', 1);
									}
								}
								else{
									$this->logOrEcho('Skipped: source file does not exist (' . $origPath . ')', 1);
								}
							}
							$processingCnt++;
							$limit--;
							if($limit < 1) break;
						}
						$rs->free();
						$this->databaseMediaRecord($mediaArr);
						$cnt = count($mediaArr);
						$this->logOrEcho($processingCnt.' media records remapped ('.date('Y-m-d H:i:s').')');
						unset($mediaArr);
					}while($cnt && $limit);
					echo '</ul>';
				}
			}
		}
		return $mediaIdStart;
	}

	private function getLocalPath($imageUrl){
		if($this->sourcePathPrefix){
			$adjustedUrl = str_replace($this->sourcePathPrefix, $GLOBALS['MEDIA_ROOT_PATH'], $imageUrl);
			if(file_exists($adjustedUrl)) return $adjustedUrl;
		}
		if(file_exists($imageUrl)){
			return $imageUrl;
		}
		if(strpos($imageUrl, $GLOBALS['MEDIA_ROOT_URL']) !== false){
			$adjustedUrl = str_replace($GLOBALS['MEDIA_ROOT_URL'], $GLOBALS['MEDIA_ROOT_PATH'], $imageUrl);
			if(file_exists($adjustedUrl)) return $adjustedUrl;
		}
		$prefix = substr($GLOBALS['MEDIA_ROOT_PATH'], 0, strlen($GLOBALS['MEDIA_ROOT_PATH']) - strlen($GLOBALS['MEDIA_ROOT_URL']));
		if(file_exists($prefix.$imageUrl)){
			$this->sourcePathPrefix = $prefix;
			return $prefix.$imageUrl;
		}
		return $imageUrl;
	}

	private function databaseMediaRecord($inputArr){
		$status = false;
		$fieldArr = array('originalUrl' => 's', 'url' => 's', 'thumbnailUrl' => 's', 'mediamd5' => 's', 'pixelxdimension' => 'i', 'pixelydimension' => 'i', 'filesize' => 'i', 'filesizethumbnail' => 'i', 'filesizemedium' => 'i');
		foreach($inputArr as $mediaID -> $mediaArr){
			$inputFieldArr = array();
			$paramArr = array();
			$typeStr = '';
			foreach($mediaArr as $field => $value){
				if(isset($fieldArr[$field])){
					$inputFieldArr[] = $field;
					$paramArr[] = $value;
					$typeStr .= $fieldArr[$field];
				}
			}
			if($inputFieldArr){
				$sql = 'UPDATE media SET ' . implode(' = ?, ', $inputFieldArr) . ' = ? WHERE mediaID = ?';
				$paramArr[] = $mediaID;
				$typeStr .= 'i';
				if($stmt = $this->conn->prepare($sql)){
					$stmt->bind_param($typeStr, ...$paramArr);
					$stmt->execute();
					if($stmt->error){
						$this->outputStr('ERROR saving new paths (mediaID = ' . $mediaID . '): ' . $stmt->error, 1);
					}
					elseif(!$stmt->affected_rows){
						$this->outputStr('Nothing changed (mediaID = ' . $mediaID . ')', 1);
					}
					else $status = true;
					$stmt->close();
				}
			}
		}
		return $status;
	}

	private function setTargetPaths(){
		if($this->imgRootPath && $this->imgRootUrl){
			if($this->collid){
				$this->imgRootPath .= $this->collMetaArr['code'].'/';
				$this->imgRootUrl .= $this->collMetaArr['code'].'/';
			}
			elseif($this->collid === 0){
				$this->imgRootPath .= 'fieldimg/';
				$this->imgRootUrl .= 'fieldimg/';
			}
			if(!file_exists($this->imgRootPath)) mkdir($this->imgRootPath);
		}
	}

	//Misc data return functions
	public function getCollectionMeta(){
		$retArr = array();
		$sql = 'SELECT collid, collectionname, CONCAT_WS(":",institutioncode,collectioncode) as instcode FROM omcollections ORDER BY collectionname';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->collid] = $r->collectionname . ' (' . $r->instcode . ')';
		}
		$rs->free();
		return $retArr;
	}

	//Setters and getters
	public function setCollid($id){
		if(is_numeric($id)){
			$this->collid = $id;
			$sql = 'SELECT collectionname, CONCAT_WS("_",institutioncode,collectioncode) as instcode FROM omcollections WHERE collid = ?';
			if($stmt = $this->conn->prepare($sql)){
				$stmt->bind_param('i', $id);
				$stmt->execute();
				$rs = $stmt->get_result();
				while($r = $rs->fetch_object()){
					$this->collMetaArr['name']= $r->collectionname;
					$this->collMetaArr['code']= $r->instcode;
				}
				$rs->free();
				$stmt->close();
			}
		}
	}

	//Archiver setters and getters
	public function setMediaIdArr($mediaIdStr){
		$mediaIdStr = str_replace(';', ' ', $mediaIdStr);
		$mediaIdStr = str_replace(',', ' ', $mediaIdStr);
		$mediaIdStr = trim(preg_replace('/\s\s+/',' ',$mediaIdStr),',');
		if($mediaIdStr){
			if(preg_match('/^[\d\s]+$/',$mediaIdStr)){
				$this->mediaIdArr = explode(' ',$mediaIdStr);
			}
		}
	}

	public function setArchiveImages($b){
		if($b) $this->archiveImages = true;
	}

	public function setDeleteThumbnail($delTn){
		if($delTn) $this->deleteThumbnail = true;
		else $this->deleteThumbnail = false;
	}

	public function setDeleteWebImage($delWeb){
		if($delWeb) $this->deleteWeb = true;
		else $this->deleteWeb = false;
	}

	public function setDeleteOriginal($delOrig){
		if($delOrig) $this->deleteOriginal = true;
		else $this->deleteOriginal = false;
	}

	//Image migration setters and getter
	public function setTransferThumbnail($bool){
		if($bool) $this->transferThumbnail = true;
		else $this->transferThumbnail = false;
	}

	public function setTransferWeb($bool){
		if($bool) $this->transferWeb = true;
		else $this->transferWeb = false;
	}

	public function setTransferLarge($bool){
		if($bool) $this->transferLarge = true;
		else $this->transferLarge = false;
	}

	public function setMatchTermThumbnail($str){
		$this->matchTermThumbnail = $str;
	}

	public function setMatchTermWeb($str){
		$this->matchTermWeb = $str;
	}

	public function setMatchTermLarge($str){
		$this->matchTermLarge = $str;
	}

	public function setDeleteSource($bool){
		$this->deleteSource = $bool;
	}

	public function setImgRootUrl($url){
		if(substr($url, -1) != '/') $url .= '/';
		$this->imgRootUrl = $url;
	}

	public function setImgRootPath($url){
		if(substr($url, -1) != '/') $url .= '/';
		$this->imgRootPath = $url;
	}

	public function setImgSubPath($path){
		$this->imgSubPath = $path;
	}

	public function setCopyOverExistingImages($bool){
		if($bool) $this->copyOverExistingImages = true;
		else $this->copyOverExistingImages = false;
	}
}
?>
