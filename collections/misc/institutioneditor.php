<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/InstitutionManager.php');

if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/collections/misc/institutioneditor.' . $LANG_TAG . '.php')) include_once($SERVER_ROOT.'/content/lang/collections/misc/institutioneditor.' . $LANG_TAG . '.php');
else include_once($SERVER_ROOT . '/content/lang/collections/misc/institutioneditor.en.php');

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/admin/institutioneditor.php?' . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$iid = array_key_exists('iid', $_REQUEST) ? filter_var($_REQUEST['iid'], FILTER_SANITIZE_NUMBER_INT) : '';
$targetCollid = array_key_exists('targetcollid', $_REQUEST) ? filter_var($_REQUEST['targetcollid'], FILTER_SANITIZE_NUMBER_INT) : '';
$eMode = !empty($_REQUEST['emode']) ? 1 : 0;
$instCodeDefault = array_key_exists('instcode',$_REQUEST) ? $_REQUEST['instcode'] : '';
$formSubmit = array_key_exists('formsubmit', $_POST) ? $_POST['formsubmit'] : '';

$instManager = new InstitutionManager();
$fullCollList = $instManager->getCollectionList();
$instManager->setInstitutionId($iid);
if(!$iid) $eMode = 1;

//Create a list of collection that are linked to this institutions
$collList = array();
foreach($fullCollList as $k => $v){
	if($v['iid'] == $iid) $collList[$k] = $v['name'];
}

