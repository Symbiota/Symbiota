<?php
include_once($SERVER_ROOT.'/content/lang/collections/misc/collstats.'.$LANG_TAG.'.php');
class OccurrenceSearchSupport {

	private $conn;
	private $collidStr = '';
	private $collArrIndex = 0;

	public function __construct($conn){
		$this->conn = $conn;
 	}

	public function __destruct(){
	}

	public function getFullCollectionList($catIdStr = '', $limitByImages = false){
		if(!preg_match('/^[,\d]+$/',$catIdStr)) $catIdStr = '';
		//Set collection array
		/*
		$collIdArr = array();
		if($this->collidStr){
			$cArr = explode(';',$this->collidStr);
			$collIdArr = explode(',',$cArr[0]);
			if(isset($cArr[1])) $collIdArr = $cArr[1];
		}
		*/
		//Set collections
		$sql = 'SELECT c.collid, c.institutioncode, c.collectioncode, c.collectionname, c.icon, c.colltype, ccl.ccpk, '.
			'cat.category, cat.icon AS caticon, cat.acronym '.
			'FROM omcollections c INNER JOIN omcollectionstats s ON c.collid = s.collid '.
			'LEFT JOIN omcollcatlink ccl ON c.collid = ccl.collid '.
			'LEFT JOIN omcollcategories cat ON ccl.ccpk = cat.ccpk '.
			'WHERE s.recordcnt > 0 AND (cat.inclusive IS NULL OR cat.inclusive = 1 OR cat.ccpk = 1) ';
		if($limitByImages) $sql .= 'AND s.dynamicproperties NOT LIKE \'%imgcnt":"0"%\' ';
		$sql .= 'ORDER BY ccl.sortsequence, cat.category, c.sortseq, c.CollectionName ';
		//echo "<div>SQL: ".$sql."</div>";
		$result = $this->conn->query($sql);
		$collArr = array();
		while($r = $result->fetch_object()){
			$collType = (stripos($r->colltype, "observation") !== false?'obs':'spec');
			if($r->ccpk){
				if(!isset($collArr[$collType]['cat'][$r->ccpk]['name'])){
					$collArr[$collType]['cat'][$r->ccpk]['name'] = $r->category;
					$collArr[$collType]['cat'][$r->ccpk]['icon'] = $r->caticon;
					$collArr[$collType]['cat'][$r->ccpk]['acronym'] = $r->acronym;
				}
				$collArr[$collType]['cat'][$r->ccpk][$r->collid]["instcode"] = $r->institutioncode;
				$collArr[$collType]['cat'][$r->ccpk][$r->collid]["collcode"] = $r->collectioncode;
				$collArr[$collType]['cat'][$r->ccpk][$r->collid]["collname"] = $r->collectionname;
				$collArr[$collType]['cat'][$r->ccpk][$r->collid]["icon"] = $r->icon;
			}
			else{
				$collArr[$collType]['coll'][$r->collid]["instcode"] = $r->institutioncode;
				$collArr[$collType]['coll'][$r->collid]["collcode"] = $r->collectioncode;
				$collArr[$collType]['coll'][$r->collid]["collname"] = $r->collectionname;
				$collArr[$collType]['coll'][$r->collid]["icon"] = $r->icon;
			}
		}
		$result->free();

		$retArr = array();
		//Modify sort so that default catid is first
		if($catIdStr){
			$catIdArr = explode(',', $catIdStr);
			if($catIdArr){
				foreach($catIdArr as $catId){
					if(isset($collArr['spec']['cat'][$catId])){
						$retArr['spec']['cat'][$catId] = $collArr['spec']['cat'][$catId];
						unset($collArr['spec']['cat'][$catId]);
					}
					elseif(isset($collArr['obs']['cat'][$catId])){
						$retArr['obs']['cat'][$catId] = $collArr['obs']['cat'][$catId];
						unset($collArr['obs']['cat'][$catId]);
					}
				}
			}
		}
		foreach($collArr as $t => $tArr){
			foreach($tArr as $g => $gArr){
				foreach($gArr as $id => $idArr){
					$retArr[$t][$g][$id] = $idArr;
				}
			}
		}
		return $retArr;
	}

