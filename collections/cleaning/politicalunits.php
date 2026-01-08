<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceCleaner.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/collections/cleaning/politicalunits.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/collections/cleaning/politicalunits.'.$LANG_TAG.'.php');
else include_once($SERVER_ROOT.'/content/lang/collections/cleaning/politicalunits.en.php');
header("Content-Type: text/html; charset=".$CHARSET);

$collid = array_key_exists('collid', $_REQUEST) ? filter_var($_REQUEST['collid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$obsUid = array_key_exists('obsuid', $_REQUEST) ? filter_var($_REQUEST['obsuid'], FILTER_SANITIZE_NUMBER_INT) : '';
$mode = array_key_exists('mode',$_REQUEST)?$_REQUEST['mode']:'';
$action = array_key_exists('action',$_POST)?$_POST['action']:'';

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/cleaning/politicalunits.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$cleanManager = new OccurrenceCleaner();
if($collid) $cleanManager->setCollId($collid);
$collMap = current($cleanManager->getCollMap());

$statusStr = '';
$isEditor = 0;
if($IS_ADMIN || (array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollAdmin"]))
	|| ($collMap['colltype'] == 'General Observations')){
	$isEditor = 1;
}

//If collection is a general observation project, limit to User
if($collMap['colltype'] == 'General Observations' && $obsUid !== 0){
	$obsUid = $SYMB_UID;
	$cleanManager->setObsUid($obsUid);
}

if($action && $isEditor){
	if($action == 'Replace Country'){
		if(!$cleanManager->updateField('country', $_POST['badcountry'], $_POST['newcountry'])){
			$statusStr = $cleanManager->getErrorStr();
		}
	}
	elseif($action == 'Assign Country'){
		if(!$cleanManager->updateField('country', '', $_POST['country'], array('stateprovince' => $_POST['stateprovince']))){
			$statusStr = $cleanManager->getErrorStr();
		}
	}
	elseif($action == 'Replace State'){
		if(!$cleanManager->updateField('stateProvince', $_POST['badstate'], $_POST['newstate'], array('country' => $_POST['country']))){
			$statusStr = $cleanManager->getErrorStr();
		}
	}
	elseif($action == 'Assign State'){
		$condArr = array('county' => $_POST['county'],'country' => $_POST['country']);
		if(!$cleanManager->updateField('stateProvince', '', $_POST['state'], $condArr)){
			$statusStr = $cleanManager->getErrorStr();
		}
	}
	elseif($action == 'Replace County'){
		$condArr = array('country' => $_POST['country'], 'stateprovince' => $_POST['state']);
		if(!$cleanManager->updateField('county', $_POST['badcounty'], $_POST['newcounty'], $condArr)){
			$statusStr = $cleanManager->getErrorStr();
		}
	}
	elseif($action == 'Assign County'){
		$condArr = array('locality' => $_POST['locality'], 'country' => $_POST['country'], 'stateprovince' => $_POST['state']);
		if(!$cleanManager->updateField('county', '', $_POST['county'], $condArr)){
			$statusStr = $cleanManager->getErrorStr();
		}
	}
}
?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title><?php echo $DEFAULT_TITLE.' '.$LANG['POL_UNITS_STANDARD']; ?></title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script type="text/javascript">
		function verifyCountryCleanForm(f){
			if(f.newcountry.value == ""){
				alert("<?php echo $LANG['SEL_COUNTRY']; ?>");
				return false
			}
			return true;
		}

		function verifyNullCountryForm(f){
			if(f.country.value == ""){
				alert("<?php echo $LANG['SEL_COUNTRY']; ?>");
				return false
			}
			return true;
		}

		function verifyStateCleanForm(f){
			if(f.newstate.value == ""){
				alert("<?php echo $LANG['SEL_STATE']; ?>");
				return false
			}
			return true;
		}

		function verifyNullStateForm(f){
			if(f.state.value == ""){
				alert("<?php echo $LANG['SEL_STATE']; ?>");
				return false
			}
			return true;
		}

		function verifyCountyCleanForm(f){
			if(f.newcounty.value == ""){
				alert("<?php echo $LANG['SEL_COUNTY']; ?>");
				return false
			}
			return true;
		}

		function verifyNullCountyForm(f){
			if(f.county.value == ""){
				alert("<?php echo $LANG['SEL_COUNTY']; ?>");
				return false
			}
			return true;
		}
	</script>
	<style>
		.img-icon{ width: 1.1em }
		button{ margin: 2px 0px 10px 20px; }
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = false;
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class='navpath'>
		<a href="../../index.php"><?= $LANG['HOME'] ?></a> &gt;&gt;
		<a href="../misc/collprofiles.php?collid=<?= $collid ?>&emode=off"><?= $LANG['COL_MAN'] ?></a> &gt;&gt;
		<a href="index.php?collid=<?= $collid ?>"><?= $LANG['CLEAN_TOOL_INDEX'] ?></a>
		<?php
		if($mode) echo '&gt;&gt; <a href="politicalunits.php?collid=' . $collid . '"><b>' . $LANG['POL_GEO_CLEAN_MENU'] . '</b></a>';
		?>
	</div>

	<!-- inner text -->
	<div role="main" id="innertext">
		<?php
		echo '<h1 class="page-heading">' . $LANG['GEO_CLEANING_TOOLS'] . ': ' . $collMap['collectionname'] . ' (' . $collMap['code'] . ')</h1>';
		if($statusStr){
			?>
			<hr/>
			<div style="margin:20px;color:<?php echo (substr($statusStr,0,5)=='ERROR'?'red':'green');?>">
				<?php echo $statusStr; ?>
			</div>
			<hr/>
			<?php
		}
		if($isEditor){
			?>
			<section class="fieldset-like">
				<h2> <span> <?= $LANG['GEO_REPORT'] ?> </span> </h2>
				<?php
				if($mode) echo '<div style="position:absolute;top:5px;right:0px;padding:10px;border:1px solid grey"><a href="politicalunits.php?collid=' . $collid . '&mode=0">' . $LANG['MAIN_MENU'] . '</a></div>';
				echo '<div style="width:85%;margin-bottom:15px;">'.$LANG['GEO_REPORT_EXPLAIN'].'</div>';
				if($mode == 'badcountry'){
					$badCountryArr = $cleanManager->getBadCountryArr();
					$goodCountryArr = $cleanManager->getGoodCountryArr();
					?>
					<div style="margin:20px">
						<div style="margin:5px">
							<div style="margin-bottom:10px;">
								<?php echo '<b>'.$LANG['QUESTION_COUNTRIES'].':</b> '.$cleanManager->getFeatureCount().' '.$LANG['POS_ISSUES']; ?>
							</div>
							<?php
							foreach($badCountryArr as $countryName => $countryCnt){
								?>
								<div style="margin-left:15px;">
									<form name="countrycleanform" method="post" action="politicalunits.php" onsubmit="return verifyCountryCleanForm(this)">
										<b><?php echo $countryName; ?></b>
										<?php echo ' <span title="'.$LANG['NUMBER_SPECIMENS'].'">('.$countryCnt.')</span>'; ?>
										<a href="../editor/occurrenceeditor.php?q_catalognumber=&occindex=0&q_customfield1=country&q_customtype1=EQUALS&q_customvalue1=<?= urlencode($countryName) . '&collid=' . $collid ?>" target="_blank"><img src="../../images/edit.png" class="img-icon" ></a>
										<select name="newcountry" style="width:200px;">
											<option value=""><?php echo $LANG['REPLACE_WITH']; ?>...</option>
											<option value="">-------------------------</option>
											<?php
											foreach($goodCountryArr as $cgv){
												echo '<option>'.$cgv.'</option>';
											}
											?>
										</select>
										<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
										<input name="mode" type="hidden" value="badcountry" />
										<input name="badcountry" type="hidden" value="<?php echo $countryName; ?>" />
										<button name="action" type="submit" value="Replace Country"><?php echo $LANG['REPLACE_COUNTRY']; ?></button>
									</form>
								</div>
								<?php
							}
							?>
						</div>
					</div>
					<?php
				}
				elseif($mode == 'nullcountry'){
					$badCountryArr = $cleanManager->getNullCountryNotStateArr();
					$goodCountryArr = $cleanManager->getGoodCountryArr(true);
					?>
					<div style="margin:20px">
						<div style="margin:5px">
							<div style="margin-bottom:10px;font-size:120%;">
								<span style="font-weight:bold"><?php echo $LANG['NULL_COUNTRY_NOT_STATE']; ?>:</span> <?php echo $cleanManager->getFeatureCount().' '.$LANG['POS_ISSUES']; ?>
							</div>
							<?php
							foreach($badCountryArr as $stateName => $stateCnt){
								?>
								<div style="margin-left:15px;">
									<form name="nullcountryform" method="post" action="politicalunits.php" onsubmit="return verifyNullCountryForm(this)">
										<b><?php echo $stateName; ?></b>
										<?php echo ' <span title="Number of Specimens">('.$stateCnt.')</span>'; ?>
										<a href="../editor/occurrenceeditor.php?q_catalognumber=&occindex=0&q_customfield1=country&q_customtype1=IS_NULL&q_customfield2=stateProvince&q_customtype2=EQUALS&q_customvalue2=<?= urlencode($stateName) . '&collid=' . $collid; ?>" target="_blank"><img src="../../images/edit.png" class="img-icon" ></a>
										<select name="country" style="width:200px;">
											<option value=""><?php echo $LANG['ASSIGN_COUNTRY']; ?>...</option>
											<option value="">-------------------------</option>
											<?php
											foreach($goodCountryArr as $gcv => $stateArr){
												echo '<option '.($gcv!='USA'&&in_array($stateName,$stateArr)?'SELECTED':'').'>'.$gcv.'</option>';
											}
											?>
										</select>
										<input name="stateprovince" type="hidden" value="<?php echo $stateName; ?>" />
										<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
										<input name="mode" type="hidden" value="nullcountry" />
										<button name="action" type="submit" value="Assign Country"><?php echo $LANG['ASSIGN_COUNTRY']; ?></button>
									</form>
								</div>
								<?php
							}
							?>
						</div>
					</div>
					<?php
				}
				elseif($mode == 'badstate'){
					$countryID = 0;
					if(!empty($_REQUEST['countrygeothesid'])) $countryID = filter_var($_REQUEST['countrygeothesid'], FILTER_SANITIZE_NUMBER_INT);
					$badStateArr = $cleanManager->getBadStateArr($countryID);
					$goodStateArr = $cleanManager->getGoodStateArr();
					?>
					<div style="margin:20px">
						<div style="margin:5px">
							<div style="margin-bottom:10px;">
								<?php echo '<b>'.$LANG['QUESTION_STATES'].':</b> '.$cleanManager->getFeatureCount().' '.$LANG['POS_ISSUES']; ?>
							</div>
							<?php
							foreach($badStateArr as $countryValue => $stateArr){
								echo '<div style="margin-left:0px;"><b><u>'.$countryValue.'</u></b></div>';
								foreach($stateArr as $stateName => $stateCnt){
									?>
									<div style="margin-left:15px;">
										<form name="statecleanform" method="post" action="politicalunits.php" onsubmit="return verifyStateCleanForm(this)">
											<b><?php echo $stateName; ?></b>
											<?php echo ' <span title="Number of Specimens">('.$stateCnt.')</span>'; ?>
											<a href="../editor/occurrenceeditor.php?q_catalognumber=&occindex=0&q_customfield1=stateProvince&q_customtype1=EQUALS&q_customvalue1=<?= urlencode($stateName) . '&collid=' . $collid; ?>" target="_blank"><img src="../../images/edit.png" class="img-icon" ></a>
											<?php
											if(array_key_exists($countryValue,$goodStateArr)){
												?>
												<select name="newstate" style="width:200px;">
													<option value=""><?php echo $LANG['REPLACE_WITH']; ?>...</option>
													<option value="">-------------------------</option>
													<?php
													$arr = $goodStateArr[$countryValue];
													foreach($arr as $stateValue){
														echo '<option>'.$stateValue.'</option>';
													}
													?>
												</select>
												<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
												<input name="mode" type="hidden" value="badstate" />
												<input name="badstate" type="hidden" value="<?php echo $stateName; ?>" />
												<input name="countrygeothesid" type="hidden" value="<?= $countryID; ?>" >
												<input name="country" type="hidden" value="<?php echo $countryValue; ?>" />
												<button name="action" type="submit" value="Replace State"><?php echo $LANG['REPLACE_STATE']; ?></button>
												<?php
											}
											?>
										</form>
									</div>
									<?php
								}
							}
							?>
						</div>
					</div>
					<?php
				}
				elseif($mode == 'nullstate'){
					$badStateArr = $cleanManager->getNullStateNotCountyArr();
					$goodStateArr = $cleanManager->getGoodStateArr(true);
					?>
					<div style="margin:20px">
						<div style="margin:5px">
							<div style="margin-bottom:10px;font-size:120%;">
								<span style="font-weight:bold"><?php echo $LANG['NULL_STATE_NOT_COUNTY']; ?>:</span> <?php echo $cleanManager->getFeatureCount().' ' .$LANG['POS_ISSUES']; ?>
							</div>
							<?php
							foreach($badStateArr as $countryName => $countyArr){
								echo '<div style="margin-left:0px;"><b><u>'.$countryName.'</u></b></div>';
								foreach($countyArr as $countyName => $countyCnt){
									?>
									<div style="margin-left:15px;">
										<form name="nullstateform" method="post" action="politicalunits.php" onsubmit="return verifyNullStateForm(this)">
											<b><?php echo $countyName; ?></b>
											<?php echo ' <span title="Number of Specimens">('.$countyCnt.')</span>'; ?>
											<a href="../editor/occurrenceeditor.php?q_catalognumber=&occindex=0&q_customfield1=stateProvince&q_customtype1=IS_NULL&q_customfield2=county&q_customtype2=EQUALS&q_customvalue2=<?= urlencode($countyName) . '&collid=' . $collid; ?>" target="_blank"><img src="../../images/edit.png" class="img-icon" ></a>
											<?php
											if(array_key_exists($countryName,$goodStateArr)){
												?>
												<select name="state" style="width:200px;">
													<option value=""><?php echo $LANG['ASSIGN_STATE']; ?>...</option>
													<option value="">-------------------------</option>
													<?php
													$countyTestStr = str_replace(array(' county',' co.',' co'),'',strtolower($countyName));
													$arr = $goodStateArr[$countryName];
													foreach($arr as $gsv => $cArr){
														echo '<option '.(in_array($countyTestStr,$cArr)?'SELECTED':'').'>'.$gsv.'</option>';
													}
													?>
												</select>
												<input name="county" type="hidden" value="<?php echo $countyName; ?>" />
												<input name="country" type="hidden" value="<?php echo $countryName; ?>" />
												<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
												<input name="mode" type="hidden" value="nullstate" />
												<button name="action" type="submit" value="Assign State" ><?php echo $LANG['ASSIGN_STATE']; ?></button>
												<?php
											}
											?>
										</form>
									</div>
									<?php
								}
							}
							?>
						</div>
					</div>
					<?php
				}
				elseif($mode == 'badstatelist'){
					echo '<div style="margin-bottom:2px"><b>' . $LANG['QUESTION_STATES_LIST'] . ':</b> ';
					if($badStateArr = $cleanManager->getBadStateCountArr()){
						echo '<ul>';
						foreach($badStateArr as $country => $cnt){
							$countryGeoThesID = substr($country, 0, strpos($country, ':'));
							echo '<li>' . substr($country, strpos($country, ':') + 1) . ': <a href="politicalunits.php?collid=' . $collid . '&mode=badstate&countrygeothesid=' . $countryGeoThesID . '">' . $cnt . ' ' . $LANG['RECORDS'] . '</a></li>';
						}
						echo '</ul>';
					}
					else echo 0;
					echo '</div>';
				}
				elseif($mode == 'badcounty'){
					$countryID = 0;
					if(!empty($_REQUEST['countrygeothesid'])) $countryID = filter_var($_REQUEST['countrygeothesid'], FILTER_SANITIZE_NUMBER_INT);
					$badCountyArr = $cleanManager->getBadCountyArr($countryID);
					$goodCountyArr = $cleanManager->getGoodCountyArr();
					?>
					<div style="margin:20px">
						<div style="margin:5px">
							<div style="margin-bottom:10px;">
								<?php echo '<b>'.$LANG['QUESTION_COUNTIES'].':</b> '.$cleanManager->getFeatureCount().' '.$LANG['POS_ISSUES']; ?>
							</div>
							<?php
							foreach($badCountyArr as $countryName => $stateArr){
								echo '<div style="margin-left:0px;"><b><u>'.$countryName.'</u></b></div>';
								foreach($stateArr as $stateName => $countyArr){
									$stateTestStr = strtolower($stateName);
									echo '<div style="margin-left:15px;"><b><u>'.$stateName.'</u></b></div>';
									foreach($countyArr as $countyName => $countyCnt){
										$countyTestStr = str_replace(array(' county',' co.',' co'), '', strtolower($countyName));
										?>
										<div style="margin-left:30px;">
											<form name="countycleanform" method="post" action="politicalunits.php" onsubmit="return verifyCountyCleanForm(this)">
												<b><?= $countyName; ?></b>
												<span title="Number of Specimens">(<?= $countyCnt ?>)</span>
												<?php
												$editorUrl = '../editor/occurrenceeditor.php?q_catalognumber=&occindex=0&collid=' . $collid;
												$editorUrl .= '&q_customfield1=country&q_customtype1=EQUALS&q_customvalue1=' . urlencode($countryName);
												$editorUrl .= '&q_customfield2=stateProvince&q_customtype2=EQUALS&q_customvalue2=' . urlencode($stateName);
												$editorUrl .= '&q_customfield3=county&q_customtype3=EQUALS&q_customvalue3=' . urlencode($countyName);
												?>
												<a href="<?= $editorUrl ?>" target="_blank"><img src="../../images/edit.png" class="img-icon"></a>
												<?php
												if(array_key_exists($stateTestStr,$goodCountyArr)){
													?>
													<select name="newcounty" style="width:200px;">
														<option value=""><?= $LANG['REPLACE_WITH']; ?>...</option>
														<option value="">-------------------------</option>
														<?php
														$arr = $goodCountyArr[$stateTestStr];
														foreach($arr as $v){
															echo '<option '.($countyTestStr==strtolower($v)?'selected':'').'>'.$v.'</option>';
														}
														?>
													</select>
													<input name="collid" type="hidden" value="<?= $collid; ?>" >
													<input name="mode" type="hidden" value="badcounty" >
													<input name="badcounty" type="hidden" value="<?= $countyName; ?>" >
													<input name="countrygeothesid" type="hidden" value="<?= $countryID; ?>" >
													<input name="country" type="hidden" value="<?= $countryName; ?>" >
													<input name="state" type="hidden" value="<?= $stateName; ?>" >
													<button name="action" type="submit" value="Replace County" ><?= $LANG['REPLACE_COUNTY']; ?></button>
													<?php
												}
												?>
											</form>
										</div>
										<?php
									}
								}
							}
							?>
						</div>
					</div>
					<?php
				}
				elseif($mode == 'nullcounty'){
					$badCountyArr = $cleanManager->getNullCountyNotLocalityArr();
					$goodCountyArr = $cleanManager->getGoodCountyArr();
					?>
					<div style="margin:20px">
						<div style="margin:5px">
							<div style="margin-bottom:10px;font-size:120%;">
								<span style="font-weight:bold"><?php echo $LANG['NULL_COUNTY_NOT_LOCALITY']; ?>:</span> <?php echo $cleanManager->getFeatureCount().' '.$LANG['POS_ISSUES']; ?>
							</div>
							<?php
							foreach($badCountyArr as $countryName => $stateArr){
								echo '<div style="margin-left:0px;"><b><u>'.$countryName.'</u></b></div>';
								foreach($stateArr as $stateName => $localityArr){
									echo '<div style="margin-left:15px;"><b><u>'.$stateName.'</u></b></div>';
									$stateTestStr = strtolower($stateName);
									foreach($localityArr as $localityName => $localityCnt){
										?>
										<div style="margin-left:30px;">
											<form name="nullstateform" method="post" action="politicalunits.php" onsubmit="return verifyNullCountyForm(this)">
												<b><?php echo $localityName; ?></b>
												<?php echo ' <span title="Number of Specimens">('.$localityCnt.')</span>'; ?>
												<a href="../editor/occurrenceeditor.php?q_catalognumber=&occindex=0&q_customfield1=county&q_customtype1=IS_NULL&q_customfield2=locality&q_customtype2=EQUALS&q_customvalue2=<?= urlencode($localityName) . '&collid=' . $collid; ?>" target="_blank"><img src="../../images/edit.png"  class="img-icon" ></a>
												<?php
												if(array_key_exists($stateTestStr,$goodCountyArr)){
													?>
													<select name="county" style="width:200px;">
														<option value=""><?php echo $LANG['ASSIGN_COUNTY']; ?>...</option>
														<option value="">-------------------------</option>
														<?php
														$arr = $goodCountyArr[$stateTestStr];
														foreach($arr as $v){
															echo '<option>'.$v.'</option>';
														}
														?>
													</select>
													<input name="locality" type="hidden" value="<?php echo htmlentities($localityName, ENT_COMPAT, $CHARSET); ?>" />
													<input name="country" type="hidden" value="<?php echo $countryName; ?>" />
													<input name="state" type="hidden" value="<?php echo $stateName; ?>" />
													<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
													<input name="mode" type="hidden" value="nullcounty" />
													<button name="action" type="submit" value="Assign County"><?php echo $LANG['ASSIGN_COUNTY']; ?></button>
													<?php
												}
												?>
											</form>
										</div>
										<?php
									}
								}
							}
							?>
						</div>
					</div>
					<?php
				}
				elseif($mode == 'badcountylist'){
					echo '<div style="margin-bottom:2px"><b>' . $LANG['QUESTION_COUNTIES_LIST'] . ':</b> ';
					if($badCountiesArr = $cleanManager->getBadCountyCountArr()){
						echo '<ul>';
						foreach($badCountiesArr as $country => $cnt){
							$countryGeoThesID = substr($country, 0, strpos($country, ':'));
							echo '<li>' . substr($country, strpos($country, ':') + 1) . ': <a href="politicalunits.php?collid=' . $collid . '&mode=badcounty&countrygeothesid=' . $countryGeoThesID . '">' . $cnt . ' ' . $LANG['RECORDS'] . '</a></li>';
						}
						echo '</ul>';
					}
					else echo 0;
					echo '</div>';
				}
				else{
					if($mode === ''){
						echo '<div style="margin-bottom:15px;">';
						echo '<div style="font-weight:bold;">'.$LANG['GEN_CLEANING'].'... </div>';
						flush();
						ob_flush();
						$cleanManager->countryCleanFirstStep();
						echo '</div>';
					}

					echo '<div style="margin-bottom:2px"><b>'.$LANG['QUESTION_COUNTRIES'].':</b> ';
					$badCountryCnt = $cleanManager->getBadCountryCount();
					if($badCountryCnt) echo ' =&gt <a href="politicalunits.php?collid=' . $collid . '&mode=badcountry">' . $badCountryCnt . ' ' . $LANG['RECORDS'] . '</a>';
					else echo 0;
					echo '</div>';

					//Get Null country and not null state
					echo '<div style="margin-bottom:20px"><b>'.$LANG['NULL_COUNTRY_NOT_STATE'].':</b> ';
					$nullCountryCnt = $cleanManager->getNullCountryNotStateCount();
					if($nullCountryCnt) echo ' =&gt <a href="politicalunits.php?collid=' . $collid . '&mode=nullcountry">' . $nullCountryCnt . ' ' . $LANG['RECORDS'] . '</a>';
					else echo 0;
					echo '</div>';

					//Get count for bad states
					echo '<div style="margin-bottom:2px"><b>'.$LANG['QUESTION_STATES'].':</b> ';
					$badStateCnt = $cleanManager->getBadStateCount();
					if($badStateCnt) echo '<a href="politicalunits.php?collid=' . $collid . '&target=geolocal&mode=badstatelist">' . $badStateCnt . ' ' . $LANG['RECORDS'] . '</a>';
					else echo 0;
					echo '</div>';

					//Get Null state and not null county or municipality
					echo '<div style="margin-bottom:20px"><b>'.$LANG['NULL_STATE_NOT_COUNTY'].':</b> ';
					$nullStateCnt = $cleanManager->getNullStateNotCountyCount();
					if($nullStateCnt) echo '<a href="politicalunits.php?collid=' . $collid . '&mode=nullstate">' . $nullStateCnt . ' ' . $LANG['RECORDS'] . '</a>';
					else echo 0;
					echo '</div>';

					//Get count for bad counties
					echo '<div style="margin-bottom:2px"><b>' . $LANG['QUESTION_COUNTIES'] . ':</b> ';
					$badCountiesCnt = $cleanManager->getBadCountyCount();
					if($badCountiesCnt) echo '<a href="politicalunits.php?collid=' . $collid . '&target=geolocal&mode=badcountylist">' . $badCountiesCnt . ' ' . $LANG['RECORDS'] . '</a>';
					else echo 0;
					echo '</div>';

					//Get Null county and not null locality
					echo '<div style="margin-bottom:60px"><b>'.$LANG['NULL_COUNTY_NOT_LOCALITY'].':</b> ';
					$nullCountyCnt = $cleanManager->getNullCountyNotLocalityCount();
					if($nullCountyCnt) echo '<a href="politicalunits.php?collid=' . $collid . '&mode=nullcounty">' . $nullCountyCnt . ' ' . $LANG['RECORDS'] . '</a>';
					else echo 0;
					echo '</div>';
				}
				?>
			</section>
 			<?php
		}
		else{
			echo '<h2>'.$LANG['NOT_AUTH'].'</h2>';
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
