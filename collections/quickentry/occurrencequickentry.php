<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/content/lang/collections/editor/occurrenceeditor.'.$LANG_TAG.'.php');

header("Content-Type: text/html; charset=".$CHARSET);

$occId = array_key_exists('occid',$_REQUEST)?$_REQUEST['occid']:'';
$collId = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:false;
$tabTarget = array_key_exists('tabtarget',$_REQUEST)?$_REQUEST['tabtarget']:0;
$goToMode = array_key_exists('gotomode',$_REQUEST)?$_REQUEST['gotomode']:0;
$occIndex = array_key_exists('occindex',$_REQUEST)?$_REQUEST['occindex']:false;
$crowdSourceMode = array_key_exists('csmode',$_REQUEST)?$_REQUEST['csmode']:0;
$action = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';
if(!$action && array_key_exists('carryover',$_REQUEST)) $goToMode = 2;

//Create Occurrence Manager
$occManager = null;
if(strpos($action,'Determination') || strpos($action,'Verification')){
	include_once($SERVER_ROOT.'/classes/OccurrenceEditorDeterminations.php');
	$occManager = new OccurrenceEditorDeterminations();
}
elseif(strpos($action,'Image')){
	include_once($SERVER_ROOT.'/classes/OccurrenceEditorImages.php');
	$occManager = new OccurrenceEditorImages();
}
else{
	include_once($SERVER_ROOT.'/classes/OccurrenceEditorManager.php');
	$occManager = new OccurrenceEditorManager();
}

if($crowdSourceMode){
	$occManager->setCrowdSourceMode(1);
}

if (isset($_REQUEST['batchid'])) {
    $batchId = $_REQUEST['batchid'];
    // Use $batchId as needed
	$imgIDs = $occManager->getImgIDs($batchId);
	// $batchSize = count($imgIDs);
	// $firstImgId = $imgIDs[0] ;
	$firstImgIndex = 0;
} else {
    $batchId = 0;
	$batchSize = 0;
	$firstImgId = 1;
	$firstImgIndex = 0;
}

if (isset($_REQUEST['imgid'])) {
    $imageId = $_REQUEST['imgid'];
	$imgIndex = $_REQUEST['imgindex'];
	// TODO: need to chec, if occID exists later
	$occID = $occManager->getOccIDs($imageId);
	// $occIndex = $occID - 1
} else {
    $imageId = 1;
	$imgIndex = 0;
}

//Sanitation
if(!is_numeric($occId)) $occId = '';
if(!is_numeric($collId)) $collId = false;
if(!is_numeric($tabTarget)) $tabTarget = 0;
if(!is_numeric($goToMode)) $goToMode = 0;
if(!is_numeric($occIndex)) $occIndex = false;
if(!is_numeric($crowdSourceMode)) $crowdSourceMode = 0;
$action = filter_var($action,FILTER_SANITIZE_STRING);

$displayQuery = 0;
$isGenObs = 0;
$collMap = Array();
$collType = 'spec';
$occArr = array();
$imgArr = array();
$specImgArr = array();
$fragArr = array();
$qryCnt = false;
$moduleActivation = array();
$statusStr = '';
$navStr = '';
$jumpStr = '';
$isEditor = 0;

// Dropdown arrays
$filedUnderDrop = $occManager->getValues('dd_filedUnderID', 'dropdown_filedUnder_values');
$specImgArr = $occManager->getImageMap();

