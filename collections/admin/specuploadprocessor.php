<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SpecUploadDirect.php');
include_once($SERVER_ROOT.'/classes/SpecUploadFile.php');
include_once($SERVER_ROOT.'/classes/SpecUploadDwca.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/collections/admin/specupload.' . $LANG_TAG . '.php'))
	include_once($SERVER_ROOT . '/content/lang/collections/admin/specupload.' . $LANG_TAG . '.php');
else include_once($SERVER_ROOT.'/content/lang/collections/admin/specupload.en.php');
header('Content-Type: text/html; charset=' . $CHARSET);
ini_set('max_execution_time', 3600);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/admin/specupload.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = !empty($_REQUEST['collid']) ? filter_var($_REQUEST['collid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$uploadType = !empty($_REQUEST['uploadtype']) ? $_REQUEST['uploadtype'] : '';		//Sanitized after uspid parsing
$uspid = !empty($_REQUEST['uspid']) ? $_REQUEST['uspid'] : '';					//Sanitized after uspid parsing
$ulPath = !empty($_REQUEST['ulpath']) ? $_REQUEST['ulpath'] : '';
$importIdent = array_key_exists('importident', $_REQUEST) ? true : false;
$importImage = array_key_exists('importimage', $_REQUEST) ? true : false;
$observerUid = !empty($_POST['observeruid']) ? filter_var($_POST['observeruid'], FILTER_SANITIZE_NUMBER_INT) : '';
$updateAction = !empty($_REQUEST['updateaction']) ? $_REQUEST['updateaction'] : '';
$matchCatNum = array_key_exists('matchcatnum', $_REQUEST) ? true : false;
$matchOtherCatNum = !empty($_REQUEST['matchothercatnum']) ? true : false;
$versionData = !empty($_REQUEST['versiondata']) ? 1 : 0;
$verifyImages = !empty($_REQUEST['verifyimages']) ? 1 : 0;
$processingStatus = !empty($_REQUEST['processingstatus']) ? $_REQUEST['processingstatus']: '';
$finalTransfer = !empty($_REQUEST['finaltransfer']) ? filter_var($_REQUEST['finaltransfer'], FILTER_SANITIZE_NUMBER_INT) : 0;
$sourceIndex = !empty($_REQUEST['sourceindex']) ? filter_var($_REQUEST['sourceindex'], FILTER_SANITIZE_NUMBER_INT) : 0;
$publicationGuid = array_key_exists('publicationGuid',$_POST) ? $_POST['publicationGuid'] : '';
$action = !empty($_REQUEST['action']) ? $_REQUEST['action'] : '';

if(strpos($uspid,'-')){
	$tok = explode('-',$uspid);
	$uspid = $tok[0];
	$uploadType = $tok[1];
}

//Sanitation
$uspid = filter_var($uspid, FILTER_SANITIZE_NUMBER_INT);
$uploadType = filter_var($uploadType, FILTER_SANITIZE_NUMBER_INT);
if(!preg_match('/^[a-zA-Z0-9\s_-]+$/',$processingStatus)) $processingStatus = '';

$DIRECTUPLOAD = 1; $FILEUPLOAD = 3; $STOREDPROCEDURE = 4; $SCRIPTUPLOAD = 5; $DWCAUPLOAD = 6; $SKELETAL = 7; $IPTUPLOAD = 8; $NFNUPLOAD = 9; $SYMBIOTA = 13;

$duManager = new SpecUploadBase();
if($uploadType == $DIRECTUPLOAD){
	$duManager = new SpecUploadDirect();
}
elseif($uploadType == $FILEUPLOAD || $uploadType == $NFNUPLOAD){
	$duManager = new SpecUploadFile();
	$duManager->setUploadFileName($ulPath);
}
elseif($uploadType == $SKELETAL){
	$duManager = new SpecUploadFile();
	$duManager->setUploadFileName($ulPath);
	$matchCatNum = true;
}
elseif($uploadType == $DWCAUPLOAD || $uploadType == $IPTUPLOAD || $uploadType == $SYMBIOTA){
	$duManager = new SpecUploadDwca();
	$duManager->setTargetPath($ulPath);
	$duManager->setIncludeIdentificationHistory($importIdent);
	$duManager->setIncludeImages($importImage);
	for($i=0;$i<3;$i++){
		if(isset($_POST['filter'.$i])){
			$duManager->addFilterCondition($_POST['filter'.$i], $_POST['condition'.$i], $_POST['value'.$i]);
		}
	}
	$duManager->setSourcePortalIndex($sourceIndex);
	$duManager->setPublicationGuid($publicationGuid);
}

$duManager->setCollId($collid);
$duManager->setUspid($uspid);
$duManager->setUploadType($uploadType);
$duManager->setObserverUid($observerUid);
$duManager->setUpdateAction($updateAction);
$duManager->setMatchCatalogNumber($matchCatNum);
$duManager->setMatchOtherCatalogNumbers($matchOtherCatNum);
$duManager->setVersionDataEdits($versionData);
$duManager->setVerifyImageUrls($verifyImages);
$duManager->setProcessingStatus($processingStatus);

$isEditor = 0;
if($IS_ADMIN) $isEditor = 1;
elseif(array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin'])) $isEditor = 1;
if($isEditor && $collid){
	$duManager->readUploadParameters();
	$duManager->setFieldMaps($_POST);
	$duManager->loadFieldMap();
}
?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title><?= $DEFAULT_TITLE . ' ' . $LANG['SPEC_UPLOAD'] ?></title>
	<link href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script src="../../js/symb/shared.js" type="text/javascript"></script>
</head>
<body>
	<?php
$displayLeftMenu = (isset($collections_admin_specuploadMenu) ? $collections_admin_specuploadMenu:false);
include($SERVER_ROOT.'/includes/header.php');
?>
<div class="navpath">
	<a href="../../index.php"><?= $LANG['HOME'] ?></a> &gt;&gt;
	<a href="../misc/collprofiles.php?collid=<?= $collid ?>&emode=1"><?= $LANG['COL_MGMNT'] ?></a> &gt;&gt;
	<a href="specuploadmanagement.php?collid=<?= $collid ?>"><?= $LANG['LIST_UPLOAD'] ?></a> &gt;&gt;
	<b><?= $LANG['SPEC_UPLOAD'] ?></b>
</div>
<div role="main" id="innertext">
	<h1 class="page-heading"><?= $LANG['UP_MODULE']; ?></h1>
	<?php
	if($isEditor && $collid){
		//Grab collection name and last upload date and display for all
		echo '<div style="font-weight:bold;font-size:130%;">'.$duManager->getCollInfo('name').'</div>';
		echo '<div style="margin:0px 0px 15px 15px;"><b>Last Upload Date:</b> '.($duManager->getCollInfo('uploaddate') ? $duManager->getCollInfo('uploaddate') : $LANG['NOT_REC']).'</div>';
		if(($action == 'Start Upload') || (!$action && ($uploadType == $STOREDPROCEDURE || $uploadType == $SCRIPTUPLOAD))){
			//Upload records
			echo '<div style="font-weight:bold;font-size:120%">' . $LANG['UP_STATUS'] . ':</div>';
			echo '<ul style="margin:10px;font-weight:bold;">';
			$duManager->uploadData($finalTransfer);
			echo '</ul>';
			if(!$finalTransfer){
				?>
				<fieldset style="margin:15px;">
					<legend style="<?php if($uploadType == $SKELETAL) echo 'background-color:lightgreen'; ?>"><b><?= $LANG['PENDING_REPORT'] ?></b></legend>
					<div style="margin:5px;">
						<?php
						$reportArr = $duManager->getTransferReport();
						echo '<div>' . $LANG['OCCS_TRANSFERING'] . ': ' . $reportArr['occur'];
						if($reportArr['occur']){
							echo ' <a href="uploadreviewer.php?collid=' . $collid . '" target="_blank" title="' . $LANG['PREVIEW'] . '"><img src="../../images/list.png" style="width:12px;" /></a>';
							echo ' <a href="uploadreviewer.php?action=export&collid=' . $collid . '" target="_self" title="' . $LANG['DOWNLOAD_RECS'] . '"><img src="../../images/dl.png" style="width:12px;" /></a>';
						}
						echo '</div>';
						echo '<div style="margin-left:15px;">';
						echo '<div>'.$LANG['RECORDS_UPDATED'].': ';
						echo $reportArr['update'];
						if($reportArr['update']){
							$searchVar = 'occid:NOT_NULL';
							if(isset($reportArr['sync']) && $reportArr['sync']) $searchVar = 'syncnew';
							echo ' <a href="uploadreviewer.php?collid=' . $collid . '&searchvar=' . $searchVar . '" target="_blank" title="' . $LANG['PREVIEW'] . '"><img src="../../images/list.png" style="width:12px;" /></a>';
							echo ' <a href="uploadreviewer.php?action=export&collid=' . $collid . '&searchvar=' . $searchVar . '" target="_self" title="' . $LANG['DOWNLOAD_RECS'] . '"><img src="../../images/dl.png" style="width:12px;" /></a>';
							if($uploadType != $SKELETAL && $uploadType != $NFNUPLOAD){
								echo '<span style="color:orange;margin-left:10px"><b>'.$LANG['CAUTION'].':</b></span> '.$LANG['CAUTION_REPLACE'];
							}
						}
						echo '</div>';
						if($uploadType != $NFNUPLOAD || $reportArr['new']){
							if($uploadType == $NFNUPLOAD) echo '<div>' . $LANG['MISMATCHED'] . ': ';
							else echo '<div>' . $LANG['NEW_RECORDS'] . ': ';
							echo $reportArr['new'];
							if($reportArr['new']){
								echo ' <a href="uploadreviewer.php?collid=' . $collid . '&searchvar=occid:IS_NULL" target="_blank" title="' . $LANG['PREVIEW'] . '"><img src="../../images/list.png" style="width:12px;" /></a>';
								echo ' <a href="uploadreviewer.php?action=export&collid=' . $collid . '&searchvar=occid:IS_NULL" target="_self" title="' . $LANG['DOWNLOAD_RECS'] . '"><img src="../../images/dl.png" style="width:12px;" /></a>';
								if($uploadType == $NFNUPLOAD) echo '<span style="margin-left:15px;color:orange">&gt;&gt; ' . $LANG['FAILED_LINK'] . '</span>';
							}
							echo '</div>';
						}
						if(isset($reportArr['matchappend']) && $reportArr['matchappend']){
							echo '<div>' . $LANG['MATCHING_CATALOG'] . ': ';
							echo $reportArr['matchappend'];
							if($reportArr['matchappend']){
								echo ' <a href="uploadreviewer.php?collid=' . $collid . '&searchvar=matchappend" target="_blank" title="' . $LANG['PREVIEW'] . '"><img src="../../images/list.png" style="width:12px;" /></a>';
								echo ' <a href="uploadreviewer.php?action=export&collid=' . $collid . '&searchvar=matchappend" target="_self" title="' . $LANG['DOWNLOAD_RECS'] . '"><img src="../../images/dl.png" style="width:12px;" /></a>';
							}
							echo '</div>';
							echo '<div style="margin-left:15px;"><span style="color:orange;">' . $LANG['WARNING'] . ':</span> ';
							echo $LANG['WARNING_DUPES'] . '</div>';
						}
						if($uploadType != $NFNUPLOAD && $uploadType != $SKELETAL){
							if(isset($reportArr['sync']) && $reportArr['sync']){
								echo '<div>' . $LANG['RECS_SYNC'] . ': ';
								echo $reportArr['sync'];
								if($reportArr['sync']){
									echo ' <a href="uploadreviewer.php?collid=' . $collid .'&searchvar=sync" target="_blank" title="' . $LANG['PREVIEW'] . '"><img src="../../images/list.png" style="width:12px;" /></a>';
									echo ' <a href="uploadreviewer.php?action=export&collid=' . $collid .'&searchvar=sync" target="_self" title="' . $LANG['DOWNLOAD_RECS'] . '"><img src="../../images/dl.png" style="width:12px;" /></a>';
								}
								echo '</div>';
								echo '<div style="margin-left:15px;">'.$LANG['EXPL_SYNC'].'.</div>';
								echo '<div style="margin-left:15px;"><span style="color:orange;">'. $LANG['WARNING'] .':</span> '.$LANG['WARNING_REPLACE'].'</div>';
							}
							if(isset($reportArr['exist']) && $reportArr['exist']){
								echo '<div>'.$LANG['NOT_MATCHING'].': '.$reportArr['exist'];
								if($reportArr['exist']){
									echo ' <a href="uploadreviewer.php?collid=' . $collid .'&searchvar=exist" target="_blank" title="' . $LANG['PREVIEW'] . '"><img src="../../images/list.png" style="width:12px;" /></a>';
									echo ' <a href="uploadreviewer.php?action=export&collid=' . $collid .'&searchvar=exist" target="_self" title="' . $LANG['DOWNLOAD_RECS'] . '"><img src="../../images/dl.png" style="width:12px;" /></a>';
								}
								echo '</div>';
								echo '<div style="margin-left:15px;">'.$LANG['EXPECTED'].'. '.$LANG['FULL_REFRESH'].'</div>';
							}
							if(isset($reportArr['nulldbpk']) && $reportArr['nulldbpk']){
								echo '<div style="color:red;">' . $LANG['NULL_RM'] . ': ';
								echo $reportArr['nulldbpk'];
								if($reportArr['nulldbpk']){
									echo ' <a href="uploadreviewer.php?collid=' . $collid .'&searchvar=dbpk:IS_NULL" target="_blank" title="' . $LANG['PREVIEW'] . '"><img src="../../images/list.png" style="width:12px;" /></a>';
									echo ' <a href="uploadreviewer.php?action=export&collid=' . $collid .'&searchvar=dbpk:IS_NULL" target="_self" title="' . $LANG['DOWNLOAD_RECS'] . '"><img src="../../images/dl.png" style="width:12px;" /></a>';
								}
								echo '</div>';
							}
							if(isset($reportArr['dupdbpk']) && $reportArr['dupdbpk']){
								echo '<div style="color:red;">' . $LANG['DUP_RM'] . ': ';
								echo $reportArr['dupdbpk'];
								if($reportArr['dupdbpk']){
									echo ' <a href="uploadreviewer.php?collid=' . $collid .'&searchvar=dupdbpk" target="_blank" title="' . $LANG['PREVIEW'] . '"><img src="../../images/list.png" style="width:12px;" /></a>';
									echo ' <a href="uploadreviewer.php?action=export&collid=' . $collid .'&searchvar=dupdbpk" target="_self" title="' . $LANG['DOWNLOAD_RECS'] . '"><img src="../../images/dl.png" style="width:12px;" /></a>';
								}
								echo '</div>';
							}
						}
						echo '</div>';
						//Extensions
						if(isset($reportArr['ident'])) echo '<div>'.$LANG['IDENT_TRANSFER'].': '.$reportArr['ident'].'</div>';
						if(isset($reportArr['image'])) echo '<div>'.$LANG['IMAGE_TRANSFER'].': '.$reportArr['image'].'</div>';
						if($uploadType == $DWCAUPLOAD || $uploadType == $IPTUPLOAD || $uploadType == $SYMBIOTA){
							$sourceIndex = $duManager->getSourcePortalIndex();
							$publicationGuid = $duManager->getPublicationGuid();
						}
						?>
					</div>
					<form name="finaltransferform" action="specuploadprocessor.php" method="post" style="margin-top:10px;" onsubmit="return confirm('<?= $LANG['FINAL_TRANSFER'] ?>');">
						<input type="hidden" name="collid" value="<?= $collid ?>" >
						<input type="hidden" name="uploadtype" value="<?= $uploadType ?>" >
						<input type="hidden" name="observeruid" value="<?= $observerUid ?>" >
						<input type="hidden" name="updateaction" value="<?= htmlspecialchars($updateAction) ?>" >
						<input type="hidden" name="versiondata" value="<?= $versionData ?>" >
						<input type="hidden" name="verifyimages" value="<?= $verifyImages ?>" >
						<input type="hidden" name="processingstatus" value="<?= htmlspecialchars($processingStatus) ?>" >
						<input type="hidden" name="uspid" value="<?= $uspid ?>" >
						<input type="hidden" name="sourceindex" value="<?= $sourceIndex ?>" >
						<input type="hidden" name="publicationGuid" value="<?= htmlspecialchars($publicationGuid) ?>" >
						<input type="hidden" name="fieldlist" value="<?= $duManager->getTargetFieldStr() ?>" >
						<div style="margin:5px;">
							<button type="submit" name="action" value="activateOccurrences"><?= $LANG['TRANS_RECS'] ?></button>
						</div>
					</form>
				</fieldset>
				<?php
			}
		}
		elseif($action == 'activateOccurrences' || $finalTransfer){
			echo '<ul>';
			$duManager->setTargetFieldArr($_POST['fieldlist']);
			$duManager->finalTransfer();
			echo '</ul>';
		}
	}
	else{
		if(!$isEditor || !$collid) echo '<h2>' . $LANG['NOT_AUTH'] . '</h2>';
		else{
			echo '<h2>';
			echo $LANG['PAGE_ERROR']. ' = ';
			echo ini_get("upload_max_filesize").'; post_max_size = '.ini_get('post_max_size');
			echo $LANG['USE_BACK'];
			echo '</h2>';
		}
	}
	?>
</div>
<?php
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>