	public function outputFullCollArr($collGrpArr, $targetCatID = '', $displayIcons = true, $displaySearchButtons = true, $collTypeLabel = '', $uniqGrouping=''){
		global $CLIENT_ROOT, $LANG;
		$catSelArr = array();
		$collSelArr = array();
		if(isset($_POST['cat'])) $catSelArr = $_POST['cat'];
		if(isset($_POST['db'])) $collSelArr = $_POST['db'];
		$targetCatArr = array();
		$targetCatID = (string)$targetCatID;
		if($targetCatID != '') $targetCatArr = explode(',', $targetCatID);
		elseif($GLOBALS['DEFAULTCATID'] != '') $targetCatArr = explode(',', $GLOBALS['DEFAULTCATID']);
		$buttonTxt = isset($LANG['BUTTON_NEXT'])?$LANG['BUTTON_NEXT']:'Search;';
		$replacedUniqGrouping = str_replace('-',' ',$uniqGrouping);
		$buttonTxtParenthetical = $uniqGrouping === '' ? '' : ' (' . $replacedUniqGrouping . ')';
		$buttonStr = '<button aria-label="' . $buttonTxt . $buttonTxtParenthetical . '" type="submit" value="search">' . $buttonTxt . $buttonTxtParenthetical . '</button>';
		$collCnt = 0;
		$borderStyle = ($displayIcons?'margin:10px;padding:10px 20px;border:inset':'margin-left:10px;');
		echo '<div>';
		if(isset($collGrpArr['cat'])){
			$categoryArr = $collGrpArr['cat'];
			if($displaySearchButtons) echo '<div class="search-button-div sticky-buttons">'.$buttonStr.'</div>';
			?>
			<section class="gridlike-form">
				<?php
				$cnt = 0;
				foreach($categoryArr as $catid => $catArr){
					$name = $catArr['name'];
					if($catArr['acronym']) $name .= ' ('.$catArr['acronym'].')';
					$catIcon = $catArr['icon'];
					unset($catArr['name']);
					unset($catArr['acronym']);
					unset($catArr['icon']);
					$idStr = $this->collArrIndex.'-'.$catid;
					?>
					<section class="gridlike-form-row bottom-breathing-room-relative">
						<?php
						if($displayIcons){
							?>
							<div style="<?php echo ($catIcon?'width:40px;height:35px;':''); ?>">
								<?php
								if($catIcon){
									$catIcon = (substr($catIcon,0,6)=='images'?$CLIENT_ROOT:'').$catIcon;
									echo '<img src="'.$catIcon.'" style="border:0px;width:30px;height:30px;" />';
								}
								?>
							</div>
							<?php
						}
						?>
						<div>
							<div>
								<?php
								$catSelected = false;
								if(!$catSelArr && !$collSelArr) $catSelected = true;
								elseif(in_array($catid, $catSelArr)) $catSelected = true;
								// $categoryLinkStr = '<a href="#" onclick="toggleCat(' . $idStr . ');return false;">' . $name. '</a>';
								$ariaLabel = $name . '(' . $collTypeLabel . ')' . '-' . $uniqGrouping;
								echo '<input aria-label="' . $ariaLabel . '"   data-role="none" id="cat-' . $idStr . '-' . $collTypeLabel . '-' . $uniqGrouping . '-Input" name="cat[]" value="' . $catid.'" type="checkbox" onclick="selectAllCat(this,\'cat-' . $idStr . '\')" ' . ($catSelected?'checked':'') . ' />';
								// echo '<label  for="cat-' . $idStr . '-' . $collTypeLabel . '-' . $uniqGrouping . '-Input">' . $name . "(" . $collTypeLabel . ")" . '</label>';
								echo $name . "(" . $collTypeLabel . ")";
								?>
							</div>
						</div>
						<div>
							<div>
								<a href="#" class="condense-expand-flex" onclick="toggleCat('<?php echo htmlspecialchars($idStr, HTML_SPECIAL_CHARS_FLAGS); ?>');return false;">
								<div class="condense-expand-button-set">
									<img id="plus-<?php echo $idStr; ?>" src="<?php echo $CLIENT_ROOT; ?>/images/plus_sm.png" style="display:none;" alt="plus sign to expand menu" />
									<img id="minus-<?php echo $idStr; ?>" src="<?php echo $CLIENT_ROOT; ?>/images/minus_sm.png" alt="minus sign to condense menu" />
									<p id="ptext-<?php echo $idStr; ?>" style="<?php echo ((0 != $catid)?'':'display:none;') ?>">
										<?php echo $LANG['CONDENSE'] ?>
									</p>
									<p id="mtext-<?php echo $idStr; ?>" style="<?php echo ((0 != $catid)?'display:none;':'') ?>" >
										<?php echo $LANG['EXPAND'] ?>
									</p>
								</div>
								</a>
							</div>
						</div>
					</section>
					<section id="cat-<?php echo $idStr ?>" class="gridlike-form-row bottom-breathing-room-relative">
						<div>
							<fieldset>
								<legend>
									<?php 
									echo $name; 
									$specimenLegendTxt = isset($LANG['SPECIMEN']) ? $LANG['SPECIMEN'] : "Specimen";
									$observationLegendTxt = isset($LANG['OBSERVATION']) ? $LANG['OBSERVATION'] : "Observation";
									$isSpecimen = $collTypeLabel === "Specimen";
									$isObservation = $collTypeLabel === "Observation";
									$outputTxt = 'deleteMe';
									if($isSpecimen) $outputTxt = $specimenLegendTxt;
									if($isObservation) $outputTxt = $observationLegendTxt;
									?> 
									(<?php echo $outputTxt ?>)
								</legend>
								<section class="gridlike-form">
									<?php
									foreach($catArr as $collid => $collName2){
										?>
										<section class="gridlike-form-row bottom-breathing-room-relative">
											<?php
											if($displayIcons){
												?>
												<div style="width:40px;height:35px">
													<?php
													if($collName2["icon"]){
														$cIcon = (substr($collName2["icon"],0,6)=='images'?$CLIENT_ROOT:'').$collName2["icon"];
														?>
														<a href = '<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/collections/misc/collprofiles.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>'>
															<img src="<?php echo htmlspecialchars($cIcon, HTML_SPECIAL_CHARS_FLAGS); ?>" style="border:0px;width:30px;height:30px;" alt='Icon associated with collection <?php echo isset($collName2["collname"]) ? substr($collName2["collname"],0, 40) : substr($idStr,0, 40) ?>' />
														</a>
														<?php
													}
													?>
												</div>
												<?php
											}
											?>
											<div>
												<?php
												echo '<input aria-label="select collection ' . $collid . '" id="coll-' . $collid . '-' . $idStr . '" data-role="none" name="db[]" value="'.$collid.'" type="checkbox" class="cat-'.$idStr.'" onclick="unselectCat(\'cat-'.$idStr.'-Input\')" '.($catSelected || !$collSelArr || in_array($collid, $collSelArr)?'checked':'').' />';
												// echo '<label>Select/Deselect</label>';
												?>
											</div>
											<div>
												<div class="collectiontitle">
													<?php
													$codeStr = ' ('.$collName2['instcode'];
													if($collName2['collcode']) $codeStr .= '-'.$collName2['collcode'];
													$codeStr .= ')';
													echo '<div class="collectionname">'.$collName2["collname"].'</div><div class="collectioncode">'.$codeStr.'</div>';
													?>
													<a href='<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/collections/misc/collprofiles.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>' target="_blank">
														<?php echo (isset($LANG['MORE_INFO'])?$LANG['MORE_INFO']:'more info...'); ?>
													</a>
												</div>
											</div>
										</section>
										<?php
										$collCnt++;
									}
									?>
								</section>
							</fieldset>
						</div>
					</section>
					<?php
					$cnt++;
				}
				?>
			</section>
			<?php
		}
		if(isset($collGrpArr['coll'])){
			$collArr = $collGrpArr['coll'];
			?>
			<table style="float:left;width:80%;">
				<?php
				foreach($collArr as $collid => $cArr){
					?>
					<section class="gridlike-form-row bottom-breathing-room-relative">
						<?php
						if($displayIcons){
							?>
							<div style="<?php ($cArr["icon"]?'width:40px;height:35px':''); ?>">
								<?php
								if($cArr["icon"]){
									$cIcon = (substr($cArr["icon"],0,6)=='images'?$CLIENT_ROOT:'').$cArr["icon"];
									?>
									<a href = '<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/collections/misc/collprofiles.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>'><img src="<?php echo htmlspecialchars($cIcon, HTML_SPECIAL_CHARS_FLAGS); ?>" style="border:0px;width:30px;height:30px;" /></a>
									<?php
								}
								?>
								&nbsp;
							</div>
							<?php
						}
						?>
						<div style="width:25px;padding-top:8px;">
							<?php
							echo '<input aria-label="select collection ' . $collid . '" data-role="none" id="collection-' . $collid . '" name="db[]" value="' . $collid . '" type="checkbox" onclick="uncheckAll()" '.(!$collSelArr || in_array($collid, $collSelArr)?'checked':'').' />';
							// echo '<label for="' . $collid . '">Boop</label>';
							?>
						</div>
						<div>
							<div class="collectiontitle">
								<?php
								$codeStr = '('.$cArr['instcode'];
								if($cArr['collcode']) $codeStr .= '-'.$cArr['collcode'];
								$codeStr .= ')';
								echo '<div class="collectionname">'.$cArr["collname"].'</div> <div class="collectioncode">'.$codeStr.'</div> ';
								?>
								<a href = '<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/collections/misc/collprofiles.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>' target="_blank">
									<?php echo (isset($LANG['MORE_INFO'])?$LANG['MORE_INFO']:'more info...'); ?>
								</a>
							</div>
						</div>
					</section>
					<?php
					$collCnt++;
				}
				?>
			</table>
			<?php
			if($displaySearchButtons){
				if(!isset($collGrpArr['cat'])){
					?>
					<div style="float:right;position:absolute;top:<?php echo count($collArr)*5; ?>px;right:0px;">
						<?php echo $buttonStr; ?>
					</div>
					<?php
				}
				if(count($collArr) > 40){
					?>
					<div style="float:right;position:absolute;top:<?php echo count($collArr)*15; ?>px;right:0px;">
						<?php echo $buttonStr; ?>
					</div>
					<?php
				}
			}
		}
		echo '</div>';
		$this->collArrIndex++;
	}

