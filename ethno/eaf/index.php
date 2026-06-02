<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoMediaManager.php');
header('Content-Type: text/html; charset='.$CHARSET);

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$mediaid = array_key_exists('mediaid',$_REQUEST)?$_REQUEST['mediaid']:0;
$tabIndex = array_key_exists("tabindex",$_REQUEST)?$_REQUEST["tabindex"]:0;
$action = array_key_exists('submitaction',$_POST)?$_POST['submitaction']:'';

//Sanitation
if($action && !preg_match('/^[a-zA-Z0-9\s_]+$/',$action)) $action = '';
if(!is_numeric($collid)) $collid = 0;
if(!is_numeric($mediaid)) $mediaid = 0;
if(!is_numeric($tabIndex)) $tabIndex = 0;

$isEditor = false;
if($SYMB_UID){
	if($IS_ADMIN){
		$isEditor = true;
	}
	elseif($collid && ((array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollAdmin"])) || (array_key_exists("CollEditor",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollEditor"])))){
		$isEditor = true;
	}
}

$eafManager = new EthnoMediaManager();
$eafManager->setCollid($collid);
$eafManager->setMediaid($mediaid);

$statusStr = '';
$eafArr = array();

if($action === 'Upload EAF'){
	$eafManager->addFile();
	$mediaid = $eafManager->getMediaid();
	$eafManager->setMediaid($mediaid);
	$eafArr = $eafManager->getEAFInfoArr();
	$eaf_file = $eafArr['eaffile'];
	$file_path = $SERVER_ROOT.$eaf_file;
	if(@simplexml_load_file($file_path)){
		header('Location: eafedit.php?mediaid='.$mediaid.'&collid='.$collid);
	}
	else{
		$eafManager->deleteEAF($mediaid);
		$mediaid = 0;
		$statusStr = "EAF file could not be parsed.";;
	}
}
elseif($action === 'Save EAF Edits'){
	$statusStr = $eafManager->saveEAFInfo($_POST);
}
elseif($action === 'Delete EAF Record'){
	$eafManager->deleteEAF($mediaid);
}

$eafArr = $eafManager->getEAFArr();
$collArr = $eafManager->getCollectionList();
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> - Manage EAF Files</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<link href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<script src="../../js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="../../js/jquery-ui.min.js" type="text/javascript"></script>
	<script src="../../js/symb/shared.js?ver=131106" type="text/javascript"></script>
	<script>
		function verifyUploadEAF(f){
			var file = f.eaffile.files[0];
			var mp3url = f.mp3url.value;
			var description = f.description.value;
			if(!file){
				alert("Please select an eaf file to upload.");
				return false;
			}
			else if(file.size > 1000000){
				alert("The eaf file you are trying to upload is too big.");
				return false;
			}
			else if(!(mp3url.substr(mp3url.length - 4) === '.mp3') && !(mp3url.substr(mp3url.length - 4) === '.mp4')){
				alert("Please enter a valid url to the associated mp3 file.");
				return false;
			}
			else if(!description){
				alert("Please enter a description for the eaf file.");
				return false;
			}
			else{
				document.getElementById("performactionup").value = 'Upload EAF';
				document.neweafform.submit();
			}
		}

		function verifyRemoveEAF(f){
			var valid = false;
			for(var i=0;i<f.length;i++){
				if((f.elements[i].name === "medid[]") && (f.elements[i].checked === true)){
					valid = true;
				}
			}
			if(valid){
				document.getElementById("performaction").value = 'Delete EAF';
				document.eaflistform.submit();
			}
			else{
				alert('Please select at least one EAF record to remove.');
			}
		}
	</script>
</head>
<body>
<?php
$displayLeftMenu = false;
include($SERVER_ROOT.'/includes/header.php');

