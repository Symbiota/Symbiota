<!DOCTYPE html>

<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/UserTaxonomy.php');
@include_once($SERVER_ROOT.'/content/lang/profile/usertaxonomymanager.'.$LANG_TAG.'.php');
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
	if($action == 'Add Taxonomic Relationship'){
		$uid = $_POST['uid'];
		$taxon = $_POST['taxon'];
		$editorStatus = $_POST['editorstatus'];
		$geographicScope = $_POST['geographicscope'];
		$notes = $_POST['notes'];
		$statusStr = $utManager->addUser($uid, $taxon, $editorStatus, $geographicScope, $notes);
	}
	elseif(array_key_exists('delutid',$_GET)){
		$delUid = array_key_exists('deluid',$_GET)?$_GET['deluid']:0;
		$editorStatus = array_key_exists('es',$_GET)?$_GET['es']:'';
		$statusStr = $utManager->deleteUser($_GET['delutid'],$delUid,$editorStatus);
	}
}
$editorArr = $utManager->getTaxonomyEditors();
?>
<html lang="<?php echo $LANG_TAG ?>">
<head>
	<title><?php echo (isset($LANG['TAX_PERMISSIONS'])?$LANG['TAX_PERMISSIONS']:'Taxonomic Interest User permissions'); ?></title>
	<link href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.min.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script type="text/javascript" src="../js/jquery-3.7.1.min.js"></script>
	<script type="text/javascript" src="../js/jquery-ui.min.js"></script>
	<script>
		$(document).ready(function() {
			$( "#taxoninput" ).autocomplete({
				source: "rpc/taxasuggest.php",
				minLength: 2,
				autoFocus: true
			});
		});

		function verifyUserAddForm(f){
			if(f.uid.value == ""){
				alert("<?php echo (isset($LANG['SELECT_USER'])?$LANG['SELECT_USER']:'Select a User'); ?>");
				return false;
			}
			if(f.editorstatus.value == ""){
				alert("<?php echo (isset($LANG['SELECT_SCOPE'])?$LANG['SELECT_SCOPE']:'Select the Scope of Relationship'); ?>");
				return false;
			}
			if(f.taxoninput.value == ""){
				alert("<?php echo (isset($LANG['SELECT_TAXON'])?$LANG['SELECT_TAXON']:'Select the Taxonomic Name'); ?>");
				return false;
			}
			return true;
		}
	</script>
	<script type="text/javascript" src="../js/symb/shared.js"></script>
