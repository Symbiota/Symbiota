<?php
include_once($SERVER_ROOT . '/classes/DwcArchiverBaseManager.php');

class DwcArchiverMedia extends DwcArchiverBaseManager{

	private $redactLocalities = true;
	private $rareReaderCollStr = '';

	public function __construct($connOverride){
		parent::__construct('write', $connOverride);
	}

	public function __destruct(){
		parent::__destruct();
	}

	public function initiateProcess($filePath){
		$this->setFieldArr();
		$this->setSql();

		$this->setFileHandler($filePath);
	}

	private function setFieldArr(){
		$fieldArr = array();
		$termArr = array();
		$fieldArr['coreid'] = 'x.occid';
		$termArr['identifier'] = 'http://purl.org/dc/terms/identifier';
		$fieldArr['identifier'] = 'IFNULL(m.originalurl,m.url) as identifier';
		$termArr['accessURI'] = 'http://rs.tdwg.org/ac/terms/accessURI';
		$fieldArr['accessURI'] = 'IFNULL(NULLIF(m.originalurl,""),m.url) as accessURI';
		$termArr['thumbnailAccessURI'] = 'http://rs.tdwg.org/ac/terms/thumbnailAccessURI';
		$fieldArr['thumbnailAccessURI'] = 'm.thumbnailurl as thumbnailAccessURI';
		$termArr['goodQualityAccessURI'] = 'http://rs.tdwg.org/ac/terms/goodQualityAccessURI';
		$fieldArr['goodQualityAccessURI'] = 'm.url as goodQualityAccessURI';
		$termArr['format'] = 'http://purl.org/dc/terms/format';		//jpg
		$fieldArr['format'] = 'm.format';
		$termArr['type'] = 'http://purl.org/dc/terms/type';		//StillImage or Sound
		$fieldArr['type'] = 'CASE WHEN m.mediaType = "audio" THEN "Sound" ELSE "StillImage" END AS type';
		$termArr['subtype'] = 'http://rs.tdwg.org/ac/terms/subtype';		//Photograph or Recorded Organism
		$fieldArr['subtype'] = 'CASE WHEN m.mediaType = "audio" THEN "Recorded Organism" ELSE "Photograph" END AS subtype';
		$termArr['rights'] = 'http://purl.org/dc/terms/rights';
		$fieldArr['rights'] = ($this->schemaType == 'backup' ? 'm.copyright AS rights' : '');
		$termArr['Owner'] = 'http://ns.adobe.com/xap/1.0/rights/Owner';	//Institution name
		$fieldArr['Owner'] = '';
		$termArr['creator'] = 'http://purl.org/dc/elements/1.1/creator';
		$fieldArr['creator'] = 'IF(m.creatorUid IS NOT NULL,CONCAT_WS(" ",u.firstname,u.lastname),m.creator) AS creator';
		$termArr['UsageTerms'] = 'http://ns.adobe.com/xap/1.0/rights/UsageTerms';	//Creative Commons BY-SA 4.0 license
		$fieldArr['UsageTerms'] = 'm.copyright AS UsageTerms';
		$termArr['WebStatement'] = 'http://ns.adobe.com/xap/1.0/rights/WebStatement';	//https://creativecommons.org/licenses/by-nc-sa/4.0/us/
		$fieldArr['WebStatement'] = '';
		$termArr['caption'] = 'http://rs.tdwg.org/ac/terms/caption';
		$fieldArr['caption'] = 'm.caption';
		$termArr['comments'] = 'http://rs.tdwg.org/ac/terms/comments';
		$fieldArr['comments'] = 'm.notes AS comments';
		$termArr['tag'] = 'http://rs.tdwg.org/ac/terms/tag';
		$fieldArr['tag'] = 'GROUP_CONCAT( tag.keyValue SEPARATOR " | ") AS tag';
		$termArr['providerManagedID'] = 'http://rs.tdwg.org/ac/terms/providerManagedID';	//GUID
		$fieldArr['providerManagedID'] = 'm.recordID AS providerManagedID';
		$termArr['MetadataDate'] = 'http://ns.adobe.com/xap/1.0/MetadataDate';	//timestamp
		$fieldArr['MetadataDate'] = 'm.initialtimestamp AS MetadataDate';
		$termArr['associatedSpecimenReference'] = 'http://rs.tdwg.org/ac/terms/associatedSpecimenReference';	//reference url in portal
		$fieldArr['associatedSpecimenReference'] = '';
		$termArr['metadataLanguage'] = 'http://rs.tdwg.org/ac/terms/metadataLanguage';	//en
		$fieldArr['metadataLanguage'] = '';

		$this->fieldArr['terms'] = $this->trimBySchemaType($termArr);
		$this->fieldArr['fields'] = $this->trimBySchemaType($fieldArr);
	}

	private function trimBySchemaType($dataArr){
		$trimArr = array();
		if($this->schemaType == 'backup'){
			$trimArr = array('Owner', 'UsageTerms', 'WebStatement');
		}
		return array_diff_key($dataArr, array_flip($trimArr));
	}

	private function setSql(){
		if($this->fieldArr){
			$sqlFrag = '';
			foreach($this->fieldArr['fields'] as $fieldName =>$colName){
				if($colName) $sqlFrag .= ', ' . $colName;
				else  $sqlFrag .= ', "" AS ' . $fieldName;
			}
			$sql = 'SELECT '.trim($sqlFrag,', '). ', x.collid
				FROM media m INNER JOIN omexportoccurrences x ON m.occid = x.occid
				LEFT JOIN imagetag tag ON m.mediaID = tag.mediaID
				LEFT JOIN users u ON m.creatorUid = u.uid
				WHERE (x.omExportID = ?) ';
			if($this->redactLocalities){
				if($this->rareReaderCollStr){
					$sql .= 'AND (x.recordSecurity = 0 OR x.collid IN(' . $this->rareReaderCollStr . ')) ';
				}
				else{
					$sql .= 'AND (x.recordSecurity = 0) ';
				}
			}
			$sql .= 'GROUP BY m.mediaID';
			$this->sqlArr[] = $sql;
		}
	}

