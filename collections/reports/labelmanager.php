<?php
include_once('../../config/symbini.php');
@include_once('Image/Barcode.php');
@include_once('Image/Barcode2.php');
include_once($SERVER_ROOT.'/classes/OccurrenceLabel.php');
header("Content-Type: text/html; charset=".$CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/reports/labelmanager.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = $_REQUEST['collid'];
$action = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';

//Sanitation
if(!is_numeric($collid)) $collid = 0;

$labelManager = new OccurrenceLabel();
$labelManager->setCollid($collid);

$isEditor = 0;
$occArr = array();
if($IS_ADMIN || (array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollAdmin"]))){
	$isEditor = 1;
}
elseif(array_key_exists("CollEditor",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollEditor"])){
	$isEditor = 1;
}
if($isEditor){
	if($action == "Filter Specimen Records"){
		$occArr = $labelManager->queryOccurrences($_POST);
	}
}
$labelFormatArr = $labelManager->getLabelFormatArr(true);

// Add collection customization variables
if($collid && file_exists('../editor/includes/config/occurVarColl'.$collid.'.php')){
	//Specific to particular collection
	include('../editor/includes/config/occurVarColl'.$collid.'.php');
}
elseif(file_exists('../editor/includes/config/occurVarDefault.php')){
	//Specific to Default values for portal
	include('../editor/includes/config/occurVarDefault.php');
}
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>">
		<title><?php echo $DEFAULT_TITLE; ?> Specimen Label Manager</title>
		<?php
		$activateJQuery = false;
		if(file_exists($SERVER_ROOT.'/includes/head.php')){
			include_once($SERVER_ROOT.'/includes/head.php');
		}
		else{
			echo '<link href="'.$CLIENT_ROOT.'/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
			echo '<link href="'.$CLIENT_ROOT.'/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
			echo '<link href="'.$CLIENT_ROOT.'/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
		}
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
				if(!status) alert("Date entered must follow the format YYYY-MM-DD");
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
			   	alert("At least one specimen checkbox needs to be selected with a label quantity greater than 0");
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
				if(action == "labeldynamic.php" && buttonElem.value == "Print in Browser"){
					if(!f["labelformatindex"] || f["labelformatindex"].value == ""){
						alert("Please select a Label Format Profile");
						return false;
					}
				}
				else if(action == "labelsword.php" && f.labeltype.valye == "packet"){
					alert("Packet labels are not yet available as a Word document");
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
			fieldset{ margin:10px; padding:15px; }
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
		<a href='../../index.php'>Home</a> &gt;&gt;
		<?php
		if(stripos(strtolower($labelManager->getMetaDataTerm('colltype')), "observation") !== false){
			echo '<a href="../../profile/viewprofile.php?tabindex=1">Personal Management Menu</a> &gt;&gt; ';
		}
		else{
			echo '<a href="../misc/collprofiles.php?collid='.$collid.'&emode=1">Collection Management Panel</a> &gt;&gt; ';
		}
		?>
		<b>Label Printing</b>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<?php
		if($isEditor){
			$reportsWritable = false;
			if(is_writable($SERVER_ROOT.'/temp/report')) $reportsWritable = true;
			if(!$reportsWritable){
				?>
				<div style="padding:5px;">
					<span style="color:red;">Please contact the site administrator to make temp/report folder writable in order to export to docx files.</span>
				</div>
				<?php
			}
			$isGeneralObservation = (($labelManager->getMetaDataTerm('colltype') == 'General Observations')?true:false);
			echo '<h2>'.$labelManager->getCollName().'</h2>';
			?>
			<div>
				<form name="datasetqueryform" action="labelmanager.php" method="post" onsubmit="return validateQueryForm(this)">
					<fieldset>
						<legend><b>Define Specimen Recordset</b></legend>
						<div style="margin:3px;">
							<div title="Scientific name as entered in database.">
								<?php echo (defined('SCIENTIFICNAMELABEL')?SCIENTIFICNAMELABEL:'Scientific Name'); ?>:
								<input type="text" name="taxa" id="taxa" size="60" value="<?php echo (array_key_exists('taxa',$_REQUEST)?$_REQUEST['taxa']:''); ?>" />
							</div>
						</div>
						<div style="margin:3px;clear:both;">
							<div style="float:left;" title="Full or last name of collector as entered in database.">
								<?php echo (defined('RECORDEDBYLABEL')?RECORDEDBYLABEL:'Collector'); ?>:
								<input type="text" name="recordedby" style="width:150px;" value="<?php echo (array_key_exists('recordedby',$_REQUEST)?$_REQUEST['recordedby']:''); ?>" />
							</div>
							<div style="float:left;margin-left:20px;" title="Separate multiple terms by comma and ranges by ' - ' (space before and after dash required), e.g.: 3542,3602,3700 - 3750">
								<?php echo (defined('RECORDNUMBERLABEL')?RECORDNUMBERLABEL:'Collector Number'); ?>(s):
								<input type="text" name="recordnumber" style="width:150px;" value="<?php echo (array_key_exists('recordnumber',$_REQUEST)?$_REQUEST['recordnumber']:''); ?>" />
							</div>
							<div style="float:left;margin-left:20px;" title="Separate multiple terms by comma and ranges by ' - ' (space before and after dash required), e.g.: 3542,3602,3700 - 3750">
								<?php echo (defined('CATALOGNUMBERLABEL')?CATALOGNUMBERLABEL:'Catalog Number'); ?>(s):
								<input type="text" name="identifier" style="width:150px;" value="<?php echo (array_key_exists('identifier',$_REQUEST)?$_REQUEST['identifier']:''); ?>" />
							</div>
						</div>
						<div style="margin:3px;clear:both;">
							<div style="float:left;">
								Entered by:
								<input type="text" name="recordenteredby" value="<?php echo (array_key_exists('recordenteredby',$_REQUEST)?$_REQUEST['recordenteredby']:''); ?>" style="width:100px;" title="login name of data entry person" />
							</div>
							<div style="margin-left:20px;float:left;" title="">
								Date range:
								<input type="text" name="date1" style="width:100px;" value="<?php echo (array_key_exists('date1',$_REQUEST)?$_REQUEST['date1']:''); ?>" onchange="validateDateFields(this.form)" /> to
								<input type="text" name="date2" style="width:100px;" value="<?php echo (array_key_exists('date2',$_REQUEST)?$_REQUEST['date2']:''); ?>" onchange="validateDateFields(this.form)" />
								<select name="datetarget">
									<option value="dateentered">Date Entered</option>
									<option value="datelastmodified" <?php echo (isset($_POST['datetarget']) && $_POST['datetarget'] == 'datelastmodified'?'SELECTED':''); ?>>Date Modified</option>
									<option value="eventdate"<?php echo (isset($_POST['datetarget']) && $_POST['datetarget'] == 'eventdate'?'SELECTED':''); ?>>Date Collected</option>
								</select>
							</div>
						</div>
						<div style="margin:3px;clear:both;">
							<?php echo (defined('LABELPROJECTLABEL')?LABELPROJECTLABEL:'Label Project'); ?>:
							<select name="labelproject" >
								<option value="">All Projects</option>
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
							<?php
							echo '<span style="margin-left:15px;"><input name="extendedsearch" type="checkbox" value="1" '.(array_key_exists('extendedsearch', $_POST)?'checked':'').' /></span> ';
							if($isGeneralObservation) echo 'Search outside user profile';
							else echo 'Search within all collections';
							?>
						</div>
						<div style="clear:both;">
							<div style="margin-left:20px;float:left;">
								<input type="hidden" name="collid" value="<?php echo $collid; ?>" />
								<input type="submit" name="submitaction" value="Filter Specimen Records" />
							</div>
							<div style="margin-left:20px;float:left;">
								* Specimen return is limited to 400 records
							</div>
						</div>
					</fieldset>
				</form>
				<div style="clear:both;">
					<?php
					if($action == "Filter Specimen Records"){
						if($occArr){
							?>
							<form name="selectform" id="selectform" action="labeldynamic.php" method="post" onsubmit="return validateSelectForm(this);">
								<table class="styledtable" style="font-family:Arial;font-size:12px;">
									<tr>
										<th title="Select/Deselect all Specimens"><input type="checkbox" onclick="selectAll(this);" /></th>
										<th title="Label quantity">Qty</th>
										<th><?php echo (defined('RECORDEDBYLABEL')?RECORDEDBYLABEL:'Collector'); ?></th>
										<th><?php echo (defined('SCIENTIFICNAMELABEL')?SCIENTIFICNAMELABEL:'Scientific Name'); ?></th>
										<th><?php echo (defined('LOCALITYLABEL')?LOCALITYLABEL:'Locality'); ?></th>
									</tr>
									<?php
									$trCnt = 0;
									foreach($occArr as $occId => $recArr){
										$trCnt++;
										?>
										<tr <?php echo ($trCnt%2?'class="alt"':''); ?>>
											<td>
												<input type="checkbox" name="occid[]" value="<?php echo $occId; ?>" />
											</td>
											<td>
												<input type="text" name="q-<?php echo $occId; ?>" value="<?php echo $recArr["q"]; ?>" style="width:20px;border:inset;" title="Label quantity" />
											</td>
											<td>
												<a href="#" onclick="openIndPopup(<?php echo $occId; ?>); return false;">
													<?php echo $recArr["c"]; ?>
												</a>
												<?php
												if($IS_ADMIN || (array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($recArr["collid"],$USER_RIGHTS["CollAdmin"])) || (array_key_exists("CollEditor",$USER_RIGHTS) && in_array($recArr["collid"],$USER_RIGHTS["CollEditor"]))){
													if(!$isGeneralObservation || $recArr['uid'] == $SYMB_UID){
														?>
														<a href="#" onclick="openEditorPopup(<?php echo $occId; ?>); return false;">
															<img src="../../images/edit.png" />
														</a>
														<?php
													}
												}
												?>
											</td>
											<td>
												<?php echo $recArr["s"]; ?>
											</td>
											<td>
												<?php echo $recArr["l"]; ?>
											</td>
										</tr>
										<?php
									}
									?>
								</table>
								<fieldset style="margin-top:15px;">
									<legend>Label Printing</legend>
										<div class="fieldDiv">
											<div class="fieldLabel">Label Profiles:
												<?php
												echo '<span title="Open label profile manager"><a href="labelprofile.php?collid='.$collid.'"><img src="../../images/edit.png" style="width:13px" /></a></span>';
												?>
											</div>
											<div class="fieldElement">
												<div>
													<select name="labelformatindex" onchange="labelFormatChanged(this)">
														<option value="">Select a Label Format</option>
														<?php
														foreach($labelFormatArr as $cat => $catArr){
															echo '<option value="">---------------------------</option>';
															foreach($catArr as $k => $labelArr){
																echo '<option value="'.$cat.'-'.$k.'">'.$labelArr['title'].'</option>';
															}
														}
														?>
													</select>
												</div>
												<?php
												if(!$labelFormatArr) echo '<b>label profiles have not yet been set within portal</b>';
												?>
											</div>
										</div>
									<div class="fieldDiv">
										<div class="fieldLabel">Heading Prefix:</div>
										<div class="fieldElement">
											<input type="text" name="hprefix" value="" style="width:450px" /> (e.g. Plants of, Insects of, Vertebrates of)
										</div>
									</div>
									<div class="fieldDiv">
										<div class="checkboxLabel">Heading Mid-Section:</div>
										<div class="fieldElement">
											<input type="radio" id="hmid1" name="hmid" value="1" /><?php echo (defined('COUNTRYLABEL')?COUNTRYLABEL:'Country');?>
											<input type="radio" id="hmid2" name="hmid" value="2" /><?php echo (defined('STATEPROVINCELABEL')?STATEPROVINCELABEL:'State');?>
											<input type="radio" id="hmid3" name="hmid" value="3" /><?php echo (defined('COUNTYLABEL')?COUNTYLABEL:'County');?>
											<input type="radio" id="hmid4" name="hmid" value="4" /><?php echo (defined('FAMILYLABEL')?FAMILYLABEL:'Family');?>
											<input type="radio" id="hmid0" name="hmid" value="0" checked/>Blank
										</div>
									</div>
									<div class="fieldDiv">
										<span class="fieldLabel">Heading Suffix:</span>
										<span class="fieldElement">
											<input type="text" name="hsuffix" value="" style="width:450px" />
										</span>
									</div>
									<div class="fieldDiv">
										<span class="fieldLabel">Label Footer:</span>
										<span class="fieldElement">
											<input type="text" name="lfooter" value="" style="width:450px" />
										</span>
									</div>
									<div class="fieldDiv">
										<input type="checkbox" name="speciesauthors" value="1" onclick="checkBarcodeCheck(this.form);" />
										<span class="checkboxLabel">Print species authors for infraspecific taxa</span>
									</div>
									<div class="fieldDiv">
										<input type="checkbox" name="catalognumbers" value="1" onclick="checkBarcodeCheck(this.form);" />
										<span class="checkboxLabel">Print <?php echo (defined('CATALOGNUMBERLABEL')?CATALOGNUMBERLABEL:'Catalog Number');?>s</span>
									</div>
									<?php
									if(class_exists('Image_Barcode2') || class_exists('Image_Barcode')){
										?>
										<div class="fieldDiv">
											<input type="checkbox" name="bc" value="1" onclick="checkBarcodeCheck(this.form);" />
											<span class="checkboxLabel">Include barcode of <?php echo (defined('CATALOGNUMBERLABEL')?CATALOGNUMBERLABEL:'Catalog Number');?></span>
										</div>
										<!--
										<div class="fieldDiv">
											<input type="checkbox" name="symbbc" value="1" onclick="checkBarcodeCheck(this.form);" />
											<span class="checkboxLabel">Include barcode of Symbiota Identifier</span>
										</div>
										 -->
										<div class="fieldDiv">
											<input type="checkbox" name="bconly" value="1" onclick="checkPrintOnlyCheck(this.form);" />
											<span class="checkboxLabel">Print only Barcode</span>
										</div>
										<?php
									}
									?>
									<div class="fieldDiv">
										<span class="fieldLabel">Label Type:</span>
										<span class="fieldElement">
											<select name="labeltype">
												<option value="1">1 columns per page</option>
												<option value="2" selected>2 columns per page</option>
												<option value="3">3 columns per page</option>
												<option value="4">4 columns per page</option>
												<option value="5">5 columns per page</option>
												<option value="6">6 columns per page</option>
												<option value="7">7 columns per page</option>
												<option value="packet">Packet labels</option>
											</select>
										</span>
									</div>
									<div style="float:left;margin: 15px 50px;">
										<input type="hidden" name="collid" value="<?php echo $collid; ?>" />
										<div style="margin:10px">
											<input type="submit" name="submitaction" onclick="return changeFormExport(this,'labeldynamic.php','_blank');" value="Print in Browser" <?php echo ($labelFormatArr?'':'DISABLED title="Browser based label printing has not been activated within the portal. Contact Portal Manager to activate this feature."'); ?> />
										</div>
										<div style="margin:10px">
											<input type="submit" name="submitaction" onclick="return changeFormExport(this,'labeldynamic.php','_self');" value="Export to CSV" />
										</div>
										<?php
										if($reportsWritable){
											?>
											<div style="margin:10px">
												<input type="submit" name="submitaction" onclick="return changeFormExport(this,'labelsword.php','_self');" value="Export to DOCX" />
											</div>
											<?php
										}
										?>
									</div>
										<?php
										if($reportsWritable){
											?>
											<div style="clear:both;padding:10px 0px">
												<b>Note:</b> Currently, Word (DOCX) output only generates the old static label format.<br/>Output of variable Label Formats (pulldown options) as a Word document is not yet supported.<br/>
												A possible work around is to print labels as PDF and then convert to a Word doc using Adobe tools.<br/>
												Another alternatively, is to output the data as CSV and then setup a Mail Merge Word document.
											</div>
											<?php
										}
										?>
								</fieldset>
							</form>
							<?php
						}
						else{
							?>
							<div style="font-weight:bold;margin:20px;font-weight:150%;">
								Query returned no data!
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
				You do not have permissions to print labels for this collection.
				Please contact the site administrator to obtain the necessary permissions.
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