</head>
<body>
	<?php
	$displayLeftMenu = (isset($profile_usertaxonomymanagerMenu)?$profile_usertaxonomymanagerMenu:true);
	include($SERVER_ROOT.'/includes/header.php');
	if(isset($profile_usertaxonomymanagerCrumbs)){
		if($profile_usertaxonomymanagerCrumbs){
			echo "<div class='navpath'>";
			echo "<a href='../index.php'>Home</a> &gt;&gt; ";
			echo $profile_usertaxonomymanagerCrumbs;
			echo ' <b>'.(isset($LANG['TAX_PERMISSIONS'])?$LANG['TAX_PERMISSIONS']:'Taxonomic Interest User permissions').'</b>';
			echo '</div>';
		}
	}
	else{
		?>
		<div class='navpath'>
			<a href='../index.php'>Home</a> &gt;&gt;
			<b><?php echo (isset($LANG['TAX_PERMISSIONS'])?$LANG['TAX_PERMISSIONS']:'Taxonomic Interest User permissions'); ?></b>
		</div>
		<?php
	}

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
		<!-- This is inner text! -->
		<div id="innertext">
			<h2><?php echo (isset($LANG['TAX_PERMISSIONS'])?$LANG['TAX_PERMISSIONS']:'Taxonomic Interest User permissions'); ?></h2>
			<div style="float:right;" title="Add a new taxonomic relationship">
				<a href="#" onclick="toggle('addUserDiv')">
					<img style='border:0px;width:15px;' src='../images/add.png' alt='<?php echo (isset($LANG['ADD'])?$LANG['ADD']:'Add Button'); ?>'/>
				</a>
			</div>
			<div id="addUserDiv" style="display:none;">
				<fieldset style="padding:20px;">
					<legend><b><?php echo (isset($LANG['NEW_TAX_REL'])?$LANG['NEW_TAX_REL']:'New Taxonomic Relationship'); ?></b></legend>
					<form name="adduserform" action="usertaxonomymanager.php" method="post" onsubmit="return verifyUserAddForm(this)">
						<div style="margin:3px;">
							<b><?php echo (isset($LANG['USER'])?$LANG['USER']:'User'); ?></b><br/>
							<select name="uid">
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
							<b><?php echo (isset($LANG['TAXON'])?$LANG['TAXON']:'Taxon'); ?></b><br/>
							<input id="taxoninput" name="taxon" type="text" value="" style="width:90%;" />
						</div>
						<div style="margin:3px;">
							<b><?php echo (isset($LANG['SCOPE_REL'])?$LANG['SCOPE_REL']:'Scope of Relationship'); ?></b><br/>
							<select name="editorstatus">
								<option value="">----------------------------</option>
								<option value="OccurrenceEditor"><?php echo (isset($LANG['OCC_ID_EDITOR'])?$LANG['OCC_ID_EDITOR']:'Occurrence Identification Editor'); ?></option>
								<option value="RegionOfInterest"><?php echo (isset($LANG['REGION'])?$LANG['REGION']:'Region Of Interest'); ?></option>
								<option value="TaxonomicThesaurusEditor"><?php echo (isset($LANG['TAX_THES_EDITOR'])?$LANG['TAX_THES_EDITOR']:'Taxonomic Thesaurus Editor'); ?></option>
							</select>

						</div>
						<div style="margin:3px;">
							<b><?php echo (isset($LANG['SCOPE_LIMITS'])?$LANG['SCOPE_LIMITS']:'Geographic Scope Limits'); ?></b><br/>
							<input name="geographicscope" type="text" value="" style="width:90%;" />

						</div>
						<div style="margin:3px;">
							<b><?php echo (isset($LANG['NOTES'])?$LANG['NOTES']:'Notes'); ?></b><br/>
							<input name="notes" type="text" value="" style="width:90%;" />

						</div>
						<div style="margin:3px;">
							<button name="action" type="submit" value="Add Taxonomic Relationship"><?php echo (isset($LANG['ADD_TAX_REL'])?$LANG['ADD_TAX_REL']:'Add Taxonomic Relationship'); ?></button>
						</div>
					</form>
				</fieldset>
			</div>
			<div>
				<?php
				foreach($editorArr as $editorStatus => $userArr){
					$cat = 'Undefined';
					if($editorStatus == 'RegionOfInterest') $cat = (isset($LANG['REGION'])?$LANG['REGION']:'Region Of Interest');
					elseif($editorStatus == 'OccurrenceEditor') $cat = (isset($LANG['OCC_EDIT'])?$LANG['OCC_EDIT']:'Occurrence Editor');
					elseif($editorStatus == 'TaxonomicThesaurusEditor') $cat = (isset($LANG['TAX_THES'])?$LANG['TAX_THES']:'Taxonomic Thesaurus Editor');
					echo '<div><b class="underlined-text">'.$cat.'</b></div>';
					echo '<ul style="margin:10px;">';
					foreach($userArr as $uid => $uArr){
						$username = $uArr['username'];
						unset($uArr['username']);
						echo '<li>';
						echo '<b>'.$username.'</b>';
						$confirmStr = (isset($LANG['REMOVE_LINKS'])?$LANG['REMOVE_LINKS']:'Are you sure you want to remove all taxonomy links for this user?');
						$titleStr = (isset($LANG['DELETE_LINKS'])?$LANG['DELETE_LINKS']:'Delete all taxonomic relationships for this user');
						echo '<a href="usertaxonomymanager.php?delutid=all&deluid=' . htmlspecialchars($uid, HTML_SPECIAL_CHARS_FLAGS) . '&es=' . htmlspecialchars($editorStatus, HTML_SPECIAL_CHARS_FLAGS) . '" onclick="return confirm(\'' . htmlspecialchars($confirmStr, HTML_SPECIAL_CHARS_FLAGS) . '\'" title="' . htmlspecialchars($titleStr, HTML_SPECIAL_CHARS_FLAGS) . '">';
						echo '<img src="../images/drop.png" style="width:12px;" alt="' . (isset($LANG['DELETE_LINKS']) ? $LANG['DELETE_LINKS'] : 'Delete all taxonomic relationships for this user') . '" />';
						echo '</a>';
						foreach($uArr as $utid => $utArr){
							echo '<li style="margin-left:15px;">'.$utArr['sciname'];
							if($utArr['geoscope']) echo ' ('.$utArr['geoscope'].')';
							if($utArr['notes']) echo ': '.$utArr['notes'];
							$confirmStr2 = (isset($LANG['REMOVE_ONE_LINK'])?$LANG['REMOVE_ONE_LINK']:'Are you sure you want to remove this taxonomy link for this user?');
							$titleStr2 = (isset($LANG['DELETE_A_LINK'])?$LANG['DELETE_A_LINK']:'Delete this user taxonomic relationship');
							echo '<a href="usertaxonomymanager.php?delutid=' . htmlspecialchars($utid, HTML_SPECIAL_CHARS_FLAGS) . '" onclick="return confirm(\'' . htmlspecialchars($confirmStr2, HTML_SPECIAL_CHARS_FLAGS) . '\'" title="' . htmlspecialchars($titleStr2, HTML_SPECIAL_CHARS_FLAGS) . '">';
							echo '<img src="../images/drop.png" style="width:12px; alt="' . (isset($LANG['DELETE_LINKS']) ? $LANG['DELETE_LINKS'] : 'Delete all taxonomic relationships for this user') . '" />';
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
		echo '<div style="color:red;">'.(isset($LANG['NOT_AUTH'])?$LANG['NOT_AUTH']:'You are not authorized to access this page').'</div>';
	}
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>