	public function writeOutMediaData($exportID, $collArr, $serverDomain){
		$recordCnt = 0;
		foreach($this->sqlArr as $sql){
			$urlPathPrefix = $serverDomain . $GLOBALS['CLIENT_ROOT'] . (substr($GLOBALS['CLIENT_ROOT'], -1) == '/' ? '' : '/');
			if (isset($GLOBALS['MEDIA_DOMAIN']) && $GLOBALS['MEDIA_DOMAIN']) {
				$serverDomain = $GLOBALS['MEDIA_DOMAIN'];
			}
			if($stmt = $this->conn->prepare($sql)){
				$stmt->bind_param('i', $exportID);
				$stmt->execute();
				$rs = $stmt->get_result();
				while($r = $rs->fetch_assoc()){
					if(!empty($r['occid'])){
						if ($r['identifier'] && substr($r['identifier'], 0, 1) == '/') $r['identifier'] = $serverDomain . $r['identifier'];
						if ($r['accessURI'] && substr($r['accessURI'], 0, 1) == '/') $r['accessURI'] = $serverDomain . $r['accessURI'];
						if($r['thumbnailAccessURI']){
							if (substr($r['thumbnailAccessURI'], 0, 10) == 'processing') $r['thumbnailAccessURI'] = '';
							elseif (substr($r['thumbnailAccessURI'], 0, 1) == '/') $r['thumbnailAccessURI'] = $serverDomain . $r['thumbnailAccessURI'];
						}
						if($r['goodQualityAccessURI']) {
							if ($r['goodQualityAccessURI'] == 'empty' || substr($r['goodQualityAccessURI'], 0, 10) == 'processing') $r['goodQualityAccessURI'] = '';
							elseif (substr($r['goodQualityAccessURI'], 0, 1) == '/') $r['goodQualityAccessURI'] = $serverDomain . $r['goodQualityAccessURI'];
						}
						$collid = $r['collid'];
						unset($r['collid']);
						if(!$r['rights']) $r['rights'] = $collArr[$collid]['rights'];
						if ($this->schemaType != 'backup') {
							$r['WebStatement'] = $collArr[$collid]['accessrights'];
							$r['Owner'] = $collArr[$collid]['rightsholder'];
							if ($r['rights'] && stripos($r['rights'], 'creativecommons.org') === 0) {
								$r['WebStatement'] = $r['rights'];
								$r['rights'] = '';
								if (!$r['UsageTerms'] && $r['WebStatement']) {
									if (strpos($r['WebStatement'], '/zero/1.0/')) {
										$r['UsageTerms'] = 'CC0 1.0 (Public-domain)';
									}
									elseif (strpos($r['WebStatement'], '/by/')) {
										$r['UsageTerms'] = 'CC BY (Attribution)';
									}
									elseif (strpos($r['WebStatement'], '/by-sa/')) {
										$r['UsageTerms'] = 'CC BY-SA (Attribution-ShareAlike)';
									}
									elseif (strpos($r['WebStatement'], '/by-nc/')) {
										$r['UsageTerms'] = 'CC BY-NC (Attribution-NonCommercial-ShareAlike)';
									}
									elseif (strpos($r['WebStatement'], '/by-nc-sa/')) {
										$r['UsageTerms'] = 'CC BY-NC-SA (Attribution-NonCommercial-ShareAlike)';
									}
								}
							}
							if (!$r['UsageTerms']) $r['UsageTerms'] = 'CC BY-NC-SA (Attribution-NonCommercial-ShareAlike)';
						}
						$r['providerManagedID'] = 'urn:uuid:' . $r['providerManagedID'];
						$r['associatedSpecimenReference'] = $urlPathPrefix . 'collections/individual/index.php?occid=' . $r['occid'];
						if($r['accessURI']){
							$extStr = strtolower(substr($r['accessURI'], strrpos($r['accessURI'], '.') + 1));
							if ($r['format'] == '') {
								if ($extStr == 'jpg' || $extStr == 'jpeg') {
									$r['format'] = 'image/jpeg';
								}
								elseif ($extStr == 'gif') {
									$r['format'] = 'image/gif';
								}
								elseif ($extStr == 'png') {
									$r['format'] = 'image/png';
								}
								elseif ($extStr == 'tiff' || $extStr == 'tif') {
									$r['format'] = 'image/tiff';
								}
								else {
									$r['format'] = '';
								}
							}
						}
						$r['metadataLanguage'] = 'en';
						$this->encodeArr($r);
						$this->addcslashesArr($r);
						$this->writeOutRecord($r);
						$recordCnt++;
					}
				}
				$rs->free();
				$stmt->close();
			}
			else{
				$this->logOrEcho('ERROR writing out to extension file: ' . $stmt->error . "\n");
				//$this->logOrEcho("\tSQL: ".$sql."\n");
			}
		}
		return $recordCnt;
	}

	//Setters and getters
	public function setRedactLocalities($bool){
		if($bool) $this->redactLocalities = true;
		else $this->redactLocalities = false;
	}

	public function setRareReaderCollStr($rareReaderArr){
		if($rareReaderArr){
			$rareStr = implode(',', $rareReaderArr);
			if(preg_match('/^[\d,]+$/' ,$rareStr)) $this->rareReaderCollStr = $rareStr;
		}
	}
}
?>
