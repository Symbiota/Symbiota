<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT . '/classes/DwcArchiverCore.php');

if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/profile/personalspecbackup.' . $LANG_TAG . '.php'))
	include_once($SERVER_ROOT.'/content/lang/profile/personalspecbackup.' . $LANG_TAG . '.php');
else include_once($SERVER_ROOT . '/content/lang/profile/personalspecbackup.en.php');
header('Content-Type: text/html; charset=' . $CHARSET);

$collid = filter_var($_REQUEST['collid'], FILTER_SANITIZE_NUMBER_INT);
$characterSet = array_key_exists('cset',$_REQUEST) ? $_REQUEST['cset'] : 'UTF-8';
$action = array_key_exists('formsubmit', $_REQUEST) ? $_REQUEST['formsubmit'] : '';

$editable = 0;
if($IS_ADMIN || !empty($USER_RIGHTS['CollAdmin'][$collid]) || !empty($USER_RIGHTS['CollEditor'][$collid])){
	$editable = 1;
}
$statusStr = '';
if($action == 'Perform Backup'){
	if ($collid) {
		$dwcaHandler = new DwcArchiverCore();
		$dwcaHandler->setSchemaType('backup');
		$dwcaHandler->setObserverUid($SYMB_UID);
		$dwcaHandler->setCharSetOut($characterSet);
		$dwcaHandler->setVerboseMode(0);
		$dwcaHandler->setIncludeDets(1);
		$dwcaHandler->setIncludeImgs(1);
		$dwcaHandler->setIncludeAttributes(1);
		if ($dwcaHandler->hasMaterialSamples($collid)) $dwcaHandler->setIncludeMaterialSample(1);
		if ($dwcaHandler->hasIdentifiers($collid)) $dwcaHandler->setIncludeIdentifiers(1);
		$dwcaHandler->setRedactLocalities(0);
		$dwcaHandler->setCollArr($collid);

		$archiveFile = $dwcaHandler->createDwcArchive();

		if ($archiveFile) {
			ob_start();
			ob_clean();
			ob_end_flush();
			header('Content-Description: Backup File (DwC-Archive data package)');
			header('Content-Type: application/zip');
			header('Content-Disposition: attachment; filename=' . basename($archiveFile));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($archiveFile));
			//od_end_clean();
			readfile($archiveFile);
			unlink($archiveFile);
		} else {
			$errMsg = $dwcaHandler->getErrorMessage();
			if($errMsg) $statusStr = 'ERROR: ' . $errMsg;
			else $statusStr = 'ERROR creating output file. Query may not have included records.';
		}
	}
}
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET ?>">
	<title><?= $LANG['PERS_SPEC_BACKUP'] ?></title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
</head>
<body style="min-height: 90%">
<!-- This is inner text! -->
<div role="main" id="innertext">
	<h1 class="page-heading"><?= $LANG['PERS_SPEC_BACKUP']; ?></h1>
	<?php
	if($editable){
		if($statusStr){
			?>
			<div style="margin=20px"><?= $statusStr ?></div>
			<?php
		}
		?>
		<form name="buform" action="personalspecbackup.php" method="post">
			<fieldset style="padding:15px;">
				<legend><?= $LANG['DOWNLOAD_MOD'] ?></legend>
				<label><?= $LANG['DATA_SET'] ?>:</label>
				<div style="margin-left: 15px">
					<input type="radio" name="cset" value="ISO-8859-1" <?= ($characterSet == 'ISO-8859-1' ? 'checked' : ''); ?> /> <?= $LANG['ISO'] ?><br/>
					<input type="radio" name="cset" value="UTF-8" <?= ($characterSet == 'UTF-8' ? 'checked' : ''); ?> /> <?= $LANG['UTF'] ?>
				</div>
				<div style="margin: 10px">
					<input type="hidden" name="collid" value="<?= $collid; ?>" />
					<button type="submit" name="formsubmit" value="Perform Backup"><?= $LANG['BACKUP'] ?></button>
				</div>
			</fieldset>
		</form>
		<?php
	}
	?>
</div>
</body>
</html>