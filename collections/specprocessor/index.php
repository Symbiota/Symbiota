<!DOCTYPE html>

<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SpecProcessorManager.php');
include_once($SERVER_ROOT.'/classes/OccurrenceCrowdSource.php');
include_once($SERVER_ROOT.'/classes/SpecProcessorOcr.php');
include_once($SERVER_ROOT.'/classes/ImageProcessor.php');
include_once($SERVER_ROOT.'/content/lang/collections/specprocessor/index.'.$LANG_TAG.'.php');
header("Content-Type: text/html; charset=".$CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/specprocessor/index.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$action = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';
$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$spprId = array_key_exists('spprid',$_REQUEST)?$_REQUEST['spprid']:0;
//NLP and OCR variables
$spNlpId = array_key_exists('spnlpid',$_REQUEST)?$_REQUEST['spnlpid']:0;
$procStatus = array_key_exists('procstatus',$_REQUEST)?$_REQUEST['procstatus']:'unprocessed';
$displayMode = array_key_exists('displaymode',$_REQUEST)?$_REQUEST['displaymode']:0;
$tabIndex = array_key_exists('tabindex',$_REQUEST)?$_REQUEST['tabindex']:0;

//Sanitation
if($action && !preg_match('/^[a-zA-Z0-9\s_]+$/',$action)) $action = '';
if(!is_numeric($collid)) $collid = 0;
if(!is_numeric($spprId)) $spprId = 0;
if(!is_numeric($spNlpId)) $spNlpId = 0;
if($procStatus && !preg_match('/^[a-zA-Z]+$/',$procStatus)) $procStatus = '';
if(!is_numeric($displayMode)) $displayMode = 0;
if(!is_numeric($tabIndex)) $tabIndex = 0;


$specManager = new SpecProcessorManager();
$specManager->setCollId($collid);

$isEditor = false;
if($IS_ADMIN || (array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollAdmin"]))){
 	$isEditor = true;
}

$fileName = '';
$statusStr = '';
if($isEditor){
	if($action == 'Analyze Image Data File'){
		if($_POST['projecttype'] == 'file'){
			$imgProcessor = new ImageProcessor();
			$fileName = $imgProcessor->loadImageFile();
		}
	}
	elseif($action == 'Save Profile'){
		if($_POST['spprid']){
			$specManager->editProject($_POST);
		}
		else{
			$specManager->addProject($_POST);
		}
	}
	elseif($action == 'Delete Profile'){
		$specManager->deleteProject($_POST['sppriddel']);
	}
	elseif($action == 'Add to Queue'){
		$csManager = new OccurrenceCrowdSource();
		$csManager->setCollid($collid);
		$statusStr = $csManager->addToQueue($_POST['omcsid'],$_POST['family'],$_POST['taxon'],$_POST['country'],$_POST['stateprovince'],$_POST['limit']);
		if(is_numeric($statusStr)){
			$statusStr .= ' records added to queue';
		}
		$action = '';
	}
	elseif($action == 'delqueue'){
		$csManager = new OccurrenceCrowdSource();
		$csManager->setCollid($collid);
		$statusStr = $csManager->deleteQueue($_GET['omcsid']);
	}
	elseif($action == 'Edit Crowdsource Project'){
		$omcsid = $_POST['omcsid'];
		$csManager = new OccurrenceCrowdSource();
		$csManager->setCollid($collid);
		$statusStr = $csManager->editProject($omcsid,$_POST['instr'],$_POST['url']);
	}
}
?>
<html lang="<?php echo $LANG_TAG ?>">
	<head>
		<title>Specimen Processor Control Panel</title>
		<?php
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<script src="../../js/jquery-3.2.1.min.js" type="text/javascript"></script>
		<script src="../../js/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
		<link href="../../js/jquery-ui/jquery-ui.min.css" type="text/css" rel="Stylesheet" />
		<script src="../../js/symb/shared.js?ver=131106" type="text/javascript"></script>
		<script>
			$(document).ready(function() {
				$('#tabs').tabs({
					select: function(event, ui) {
						return true;
					},
					active: <?php echo $tabIndex; ?>,
					beforeLoad: function( event, ui ) {
						$(ui.panel).html("<p>Loading...</p>");
					}
				});

			});
		</script>
	</head>
	<body>
		<?php
		$displayLeftMenu = false;
		include($SERVER_ROOT.'/includes/header.php');
		if(isset($collections_specprocessor_indexCrumbs)){
			if($collections_specprocessor_indexCrumbs){
				echo "<div class='navpath'>";
				echo "<a href='../../index.php'>" . (isset($LANG['HOME']) ? $LANG['HOME'] : 'Home') . "</a> &gt;&gt; ";
				echo $collections_specprocessor_indexCrumbs;
				echo " <b>" . (isset($LANG['SPEC_CONTROL_PANEL']) ? $LANG['SPEC_CONTROL_PANEL'] : 'Specimen Processor Control Panel') . "</b>";
				echo "</div>";
			}
		}
		else{
			echo '<div class="navpath">';
			echo '<a href="../../index.php">' . (isset($LANG['HOME']) ? $LANG['HOME'] : 'Home') . '</a> &gt;&gt; ';
			echo '<a href="../misc/collprofiles.php?collid=' . htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS) . '&emode=1">' . (isset($LANG['COLL_CONTROL_PANEL']) ? $LANG['COLL_CONTROL_PANEL'] : 'Collection Control Panel') . '</a> &gt;&gt; ';
			echo '<b>' . (isset($LANG['SPEC_CONTROL_PANEL']) ? $LANG['SPEC_CONTROL_PANEL'] : 'Specimen Processor Control Panel') . '</b>';
			echo '</div>';
		}
		?>
		<!-- This is inner text! -->
		<div id="innertext">
			<h2><?php echo $specManager->getCollectionName(); ?></h2>
			<?php
			if($statusStr){
				?>
				<div style='margin:20px 0px 20px 0px;'>
					<hr/>
					<div style="margin:15px;color:<?php echo (stripos($statusStr,'error') !== false?'red':'green'); ?>">
						<?php echo $statusStr; ?>
					</div>
					<hr/>
				</div>
				<?php
			}
			if($collid){
				?>
				<div id="tabs" class="taxondisplaydiv">
				    <ul>
				        <li><a href="imageprocessor.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS) . '&spprid=' . htmlspecialchars($spprId, HTML_SPECIAL_CHARS_FLAGS) . '&submitaction=' . htmlspecialchars($action, HTML_SPECIAL_CHARS_FLAGS) . '&filename=' . htmlspecialchars($fileName, HTML_SPECIAL_CHARS_FLAGS); ?>"> <?php echo (isset($LANG['IMG_LOAD']) ? $LANG['IMG_LOAD'] : 'Image Loading')?> </a></li>
				        <li><a href="crowdsource/controlpanel.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>"> <?php echo (isset($LANG['CROWDSRC']) ? $LANG['CROWDSRC'] : 'Crowdsourcing')?> </a></li>
				        <li><a href="ocrprocessor.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS) . '&procstatus=' . htmlspecialchars($procStatus, HTML_SPECIAL_CHARS_FLAGS) . '&spprid=' . htmlspecialchars($spprId, HTML_SPECIAL_CHARS_FLAGS); ?>"> <?php echo (isset($LANG['OCR']) ? $LANG['OCR'] : 'OCR')?> </a></li>
				        <!--
				        <li><a href="nlpprocessor.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS) . '&spnlpid=' . htmlspecialchars($spNlpId, HTML_SPECIAL_CHARS_FLAGS); ?>">NLP</a></li>
				         -->
				        <li><a href="reports.php?<?php echo htmlspecialchars($_SERVER['QUERY_STRING'], HTML_SPECIAL_CHARS_FLAGS); ?>"> <?php echo (isset($LANG['REPORTS']) ? $LANG['REPORTS'] : 'Reports')?> </a></li>
				        <li><a href="exporter.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS) . '&displaymode=' . htmlspecialchars($displayMode, HTML_SPECIAL_CHARS_FLAGS); ?>"> <?php echo (isset($LANG['EXPORTER']) ? $LANG['EXPORTER'] : 'Exporter')?> </a></li>
				        <?php
				        if($ACTIVATE_GEOLOCATE_TOOLKIT){
					        ?>
					        <li><a href="geolocate.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>"> <?php echo (isset($LANG['GEOLOC_COGE']) ? $LANG['GEOLOC_COGE'] : 'GeoLocate CoGe')?> </a></li>
					        <?php
				        }
				        ?>
				    </ul>
				</div>
				<?php
			}
			else{
				?>
				<div style='font-weight:bold;'>
					<?php echo (isset($LANG['NOT_IDENTIFIED']) ? $LANG['NOT_IDENTIFIED'] : 'Collection project has not been identified')?>
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