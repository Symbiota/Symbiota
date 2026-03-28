<?php
include_once('../../config/symbini.php');
@include_once('Image/Barcode.php');
@include_once('Image/Barcode2.php');
include_once($SERVER_ROOT.'/classes/OccurrenceLabel.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load('collections/reports/labelmanager');

header("Content-Type: text/html; charset=".$CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/reports/labelmanager.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = Sanitize::int($_REQUEST['collid']);
$action = array_key_exists('submitaction', $_REQUEST) ? $_REQUEST['submitaction'] : '';

$labelManager = new OccurrenceLabel();
$labelManager->setCollid($collid);

$limit = (ini_get('max_input_vars')/2) - 100;
if(!$limit) $limit = 400;
elseif($limit > 1000) $limit = 1000;

$isEditor = 0;
$occArr = array();
if($IS_ADMIN || (array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollAdmin"]))){
	$isEditor = 1;
}
elseif(array_key_exists("CollEditor",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollEditor"])){
	$isEditor = 1;
}
if($isEditor){
	if($action == 'filterRecords'){
		$occArr = $labelManager->queryOccurrences($_POST, $limit);
	}
}
$labelFormatArr = $labelManager->getLabelFormatArr(true);
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET;?>">
		<title><?= $DEFAULT_TITLE ?> <?= $LANG['SPEC_LABEL_MANAGER'] ?> </title>
		<?php
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<script type="text/javascript">
			<?php
			if($labelFormatArr) echo "var labelFormatObj = ".json_encode($labelFormatArr).";";
			?>

			function selectAll(cb){
				boxesChecked = true;
				if(!cb.checked){
					boxesChecked = false;
				}
				var dbElements = document.getElementsByName("occid[]");
				for(i = 0; i < dbElements.length; i++){
					var dbElement = dbElements[i];
					dbElement.checked = boxesChecked;
				}
			}

			function validateQueryForm(f){
				if(!validateDateFields(f)){
					return false;
				}
				return true;
			}

			function validateDateFields(f){
				var status = true;
				var validformat1 = /^\s*\d{4}-\d{2}-\d{2}\s*$/ //Format: yyyy-mm-dd
				if(f.date1.value !== "" && !validformat1.test(f.date1.value)) status = false;
				if(f.date2.value !== "" && !validformat1.test(f.date2.value)) status = false;
				if(!status) alert("<?= $LANG['ALERT_DATE'] ?>");
				return status;
			}

			function validateSelectForm(f){
				var dbElements = document.getElementsByName("occid[]");
				for(i = 0; i < dbElements.length; i++){
					var dbElement = dbElements[i];
					if(dbElement.checked){
						var quantityObj = document.getElementsByName("q-"+dbElement.value);
						if(quantityObj && quantityObj[0].value > 0) return true;
					}
				}
			   	alert("<?= $LANG['ALERT_SPEC'] ?>");
			  	return false;
			}

			function openIndPopup(occid){
				openPopup('../individual/index.php?occid=' + occid);
			}

			function openEditorPopup(occid){
				openPopup('../editor/occurrenceeditor.php?occid=' + occid);
			}

			function openPopup(urlStr){
				var wWidth = 900;
				if(document.body.offsetWidth) wWidth = document.body.offsetWidth*0.9;
				if(wWidth > 1200) wWidth = 1200;
				newWindow = window.open(urlStr,'popup','scrollbars=1,toolbar=0,resizable=1,width='+(wWidth)+',height=600,left=20,top=20');
				if (newWindow.opener == null) newWindow.opener = self;
				return false;
			}

			function changeFormExport(buttonElem, action, target){
				var f = buttonElem.form;
				if(action == "labeldynamic.php" && buttonElem.value == "printBrowser"){
					if(!f["labelformatindex"] || f["labelformatindex"].value == ""){
						alert("<?= $LANG['ALERT_LABEL'] ?>");
						return false;
					}
				}
				else if(action == "labelsword.php" && f.labeltype.valye == "packet"){
					alert("<?= $LANG['ALERT_PACKET_LABEL'] ?>");
					return false;
				}
				if(f.bconly && f.bconly.checked && action == "labeldynamic.php") action = "barcodes.php";
				f.action = action;
				f.target = target;
				return true;
			}

			function checkPrintOnlyCheck(f){
				if(f.bconly.checked){
					f.speciesauthors.checked = false;
					f.catalognumbers.checked = false;
					f.bc.checked = false;
					f.symbbc.checked = false;
				}
			}

			function checkBarcodeCheck(f){
				if(f.bc.checked || f.symbbc.checked || f.speciesauthors.checked || f.catalognumbers.checked){
					f.bconly.checked = false;
				}
			}

			function labelFormatChanged(selObj){
				if(selObj && labelFormatObj){
					var catStr = selObj.value.substring(0,1);
					var labelIndex = selObj.value.substring(2);
					var f = document.selectform;
					if(catStr != ''){
						f.hprefix.value = labelFormatObj[catStr][labelIndex].labelHeader.prefix;
						var midIndex = labelFormatObj[catStr][labelIndex].labelHeader.midText;
						document.getElementById("hmid"+midIndex).checked = true;
						f.hsuffix.value = labelFormatObj[catStr][labelIndex].labelHeader.suffix;
						f.lfooter.value = labelFormatObj[catStr][labelIndex].labelFooter.textValue;
						if(labelFormatObj[catStr][labelIndex].displaySpeciesAuthor == 1) f.speciesauthors.checked = true;
						else f.speciesauthors.checked = false;
						if(f.bc){
							if(labelFormatObj[catStr][labelIndex].displayBarcode == 1) f.bc.checked = true;
							else f.bc.checked = false;
						}
						f.labeltype.value = labelFormatObj[catStr][labelIndex].labelType;
					}
				}
			}
		</script>
		<style>
			fieldset{ margin-top:10px; margin-bottom:10px; padding:15px; }
			fieldset legend{ font-weight:bold; }
			.fieldDiv{ clear:both; padding:5px 0px; margin:5px 0px }
			.fieldLabel{ font-weight: bold; display:block }
			.checkboxLabel{ font-weight: bold; }
			.fieldElement{  }
		</style>
	</head>
	<body>
	<?php
	$displayLeftMenu = false;
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class='navpath'>
		<a href='../../index.php'> <?= $LANG['NAV_HOME'] ?> </a> &gt;&gt;
		<?php
		if(stripos(strtolower($labelManager->getMetaDataTerm('colltype')), "observation") !== false){
			echo '<a href="../../profile/viewprofile.php?tabindex=1">' . $LANG['PERS_MANAG_MENU'] . '</a> &gt;&gt; ';
		}
		else{
			echo '<a href="../misc/collprofiles.php?collid=' . $collid . '&emode=1">' . $LANG['COLL_MANAG_PANEL'] . '</a> &gt;&gt; ';
		}
		?>
		<b> <?= $LANG['LABEL_PRINT'] ?> </b>
	</div>
	<!-- This is inner text! -->
	<div role="main" id="innertext">
		<h1 class="page-heading"><?= $LANG['SPEC_LABEL_MANAGER'] ?></h1>
		<?php
		if($isEditor){
			$isGeneralObservation = (($labelManager->getMetaDataTerm('colltype') == 'General Observations')?true:false);
			echo '<h2>'.$labelManager->getCollName().'</h2>';
			?>
			<div>
				<form name="datasetqueryform" action="labelmanager.php" method="post" onsubmit="return validateQueryForm(this)">
					<fieldset>
						<legend><b> <?= $LANG['DEF_SPEC_REC'] ?> </b></legend>
						<div style="margin:3px;">
							<div title="<?= $LANG['DEF_SPEC_REC'] ?>">
								<label for="taxa"> <?= $LANG['SCI_NAME'] ?></label>
								<input type="text" name="taxa" id="taxa" size="60" value="<?= !empty($_REQUEST['taxa']) ? Sanitize::inString($_REQUEST['taxa']) : '' ?>" />
							</div>
						</div>
						<div style="margin:3px;clear:both;">
							<div style="float:left;" title="<?= $LANG['FULL_NAME'] ?>">
								<label for="recordedby"><?= $LANG['COLLECTOR'] ?></label>
								<input type="text" name="recordedby" id="recordedby" style="width:150px;" value="<?= !empty($_REQUEST['recordedby']) ? Sanitize::inString($_REQUEST['recordedby']) : '' ?>" />
							</div>
							<div style="float:left;margin-left:10px;" title="<?= $LANG['SEPARATE_TERMS'] ?>">
								<label for="recordnumber"><?= $LANG['REC_NUM'] ?></label>
								<input type="text" name="recordnumber" id="recordnumber" style="width:150px;" value="<?= !empty($_REQUEST['recordnumber']) ? Sanitize::inString($_REQUEST['recordnumber']) : '' ?>" />
							</div>
							<div style="float:left;margin-left:10px;" title="<?= $LANG['SEPARATE_TERMS'] ?>">
								<label for="identifier"><?= $LANG['CAT_NUM'] ?></label>
								<input type="text" name="identifier" id="identifier" style="width:150px;" value="<?= !empty($_REQUEST['identifier']) ? Sanitize::inString($_REQUEST['identifier']) : '' ?>" />
							</div>
						</div>
						<div style="margin:3px;clear:both;">
							<div style="float:left;">
								<label for="recordenteredby"> <?= $LANG['ENTER_BY'] ?> </label>
								<input type="text" name="recordenteredby" id="recordenteredby" value="<?= !empty($_REQUEST['recordenteredby']) ? Sanitize::inString($_REQUEST['recordenteredby']) : '' ?>" style="width:100px;" title="<?= $LANG['LOG_NAME'] ?> " aria-label="<?= $LANG['ENTER_BY'] ?>" />
							</div>
							<div style="margin-left:20px;float:left;">
								<label for="date1"><?= $LANG['DATE_RANGE'] ?></label>
								<input type="text" name="date1" id="date1" style="width:100px;" value="<?= !empty($_REQUEST['date1']) ? Sanitize::inString($_REQUEST['date1']) : '' ?>" onchange="validateDateFields(this.form)" />
								<label for="date2"> <?= $LANG['TO'] ?> </label>
								<input type="text" name="date2" id="date2" style="width:100px;" value="<?= !empty($_REQUEST['date2']) ? Sanitize::inString($_REQUEST['date2']) : '' ?>" onchange="validateDateFields(this.form)" />
								<label for="datetarget" style="margin-left:10px"><?= $LANG['TYPE_OF_DATE'] ?>:</label>
								<select name="datetarget" id="datetarget">
									<option value="dateentered"><?= $LANG['DATE_ENTERED'] ?></option>
									<option value="datelastmodified" <?= (isset($_POST['datetarget']) && $_POST['datetarget'] == 'datelastmodified'?'SELECTED':'') ?>><?= $LANG['DATE_MOD'] ?></option>
									<option value="eventdate"<?= (isset($_POST['datetarget']) && $_POST['datetarget'] == 'eventdate'?'SELECTED':'') ?>><?= $LANG['DATE_COLL'] ?></option>
								</select>
							</div>
						</div>
						<div style="margin:3px;clear:both;">
							<label for="labelproject"> <?= $LANG['LABEL_PROJ'] ?></label>
							<select name="labelproject" id="labelproject">
								<option value=""> <?= $LANG['ALL_PROJ'] ?> </option>
								<option value="">-------------------------</option>
								<?php
								$lProj = '';
								if(array_key_exists('labelproject',$_REQUEST)) $lProj = $_REQUEST['labelproject'];
								$lProjArr = $labelManager->getLabelProjects();
								foreach($lProjArr as $projStr){
									echo '<option '.($lProj==$projStr?'SELECTED':'').'>'.$projStr.'</option>'."\n";
								}
								?>
							</select>
							<!--
							Dataset Projects:
							<select name="datasetproject" >
								<option value=""></option>
								<option value="">-------------------------</option>
								<?php
								/*
								$datasetProj = '';
								if(array_key_exists('datasetproject',$_REQUEST)) $datasetProj = $_REQUEST['datasetproject'];
								$dProjArr = $labelManager->getDatasetProjects();
								foreach($dProjArr as $dsid => $dsProjStr){
									echo '<option id="'.$dsid.'" '.($datasetProj==$dsProjStr?'SELECTED':'').'>'.$dsProjStr.'</option>'."\n";
								}
								*/
								?>
							</select>
							-->
							<span style="margin-left:15px;"><input name="extendedsearch" id="extendedsearch" type="checkbox" value="1" <?= (array_key_exists('extendedsearch', $_POST)?'checked':'') ?> ></span>
							<label for="extendedsearch">
								<?php
								if($isGeneralObservation) echo $LANG['SEARCH_OUT'];
								else echo $LANG['SEARCH_IN'];
								?>
							</label>
						</div>
						<div style="clear:both;">
							<div style="float:left;">
								<input type="hidden" name="collid" value="<?= $collid ?>" />
								<button type="submit" name="submitaction" value="filterRecords"><?= $LANG['FILT_SPEC_REC'] ?></button>
							</div>
							<div style="margin-left:20px;float:left;">
								* <?= $LANG['SPEC_LIM'] ?>: <?= $limit ?>
							</div>
						</div>
					</fieldset>
				</form>
				<div style="clear:both;">
					<?php
					if($action == 'filterRecords'){
						if($occArr){
							?>
							<form name="selectform" id="selectform" action="labeldynamic.php" method="post" onsubmit="return validateSelectForm(this);">
								<table class="styledtable" style="font-size:12px;">
									<tr>
										<th title="Select/Deselect all Specimens"><input type="checkbox" onclick="selectAll(this);" /></th>
										<th title="Label quantity"> <?= $LANG['QTY'] ?> </th>
										<th> <?= $LANG['COLLECTOR'] ?> </th>
										<th> <?= $LANG['SCI_NAME'] ?></th>
										<th> <?= $LANG['LOCALITY'] ?></th>
									</tr>
									<?php
									$trCnt = 0;
									foreach($occArr as $occId => $recArr){
										$trCnt++;
										?>
										<tr <?= ($trCnt%2?'class="alt"':'') ?>>
											<td>
												<input type="checkbox" name="occid[]" value="<?= $occId ?>" />
											</td>
											<td>
												<input type="text" name="q-<?= $occId ?>" value="<?= $recArr["q"] ?>" style="width:20px;border:inset;" title="<?= $LANG['LABEL_QTY'] ?>" />
											</td>
											<td>
												<a href="#" onclick="openIndPopup(<?= $occId ?>); return false;">
													<?= $recArr["c"] ?>
												</a>
												<?php
												if($IS_ADMIN || (array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($recArr["collid"],$USER_RIGHTS["CollAdmin"])) || (array_key_exists("CollEditor",$USER_RIGHTS) && in_array($recArr["collid"],$USER_RIGHTS["CollEditor"]))){
													if(!$isGeneralObservation || $recArr['uid'] == $SYMB_UID){
														?>
														<a href="#" onclick="openEditorPopup(<?= $occId ?>); return false;">
															<img src="../../images/edit.png" style="width:1.3em" />
														</a>
														<?php
													}
												}
												?>
											</td>
											<td>
												<?= $recArr["s"] ?>
											</td>
											<td>
												<?= $recArr["l"] ?>
											</td>
										</tr>
										<?php
									}
									?>
								</table>
								<fieldset style="margin-top:15px;">
									<legend> <?= $LANG['LABEL_PRINT'] ?></legend>
										<div class="fieldDiv">
											<div class="fieldLabel"> <?= $LANG['LABEL_PROFILE'] ?>
												<span title="Open label profile manager">
													<a href="labelprofile.php?collid=<?= $collid ?>"><img src="../../images/edit.png" style="width:1.2em" /></a>
												</span>
											</div>
											<div class="fieldElement">
												<div>
													<select name="labelformatindex" onchange="labelFormatChanged(this)">
														<option value=""> <?= $LANG['SEL_LABEL_FORMAT'] ?> </option>
														<?php
														foreach($labelFormatArr as $cat => $catArr){
															echo '<option value="">---------------------------</option>';
															foreach($catArr as $k => $labelArr){
																if (!isset($labelArr['title'])) continue;
																echo '<option value="'.$cat.'-'.$k.'">'.$labelArr['title'].'</option>';
															}
														}
														?>
													</select>
												</div>
												<?php
												if(!$labelFormatArr) echo '<b>' . $LANG['LABEL_NOT_SET'] . '</b>';
												?>
											</div>
										</div>
									<div class="fieldDiv">
										<div class="fieldLabel"> <?= $LANG['HEAD_PREFIX'] ?> </div>
										<div class="fieldElement">
											<input type="text" name="hprefix" value="" style="width:450px" /> <?= $LANG['E_G_PLANTS'] ?>
										</div>
									</div>
									<div class="fieldDiv">
										<div class="checkboxLabel"> <?= $LANG['HEAD_MID'] ?> </div>
										<div class="fieldElement">
											<input type="radio" id="hmid1" name="hmid" value="1" /> <?= $LANG['COUNTRY'] ?>
											<input type="radio" id="hmid2" name="hmid" value="2" /> <?= $LANG['STATE'] ?>
											<input type="radio" id="hmid3" name="hmid" value="3" /> <?= $LANG['COUNTY'] ?>
											<input type="radio" id="hmid4" name="hmid" value="4" /> <?= $LANG['FAMILY'] ?>
											<input type="radio" id="hmid0" name="hmid" value="0" checked/> <?= $LANG['BLANK'] ?>
										</div>
									</div>
									<div class="fieldDiv">
										<span class="fieldLabel"> <?= $LANG['HEAD_SUFF'] ?> </span>
										<span class="fieldElement">
											<input type="text" name="hsuffix" value="" style="width:450px" />
										</span>
									</div>
									<div class="fieldDiv">
										<span class="fieldLabel"> <?= $LANG['FOOTER'] ?> </span>
										<span class="fieldElement">
											<input type="text" name="lfooter" value="" style="width:450px" />
										</span>
									</div>
									<div class="fieldDiv">
										<input type="checkbox" name="speciesauthors" value="1" onclick="checkBarcodeCheck(this.form);" />
										<span class="checkboxLabel"> <?= $LANG['PRINT_AUTH'] ?> </span>
									</div>
									<div class="fieldDiv">
										<input type="checkbox" name="catalognumbers" value="1" onclick="checkBarcodeCheck(this.form);" />
										<span class="checkboxLabel"> <?= $LANG['PRINT_CAT_NUM'] ?> </span>
									</div>
									<?php
									if(class_exists('Image_Barcode2') || class_exists('Image_Barcode')){
										?>
										<div class="fieldDiv">
											<input type="checkbox" name="bc" value="1" onclick="checkBarcodeCheck(this.form);" />
											<span class="checkboxLabel"> <?= $LANG['INCL_BARCODE'] ?> </span>
										</div>
										<!--
										<div class="fieldDiv">
											<input type="checkbox" name="symbbc" value="1" onclick="checkBarcodeCheck(this.form);" />
											<span class="checkboxLabel">Include barcode of Symbiota Identifier</span>
										</div>
										 -->
										<div class="fieldDiv">
											<input type="checkbox" name="bconly" value="1" onclick="checkPrintOnlyCheck(this.form);" />
											<span class="checkboxLabel"> <?= $LANG['PRINT_BARCODE'] ?> </span>
										</div>
										<?php
									}
									?>
									<div class="fieldDiv">
										<span class="fieldLabel"> <?= $LANG['LABEL_TYPE'] ?> </span>
										<span class="fieldElement">
											<select name="labeltype">
												<option value="1"> 1 <?= $LANG['COLL_PAGE'] ?> </option>
												<option value="2" selected>2 <?= $LANG['COLL_PAGE'] ?> </option>
												<option value="3">3 <?= $LANG['COLL_PAGE'] ?> </option>
												<option value="4">4 <?= $LANG['COLL_PAGE'] ?> </option>
												<option value="5">5 <?= $LANG['COLL_PAGE'] ?> </option>
												<option value="6">6 <?= $LANG['COLL_PAGE'] ?> </option>
												<option value="7">7 <?= $LANG['COLL_PAGE'] ?> </option>
												<option value="packet"><?= $LANG['PACKET_LABEL'] ?> </option>
											</select>
										</span>
									</div>
									<div style="float:left;margin: 15px 50px;">
										<input type="hidden" name="collid" value="<?= $collid ?>" />
										<div style="margin:10px">
											<button type="submit" name="submitaction" onclick="return changeFormExport(this,'labeldynamic.php','_blank');" value="printBrowser" title="<?= $LANG['CONTACT_ADMIN'] ?>" <?= ($labelFormatArr?'':'DISABLED') ?>><?= $LANG['PRINT_BROWSER'] ?></button>
										</div>
										<div style="margin:10px">
											<button type="submit" name="submitaction" onclick="return changeFormExport(this,'labeldynamic.php','_self');" value="csvExport"><?= $LANG['EXP_CSV'] ?></button>
										</div>
										<div style="margin:10px">
											<button type="submit" name="submitaction" onclick="return changeFormExport(this,'labelsword.php','_self');" value="exportDOCX"><?= $LANG['EXP_DOCX'] ?></button>
										</div>
										<div style="clear:both;padding:10px 0px">
											<b><?= $LANG['NOTE'] ?></b>: <?= $LANG['NOTE_DETAILS'] ?>
										</div>
								</fieldset>
							</form>
							<?php
						}
						else{
							?>
							<div style="font-weight:bold;margin:20px;font-weight:150%;">
								<?= $LANG['NO_DATA'] ?>
							</div>
							<?php
						}
					}
					?>
				</div>
			</div>
			<?php
		}
		else{
			?>
			<div style="font-weight:bold;margin:20px;font-weight:150%;">
				<?= $LANG['NO_PERM'] ?>
			</div>
			<?php
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
	</body>
</html>
