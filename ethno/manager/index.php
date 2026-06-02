<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoProjectManager.php');
include_once($SERVER_ROOT.'/classes/ReferenceManager.php');

header("Content-Type: text/html; charset=".$CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../ethno/manager/index.php?collid='.$collid);

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$tabIndex = array_key_exists("tabindex",$_REQUEST)?$_REQUEST["tabindex"]:0;
$action = array_key_exists('action',$_POST)?$_POST['action']:'';

//Sanitation
if($action && !preg_match('/^[a-zA-Z0-9\s_]+$/',$action)) $action = '';
if(!is_numeric($collid)) $collid = 0;
if(!is_numeric($tabIndex)) $tabIndex = 0;

$ethnoManager = new EthnoProjectManager();
$refManager = new ReferenceManager();
$ethnoManager->setCollid($collid);

$statusStr = '';
if($action === 'Remove Personnel'){
	$ethnoManager->deleteProjPerLink($_POST);
	$tabIndex = 2;
}
elseif($action === 'Create Reference'){
	$refManager->createReference($_POST);
	$refId = $refManager->getRefId();
	$ethnoManager->createProjRefLink($refId);
	$tabIndex = 3;
}
elseif($action === 'Link Reference'){
	$ethnoManager->createProjRefLink($_POST['linkReferenceID']);
	$tabIndex = 3;
}
elseif($action === 'Remove Reference'){
	$ethnoManager->deleteProjRefLink($_POST);
	$tabIndex = 3;
}
elseif($action === 'Link community'){
	$ethnoManager->createProjCommLink($_POST);
	$tabIndex = 1;
}
elseif($action === 'Remove Community'){
	$ethnoManager->deleteProjCommLink($_POST);
	$tabIndex = 1;
}
elseif($action === 'Link language'){
	$ethnoManager->createProjLangLink($_POST);
	$tabIndex = 0;
}
elseif($action === 'Remove Language'){
	$ethnoManager->deleteProjLangLink($_POST);
	$tabIndex = 0;
}
elseif($action === 'Create Personnel'){
	$ethnoManager->createPersonnel($_POST);
	$perId = $ethnoManager->getPerid();
	$pArr = $_POST;
	$pArr['perid'] = $perId;
	$ethnoManager->createPersonnelLink($pArr);
	$cplId = $ethnoManager->getCplid();
	$tabIndex = 2;
}
elseif($action === 'Save Personnel Changes'){
	$ethnoManager->savePersonnelChanges($_POST);
	$ethnoManager->savePersonnelLinkChanges($_POST);
	$tabIndex = 2;
}
elseif($action === 'Create Community'){
	$ethnoManager->createCommunity($_POST);
	$comId = $ethnoManager->getComid();
	$pArr = $_POST;
	$pArr['addCommID'] = $comId;
	$ethnoManager->createProjCommLink($pArr);
	$tabIndex = 1;
}
elseif($action === 'Save Community Changes'){
	$ethnoManager->saveCommunityChanges($_POST);
	$tabIndex = 1;
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> - Manage Project</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<link href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<script src="../../js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="../../js/jquery-ui.min.js" type="text/javascript"></script>
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
include($SERVER_ROOT.'/includes/header.php');

echo '<div class="navpath">';
echo '<a href="../../index.php">Home</a> &gt;&gt; ';
echo '<a href="../../collections/misc/collprofiles.php?collid='.$collid.'&emode=1">Collection Control Panel</a> &gt;&gt; ';
echo '<b>Manage Project</b>';
echo '</div>';

?>
<!-- This is inner text! -->
<div id="innertext">
	<h2><?php echo $ethnoManager->getCollectionName(); ?></h2>
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
		<div id="tabs">
			<ul>
				<li><a href="languagemanager.php?collid=<?php echo $collid; ?>">Language Manager</a></li>
				<li><a href="communitymanager.php?collid=<?php echo $collid; ?>">Community Manager</a></li>
				<li><a href="personnelmanager.php?collid=<?php echo $collid; ?>">Personnel Manager</a></li>
				<li><a href="referencemanager.php?collid=<?php echo $collid; ?>">Reference Manager</a></li>
			</ul>
		</div>
		<?php
	}
	else{
		?>
		<div style='font-weight:bold;'>
			Collection project has not been identified
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
