<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/GuidManager.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load('collections/admin/guidmapper');

header('Content-Type: text/html; charset=' . $CHARSET);
ini_set('max_execution_time', 3600);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/admin/guidmapper.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = array_key_exists('collid', $_REQUEST) ? filter_var($_REQUEST['collid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$action = array_key_exists('formsubmit', $_POST) ? $_POST['formsubmit'] : '';

$isEditor = 0;
if($IS_ADMIN || array_key_exists('CollAdmin', $USER_RIGHTS) && in_array($collid, $USER_RIGHTS['CollAdmin'])){
	$isEditor = 1;
}

$guidManager = new GuidManager();
$guidManager->setCollid($collid);
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET; ?>">
	<title><?= $LANG['UID_MAP'] ?></title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script type="text/javascript">
		function toggle(target){
			var objDiv = document.getElementById(target);
			if(objDiv){
				if(objDiv.style.display=="none"){
					objDiv.style.display = "block";
				}
				else{
					objDiv.style.display = "none";
				}
			}
			else{
			  	var divs = document.getElementsByTagName("div");
			  	for (var h = 0; h < divs.length; h++) {
			  	var divObj = divs[h];
					if(divObj.className == target){
						if(divObj.style.display=="none"){
							divObj.style.display="block";
						}
					 	else {
					 		divObj.style.display="none";
					 	}
					}
				}
			}
			return false;
		}
    </script>
</head>
<body>
	<?php
$displayLeftMenu = (isset($admin_guidmapperMenu)?$admin_guidmapperMenu:"true");
include($SERVER_ROOT.'/includes/header.php');
?>
<div class="navpath">
	<a href="../../index.php"><?= $LANG['HOME'] ?></a> &gt;&gt;
	<a href="../misc/collprofiles.php?collid=<?= $collid ?>&emode=1"><?= $LANG['COL_MGMNT'] ?></a> &gt;&gt;
	<b><?= $LANG['UID_MAP']; ?></b>
</div>
<!-- This is inner text! -->
<div role="main" id="innertext">
	<div style="margin:10px;">
		<h1 class="page-heading"><?= $LANG['GUID_CP']; ?></h1>
	</div>
	<?php
	if($isEditor){
		if($action == 'populateCollectionGUIDs'){
			echo '<ul>';
			$guidManager->populateGuids();
			echo '</ul>';
		}
		elseif($action == 'populateGUIDs'){
			echo '<ul>';
			$guidManager->populateGuids();
			echo '</ul>';
		}

		$occCnt = $guidManager->getOccurrenceCount();
		if($collid){
			echo '<h3>' . $guidManager->getCollectionName() . '</h3>';
		}
		?>
		<div style="font-weight:bold;"><?= $LANG['REC_WO_GUIDS']; ?></div>
		<div style="margin:10px;">
			<div><?= '<b>' . $LANG['OCCS'] . ': </b>' . $occCnt; ?></div>
			<?php
			$extArr = $guidManager->getExtensionCounts();
			foreach($extArr as $extName => $extCnt){
				?>
				<div><?= '<b>' . $LANG[strtoupper($extName)] . ': </b>' . $extCnt; ?></div>
				<?php
			}
			?>
		</div>
		<div id="guidadmindiv">
			<form name="dwcaguidform" action="guidmapper.php" method="post">
				<fieldset style="padding:15px;">
					<legend><b><?= $LANG['UID_MAP']; ?></b></legend>
					<div style="clear:both;margin:10px;">
						<input type="hidden" name="collid" value="<?= $collid; ?>" />
						<button type="submit" name="formsubmit" value="populateGUIDs" ><?= $LANG['POP_GUID']; ?></button>
					</div>
				</fieldset>
			</form>
		</div>
		<?php
	}
	else{
		echo '<h2>' . $LANG['NOT_AUTH'] . '</h2>';
	}
	?>
</div>
<?php
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>
