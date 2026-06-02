<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoUpload.php');
include_once($SERVER_ROOT.'/classes/EthnoDataManager.php');
header('Content-Type: text/html; charset='.$CHARSET);
if(!$SYMB_UID) {
	header('Location: ../../profile/index.php?refurl=' . $CLIENT_ROOT . '/ethno/manager/ethnoloader.php');
}

$collId = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';
$ulFileName = array_key_exists('ulfilename',$_REQUEST)?$_REQUEST['ulfilename']:'';

$isEditor = false;
if($SYMB_UID){
	if($IS_ADMIN || (array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid, $USER_RIGHTS['CollAdmin'], true))){
		$isEditor = true;
	}
	elseif((array_key_exists('CollEditor',$USER_RIGHTS) && in_array($collid, $USER_RIGHTS['CollEditor'], true))){
		$isEditor = true;
	}
}

$loaderManager = new EthnoUpload();
$ethnoDataManager = new EthnoDataManager();

$loaderManager->setCollId($collId);

$status = '';
$fieldMap = array();
if($isEditor){
	if($ulFileName){
		$loaderManager->setFileName($ulFileName);
	}
	else{
		$loaderManager->setUploadFile();
	}

	if(array_key_exists('sf',$_REQUEST)){
		$targetFields = $_REQUEST['tf'];
 		$sourceFields = $_REQUEST['sf'];
		for($x = 0, $xMax = count($targetFields); $x< $xMax; $x++){
			if($targetFields[$x] && $sourceFields[$x]) {
				$fieldMap[$sourceFields[$x]] = $targetFields[$x];
			}
		}
	}

	if($action === 'downloadsynopticcsv'){
		$loaderManager->exportSynopticUpload();
		exit;
	}

	if($action === 'downloadvernacularcsv'){
		$loaderManager->exportVernacularUpload();
		exit;
	}
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> - Glossary Term Loader</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<link href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui.js"></script>
	<script src="../../js/jquery.manifest.js" type="text/javascript"></script>
	<script src="../../js/jquery.marcopolo.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../js/symb/glossary.index.js"></script>
	<script type="text/javascript">
		function verifyUploadForm(f){
			/*var inputValue = f.uploadfile.value;
			if(inputValue.indexOf(".csv") == -1 && inputValue.indexOf(".CSV") == -1 && inputValue.indexOf(".zip") == -1){
				alert("Upload file must be a .csv or .zip file.");
				return false;
			}
			return true;*/
		}
	</script>
</head>
<body>
<?php
include($SERVER_ROOT.'/includes/header.php');

echo '<div class="navpath">';
echo '<a href="../../index.php">Home</a> &gt;&gt; ';
echo '<a href="../../collections/misc/collprofiles.php?collid='.$collId.'&emode=1">Collection Control Panel</a> &gt;&gt; ';
echo '<b>Upload Ethnobiological Data</b>';
echo '</div>';

if($isEditor){
	?>
	<div id="innertext">
		<h1>Batch Ethnobiological Data Loader</h1>
		<div style="margin:30px;">
			<?php
			if($action === 'Map Synoptic Data File' || $action === 'Verify Synoptic Mapping'){
				?>
				<form name="mapform" action="ethnoloader.php" method="post">
					<fieldset style="width:90%;">
						<legend style="font-weight:bold;font-size:120%;">Synoptic Data Upload Form</legend>
						<div style="margin:10px;">
						</div>
						<table style="border:1px solid black;">
							<tr>
								<th style='padding:2px;'>
									Source Field
								</th>
								<th style='padding:2px;'>
									Target Field
								</th>
							</tr>
							<?php
							$fArr = $loaderManager->getFieldArr('synoptic');
							$sArr = $fArr['source'];
							$tArr = $fArr['target'];
							asort($tArr);
							foreach($sArr as $sField){
								?>
								<tr>
									<td style='padding:2px;'>
										<?php echo $sField; ?>
										<input type="hidden" name="sf[]" value="<?php echo $sField; ?>" />
									</td>
									<td style='padding:2px;'>
										<select name="tf[]" style="background:yellow">
											<option value="unmapped">Field Unmapped</option>
											<option value="unmapped">-------------------------</option>
											<?php
											$selStr = '';
											echo "<option value='unmapped' ".$selStr.'>Leave Field Unmapped</option>';
											if($selStr){
												$selStr = 0;
											}
											foreach($tArr as $k => $tField){
												if($selStr !== 0){
													if($tField===$sField){
														$selStr = 'SELECTED';
													}
													elseif($tField===$sField.'_term'){
														$selStr = 'SELECTED';
													}
												}
												echo '<option value="'.$tField.'" '.($selStr?:'').'>'.$tField."</option>\n";
												if($selStr){
													$selStr = 0;
												}
											}
											?>
										</select>
									</td>
								</tr>
								<?php
							}
							?>
						</table>
						<div style="margin:10px;">
							<input type="submit" name="action" value="Upload Synoptic Data" />
							<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
							<input type="hidden" name="ulfilename" value="<?php echo $loaderManager->getFileName();?>" />
						</div>
					</fieldset>
				</form>
				<?php
			}
			elseif($action === 'Map Vernacular Data File' || $action === 'Verify Vernacular Mapping'){
				?>
				<form name="mapform" action="ethnoloader.php" method="post">
					<fieldset style="width:90%;">
						<legend style="font-weight:bold;font-size:120%;">Vernacular Data Upload Form</legend>
						<div style="margin:10px;">
						</div>
						<table style="border:1px solid black;">
							<tr>
								<th style='padding:2px;'>
									Source Field
								</th>
								<th style='padding:2px;'>
									Target Field
								</th>
							</tr>
							<?php
							$fArr = $loaderManager->getFieldArr('vernacular');
							$sArr = $fArr['source'];
							$tArr = $fArr['target'];
							asort($tArr);
							foreach($sArr as $sField){
								?>
								<tr>
									<td style='padding:2px;'>
										<?php echo $sField; ?>
										<input type="hidden" name="sf[]" value="<?php echo $sField; ?>" />
									</td>
									<td style='padding:2px;'>
										<select name="tf[]" style="background:yellow">
											<option value="unmapped">Field Unmapped</option>
											<option value="unmapped">-------------------------</option>
											<?php
											$selStr = '';
											echo "<option value='unmapped' ".$selStr.'>Leave Field Unmapped</option>';
											if($selStr){
												$selStr = 0;
											}
											foreach($tArr as $k => $tField){
												if($selStr !== 0){
													if($tField===$sField){
														$selStr = 'SELECTED';
													}
													elseif($tField===$sField.'_term'){
														$selStr = 'SELECTED';
													}
												}
												echo '<option value="'.$tField.'" '.($selStr?:'').'>'.$tField."</option>\n";
												if($selStr){
													$selStr = 0;
												}
											}
											?>
										</select>
									</td>
								</tr>
								<?php
							}
							?>
						</table>
						<div style="margin:10px;">
							<input type="submit" name="action" value="Upload Vernacular Data" />
							<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
							<input type="hidden" name="ulfilename" value="<?php echo $loaderManager->getFileName();?>" />
						</div>
					</fieldset>
				</form>
				<?php
			}
			elseif($action === 'Upload Synoptic Data'){
				echo '<ul>';
				$loaderManager->loadSynopticFile($fieldMap);
				$loaderManager->cleanSynopticUpload();
				$loaderManager->analysisSynopticUpload();
				echo '</ul>';
				?>
				<form name="transferform" action="ethnoloader.php" method="post">
					<fieldset style="width:450px;">
						<legend style="font-weight:bold;font-size:120%;">Transfer Synoptic Data To Central Table</legend>
						<div style="margin:10px;">
							Review upload totals below before activating. Use the download option to review and/or adjust for reload if necessary.
						</div>
						<div style="margin:10px;">
							<?php
							$statArr = $loaderManager->getStatArr();
							if($statArr){
								if(isset($statArr['upload'])) {
									echo '<u>Records uploaded</u>: <b>' . $statArr['upload'] . '</b><br/>';
								}
								echo '<u>Total records</u>: <b>'.$statArr['total'].'</b><br/>';
								echo '<u>Records already in database</u>: <b>'.(isset($statArr['exist'])?$statArr['exist']:0).'</b><br/>';
								echo '<u>New records</u>: <b>'.(isset($statArr['new'])?$statArr['new']:0).'</b><br/>';
							}
							else{
								echo 'Upload totals are unavailable';
							}
							?>
						</div>
						<div style="margin:10px;">
							<input type="submit" name="action" value="Activate Synoptic Data" />
							<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
						</div>
						<div style="float:right;margin:10px;">
							<a href="ethnoloader.php?action=downloadsynopticcsv" >Download CSV Terms File</a>
						</div>
					</fieldset>
				</form>
				<?php
			}
			elseif($action === 'Upload Vernacular Data'){
				echo '<ul>';
				$loaderManager->loadVernacularFile($fieldMap);
				$loaderManager->cleanVernacularUpload();
				$loaderManager->analysisVernacularUpload();
				echo '</ul>';
				?>
				<form name="transferform" action="ethnoloader.php" method="post">
					<fieldset style="width:450px;">
						<legend style="font-weight:bold;font-size:120%;">Transfer Vernacular Data To Central Table</legend>
						<div style="margin:10px;">
							Review upload totals below before activating. Use the download option to review and/or adjust for reload if necessary.
						</div>
						<div style="margin:10px;">
							<?php
							$statArr = $loaderManager->getStatArr();
							if($statArr){
								if(isset($statArr['upload'])) {
									echo '<u>Records uploaded</u>: <b>' . $statArr['upload'] . '</b><br/>';
								}
								echo '<u>Total records</u>: <b>'.$statArr['total'].'</b><br/>';
								echo '<u>Records already in database</u>: <b>'.(isset($statArr['exist'])?$statArr['exist']:0).'</b><br/>';
								echo '<u>New records</u>: <b>'.(isset($statArr['new'])?$statArr['new']:0).'</b><br/>';
								if(isset($statArr['bad'])) {
									echo '<u>Records that cannot be linked to an occurrence or a data event, or cannot be linked to a consultant associated with this project</u> (will not be uploaded): <b>' . $statArr['bad'] . '</b><br/>';
								}
							}
							else{
								echo 'Upload totals are unavailable';
							}
							?>
						</div>
						<div style="margin:10px;">
							<input type="submit" name="action" value="Activate Vernacular Data" />
							<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
						</div>
						<div style="float:right;margin:10px;">
							<a href="ethnoloader.php?action=downloadvernacularcsv" >Download CSV Terms File</a>
						</div>
					</fieldset>
				</form>
				<?php
			}
			elseif($action === 'Activate Synoptic Data'){
				echo '<ul>';
				$loaderManager->transferSynopticUpload();
				echo '<li>Synoptic data upload appears to have been successful.</li>';
				echo '</ul>';
			}
			elseif($action === 'Activate Vernacular Data'){
				echo '<ul>';
				$loaderManager->transferVernacularUpload();
				echo '<li>Vernacular data upload appears to have been successful.</li>';
				echo '</ul>';
			}
			else{
				?>
				<div>
					<form name="synopticuploadform" action="ethnoloader.php" method="post" enctype="multipart/form-data" onsubmit="return verifyUploadForm(this)">
						<fieldset style="width:90%;">
							<legend style="font-weight:bold;font-size:120%;">Synoptic Data Upload Form</legend>
							<div style="margin:10px;">
								Flat structured, CSV (comma delimited) text files can be uploaded here.
								Columns can be added for the namedatadiscussion, usedatadiscussion,
								and consultantdiscussion for data collection events.
								Please do not use spaces in the column names or file names.
							</div>
							<input type='hidden' name='MAX_FILE_SIZE' value='100000000' />
							<div>
								<div class="overrideopt">
									<b>Upload File:</b>
									<div style="margin:10px;">
										<input id="synopticuploadfile" name="uploadfile" type="file" size="40" />
									</div>
								</div>
								<div style="margin:10px;">
									<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
									<input type="submit" name="action" value="Map Synoptic Data File" />
								</div>
							</div>
						</fieldset>
					</form>
				</div>
				<div>
					<form name="vernacularuploadform" action="ethnoloader.php" method="post" enctype="multipart/form-data" onsubmit="return verifyUploadForm(this)">
						<fieldset style="width:90%;">
							<legend style="font-weight:bold;font-size:120%;">Vernacular Data Upload Form</legend>
							<div style="margin:10px;">
								Flat structured, CSV (comma delimited) text files can be uploaded here.
								Columns can be added for the verbatimVernacularName, annotatedVernacularName, verbatimLanguage,
								languageGlottologId, otherVerbatimVernacularName, otherLanguageGlottologId, verbatimParse,
								annotatedParse, verbatimGloss, annotatedGloss, typology, translation, taxonomicDescription,
								nameDiscussion, consultantComments, and useDiscussion.
								Please do not use spaces in the column names or file names.
							</div>
							<input type='hidden' name='MAX_FILE_SIZE' value='100000000' />
							<div>
								<div class="overrideopt">
									<b>Upload File:</b>
									<div style="margin:10px;">
										<input id="vernacularuploadfile" name="uploadfile" type="file" size="40" />
									</div>
								</div>
								<div style="margin:10px;">
									<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
									<input type="submit" name="action" value="Map Vernacular Data File" />
								</div>
							</div>
						</fieldset>
					</form>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	<?php
}
else{
	?>
	<div style='font-weight:bold;margin:30px;'>
		You do not have permissions to batch upload ethnobiological data
	</div>
	<?php
}
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>
