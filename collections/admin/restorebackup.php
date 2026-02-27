<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SpecUploadDwca.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load([
	'collections/admin/restorebackup',
	'collections/admin/specupload'
]);

header("Content-Type: text/html; charset=".$CHARSET);
ini_set('max_execution_time', 3600);
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/admin/reloadbackup.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = filter_var($_REQUEST['collid'], FILTER_SANITIZE_NUMBER_INT);
$includeIdentificationHistory = !empty($_POST['includeidentificationhistory']) ? 1 : '';
$includeImages = !empty($_POST['includeimages']) ? 1 : '';
$ulPath = array_key_exists('ulpath',$_REQUEST) ? $_POST['ulpath'] : '';
$action = array_key_exists('action', $_REQUEST) ? $_POST['action'] : '';

$duManager = new SpecUploadDwca();
$duManager->setCollId($collid);
$duManager->setUploadType(10);
$duManager->setTargetPath($ulPath);
$duManager->setIncludeIdentificationHistory($includeIdentificationHistory);
$duManager->setIncludeImages($includeImages);
$duManager->setMatchCatalogNumber(false);
$duManager->setMatchOtherCatalogNumbers(false);
$duManager->setVerifyImageUrls(false);

$isEditor = 0;
if($IS_ADMIN || (array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollAdmin"]))){
	$isEditor = 1;
}

$isLiveData = false;
if($duManager->getCollInfo("managementtype") == 'Live Data') $isLiveData = true;

//Grab field mapping, if mapping form was submitted
if(array_key_exists("sf",$_POST)){
	//Set field map for occurrences using mapping form
	$targetFields = $_POST["tf"];
	$sourceFields = $_POST["sf"];
	$fieldMap = Array();
	for($x = 0;$x<count($targetFields);$x++){
		if($targetFields[$x]){
			$tField = $targetFields[$x];
			if($tField == 'unmapped') $tField .= '-'.$x;
			$fieldMap[$tField]["field"] = $sourceFields[$x];
		}
	}
	//Set Source PK
	$duManager->setFieldMap($fieldMap);

	//Set field map for identification history
	if(array_key_exists("ID-sf",$_POST)){
		$targetIdFields = $_POST["ID-tf"];
		$sourceIdFields = $_POST["ID-sf"];
		$fieldIdMap = Array();
		for($x = 0;$x<count($targetIdFields);$x++){
			if($targetIdFields[$x]){
				$tIdField = $targetIdFields[$x];
				if($tIdField == 'unmapped') $tIdField .= '-'.$x;
				$fieldIdMap[$tIdField]["field"] = $sourceIdFields[$x];
			}
		}
		$duManager->setIdentFieldMap($fieldIdMap);
	}
	//Set field map for image history
	if(array_key_exists("IM-sf",$_POST)){
		$targetImFields = $_POST["IM-tf"];
		$sourceImFields = $_POST["IM-sf"];
		$fieldImMap = Array();
		for($x = 0;$x<count($targetImFields);$x++){
			if($targetImFields[$x]){
				$tImField = $targetImFields[$x];
				if($tImField == 'unmapped') $tImField .= '-'.$x;
				$fieldImMap[$tImField]["field"] = $sourceImFields[$x];
			}
		}
		$duManager->setImageFieldMap($fieldImMap);
	}
}
$duManager->loadFieldMap(true);
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET ?>">
	<title><?= $DEFAULT_TITLE . ' ' . $LANG['RESTORE'] ?></title>
	<link href="<?= $CSS_BASE_PATH ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script src="../../js/symb/shared.js" type="text/javascript"></script>
	<script>

		function verifyFileUploadForm(f){
			var fileName = "";
			if(f.uploadfile && f.uploadfile.value){
				 fileName = f.uploadfile.value;
			}
			else{
				fileName = f.ulfnoverride.value;
			}
			if(fileName == ""){
				alert("<?= $LANG['PATH_EMPTY'] ?>");
				return false;
			}
			else{
				var ext = fileName.split('.').pop();
				if(ext == 'zip' || ext == 'ZIP') return true;
				else{
					alert("<?= $LANG['MUST_ZIP'] ?>");
					return false;
				}
			}
			return true;
		}

		function verifyFileSize(inputObj){
			inputObj.form.ulfnoverride.value = ''
			if (!window.FileReader) {
				//alert("<?= $LANG['API_SUP'] ?>");
				return;
			}
			<?php
			$maxUpload = ini_get('upload_max_filesize');
			$maxUpload = str_replace("M", "000000", $maxUpload);
			if($maxUpload > 100000000) $maxUpload = 100000000;
			echo 'var maxUpload = '.$maxUpload.";\n";
			?>
			var file = inputObj.files[0];
			if(file.size > maxUpload){
				var msg = "<?= $LANG['IMPORT_FILE'] ?>"+file.name+" ("+Math.round(file.size/100000)/10+"<?= $LANG['IS_BIGGER'] ?>"+(maxUpload/1000000)+"MB).";
				if(file.name.slice(-3) != "zip") msg = msg + "<?= $LANG['MAYBE_ZIP'] ?>";
				alert(msg);
		    }
		}
	</script>
	<style>
		 .icon-img { width: 1.1em; }
	</style>
</head>
<body>
	<?php
$displayLeftMenu = false;
include($SERVER_ROOT.'/includes/header.php');
?>
<div class="navpath">
	<a href="../../index.php"><?= $LANG['HOME'] ?></a> &gt;&gt;
	<a href="../misc/collprofiles.php?collid=<?= $collid ?>&emode=1"><?= $LANG['COL_MGMNT'] ?></a> &gt;&gt;
	<b><?= $LANG['BACKUP_MOD'] ?></b>
</div>
<!-- This is inner text! -->
<div role="main" id="innertext">
	<h1 class="page-heading"><?= $LANG['RESTORE_COLLEC_FROM_LIST'] ?></h1>
	<?php
	$recReplaceMsg = '<span style="color:orange"><b>' . $LANG['CAUTION'] . ':</b></span> ' . $LANG['MATCH_REPLACE'];
	if($isEditor){
		if($collid){
			echo '<div style="font-weight:bold;font-size:130%;margin-bottom:20px">'.$duManager->getCollInfo('name').'</div>';
			if(!$action){
				?>
				<form name="fileuploadform" action="restorebackup.php" method="post" enctype="multipart/form-data" onsubmit="return verifyFileUploadForm(this)">
					<fieldset style="padding:25px;width:95%;">
						<legend style="font-weight:bold;"><?= $LANG['SEL_BACKUP'] ?></legend>
						<div>
							<div>
								<input name="uploadfile" type="file" size="50" onchange="verifyFileSize(this)" />
							</div>
							<div class="ulfnoptions" style="display:none;margin:15px 0px">
								<b><?= $LANG['RESOURCE_URL'] ?>:</b>
								<input name="ulfnoverride" type="text" size="70" /><br/>
								<div>
									<?= $LANG['WORKAROUND'] ?>
								</div>
							</div>
						</div>
						<div style="margin:10px 0px;">
							<input name="includeidentificationhistory" type="checkbox" value="1" checked /> <?= $LANG['RESTORE_DETS'] ?><br/>
							<input name="includeimages" type="checkbox" value="1" checked /> <?= $LANG['RESTORE_MEDIA_LINKS'] ?><br/>
						</div>
						<div style="margin:10px 0px;">
							<button name="action" type="submit" value="AnalyzeFile"><?= $LANG['ANALYZE'] ?></button>
							<input name="collid" type="hidden" value="<?= $collid ?>" />
							<input name="MAX_FILE_SIZE" type="hidden" value="100000000" />
						</div>
						<div class="ulfnoptions">
							<a href="#" onclick="toggle('ulfnoptions');return false;"><?= $LANG['MANUAL'] ?></a>
						</div>
					</fieldset>
				</form>
				<?php
			}
			elseif($action == 'AnalyzeFile' || $action == 'Continue with Restore'){
				$uploadData = false;
				if($action == 'AnalyzeFile'){
					if($ulPath = $duManager->uploadFile()){
						if($verificationResult = $duManager->verifyBackupFile()){
							if($verificationResult === true){
								$uploadData = true;
							}
							elseif(is_array($verificationResult)){
								?>
								<form name="filemappingform" action="restorebackup.php" method="post" onsubmit="return verifyMappingForm(this)">
									<fieldset style="width:95%;padding:15px">
										<legend style="font-weight:bold;font-size:120%;"><?= $LANG['BACKUP_MOD'] ?></legend>
										<div style="margin:15px">
											<div style="color:orange;font-weight:bold"><?= $LANG['WARNINGS'] ?>:</div>
											<div style="margin:10px">
												<?php
												foreach($verificationResult as $warningStr){
													if($warningStr == 'UnableToLocateCollectionElement') echo '<div><b>WARNING:</b> does NOT appear to be a valid backup file; unable to locate collection element within eml.xml</div>';
													elseif($warningStr == 'CollectionIdNotMatching') echo '<div><b>ABORT:</b> does NOT appear to be a valid backup file for this collection; collection ID not matching target collection</div>';
													elseif($warningStr == 'CollectionGuidNotMatching') echo '<div><b>ABORT:</b> does NOT appear to be a valid backup file for this collection; collection GUID not matching target collection</div>';
													elseif($warningStr == 'MultipleCollectionElements') echo '<div><b>WARNING:</b> does NOT appear to be a valid backup file; more than one collection element located within eml.xml</div>';
												}
												?>
												<div style="margin-top: 10px"><?= $LANG['LIVE_DANGEROUSLY'] ?></div>
											</div>
										</div>
										<div style="margin:20px;">
											<!--
											<input type="submit" name="action" value="Continue with Restore" />
											 -->
 										</div>
									</fieldset>
									<input name="includeidentificationhistory" type="hidden" value="<?= $includeIdentificationHistory ?>" />
									<input name="includeimages" type="hidden" value="<?= $includeImages ?>" />
									<input name="collid" type="hidden" value="<?= $collid ?>" />
									<input name="ulpath" type="hidden" value="<?= $ulPath ?>" />
								</form>
								<?php
							}
						}
						else{
							echo '<div><span style="color:red">' . $LANG['FATAL_ERROR'] . ':</span> ';
							$errCode = $duManager->getErrorStr();
							if($errCode == 'OccurrencesMissing') echo $LANG['OCCURRENCES_MISSING_ERROR'];
							elseif($errCode == 'MetaMissing') echo $LANG['META_MISSING_ERROR'];
							elseif($errCode == 'MalformedMeta') echo $LANG['MALFORMED_META_ERROR'];
							elseif($errCode == 'EmlMissing') echo $LANG['EML_MISSING_ERROR'];
							elseif($errCode == 'MediaMissing') echo $LANG['MEDIA_MISSING_ERROR'];
							elseif($errCode == 'IdentificationsMissing') echo $LANG['ID_MISSING_ERROR'];
							echo '</div>';
						}
					}
				}
				if($action == 'Continue with Restore' || $uploadData){
					echo "<div style='font-weight:bold;font-size:120%'>Upload Status:</div>";
					echo "<ul style='margin:10px;font-weight:bold;'>";
					$duManager->uploadData(false);
					$duManager->cleanBackupReload();
					echo "</ul>";
					if($duManager->getTransferCount()){
						?>
						<fieldset style="margin:15px;">
							<legend style=""><b><?= $LANG['FINAL_T'] ?></b></legend>
							<div style="margin:5px;">
								<?php
								$reportArr = $duManager->getTransferReport();
								echo '<div>' . $LANG['OCCS_TRANSFERING'] . ': ' . $reportArr['occur'];
								if($reportArr['occur']){
									echo ' <a href="uploadreviewer.php?collid=' . $collid . '" target="_blank" title="' . $LANG['PREVIEW'] . '"><img class="icon-img" src="../../images/list.png" ></a>';
									echo ' <a href="uploadreviewer.php?action=export&collid=' . $collid . '" target="_self" title="' . $LANG['DOWNLOAD_RECS'] . '"><img class="icon-img" src="../../images/dl.png" ></a>';
								}
								echo '</div>';
								echo '<div style="margin-left:15px;">';
								echo '<div>Records to be updated: ';
								echo $reportArr['update'];
								if($reportArr['update']){
									echo ' <a href="uploadreviewer.php?collid=' . $collid . '&searchvar=occid:NOT_NULL" target="_blank" title="' . $LANG['PREVIEW'] . '"><img class="icon-img" src="../../images/list.png"></a>';
									echo ' <a href="uploadreviewer.php?action=export&collid=' . $collid . '&searchvar=occid:NOT_NULL" target="_self" title="' . $LANG['DOWNLOAD_RECS'] . '"><img class="icon-img" src="../../images/dl.png" ></a>';
								}
								echo '</div>';
								if($reportArr['new']){
									echo '<div>Records to be restored: ';
									echo $reportArr['new'];
									if($reportArr['new']){
										echo ' <a href="uploadreviewer.php?collid=' . $collid . '&searchvar=new" target="_blank" title="' . $LANG['PREVIEW'] . '"><img class="icon-img" src="../../images/list.png" ></a>';
										echo ' <a href="uploadreviewer.php?action=export&collid=' . $collid . '&searchvar=new" target="_self" title="' . $LANG['DOWNLOAD_RECS'] . '"><img class="icon-img" src="../../images/dl.png" ></a>';
									}
									echo '</div>';
								}
								if(isset($reportArr['exist']) && $reportArr['exist']){
									echo '<div>Previous loaded records not matching incoming records: ';
									echo $reportArr['exist'];
									if($reportArr['exist']){
										echo ' <a href="uploadreviewer.php?collid=' . $collid . '&searchvar=exist" target="_blank" title="' . $LANG['PREVIEW'] . '"><img class="icon-img" src="../../images/list.png" ></a>';
										echo ' <a href="uploadreviewer.php?action=export&collid=' . $collid . '&searchvar=exist" target="_self" title="' . $LANG['DOWNLOAD_RECS'] . '"><img class="icon-img" src="../../images/dl.png" ></a>';
									}
									echo '</div>';
									echo '<div style="margin-left:15px;">';
									echo $LANG['DEL_OR_PREV'] . ', ';
									echo $LANG['OR_CONTACT'] . '. ';
									echo '</div>';
								}
								echo '</div>';
								//Extensions
								if(isset($reportArr['ident'])){
									echo '<div>' . $LANG['IDENT_TRANSFER'] . ': ' . $reportArr['ident'] . '</div>';
								}
								if(isset($reportArr['image'])){
									echo '<div>' . $LANG['IMAGE_TRANSFER'] . ': ' . $reportArr['image'] . '</div>';
								}

								?>
							</div>
							<form name="finaltransferform" action="restorebackup.php" method="post" style="margin-top:10px;" onsubmit="return confirm('Are you sure you want to transfer records from temporary table to central specimen table?');">
								<input name="includeidentificationhistory" type="hidden" value="<?= $includeIdentificationHistory ?>" />
								<input name="includeimages" type="hidden" value="<?= $includeImages ?>" />
								<input type="hidden" name="collid" value="<?= $collid ?>" />
								<div style="margin:5px;">
									<button name="action" type="submit" value="TransferRecords"><?= $LANG['FINALIZE_RESTORE'] ?></button>
								</div>
							</form>
						</fieldset>
						<?php
					}
				}
			}
			elseif($action == 'TransferRecords'){
				echo '<ul>';
				$duManager->finalTransfer();
				echo '</ul>';
			}
		}
		else{
			?>
			<div style="font-weight:bold;font-size:120%;">
				<?php
				echo $LANG['NO_SETTING'] . '. ' . ini_get("upload_max_filesize") . '; post_max_size = ' . ini_get("post_max_size") . '. ';
				echo $LANG['USE_BACK'];
				?>
			</div>
			<?php
		}
	}
	else{
		echo '<div style="font-weight:bold;font-size:120%;">' . $LANG['NOT_AUTH'] . '</div>';
	}
	?>
</div>
<?php
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>
