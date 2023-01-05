<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ImageCleaner.php');
header("Content-Type: text/html; charset=".$CHARSET);

$action = array_key_exists("submitaction",$_POST)?$_POST["submitaction"]:"";
$collid = $_REQUEST['collid'];

$isEditor = false;
if($IS_ADMIN){
	$isEditor = true;
}

$imgManager = new ImageCleaner();

if($isEditor){
	if($action == 'remove_images'){
		if($_POST['target_imgid']){
			$imgManager->setCollid($collid);
			$imgManager->recycleImagesFromStr($_POST['target_imgid']);
		}
		else{
			//Get image ids from input fields
		}
	}
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Image Recycler</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>" />
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script type="text/javascript">
		function verifyRecycleForm(f){
			return true;
		}
	</script>
</head>
<body>
	<?php
	$displayLeftMenu = false;
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="../../index.php">Homepage</a> &gt;&gt;
		<a href="../../collections/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Management Menu</a> &gt;&gt;
		<b>Bulk Image Recycler</b>
	</div>
	<?php
	if($collid){
		?>
		<div id="innertext">
			<form name="imgdelform" action="imagerecycler.php" method="post" enctype="multipart/form-data" onsubmit="return verifyRecycleForm(this)">
				<fieldset style="width:90%;">
					<legend style="font-weight:bold;font-size:120%;">Batch Image Remover</legend>
					<div style="margin:10px;">
						This tool will batch delete images based on submission of multiple image identifiers.
					</div>
					<div style="margin:10px;">
						<input type='hidden' name='MAX_FILE_SIZE' value='10000000' />
						<input name="uploadfile" type="file" size="40" />
					</div>
					<div style="margin:10px;">
						<b>Image Identifiers</b><br/>
						<textarea name="target_imgid" style="width:300px;height:100px;"></textarea>
					</div>
					<div style="margin:20px;">
						<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
						<button type="submit" name="submitaction" value="remove_images">Bulk Remove Image Files</button>
					</div>
				</fieldset>
			</form>
		</div>
		<?php
	}
	else{
		echo '<b>ERROR: collection identifier is not set</b>';
	}
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>