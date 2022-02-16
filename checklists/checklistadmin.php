<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ChecklistAdmin.php');
include_once($SERVER_ROOT.'/content/lang/checklists/checklistadmin.'.$LANG_TAG.'.php');
header('Content-Type: text/html; charset='.$CHARSET);
if(!$SYMB_UID) header('Location: ../profile/index.php?refurl=../checklists/checklistadmin.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$clid = array_key_exists('clid',$_REQUEST)?$_REQUEST['clid']:0;
$pid = array_key_exists('pid',$_REQUEST)?$_REQUEST['pid']:'';
$tabIndex = array_key_exists('tabindex',$_REQUEST)?$_REQUEST['tabindex']:0;
$action = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';

$clManager = new ChecklistAdmin();
if(!$clid && isset($_POST['delclid'])) $clid = $_POST['delclid'];
$clManager->setClid($clid);

if($action == 'SubmitAdd'){
	//Anyone with a login can create a checklist
	$newClid = $clManager->createChecklist($_POST);
	header('Location: checklist.php?clid='.$newClid);
}

$statusStr = '';
$isEditor = 0;
if($IS_ADMIN || (array_key_exists('ClAdmin',$USER_RIGHTS) && in_array($clid,$USER_RIGHTS['ClAdmin']))){
	$isEditor = 1;

	//Submit checklist MetaData edits
	if($action == 'SubmitEdit'){
		$clManager->editMetaData($_POST);
		header('Location: checklist.php?clid='.$clid.'&pid='.$pid);
	}
	elseif($action == 'DeleteCheck'){
		$statusStr = $clManager->deleteChecklist($_POST['delclid']);
		if($statusStr === true) header('Location: ../index.php');
	}
	elseif($action == 'Addeditor'){
		$statusStr = $clManager->addEditor($_POST['editoruid']);
	}
	elseif(array_key_exists('deleteuid',$_REQUEST)){
		$statusStr = $clManager->deleteEditor($_REQUEST['deleteuid']);
	}
	elseif($action == 'AddToProject'){
		$statusStr = $clManager->addProject($_POST['pid']);
	}
	elseif($action == 'DeleteProject'){
		$statusStr = $clManager->deleteProject($_POST['pid']);
	}
	elseif($action == 'Add Point'){
		$statusStr = $clManager->addPoint($_POST['pointtid'],$_POST['pointlat'],$_POST['pointlng'],$_POST['notes']);
	}
	elseif($action && array_key_exists('clidadd',$_POST)){
		$statusStr = $clManager->addChildChecklist($_POST['clidadd']);
	}
	elseif($action && array_key_exists('cliddel',$_GET)){
		$statusStr = $clManager->deleteChildChecklist($_GET['cliddel']);
	}
}
$clArray = $clManager->getMetaData();
$defaultArr = array();
if($clArray['defaultsettings']){
	$defaultArr = json_decode($clArray['defaultsettings'], true);
}
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>"/>
	<title><?php echo $DEFAULT_TITLE.' - '.$LANG['CHECKADMIN'];?></title>
	<?php
	$activateJQuery = true;
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/jquery-ui.js"></script>
	<script type="text/javascript" src="../js/tinymce/tinymce.min.js"></script>
	<script type="text/javascript">
		var clid = <?php echo $clid; ?>;
		var tabIndex = <?php echo $tabIndex; ?>;

		tinymce.init({
			selector: "textarea",
			width: "100%",
			height: 300,
			menubar: false,
			plugins: "link,charmap,code,paste",
			toolbar : ["bold italic underline | cut copy paste | outdent indent | subscript superscript | undo redo removeformat | link | charmap | code"],
			default_link_target: "_blank",
			paste_as_text: true
		});

		function verifyAddUser(f){
			if(f.editoruid.value == ""){
				alert("<?php echo isset($LANG['SELECTUSER'])?$LANG['SELECTUSER']:'Select User'; ?>");
				return false;
			}
			return true;
		}

		function validateAddProjectForm(f){
			if(f.pid.value == ""){
				alert("<?php echo isset($LANG['SELECTPROJECT'])?$LANG['SELECTPROJECT']:'Select a project'; ?>");
				return false;
			}
			return true;
		}

	</script>
	<script type="text/javascript" src="../js/symb/shared.js"></script>
	<script type="text/javascript" src="../js/symb/checklists.checklistadmin.js?ver=2"></script>
	<style type="text/css">
		.tox-dialog { min-height: 400px }
		fieldset{ padding:15px; margin:40px 10px; }
		legend{ font-weight: bold; }
	</style>
</head>
<body>
<?php
//$HEADER_URL = '';
//if(isset($clArray['headerurl']) && $clArray['headerurl']) $HEADER_URL = $CLIENT_ROOT.$clArray['headerurl'];
$displayLeftMenu = false;
include($SERVER_ROOT.'/includes/header.php');
?>
<div class="navpath">
	<a href="../index.php"><?php echo $LANG['NAV_HOME'];?></a> &gt;&gt;
	<a href="checklist.php?clid=<?php echo $clid.'&pid='.$pid; ?>"><?php echo $LANG['RETURNCHECK'];?></a> &gt;&gt;
	<b><?php echo $LANG['CHECKADMIN']; ?></b>
</div>
<div id='innertext'>
	<div style="color:#990000;font-size:20px;font-weight:bold;margin:0px 10px 10px 0px;">
		<a href="checklist.php?clid=<?php echo $clid.'&pid='.$pid; ?>">
			<?php echo $clManager->getClName(); ?>
		</a>
	</div>
	<?php
	if($statusStr){
		?>
		<hr />
		<div style="margin:20px;font-weight:bold;color:red;">
			<?php echo $statusStr; ?>
		</div>
		<hr />
		<?php
	}

	if($clid && $isEditor){
		?>
		<div id="tabs" style="margin:10px;">
			<ul>
				<li><a href="#admintab"><span><?php echo $LANG['ADMIN'];?></span></a></li>
				<li><a href="checklistadminmeta.php?clid=<?php echo $clid.'&pid='.$pid; ?>"><span><?php echo $LANG['DESCRIPTION'];?></span></a></li>
				<!--					<li><a href="#pointtab"><span>Non-vouchered Points</span></a></li> -->
				<li><a href="checklistadminchildren.php?clid=<?php echo $clid.'&pid='.$pid; ?>"><span><?php echo $LANG['RELATEDCHECK'];?></span></a></li>
				<?php
				if($clManager->hasVoucherProjects()) echo '<li><a href="imgvouchertab.php?clid='.$clid.'">'.(isset($LANG['ADDIMGVOUCHER'])?$LANG['ADDIMGVOUCHER']:'Add Image Voucher').'</a></li>';
				?>
			</ul>
			<div id="admintab">
				<div style="margin:20px;">
					<div style="font-weight:bold;font-size:120%;"><?php echo $LANG['CURREDIT'];?></div>
					<?php
					$editorArr = $clManager->getEditors();
					if($editorArr){
						?>
						<ul>
							<?php
							foreach($editorArr as $uid => $uNameArr){
								?>
								<li>
									<?php echo '<span title="'.($uNameArr['assignedby']?'Assigned by '.$uNameArr['assignedby']:'').'">'.$uNameArr['name'].'</span>'; ?>
									<form name="delEditorForm-<?php echo $uid; ?>" action="checklistadmin.php" method="post" onclick="return confirm(<?php echo $LANG['REMOVEEDITPRIVCONFIRM']; ?>);" title="<?php echo $LANG['DELETETHISU'];?>" style="display:inline">
										<input name="clid" type="hidden" value="<?php echo $clid; ?>" />
										<input name="pid" type="hidden" value="<?php echo $pid; ?>" />
										<input name="deleteuid" type="hidden" value="<?php echo $uid; ?>" />
										<input name="submitaction" type="hidden" value="DeleteEditor" />
										<input name="submit" type="image" src="../images/drop.png" style="width:12px;" />
									</form>
								</li>
								<?php
							}
							?>
						</ul>
						<?php
					}
					else{
						echo "<div>".$LANG['NOEDITOR']."</div>\n";
					}
					?>
					<fieldset>
						<legend><?php echo (isset($LANG['ADDNEWUSER'])?$LANG['ADDNEWUSER']:'Add New User'); ?></legend>
						<form name="adduser" action="checklistadmin.php" method="post" onsubmit="return verifyAddUser(this)">
							<div>
								<select name="editoruid">
									<option value=""><?php echo (isset($LANG['SELECTUSER'])?$LANG['SELECTUSER']:'Select User'); ?></option>
									<option value="">------------------------------</option>
									<?php
									$userArr = $clManager->getUserList();
									foreach($userArr as $uid => $uName){
										echo '<option value="'.$uid.'">'.$uName.'</option>';
									}
									?>
								</select>
								<button name="submitaction" type="submit" value="Addeditor"><?php echo $LANG['ADDEDITOR'];?></button>
								<input type="hidden" name="pid" value="<?php echo $pid; ?>" />
								<input type="hidden" name="clid" value="<?php echo $clid; ?>" />
							</div>
						</form>
					</fieldset>
				</div>
				<hr/>
				<div style="margin:20px;">
					<div style="font-weight:bold;font-size:120%;"><?php echo (isset($LANG['INVENTORYPROJECTS'])?$LANG['INVENTORYPROJECTS']:'Inventory Project Assignments');?></div>
					<ul>
						<?php
						$projArr = $clManager->getInventoryProjects();
						if($projArr){
							foreach($projArr as $pid => $pName){
								?>
								<li>
									<a href="../projects/index.php?pid=<?php echo $pid; ?>"><?php echo $pName; ?></a>
									<?php
									if(isset($USER_RIGHTS['ProjAdmin']) && in_array($pid, $USER_RIGHTS['ProjAdmin'])){
										?>
										<form name="delProjectForm-<?php echo $pid; ?>" action="checklistadmin.php" method="post" onclick="return confirm(<?php echo $LANG['REMOVEPROJECTCONFIRM']; ?>);" title="<?php echo $LANG['REMOVEPROJECT'];?>" style="display:inline">
											<input name="clid" type="hidden" value="<?php echo $clid; ?>" />
											<input name="pid" type="hidden" value="<?php echo $pid; ?>" />
											<input name="submitaction" type="hidden" value="DeleteProject" />
											<input name="submit" type="image" src="../images/drop.png" style="width:12px;" />
										</form>
										<?php
									}
									?>
								</li>
								<?php
							}
						}
						else{
							echo '<li>'.(isset($LANG['CHECKNOTASSIGNED'])?$LANG['CHECKNOTASSIGNED']:'Checklist has not been assigned to any inventory projects').'</li>';
						}
						?>
					</ul>
					<?php
					if(array_key_exists('ProjAdmin',$USER_RIGHTS)){
						if($potentialProjects = array_diff_key($clManager->getPotentialProjects($USER_RIGHTS['ProjAdmin']),$projArr)){
							?>
							<fieldset>
								<legend><?php echo (isset($LANG['LINKTOPROJECT'])?$LANG['LINKTOPROJECT']:'Link to a Project'); ?></legend>
								<form name="addtoprojectform" action="checklistadmin.php" method="post" onsubmit="return validateAddProjectForm(this)">
									<select name="pid">
										<option value=""><?php echo (isset($LANG['SELECTPROJECT'])?$LANG['SELECTPROJECT']:'Select User'); ?></option>
										<option value="">---------------------------------</option>
										<?php
										foreach($potentialProjects as $pid => $pName){
											echo '<option value="'.$pid.'">'.$pName.'</option>';
										}
										?>
									</select>
									<input name="clid" type="hidden" value="<?php echo $clid; ?>" />
									<button name="submitaction" type="submit" value="AddToProject"><?php echo isset($LANG['SUBMIT'])?$LANG['SUBMIT']:'Submit';?></button>
								</form>
							</fieldset>
							<?php
						}
					}

					?>
				</div>
				<hr/>
				<div style="margin:20px;">
					<div style="font-weight:bold;font-size:120%;"><?php echo $LANG['PERMREMOVECHECK'];?></div>
					<div style="margin:10px;">
						<?php echo $LANG['REMOVEUSERCHECK'];?><br/>
						<b><?php echo $LANG['WARNINGNOUN'];?></b>
					</div>
					<div style="margin:15px;">
						<form name="deleteclform" action="checklistadmin.php" method="post" onsubmit="return window.confirm('<?php echo $LANG['CONFIRMDELETE'];?>')">
							<input name="delclid" type="hidden" value="<?php echo $clid; ?>" />
							<input name="submit" type="submit" value="<?php echo $LANG['DELETECHECK'];?>" <?php if($projArr || count($editorArr) > 1) echo 'DISABLED'; ?> />
							<input type="hidden" name="submitaction" value="DeleteCheck" />
						</form>
					</div>
				</div>
			</div>
			<!--
			<div id="pointtab">
				<fieldset>
					<legend>Add New Point</legend>
					<form name="pointaddform" target="checklistadmin.php" method="post" onsubmit="return verifyPointAddForm(this)">
						Taxon<br/>
						<select name="pointtid" onchange="togglePoint(this.form);">
							<option value="">Select Taxon</option>
							<option value="">-----------------------</option>
							<?php
							$taxaArr = $clManager->getTaxa();
							foreach($taxaArr as $tid => $sn){
								echo '<option value="'.$tid.'">'.$sn.'</option>';
							}
							?>
						</select>
						<div id="pointlldiv" style="display:none;">
							<div style="float:left;">
								Latitude Centroid<br/>
								<input id="latdec" type="text" name="pointlat" style="width:110px;" value="" />
							</div>
							<div style="float:left;margin-left:5px;">
								Longitude Centroid<br/>
								<input id="lngdec" type="text" name="pointlng" style="width:110px;" value="" />
							</div>
							<div style="float:left;margin:15px 0px 0px 10px;cursor:pointer;" onclick="openPointAid(<?php echo $clArray["latcentroid"].','.$clArray["longcentroid"]?>);">
								<img src="../images/world.png" style="width:12px;" />
							</div>
							<div style="clear:both;">
								Notes:<br/>
								<input type="text" name="notes" style="width:95%" value="" />
							</div>
							<div>
								<input name="submitaction" type="submit" value="Add Point" />
								<input type="hidden" name="tabindex" value="2" />
								<input type="hidden" name="pid" value="<?php echo $pid; ?>" />
								<input type="hidden" name="clid" value="<?php echo $clid; ?>" />
							</div>
						</div>
					</form>
				</fieldset>
			</div>
			-->
		</div>
		<?php
	}
	else{
		if(!$clid){
			echo '<div><span style="font-weight:bold;font-size:110%;">Error:</span>'.$LANG['IDNOTSET'].'</div>';
		}
		else{
			echo '<div><span style="font-weight:bold;font-size:110%;">Error:</span>'.$LANG['NOADMINPERM'].'</div>';
		}
	}
	?>
</div>
<?php
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>