$editorCode = 0;
if($IS_ADMIN){
	$editorCode = 3;
}
elseif(array_key_exists('CollAdmin', $USER_RIGHTS)){
	$editorCode = 1;
	if($collList && array_intersect($USER_RIGHTS['CollAdmin'], array_keys($collList))){
		$editorCode = 2;
	}
}
$statusStr = '';
if($editorCode){
	if($formSubmit == 'Add Institution'){
		if($instManager->insertInstitution($_POST)){
			$iid = $instManager->getInstitutionId();
			$statusStr = 'SUCCESS, institution added!';
			if($targetCollid) header('Location: ../misc/collprofiles.php?collid=' . $targetCollid);
		}
		else{
			$statusStr = 'ERROR creating institution: ' . $instManager->getErrorMessage();
		}
	}
	else{
		if($editorCode > 1){
			if($formSubmit == 'Update Institution Address'){
				if($instManager->updateInstitution($_POST)){
					if($targetCollid) header('Location: ../misc/collprofiles.php?collid=' . $targetCollid);
				}
				else{
					$statusStr = 'ERROR updating institutions record: ' . $instManager->getErrorMessage();
				}
			}
			elseif(!empty($_POST['deliid'])){
				if($instManager->deleteInstitution($_POST['deliid'])){
					$statusStr = 'SUCCESS! Institution deleted.';
					$iid = 0;
				}
				else{
					$statusStr = 'Unable to delete: ';
					$errorStr = $instManager->getErrorMessage();
					if($errorStr == 'LINKED_COLLECTIONS'){
						$statusStr .= 'following collections need to be unlinked before deletion is allowed';
						$statusStr .= '<ul><li>' . implode('</li><li>', $instManager->getWarningArr()) . '</li></ul>';
					}
					elseif($errorStr == 'LINKED_LOANS'){
						$statusStr .= 'institution is linked to ' . count($instManager->getWarningArr()) . ' loans';
					}
					else{
						$errorStr = 'ERROR deleting institution: ' . $errorStr;
					}
				}
			}
			elseif($formSubmit == 'Add Collection'){
				if($_POST['addcollid'] && is_numeric($_POST['addcollid'])){
					if($instManager->updateCollectionLink($_POST['addcollid'], $iid)){
						$collList[$_POST['addcollid']] = $fullCollList[$_POST['addcollid']]['name'];
					}
					else{
						$statusStr = 'ERROR linking collection to institution: ' . $instManager->getErrorMessage();
					}
				}
			}
			elseif(isset($_GET['removecollid'])){
				if($instManager->updateCollectionLink($_GET['removecollid'], null)){
					$statusStr = 'SUCCESS! Institution removed';
					unset($collList[$_GET['removecollid']]);
				}
				else{
					$statusStr = 'ERROR deleting institution: ' . $instManager->getErrorMessage();
				}
			}
		}
	}
}
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET; ?>">
	<title><?= $DEFAULT_TITLE; ?> <?= $LANG['INSTITUTION_EDITOR']; ?></title>
	<link href="<?= $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="<?= $CLIENT_ROOT; ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT; ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script src="../../js/symb/collections.grscicoll.js?ver=2" type="text/javascript"></script>
	<script>
		function toggle(target){
			var tDiv = document.getElementById(target);
			if(tDiv != null){
				if(tDiv.style.display=="none"){
					tDiv.style.display="block";
				}
				else {
					tDiv.style.display="none";
				}
			}
			else{
				var divs = document.getElementsByTagName("div");
				for (var i = 0; i < divs.length; i++) {
				var divObj = divs[i];
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
		}

		function validateAddCollectionForm(f){
			if(f.addcollid.value == ""){
				alert("<?= $LANG['SELECT_COLLECTION']; ?>");
				return false;
			}
			return true;
		}
	</script>
	<style>
		label-div{ float:left; width:155px; font-weight:bold; }
	</style>
</head>
<body>
<?php
include($SERVER_ROOT.'/includes/header.php');
?>
<div class='navpath'>
	<a href='../../index.php'><?= $LANG['HOME']; ?></a> &gt;&gt;
	<?php
	if($targetCollid && !empty($collList[$targetCollid])){
		echo '<a href="../misc/collprofiles.php?collid=' . $targetCollid . '&emode=1">' . $collList[$targetCollid] . ' ' . $LANG['MANAGEMENT'] . '</a> &gt;&gt;';
	}
	else{
		echo '<a href="institutioneditor.php">' . $LANG['FULL_ADDRESS_LIST'] . '</a> &gt;&gt;';
	}
	?>
	<b><?= $LANG['INSTITUTION_EDITOR']; ?></b>
</div>
<!-- This is inner text! -->
<div role="main" id="innertext">
	<h1 class="page-heading"><?= $LANG['INSTITUTION_EDITOR']; ?></h1>
	<div id="dialog" title="" style="display: none;">
		<div id="dialogmsg"></div>
		<select id="getresult">
		</select>
	</div>
	<?php
	if($statusStr){
		?>
		<hr />
		<div style="margin:20px;color:<?= (substr($statusStr,0,5)=='ERROR'?'red':'green'); ?>;">
			<?= htmlspecialchars($statusStr, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) ?>
		</div>
		<hr />
		<?php
	}
	$instArr = $instManager->getInstitutionData();
	?>
	<div style="float:right;">
		<?php
		if($iid){
			?>
			<a href="#" onclick="toggle('instadddiv');">
				<img src="<?= $CLIENT_ROOT;?>/images/add.png" style="width:1.5em;border:0px;" title="<?= $LANG['ADD_NEW_INST']; ?>" />
			</a>
			<?php
		}
		else{
			?>
			<a href="institutioneditor.php">
				<img src="<?= $CLIENT_ROOT;?>/images/toparent.png" style="width:1.2em;border:0px;" title="<?= $LANG['RETURN_TO_INST']; ?>" />
			</a>
			<?php
			if($editorCode > 1){
				?>
				<a href="#" onclick="toggle('editdiv');">
					<img src="<?= $CLIENT_ROOT;?>/images/edit.png" style="width:1.2em;border:0px;" title="<?= $LANG['EDIT_INST']; ?>" />
				</a>
				<?php
			}
		}
		?>
	</div>
	<div style="clear:both;">
		<form id="insteditform" name="insteditform" action="institutioneditor.php" method="post">
			<fieldset style="padding:20px;">
				<legend><b><?= ($iid ? $LANG['ADDRESS_DETAILS'] : $LANG['ADD_NEW_INSTITUTION']) ?></b></legend>
				<div style="position:relative;">
					<div class="label-div">
						<?= $LANG['INSTITUTION_CODE']; ?>:
					</div>
					<div class="editdiv" style="display:<?= $eMode?'none':'block'; ?>;">
						<?= $instArr['institutioncode']; ?>
					</div>
					<div class="editdiv" style="display:<?= $eMode?'block':'none'; ?>;">
						<input name="institutioncode" type="text" value="<?= $instArr['institutioncode']; ?>" />
						<button name="getgrscicoll" type="button" value="Update from GrSciColl" style="display:inline;" onClick="grscicoll()"><?= ($iid ? $LANG['UPDATE_GRSCICOLL'] : $LANG['GET_DATA_FROM_GRSCICOLL']) ?></button>
					</div>
				</div>
				<div style="position:relative;clear:both;">
					<div class="label-div">
						<?= $LANG['INSTITUTION_NAME']; ?>:
					</div>
					<div class="editdiv" style="display:<?= $eMode?'none':'block'; ?>;">
						<?= $instArr['institutionname']; ?>
					</div>
					<div class="editdiv" style="display:<?= $eMode?'block':'none'; ?>;">
						<input name="institutionname" type="text" value="<?= $instArr['institutionname']; ?>" style="width:400px;" />
					</div>
				</div>
				<div style="position:relative;clear:both;">
					<div class="label-div">
						<?= $LANG['INSTITUTION_NAME_TWO']; ?>:
					</div>
					<div class="editdiv" style="display:<?= $eMode?'none':'block'; ?>;">
						<?= $instArr['institutionname2']; ?>
					</div>
					<div class="editdiv" style="display:<?= $eMode?'block':'none'; ?>;">
						<input name="institutionname2" type="text" value="<?= $instArr['institutionname2']; ?>" style="width:400px;" />
					</div>
				</div>
				<div style="position:relative;clear:both;">
					<div class="label-div">
						<?= $LANG['ADDRESS']; ?>:
					</div>
					<div class="editdiv" style="display:<?= $eMode?'none':'block'; ?>;">
						<?= $instArr['address1']; ?>
					</div>
					<div class="editdiv" style="display:<?= $eMode?'block':'none'; ?>;">
						<input name="address1" type="text" value="<?= $instArr['address1']; ?>" style="width:400px;" />
					</div>
				</div>
				<div style="position:relative;clear:both;">
					<div class="label-div">
						<?= $LANG['ADDRESS_TWO']; ?>:
					</div>
					<div class="editdiv" style="display:<?= $eMode?'none':'block'; ?>;">
						<?= $instArr['address2']; ?>
					</div>
					<div class="editdiv" style="display:<?= $eMode?'block':'none'; ?>;">
						<input name="address2" type="text" value="<?= $instArr['address2']; ?>" style="width:400px;" />
					</div>
				</div>
				<div style="position:relative;clear:both;">
					<div class="label-div">
						<?= $LANG['CITY']; ?>City:
					</div>
					<div class="editdiv" style="display:<?= $eMode?'none':'block'; ?>;">
						<?= $instArr['city']; ?>
					</div>
					<div class="editdiv" style="display:<?= $eMode?'block':'none'; ?>;">
						<input name="city" type="text" value="<?= $instArr['city']; ?>" style="width:100px;" />
					</div>
				</div>
				<div style="position:relative;clear:both;">
					<div class="label-div">
						<?= $LANG['STATE_PROVINCE']; ?>:
					</div>
					<div class="editdiv" style="display:<?= $eMode?'none':'block'; ?>;">
						<?= $instArr['stateprovince']; ?>
					</div>
					<div class="editdiv" style="display:<?= $eMode?'block':'none'; ?>;">
						<input name="stateprovince" type="text" value="<?= $instArr['stateprovince']; ?>" style="width:100px;" />
					</div>
				</div>
				<div style="position:relative;clear:both;">
					<div class="label-div">
						<?= $LANG['POSTAL_CODE']; ?>:
					</div>
					<div class="editdiv" style="display:<?= $eMode?'none':'block'; ?>;">
						<?= $instArr['postalcode']; ?>
					</div>
					<div class="editdiv" style="display:<?= $eMode?'block':'none'; ?>;">
						<input name="postalcode" type="text" value="<?= $instArr['postalcode']; ?>" />
					</div>
				</div>
				<div style="position:relative;clear:both;">
					<div class="label-div">
						<?= $LANG['COUNTRY']; ?>:
					</div>
					<div class="editdiv" style="display:<?= $eMode?'none':'block'; ?>;">
						<?= $instArr['country']; ?>
					</div>
					<div class="editdiv" style="display:<?= $eMode?'block':'none'; ?>;">
						<input name="country" type="text" value="<?= $instArr['country']; ?>" />
					</div>
				</div>
				<div style="position:relative;clear:both;">
					<div class="label-div">
						<?= $LANG['PHONE']; ?>:
					</div>
					<div class="editdiv" style="display:<?= $eMode?'none':'block'; ?>;">
						<?= $instArr['phone']; ?>
					</div>
					<div class="editdiv" style="display:<?= $eMode?'block':'none'; ?>;">
						<input name="phone" type="text" value="<?= $instArr['phone']; ?>" />
					</div>
				</div>
				<div style="position:relative;clear:both;">
					<div class="label-div">
						<?= $LANG['CONTACT']; ?>:
					</div>
					<div class="editdiv" style="display:<?= $eMode?'none':'block'; ?>;">
						<?= $instArr['contact']; ?>
					</div>
					<div class="editdiv" style="display:<?= $eMode?'block':'none'; ?>;">
						<input name="contact" type="text" value="<?= $instArr['contact']; ?>" />
					</div>
				</div>
				<div style="position:relative;clear:both;">
					<div class="label-div">
						<?= $LANG['EMAIL']; ?>:
					</div>
					<div class="editdiv" style="display:<?= $eMode?'none':'block'; ?>;">
						<?= $instArr['email']; ?>
					</div>
					<div class="editdiv" style="display:<?= $eMode?'block':'none'; ?>;">
						<input name="email" type="text" value="<?= $instArr['email']; ?>" style="width:150px" />
					</div>
				</div>
				<div style="position:relative;clear:both;">
					<div class="label-div">
						<?= $LANG['URL']; ?>:
					</div>
					<div class="editdiv" style="display:<?= $eMode?'none':'block'; ?>;">
						<a href="<?= $instArr['url']; ?>" target="_blank">
							<?= $instArr['url']; ?>
						</a>
					</div>
					<div class="editdiv" style="display:<?= $eMode?'block':'none'; ?>;">
						<input name="url" type="text" value="<?= $instArr['url']; ?>" style="width:400px" />
					</div>
				</div>
				<div style="position:relative;clear:both;">
					<div class="label-div">
						<?= $LANG['NOTES']; ?>:
					</div>
					<div class="editdiv" style="display:<?= $eMode?'none':'block'; ?>;">
						<?= $instArr['notes']; ?>
					</div>
					<div class="editdiv" style="display:<?= $eMode?'block':'none'; ?>;">
						<input name="notes" type="text" value="<?= $instArr['notes']; ?>" style="width:400px" />
					</div>
				</div>
				<?php
				if(!$iid){
					?>
					<div style="position:relative;clear:both;">
						<div class="label-div">
							<?= $LANG['LINK_TO']; ?>:
						</div>
						<div>
							<select name="targetcollid" style="width:400px;">
								<option value=""><?= $LANG['LEAVE_ORPHANED']; ?></option>
								<option value="">--------------------------------------</option>
								<?php
								foreach($fullCollList as $collid => $collArr){
									//Don't show collection that already linked and only show one that user can admin
									if($collArr['iid'] && ($IS_ADMIN || ($USER_RIGHTS["CollAdmin"] && in_array($collid,$USER_RIGHTS["CollAdmin"])))){
										echo '<option value="'.$collid.'"'.($collid == $targetCollid?'SELECTED':'').'>'.$collArr['name'].'</option>';
									}
								}
								?>
							</select>
						</div>
					</div>
					<?php
				}
				?>
				<div class="editdiv" style="display:<?= $eMode?'block':'none'; ?>;clear:both;margin:30px 0px 0px 20px;">
					<?php
					if($iid){
						?>
						<button name="formsubmit" type="submit" value="Update Institution Address" ><?= $LANG['UPDATE_INST_ADDRESS']; ?></button>
						<input name="iid" type="hidden" value="<?= $iid; ?>" />
						<input name="targetcollid" type="hidden" value="<?= $targetCollid; ?>" />
						<?php
					}
					else{
						?>
						<button name="formsubmit" type="submit" value="Add Institution" ><?= $LANG['ADD_INST']; ?></button>
						<?php
					}
					?>
				</div>
			</fieldset>
		</form>
	</div>
	<?php
	if($iid){
		?>
		<div style="clear:both;">
			<fieldset style="padding:20px;">
				<legend><b><?= $LANG['COLL_LINKED_TO _INST_ADDRESS']; ?></b></legend>
				<div>
					<?php
					if($collList){
						foreach($collList as $id => $collName){
							echo '<div style="margin:5px;font-weight:bold;clear:both;height:15px;">';
							echo '<div style="float:left;"><a href="../misc/collprofiles.php?collid=' . $id . '">' . $collName . '</a></div> ';
							if($editorCode == 3 || in_array($id,$USER_RIGHTS["CollAdmin"]))
								echo ' <div class="editdiv" style="margin-left:10px;display:'.($eMode?'':'none').'"><a href="institutioneditor.php?iid=' . $iid . '&removecollid=' . $id . '"><img src="../../images/del.png" style="width:1em;"/></a></div>';
							echo '</div>';
						}
					}
					else{
						echo '<div style="margin:25px;"><b>' . $LANG['INST_NOT_LINKED'] . '</b></div>';
					}
					?>
				</div>
				<div class="editdiv" style="display:<?= $eMode?'block':'none'; ?>;">
					<div style="margin:15px;clear:both;">* <?= $LANG['CLICK_ON_RED']; ?></div>
					<?php
					//Don't show collection that already linked and only show one that user can admin
					$addList = array();
					foreach($fullCollList as $collid => $collArr){
						if($collArr['iid'] != $iid){
							if($IS_ADMIN || (isset($USER_RIGHTS["CollAdmin"]) && in_array($collid,$USER_RIGHTS["CollAdmin"]))){
								$addList[$collid] = $collArr;
							}
						}
					}
					if($addList){
						?>
						<hr />
						<form name="addcollectionform" method="post" action="institutioneditor.php" onsubmit="return validateAddCollectionForm(this)">
							<select name="addcollid" style="width:400px;">
								<option value=""><?= $LANG['SELECT_COLL_TO_ADD']; ?></option>
								<option value="">------------------------------------</option>
								<?php
								foreach($addList as $collid => $collArr){
									echo '<option value="'.$collid.'">'.$collArr['name'].'</option>';
								}
								?>
							</select>
							<input name="iid" type="hidden" value="<?= $iid; ?>" />
							<button name="formsubmit" type="submit" value="Add Collection" ><?= $LANG['ADD_COLLECTION']; ?></button>
						</form>
						<?php
					}
					?>
				</div>
			</fieldset>
			<div class="editdiv" style="display:<?= $eMode?'block':'none'; ?>;">
				<fieldset style="padding:20px;">
					<legend><b><?= $LANG['DEL_INSTITUTION']; ?></b></legend>
					<form name="instdelform" action="institutioneditor.php" method="post" onsubmit="return confirm('<?= $LANG['WANT_TO_DELETE_INST']; ?>')">
						<div style="position:relative;clear:both;">
							<button class="button-danger" name="formsubmit" type="submit" value="Delete Institution" <?php if($collList) echo 'disabled'; ?> ><?= $LANG['DEL_INSTITUTION']; ?></button>
							<input name="deliid" type="hidden" value="<?= $iid; ?>" />
							<?php
							if($collList) echo '<div style="margin:15px;color:red;">' . $LANG['DELETION_OF_ADDRESS'] . '</div>';
							?>
						</div>
					</form>
				</fieldset>
			</div>
		</div>
		<?php
	}
	elseif(!$eMode){
		?>
		<div style="padding-left:10px;">
			<h2><?= $LANG['SELECT_INST_FROM_LIST'] ?></h2>
			<ul>
				<?php
				$instList = $instManager->getInstitutionList();
				if($instList){
					foreach($instList as $iid => $iArr){
						echo '<li><a href="institutioneditor.php?iid='.$iid.'">';
						echo $iArr['institutionname'].' ('.$iArr['institutioncode'].')';
						if($editorCode == 3 || array_intersect(explode(',',$iArr['collid']),$USER_RIGHTS["CollAdmin"])){
							echo ' <a href="institutioneditor.php?emode=1&iid=' . $iid . '"><img src="' . $CLIENT_ROOT . '/images/edit.png" style="width:1.2em;" /></a>';
						}
						echo '</a></li>';
					}
				}
				else{
					echo "<div>" . $LANG['NO_RIGHTS_TO_EDIT_INST'] . "</div>";
				}
				?>
			</ul>
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
