<?php
include_once('../../config/symbini.php');
// TODO: double check what is this file for
include_once($SERVER_ROOT.'/classes/OccurrenceEditorDeterminations.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/collections/editor/transcribe.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/collections/editor/transcribe.'.$LANG_TAG.'.php');
else include_once($SERVER_ROOT.'/content/lang/collections/editor/transcribe.en.php');
header("Content-Type: text/html; charset=".$CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/editor/transcribe.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$crowdSourceMode = array_key_exists('csmode', $_REQUEST) ? filter_var($_REQUEST['csmode'], FILTER_SANITIZE_NUMBER_INT) : 0;
$goToMode = array_key_exists('gotomode', $_REQUEST) ? filter_var($_REQUEST['gotomode'], FILTER_SANITIZE_NUMBER_INT) : 0;

$occManager = new OccurrenceEditorDeterminations();
$occManager->setCollId($collid);
$collMap = $occManager->getCollMap();
$collid = $_REQUEST['collid'];
$qryCnt = $occManager->getQueryRecordCount();

if($collMap){
	if($collMap['colltype']=='General Observations'){
		$isGenObs = 1;
		$collType = 'obs';
	}
	elseif($collMap['colltype']=='Observations'){
		$collType = 'obs';
	}
	$propArr = $occManager->getDynamicPropertiesArr();
	if(isset($propArr['modules-panel'])){
		foreach($propArr['modules-panel'] as $module){
			if(isset($module['paleo']['status']) && $module['paleo']['status']) $moduleActivation[] = 'paleo';
			elseif(isset($module['matSample']['status']) && $module['matSample']['status']){
				$moduleActivation[] = 'matSample';
				if($tabTarget > 3) $tabTarget++;
			}
		}
	}
}

$isEditor = 0;
$batchIds = $occManager->getBatch();

if($IS_ADMIN || (array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollAdmin"]))){
	$isEditor = 1;
}
elseif(array_key_exists("CollEditor",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollEditor"])){
	$isEditor = 1;
}
$statusStr = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	if (isset($_POST["batchID"])) {
		$selectedBatchID = $_POST["batchID"];
		$imgIDs = $occManager->getImgIDs($selectedBatchID);
	} else {
		$imgIDs = $occManager->getAllImgIDs();		
	}
	$firstImgId = $imgIDs[0];
	$firstBarcode = !empty($occManager->getBarcode($firstImgId)) ? ($occManager->getBarcode($firstImgId)) : 0;
	$firstIndex = 0;
	$lastImgId = end($imgIDs);
	$lastBarcode = !empty($occManager->getBarcode($lastBarcode)) ? ($occManager->getBarcode($lastBarcode)) : 0;
	$lastIndex = count($imgIDs) - 1;
	$occData = array();
	$lastEditImgId = $occManager->getlastEdit($selectedBatchID);
	$lastEditBarcode = !empty($occManager->getBarcode($lastEditImgId)) ? ($occManager->getBarcode($lastEditImgId)) : 0;
	$lastEditIndex = $occManager->getImgIndex($lastEditImgId) - 1;
	// occData is a hashtable, which has imgid as key, and occid as value
	foreach ($imgIDs as $imgID) {
        $occData[$imgID] = $occManager->getOneOccID($imgID);
    }
	$firstOccId = !empty($occData[$firstImgId]) ? ($occData[$firstImgId]) : 0;
	$lastOccId = !empty($occData[$lastImgId]) ? ($occData[$lastImgId]) : 0;
	$lastEditOccId = !empty($occData[$lastEditImgId]) ? ($occData[$lastEditImgId]) : 0;
}

?>

<html>
	<head>
	    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>">
		<title><?php echo $DEFAULT_TITLE.$LANG['IMAGE_BATCH']; ?></title>
		<?php
		$activateJQuery = true;
		if(file_exists($SERVER_ROOT.'/includes/head.php')){
			include_once($SERVER_ROOT.'/includes/head.php');
		}
		else{
			echo '<link href="'.$CLIENT_ROOT.'/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
			echo '<link href="'.$CLIENT_ROOT.'/css/basse.css?ver=1" type="text/css" rel="stylesheet" />';
			echo '<link href="'.$CLIENT_ROOT.'/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
		}
		?>
		<script src="../../js/jquery.js" type="text/javascript"></script>
		<script src="../../js/jquery-ui.js" type="text/javascript"></script>
		<script src="../../js/symb/collections.editor.query.js?ver=5" type="text/javascript"></script>
		<script type="text/javascript">
			// function 
			// TODO: currently, we leave the occIndex, it is exactly the same value as imgIndex
			function navigateToRecordNew(crowdSourceMode, gotomode, collId, batchId, imgId, imgIndex, barcode, occId, occIndex) {
				if(barcode == 0 && occId == 0) {
					var url = 'occurrencequickentry.php?csmode=' + crowdSourceMode + '&collid=' + collId +'&batchid=' + batchId + '&imgid=' + imgId + '&imgindex=' + imgIndex + '&barcode=' + 0 + '&occid=' + 0;
				} else if(barcode == 0) {
					var url = 'occurrencequickentry.php?csmode=' + crowdSourceMode + '&collid=' + collId +'&batchid=' + batchId + '&imgid=' + imgId + '&imgindex=' + imgIndex + '&barcode=' + 0 + '&occid=' + occId + '&occindex=' + occIndex;
				} else if(occId == 0) {
					var url = 'occurrencequickentry.php?csmode=' + crowdSourceMode + '&collid=' + collId +'&batchid=' + batchId + '&imgid=' + imgId + '&imgindex=' + imgIndex + '&barcode=' + barcode + '&occid=' + 0;
				} else {
					var url = 'occurrencequickentry.php?csmode=' + crowdSourceMode + '&collid=' + collId +'&batchid=' + batchId + '&imgid=' + imgId + '&imgindex=' + imgIndex + '&barcode=' + barcode + '&occid=' + occId + '&occindex=' + occIndex;
				}
				window.location.href = url;
				event.preventDefault();
			}

			function initScinameAutocomplete(f){
				$( f.sciname ).autocomplete({
					source: "rpc/getspeciessuggest.php",
					minLength: 3,
					change: function(event, ui) {
					}
				});
			}

		</script>
	</head>
	<body>
	<?php
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class='navpath'>
		<a href='../../index.php'><?php echo $LANG['HOME']; ?></a> &gt;&gt;
		<a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1"><?php echo $LANG['COLL_MANAGE']; ?></a> &gt;&gt;
		<b><?php echo $LANG['BATCH_DETERS']; ?></b>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<?php
		if($isEditor){
			?>
			<div style="margin:0px;">
				<fieldset style="padding:10px;">
					<legend><b><?php echo $LANG['TRANSCRIBE_INTO_SPECIFY']; ?></b></legend>
					<div style="margin:15px;width:700px;">
                        <!-- TODO: update the submit function of the form -->
						<form name="batchform" method="post">
                            <div style="margin-bottom:15px;">
								<h4>Work On batch: <?php echo($selectedBatchID) ?></h4>
								<button type="button" name="first" onclick="return navigateToRecordNew(<?php echo ($crowdSourceMode).', '.($goToMode).', '.($collid).', '.($selectedBatchID).', '.($firstImgId).', '.($firstIndex).', '.($firstBarcode).', '.($firstOccId).', '.($firstIndex) ; ?>)"><?php echo $LANG['START_FROM']; ?> first.</button>
                                <button type="button" name="last" onclick="return navigateToRecordNew(<?php echo ($crowdSourceMode).', '.($goToMode).', '.($collid).', '.($selectedBatchID).', '.($lastImgId).', '.($lastIndex).', '.($lastBarcode).', '.($lastOccId).', '.($lastIndex); ?>)"><?php echo $LANG['START_FROM']; ?> last.</button>
								<button type="button" name="lastView" onclick="return navigateToRecordNew(<?php echo ($crowdSourceMode).', '.($goToMode).', '.($collid).', '.($selectedBatchID).', '.($lastEditImgId).', '.($lastEditIndex).', '.($lastEditBarcode).', '.($lastEditOccId).', '.($lastEditIndex); ?>)"><?php echo $LANG['START_FROM']; ?> last edit.</button>
                            </div>
							<div>
								<b><?php echo $LANG['WORK_ON_BATCH']; ?></b>
								<select id="batchID" name="batchID" style="width:400px;" onchange="this.form.submit()">
									<option value="">-- Select Batch --</option>
									<?php
									foreach ($batchIds as $batchID) {
										echo "<option value=\"$batchID\">Batch $batchID</option>";
									}
									?>
								</select>
							</div>
						</form>
					</div>
				</fieldset>
				<!-- TODO: need to figure out what this status is -->
				<fieldset>
					<div>
						<p style="margin:0px;"><?php echo $LANG['STATUS']; ?></p>
					</div>
				</fieldset>
			</div>
			<?php
		}
		else{
			?>
			<div style="font-weight:bold;margin:20px;font-weight:150%;">
				<?php echo $LANG['NO_PERMISSIONS']; ?>
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