echo '<div class="navpath">';
echo '<a href="../../index.php">Home</a> &gt;&gt; ';
if($isEditor && $collid) echo '<a href="../../collections/misc/collprofiles.php?collid='.$collid.'&emode=1">Collection Control Panel</a> &gt;&gt; ';
if($isEditor) echo '<b>Manage EAF Files</b>';
else echo '<b>View EAF Files</b>';
echo '</div>';
?>
<!-- This is inner text! -->
<div id="innertext" style="padding-top:0;">
	<?php
	if($statusStr){
		?>
		<hr/>
		<div style="margin:15px;color:red;">
			<?php echo $statusStr; ?>
		</div>
		<?php
	}
	?>
	<div id="reflistdiv" style="min-height:200px;">
		<?php
		if($isEditor && $collid){
			?>
			<div style="float:right;">
				<a href="#" onclick="toggle('neweafdiv');">
					<img src="../../images/add.png" alt="Upload EAF File" />
				</a>
			</div>
			<?php
		}
		?>
		<div id="neweafdiv" style="display:none;">
			<form name="neweafform" action="index.php" method="post" enctype="multipart/form-data">
				<fieldset>
					<legend><b>Add New EAF File</b></legend>
					<div id="eafuploaddiv" style="margin-top:8px;">
						EAF File:
						<!-- following line sets MAX_FILE_SIZE (must precede the file input field)  -->
						<input type='hidden' name='MAX_FILE_SIZE' value='100000000' />
						<input name='eaffile' type='file' size='70' />
					</div>
					<div style="width:100%;margin-top:8px;">
						MP3/MP4 URL
						<input name="mp3url" type="text" style="width:500px;" />
					</div>
					<div style="width:100%;margin-top:8px;">
						EAF Description
						<input name="description" type="text" style="width:500px;" />
					</div>
					<div style="clear:both;padding-top:8px;float:right;">
						<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
						<input id="performactionup" name="submitaction" type="hidden" value="" />
						<button type="button" onclick='verifyUploadEAF(this.form);'>Upload</button>
						<input name="cancelbutton" type="button" onClick="toggle('neweafdiv');" value="Cancel" />
					</div>
				</fieldset>
			</form>
		</div>

		<?php
		if($eafArr) {
			?>
			<form name="collselectform" action="index.php" method="post" onsubmit="">
				<div style="padding-top:8px;width:780px;margin: 0 auto 10px auto;clear:both;display:flex;align-items:center;">
					Collection/Project:
					<select style='margin-left:10px;width:600px;' name="collid" id="collectionSelect" onChange="this.form.submit();">
						<option value="0">All collections/projects</option>
						<?php
						foreach ($collArr as $k => $v) {
							echo '<option value="'.$k.'" '.($k==$collid?"selected":"").'>'.$v.'</option>';
						}
						?>
					</select>
				</div>
			</form>
			<div style="min-height:200px;clear:both">
				<table class="styledtable"
					   style="width:770px;font-family:Arial;font-size:12px;margin-left:auto;margin-right:auto;">
					<tr>
						<th style="width:500px;">Description</th>
						<?php
						if ($isEditor) {
							?>
							<th style="width:15px;"></th>
							<?php
						}
						?>
					</tr>
					<?php
					foreach ($eafArr as $medId => $mArr) {
						echo '<tr>';
						echo '<td style="width:500px;"><a href="eafdetail.php?mediaid=' . $medId . '&collid=' . $collid . '">' . $mArr['desc'] . '</a></td>' . "\n";
						if ($isEditor) echo '<td style="width:15px;cursor:pointer;"><a href="eafedit.php?mediaid=' . $medId . '&collid=' . $collid . '"><img style="border:0px;" src="../../images/edit.png" /></a></td>' . "\n";
						echo '</tr>';
					}
					?>
				</table>
			</div>
			<?php
		}
		else{
			echo '<div style="margin-top:10px;font-weight:bold;font-size:120%;">There are currently no EAF records in the database for this project.</div>';
		}
		?>
	</div>
</div>
<?php
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>
