<?php
include_once('../../config/symbini.php');
// TODO: double check what is this file for
include_once($SERVER_ROOT.'/classes/OccurrenceEditorDeterminations.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/collections/editor/transcribe.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/collections/editor/transcribe.'.$LANG_TAG.'.php');
else include_once($SERVER_ROOT.'/content/lang/collections/editor/transcribe.en.php');
header("Content-Type: text/html; charset=".$CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/editor/transcribe.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$crowdSourceMode = array_key_exists('csmode',$_REQUEST)?$_REQUEST['csmode']:0;
$collid = $_REQUEST["collid"];
$firstImgId = 1;
$firstImgIndex = 0;
// $FirstoccIDs = $occManager->getOneOccID($firstImgId);

if(!is_numeric($collid)) $collid = 0;

$occManager = new OccurrenceEditorDeterminations();
$occManager->setCollId($collid);
$collMap = $occManager->getCollMap();

if($collId && isset($collMap['collid']) && $collId != $collMap['collid']){
	$collId = $collMap['collid'];
	$occManager->setCollId($collId);
}
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

// post the selected batch ID
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	if (isset($_POST["batchID"])) {
		$selectedBatchID = $_POST["batchID"];
		$imgIDs = $occManager->getImgIDs($selectedBatchID);
	} 
} else {
	$imgIDs = $occManager->getAllImgIDs();
}
$occData = array();
foreach ($imgIDs as $imgID) {
	$occIDs = $occManager->getOccIDs($imgID);
	$occData[$imgID] = $occIDs;
}
$allOccIDs = array();
foreach ($occData as $occIDs) {
	$allOccIDs = array_merge($allOccIDs, $occIDs);
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
			function navigateToRecordNew(occindex, occid, collid, csmode, batchid, imgid) {
				var url = 'occurrencequickentry.php?';
				url += 'csmode=' + csmode;
				url += '&occindex=' + occindex;
				url += '&occid=' + occid;
				url += '&collid=' + collid;
				url += '&batchid=' + batchid;
				url += '&imgid=' + imgid;

				window.location.href = url;
				return false;
			}

			function initScinameAutocomplete(f){
				$( f.sciname ).autocomplete({
					source: "rpc/getspeciessuggest.php",
					minLength: 3,
					change: function(event, ui) {
					}
				});
			}

            // TODO: update this batch form accordingly
			function submitBatchForm(f){
				var workingObj = document.getElementById("workingcircle");
				workingObj.style.display = "inline"
				var allCatNum = 0;
				if(f.allcatnum.checked) allCatNum = 1;

				$.ajax({
					type: "POST",
					url: "rpc/getnewdetitem.php",
					dataType: "json",
					data: {
						catalognumber: f.catalognumber.value,
						allcatnum: allCatNum,
						sciname: f.sciname.value,
						collid: f.collid.value
					}
				}).done(function( retStr ) {
					if(retStr != ""){
						for (var occid in retStr) {
							var occObj = retStr[occid];
							if(f.catalognumber.value && checkCatalogNumber(occid, occObj["cn"])){
								alert("<?php echo $LANG['RECORD_EXISTS']; ?>");
							}
							else{
								var trNode = createNewTableRow(occid, occObj);
								var tableBody = document.getElementById("catrecordstbody");
								tableBody.insertBefore(trNode, tableBody.firstElementChild);
							}
						}
						document.getElementById("accrecordlistdviv").style.display = "block";
					}
					else{
						alert("<?php echo $LANG['NO_RECORDS']; ?>");
					}
				});

				if(f.catalognumber.value != ""){
					f.catalognumber.value = '';
					f.catalognumber.focus();
				}
				workingObj.style.display = "none";
				return false;
			}
			// function updateSelectedBatch(selectElement) {
			// 	var selectedValue = selectElement.value; // Get the selected value
			// 	var selectedBatchElement = document.getElementById('slectedBatch'); // Get the <b> element
			// 	// Update the value of the <b> element
			// 	selectedBatchElement.textContent = 'Batch: ' + selectedValue;
			// }
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
						<div><h1><?php //print_r($imgIDs) ?></h1><div>
						<div><h1><?php print_r($occData) ?></h1><div>
						<div><h1><?php print_r($occData) ?></h1><div>
						<form name="batchform" action="transcribe.php" method="post">
                            <div style="margin-bottom:15px;">
                                <!-- TODO: figure out what is this line is, then customized the content -->
                                <!-- TODO: onclick function of the buttons -->
								<div>
									<?php //$url = 'occurrencequickentry.php?csmode='.$crowdSourceMode.'&occindex='.($firstOcc).'&occid='.($firstOcc+1).'&collid='.$collid; ?>
									<?php //$url = 'occurrencequickentry.php?csmode='.$crowdSourceMode.'&collid='.$collid.'&imgid='.($firstImgId).'&imgindex='.($firstImgIndex).'&occid='.($).'&occindex='.($firstOcc).'&occindex='.($firstOcc); ?>
									<a href=<?php echo($url) ?> >
										<h3>Go to the quick entry form</h3>
									</a>
								</div>
                                <!-- <b id="slectedBatch">Batch: </b><br> -->
								<button type="button" name="first" onclick="return navigateToRecordNew(<?php echo ($firstOccIDs[0]-1).', '.$firstOccIDs[0].', '.($collid + 1).', '.$crowdSourceMode.', '.$selectedBatchID; ?>)"><?php echo $LANG['START_FROM']; ?> first.</button>
                                <!-- TODO: need to customize the page number and set a veriable to start from the last page-->
                                <button type="button" name="last"  onclick="return navigateToRecordNew(<?php echo ($lastOccIDs[0]-1).', '.$lastOccIDs[0].', '.($collid + 1).', '.$crowdSourceMode.', '.$selectedBatchID; ?>)"><?php echo $LANG['START_FROM']; ?> last.</button>
								<button type="button" name="lastView"><?php echo $LANG['START_FROM']; ?> last view.</button>
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
				<!-- TODO: double check if we can just get ride of this parts -->
				<!-- <fieldset>
					<div>
						<b style="margin:0px;">
							<?php // if(array_key_exists('recordenteredby',$collArr)){
									//echo ($collArr['recordenteredby']?$collArr['recordenteredby']:$LANG['NO_RECORDS']);
								//}
								//if(isset($collArr['dateentered']) && $collArr['dateentered']) echo ' ['.$collArr['dateentered'].']'; 
								?>
							<?php //echo $jumpStr; ?>
						</b>
					</div>
				</fieldset> -->
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