<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SpecUploadBase.php');
include_once($SERVER_ROOT.'/content/lang/collections/admin/specupload.'.$LANG_TAG.'.php');

header('Content-Type: text/html; charset='.$CHARSET);
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/admin/specupload.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = $_REQUEST['collid'];
$uploadType = $_REQUEST['uploadtype'];
$uspid = array_key_exists('uspid',$_REQUEST)?$_REQUEST['uspid']:'';

if(strpos($uspid,'-')){
	$tok = explode('-',$uspid);
	$uspid = $tok[0];
	$uploadType = $tok[1];
}

//Sanitation
if(!is_numeric($collid)) $collid = 0;
if(!is_numeric($uploadType)) $uploadType = 0;
if(!is_numeric($uspid)) $uspid = 0;

$DIRECTUPLOAD = 1; $SKELETAL = 7; $IPTUPLOAD = 8; $NFNUPLOAD = 9; $STOREDPROCEDURE = 4; $SCRIPTUPLOAD = 5;

$duManager = new SpecUploadBase();

$duManager->setCollId($collid);
$duManager->setUspid($uspid);
$duManager->setUploadType($uploadType);

$isEditor = 0;
if($IS_ADMIN || (array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin']))){
	$isEditor = 1;
}
$duManager->readUploadParameters();
if($uploadType == $IPTUPLOAD) if($duManager->getPath()) header('Location: specuploadmap.php?uploadtype=8&uspid='.$uspid.'&collid='.$collid);
elseif($uploadType == $DIRECTUPLOAD || $uploadType == $STOREDPROCEDURE || $uploadType == $SCRIPTUPLOAD){
	header('Location: specuploadprocessor.php?uploadtype='.$SCRIPTUPLOAD.'&uspid='.$uspid.'&collid='.$collid);
}
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title><?php echo $DEFAULT_TITLE.' '.(isset($LANG['SPEC_UPLOAD'])?$LANG['SPEC_UPLOAD']:'Specimen Uploader - file selector'); ?></title>
	<?php
	$activateJQuery = true;
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="../../js/jquery.js" type="text/javascript"></script>
	<script src="../../js/jquery-ui.js" type="text/javascript"></script>
	<script src="../../js/symb/shared.js" type="text/javascript"></script>
	<script>
		function verifyFileUploadForm(f){
			var fileName = "";
			if(f.uploadfile || f.ulfnoverride){
				if(f.uploadfile && f.uploadfile.value){
					 fileName = f.uploadfile.value;
				}
				else{
					fileName = f.ulfnoverride.value;
				}
				if(fileName == ""){
					alert("<?php echo (isset($LANG['PATH_EMPTY'])?$LANG['PATH_EMPTY']:'File path is empty. Please select the file that is to be loaded.'); ?>");
					return false;
				}
				else{
					var ext = fileName.split('.').pop();
					if(ext == 'csv' || ext == 'CSV') return true;
					else if(ext == 'zip' || ext == 'ZIP') return true;
					else if(ext == 'txt' || ext == 'TXT') return true;
					else if(ext == 'tab' || ext == 'tab') return true;
					else if(fileName.substring(0,4) == 'http') return true;
					else{
						alert("<?php echo (isset($LANG['MUST_CSV'])?$LANG['MUST_CSV']:'File must be comma separated (.csv), tab delimited (.txt or .tab), ZIP file (.zip), or a URL to an IPT Resource'); ?>");
						return false;
					}
				}
			}
			return true;
		}

		function verifyFileSize(inputObj){
			inputObj.form.ulfnoverride.value = ''
			if (!window.FileReader) {
				//alert("The file API isn't supported on this browser yet.");
				return;
			}
			<?php
			$maxUpload = ini_get('upload_max_filesize');
			$maxUpload = str_replace("M", "000000", $maxUpload);
			if($maxUpload > 100000000) $maxUpload = 100000000;
			echo 'var maxUpload = '.$maxUpload.";\n";
			?>
			var file = inputObj.files[0];
			if(file.size > maxUpload){
				var msg = "<?php echo (isset($LANG['IMPORT_FILE'])?$LANG['IMPORT_FILE']:'Import file '); ?>"+file.name+" ("+Math.round(file.size/100000)/10+"<?php echo (isset($LANG['IS_BIGGER'])?$LANG['IS_BIGGER']:'MB) is larger than is allowed (current limit: '); ?>"+(maxUpload/1000000)+"MB).";
				if(file.name.slice(-3) != "zip") msg = msg + "<?php echo (isset($LANG['MAYBE_ZIP'])?$LANG['MAYBE_ZIP']:' Note that import file size can be reduced by compressing within a zip file. '); ?>";
				alert(msg);
			}
		}
	</script>
</head>
<body>
<?php
$displayLeftMenu = false;
include($SERVER_ROOT.'/includes/header.php');
?>
<div class="navpath">
	<a href="../../index.php"><?php echo (isset($LANG['HOME'])?$LANG['HOME']:'Home'); ?></a> &gt;&gt;
	<a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1"><?php echo (isset($LANG['COL_MGMNT'])?$LANG['COL_MGMNT']:'Collection Management Panel'); ?></a> &gt;&gt;
	<a href="specuploadmanagement.php?collid=<?php echo $collid; ?>"><?php echo (isset($LANG['LIST_UPLOAD'])?$LANG['LIST_UPLOAD']:'List of Upload Profiles'); ?></a> &gt;&gt;
	<b><?php echo (isset($LANG['SPEC_UPLOAD'])?$LANG['SPEC_UPLOAD']:'Specimen Uploader'); ?></b>
</div>
<div id="innertext">
	<?php
	echo '<h1>'.(isset($LANG['UP_MODULE'])?$LANG['UP_MODULE']:'Data Upload Module').'</h1>';
	if($isEditor && $collid){
		//Grab collection name and last upload date and display for all
		echo '<div style="font-weight:bold;font-size:130%;">'.$duManager->getCollInfo('name').'</div>';
		echo '<div style="margin:0px 0px 15px 15px;"><b>Last Upload Date:</b> '.($duManager->getCollInfo('uploaddate')?$duManager->getCollInfo('uploaddate'):(isset($LANG['NOT_REC'])?$LANG['NOT_REC']:'not recorded')).'</div>';
		?>
		<form name="fileuploadform" action="specuploadmap.php" method="post" enctype="multipart/form-data" onsubmit="return verifyFileUploadForm(this)">
			<fieldset style="width:95%;">
				<legend style="font-weight:bold;font-size:120%;<?php if($uploadType == $SKELETAL) echo 'background-color:lightgreen'; ?>"><?php echo $duManager->getTitle().': '.(isset($LANG['ID_SOURCE'])?$LANG['ID_SOURCE']:'Identify Data Source'); ?></legend>
				<div>
					<div style="margin:10px">
						<?php
						$pathLabel = (isset($LANG['IPT_URL'])?$LANG['IPT_URL']:'IPT Resource URL');
						if($uploadType != $IPTUPLOAD){
							$pathLabel = (isset($LANG['RES_URL'])?$LANG['RES_URL']:'Resource Path or URL');
							?>
							<div>
								<input name="uploadfile" type="file" size="50" onchange="verifyFileSize(this)" />
							</div>
							<?php
						}
						?>
						<div class="ulfnoptions" style="display:<?php echo ($uploadType!=$IPTUPLOAD?'none':''); ?>;margin:15px 0px">
							<b><?php echo $pathLabel; ?>:</b>
							<input name="ulfnoverride" type="text" size="70" /><br/>
							<?php
							if($uploadType != $IPTUPLOAD) echo '* '.$LANG['WORKAROUND'];
							?>
						</div>
						<?php
						if($uploadType != $IPTUPLOAD){
							?>
							<div class="ulfnoptions">
								<a href="#" onclick="toggle('ulfnoptions');return false;"><?php echo (isset($LANG['DISPLAY_OPS'])?$LANG['DISPLAY_OPS']:'Display Additional Options'); ?></a>
							</div>
							<?php
						}
						?>
					</div>
					<div style="margin:10px;">
						<?php
						if(!$uspid && $uploadType != $NFNUPLOAD)
							echo '<input name="automap" type="checkbox" value="1" CHECKED /> <b>'.(isset($LANG['AUTOMAP'])?$LANG['AUTOMAP']:'Automap fields').'</b><br/>';
						?>
					</div>
					<div style="margin:10px;">
						<button name="action" type="submit" value="Analyze File"><?php echo (isset($LANG['ANALYZE_FILE'])?$LANG['ANALYZE_FILE']:'Analyze File'); ?></button>
						<input name="uspid" type="hidden" value="<?php echo $uspid; ?>" />
						<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
						<input name="uploadtype" type="hidden" value="<?php echo $uploadType; ?>" />
						<input name="MAX_FILE_SIZE" type="hidden" value="100000000" />
					</div>
				</div>
			</fieldset>
		</form>
		<?php
	}
	else{
		if(!$isEditor || !$collid) echo '<div style="font-weight:bold;font-size:120%;">'.(isset($LANG['NOT_AUTH'])?$LANG['NOT_AUTH']:'ERROR: you are not authorized to upload to this collection').'</div>';
		else{
			echo '<div style="font-weight:bold;font-size:120%;">';
			echo (isset($LANG['PAGE_ERROR'])?$LANG['PAGE_ERROR']:'').' = ';
			echo ini_get("upload_max_filesize").'; post_max_size = '.ini_get('post_max_size');
			echo (isset($LANG['USE_BACK'])?$LANG['USE_BACK']:'Use the back arrows to get back to the file upload page.');
			echo '</div>';
		}
	}
	?>
</div>
<?php
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>