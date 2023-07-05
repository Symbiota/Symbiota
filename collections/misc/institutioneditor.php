<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/InstitutionManager.php');
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/admin/institutioneditor.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$iid = array_key_exists("iid",$_REQUEST)?$_REQUEST["iid"]:0;
$targetCollid = array_key_exists("targetcollid",$_REQUEST)?$_REQUEST["targetcollid"]:0;
$eMode = array_key_exists("emode",$_REQUEST)?$_REQUEST["emode"]:0;
$instCodeDefault = array_key_exists("instcode",$_REQUEST)?$_REQUEST["instcode"]:'';
$formSubmit = array_key_exists("formsubmit",$_POST)?$_POST["formsubmit"]:"";

$instManager = new InstitutionManager();
$fullCollList = $instManager->getCollectionList();
if($iid){
	$instManager->setInstitutionId($iid);
}
//Get list of collection that are linked to this institutions
$collList = array();
foreach($fullCollList as $k => $v){
	if($v['iid'] == $iid) $collList[$k] = $v['name'];
}

$editorCode = 0;
$statusStr = '';
if($IS_ADMIN){
	$editorCode = 3;
}
elseif(array_key_exists("CollAdmin",$USER_RIGHTS)){
	$editorCode = 1;
	if($collList && array_intersect($USER_RIGHTS["CollAdmin"],array_keys($collList))){
		$editorCode = 2;
	}
}
if($editorCode){
	if($formSubmit == "Add Institution"){
		$iid = $instManager->submitInstitutionAdd($_POST);
		if($iid){
			if($targetCollid) header('Location: ../misc/collprofiles.php?collid='.$targetCollid);
			$statusStr = 'SUCCESS! Institution added.';
		}
		else{
			$statusStr = $instManager->getErrorStr();
		}
	}
	else{
		if($editorCode > 1){
			if($formSubmit == "Update Institution Address"){
				if($instManager->submitInstitutionEdits($_POST)){
					if($targetCollid) header('Location: ../misc/collprofiles.php?collid='.$targetCollid);
				}
				else{
					$statusStr = $instManager->getErrorStr();
				}
			}
			elseif(isset($_POST['deliid'])){
				$delIid = $_POST['deliid'];
				if($instManager->deleteInstitution($delIid)){
					$statusStr = 'SUCCESS! Institution deleted.';
					$iid = 0;
				}
				else{
					$statusStr = $instManager->getErrorStr();
				}
			}
			elseif($formSubmit == "Add Collection"){
				if($instManager->addCollection($_POST['addcollid'],$iid)){
					$collList[$_POST['addcollid']] = $fullCollList[$_POST['addcollid']]['name'];
				}
				else{
					$statusStr = $instManager->getErrorStr();
				}
			}
			elseif(isset($_GET['removecollid'])){
				if($instManager->removeCollection($_GET['removecollid'])){
					$statusStr = 'SUCCESS! Institution removed';
					unset($collList[$_GET['removecollid']]);
				}
				else{
					$statusStr = $instManager->getErrorStr();
				}
			}
		}
	}
}
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title><?php echo $DEFAULT_TITLE; ?> Institution Editor</title>
	<link href="<?php echo htmlspecialchars($CSS_BASE_PATH, HTML_SPECIAL_CHARS_FLAGS); ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="../../js/jquery.js?ver=140310" type="text/javascript"></script>
	<script src="../../js/jquery-ui.js?ver=140310" type="text/javascript"></script>
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
				alert("Select a collection to be added");
				return false;
			}
			return true;
		}

	</script>
</head>
<body>
<?php
$displayLeftMenu = (isset($collections_admin_institutioneditor)?$collections_admin_institutioneditor:true);
include($SERVER_ROOT.'/includes/header.php');
?>
<div class='navpath'>
	<a href='../../index.php'>Home</a> &gt;&gt;
	<?php
	if(!$targetCollid && count($collList) == 1){
		$targetCollid = key($collList);
	}
	if($targetCollid){
		echo '<a href="../misc/collprofiles.php?collid=' . htmlspecialchars($targetCollid, HTML_SPECIAL_CHARS_FLAGS) . '&emode=1">' . htmlspecialchars($collList[$targetCollid], HTML_SPECIAL_CHARS_FLAGS) . ' Management</a> &gt;&gt;';
	}
	else{
		echo '<a href="institutioneditor.php">Full Address List</a> &gt;&gt;';
	}
	?>
	<b>Institution Editor</b>