if($SYMB_UID){
	//Set variables
	$occManager->setOccId($occId);
	$occManager->setCollId($collId);
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
	//Bring in config variables
	if($isGenObs){
		if(file_exists('includes/config/occurVarGenObs'.$SYMB_UID.'.php')){
			//Specific to particular collection
			include('includes/config/occurVarGenObs'.$SYMB_UID.'.php');
		}
		elseif(file_exists('includes/config/occurVarGenObsDefault.php')){
			//Specific to Default values for portal
			include('includes/config/occurVarGenObsDefault.php');
		}
	}
	else{
		if($collId && file_exists('includes/config/occurVarColl'.$collId.'.php')){
			//Specific to particular collection
			include('includes/config/occurVarColl'.$collId.'.php');
		}
		elseif(file_exists('includes/config/occurVarDefault.php')){
			//Specific to Default values for portal
			include('includes/config/occurVarDefault.php');
		}
		if($crowdSourceMode && file_exists('includes/config/crowdSourceVar.php')){
			//Specific to Crowdsourcing
			include('includes/config/crowdSourceVar.php');
		}
	}

	//0 = not editor, 1 = admin, 2 = editor, 3 = taxon editor, 4 = crowdsource editor or collection allows public edits
	//If not editor, edits will be submitted to omoccuredits table but not applied to omoccurrences
	if($IS_ADMIN || ($collId && array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collId,$USER_RIGHTS['CollAdmin']))){
		$isEditor = 1;
	}
	else{
		if($isGenObs){
			if(!$occId && array_key_exists('CollEditor',$USER_RIGHTS) && in_array($collId,$USER_RIGHTS['CollEditor'])){
				//Approved General Observation editors can add records
				$isEditor = 2;
			}
			elseif($action){
				//Lets assume that Edits where submitted and they remain on same specimen, user is still approved
				 $isEditor = 2;
			}
			elseif($occManager->getObserverUid() == $SYMB_UID){
				//Users can edit their own records
				$isEditor = 2;
			}
		}
		elseif(array_key_exists('CollEditor',$USER_RIGHTS) && in_array($collId,$USER_RIGHTS['CollEditor'])){
			//Is an assigned editor for this collection
			$isEditor = 2;
		}
		elseif($crowdSourceMode && $occManager->isCrowdsourceEditor()){
			//Is a crowdsourcing editor (CS status is open (=0) or CS status is pending (=5) and active user was original editor
			$isEditor = 4;
		}
		elseif($collMap && $collMap['publicedits']){
			//Collection is set as allowing public edits
			$isEditor = 4;
		}
		elseif(array_key_exists('CollTaxon',$USER_RIGHTS) && $occId){
			//Check to see if this user is authorized to edit this occurrence given their taxonomic editing authority
			$isEditor = $occManager->isTaxonomicEditor();
		}
	}
	include_once 'editProcessor.php';
	if($action == 'saveOccurEdits'){
		$statusStr = $occManager->editOccurrence($_POST,$isEditor);
	}
	if($isEditor && $isEditor != 3){
		if($action == 'Save OCR'){
			$statusStr = $occManager->insertTextFragment($_POST['imgid'],$_POST['rawtext'],$_POST['rawnotes'],$_POST['rawsource']);
			if(is_numeric($statusStr)){
				$newPrlid = $statusStr;
				$statusStr = '';
			}
		}
		elseif($action == 'Save OCR Edits'){
			$statusStr = $occManager->saveTextFragment($_POST['editprlid'],$_POST['rawtext'],$_POST['rawnotes'],$_POST['rawsource']);
		}
		elseif($action == 'Delete OCR'){
			$statusStr = $occManager->deleteTextFragment($_POST['delprlid']);
		}
	}
	if($isEditor){
		//Available to full editors and taxon editors
		if($action == 'submitDetermination'){
			//Adding a new determination
			$statusStr = $occManager->addDetermination($_POST,$isEditor);
			$tabTarget = 1;
		}
		elseif($action == 'submitDeterminationEdit'){
			$statusStr = $occManager->editDetermination($_POST);
			$tabTarget = 1;
		}
		elseif($action == 'Delete Determination'){
			$statusStr = $occManager->deleteDetermination($_POST['detid']);
			$tabTarget = 1;
		}
		//Only full editors can perform following actions
		if($isEditor == 1 || $isEditor == 2){
			if($action == 'addOccurRecord'){
				if($occManager->addOccurrence($_POST)){
					$occManager->setQueryVariables();
					$qryCnt = $occManager->getQueryRecordCount();
					$qryCnt++;
					if($goToMode) $occIndex = $qryCnt;			//Go to new record
					else $occId = $occManager->getOccId();		//Stay on record and get $occId
				}
				else $statusStr = $occManager->getErrorStr();
			}
			elseif($action == 'Delete Occurrence'){
				if($occManager->deleteOccurrence($occId)){
					$occId = 0;
					$occManager->setOccId(0);
				}
				else $statusStr = $occManager->getErrorStr();
			}
			elseif($action == 'Transfer Record'){
				$transferCollid = $_POST['transfercollid'];
				if($transferCollid){
					if($occManager->transferOccurrence($occId,$transferCollid)){
						if(!isset($_POST['remainoncoll']) || !$_POST['remainoncoll']){
							$occManager->setCollId($transferCollid);
							$collId = $transferCollid;
							$collMap = $occManager->getCollMap();
						}
					}
					else{
						$statusStr = $occManager->getErrorStr();
					}
				}
			}
			elseif($action == 'cloneRecord'){
				$cloneArr = $occManager->cloneOccurrence($_POST);
				if($cloneArr){
					$statusStr = (isset($LANG['CLONES_CREATED'])?$LANG['CLONES_CREATED']:'Success! The following new clone record(s) have been created').' ';
					$statusStr .= '<div style="margin:5px 10px;color:black">';
					$statusStr .= '<div><a href="occurrenceeditor.php?occid='.$occId.'" target="_blank">#'.$occId.'</a> - '.(isset($LANG['CLONE_SOURCE'])?$LANG['CLONE_SOURCE']:'clone source').'</div>';
					$occId = current($cloneArr);
					$occManager->setOccId($occId);
					foreach($cloneArr as $cloneOccid){
						if($cloneOccid==$occId) $statusStr .= '<div>#'.$cloneOccid.' - '.(isset($LANG['THIS_RECORD'])?$LANG['THIS_RECORD']:'this record').'</div>';
						else $statusStr .= '<div><a href="occurrenceeditor.php?occid='.$cloneOccid.'" target="_blank">#'.$cloneOccid.'</a></div>';
					}
					$statusStr .= '</div>';
					if(isset($_POST['targetcollid']) && $_POST['targetcollid'] && $_POST['targetcollid'] != $collId){
						$collId = $_POST['targetcollid'];
						$occManager->setCollId($collId);
						$collMap = $occManager->getCollMap();
					}
					$occManager->setQueryVariables(array('eb'=>$PARAMS_ARR['un'],'de'=>date('Y-m-d')));
					$qryCnt = $occManager->getQueryRecordCount();
					$occIndex = $qryCnt - count($cloneArr);
				}
			}
			elseif($action == 'Submit Image Edits'){
				$occManager->editImage($_POST);
				if($errArr = $occManager->getErrorArr()){
					if(isset($errArr['web'])){
						if(!$errArr['web']) $statusStr .= $LANG['ERROR_UPDATING_IMAGE'].': web image<br />';
					}
					if(isset($errArr['tn'])){
						if(!$errArr['tn']) $statusStr .= $LANG['ERROR_UPDATING_IMAGE'].': thumbnail<br />';
					}
					if(isset($errArr['orig'])){
						if(!$errArr['orig']) $statusStr .= $LANG['ERROR_UPDATING_IMAGE'].': large image<br />';
					}
					if(isset($errArr['error'])) $statusStr .= $LANG['ERROR_EDITING_IMAGE'].': '.$errArr['error'];
				}
				$tabTarget = 2;
			}
			elseif($action == 'Submit New Image'){
				if($occManager->addImage($_POST)){
					$statusStr = (isset($LANG['IMAGE_ADD_SUCCESS'])?$LANG['IMAGE_ADD_SUCCESS']:'Image added successfully');
					$tabTarget = 2;
				}
				if($occManager->getErrorStr()){
					$statusStr .= $occManager->getErrorStr();
				}
			}
			elseif($action == 'Delete Image'){
				$removeImg = (array_key_exists('removeimg',$_POST)?$_POST['removeimg']:0);
				if($occManager->deleteImage($_POST["imgid"], $removeImg)){
					$statusStr = (isset($LANG['IMAGE_DEL_SUCCESS'])?$LANG['IMAGE_DEL_SUCCESS']:'Image deleted successfully');
					$tabTarget = 2;
				}
				else{
					$statusStr = $occManager->getErrorStr();
				}
			}
			elseif($action == 'Remap Image'){
				if($occManager->remapImage($_POST['imgid'], $_POST['targetoccid'])){
					$statusStr = (isset($LANG['IMAGE_REMAP_SUCCESS'])?$LANG['IMAGE_REMAP_SUCCESS']:'SUCCESS: Image remapped to record').' <a href="occurrenceeditor.php?occid='.$_POST["targetoccid"].'" target="_blank">'.$_POST["targetoccid"].'</a>';
				}
				else{
					$statusStr = (isset($LANG['IMAGE_REMAP_ERROR'])?$LANG['IMAGE_REMAP_ERROR']:'ERROR linking image to new specimen').': '.$occManager->getErrorStr();
				}
			}
			elseif($action == 'remapImageToNewRecord'){
				$newOccid = $occManager->remapImage($_POST['imgid'], 'new');
				if($newOccid){
					$statusStr = (isset($LANG['IMAGE_REMAP_SUCCESS'])?$LANG['IMAGE_REMAP_SUCCESS']:'SUCCESS: Image remapped to record').' <a href="occurrenceeditor.php?occid='.$newOccid.'" target="_blank">'.$newOccid.'</a>';
				}
				else{
					$statusStr = (isset($LANG['NEW_IMAGE_ERROR'])?$LANG['NEW_IMAGE_ERROR']:'ERROR linking image to new blank specimen').': '.$occManager->getErrorStr();
				}
			}
			elseif($action == "Disassociate Image"){
				if($occManager->remapImage($_POST["imgid"])){
					$statusStr = (isset($LANG['DISASS_SUCCESS'])?$LANG['DISASS_SUCCESS']:'SUCCESS disassociating image').' <a href="../../imagelib/imgdetails.php?imgid='.$_POST["imgid"].'" target="_blank">#'.$_POST["imgid"].'</a>';
				}
				else{
					$statusStr = (isset($LANG['DISASS_ERORR'])?$LANG['DISASS_ERORR']:'ERROR disassociating image').': '.$occManager->getErrorStr();
				}

			}
			elseif($action == "Apply Determination"){
				$makeCurrent = 0;
				if(array_key_exists('makecurrent',$_POST)) $makeCurrent = 1;
				$statusStr = $occManager->applyDetermination($_POST['detid'],$makeCurrent);
				$tabTarget = 1;
			}
			elseif($action == "Make Determination Current"){
				$statusStr = $occManager->makeDeterminationCurrent($_POST['detid']);
				$tabTarget = 1;
			}
			elseif($action == "Submit Verification Edits"){
				$statusStr = $occManager->editIdentificationRanking($_POST['confidenceranking'],$_POST['notes']);
				$tabTarget = 1;
			}
			elseif($action == 'Link to Checklist as Voucher'){
				$statusStr = $occManager->linkChecklistVoucher($_POST['clidvoucher'],$_POST['tidvoucher']);
			}
			elseif($action == 'deletevoucher'){
				$statusStr = $occManager->deleteChecklistVoucher($_REQUEST['delclid']);
			}
			elseif($action == 'editgeneticsubmit'){
				$statusStr = $occManager->editGeneticResource($_POST);
			}
			elseif($action == 'deletegeneticsubmit'){
				$statusStr = $occManager->deleteGeneticResource($_POST['genid']);
			}
			elseif($action == 'addgeneticsubmit'){
				$statusStr = $occManager->addGeneticResource($_POST);
			}
		}
	}

	if($goToMode){
		//Adding new record, override query form and prime for current user's dataentry for the day
		$occId = 0;
		$occManager->setQueryVariables(array('eb'=>$PARAMS_ARR['un'],'de'=>date('Y-m-d')));
		if(!$qryCnt){
			$qryCnt = $occManager->getQueryRecordCount();
			$occIndex = $qryCnt;
		}
	}
	if(is_numeric($occIndex)){
		//Query Form has been activated
		$occManager->setQueryVariables();
		if($action == 'Delete Occurrence'){
			//Reset query form index to one less, unless it's already 1, then just reset
			$qryCnt = $occManager->getQueryRecordCount();		//Value won't be returned unless set in cookies in previous query
			if($qryCnt > 1){
				if(($occIndex + 1) >= $qryCnt) $occIndex = $qryCnt - 2;
				$qryCnt--;
			}
			else{
				unset($_SESSION['editorquery']);
				$occIndex = false;
			}
		}
		elseif($action == 'saveOccurEdits'){
			//Get query count and then reset; don't use new count for this display
			$qryCnt = $occManager->getQueryRecordCount();
			$occManager->getQueryRecordCount(1);
		}
		else{
			$qryCnt = $occManager->getQueryRecordCount();
		}
	}
	elseif(isset($_SESSION['editorquery'])){
		//Make sure query variables are null
		unset($_SESSION['editorquery']);
	}
	$occManager->setOccIndex($occIndex);

	if($occId || (!$goToMode && $occIndex !== false)){
		$oArr = $occManager->getOccurMap();
		if($oArr){
			$occId = $occManager->getOccId();
			$occArr = $oArr[$occId];
			$occIndex = $occManager->getOccIndex();
			if(!$collMap){
				$collMap = $occManager->getCollMap();
				if(!$isEditor){
					if(isset($USER_RIGHTS["CollAdmin"]) && in_array($collMap['collid'],$USER_RIGHTS["CollAdmin"])){
						$isEditor = 1;
					}
					elseif(isset($USER_RIGHTS["CollEditor"]) && in_array($collMap['collid'],$USER_RIGHTS["CollEditor"])){
						$isEditor = 1;
					}
				}
			}
		}
	}
	elseif($goToMode == 2) $occArr = $occManager->carryOverValues($_REQUEST);
	if(!$isEditor && $crowdSourceMode && $occManager->isCrowdsourceEditor()) $isEditor = 4;

	// TODO: check here for navigation functionality examples

	$nextRecord = 'occurrencequickentry.php?csmode='.$crowdSourceMode.'&occindex='.($occIndex+1).'&occid='.($occIndex+2).'&collid='.$collId;

	$firstRecord = 1;

	if($qryCnt !== false){
		$navStr = '<b>';
		if($occIndex > 0) $navStr .= '<a href="#" onclick="return navigateToRecordNew('.($firstRecord-1).', '.$firstRecord.', '.$collId.', '.$crowdSourceMode.')" title="'.(isset($LANG['FIRST_REC'])?$LANG['FIRST_REC']:'First Record').'">';
		$navStr .= '|&lt;';
		if($occIndex > 0) $navStr .= '</a>';
		$navStr .= '&nbsp;&nbsp;&nbsp;&nbsp;';
		$navStr .= '<a href="#" onclick="return navigateToRecordNew('.($occIndex-1).', '.$occIndex.', '.$collId.', '.$crowdSourceMode.')" title="'.(isset($LANG['PREV_REC']) ? $LANG['PREV_REC'] : 'Previous Record').'">';
		$navStr .= '&lt;&lt;';
		if($occIndex > 0) $navStr .= '</a>';
		$recIndex = ($occIndex<$qryCnt?($occIndex + 1):'*');
		$navStr .= '&nbsp;&nbsp;| '.$recIndex.' of '.$qryCnt.' |&nbsp;&nbsp;';
		if ($occIndex < $qryCnt-1) $navStr .= '<a href="#" onclick="return navigateToRecordNew('.($occIndex+1).', '.($occIndex+2).', '.$collId.', '.$crowdSourceMode.')" title="'.(isset($LANG['PREV_REC']) ? $LANG['PREV_REC'] : 'Previous Record').'">';
		$navStr .= '&gt;&gt;';
		if($occIndex<$qryCnt-1) $navStr .= '</a>';
		$navStr .= '&nbsp;&nbsp;&nbsp;&nbsp;';
		if($occIndex<$qryCnt-1) $navStr .= '<a href="#" onclick="return navigateToRecordNew('.($qryCnt-1).', '.$qryCnt.', '.$collId.', '.$crowdSourceMode.')" title="'.(isset($LANG['LAST_REC'])?$LANG['LAST_REC']:'Last Record').'">';
		$navStr .= '&gt;|';
		if($occIndex<$qryCnt-1) $navStr .= '</a> ';
		if(!$crowdSourceMode){
			$navStr .= '&nbsp;&nbsp;&nbsp;&nbsp;';
			$navStr .= '<a href="occurrenceeditor.php?gotomode=1&collid='.$collId.'" onclick="return verifyLeaveForm()" title="'.(isset($LANG['NEW_REC'])?$LANG['NEW_REC']:'New Record').'">&gt;*</a>';
		}
		$navStr .= '</b>';
	}

	// the string for jump
	if($qryCnt !== false){			
		$jumpStr .= '&nbsp;&nbsp file '.$recIndex.' of '.$qryCnt.' &nbsp;&nbsp;';
	}

	if(isset($_POST['jump']) && $_POST['jump'] !== '') {
		$jumpIndex = intval($_POST['jump']);
		if($jumpIndex >= 0 && $jumpIndex < $qryCnt) {
		  $occIndex = $jumpIndex;
		}
	  }

	//Images and other things needed for OCR
	$specImgArr = $occManager->getImageMap();
	if($specImgArr){
		$imgUrlPrefix = (isset($IMAGE_DOMAIN)?$IMAGE_DOMAIN:'');
		$imgCnt = 1;
		foreach($specImgArr as $imgId => $i2){
			$iUrl = $i2['url'];
			if($iUrl == 'empty' && $i2['origurl']) $iUrl = $i2['origurl'];
			if($imgUrlPrefix && substr($iUrl,0,4) != 'http') $iUrl = $imgUrlPrefix.$iUrl;
			$imgArr[$imgCnt]['imgid'] = $imgId;
			$imgArr[$imgCnt]['web'] = $iUrl;
			if($i2['origurl']){
				$lgUrl = $i2['origurl'];
				if($imgUrlPrefix && substr($lgUrl,0,4) != 'http') $lgUrl = $imgUrlPrefix.$lgUrl;
				$imgArr[$imgCnt]['lg'] = $lgUrl;
			}
			if(isset($i2['error'])) $imgArr[$imgCnt]['error'] = $i2['error'];
			$imgCnt++;
		}
		$fragArr = $occManager->getRawTextFragments();
	}

	$isLocked = false;
	if($occId) $isLocked = $occManager->getLock();

}
else{
	header('Location: ../../profile/index.php?refurl=../collections/editor/occurrenceeditor.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));
}
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
		<title><?php echo $DEFAULT_TITLE.' '.(isset($LANG['OCCEDITOR'])?$LANG['OCCEDITOR']:'Occurrence Editor'); ?></title>
		<link href="../../css/jquery-ui.css" type="text/css" rel="stylesheet" />
		<?php
		if($crowdSourceMode == 1){
			?>
			<link href="includes/config/occureditorcrowdsource.css?ver=2" type="text/css" rel="stylesheet" id="editorCssLink" />
			<?php
		}
		else{
			?>
			<link href="../../css/symb/occurrenceeditor.css?ver=6" type="text/css" rel="stylesheet" id="editorCssLink" />
			<?php
			if(isset($CSSARR)){
				foreach($CSSARR as $cssVal){
					echo '<link href="includes/config/'.$cssVal.'?ver=170601" type="text/css" rel="stylesheet" />';
				}
			}
			if(isset($JSARR)){
				foreach($JSARR as $jsVal){
					echo '<script src="includes/config/'.$jsVal.'?ver=170601" type="text/javascript"></script>';
				}
			}
		}
		include_once($SERVER_ROOT.'/includes/googleanalytics.php');
		?>
		<script src="../../js/jquery.js?ver=140310" type="text/javascript"></script>
		<script src="../../js/jquery-ui.js?ver=140310" type="text/javascript"></script>
		<script type="text/javascript">
			var collId = "<?php echo (isset($collMap['collid'])?$collMap['collid']:(is_numeric($collId)?$collId:0)); ?>";
			var csMode = "<?php echo $crowdSourceMode; ?>";
			var tabTarget = <?php echo (is_numeric($tabTarget)?$tabTarget:'0'); ?>;
			var imgArr = [];
			var imgLgArr = [];
			var localityAutoLookup = <?php echo (defined('LOCALITYAUTOLOOKUP') && !LOCALITYAUTOLOOKUP?'0':'1'); ?>;

			<?php
			if($imgArr){
				foreach($imgArr as $iCnt => $iArr){
					echo 'imgArr['.$iCnt.'] = "'.$iArr['web'].'";'."\n";
					if(isset($iArr['lg'])) echo 'imgLgArr['.$iCnt.'] = "'.$iArr['lg'].'";'."\n";
				}
			}
			?>

			$(document).ready(function() {
				<?php
				if(!defined('CATNUMDUPECHECK') || CATNUMDUPECHECK) echo '$("#catalognumber").on("change", function(e) { searchCatalogNumber(this.form,true); });'."\n";
				if(!defined('OTHERCATNUMDUPECHECK') || OTHERCATNUMDUPECHECK) echo '$("input[name=\'idvalue[]\']").on("change", function(e) { searchOtherCatalogNumbers(this); });'."\n";
				?>
			});

			function requestImage(){
				$.ajax({
					type: "POST",
					url: 'rpc/makeactionrequest.php',
					data: { <?php echo ' occid: '.$occManager->getOccId(); ?>, requesttype: 'Image' },
					success: function( response ) {
					$('div#imagerequestresult').html(response);
					}
				});
			}
		</script>
		<script src="../../js/symb/collections.coordinateValidation.js?ver=2" type="text/javascript"></script>
		<script src="../../js/symb/wktpolygontools.js?ver=2" type="text/javascript"></script>
		<script src="../../js/symb/collections.georef.js?ver=1" type="text/javascript"></script>
		<script src="../../js/symb/collections.editor.main.js?ver=14" type="text/javascript"></script>
		<script src="../../js/symb/collections.editor.tools.js?ver=4" type="text/javascript"></script>
		<script src="../../js/symb/collections.editor.imgtools.js?ver=2" type="text/javascript"></script>
		<script src="../../js/jquery.imagetool-1.7.js?ver=140310" type="text/javascript"></script>
		<script src="../../js/symb/collections.editor.query.js?ver=5" type="text/javascript"></script>
		<script>
			function navigateToRecordNew(crowdSourceMode, collId, batchId, imgId, imgIndex, occId, occIndex) {
				var url = 'occurrencequickentry.php?csmode=' + crowdSourceMode + '&collid=' + collId +'&batchid=' + batchId + '&imgid=' + imgId + '&imgindex=' + imgIndex + '&occid=' + occId + '&occindex=' + occIndex;
				window.location.href = url;
				event.preventDefault();
			}
		</script>
		<style type="text/css">
			fieldset{ padding:15px }
			fieldset > legend{ font-weight:bold; }
			.fieldGroupDiv{ clear:both; margin-bottom:2px; }
			.fieldDiv{ float:left; margin-right: 20px; }
			#identifierDiv img{ width:10px; margin-left: 5px; }
			.editimg{ width: 15px; }

			/* this is the style for the new form ------------------------------------*/
			*{
				box-sizing: border-box;
				font-family: sans-serif;
			}
			h2{
				background: linear-gradient(1000deg, rgb(218, 200, 255), #34ace0);;
				color: white;
			}
			.column{
				float: left;
				padding:10px;
			}
			.left{
				width: 65%;
			}
			.right{
				width: 35%;
			}
			.info{
				width: 100%;
				display: flex;
				align-items: center;
				padding: 8px;
			}
			.row:after {
				content: "";
				display: table;
				clear: both;
			}
			.nav-bar a{
				background-color: #00FFFF;
			color: black;
			border-style: solid;
			border-color: black;
			margin: 0;
			}
			.nav-bar label{
			background-color: #00FFFF;
			color: black;
			border-style: solid;
			border-color: black;
			}
			.function-bar{
				border:1px solid black; 
				display: flex;
				align-items: center;
			}
			button{
				color:black;
				background-color: #34ace0;
			}
			.btn label{
				color:black;
				background-color: #34ace0;
				border-style: solid;
				border-color: black;
			}
			.data{
				border-style: solid;
				border-color: black;
			}
			.login-info{
				border-style: solid;
				border-color: black;
			}
			#editdiv {
				margin-left:auto;
				margin-right:auto;
				width:960px;
			}
			.field-block {
				margin: 5px 0px;
				display: flex;
			}
			.field-label {
				text-align: left;
				width: 120px;
			}
			.title{
				backgroufnd-color:#86C5D8; 
				display: block;
				width: 100%;
			}
		</style>
	</head>
<body>
	<div id="innertext">
		<div id="titleDiv">
			<?php
			if($collMap) echo $collMap['collectionname'].' ('.$collMap['institutioncode'].($collMap['collectioncode']?':'.$collMap['collectioncode']:'').')';
			if($isEditor && $isEditor != 3){
				?>
				<div id="querySymbolDiv" style="margin:5px 5px 5px 5px;">
					<a href="#" title="<?php echo $LANG['SEARCH_FILTER']; ?>" onclick="toggleQueryForm();"><img src="../../images/find.png" style="width:18px;" /></a>
				</div>
				<?php
			}
			?>
		</div>
		<?php
		if($isEditor && ($occId || ($collId && $isEditor < 3))){
			if(!$occArr && !$goToMode) $displayQuery = 1;
			include 'includes/queryform.php';
			?>
			<!-- this is nav bar division Home>>collection management>>... -->
			<div id="navDiv">
				<?php
				if($navStr){
					?>
					<div style="float:right;">
						<?php echo $navStr; ?>
					</div>
					<?php
				}
				if(isset($collections_editor_occurrenceeditorCrumbs)){
					if($collections_editor_occurrenceeditorCrumbs){
						?>
						<div class="navpath">
							<a href='../../index.php'><?php echo (isset($LANG['HOME'])?$LANG['Home']:'Home'); ?></a> &gt;&gt;
							<?php
							echo $collections_editor_occurrenceeditorCrumbs;
							echo '<b>'.(isset($LANG['EDITOR'])?$LANG['EDITOR']:'Editor').'</b>';
							?>
						</div>
						<?php
					}
				}
				else{
					?>
					<div class='navpath'>
						<a href="../../index.php" onclick="return verifyLeaveForm()"><?php echo (isset($LANG['HOME'])?$LANG['HOME']:'Home'); ?></a> &gt;&gt;
						<?php
						if($crowdSourceMode){
							?>
							<a href="../specprocessor/crowdsource/index.php"><?php echo (isset($LANG['CENTRAL_CROWD'])?$LANG['CENTRAL_CROWD']:'Crowd Source Central'); ?></a> &gt;&gt;
							<?php
						}
						else{
							if($isGenObs){
								?>
								<a href="../../profile/viewprofile.php?tabindex=1" onclick="return verifyLeaveForm()"><?php echo (isset($LANG['PERS_MANAGEMENT'])?$LANG['PERS_MANAGEMENT']:'Personal Management'); ?></a> &gt;&gt;
								<?php
							}
							else{
								if($isEditor == 1 || $isEditor == 2){
									?>
									<a href="../misc/collprofiles.php?collid=<?php echo $collId; ?>&emode=1" onclick="return verifyLeaveForm()"><?php echo (isset($LANG['COL_MANAGEMENT'])?$LANG['COL_MANAGEMENT']:'Collection Management'); ?></a> &gt;&gt;
									<?php
								}
							}
						}
						if($occId) echo '<a href="../individual/index.php?occid='.$occManager->getOccId().'">'.(isset($LANG['PUBLIC_DISPLAY'])?$LANG['PUBLIC_DISPLAY']:'Public Display').'</a> &gt;&gt;';
						?>
						<b><?php if($isEditor == 3) echo $LANG['TAXONOMIC_EDITOR']; ?></b>
					</div>
					<?php
				}
				?>
			</div>


		<!-- body part of the new form start from here -->
		<!-- TODO: we leave the old form so that some of its funcitons can be reused in the new form  -->
		<?php
			if($statusStr){
				?>
				<div id="statusdiv" style="margin:5px 0px 5px 15px;">
					<b><?php echo (isset($LANG['ACTION_STATUS'])?$LANG['ACTION_STATUS']:'Action Status'); ?>: </b>
					<span style="color:<?php echo (stripos($statusStr,'ERROR')!==false?'red':'green'); ?>;"><?php echo $statusStr; ?></span>
					<?php
					if($action == 'Delete Occurrence'){
						?>
						<br/>
						<a href="#" style="margin:5px;" onclick="window.opener.location.href = window.opener.location.href;window.close();">
							<?php echo (isset($LANG['RETURN_TO_SEARCH'])?$LANG['RETURN_TO_SEARCH']:'Return to Search Page'); ?>
						</a>
						<?php
					}
					?>
				</div>
				<?php
			}
		}?>
		<div id="editdiv">
			<div class = "row">
				<section>
					<div class="btn function-bar" name="jumpform">
						<form method="post">
							<button type="submit" name="toggle-button" value="<?php echo isset($_POST['toggle-button']) && $_POST['toggle-button'] === 'Minimal' ? 'Detailed' : 'Minimal'; ?>">
								<?php echo isset($_POST['toggle-button']) ? $_POST['toggle-button'] : 'Detailed'; ?>
							</button>
							<button type="button" onclick="jumpToPage(<?php echo($collId) ?>, <?php echo($crowdSourceMode) ?>)">Jump to:</button>
							<input type="number" id="pageNumber" size="3" />
						</form>
						<div id="jumpDiv">
							<?php
							if($jumpStr){
							?>
								<div>
									<?php if(array_key_exists('recordenteredby',$occArr)){
												echo ($occArr['recordenteredby']?$occArr['recordenteredby']:$LANG['NOT_RECORDED']);
											}
											if(isset($occArr['dateentered']) && $occArr['dateentered']) echo ' ['.$occArr['dateentered'].']'; 
											?>
									<?php echo $jumpStr; ?>
								</div>
							<?php
							}
							if(isset($collections_editor_occurrenceeditorCrumbs)){
								if($collections_editor_occurrenceeditorCrumbs){
							?>
									<div class="navpath">
										<a href='../../index.php'><?php echo (isset($LANG['HOME'])?$LANG['Home']:'Home'); ?></a> &gt;&gt;
										<?php
										echo $collections_editor_occurrenceeditorCrumbs;
										echo '<b>'.(isset($LANG['EDITOR'])?$LANG['EDITOR']:'Editor').'</b>';
										?>
									</div>
									<?php
								}
							} ?>
						</div>
						<!-- <button id="detail-btn" onclick="toggleDetail()" value="detailed">Detailed</button> -->
					</div>
				</section> 
				<form id="fullform" name="fullform" action="occurrenceeditor.php" method="post" onsubmit="return verifyFullForm(this);">
					<!-- navigation bar -->
					<!-- left part of the form starts -->
					<div class = "column left login-info" style = "background-color: #F2F2F2; ">
						<div class="field-block title">
							<h2>Transcribe into Fields</h2>
							<h1><?php print_r($test) ?></h1>
						</div>
						<div class="field-block">
							<span class="field-label"><?php echo (isset($LANG['BARCODE']) ? $LANG['BARCODE'] : 'Barcode'); ?></span>
							<span class="field-elem">
								<!-- this $occArr array connects to the old form's database -->
								<?php if(array_key_exists('barcode',$occArr)) { ?>
									<input type="text" size = '50' id="barcode" name="barcode" value="<?php // echo $occArr['barcode'] ?>" onchange="fieldChanged('barcode');" <?php if($isEditor > 2) echo 'disabled'; ?> autocomplete="off" />
								<?php 
								} else { 
								?>
									NOT FOUND
								<?php 
								} 
								?>
							</span>
						</div>
						<?php if(!isset($_POST['toggle-button']) || (isset($_POST['toggle-button']) && $_POST['toggle-button'] != 'Minimal')): ?>
							<div class="field-block">
								<span class="field-label"><?php echo (isset($LANG['ACCES_NUM']) ? $LANG['ACCES_NUM'] : 'Accession Num.'); ?></span>
								<span class="field-elem">
									<?php if(array_key_exists('accesNum',$occArr)) { ?>
										<input type="text" size = '50' name="accesNum" value="<?php echo $occArr["accesNum"]; ?>" onchange="fieldChanged('accesNum');" />
									<?php 
									} else { 
									?>
										NOT FOUND
									<?php 
									} 
									?>
								</span>
							</div>
						<?php endif; ?>
						<div class="field-block">
							<span class="field-label"><?php echo (isset($LANG['FILED_UNDER']) ? $LANG['FILED_UNDER'] : 'Filed Under'); ?></span>
							<span class="field-elem">
								<?php if(array_key_exists('filedUnder',$occArr)) { 
									$filedUnderValue = isset($filedUnderDrop[$occArr["filedUnder"]]) ? $filedUnderDrop[$occArr["filedUnder"]] : null;
								?>	
									<input type="text" size="50" name="filedUnderDisplay" id="filedUnderDisplay" value="<?php echo $filedUnderValue; ?>" onkeyup="filterDropdown('filedUnderDisplay', 'filedUnder', <?php echo htmlspecialchars(json_encode($filedUnderDrop), ENT_QUOTES, 'UTF-8'); ?>)" />
									<select name="filedUnder" id="filedUnder" style="display: none;" onchange="selectOption(this, 'filedUnderDisplay',<?php echo htmlspecialchars(json_encode($filedUnderDrop), ENT_QUOTES, 'UTF-8'); ?>);"></select>
								<?php 
								} else { 
								?>
									NOT FOUND
								<?php 
								} 
								?>
							</span>
						</div>
						<div class="field-block">
							<span class="field-label"><?php echo (isset($LANG['CURR_NAME']) ? $LANG['CURR_NAME'] : 'Current Name'); ?></span>
							<span class="field-elem">
								<?php if(array_key_exists('currName',$occArr)) { ?>
									<input type="text" size = '50' name="currName" value="<?php echo $occArr["currName"]; ?>" onchange="fieldChanged('currName');" />
								<?php 
								} else { 
								?>
									NOT FOUND
								<?php 
								} 
								?>
							</span>
						</div>
						<?php if(!isset($_POST['toggle-button']) || (isset($_POST['toggle-button']) && $_POST['toggle-button'] != 'Minimal')): ?>
							<div class="field-block">
								<span class="field-label"><?php echo (isset($LANG['IDQUALIFIER']) ? $LANG['IDQUALIFIER'] : 'ID Qualifier'); ?></span>
								<span class="field-elem">
									<!-- the drop list needs to be update -->
									<?php
										if(array_key_exists('idQualifier',$occArr)) {
									?>
										<select name="idQualifier" onchange="fieldChanged('idQualifier');">
											<option value=""><?php echo $LANG['IDQUALIFIER']; ?>Select Your ID Qualifier</option>
											<option value="">---------------------------------------</option>
											<?php
											$idqArr = array('s. str.', '?', 'not', 'cf.', 's. lat.', 'aff.');
											foreach ($idqArr as $k) {
												$selected = ($k == $occArr['idQualifier']) ? 'selected' : '';
												echo '<option value="' . $k . '" ' . $selected . '>' . $k . '</option>' . "\n";
											}
											?>
										</select>
									<?php
									} else {
									?>
										NOT FOUND
									<?php
									}
									?>
								</span>
							</div>
							<div class="field-block">
								<span class="field-label"><?php echo (defined('IDENTIFIEDBYLABEL')?IDENTIFIEDBYLABEL:'Identified By'); ?></span>
								<span class="field-elem">
									<input size = '50' type="text"  maxlength="255" name="identifiedby" value="<?php echo array_key_exists('identifiedby',$occArr)?$occArr['identifiedby']:''; ?>" onchange="fieldChanged('identifiedby');" />
									<a href="#" onclick="return dwcDoc('identifiedBy')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a>
								</span>
							</div>
							<div class="field-block">
								<span class="field-label"><?php echo (defined('DATEIDENTIFIEDLABEL')?DATEIDENTIFIEDLABEL:'Date Identified'); ?></span>
								<span class="field-elem">
									<input size = '50' type="text" name="dateidentified" maxlength="45" value="<?php echo array_key_exists('dateidentified',$occArr)?$occArr['dateidentified']:''; ?>" onchange="fieldChanged('dateidentified');" />
									<a href="#" onclick="return dwcDoc('dateIdentified')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a>
								</span>
							</div>
							<!-- There is a tab below is for determiation -->
							<div class="field-block">
								<span class="field-label"><?php echo (isset($LANG['DET_TEXT']) ? $LANG['DET_TEXT'] : 'Det. Text'); ?></span>
								<span class="field-elem">
									<?php if(array_key_exists('detText',$occArr)) { ?>
										<input size = '50' type="text" name="detText" value="<?php echo $occArr["detText"]; ?>" onchange="fieldChanged('detText');" />
									<?php 
									} else { 
									?>
										NOT FOUND
									<?php 
									} 
									?>
								</span>
							</div>
							<div class="field-block">
								<span class="field-label"><?php echo (isset($LANG['PROVENANCE']) ? $LANG['PROVENANCE'] : 'Provenance'); ?></span>
								<span class="field-elem">
									<?php if(array_key_exists('provenance',$occArr)) { ?>
										<input type="text" size = '50' name="provenance" value="<?php echo $occArr["provenance"]; ?>" onchange="fieldChanged('provenance');" />
									<?php 
									} else { 
									?>
										NOT FOUND
									<?php 
									} 
									?>
								</span>
							</div>
						<?php endif; ?>
						<div class="field-block">
							<span class="field-label"><?php echo (defined('RECORDEDBYLABEL')?RECORDEDBYLABEL:'Collectors'); ?></span>
							<span class="field-elem">
								<input size = '50' type="text" name="recordedby" maxlength="255" value="<?php echo array_key_exists('recordedby',$occArr)?$occArr['recordedby']:''; ?>" onchange="fieldChanged('recordedby');" />
								<a href="#" onclick="return dwcDoc('recordedBy')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a>
							</span>
						</div>
						<!-- I put associated collectors here -->
						<div class="field-block">
							<span class="field-label"><?php echo (defined('ASSOCIATEDCOLLECTORSLABEL')?ASSOCIATEDCOLLECTORSLABEL:'Et al.'); ?></span>
							<span class="field-elem">
								<input size = '50' type="text" name="associatedcollectors" maxlength="255" value="<?php echo array_key_exists('associatedcollectors',$occArr)?$occArr['associatedcollectors']:''; ?>" onchange="fieldChanged('associatedcollectors');" />
								<a href="#" onclick="return dwcDoc('associatedCollectors')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a>
							</span>
						</div>
						<div class="field-block">
							<span class="field-label"><?php echo (defined('RECORDNUMBERLABEL')?RECORDNUMBERLABEL:'Collector Number'); ?></span>
							<span class="field-elem">
								<input size = '50' type="text" name="recordnumber" maxlength="45" value="<?php echo array_key_exists('recordnumber',$occArr)?$occArr['recordnumber']:''; ?>" onchange="recordNumberChanged(this);" />
								<a href="#" onclick="return dwcDoc('recordNumber')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a>
							</span>
						</div> 
						<div class="field-block">
							<span class="field-label"><?php echo (defined('EVENTDATELABEL')?EVENTDATELABEL:'Date Collected'); ?></span>
							<span class="field-elem">
								<input size = '50' type="text" name="eventdate" value="<?php echo array_key_exists('eventdate',$occArr)?$occArr['eventdate']:''; ?>" onchange="eventDateChanged(this);" />
								<a href="#" onclick="return dwcDoc('eventDate')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a>
							</span>
						</div>
						<div class="field-block">
							<span class="field-label"><?php echo (defined('VERBATIMEVENTDATELABEL')?VERBATIMEVENTDATELABEL:'Verbatim Date'); ?></span>
							<span class="field-elem">
								<input size = '50' type="text" name="verbatimeventdate" maxlength="255" value="<?php echo array_key_exists('verbatimeventdate',$occArr)?$occArr['verbatimeventdate']:''; ?>" onchange="verbatimEventDateChanged(this)" />
								<a href="#" onclick="return dwcDoc('verbatimEventDate')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a>
							</span>
						</div>
						<?php if(!isset($_POST['toggle-button']) || (isset($_POST['toggle-button']) && $_POST['toggle-button'] != 'Minimal')): ?>
							<div class="field-block">
								<span class="field-label"><?php echo (isset($LANG['CONTAINER']) ? $LANG['CONTAINER'] : 'Container'); ?></span>
								<span class="field-elem">
									<?php if(array_key_exists('container',$occArr)) { ?>
										<input size = '50' type="text" name="container" value="<?php echo $occArr["container"]; ?>" onchange="fieldChanged('container');" />
									<?php 
									} else { 
									?>
										NOT FOUND
									<?php 
									} 
									?>
								</span>
							</div>
							<div class="field-block">
								<span class="field-label"><?php echo (isset($LANG['COLL_TRIP']) ? $LANG['COLL_TRIP'] : 'Collecting Trip'); ?></span>
								<span class="field-elem">
									<?php if(array_key_exists('collTrip',$occArr)) { ?>
										<input size = '50' type="text" name="collTrip" value="<?php echo $occArr["collTrip"]; ?>" onchange="fieldChanged('collTrip');" />
									<?php 
									} else { 
									?>
										NOT FOUND
									<?php 
									} 
									?>
								</span>
							</div>
						<?php endif; ?>
						<div class="field-block">
							<span class="field-label"><?php echo (isset($LANG['GEO_WITHIN']) ? $LANG['GEO_WITHIN'] : 'Geography Within'); ?></span>
							<span class="field-elem">
								<?php if(array_key_exists('geoWithin',$occArr)) { ?>
									<input size = '50' type="text" name="geoWithin" value="<?php echo $occArr["geoWithin"]; ?>" onchange="fieldChanged('geoWithin');" />
								<?php 
								} else { 
								?>
									NOT FOUND
								<?php 
								} 
								?>
							</span>
						</div>
						<div class="field-block">
							<span class="field-label"><?php echo (isset($LANG['HIGH_GEO']) ? $LANG['HIGH_GEO'] : 'Higher Geography'); ?></span>
							<span class="field-elem">
								<?php if(array_key_exists('highGeo',$occArr)) { ?>
									<input type="text" size = '50' name="highGeo" value="<?php echo $occArr["highGeo"]; ?>" onchange="fieldChanged('highGeo');" />
								<?php 
								} else { 
								?>
									NOT FOUND
								<?php 
								} 
								?>
							</span>
						</div>
						<?php if(!isset($_POST['toggle-button']) || (isset($_POST['toggle-button']) && $_POST['toggle-button'] != 'Minimal')): ?>
							<div class="field-block">
								<span class="field-label"><?php echo (defined('LOCALITYLABEL')?LOCALITYLABEL:'Verbatim Locality'); ?></span>
								<span class="field-elem">
									<input id="fflocality" type="text" size = '50'  onchange="fieldChanged('locality');" name="locality" value="<?php echo array_key_exists('locality',$occArr)?$occArr['locality']:''; ?>" />
									<a href="#" onclick="return dwcDoc('locality')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" style="width:9px" /></a>
								</span>
							</div>
							<div class="field-block">
								<span class="field-label"><?php echo (defined('HABITATLABEL')?HABITATLABEL:'Habitat'); ?></span>
								<span class="field-elem">
									<input size = '50' type="text" name="habitat" value="<?php echo array_key_exists('habitat',$occArr)?$occArr['habitat']:''; ?>" onchange="fieldChanged('habitat');" />
									<a href="#" onclick="return dwcDoc('habitat')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a>
								</span>
							</div>
							<div class="field-block">
								<span class="field-label"><?php echo (isset($LANG['FREQUENCY']) ? $LANG['FREQUENCY'] : 'Frequency'); ?></span>
								<span class="field-elem">
									<?php if(array_key_exists('frequency',$occArr)) { ?>
										<input size = '50' type="text" name="frequency" value="<?php echo $occArr["frequency"]; ?>" onchange="fieldChanged('frequency');" />
									<?php 
									} else { 
									?>
										NOT FOUND
									<?php 
									} 
									?>
								</span>
							</div>
							<div class="field-block">
								<span class="field-label"><?php echo (defined('VERBATIMATTRIBUTESLABEL')?VERBATIMATTRIBUTESLABEL:'Description'); ?></span>
								<span class="field-elem">
									<input size = '50' type="text" name="verbatimattributes" value="<?php echo array_key_exists('verbatimattributes',$occArr)?$occArr['verbatimattributes']:''; ?>" onchange="fieldChanged('verbatimattributes');" />
									<a href="#" onclick="return dwcDoc('verbatimAttributes')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a>
								</span>
							</div>
							<div class="field-block">
								<span class="field-label"><?php echo (defined('OCCURRENCEREMARKSLABEL')?OCCURRENCEREMARKSLABEL:'Remarks'); ?></span>
								<span class="field-elem">
									<input size = '50' type="text" name="occurrenceremarks" value="<?php echo array_key_exists('occurrenceremarks',$occArr)?$occArr['occurrenceremarks']:''; ?>" onchange="fieldChanged('occurrenceremarks');" title="<?php echo $LANG['OCC_REMARKS']; ?>" />
									<a href="#" onclick="return dwcDoc('occurrenceRemarks')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a>
								</span>
							</div>
						<?php endif; ?>
						<!-- I use the label project here from the old form, not sure if it's the same thing -->
						<div class="field-block">
							<span class="field-label"><?php echo (defined('LABELPROJECTLABEL')?LABELPROJECTLABEL:'Project'); ?></span>
							<span class="field-elem">
								<input size = '50' type="text" name="labelproject" maxlength="45" value="<?php echo array_key_exists('labelproject',$occArr)?$occArr['labelproject']:''; ?>" onchange="fieldChanged('labelproject');" />
							</span>
						</div>
					</div>  <!-- column left ends -->
					<!-- image part -->
					<!-- TODO: implement image code from old form, see collections/editor/includes/imagetab.php and check the embedded code to get started -->
					<div class = "column right" style = "border: 1; background-color:#F2F2F3;">
						<div class="field-block title">
							<h2>Click to Zoom in Other Window</h2>
						</div>
						<div class="login-info" style = "backgroufnd-color:#86C5D8; text-align: center;">
							<img id="activeimg-<?php echo $imgCnt; ?>" src="<?php echo $iUrl; ?>" style="width:300px" />
						</div>
						<div class="login-info">
							<!-- TODO: need to figure out how to deal with this input. It supposes to be generated automatically based on the form -->
							<div class="field-block">
								<span class="field-label"><?php echo (isset($LANG['RECCORECTED']) ? $LANG['RECCORECTED'] : 'Record Created'); ?>:</span>
								<span class="field-elem">
									<?php echo ($accesNum ? $occArr["recCreated"] : 'New Record'); ?>
								</span>
							</div>
							<div class="field-block">
								<span class="field-label"><?php echo (isset($LANG['PREPMETHOD']) ? $LANG['PREPMETHOD'] : 'Prep Method'); ?></span>
								<span class="field-elem">
									<?php if(array_key_exists('prepMethod',$occArr)) { ?>
										<input size = '25' type="text" name="prepMethod" value="<?php echo $occArr["prepMethod"]; ?>" onchange="fieldChanged('prepMethod');"  />
									<?php 
									} else { 
									?>
										NOT FOUND
									<?php 
									} 
									?>
								</span>
							</div>
							<div class="field-block">
								<span class="field-label"><?php echo (isset($LANG['FORMAT']) ? $LANG['FORMAT'] : 'Format'); ?></span>
								<span class="field-elem">
									<?php if(array_key_exists('format',$occArr)) { ?>
										<input size = '25' type="text" name="format" value="<?php echo $occArr["format"]; ?>" onchange="fieldChanged('format');" />
									<?php 
									} else { 
									?>
										NOT FOUND
									<?php 
									} 
									?>
								</span>
							</div>
						</div>
						<?php if(!isset($_POST['toggle-button']) || (isset($_POST['toggle-button']) && $_POST['toggle-button'] != 'Minimal')): ?>
							<div class="login-info">
								<div class="field-block">
									<span class="field-label"><?php echo (defined('VERBATIMELEVATIONLABEL')?VERBATIMELEVATIONLABEL:'Verb. Elev.'); ?></span>
									<span class="field-elem">
										<input size = '25' type="text" name="verbatimelevation" maxlength="255" value="<?php echo array_key_exists('verbatimelevation',$occArr)?$occArr['verbatimelevation']:''; ?>" onchange="verbatimElevationChanged(this.form);" />
										<a href="#" onclick="return dwcDoc('verbatimElevation')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a>
									</span>
								</div>
								<div class="field-block">
									<span class="field-label"><?php echo (isset($LANG['VERBLAT']) ? $LANG['VERBLAT'] : 'Verb. Lat.'); ?></span>
									<span class="field-elem">
										<?php if(array_key_exists('verbLat',$occArr)) { ?>
											<!-- TODO: need to update the onchange function later to make sure the input format is correct -->
											<input size = '25' type="text" name="verbLat" value="<?php echo $occArr["verbLat"]; ?>" onchange="decimalLatitudeChanged(this.form)" />
										<?php 
										} else { 
										?>
											NOT FOUND
										<?php 
										} 
										?>
									</span>
								</div>
								<div class="field-block">
									<span class="field-label"><?php echo (isset($LANG['VERBLONG']) ? $LANG['VERBLONG'] : 'Verb. Long.'); ?></span>
									<span class="field-elem">
										<?php if(array_key_exists('verbLong',$occArr)) { ?>
											<!-- TODO: need to update the onchange function later to make sure the input format is correct -->
											<input size = '25' type="text" name="verbLong" value="<?php echo $occArr["verbLong"]; ?>" onchange="decimalLatitudeChanged(this.form)" />
										<?php 
										} else { 
										?>
											NOT FOUND
										<?php 
										} 
										?>
									</span>
								</div>
								<div class="field-block">
									<span class="field-label"><?php echo (defined('DECIMALLATITUDELABEL')?DECIMALLATITUDELABEL:'Dec. Lat.'); ?></span>
									<span class="field-elem">
										<?php
										$latValue = "";
										if(array_key_exists("decimallatitude",$occArr) && $occArr["decimallatitude"] != "") {
											$latValue = $occArr["decimallatitude"];
										}
										?>
										<input size = '25' type="text" name="decimallatitude" maxlength="15" value="<?php echo $latValue; ?>" onchange="decimalLatitudeChanged(this.form)" />
									</span>
								</div>
								<div class="field-block">
									<span class="field-label"><?php echo (defined('DECIMALLONGITUDELABEL')?DECIMALLONGITUDELABEL:'Dec. Long.'); ?></span>
									<span class="field-elem">
										<?php
										$longValue = "";
										if(array_key_exists("decimallongitude",$occArr) && $occArr["decimallongitude"] != "") {
											$longValue = $occArr["decimallongitude"];
										}
										?>
										<input size = '25' type="text" name="decimallongitude" maxlength="15" value="<?php echo $longValue; ?>" onchange="decimalLongitudeChanged(this.form);" />
									</span>
								</div>
								<div class="field-block">
									<span class="field-label"><?php echo (isset($LANG['METHOD']) ? $LANG['METHOD'] : 'Method'); ?></span>
									<span class="field-elem">
										<?php if(array_key_exists('method',$occArr)) { ?>
											<input size = '25' type="text" name="method" value="<?php echo $occArr["method"]; ?>" onchange="fieldChanged('method');" />
											<?php 
										} else { 
										?>
											NOT FOUND
										<?php 
										} 
										?>
									</span>
								</div>
								<div class="field-block">
									<span class="field-label"><?php echo (defined('COORDINATEUNCERTAINITYINMETERSLABEL')?COORDINATEUNCERTAINITYINMETERSLABEL:'Uncertainty'); ?></span>
									<span class="field-elem">
										<input size = '25' type="text" name="coordinateuncertaintyinmeters" maxlength="10" value="<?php echo array_key_exists('coordinateuncertaintyinmeters',$occArr)?$occArr['coordinateuncertaintyinmeters']:''; ?>" onchange="coordinateUncertaintyInMetersChanged(this.form);" title="<?php echo (isset($LANG['UNCERTAINTY_METERS'])?$LANG['UNCERTAINTY_METERS']:'Uncertainty in Meters'); ?>" />
										<a href="#" onclick="return dwcDoc('coordinateUncertaintyInMeters')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a>
									</span>
								</div>
							</div>
						<?php endif; ?>
					</div>  <!-- image ends -->
					<table class = "column left" style = "border:1px solid black;">									
						<!-- TODO: implement button functionality, examine relevant button divs in old code below ie saveedits button to get started -->
						<tr>
							<td>
								<div id="bottomSubmitDiv">
									<input type="hidden" name="occid" value="<?php echo $occManager->getOccId(); ?>" />
									<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
									<input type="hidden" name="observeruid" value="<?php echo $SYMB_UID; ?>" />
									<input type="hidden" name="csmode" value="<?php echo $crowdSourceMode; ?>" />
									<input type="hidden" name="linkdupe" value="" />
									<?php
									if($occId){
										?>
										<div style="float:left">
											<div id="editButtonDiv">
												<button type="submit" id="saveEditsButton" name="submitaction" value="saveOccurEdits" onclick="return verifyFullFormEdits(this.form)" disabled><?php echo $LANG['SAVE_EDITS']; ?></button>
												<button 
													type="submit" 
													value="Previous" 
													onclick="<?php if($occIndex>0) { ?>submitQueryForm('back');<?php } ?> return false;">
													Previous
												</button>
												<a href="../editor/transcribe.php?collid=" + <?php echo $collId; ?> >
													<button type="button" value="Done" onclick="navigateToURL(<?php echo $collId; ?>)">Done</button>
												</a>
												<button 
													type="submit" 
													value="Next" 
													onclick="<?php if($occIndex<$qryCnt-1) { ?>submitQueryForm('forward');<?php } ?> return false;">
													Next
												</button>
												<input type="hidden" name="occindex" value="<?php echo is_numeric($occIndex)?$occIndex:''; ?>" />
												<input type="hidden" name="editedfields" value="" />
											</div>
										</div>
											<?php
									}
									else{
										?>
										<div id="addButtonDiv">
											<input name="recordenteredby" type="hidden" value="<?php echo $PARAMS_ARR['un']; ?>" />
											<button name="submitaction" type="submit" value="addOccurRecord" style="width:150px;font-weight:bold;margin:10px;"><?php echo $LANG['ADD_RECORD']; ?></button>
											<input name="qrycnt" type="hidden" value="<?php echo $qryCnt?$qryCnt:''; ?>" />
											<div style="margin-left:15px;font-weight:bold;">
												<?php echo $LANG['FOLLOW_UP']; ?>:
											</div>
											<div style="margin-left:20px;">
												<input name="gotomode" type="radio" value="1" <?php echo ($goToMode==1?'CHECKED':''); ?> /> <?php echo $LANG['GO_TO_NEW']; ?><br/>
												<input name="gotomode" type="radio" value="2" <?php echo ($goToMode==2?'CHECKED':''); ?> /> <?php echo $LANG['GO_NEW_CARRYOVER']; ?><br/>
												<input name="gotomode" type="radio" value="0" <?php echo (!$goToMode?'CHECKED':''); ?> /> <?php echo $LANG['REMAIN_ON_PAGE']; ?>
											</div>
										</div>
										<?php
									}
									?>
								</div>
							</td>
						</tr>
					</table>
				</form>
				<section>
					<div class="info function-bar">
						<?php echo "info needed   " ?>
						<div style="float:left;" title="<?php echo $LANG['PRIMARY_KEY']; ?>">
							<?php if($occId) echo 'Key: '.$occManager->getOccId(); ?>
						</div>
					</div>
					<div class="info function-bar">
						<?php
							if(array_key_exists('recordenteredby',$occArr)){
								echo $LANG['ENTERED_BY'].': '.($occArr['recordenteredby']?$occArr['recordenteredby']:$LANG['NOT_RECORDED']);
							}
							if(isset($occArr['dateentered']) && $occArr['dateentered']) echo ' ['.$occArr['dateentered'].']';
						?>
					</div>
					<div class="info">
						<?php echo "info needed" ?>
					</div>
				</section> 
			</div>
		</div>			
	</div>
</body>
</html>