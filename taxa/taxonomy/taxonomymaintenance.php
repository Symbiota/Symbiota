<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/TaxonomyHarvester.php');
include_once($SERVER_ROOT.'/content/lang/taxa/taxonomy/taxonomymaintenance.'.$LANG_TAG.'.php');

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../taxa/taxonomy/taxonomymaintenance.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';

$harvesterManager = new TaxonomyHarvester();

$isEditor = false;
if($IS_ADMIN || array_key_exists("Taxonomy",$USER_RIGHTS)) $isEditor = true;

if($isEditor){
	if($action == 'buildenumtree'){
		if($harvesterManager->buildHierarchyEnumTree()){
			$statusStr = (isset($LANG['SUCCESS_TAX_INDEX'])?$LANG['SUCCESS_TAX_INDEX']:'SUCCESS building Taxonomic Index');
		}
		else{
			$statusStr = (isset($LANG['ERROR_TAX_INDEX'])?$LANG['ERROR_TAX_INDEX']:'ERROR building Taxonomic Index').': '.$harvesterManager->getErrorMessage();
		}
	}
	elseif($action == 'rebuildenumtree'){
		if($harvesterManager->rebuildHierarchyEnumTree()){
			$statusStr = (isset($LANG['SUCCESS_TAX_INDEX'])?$LANG['SUCCESS_TAX_INDEX']:'SUCCESS building Taxonomic Index');
		}
		else{
			$statusStr = (isset($LANG['ERROR_TAX_INDEX'])?$LANG['ERROR_TAX_INDEX']:'ERROR building Taxonomic Index').': '.$harvesterManager->getErrorMessage();
		}
	}
}

?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE." ".(isset($LANG['TAXON_MAINT'])?$LANG['TAXON_MAINT']:'Taxonomy Maintenance'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>"/>
	<link href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script type="text/javascript" src="../../js/jquery-3.7.1.min.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui.min.js"></script>
</head>
<body>
	<?php
	$displayLeftMenu = (isset($taxa_admin_taxonomydisplayMenu)?$taxa_admin_taxonomydisplayMenu:"true");
	include($SERVER_ROOT.'/includes/header.php');
	if(isset($taxa_admin_taxonomydisplayCrumbs)){
		echo "<div class='navpath'>";
		echo "<a href='../index.php'>Home</a> &gt; ";
		echo $taxa_admin_taxonomydisplayCrumbs;
		echo " <b>Taxonomic Tree Viewer</b>";
		echo "</div>";
	}
	if(isset($taxa_admin_taxonomydisplayCrumbs)){
		if($taxa_admin_taxonomydisplayCrumbs){
			echo '<div class="navpath">';
			echo $taxa_admin_taxonomydisplayCrumbs;
			echo ' <b>Taxonomic Tree Viewer</b>';
			echo '</div>';
		}
	}
	else{
		?>
		<div class="navpath">
			<a href="../../index.php"><?php echo htmlspecialchars((isset($LANG['HOME'])?$LANG['HOME']:'Home'), HTML_SPECIAL_CHARS_FLAGS); ?></a> &gt;&gt;
			<a href="taxonomydisplay.php"><b><?php echo htmlspecialchars((isset($LANG['TAX_TREE_VIEW'])?$LANG['TAX_TREE_VIEW']:'Taxonomy Tree Viewer'), HTML_SPECIAL_CHARS_FLAGS); ?></b></a>
		</div>
		<?php
	}
	?>
	<!-- This is inner text! -->
	<div id="innertext">
		<?php
		if($statusStr){
			?>
			<hr/>
			<div style="color:<?php echo (strpos($statusStr,'SUCCESS') !== false?'green':'red'); ?>;margin:15px;">
				<?php echo $statusStr; ?>
			</div>
			<hr/>
			<?php
		}
		if($isEditor){
			?>


			<?php
		}
		else{
			?>
			<div style="margin:30px;font-weight:bold;font-size:120%;">
				<?php echo (isset($LANG['NOT_AUTH'])?$LANG['NOT_AUTH']:'You are not authorized to access this page'); ?>
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