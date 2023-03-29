<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/content/lang/collections/misc/collprops.'.$LANG_TAG.'.php');
include_once($SERVER_ROOT.'/classes/OccurrenceCollectionProperty.php');
header('Content-Type: text/html; charset='.$CHARSET);

$action = array_key_exists('submitaction',$_POST)?$_POST['submitaction']:'';
$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;

$propManager = new OccurrenceCollectionProperty();
$propManager->setCollid($collid);
$collMeta = $propManager->getCollMetaArr();

$isEditor = 0;
if($SYMB_UID){
	if($IS_ADMIN || (array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin']))){
		$isEditor = 1;
	}
}
$statusStr = '';
if($isEditor){
	if($action == 'convertFormat'){
		if($propManager->transferDynamicProperties()) $statusStr = '<span style="color:green">Success! Old collection profile updated</span>';
		else $statusStr = '<span style="color:red">ERROR:</span> updating collection profile : '.$propManager->getErrorMessage();
	}
	elseif($action == 'saveTitleOverride'){

	}
}
?>
<html>
<head>
	<title><?php echo $collMeta['collName'].(isset($LANG['SPECIAL_PROPS'])?$LANG['SPECIAL_PROPS']:'Special Properties'); ?></title>
	<?php

	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script>
		function verifyAddContactForm(f){
			if(f.uid.value == ""){
				alert("Please select a user from list");
				return false;
			}
			return true;
		}
	</script>
	<style type="text/css">
		fieldset{ padding: 10px }
		legend{ font-weight: bold }
		.fieldRowDiv{ clear:both; margin: 2px 0px; }
		.fieldDiv{ float:left; margin: 2px 10px 2px 0px; }
		.fieldLabel{ font-weight: bold; display: block; }
		.fieldDiv button{ margin-top: 10px; }
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = false;
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class='navpath'>
		<a href='../../index.php'><?php echo (isset($LANG['HOME'])?$LANG['HOME']:'Home'); ?></a> &gt;&gt;
		<a href='collprofiles.php?emode=1&collid=<?php echo $collid; ?>'><?php echo (isset($LANG['SPECIAL_PROPS'])?$LANG['SPECIAL_PROPS']:'Special Properties'); ?></a> &gt;&gt;
		<b>Collection Management Properties</b>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<?php
		echo '<div style="font-weight:bold;font-size: 1.3em">'.$collMeta['collName'].' Management Properties</div>';
		if($isEditor){
			$conversionCode = $propManager->getSystemConvertionCode();
			if($conversionCode == 0){
				$statusStr = '<span style="color:orange">WARNING:</span> Collection Properties has not yet been activated (e.g. missing tables or other schema issues)';
			}
			elseif($conversionCode == 2){
				$statusStr = '<span style="color:orange">WARNING:</span> Old Collection Properties need to be converted to new format! ';
				$statusStr .= '<form name="convertPropForm" action="collectionproperties.php" method="post" style="display:inline; margin-left:20px"><input name="collid" type="hidden" value="'.$collid.'" />';
				$statusStr .= '<button name="submitaction" type="submit" value="convertFormat">Convert to new format</button>';
				$statusStr .= '</form>';
			}
			if($statusStr){
				echo '<fieldset><legend>Action Panel</legend>';
				echo $statusStr;
				echo '</fieldset>';
			}
			$dynamicProps = $propManager->getDynPropArr();
			?>
			<fieldset style="margin:15px;padding:15px;">
				<legend><?php echo (isset($LANG['PUB_PROPS'])?$LANG['PUB_PROPS']:'Publication Properties'); ?></legend>
				<div style="margin:20px">Following field will override the title of the collection/observation project that is published in the EML file within the Darwin Core Archive (DwC-A) export file</div>
				<form name="pubPropForm" action="collectionproperties.php" method="post" onsubmit="return verifyPubPropForm(this)">
					<div style="margin:25px;clear:both;">
						<span class="fieldLabel"><?php echo (isset($LANG['TITLE_OVERRIDE'])?$LANG['TITLE_OVERRIDE']:'Title Override'); ?>: </span>
						<input name="titleOverride" type="text" value="<?php echo (isset($dynamicProps['publicationProps']['titleOverride'])?$dynamicProps['publicationProps']['titleOverride']:''); ?>" style="width:80%" />
					</div>
					<div style="margin:25px;">
						<input type="hidden" name="collid" value="<?php echo $collid; ?>" />
						<button name="submitaction" type="submit" value="saveTitleOverride">Save Title Override</button>
					</div>
				</form>
			</fieldset>
			<fieldset style="margin:15px;padding:15px;">
				<legend><?php echo (isset($LANG['OCC_EDIT_PROPS'])?$LANG['OCC_EDIT_PROPS']:'Occurrence Editor Properties'); ?></legend>
				<?php
				$moduleArr = array();
				if(isset($dynamicProps['editorProperties']['module'])) $moduleArr = $dynamicProps['editorProps']['modules-panel'];
				?>
				<div class="fieldRowDiv">
					<div class="fieldDiv">
						<span class="fieldLabel"><?php echo (isset($LANG['TITLE_OVERRIDE'])?$LANG['TITLE_OVERRIDE']:'Title Override'); ?>: </span>
						<input name="paleomodule" type="checkbox" value="1" <?php echo (isset($moduleArr['paleo']['status']) && $moduleArr['paleo']['status']?'checked':''); ?> />
					</div>
				</div>
			</fieldset>
			<fieldset style="margin:15px;padding:15px;">
				<legend><?php echo (isset($LANG['OCC_EDIT_PROPS'])?$LANG['OCC_EDIT_PROPS']:'Occurrence Editor Properties'); ?></legend>
				<?php
				$moduleArr = array();
				if(isset($dynamicProps['editorProperties']['module'])) $moduleArr = $dynamicProps['editorProps']['modules-panel'];
				?>
				<div class="fieldRowDiv">
					<div class="fieldDiv">
						<span class="fieldLabel"><?php echo (isset($LANG['TITLE_OVERRIDE'])?$LANG['TITLE_OVERRIDE']:'Title Override'); ?>: </span>
						<input name="paleomodule" type="checkbox" value="1" <?php echo (isset($moduleArr['paleo']['status']) && $moduleArr['paleo']['status']?'checked':''); ?> />
					</div>
				</div>
			</fieldset>

			sesarTools', 'IGSN Profile
			labelFormat

			<?php
		}
		else echo '<div style="font-weight:bold;font-size:120%;">'.(isset($LANG['NOT_AUTH'])?$LANG['NOT_AUTH']:'Unauthorized to edit special collection properties').'</div>';
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>