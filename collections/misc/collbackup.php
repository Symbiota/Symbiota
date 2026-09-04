<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/DwcArchiverPublisher.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load('collections/misc/collbackup');

header('Content-Type: text/html; charset=' . $CHARSET);

$collid = isset($_REQUEST['collid']) ? filter_var($_REQUEST['collid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$action = isset($_POST['formsubmit']) ? $_POST['formsubmit'] : '';
$cSet = isset($_POST['cset']) ? $_POST['cset'] : '';
$backupFile = isset($_REQUEST['bufile']) ? $_REQUEST['bufile'] : '';

$isEditor = 0;
if($IS_ADMIN){
	$isEditor = 1;
}
elseif($collid && isset($USER_RIGHTS['CollAdmin']) && in_array($collid, $USER_RIGHTS['CollAdmin'])){
	$isEditor = 1;
}
if($isEditor){
	if(preg_match('/_backup_\d{4}-\d{2}-\d{2}_\d{6}_DwC-A\.zip$/', $backupFile)){
		$dwcaHandler = new DwcArchiverCore();
		$path = $dwcaHandler->getTargetPath();
		$archiveFile = $path . $backupFile;
		while (ob_get_level()) {
			ob_end_clean();
		}
		header('Content-Description: ' . $LANG['OCCURRENCE_BAKUP_FILE']);
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename=' . basename($archiveFile));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($archiveFile));
		readfile($archiveFile);
		unlink($archiveFile);
		exit;
	}
}
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<title><?= $LANG['OCCURRENCE_DOWNLOAD'] ?></title>
	<?php
	include_once($SERVER_ROOT . '/includes/head.php');
	?>
	<script>
		function fileDownloaded(){
			document.getElementById("download-link").style.display = "none"
			document.getElementById("file-downloaded-span").style.display = "inline"
		}
	</script>
    <style>
    	fieldset{ padding:15px;width:350px }
    	legend{ font-weight: bold }
    	label{ font-weight: bold }
    	#workingdiv{ float:left; margin-left:15px; font-weight: bold; }
    </style>
</head>
<body>
	<div role="main" id="innertext">
		<h1 class="page-heading screen-reader-only"><?= $LANG['DOWNLOAD_MODULE'] ?></h1>
		<?php
		if($isEditor){
			if($action == 'preformBackup'){
				if ($collid && is_numeric($collid)) {
					$dwcaHandler = new DwcArchiverCore();
					$dwcaHandler->setCollArr($collid);
					$dwcaHandler->setSchemaType('backup');
					$dwcaHandler->setCharSetOut($cSet);
					$dwcaHandler->setVerboseMode(2);
					$dwcaHandler->setIncludeDets(1);
					$dwcaHandler->setIncludeImgs(1);
					$dwcaHandler->setIncludeAttributes(1);
					$dwcaHandler->setIncludeMaterialSample(1);
					$dwcaHandler->setIncludeIdentifiers(1);
					$dwcaHandler->setIncludeAssociations(1);
					$dwcaHandler->setRedactLocalities(0);
					$archiveFile = $dwcaHandler->createDwcArchive();

					if ($archiveFile) {
						$filename = substr($archiveFile, strrpos($archiveFile, '/') + 1);
						?>
						<div id="download-div">
							<?= $LANG['BACKUP_FILE'] ?>:
							<a id="download-link" href="collbackup.php?collid=<?= $collid ?>&bufile=<?= $filename ?>" onclick="fileDownloaded()"><?= $filename ?></a>
							<span id="file-downloaded-span" style="display:none"><b><?= $LANG['DOWNLOAD_COMPLETE'] ?></b></span>
						</div>
						<?php
					} else {
						$errMsg = $dwcaHandler->getErrorMessage();
						if($errMsg) echo $errMsg;
						else echo $LANG['ERROR_CREATING_OUTPUT'];
					}
				}
			}
			else{
				?>
				<form name="buform" action="collbackup.php" method="post" onsubmit="">
					<fieldset>
						<legend><?= $LANG['DOWNLOAD_MODULE'] ?></legend>
						<div style="height:50px; margin: 10px">
							<input type="radio" id="cset1" name="cset" value="iso-8859-1" <?= ($cSet == 'iso88591' ? 'checked' : ''); ?> /> <label for="cset1">ISO-8859-1 (western)</label><br/>
							<input type="radio" id="cset2" name="cset" value="utf-8" <?= (!$cSet || $cSet == 'utf8' ? 'checked' : ''); ?> /> <label for="cset2">UTF-8 (unicode)</label>
						</div>
						<div>
							<div>
								<input type="hidden" name="collid" value="<?= $collid; ?>">
								<button type="submit" name="formsubmit" value="preformBackup"><?= $LANG['DOWNLOAD'] ?></button>
							</div>
						</div>
					</fieldset>
				</form>
				<?php
			}
		}
		?>
	</div>
</body>
</html>
