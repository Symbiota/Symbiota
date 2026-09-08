<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/UserTaxonomy.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load('profile/usertaxonomymanager');

header("Content-Type: text/html; charset=".$CHARSET);

$action = array_key_exists("action",$_POST)?$_POST["action"]:"";

$utManager = new UserTaxonomy();

$isEditor = 0;
if($SYMB_UID){
	if( $IS_ADMIN ){
		$isEditor = 1;
	}
}
else{
	header('Location: ../profile/index.php?refurl=../profile/usertaxonomymanager.php');
}

$statusStr = '';
if($isEditor){
	if($action == 'addTaxonomicRelationship'){
		$uid = $_POST['uid'];
		$tid = $_POST['tid'];
		$editorStatus = $_POST['editorstatus'];
		$geographicScope = $_POST['geographicscope'];
		$notes = $_POST['notes'];
		if($utManager->addUser($uid, $tid, $editorStatus, $geographicScope, $notes)){
			$statusStr = $LANG['SUCCESS_ADDING_TAXON_INTEREST'];
		}
		else{
			$statusStr = $LANG['ERROR_ADDING_TAXON_INTEREST'];
		}
	}
	elseif(array_key_exists('delutid',$_GET)){
		$delUid = array_key_exists('deluid',$_GET)?$_GET['deluid']:0;
		$editorStatus = array_key_exists('es',$_GET)?$_GET['es']:'';
		$statusStr = $utManager->deleteUser($_GET['delutid'],$delUid,$editorStatus);
	}
}
$editorArr = $utManager->getTaxonomyEditors();
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<title><?= $LANG['TAX_PERMISSIONS']; ?></title>
	<link href="<?= $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/symb/taxa.suggest.js?v=1" type="text/javascript"></script>
	<script>
		$(document).ready(function() {

			const taxonInput = document.querySelector("#taxoninput");
			if(taxonInput){
				taxonInput.addEventListener("focus", (event) => {
					taxaSuggest.config.clientRoot = "<?= $CLIENT_ROOT ?>";
					taxaSuggest.config.includeAuthor = <?= (empty($TAXON_AUTOCOMPLETE_INCLUDE_AUTHOR) ? 'false' : 'true') ?>;
					taxaSuggest.config.includeKingdom = <?= (empty($TAXON_AUTOCOMPLETE_INCLUDE_KINGDOM) ? 'false' : 'true') ?>;
					taxaSuggest.initiate("taxoninput", function(result){
						if (result.valid) {
							document.getElementById("tidinput").value = result.item.id;
						}
						else{
							document.getElementById("tidinput").value = "";
							if(this.value != ""){
								alert("<?= $LANG['SELECT_FROM_LIST'] ?>");
							}
						}
					});
				});
			}

		});

		function verifyUserAddForm(f){
			if(f.taxon.value != "" && f.tid.value == ""){
				alert("<?= $LANG['SELECT_FROM_LIST'] ?>");
				return false;
			}
			return true;
		}
	</script>
	<script type="text/javascript" src="../js/symb/shared.js"></script>
	<style>
		.underlined-text {
			text-decoration: underline;
		}
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = (isset($profile_usertaxonomymanagerMenu)?$profile_usertaxonomymanagerMenu:true);
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class='navpath'>
		<a href='../index.php'>Home</a> &gt;&gt;
		<b><?= $LANG['TAX_PERMISSIONS'] ?></b>
	</div>
	<?php

	if($statusStr){
		?>
		<hr/>
		<div style="color:<?= (strpos($statusStr,'SUCCESS') !== false?'green':'red'); ?>;margin:15px;">
			<?= $statusStr; ?>
		</div>
		<hr/>
		<?php
	}
	if($isEditor){
		?>
		<!-- This is inner text! -->
		<div role="main" id="innertext">
			<h1 class="page-heading"><?= $LANG['TAX_PERMISSIONS']; ?></h1>
			<div style="float:right;" title="Add a new taxonomic relationship">
				<a href="#" onclick="toggle('addUserDiv')">
					<img style='border:0px;width:1.3em;' src='../images/add.png' alt='<?= $LANG['ADD'] ?>'/>
				</a>
			</div>
			<div id="addUserDiv" style="display:none;">
				<fieldset style="padding:20px;">
					<legend><b><?= $LANG['NEW_TAX_REL'] ?></b></legend>
					<form name="adduserform" action="usertaxonomymanager.php" method="post" onsubmit="return verifyUserAddForm(this)">
						<div style="margin:3px;">
							<b><?= $LANG['USER'] ?></b><br/>
							<select name="uid" required>
								<option value="">-------------------------------</option>
								<?php
								$userArr = $utManager->getUserArr();
								foreach($userArr as $uid => $displayName){
									echo '<option value="'.$uid.'">'.$displayName.'</option>';
								}
								?>
							</select>
						</div>
						<div style="margin:3px;">
							<b><?= $LANG['TAXON'] ?></b><br/>
							<input id="taxoninput" name="taxon" type="text" value="" style="width:90%;" required />
							<input id="tidinput" name="tid" type="hidden" value="" >
						</div>
						<div style="margin:3px;">
							<b><?= $LANG['SCOPE_REL'] ?></b><br/>
							<select name="editorstatus" required>
								<option value="">----------------------------</option>
								<option value="OccurrenceEditor"><?= $LANG['OCC_ID_EDITOR'] ?></option>
								<option value="RegionOfInterest"><?= $LANG['REGION'] ?></option>
								<option value="TaxonomicThesaurusEditor"><?= $LANG['TAX_THES_EDITOR'] ?></option>
							</select>

						</div>
						<div style="margin:3px;">
							<b><?= $LANG['SCOPE_LIMITS'] ?></b><br/>
							<input name="geographicscope" type="text" value="" style="width:90%;" />

						</div>
						<div style="margin:3px;">
							<b><?= $LANG['NOTES'] ?></b><br/>
							<input name="notes" type="text" value="" style="width:90%;" />

						</div>
						<div style="margin:3px;">
							<button name="action" type="submit" value="addTaxonomicRelationship"><?= $LANG['ADD_TAX_REL'] ?></button>
						</div>
					</form>
				</fieldset>
			</div>
			<div>
				<?php
				foreach($editorArr as $editorStatus => $userArr){
					$cat = 'Undefined';
					if($editorStatus == 'RegionOfInterest') $cat = $LANG['REGION'];
					elseif($editorStatus == 'OccurrenceEditor') $cat = $LANG['OCC_EDIT'];
					elseif($editorStatus == 'TaxonomicThesaurusEditor') $cat = $LANG['TAX_THES'];
					echo '<div><b class="underlined-text">'.$cat.'</b></div>';
					echo '<ul style="margin:10px;">';
					foreach($userArr as $uid => $uArr){
						$username = $uArr['username'];
						unset($uArr['username']);
						echo '<li>';
						echo '<b>'.$username.'</b>';
						$confirmStr = $LANG['REMOVE_LINKS'];
						$titleStr = $LANG['DELETE_LINKS'];
						echo '<a href="usertaxonomymanager.php?delutid=all&deluid=' . $uid . '&es=' . Sanitize::outString($editorStatus) . '" onclick="return confirm(\'' . Sanitize::outString($confirmStr) . '\'" title="' . Sanitize::outString($titleStr) . '">';
						echo '<img src="../images/drop.png" style="width:1.3em;" alt="' . $LANG['DELETE_LINKS'] . '" />';
						echo '</a>';
						foreach($uArr as $utid => $utArr){
							echo '<li style="margin-left:15px;">'.$utArr['sciname'];
							if($utArr['geoscope']) echo ' ('.$utArr['geoscope'].')';
							if($utArr['notes']) echo ': '.$utArr['notes'];
							$confirmStr2 = $LANG['REMOVE_ONE_LINK'];
							$titleStr2 = $LANG['DELETE_A_LINK'];
							echo '<a href="usertaxonomymanager.php?delutid=' . $utid . '" onclick="return confirm(\'' . Sanitize::outString($confirmStr2) . '\'" title="' . Sanitize::outString($titleStr2) . '">';
							echo '<img src="../images/drop.png" style="width:1.3em; alt="' . $LANG['DELETE_LINKS'] . '" />';
							echo '</a>';
							echo '</li>';
						}
						echo '</li>';
					}
					echo '</ul>';
				}
				?>
			</div>
		</div>
		<?php
	}
	else{
		echo '<div style="color:red;">' . $LANG['NOT_AUTH'] . '</div>';
	}
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