</div>
<!-- This is inner text! -->
<div id="innertext">
	<div id="dialog" title="" style="display: none;">
		<div id="dialogmsg"></div>
		<select id="getresult">
		</select>
	</div>
	<?php
	if($statusStr){
		?>
		<hr />
		<div style="margin:20px;color:<?php echo (substr($statusStr,0,5)=='ERROR'?'red':'green'); ?>;">
			<?php echo $statusStr; ?>
		</div>
		<hr />
		<?php
	}
	if($iid){
		if($instArr = $instManager->getInstitutionData()){
			?>
			<div style="float:right;">
				<a href="institutioneditor.php">
					<img src="<?php echo $CLIENT_ROOT;?>/images/toparent.png" style="width:15px;border:0px;" title="Return to Institution List" />
				</a>
				<?php
				if($editorCode > 1){
					?>
					<a href="#" onclick="toggle('editdiv');">
						<img src="<?php echo $CLIENT_ROOT;?>/images/edit.png" style="width:15px;border:0px;" title="Edit Institution" />
					</a>
					<?php
				}
				?>
			</div>
			<div style="clear:both;">
				<form id="insteditform" name="insteditform" action="institutioneditor.php" method="post">
					<fieldset style="padding:20px;">
						<legend><b>Address Details</b></legend>
						<div style="position:relative;">
							<div style="float:left;width:155px;font-weight:bold;">
								Institution Code:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['institutioncode']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="institutioncode" type="text" value="<?php echo $instArr['institutioncode']; ?>" />
								<input name="getgrscicoll" type="button" value="Update from GrSciColl" onClick="grscicoll('insteditform')"/>
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Institution Name:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['institutionname']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="institutionname" type="text" value="<?php echo $instArr['institutionname']; ?>" style="width:400px;" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Institution Name2:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['institutionname2']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="institutionname2" type="text" value="<?php echo $instArr['institutionname2']; ?>" style="width:400px;" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Address:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['address1']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="address1" type="text" value="<?php echo $instArr['address1']; ?>" style="width:400px;" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Address 2:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['address2']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="address2" type="text" value="<?php echo $instArr['address2']; ?>" style="width:400px;" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								City:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['city']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="city" type="text" value="<?php echo $instArr['city']; ?>" style="width:100px;" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								State/Province:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['stateprovince']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="stateprovince" type="text" value="<?php echo $instArr['stateprovince']; ?>" style="width:100px;" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Postal Code:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['postalcode']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="postalcode" type="text" value="<?php echo $instArr['postalcode']; ?>" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Country:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['country']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="country" type="text" value="<?php echo $instArr['country']; ?>" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Phone:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['phone']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="phone" type="text" value="<?php echo $instArr['phone']; ?>" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Contact:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['contact']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="contact" type="text" value="<?php echo $instArr['contact']; ?>" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Email:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['email']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="email" type="text" value="<?php echo $instArr['email']; ?>" style="width:150px" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								URL:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<a href="<?php echo htmlspecialchars($instArr['url'], HTML_SPECIAL_CHARS_FLAGS); ?>" target="_blank">
									<?php echo $instArr['url']; ?>
								</a>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="url" type="text" value="<?php echo $instArr['url']; ?>" style="width:400px" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Notes:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['notes']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="notes" type="text" value="<?php echo $instArr['notes']; ?>" style="width:400px" />
							</div>
						</div>
						<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;clear:both;margin:30px 0px 0px 20px;">
							<input name="formsubmit" type="submit" value="Update Institution Address" />
							<input name="iid" type="hidden" value="<?php echo $iid; ?>" />
							<input name="targetcollid" type="hidden" value="<?php echo $targetCollid; ?>" />
						</div>
					</fieldset>
				</form>
				<div style="clear:both;">
					<fieldset style="padding:20px;">
						<legend><b>Collections Linked to Institution Address</b></legend>
						<div>
							<?php
							if($collList){
								foreach($collList as $id => $collName){
									echo '<div style="margin:5px;font-weight:bold;clear:both;height:15px;">';
									echo '<div style="float:left;"><a href="../misc/collprofiles.php?collid=' . htmlspecialchars($id, HTML_SPECIAL_CHARS_FLAGS) . '">' . htmlspecialchars($collName, HTML_SPECIAL_CHARS_FLAGS) . '</a></div> ';
									if($editorCode == 3 || in_array($id,$USER_RIGHTS["CollAdmin"]))
										echo ' <div class="editdiv" style="margin-left:10px;display:'.($eMode?'':'none').'"><a href="institutioneditor.php?iid=' . htmlspecialchars($iid, HTML_SPECIAL_CHARS_FLAGS) . '&removecollid=' . htmlspecialchars($id, HTML_SPECIAL_CHARS_FLAGS) . '"><img src="../../images/del.png" style="width:15px;"/></a></div>';
									echo '</div>';
								}
							}
							else{
								echo '<div style="margin:25px;"><b>Institution is not linked to a collection</b></div>';
							}
							?>
						</div>
						<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
							<div style="margin:15px;clear:both;">* Click on red X to unlink collection</div>
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
										<option value="">Select collection to add</option>
										<option value="">------------------------------------</option>
										<?php
										foreach($addList as $collid => $collArr){
											echo '<option value="'.$collid.'">'.$collArr['name'].'</option>';
										}
										?>
									</select>
									<input name="iid" type="hidden" value="<?php echo $iid; ?>" />
									<input name="formsubmit" type="submit" value="Add Collection" />
								</form>
								<?php
							}
							?>
						</div>
					</fieldset>
					<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
						<fieldset style="padding:20px;">
							<legend><b>Delete Institution</b></legend>
							<form name="instdelform" action="institutioneditor.php" method="post" onsubmit="return confirm('Are you sure you want to delete this institution?')">
								<div style="position:relative;clear:both;">
									<input name="formsubmit" type="submit" value="Delete Institution" <?php if($collList) echo 'disabled'; ?> />
									<input name="deliid" type="hidden" value="<?php echo $iid; ?>" />
									<?php
									if($collList) echo '<div style="margin:15px;color:red;">Deletion of addresses that have linked collections is not allowed</div>';
									?>
								</div>
							</form>
						</fieldset>
					</div>
				</div>
			</div>
			<?php
		}
	}
	else{
		if($editorCode){
			?>
			<div style="float:right;">
				<a href="#" onclick="toggle('instadddiv');">
					<img src="<?php echo $CLIENT_ROOT;?>/images/add.png" style="width:15px;border:0px;" title="Add a New Institution" />
				</a>
			</div>
			<div id="instadddiv" style="display:<?php echo ($eMode?'block':'none'); ?>;margin-bottom:8px;">
				<form id="instaddform" name="instaddform" action="institutioneditor.php" method="post">
					<fieldset style="padding:20px;">
						<legend><b>Add New Institution</b></legend>
						<div style="position:relative;">
							<div style="float:left;width:155px;font-weight:bold;">
								Institution Code:
							</div>
							<div>
								<input name="institutioncode" type="text" value="<?php echo $instCodeDefault; ?>" />
								<input name="getgrscicoll" type="button" value="Get data from GrSciColl" onClick="grscicoll('instaddform')"/>
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Institution Name:
							</div>
							<div>
								<input name="institutionname" type="text" value="" style="width:400px;" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Institution Name2:
							</div>
							<div>
								<input name="institutionname2" type="text" value="" style="width:400px;" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Address:
							</div>
							<div>
								<input name="address1" type="text" value="" style="width:400px;" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Address 2:
							</div>
							<div>
								<input name="address2" type="text" value="" style="width:400px;" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								City:
							</div>
							<div>
								<input name="city" type="text" value="" style="width:100px;" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								State/Province:
							</div>
							<div>
								<input name="stateprovince" type="text" value="" style="width:100px;" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Postal Code:
							</div>
							<div>
								<input name="postalcode" type="text" value="" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Country:
							</div>
							<div>
								<input name="country" type="text" value="" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Phone:
							</div>
							<div>
								<input name="phone" type="text" value="" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Contact:
							</div>
							<div>
								<input name="contact" type="text" value="" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Email:
							</div>
							<div>
								<input name="email" type="text" value="" style="width:150px" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								URL:
							</div>
							<div>
								<input name="url" type="text" value="" style="width:400px" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Notes:
							</div>
							<div>
								<input name="notes" type="text" value="" style="width:400px" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Link to:
							</div>
							<div>
								<select name="targetcollid" style="width:400px;">
									<option value="">Leave Orphaned</option>
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
						<div style="margin:20px;clear:both;">
							<input name="formsubmit" type="submit" value="Add Institution" />
						</div>
					</fieldset>
				</form>
			</div>
			<?php
			if(!$eMode){
				?>
				<div style="padding-left:10px;">
					<h2>Select an Institution from the list</h2>
					<ul>
						<?php
						$instList = $instManager->getInstitutionList();
						if($instList){
							foreach($instList as $iid => $iArr){
								echo '<li><a href="institutioneditor.php?iid='.$iid.'">';
								echo $iArr['institutionname'].' ('.$iArr['institutioncode'].')';
								if($editorCode == 3 || array_intersect(explode(',',$iArr['collid']),$USER_RIGHTS["CollAdmin"])){
									echo ' <a href="institutioneditor.php?emode=1&iid=' .htmlspecialchars($iid, HTML_SPECIAL_CHARS_FLAGS) . '"><img src="' . htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS) . '/images/edit.png" style="width:13px;" /></a>';
								}
								echo '</a></li>';
							}
						}
						else{
							echo "<div>There are no institutions you have right to edit</div>";
						}
						?>
					</ul>
				</div>
				<?php
			}
		}
		else{
			echo "<div>You need to have administrative user rights for a collection to add an institution</div>";
		}
	}
	?>
</div>
<?php
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>