<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/ShipmentManager.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl='.$CLIENT_ROOT.'/neon/shipment/manifestloader.php');

$action = array_key_exists("action",$_REQUEST)?$_REQUEST["action"]:"";
$ulFileName = array_key_exists("ulfilename",$_REQUEST)?$_REQUEST["ulfilename"]:"";

$isEditor = false;
if($IS_ADMIN) $isEditor = true;
elseif(array_key_exists('CollAdmin',$USER_RIGHTS) || array_key_exists('CollEditor',$USER_RIGHTS)) $isEditor = true;

$loaderManager = new ShipmentManager();

$fieldMap = array();
if($isEditor){
	if($ulFileName){
		$loaderManager->setUploadFileName($ulFileName);
	}
	if(array_key_exists("sf",$_POST)){
		//Grab field mapping, if mapping form was submitted
		$targetFields = $_REQUEST["tf"];
 		$sourceFields = $_REQUEST["sf"];
		for($x = 0;$x<count($targetFields);$x++){
			if($targetFields[$x] && $sourceFields[$x]) $fieldMap[$sourceFields[$x]] = $targetFields[$x];
		}
		$loaderManager->setFieldMap($fieldMap);
	}
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Manifest Loader</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>" />
	<?php
	$activateJQuery = true;
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="../../js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="../../js/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		function verifyUploadForm(f){
			var status = true;
			var fileName = f.uploadfile.value;
			if(fileName == ""){
				alert("Select a manifest file to upload");
				return false;
			}
			else{
				var ext = fileName.split('.').pop().toLowerCase();
				if(ext == "xlsx" || ext == "xls"){
					alert("Unable to import Excel files (.xlsx, .xls). Save file in the CSV format.");
					return false;
				}
				else if(ext != "csv"){
					status = confirm("Is the import file in the CSV format? If not, select cancel, save file in the CSV format, and reimport.");
				}
			}
			return status;
		}

		function verifyMappingForm(f){
			var sfArr = [];
			var tfArr = [];
			for(var i=0;i<f.length;i++){
				var obj = f.elements[i];
				if(obj.name == "sf[]"){
					if(obj.value.trim() != "" && sfArr.indexOf(obj.value) > -1){
						alert("ERROR: Source field names must be unique (duplicate field: "+obj.value+")");
						return false;
					}
					sfArr[sfArr.length] = obj.value;
				}
				else if(obj.name == "tf[]" && obj.value != "" && obj.value != "unmapped" && obj.value != "dynamicProperties" && obj.value != "symbiotaTarget"){
					if(tfArr.indexOf(obj.value) > -1){
						alert('ERROR: Can\'t map to the same target field "'+obj.value+'" more than once');
						return false;
					}
					tfArr[tfArr.length] = obj.value;
				}
			}
		}
	</script>
</head>
<body>
<?php
$displayLeftMenu = false;
include($SERVER_ROOT.'/includes/header.php');
?>
<div class="navpath">
	<a href="../../index.php">Home</a> &gt;&gt;
	<a href="../index.php"><b>NEON Biorepository Management Tools</b></a> &gt;&gt;
	<b>Manifest Loader</b>
</div>
<?php
if($isEditor){
	?>
	<div id="innertext">
		<h1>Manifest Loader</h1>
		<div style="margin:30px;">
			<?php
			if($action == 'Map Input File'){
				if(!$ulFileName) $loaderManager->uploadManifestFile();
				$analyzeStatus = $loaderManager->analyzeUpload();
				$errCode = 1;
				if(!$analyzeStatus){
					$errStr = $loaderManager->getErrorStr();
					if(strpos($errStr,'shipment already in system')){
						echo $errStr;
						?>
						<div>Are you sure you want to append the data to existing shipment?</div>
						<div style="margin-left:15px">If so, <a href="#" onclick="$('#mappingFormDiv').show();return false">click here to continue</a></div>
						<div style="margin-left:15px">If not, modify the shipmentIDs for each record and <a href="manifestloader.php">reload the manifest</a></div>
						<?php
						$errCode = 2;
					}
					else{
						echo '<div style="font-weight:bold">ERROR analyzing import file: '.$errStr.'</div>';
						$errCode = 0;
					}
				}
				if($errCode){
					?>
					<div id="mappingFormDiv" style="<?php if($errCode==2) echo 'display:none'; ?>">
						<form name="mappingform" action="manifestloader.php" method="post" onsubmit="return verifyMappingForm(this)">
							<fieldset style="width:90%;">
								<legend style="font-weight:bold;font-size:120%;">Manifest Upload Form</legend>
								<div style="margin:10px;">
								</div>
								<table class="styledtable" style="width:350px;">
									<tr>
										<th>
											Source Field
										</th>
										<th>
											Target Field
										</th>
									</tr>
									<?php
									$sourceArr = $loaderManager->getSourceArr();
									$targetArr = $loaderManager->getTargetArr();
									$symbTargetArr = $loaderManager->getSymbTargetArr();
									$translationMap = array('shipdate'=>'dateshipped','sentto'=>'senttoid','remarks'=>'shipmentnotes','siteid'=>'namedlocation','deprecatedsampleid'=>'alternativesampleid',
										'containerid'=>'dynamicproperties','containerlocation'=>'dynamicproperties','sampletype'=>'dynamicproperties','containerid'=>'dynamicproperties',
										'plateid'=>'dynamicproperties','platebarcode'=>'dynamicproperties', 'wellcoordinates'=>'dynamicproperties', 'samplesecondarybag'=>'dynamicproperties');
									foreach($sourceArr as $sourceField){
										?>
										<tr>
											<td style='padding:2px;'>
												<?php echo $sourceField; ?>
												<input type="hidden" name="sf[]" value="<?php echo $sourceField; ?>" />
											</td>
											<td>
												<?php
												$translatedSourceField = strtolower($sourceField);
												if(array_key_exists($translatedSourceField, $translationMap)) $translatedSourceField = $translationMap[$translatedSourceField];
												$bgColor = 'yellow';
												if($loaderManager->array_key_iexists($translatedSourceField,$fieldMap)) $bgColor = 'white';
												elseif($loaderManager->in_iarray($translatedSourceField, $targetArr)) $bgColor = 'white';
												elseif($loaderManager->in_iarray($translatedSourceField,$symbTargetArr)) $bgColor = 'white';
												?>
												<select name="tf[]" style="background:<?php echo $bgColor; ?>">
													<option value="">Field Unmapped</option>
													<option value="">-------------------------</option>
													<?php
													$matchTerm = '';
													if($loaderManager->array_key_iexists($translatedSourceField,$fieldMap)) $matchTerm = strtolower($fieldMap[$translatedSourceField]);
													else $matchTerm = $translatedSourceField;
													foreach($targetArr as $targetField){
														echo '<option '.($matchTerm==strtolower($targetField)?'SELECTED':'').'>'.$targetField.'</option>';
													}
													echo '<option value="">-------------------------</option>';
													foreach($symbTargetArr as $symbTerm){
														echo '<option '.($translatedSourceField==strtolower($symbTerm)?'SELECTED':'').'>symb:'.$symbTerm.'</option>';
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
									<input type="checkbox" name="reloadSamples" value="1" /> Reload sample record if it already exists
								</div>
								<div style="margin:10px;">
									<input type="submit" name="action" value="Process Manifest" />
									<input type="hidden" name="ulfilename" value="<?php echo $loaderManager->getUploadFileName(); ?>" />
								</div>
							</fieldset>
						</form>
					</div>
					<?php
				}
			}
			elseif($action == 'Process Manifest'){
				echo '<ul>';
				$loaderManager->setUploadFileName($ulFileName);
				$loaderManager->setFieldMap($fieldMap);
				if(array_key_exists('reloadSamples',$_POST)) $loaderManager->setReloadSampleRecs($_POST['reloadSamples']);
				$shipmentPKArr = $loaderManager->uploadData();
				echo '</ul>';
				?>
				<fieldset>
					<legend><b>Manifests Associated with Shipment</b></legend>
					<?php
					foreach($shipmentPKArr as $shipmentID => $shipmentPK){
						echo '<div style="margin-left:10px">';
						if($shipmentPK) echo '<a href="manifestviewer.php?shipmentPK='.$shipmentPK.'">#'.$shipmentID.'</a>';
						else echo 'Manifest #'.$shipmentID.' failed to load';
						echo '</div>';
					}
					?>
				</fieldset>
				<div style="margin:10px 0px"><a href="manifestloader.php">Load Another Manifest</a></div>
				<div style="margin:10px 0px"><a href="manifestsearch.php">List and Search Manifests</a></div>
				<?php
			}
			else{
				?>
				<div>
					<form name="uploadform" action="manifestloader.php" method="post" enctype="multipart/form-data" onsubmit="return verifyUploadForm(this)">
						<fieldset style="width:90%;">
							<legend style="font-weight:bold;font-size:120%;">Manifest Upload Form</legend>
							<div style="margin:10px;">
							</div>
							<input type='hidden' name='MAX_FILE_SIZE' value='10000000' />
							<div>
								<div style="margin:10px;">
									<input id="genuploadfile" name="uploadfile" type="file" size="40" />
								</div>
								<div style="margin:10px;">
									<input type="submit" name="action" value="Map Input File" />
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
		You do not have permissions to upload and process NEON manifests
	</div>
	<?php
}
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>