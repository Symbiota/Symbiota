<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SpecUpload.php');
if($LANG_TAG == 'en' || !file_exists(include_once($SERVER_ROOT . '/content/lang/collections/admin/specuploadmanagement.' . $LANG_TAG . '.php')))
	include_once($SERVER_ROOT . '/content/lang/collections/admin/specuploadmanagement.en.php');
else include_once($SERVER_ROOT . '/content/lang/collections/admin/specuploadmanagement.' . $LANG_TAG . '.php');
header('Content-Type: text/html; charset=' . $CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/admin/specuploadmanagement.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = array_key_exists('collid',$_REQUEST) ? filter_var($_REQUEST['collid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$uspid = array_key_exists('uspid',$_REQUEST) ? filter_var($_REQUEST['uspid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';

$DIRECTUPLOAD = 1; $FILEUPLOAD = 3; $STOREDPROCEDURE = 4; $SCRIPTUPLOAD = 5; $DWCAUPLOAD = 6; $SKELETAL = 7; $IPTUPLOAD = 8; $NFNUPLOAD = 9; $SYMBIOTA = 13;

$duManager = new SpecUpload();

$duManager->setCollId($collid);
$duManager->setUspid($uspid);

$statusStr = '';
$isEditor = 0;
if($IS_ADMIN || (array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin']))){
	$isEditor = 1;
}
if($isEditor){
	if($action == 'saveEdits'){
		if($duManager->editUploadProfile($_POST)){
			$statusStr = $LANG['SUCCESS_IMP'];
		}
		else{
			$statusStr = $duManager->getErrorStr();
		}
		$action = '';
	}
	elseif($action == 'createProfile'){
		if($duManager->createUploadProfile($_POST)){
			$statusStr = $LANG['SUCCESS_UP'];
		}
		else{
			$statusStr = $duManager->getErrorStr();
		}
		$action = '';
	}
	elseif($action == 'Delete Profile'){
		if($duManager->deleteUploadProfile($uspid)){
			$statusStr = $LANG['SUCCESS_DEL'];
		}
		else{
			$statusStr = $duManager->getErrorStr();
		}
		$action = '';
	}
}
$duManager->readUploadParameters();
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET; ?>">
	<title><?= $DEFAULT_TITLE . ' ' . $LANG['UP_PROF_MAN'] ?></title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script>
		function checkUploadListForm(f){
			if(f.uspid.length == null){
				if(f.uspid.checked) return true;
			}
			else{
				var radioCnt = f.uspid.length;
				for(var counter = 0; counter < radioCnt; counter++){
					if (f.uspid[counter].checked) return true;
				}
			}
			alert("<?= $LANG['OPT_PLZ'] ?>");
			return false;
		}

		function checkParameterForm(f){
			if(f.title.value == ""){
				alert("<?= $LANG['TITLE_REQ'] ?>");
				return false;
			}
			else if(f.uploadtype.value == ""){
				alert("<?= $LANG['SELECT_TYPE'] ?>");
				return false;
			}
			else if(f.uploadtype.value == 8 || f.uploadtype.value == 13){
				if(f.path.value == ""){
					alert("<?= $LANG['SELECT_PATH'] ?>");
					return false;
				}
			}
			return true;
		}

		function adjustParameterForm(){
			//Hide all
			document.getElementById("platformDiv").style.display='none';
			document.getElementById("serverDiv").style.display='none';
			document.getElementById("portDiv").style.display='none';
			document.getElementById("codeDiv").style.display='none';
			document.getElementById("pathDiv").style.display='none';
			document.getElementById("pkfieldDiv").style.display='none';
			document.getElementById("usernameDiv").style.display='none';
			document.getElementById("passwordDiv").style.display='none';
			document.getElementById("schemanameDiv").style.display='none';
			document.getElementById("cleanupspDiv").style.display='none';
			document.getElementById("querystrDiv").style.display='none';
			document.getElementById("dwca_notes").style.display='none';
			//Then open according to upload type selection
			selValue = document.parameterform.uploadtype.value;
			if(selValue == 1){ //Direct Upload
				document.getElementById("platformDiv").style.display='block';
				document.getElementById("serverDiv").style.display='block';
				document.getElementById("portDiv").style.display='block';
				document.getElementById("usernameDiv").style.display='block';
				document.getElementById("passwordDiv").style.display='block';
				document.getElementById("schemanameDiv").style.display='block';
				document.getElementById("cleanupspDiv").style.display='block';
				document.getElementById("querystrDiv").style.display='block';
			}
			else if(selValue == 3){ //File Upload
				document.getElementById("cleanupspDiv").style.display='block';
			}
			else if(selValue == 4){ //Stored Procedure
				document.getElementById("cleanupspDiv").style.display='block';
				document.getElementById("querystrDiv").style.display='block';
			}
			else if(selValue == 5){ //Script Upload
				document.getElementById("cleanupspDiv").style.display='block';
				document.getElementById("querystrDiv").style.display='block';
			}
			else if(selValue == 6){ //Darwin Core Archive Manual Upload
				//document.getElementById("pathDiv").style.display='block';
				document.getElementById("cleanupspDiv").style.display='block';
			}
			else if(selValue == 7){ //Skeletal File Upload
				document.getElementById("cleanupspDiv").style.display='block';
			}
			else if(selValue == 8){ //IPT or DwC-A resource
				document.getElementById("pathDiv").style.display='block';
				document.getElementById("cleanupspDiv").style.display='block';
				document.getElementById("dwca_notes").style.display='block';
			}
			else if(selValue == 13){ //Symbiota resource
				document.getElementById("pathDiv").style.display='block';
				document.getElementById("cleanupspDiv").style.display='block';
				document.getElementById("dwca_notes").style.display='block';
			}
		}
	</script>
</head>
<body onload="<?php if($uspid && $action) echo 'adjustParameterForm()'; ?>">
<?php
include($SERVER_ROOT.'/includes/header.php');
?>
<div class="navpath">
	<a href="../../index.php"><?= $LANG['HOME'] ?></a> &gt;&gt;
	<a href="../misc/collprofiles.php?collid=<?= $collid ?>&emode=1"><?= $LANG['COL_MAN_PAN'] ?></a> &gt;&gt;
	<b><?= $LANG['SPEC_LOADER'] ?></b>
</div>
<!-- This is inner text! -->
<div role="main" id="innertext">
	<h1 class="page-heading"><?= $LANG['DAT_UP_MAN']; ?></h1>
	<?php
	if($statusStr){
		echo '<hr />';
		echo '<div>'.$statusStr.'</div>';
		echo '<hr />';
	}
	if($isEditor){
		if($collid){
			echo '<div style="font-weight:bold;font-size:130%;">'.$duManager->getCollInfo('name').'</div>';
			if($duManager->getCollInfo("uploaddate")) {
				echo '<div style="margin:0px 0px 15px 15px;"><b>Last Upload Date:</b> '.$duManager->getCollInfo('uploaddate').'</div>';
			}
			if(!$action){
			 	$profileList = $duManager->getUploadList();
				?>
				<form name="uploadlistform" action="specupload.php" method="post" onsubmit="return checkUploadListForm(this);">
					<fieldset>
						<legend style="font-weight:bold;font-size:120%;"><?= $LANG['UP_OPT'] ?></legend>
						<div style="float:right;">
							<?php
							echo '<a href="specuploadmanagement.php?collid=' . $collid . '&action=addprofile"><img src="' . $CLIENT_ROOT . '/images/add.png" style="width:1.5em;border:0px;" title="' . (isset($LANG['ADD_PROF']) ? $LANG['ADD_PROF'] : 'Add a New Upload Profile') . '" aria-label="' . (isset($LANG['ADD_PROF']) ? $LANG['ADD_PROF'] : 'Add a New Upload Profile') . '" /></a>';
							?>
						</div>
						<?php
						if($profileList){
						 	foreach($profileList as $id => $v){
						 		?>
						 		<div style="margin:10px;">
									<input type="radio" id="uspid-<?= $id ?>" name="uspid" value="<?= $id ?>" />
									<label for="uspid-<?php echo $id ?>"> <?php echo $v['title']; ?> </label>
									<a href="specuploadmanagement.php?action=editprofile&collid=<?= $collid . '&uspid=' . filter_var($id, FILTER_SANITIZE_NUMBER_INT); ?>" title="<?= $LANG['VIEW_PARS'] ?>" aria-label="<?= $LANG['VIEW_PARS'] ?>">
										<img src="../../images/edit.png" style="width:1.2em;" alt="<?= $LANG['IMG_EDIT'] ?>"/>
									</a>
								</div>
								<?php
						 	}
							?>
							<input type="hidden" name="collid" value="<?php echo $collid; ?>" />
							<div style="margin:10px;">
								<input type="submit" name="action" value="Initialize Upload..." />
							</div>
							<?php
						}
					 	else{
					 		?>
							<div style="padding:30px;">
								<?= $LANG['NO_PROFS'] ?>. <br />
								<?= $LANG['CLICK'] ?> <a href="specuploadmanagement.php?collid=<?= $collid ?>&action=addprofile"><?= $LANG['HERE'] ?></a> <?= $LANG['TO_ADD'] ?>.
							</div>
							<?php
					 	}
					 	 ?>
					</fieldset>
				</form>
				<hr />
				<?php
			}
			else{
		 		?>
				<div style="clear:both;">
					<fieldset>
						<legend><b><?= $LANG['UPLOAD_PARS'] ?></b></legend>
						<div style="float:right;">
							<?php
							echo '<a href="specuploadmanagement.php?collid=' . $collid . '">View All</a> ';
							?>
						</div>
						<form name="parameterform" action="specuploadmanagement.php" method="post" onsubmit="return checkParameterForm(this)">
							<div id="updatetypeDiv" style="">
								<b><?= $LANG['UP_TYPE'] ?>:</b>
								<select name="uploadtype" onchange="adjustParameterForm()" <?php if($uspid) echo 'DISABLED'; ?>>
									<option value=""><?= $LANG['SELECT_TYPE'] ?></option>
									<option value="">----------------------------------</option>
									<?php
									$uploadType = $duManager->getUploadType();
									echo '<option value="' . $DWCAUPLOAD . '" '.($uploadType==$DWCAUPLOAD ? 'SELECTED':'') . '>' . $LANG['MANUAL_DWCA']  . '</option>';
									echo '<option value="' . $IPTUPLOAD . '" '.($uploadType==$IPTUPLOAD ? 'SELECTED':'') . '>' . $LANG['IPT_DWCA'] . '</option>';
									echo '<option value="' . $SYMBIOTA . '" ' . ($uploadType==$SYMBIOTA ? 'SELECTED':'') . '>' . $LANG['SYMBIOTA_DWCA'] . '</option>';
									echo '<option value="' . $FILEUPLOAD . '" ' . ($uploadType==$FILEUPLOAD ? 'SELECTED' : '') . '>' . $LANG['FILE'] . '</option>';
									echo '<option value="' . $SKELETAL . '" ' . ($uploadType==$SKELETAL ? 'SELECTED':'') . '>' . $LANG['SKELETAL_FILE'] . '</option>';
									echo '<option value="' . $NFNUPLOAD . '" ' . ($uploadType==$NFNUPLOAD ? 'SELECTED':'') . '>' . $LANG['NFN_UPLOAD'] . '</option>';
									echo '<option value="">......................................</option>';
									echo '<option value="' . $DIRECTUPLOAD . '" ' . ($uploadType==$DIRECTUPLOAD ? 'SELECTED':'') . '>' . $LANG['DIRECT_DB'] . '</option>';
									echo '<option value="' . $STOREDPROCEDURE . '" ' . ($uploadType==$STOREDPROCEDURE ? 'SELECTED':'') . '>' . $LANG['STORED_PROC'] . '</option>';
									echo '<option value="' . $SCRIPTUPLOAD . '" ' . ($uploadType==$SCRIPTUPLOAD ? 'SELECTED':'') . '>' . $LANG['SCRIPT_UP'] . '</option>';
									?>
								</select>
							</div>
							<div id="titleDiv" style="">
								<b><?= $LANG['TITLE'] ?>:</b>
								<input name="title" type="text" value="<?php echo $duManager->getTitle(); ?>" style="width:400px;" maxlength="45" />
							</div>
							<div id="platformDiv" style="display:none">
								<b><?= $LANG['DB_PLATFORM'] ?>:</b>
								<select name="platform">
									<option value=""><?= $LANG['NONE_SEL'] ?></option>
									<option value="">--------------------------------------------</option>
									<option value="mysql" <?php echo ($duManager->getPlatform()=='mysql'?'SELECTED':''); ?>><?= $LANG['MYSQL'] ?></option>
								</select>
							</div>
							<div id="serverDiv" style="display:none">
								<b><?= $LANG['SERVER'] ?>:</b>
								<input name="server" type="text" size="50" value="<?php echo $duManager->getServer(); ?>" style="width:400px;" />
							</div>
							<div id="portDiv" style="display:none">
								<b><?= $LANG['PORT'] ?>:</b>
								<input name="port" type="text" value="<?php echo $duManager->getPort(); ?>" />
							</div>
							<div id="pathDiv" style="display:none">
								<b><?= $LANG['PATH'] ?>:</b>
								<input name="path" type="text" size="50" value="<?php echo $duManager->getPath(); ?>" style="width:700px;" />
							</div>
							<div id="codeDiv" style="display:none">
								<b><?= $LANG['CODE'] ?>:</b>
								<input name="code" type="text" value="<?php echo $duManager->getCode(); ?>" />
							</div>
							<div id="pkfieldDiv" style="display:none">
								<b><?= $LANG['PRIMARY_KEY'] ?>:</b>
								<input name="pkfield" type="text" value="<?php echo $duManager->getPKField(); ?>" />
							</div>
							<div id="usernameDiv" style="display:none">
								<b><?= $LANG['USERNAME'] ?>:</b>
								<input name="username" type="text" value="<?php echo $duManager->getUsername(); ?>" />
							</div>
							<div id="passwordDiv" style="display:none">
								<b><?= $LANG['PWORD'] ?>:</b>
								<input name="password" type="text" value="<?php echo $duManager->getPassword(); ?>" />
							</div>
							<div id="schemanameDiv" style="display:none">
								<b><?= $LANG['SCHEMA'] ?>:</b>
								<input name="schemaname" type="text" size="65" value="<?php echo $duManager->getSchemaName(); ?>" />
							</div>
							<div id="cleanupspDiv" style="display:none">
								<b><?= $LANG['STORED_PROC'] ?>:</b>
								<input name="cleanupsp" type="text" size="40" value="<?php echo $duManager->getStoredProcedure(); ?>" style="width:400px;" />
							</div>
							<div id="querystrDiv" style="display:none">
								<b><?= $LANG['QUERY'] ?>: </b><br/>
								<textarea name="querystr" cols="75" rows="6" ><?= $duManager->getQueryStr() ?></textarea>
							</div>
							<div style="margin:15px">
								<input type="hidden" name="uspid" value="<?= $uspid ?>" />
								<input type="hidden" name="collid" value="<?= $collid ?>" />
								<?php
								if($uspid){
									?>
									<button type="submit" name="action" value="saveEdits"><?= $LANG['SAVE_PROFILE'] ?></button>
									<?php
								}
								else{
									?>
									<button type="submit" name="action" value="createProfile"><?= $LANG['CREATE_PROFILE'] ?></button>
									<?php
								}
								?>
							</div>
							<div id="dwca_notes" style="display: none">
								<?= '* ' . $LANG['PATH_EXPLAIN'] ?>
							</div>
						</form>
					</fieldset>
				</div>
				<?php
				if($uspid){
					?>
					<form action="specuploadmanagement.php" method="post" onsubmit="return confirm('<?= $LANG['VERIFY_DEL'] ?>')">
						<fieldset>
							<legend><b><?= $LANG['DEL_PROFILE'] ?></b></legend>
							<div>
								<input type="hidden" name="uspid" value="<?= $uspid ?>" />
								<input type="hidden" name="collid" value="<?= $collid ?>" />
								<button type="submit" name="action" value="Delete Profile" ><?= $LANG['DEL_PR'] ?></button>
							</div>
						</fieldset>
					</form>
					<?php
				}
		 	}
		}
		else{
			echo '<div style="font-weight:bold;font-size:120%;">ERROR: collection identifier not set</div>';
		}
	}
	else{
		?>
		<div style="font-weight:bold;font-size:120%;">
			<? $LANG['ERROR_AUTH'] ?>
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