	public static function getDbRequestVariable($reqArr){
		$dbStr = $reqArr['db'];
		if(is_array($dbStr)) $dbStr = implode(',',array_unique($dbStr)).';';
		else $dbStr = $dbStr;
		if(strpos($dbStr,'allspec') !== false) $dbStr = 'allspec';
		elseif(strpos($dbStr,'allobs') !== false) $dbStr = 'allobs';
		elseif(strpos($dbStr,'all') !== false) $dbStr = 'all';
		if(substr($dbStr,0,3) != 'all' && array_key_exists('cat',$reqArr) && $reqArr['cat']){
			$catArr = array();
			$catid = $reqArr['cat'];
			if(is_string($catid)) $catArr = Array($catid);
			else $catArr = $catid;
			if(!$dbStr) $dbStr = ';';
			$dbStr .= implode(',',$catArr);
		}
		if(!$dbStr) $dbStr = 'all';
		if(!preg_match('/^[a-z0-9,;]+$/', $dbStr)) $dbStr = 'all';
		return $dbStr;
	}

	public static function getDbWhereFrag($dbSearchTerm){
		$sqlRet = "";
		//Do nothing if db = all
		if($dbSearchTerm != 'all'){
			if($dbSearchTerm == 'allspec'){
				$sqlRet .= 'AND (o.collid IN(SELECT collid FROM omcollections WHERE colltype = "Preserved Specimens")) ';
			}
			elseif($dbSearchTerm == 'allobs'){
				$sqlRet .= 'AND (o.collid IN(SELECT collid FROM omcollections WHERE colltype IN("General Observations","Observations"))) ';
			}
			else{
				$dbArr = explode(';',$dbSearchTerm);
				$dbStr = '';
				if(isset($dbArr[0]) && $dbArr[0]){
					$dbStr = "(o.collid IN(".$dbArr[0].")) ";
				}
				if(isset($dbArr[1]) && $dbArr[1]){
					//$dbStr .= ($dbStr?'OR ':'').'(o.CollID IN(SELECT collid FROM omcollcatlink WHERE (ccpk IN('.$dbArr[1].')))) ';
				}
				$sqlRet .= 'AND ('.$dbStr.') ';
			}
		}
		return $sqlRet;
	}

	public function setCollidStr($str){
		$this->collidStr = $str;
	}
}
?>