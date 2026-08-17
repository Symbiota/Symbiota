<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/KeyCharAdmin.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

header('Content-Type: text/html; charset=' . $CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../ident/admin/headingadmin.php?' . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$hid = array_key_exists('hid', $_POST) ? Sanitize::int($_POST['hid']) : 0;
$langId = array_key_exists('langid', $_REQUEST) ? $_REQUEST['langid'] : '';
$action = array_key_exists('action', $_POST) ? $_POST['action'] : '';

$charManager = new KeyCharAdmin();
$charManager->setLangId($langId);

$isEditor = false;
if($IS_ADMIN || array_key_exists("KeyAdmin",$USER_RIGHTS)){
	$isEditor = true;
}

$statusStr = '';
if($isEditor && $action){
	if($action == 'Create'){
		$statusStr = $charManager->addHeading($_POST['headingname'],$_POST['notes'],$_POST['sortsequence']);
	}
	elseif($action == 'Save'){
		$statusStr = $charManager->editHeading($hid,$_POST['headingname'],$_POST['notes'],$_POST['sortsequence']);
	}
	elseif($action == 'Delete'){
		$statusStr = $charManager->deleteHeading($hid);
	}
}
$headingArr = $charManager->getHeadingArr();
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET;?>">
	<title>Heading Administration</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script type="text/javascript" src="../../js/symb/shared.js"></script>
	<script type="text/javascript">
		function validateHeadingForm(f){
			if(f.headingname.value == ""){
				alert("Please enter a grouping title");
				return false;
			}
			return true;
		}
	</script>
	<style type="text/css">
		fieldset{ margin:15px; padding:15px; }
		legend{ font-weight: bold; }
		input{ autocomplete: off; }
		.icon-img{ width: 1.1em }
	</style>
</head>
<body>
	<!-- This is inner text! -->
	<div  id="innertext" style="width:700px;padding:15px">
		<h1 class="page-heading">Heading Administration</h1>
		<?php
		if($statusStr){
			?>
			<hr/>
			<div style="margin:15px;color:<?= (strpos($statusStr,'SUCCESS')===0?'green':'red'); ?>;">
				<?= $statusStr; ?>
			</div>
			<hr/>
			<?php
		}
		if($isEditor){
			?>
			<div id="addheadingdiv">
				<form name="newheadingform" action="headingadmin.php" method="post" onsubmit="return validateHeadingForm(this)">
					<fieldset>
						<legend>New Group</legend>
						<div>
							<label for="headingname">Group Title</label><br />
							<input type="text" id="headingname" name="headingname" maxlength="255" style="width:400px;" />
						</div>
						<div style="padding-top:6px;">
							<label for="notes">Notes</label><br />
							<input type="text" id="notes" name="notes" style="width:500px;" />
						</div>
						<div style="padding-top:6px;">
							<label for="sortsequence">Sort Sequence</label><br />
							<input type="text" id="sortsequence" name="sortsequence" style="width:80px" />
						</div>
						<div style="width:100%;padding-top:6px;">
							<button name="action" type="submit" value="Create">Create Group</button>
						</div>
					</fieldset>
				</form>
			</div>
			<div>
				<?php
				if($headingArr){
					?>
					<fieldset>
						<legend>Existing Groups</legend>
						<ul>
							<?php
							foreach($headingArr as $headingId => $headArr){
								?>
								<li><a href="#" onclick="toggle('headingedit-<?= $headingId ?>');"><?= $headArr['name'] ?> <img class="icon-img" src="../../images/edit.png"></a></li>
								<div id="headingedit-<?= $headingId ?>" style="display:none;margin:20px;">
									<fieldset>
										<legend>Editor</legend>
										<form name="headingeditform" action="headingadmin.php" method="post" onsubmit="return validateHeadingForm(this)">
											<div style="margin:2px;">
												<label for="headingname-<?= $headingId; ?>"">Group Title<br/>
												<input id="headingname" name="headingname" type="text" value="<?= $headArr['name']; ?>" style="width:400px;" />
											</div>
											<div style="margin:2px;">
												<label for="notes-<?= $headingId; ?>"">Notes<br/>
												<input id="notes" name="notes" type="text" value="<?= $headArr['notes']; ?>" style="width:500px;" />
											</div>
											<div style="margin:2px;">
												<label for="sortsequence-<?= $headingId; ?>">Sort Sequence<br/>
												<input id="sortsequence" name="sortsequence" type="text" value="<?= $headArr['sortsequence']; ?>" style="width:80px" />
											</div>
											<div>
												<input name="hid" type="hidden" value="<?= $headingId; ?>" />
												<button name="action" type="submit" value="Save">Save Edits</button>
											</div>
										</form>
									</fieldset>
									<fieldset>
										<legend>Delete Group</legend>
										<form name="headingdeleteform" action="headingadmin.php" method="post">
											<input name="hid" type="hidden" value="<?= $headingId; ?>" />
											<button name="action" type="submit" value="Delete">Delete</button>
										</form>
									</fieldset>
								</div>
								<?php
							}
							?>
						</ul>
					</fieldset>
					<?php
				}
				else{
					echo '<div style="font-weight:bold;font-size:120%;">There are no existing character groupings</div>';
				}
				?>
			</div>
			<?php
		}
		else{
			echo '<h2>You are not authorized to access page</h2>';
		}
		?>
	</div>
</body>
</html>
