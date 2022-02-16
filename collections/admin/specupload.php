<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SpecUploadDirect.php');
include_once($SERVER_ROOT.'/classes/SpecUploadFile.php');
include_once($SERVER_ROOT.'/classes/SpecUploadDwca.php');
include_once($SERVER_ROOT.'/content/lang/collections/admin/specupload.'.$LANG_TAG.'.php');

header("Content-Type: text/html; charset=".$CHARSET);
ini_set('max_execution_time', 3600);
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/admin/specupload.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = $_REQUEST["collid"];
$uploadType = $_REQUEST["uploadtype"];
$uspid = array_key_exists("uspid",$_REQUEST)?$_REQUEST["uspid"]:'';
$action = array_key_exists("action",$_REQUEST)?$_REQUEST["action"]:"";
$autoMap = array_key_exists("automap",$_POST)?true:false;
$ulPath = array_key_exists("ulpath",$_REQUEST)?$_REQUEST["ulpath"]:"";
$importIdent = array_key_exists("importident",$_REQUEST)?true:false;
$importImage = array_key_exists("importimage",$_REQUEST)?true:false;
$observerUid = array_key_exists('observeruid',$_POST)?$_POST['observeruid']:'';
$matchCatNum = array_key_exists("matchcatnum",$_REQUEST)?true:false;
$matchOtherCatNum = array_key_exists('matchothercatnum',$_REQUEST)&&$_REQUEST['matchothercatnum']?true:false;
$verifyImages = array_key_exists("verifyimages",$_REQUEST)&&$_REQUEST['verifyimages']?true:false;
$processingStatus = array_key_exists("processingstatus",$_REQUEST)?$_REQUEST['processingstatus']:'';
$finalTransfer = array_key_exists("finaltransfer",$_REQUEST)?$_REQUEST["finaltransfer"]:0;
$dbpk = array_key_exists("dbpk",$_REQUEST)?$_REQUEST["dbpk"]:'';
$sourceIndex = isset($_REQUEST['sourceindex'])?$_REQUEST['sourceindex']:0;
$recStart = array_key_exists("recstart",$_REQUEST)?$_REQUEST["recstart"]:0;
$recLimit = array_key_exists("reclimit",$_REQUEST)?$_REQUEST["reclimit"]:1000;

//Sanitation
if(!is_numeric($collid)) $collid = 0;
if(!is_numeric($uploadType)) $uploadType = 0;
if($action && !preg_match('/^[a-zA-Z0-9\s_]+$/',$action)) $action = '';
if($autoMap !== true) $autoMap = false;
if($importIdent !== true) $importIdent = false;
if(!is_numeric($observerUid)) $observerUid = 0;
if($matchCatNum !== true) $matchCatNum = false;
if($matchOtherCatNum !== true) $matchOtherCatNum = false;
if($verifyImages !== true) $verifyImages = false;
if(!preg_match('/^[a-zA-Z0-9\s_-]+$/',$processingStatus)) $processingStatus = '';
if($autoMap !== true) $autoMap = false;
if(!is_numeric($finalTransfer)) $finalTransfer = 0;
if($dbpk) $dbpk = htmlspecialchars($dbpk);
if(!is_numeric($sourceIndex)) $sourceIndex = 0;
if(!is_numeric($recStart)) $recStart = 0;
if(!is_numeric($recLimit)) $recLimit = 1000;

$DIRECTUPLOAD = 1; $FILEUPLOAD = 3; $STOREDPROCEDURE = 4; $SCRIPTUPLOAD = 5; $DWCAUPLOAD = 6; $SKELETAL = 7; $IPTUPLOAD = 8; $NFNUPLOAD = 9;

if(strpos($uspid,'-')){
	$tok = explode('-',$uspid);
	$uspid = $tok[0];
	$uploadType = $tok[1];
}

$duManager = new SpecUploadBase();
if($uploadType == $DIRECTUPLOAD){
	$duManager = new SpecUploadDirect();
}
elseif($uploadType == $FILEUPLOAD || $uploadType == $NFNUPLOAD){
	$duManager = new SpecUploadFile();
	$duManager->setUploadFileName($ulPath);
}
elseif($uploadType == $SKELETAL){
	$duManager = new SpecUploadFile();
	$duManager->setUploadFileName($ulPath);
	$matchCatNum = true;
}
elseif($uploadType == $DWCAUPLOAD || $uploadType == $IPTUPLOAD){
	$duManager = new SpecUploadDwca();
	$duManager->setTargetPath($ulPath);
	$duManager->setIncludeIdentificationHistory($importIdent);
	$duManager->setIncludeImages($importImage);
	for($i=0;$i<3;$i++){
		if(isset($_POST['filter'.$i])){
			$duManager->addFilterCondition($_POST['filter'.$i], $_POST['condition'.$i], $_POST['value'.$i]);
		}
	}
}

$duManager->setCollId($collid);
$duManager->setUspid($uspid);
$duManager->setUploadType($uploadType);
$duManager->setObserverUid($observerUid);
$duManager->setMatchCatalogNumber($matchCatNum);
$duManager->setMatchOtherCatalogNumbers($matchOtherCatNum);
$duManager->setVerifyImageUrls($verifyImages);
$duManager->setProcessingStatus($processingStatus);
$duManager->setSourcePortalIndex($sourceIndex);

if($action == 'Automap Fields'){
	$autoMap = true;
}

$statusStr = '';
$isEditor = 0;
if($IS_ADMIN || (array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollAdmin"]))){
	$isEditor = 1;
}
if($isEditor && $collid){
	$duManager->readUploadParameters();

	$isLiveData = false;
	if($duManager->getCollInfo('managementtype') == 'Live Data') $isLiveData = true;

	//Grab field mapping, if mapping form was submitted
	if(array_key_exists("sf",$_POST)){
		if($action == "Reset Field Mapping"){
			$statusStr = $duManager->deleteFieldMap();
		}
		else{
			//Set field map for occurrences using mapping form
			$targetFields = $_POST["tf"];
			$sourceFields = $_POST["sf"];
			$fieldMap = Array();
			for($x = 0;$x<count($targetFields);$x++){
				if($targetFields[$x]){
					$tField = $targetFields[$x];
					if($tField == 'unmapped') $tField .= '-'.$x;
					$fieldMap[$tField]["field"] = $sourceFields[$x];
				}
			}
			//Set Source PK
			if($dbpk) $fieldMap["dbpk"]["field"] = $dbpk;
			$duManager->setFieldMap($fieldMap);

			//Set field map for identification history
			if(array_key_exists("ID-sf",$_POST)){
				$targetIdFields = $_POST["ID-tf"];
				$sourceIdFields = $_POST["ID-sf"];
				$fieldIdMap = Array();
				for($x = 0;$x<count($targetIdFields);$x++){
					if($targetIdFields[$x]){
						$tIdField = $targetIdFields[$x];
						if($tIdField == 'unmapped') $tIdField .= '-'.$x;
						$fieldIdMap[$tIdField]["field"] = $sourceIdFields[$x];
					}
				}
				$duManager->setIdentFieldMap($fieldIdMap);
			}
			//Set field map for image history
			if(array_key_exists("IM-sf",$_POST)){
				$targetImFields = $_POST["IM-tf"];
				$sourceImFields = $_POST["IM-sf"];
				$fieldImMap = Array();
				for($x = 0;$x<count($targetImFields);$x++){
					if($targetImFields[$x]){
						$tImField = $targetImFields[$x];
						if($tImField == 'unmapped') $tImField .= '-'.$x;
						$fieldImMap[$tImField]["field"] = $sourceImFields[$x];
					}
				}
				$duManager->setImageFieldMap($fieldImMap);
			}
		}
		if($action == 'saveMapping'){
			$statusStr = $duManager->saveFieldMap($_POST);
			if(!$uspid) $uspid = $duManager->getUspid();
		}
	}
	$duManager->loadFieldMap();
}
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title><?php echo $DEFAULT_TITLE.' '.(isset($LANG['SPEC_UPLOAD'])?$LANG['SPEC_UPLOAD']:'Specimen Uploader'); ?></title>
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

		function verifyImageSize(inputObj){
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

		function verifyMappingForm(f){
			var sfArr = [];
			var idSfArr = [];
			var imSfArr = [];
			var tfArr = [];
			var idTfArr = [];
			var imTfArr = [];
			var catalogNumberIndex = 0;
			var possibleMappingErr = false;
			for(var i=0;i<f.length;i++){
				var obj = f.elements[i];
				if(obj.name == "sf[]"){
					if(sfArr.indexOf(obj.value) > -1){
						alert("<?php echo (isset($LANG['ERR_UNIQUE_D'])?$LANG['ERR_UNIQUE_D']:'ERROR: Source field names must be unique (duplicate field: '); ?>"+obj.value+")");
						return false;
					}
					sfArr[sfArr.length] = obj.value;
					//Test value to make sure source file isn't missing the header and making directly to file record
					if(!possibleMappingErr){
						if(isNumeric(obj.value)){
							possibleMappingErr = true;
						}
						if(obj.value.length > 7){
							if(isNumeric(obj.value.substring(5))){
								possibleMappingErr = true;
							}
							else if(obj.value.slice(-5) == "aceae" || obj.value.slice(-4) == "idae"){
								possibleMappingErr = true;
							}
						}
					}
				}
				else if(obj.name == "ID-sf[]"){
					if(f.importident.value == "1"){
						if(idSfArr.indexOf(obj.value) > -1){
							alert("<?php echo (isset($LANG['ERR_UNIQUE_ID'])?$LANG['ERR_UNIQUE_ID']:'ERROR: Source field names must be unique (Identification: '); ?>"+obj.value+")");
							return false;
						}
						idSfArr[idSfArr.length] = obj.value;
					}
				}
				else if(obj.name == "IM-sf[]"){
					if(f.importimage.value == "1"){
						if(imSfArr.indexOf(obj.value) > -1){
							alert("<?php echo (isset($LANG['ERR_UNIQUE_IM'])?$LANG['ERR_UNIQUE_IM']:'ERROR: Source field names must be unique (Image: '); ?>"+obj.value+")");
							return false;
						}
						imSfArr[imSfArr.length] = obj.value;
					}
				}
				else if(obj.value != "" && obj.value != "unmapped"){
					if(obj.name == "tf[]"){
						if(tfArr.indexOf(obj.value) > -1){
							alert("<?php echo (isset($LANG['SAME_TARGET_D'])?$LANG['SAME_TARGET_D']:'ERROR: Can\'t map to the same target field more than once ( '); ?>"+obj.value+")");
							return false;
						}
						tfArr[tfArr.length] = obj.value;
					}
					else if(obj.name == "ID-tf[]"){
						if(f.importident.value == "1"){
							if(idTfArr.indexOf(obj.value) > -1){
								alert("<?php echo (isset($LANG['SAME_TARGET_ID'])?$LANG['SAME_TARGET_ID']:'ERROR: Can\'t map to the same target field more than once (Identification: '); ?>"+obj.value+")");
								return false;
							}
							idTfArr[idTfArr.length] = obj.value;
						}
					}
					else if(obj.name == "IM-tf[]"){
						if(f.importimage.value == "1"){
							if(imTfArr.indexOf(obj.value) > -1){
								alert("<?php echo (isset($LANG['SAME_TARGET_IM'])?$LANG['SAME_TARGET_IM']:'ERROR: Can\'t map to the same target field more than once (Images: '); ?>"+obj.value+")");
								return false;
							}
							imTfArr[imTfArr.length] = obj.value;
						}
					}
				}
				if(obj.name == "tf[]"){
					//Is skeletal file upload
					if(obj.value == "catalognumber"){
						catalogNumberIndex = catalogNumberIndex + 1;
					}
					else if(obj.value == "othercatalognumbers"){
						catalogNumberIndex = catalogNumberIndex + 2;
					}
				}
			}
			if(f.uploadtype.value == 7){
				if(catalogNumberIndex == 0){
					//Skeletal records require catalog number to be mapped
					alert("<?php echo (isset($LANG['NEED_CAT'])?$LANG['NEED_CAT']:'ERROR: catalogNumber or otherCatalogNumbers is required for Skeletal File Uploads'); ?>");
					return false;
				}
				else if(f.matchcatnum.checked == false && f.matchothercatnum.checked == false){
					alert("<?php echo (isset($LANG['SEL_MATCH'])?$LANG['SEL_MATCH']:'ERROR: select which identifier will be used for record matching (required for Skeletal File imports)'); ?>");
					return false;
				}
				else{
					if((catalogNumberIndex == 1 && f.matchcatnum.checked == false) || (catalogNumberIndex == 2 && f.matchothercatnum.checked == false)){
						alert("<?php echo (isset($LANG['ID_NOT_MATCH'])?$LANG['ID_NOT_MATCH']:'ERROR: identifier record matching does not match import fields (required for Skeletal File imports)'); ?>");
						return false;
					}
				}
			}
			if(f.observeruid && f.observeruid.value == ""){
				alert("<?php echo $LANG['SEL_TAR_USER']; ?>");
				return false;
			}
			if(possibleMappingErr){
				return confirm("<?php echo $LANG['FIRST_ROW']; ?>");
			}
			return true;
		}

		function verifySaveMapping(f){
			if(f.uspid.value == "" && f.profiletitle.value == ""){
				$("#newProfileNameDiv").show();
				alert("<?php echo $LANG['ENTER_PROF']; ?>");
				return false;
			}
			return true;
		}

		function pkChanged(selObj){
			if(selObj.value){
				$("#mdiv").show();
				//$("#uldiv").show();
			}
			else{
				$("#mdiv").hide();
				//$("#uldiv").show();
			}
		}
	</script>
	<style type="text/css">
		.unmapped{ background: yellow; }
	</style>
</head>
<body>
<?php
	$displayLeftMenu = (isset($collections_admin_specuploadMenu)?$collections_admin_specuploadMenu:false);
	include($SERVER_ROOT.'/includes/header.php');
	if(isset($collections_admin_specuploadCrumbs)){
		if($collections_admin_specuploadCrumbs){
			?>
			<div class="navpath">
				<a href="../../index.php"><?php echo (isset($LANG['HOME'])?$LANG['HOME']:'Home'); ?></a> &gt;&gt;
				<?php echo $collections_admin_specuploadCrumbs; ?>
				<b><?php echo (isset($LANG['SPEC_UPLOAD'])?$LANG['SPEC_UPLOAD']:'Specimen Uploader'); ?></b>
			</div>
			<?php
		}
	}
	else{
		?>
		<div class="navpath">
			<a href="../../index.php"><?php echo (isset($LANG['HOME'])?$LANG['HOME']:'Home'); ?></a> &gt;&gt;
			<a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1"><?php echo (isset($LANG['COL_MGMNT'])?$LANG['COL_MGMNT']:'Collection Management Panel'); ?></a> &gt;&gt;
			<a href="specuploadmanagement.php?collid=<?php echo $collid; ?>"><?php echo (isset($LANG['LIST_UPLOAD'])?$LANG['LIST_UPLOAD']:'List of Upload Profiles'); ?></a> &gt;&gt;
			<b><?php echo (isset($LANG['SPEC_UPLOAD'])?$LANG['SPEC_UPLOAD']:'Specimen Uploader'); ?></b>
		</div>
		<?php
	}
?>
<!-- This is inner text! -->
<div id="innertext">
	<h1><?php echo (isset($LANG['UP_MODULE'])?$LANG['UP_MODULE']:'Data Upload Module'); ?></h1>
	<?php
	if($statusStr){
		echo "<hr />";
		echo "<div>$statusStr</div>";
		echo "<hr />";
	}
	$recReplaceMsg = '<span style="color:orange"><b>'.(isset($LANG['CAUTION'])?$LANG['CAUTION']:'Caution').':</b></span> '.(isset($LANG['REC_REPLACE'])?$LANG['REC_REPLACE']:'Matching records will be replaced with incoming records');
	if($isEditor && $collid){
		//Grab collection name and last upload date and display for all
		echo '<div style="font-weight:bold;font-size:130%;">'.$duManager->getCollInfo('name').'</div>';
		echo '<div style="margin:0px 0px 15px 15px;"><b>Last Upload Date:</b> '.($duManager->getCollInfo('uploaddate')?$duManager->getCollInfo('uploaddate'):(isset($LANG['NOT_REC'])?$LANG['NOT_REC']:'not recorded')).'</div>';
		if(($action == "Start Upload") || (!$action && ($uploadType == $STOREDPROCEDURE || $uploadType == $SCRIPTUPLOAD))){
			//Upload records
			echo "<div style='font-weight:bold;font-size:120%'>".(isset($LANG['UP_STATUS'])?$LANG['UP_STATUS']:'Upload Status').":</div>";
			echo "<ul style='margin:10px;font-weight:bold;'>";
			$duManager->uploadData($finalTransfer);
			if($uploadType == $DWCAUPLOAD || $uploadType == $IPTUPLOAD) $sourceIndex = $duManager->getSourcePortalIndex();
			echo '</ul>';
			if(!$finalTransfer){
				?>
				<fieldset style="margin:15px;">
					<legend style="<?php if($uploadType == $SKELETAL) echo 'background-color:lightgreen'; ?>"><b><?php echo (isset($LANG['PENDING_REPORT'])?$LANG['PENDING_REPORT']:'Pending Data Transfer Report'); ?></b></legend>
					<div style="margin:5px;">
						<?php
						$reportArr = $duManager->getTransferReport();
						echo '<div>'.(isset($LANG['OCCS_TRANSFERING'])?$LANG['OCCS_TRANSFERING']:'Occurrences pending transfer').': '.$reportArr['occur'];
						if($reportArr['occur']){
							echo ' <a href="uploadreviewer.php?collid='.$collid.'" target="_blank" title="'.(isset($LANG['PREVIEW'])?$LANG['PREVIEW']:'Preview 1st 1000 Records').'"><img src="../../images/list.png" style="width:12px;" /></a>';
							echo ' <a href="uploadreviewer.php?action=export&collid='.$collid.'" target="_self" title="'.(isset($LANG['DOWNLOAD_RECS'])?$LANG['DOWNLOAD_RECS']:'Download Records').'"><img src="../../images/dl.png" style="width:12px;" /></a>';
						}
						echo '</div>';
						echo '<div style="margin-left:15px;">';
						echo '<div>'.$LANG['RECORDS_UPDATED'].': ';
						echo $reportArr['update'];
						if($reportArr['update']){
							echo ' <a href="uploadreviewer.php?collid='.$collid.'&searchvar=occid:ISNOTNULL" target="_blank" title="'.$LANG['PREVIEW'].'"><img src="../../images/list.png" style="width:12px;" /></a>';
							echo ' <a href="uploadreviewer.php?action=export&collid='.$collid.'&searchvar=occid:ISNOTNULL" target="_self" title="'.$LANG['DOWNLOAD_RECS'].'"><img src="../../images/dl.png" style="width:12px;" /></a>';
							if($uploadType != $SKELETAL && $uploadType != $NFNUPLOAD){
								echo '<span style="color:orange;margin-left:10px"><b>'.$LANG['CAUTION'].':</b></span> '.$LANG['CAUTION_REPLACE'];
							}
						}
						echo '</div>';
						if($uploadType != $NFNUPLOAD || $reportArr['new']){
							if($uploadType == $NFNUPLOAD) echo '<div>'.(isset($LANG['MISMATCHED'])?$LANG['MISMATCHED']:'Mismatched records').': ';
							else echo '<div>'.(isset($LANG['NEW_RECORDS'])?$LANG['NEW_RECORDS']:'New records').': ';
							echo $reportArr['new'];
							if($reportArr['new']){
								echo ' <a href="uploadreviewer.php?collid='.$collid.'&searchvar=occid:ISNULL" target="_blank" title="'.(isset($LANG['PREVIEW'])?$LANG['PREVIEW']:'Preview 1st 1000 Records').'"><img src="../../images/list.png" style="width:12px;" /></a>';
								echo ' <a href="uploadreviewer.php?action=export&collid='.$collid.'&searchvar=occid:ISNULL" target="_self" title="'.(isset($LANG['DOWNLOAD_RECS'])?$LANG['DOWNLOAD_RECS']:'Download Records').'"><img src="../../images/dl.png" style="width:12px;" /></a>';
								if($uploadType == $NFNUPLOAD) echo '<span style="margin-left:15px;color:orange">&gt;&gt; '.(isset($LANG['FAILED_LINK'])?$LANG['FAILED_LINK']:'Records failed to link to records within this collection and will not be imported').'</span>';
							}
							echo '</div>';
						}
						if(isset($reportArr['matchappend']) && $reportArr['matchappend']){
							echo '<div>'.(isset($LANG['MATCHING_CATALOG'])?$LANG['MATCHING_CATALOG']:'Records matching on catalog number that will be appended').': ';
							echo $reportArr['matchappend'];
							if($reportArr['matchappend']){
								echo ' <a href="uploadreviewer.php?collid='.$collid.'&searchvar=matchappend" target="_blank" title="'.(isset($LANG['PREVIEW'])?$LANG['PREVIEW']:'Preview 1st 1000 Records').'"><img src="../../images/list.png" style="width:12px;" /></a>';
								echo ' <a href="uploadreviewer.php?action=export&collid='.$collid.'&searchvar=matchappend" target="_self" title="'.(isset($LANG['DOWNLOAD_RECS'])?$LANG['DOWNLOAD_RECS']:'Download Records').'"><img src="../../images/dl.png" style="width:12px;" /></a>';
							}
							echo '</div>';
							echo '<div style="margin-left:15px;"><span style="color:orange;">'.(isset($LANG['WARNING'])?$LANG['WARNING']:'WARNING').':</span> ';
							echo (isset($LANG['WARNING_DUPES'])?$LANG['WARNING_DUPES']:'This will result in records with duplicate catalog numbers').'</div>';
						}
						if($uploadType != $NFNUPLOAD && $uploadType != $SKELETAL){
							if(isset($reportArr['sync']) && $reportArr['sync']){
								echo '<div>'.(isset($LANG['RECS_SYNC'])?$LANG['RECS_SYNC']:'Records that will be syncronized with central database').': ';
								echo $reportArr['sync'];
								if($reportArr['sync']){
									echo ' <a href="uploadreviewer.php?collid='.$collid.'&searchvar=sync" target="_blank" title="'.(isset($LANG['PREVIEW'])?$LANG['PREVIEW']:'Preview 1st 1000 Records').'"><img src="../../images/list.png" style="width:12px;" /></a>';
									echo ' <a href="uploadreviewer.php?action=export&collid='.$collid.'&searchvar=sync" target="_self" title="'.(isset($LANG['DOWNLOAD_RECS'])?$LANG['DOWNLOAD_RECS']:'Download Records').'"><img src="../../images/dl.png" style="width:12px;" /></a>';
								}
								echo '</div>';
								echo '<div style="margin-left:15px;">'.$LANG['EXPL_SYNC'].'.</div>';
								echo '<div style="margin-left:15px;"><span style="color:orange;">'.(isset($LANG['WARNING'])?$LANG['WARNING']:'WARNING').':</span> '.$LANG['WARNING_REPLACE'].'</div>';
							}
							if(isset($reportArr['exist']) && $reportArr['exist']){
								echo '<div>'.$LANG['NOT_MATCHING'].': '.$reportArr['exist'];
								if($reportArr['exist']){
									echo ' <a href="uploadreviewer.php?collid='.$collid.'&searchvar=exist" target="_blank" title="'.(isset($LANG['PREVIEW'])?$LANG['PREVIEW']:'Preview 1st 1000 Records').'"><img src="../../images/list.png" style="width:12px;" /></a>';
									echo ' <a href="uploadreviewer.php?action=export&collid='.$collid.'&searchvar=exist" target="_self" title="'.(isset($LANG['DOWNLOAD_RECS'])?$LANG['DOWNLOAD_RECS']:'Download Records').'"><img src="../../images/dl.png" style="width:12px;" /></a>';
								}
								echo '</div>';
								echo '<div style="margin-left:15px;">'.$LANG['EXPECTED'].'. '.$LANG['FULL_REFRESH'].'</div>';
							}
							if(isset($reportArr['nulldbpk']) && $reportArr['nulldbpk']){
								echo '<div style="color:red;">'.(isset($LANG['NULL_RM'])?$LANG['NULL_RM']:'Records that will be removed due to NULL Primary Identifier').': ';
								echo $reportArr['nulldbpk'];
								if($reportArr['nulldbpk']){
									echo ' <a href="uploadreviewer.php?collid='.$collid.'&searchvar=dbpk:ISNULL" target="_blank" title="'.(isset($LANG['PREVIEW'])?$LANG['PREVIEW']:'Preview 1st 1000 Records').'"><img src="../../images/list.png" style="width:12px;" /></a>';
									echo ' <a href="uploadreviewer.php?action=export&collid='.$collid.'&searchvar=dbpk:ISNULL" target="_self" title="'.(isset($LANG['DOWNLOAD_RECS'])?$LANG['DOWNLOAD_RECS']:'Download Records').'"><img src="../../images/dl.png" style="width:12px;" /></a>';
								}
								echo '</div>';
							}
							if(isset($reportArr['dupdbpk']) && $reportArr['dupdbpk']){
								echo '<div style="color:red;">'.(isset($LANG['DUP_RM'])?$LANG['DUP_RM']:'Records that will be removed due to DUPLICATE Primary Identifier').': ';
								echo $reportArr['dupdbpk'];
								if($reportArr['dupdbpk']){
									echo ' <a href="uploadreviewer.php?collid='.$collid.'&searchvar=dupdbpk" target="_blank" title="'.(isset($LANG['PREVIEW'])?$LANG['PREVIEW']:'Preview 1st 1000 Records').'"><img src="../../images/list.png" style="width:12px;" /></a>';
									echo ' <a href="uploadreviewer.php?action=export&collid='.$collid.'&searchvar=dupdbpk" target="_self" title="'.(isset($LANG['DOWNLOAD_RECS'])?$LANG['DOWNLOAD_RECS']:'Download Records').'"><img src="../../images/dl.png" style="width:12px;" /></a>';
								}
								echo '</div>';
							}
						}
						echo '</div>';
						//Extensions
						if(isset($reportArr['ident'])) echo '<div>'.$LANG['IDENT_TRANSFER'].': '.$reportArr['ident'].'</div>';
						if(isset($reportArr['image'])) echo '<div>'.$LANG['IMAGE_TRANSFER'].': '.$reportArr['image'].'</div>';
						?>
					</div>
					<form name="finaltransferform" action="specupload.php" method="post" style="margin-top:10px;" onsubmit="return confirm('<?php echo $LANG['FINAL_TRANSFER']; ?>');">
						<input type="hidden" name="collid" value="<?php echo $collid;?>" />
						<input type="hidden" name="uploadtype" value="<?php echo $uploadType; ?>" />
						<input type="hidden" name="observeruid" value="<?php echo $observerUid; ?>" />
						<input type="hidden" name="verifyimages" value="<?php echo ($verifyImages?'1':'0'); ?>" />
						<input type="hidden" name="processingstatus" value="<?php echo $processingStatus;?>" />
						<input type="hidden" name="uspid" value="<?php echo $uspid;?>" />
						<input type="hidden" name="sourceindex" value="<?php echo $sourceIndex;?>" />
						<div style="margin:5px;">
							<button type="submit" name="action" value="activateOccurrences"><?php echo (isset($LANG['TRANS_RECS'])?$LANG['TRANS_RECS']:'Transfer Records to Central Specimen Table'); ?></button>
						</div>
					</form>
				</fieldset>
				<?php
			}
		}
		elseif($action == 'activateOccurrences' || $finalTransfer){
			echo '<ul>';
			$duManager->finalTransfer();
			echo '</ul>';
		}
		else{
			$uploadTitle = $duManager->getTitle();
			if(!$uploadTitle){
				if($uploadType == $DWCAUPLOAD) $uploadTitle = 'Manual DwC-Archive Import';
				elseif($uploadType == $IPTUPLOAD) $uploadTitle = 'IPT/DwC-A Provider Import';
				elseif($uploadType == $SKELETAL) $uploadTitle = 'Skeletal File Import';
				elseif($uploadType == $FILEUPLOAD) $uploadTitle = 'Delimited Text File Import';
				elseif($uploadType == $NFNUPLOAD) $uploadTitle = 'Notes from Natural Import';
			}
			//Upload type is direct, file, or DWCA
			if(!$ulPath && ($uploadType == $FILEUPLOAD || $uploadType == $SKELETAL || $uploadType == $NFNUPLOAD || $uploadType == $DWCAUPLOAD || $uploadType == $IPTUPLOAD)){
				//Need to upload data for file and DWCA uploads
				$ulPath = $duManager->uploadFile();
				if(!$ulPath){
					//Still null, thus we have to upload file
					?>
					<form name="fileuploadform" action="specupload.php" method="post" enctype="multipart/form-data" onsubmit="return verifyFileUploadForm(this)">
						<fieldset style="width:95%;">
							<legend style="font-weight:bold;font-size:120%;<?php if($uploadType == $SKELETAL) echo 'background-color:lightgreen'; ?>"><?php echo $uploadTitle.': '.(isset($LANG['ID_SOURCE'])?$LANG['ID_SOURCE']:'Identify Data Source'); ?></legend>
							<div>
								<div style="margin:10px">
									<?php
									$pathLabel = (isset($LANG['IPT_URL'])?$LANG['IPT_URL']:'IPT Resource URL');
									if($uploadType != $IPTUPLOAD){
										$pathLabel = (isset($LANG['RES_URL'])?$LANG['RES_URL']:'Resource Path or URL');
										?>
										<div>
											<input name="uploadfile" type="file" size="50" onchange="verifyImageSize(this)" />
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
									<input name="matchothercatnum" type="hidden" value="<?php echo $matchOtherCatNum; ?>" />
									<input name="MAX_FILE_SIZE" type="hidden" value="100000000" />
								</div>
							</div>
						</fieldset>
					</form>
					<?php
				}
			}
			$processingList = array('unprocessed' => 'Unprocessed', 'stage 1' => 'Stage 1', 'stage 2' => 'Stage 2', 'stage 3' => 'STAGE_3', 'pending review' => 'Pending Review',
				'expert required' => 'Expert Required', 'pending review-nfn' => 'Pending Review-NfN', 'reviewed' => 'Reviewed', 'closed' => 'Closed');
			if($ulPath && ($uploadType == $DWCAUPLOAD || $uploadType == $IPTUPLOAD)){
				//Data has been uploaded and it's a DWCA upload type
				if($duManager->analyzeUpload()){
					$metaArr = $duManager->getMetaArr();
					if(isset($metaArr['occur'])){
						?>
						<form name="dwcauploadform" action="specupload.php" method="post" onsubmit="return verifyMappingForm(this)">
							<fieldset style="width:95%;">
								<legend style="font-weight:bold;font-size:120%;"><?php echo $uploadTitle.': Field Mapping';?></legend>
								<div style="margin:10px;">
									<b><?php echo (isset($LANG['SOURCE_ID'])?$LANG['SOURCE_ID']:'Source Unique Identifier / Primary Key'); ?> (<span style="color:red"><?php echo (isset($LANG['REQ'])?$LANG['REQ']:'required'); ?></span>): </b>
									<?php
									$dbpk = $duManager->getDbpk();
									$dbpkTitle = 'Core ID';
									if($dbpk == 'catalognumber') $dbpkTitle = 'Catalog Number';
									elseif($dbpk == 'occurrenceid') $dbpkTitle = 'Occurrence ID';
									echo $dbpkTitle;
									?>
									<div style="margin:10px;">
										<div>
											<input name="importspec" value="1" type="checkbox" checked />
											<?php echo (isset($LANG['IMPORT_OCCS'])?$LANG['IMPORT_OCCS']:'Import Occurrence Records'); ?> (<a href="#" onclick="toggle('dwcaOccurDiv');return false;"><?php echo (isset($LANG['VIEW_DETS'])?$LANG['VIEW_DETS']:'view details'); ?></a>)
										</div>
										<div id="dwcaOccurDiv" style="display:none;margin:20px;">
											<div style="margin-bottom:5px">
												<?php $duManager->echoFieldMapTable(true,'occur'); ?>
												<div>
													<?php echo '* '.(isset($LANG['UNVER'])?$LANG['UNVER']:'Unverified mappings are displayed in yellow'); ?>
												</div>
											</div>
											<fieldset>
												<legend><b><?php echo (isset($LANG['CUSTOM_FILT'])?$LANG['CUSTOM_FILT']:'Custom Occurrence Record Import Filters'); ?></b></legend>
												<?php
												$qArr = json_decode($duManager->getQueryStr(),true);
												$queryArr = array();
												if($qArr){
													foreach($qArr as $column => $aArr){
														foreach($aArr as $cond => $bArr){
															foreach($bArr as $v){
																$queryArr[] = array('col'=>$column,'cond'=>$cond,'val'=>$v);
															}
														}
													}
												}
												$sourceFields = $duManager->getSourceArr();
												sort($sourceFields);
												for($x=0;$x<3;$x++){
													$savedField = '';
													$savedCondition = '';
													$savedValue = '';
													if($action != 'Reset Field Mapping'){
														if(array_key_exists('filter'.$x, $_POST) && $_POST['filter'.$x]){
															$savedField = strtolower($_POST['filter'.$x]);
															$savedCondition = $_POST['condition'.$x];
															$savedValue = $_POST['value'.$x];
														}
														elseif(isset($queryArr[$x])){
															$savedField = $queryArr[$x]['col'];
															$savedCondition = $queryArr[$x]['cond'];
															$savedValue = $queryArr[$x]['val'];
														}
													}

													?>
													<div>
														<?php echo (isset($LANG['FIELD'])?$LANG['FIELD']:'Field'); ?>:
														<select name="filter<?php echo $x; ?>" style="margin-right:10px">
															<option value=""><?php echo (isset($LANG['SEL_FIELD'])?$LANG['SEL_FIELD']:'Select Field Name'); ?></option>
															<?php
															foreach($sourceFields as $f){
																echo '<option '.($savedField == strtolower($f)?'SELECTED':'').'>'.$f.'</option>';
															}
															?>
														</select>
														<?php echo (isset($LANG['COND'])?$LANG['COND']:'Condition'); ?>:
														<select name="condition<?php echo $x; ?>" style="margin-right:10px">
															<option value="EQUALS" <?php if($savedCondition == 'EQUALS') echo 'SELECTED'; ?>><?php echo (isset($LANG['EQUALS'])?$LANG['EQUALS']:'EQUALS'); ?></option>
															<option value="STARTS" <?php if($savedCondition == 'STARTS') echo 'SELECTED'; ?>><?php echo (isset($LANG['STARTS_WITH'])?$LANG['STARTS_WITH']:'STARTS WITH'); ?></option>
															<option value="LIKE" <?php if($savedCondition == 'LIKE') echo 'SELECTED'; ?>><?php echo (isset($LANG['CONTAINS'])?$LANG['CONTAINS']:'CONTAINS'); ?></option>
															<option value="LESSTHAN" <?php if($savedCondition == 'LESSTHAN') echo 'SELECTED'; ?>><?php echo (isset($LANG['LESS_THAN'])?$LANG['LESS_THAN']:'LESS THAN'); ?></option>
															<option value="GREATERTHAN" <?php if($savedCondition == 'GREATERTHAN') echo 'SELECTED'; ?>><?php echo (isset($LANG['GREATER_THAN'])?$LANG['GREATER_THAN']:'GREATER THAN'); ?></option>
															<option value="ISNULL" <?php if($savedCondition == 'ISNULL') echo 'SELECTED'; ?>><?php echo (isset($LANG['IS_NULL'])?$LANG['IS_NULL']:'IS NULL'); ?></option>
															<option value="NOTNULL" <?php if($savedCondition == 'NOTNULL') echo 'SELECTED'; ?>><?php echo (isset($LANG['NOT_NULL'])?$LANG['NOT_NULL']:'IS NOT NULL'); ?></option>
														</select>
														<?php echo (isset($LANG['VALUE'])?$LANG['VALUE']:'Value'); ?>:
														<input name="value<?php echo $x; ?>" type="text" value="<?php echo $savedValue; ?>" />
													</div>
													<?php
												}
												?>
												<div style="margin:5px"><?php echo '* '.(isset($LANG['MULT_TERMS'])?$LANG['MULT_TERMS']:'Adding multiple terms separated by semi-colon will filter as an OR condition'); ?></div>
											</fieldset>
										</div>
										<div>
											<input name="importident" value="1" type="checkbox" <?php echo (isset($metaArr['ident'])?'checked':'disabled') ?> />
											<?php
											echo (isset($LANG['IMPORT_ID'])?$LANG['IMPORT_ID']:'Import Identification History');
											if(isset($metaArr['ident'])){
												echo '(<a href="#" onclick="toggle(\'dwcaIdentDiv\');return false;">'.(isset($LANG['VIEW_DETS'])?$LANG['VIEW_DETS']:'view details').'</a>)';
												?>
												<div id="dwcaIdentDiv" style="display:none;margin:20px;">
													<?php $duManager->echoFieldMapTable(true,'ident'); ?>
													<div>
														<?php echo '* '.(isset($LANG['UNVER'])?$LANG['UNVER']:'Unverified mappings are displayed in yellow'); ?>
													</div>
												</div>
												<?php
											}
											else{
												echo '('.(isset($LANG['NOT_IN_DWC'])?$LANG['NOT_IN_DWC']:'not present in DwC-Archive').')';
											}
											?>

										</div>
										<div>
											<input name="importimage" value="1" type="checkbox" <?php echo (isset($metaArr['image'])?'checked':'disabled') ?> />
											<?php echo (isset($LANG['IMP_IMG'])?$LANG['IMP_IMG']:'Import Images'); ?>
											<?php
											if(isset($metaArr['image'])){
												echo '(<a href="#" onclick="toggle(\'dwcaImgDiv\');return false;">view details</a>)';
												?>
												<div id="dwcaImgDiv" style="display:none;margin:20px;">
													<?php $duManager->echoFieldMapTable(true,'image'); ?>
													<div>
														<?php echo '* '.(isset($LANG['UNVER'])?$LANG['UNVER']:'Unverified mappings are displayed in yellow'); ?>
													</div>
												</div>
												<?php
											}
											else{
												echo '('.(isset($LANG['NOT_IN_DWC'])?$LANG['NOT_IN_DWC']:'not present in DwC-Archive').')';
											}
											?>
										</div>
										<div style="margin:10px 0px;">
											<?php
											if($uspid) echo '<button type="submit" name="action" value="Reset Field Mapping">'.(isset($LANG['RESET_MAP'])?$LANG['RESET_MAP']:'Reset Field Mapping').'</button>';
											echo '<button name="action" type="submit" value="saveMapping" onclick="return verifySaveMapping(this.form)" style="margin-left:5px">'.(isset($LANG['SAVE_MAP'])?$LANG['SAVE_MAP']:'Save Mapping').'</button>';
											if(!$uspid) echo ' <span id="newProfileNameDiv" style="margin-left:15px;color:orange;display:none">'.$LANG['NEW_PROF_TITLE'].': <input type="text" name="profiletitle" style="width:300px" /></span>';
											?>

										</div>
										<div style="margin-top:30px;">
											<?php
											if($isLiveData){
												if($duManager->getCollInfo('colltype') == 'General Observations'){
													echo (isset($LANG['TARGET_USER'])?$LANG['TARGET_USER']:'Target User').': ';
													echo '<select name="observeruid">';
													echo '<option value="">'.(isset($LANG['SEL_TAR_USER'])?$LANG['SEL_TAR_USER']:'Select Target User').'</option>';
													echo '<option value="">----------------------------</option>';
													$obsUidArr = $duManager->getObserverUidArr();
													foreach($obsUidArr as $uid => $userName){
														echo '<option value="'.$uid.'">'.$userName.'</option>';
													}
													echo '</select>';
												}
												?>
												<div>
													<input name="matchcatnum" type="checkbox" value="1" checked />
													<?php echo (isset($LANG['MATCH_CAT'])?$LANG['MATCH_CAT']:'Match on Catalog Number'); ?>
												</div>
												<div>
													<input name="matchothercatnum" type="checkbox" value="1" <?php echo ($matchOtherCatNum?'checked':''); ?> />
													<?php echo (isset($LANG['MATCH_O_CAT'])?$LANG['MATCH_O_CAT']:'Match on Other Catalog Numbers'); ?>
												</div>
												<ul style="margin-top:2px">
													<li><?php echo $recReplaceMsg; ?></li>
													<li><?php echo $LANG['BOTH_CATS']; ?></li>
												</ul>
												<?php
											}
											?>
											<div style="margin:10px 0px;">
												<input name="verifyimages" type="checkbox" value="1" />
												<?php echo (isset($LANG['VER_LINKS'])?$LANG['VER_LINKS']:'Verify image links'); ?>
											</div>
											<div style="margin:10px 0px;">
												<?php echo (isset($LANG['PROC_STATUS'])?$LANG['PROC_STATUS']:'Processing Status'); ?>:
												<select name="processingstatus">
													<option value=""><?php echo (isset($LANG['NO_SETTING'])?$LANG['NO_SETTING']:'Leave as is / No Explicit Setting'); ?></option>
													<option value="">--------------------------</option>
													<?php
													foreach($processingList as $ps){
														echo '<option value="'.$ps.'">'.ucwords($ps).'</option>';
													}
													?>
												</select>
											</div>
											<div style="margin:10px;">
												<button type="submit" name="action" value="Start Upload"><?php echo (isset($LANG['START_UPLOAD'])?$LANG['START_UPLOAD']:'Start Upload'); ?></button>
												<input type="hidden" name="uspid" value="<?php echo $uspid;?>" />
												<input type="hidden" name="collid" value="<?php echo $collid;?>" />
												<input type="hidden" name="uploadtype" value="<?php echo $uploadType;?>" />
												<input type="hidden" name="ulpath" value="<?php echo $ulPath;?>" />
											</div>
										</div>
									</div>
								</div>
							</fieldset>
						</form>
						<?php
					}
				}
				else{
					if($duManager->getErrorStr()){
						echo '<div style="font-weight:bold;">'.$duManager->getErrorStr().'</div>';
					}
					else{
						echo '<div style="font-weight:bold;">'.(isset($LANG['UNK_ERR'])?$LANG['UNK_ERR']:'Unknown error analyzing upload').'</div>';
					}
				}
			}
			elseif($uploadType == $NFNUPLOAD && $ulPath){
				$duManager->analyzeUpload();
				?>
				<form name="filemappingform" action="specupload.php" method="post" onsubmit="return verifyMappingForm(this)">
					<fieldset style="width:95%;padding:15px">
						<legend style="font-weight:bold;font-size:120%;"><?php echo (isset($LANG['NFN_IMPORT'])?$LANG['NFN_IMPORT']:'Notes from Nature File Import'); ?></legend>
						<?php
						if($duManager->echoFieldMapTable(true, 'spec')){
							?>
							<div style="margin:10px 0px;">
								<?php echo (isset($LANG['PROC_STATUS'])?$LANG['PROC_STATUS']:'Processing Status'); ?>:
								<select name="processingstatus">
									<option value=""><?php echo (isset($LANG['NO_SETTING'])?$LANG['NO_SETTING']:'Leave as is / No Explicit Setting'); ?></option>
									<option value="">--------------------------</option>
									<?php
									foreach($processingList as $ps){
										echo '<option value="'.$ps.'">'.ucwords($ps).'</option>';
									}
									?>
								</select>
							</div>
							<div style="margin:20px;">
								<button type="submit" name="action" value="Start Upload"><?php echo (isset($LANG['START_UPLOAD'])?$LANG['START_UPLOAD']:'Start Upload'); ?></button>
							</div>
							<?php
						}
						?>
					</fieldset>
					<input name="matchcatnum" type="hidden" value="0" />
					<input name="matchothercatnum" type="hidden" value="0" />
					<input name="uspid" type="hidden" value="<?php echo $uspid;?>" />
					<input name="collid" type="hidden" value="<?php echo $collid;?>" />
					<input name="uploadtype" type="hidden" value="<?php echo $uploadType;?>" />
					<input name="ulpath" type="hidden" value="<?php echo $ulPath;?>" />
				</form>
				<?php
			}
			elseif($uploadType == $DIRECTUPLOAD || (($uploadType == $FILEUPLOAD || $uploadType == $SKELETAL) && $ulPath)){
				$duManager->analyzeUpload();
				?>
				<form name="filemappingform" action="specupload.php" method="post" onsubmit="return verifyMappingForm(this)">
					<fieldset style="width:95%;">
						<legend style="font-weight:bold;font-size:120%;<?php if($uploadType == $SKELETAL) echo 'background-color:lightgreen'; ?>"><?php echo $uploadTitle; ?></legend>
						<?php
						if(!$isLiveData && $uploadType != $SKELETAL){
							//Primary key field is required and must be mapped
							?>
							<div style="margin:20px;">
								<b><?php echo (isset($LANG['SOURCE_ID'])?$LANG['SOURCE_ID']:'Source Unique Identifier / Primary Key'); ?> (<span style="color:red"><?php echo (isset($LANG['REQ'])?$LANG['REQ']:'required'); ?></span>): </b>
								<?php
								$dbpk = $duManager->getDbpk();
								$dbpkOptions = $duManager->getDbpkOptions();
								?>
								<select name="dbpk" onchange="pkChanged(this);">
									<option value=""><?php echo (isset($LANG['SEL_KEY'])?$LANG['SEL_KEY']:'Select Source Primary Key'); ?></option>
									<option value="">----------------------------------</option>
									<?php
									foreach($dbpkOptions as $f){
										echo '<option value="'.strtolower($f).'" '.($dbpk==strtolower($f)?'SELECTED':'').'>'.$f.'</option>';
									}
									?>
								</select>
							</div>
							<?php
						}
						$displayStr = 'block';
						if(!$isLiveData) $displayStr = 'none';
						if($uploadType == $SKELETAL) $displayStr = 'block';
						if($dbpk) $displayStr = 'block';
						?>
						<div id="mdiv" style="display:<?php echo $displayStr; ?>">
							<?php $duManager->echoFieldMapTable($autoMap,'spec'); ?>
							<div>
								<?php
								echo '* '.$LANG['UNVER'].'<br/>';
								echo '* '.$LANG['SKIPPED'].'<br/>';
								echo '* '.$LANG['LEARN_MORE'].':';
								?>
								<div style="margin-left:15px;">
									<a href="https://symbiota.org/wp-content/uploads/SymbiotaOccurrenceFields.pdf" target="_blank">SymbiotaOccurrenceFields.pdf</a><br/>
									<a href="https://symbiota.org/symbiota-introduction/loading-specimen-data/" target="_blank"><?php echo (isset($LANG['LOADING_DATA'])?$LANG['LOADING_DATA']:'Loading Data into Symbiota'); ?></a>
								</div>
							</div>
							<div style="margin:10px;">
								<?php
								if($uspid){
									?>
									<button type="submit" name="action" value="Reset Field Mapping" ><?php echo (isset($LANG['RESET_MAP'])?$LANG['RESET_MAP']:'Reset Field Mapping'); ?></button>
									<?php
								}
								?>
								<button type="submit" name="action" value="Automap Fields" ><?php echo (isset($LANG['AUTOMAP'])?$LANG['AUTOMAP']:'Automap Fields'); ?></button>
								<button type="submit" name="action" value="Verify Mapping" ><?php echo (isset($LANG['VER_MAPPING'])?$LANG['VER_MAPPING']:'Verify Mapping'); ?></button>
								<button type="submit" name="action" value="saveMapping" onclick="return verifySaveMapping(this.form)" ><?php echo (isset($LANG['SAVE_MAP'])?$LANG['SAVE_MAP']:'Save Mapping'); ?></button>
								<span id="newProfileNameDiv" style="margin-left:15px;color:red;display:none">
									<?php echo (isset($LANG['NEW_PROF_TITLE'])?$LANG['NEW_PROF_TITLE']:'New profile title'); ?>:
									<input type="text" name="profiletitle" style="width:300px" />
								</span>
							</div>
							<hr />
							<div id="uldiv" style="margin-top:30px;">
								<?php
								if($isLiveData || $uploadType == $SKELETAL){
									if($duManager->getCollInfo('colltype') == 'General Observations'){
										echo (isset($LANG['TARGET_USER'])?$LANG['TARGET_USER']:'Target User').': ';
										echo '<select name="observeruid">';
										echo '<option value="">'.(isset($LANG['SEL_TAR_USER'])?$LANG['SEL_TAR_USER']:'Select Target User').'</option>';
										echo '<option value="">----------------------------</option>';
										$obsUidArr = $duManager->getObserverUidArr();
										foreach($obsUidArr as $uid => $userName){
											echo '<option value="'.$uid.'">'.$userName.'</option>';
										}
										echo '</select>';
									}
									?>
									<div>
										<input name="matchcatnum" type="checkbox" value="1" checked />
										<?php echo (isset($LANG['MATCH_CAT'])?$LANG['MATCH_CAT']:'Match on Catalog Number'); ?>
									</div>
									<div>
										<input name="matchothercatnum" type="checkbox" value="1" <?php echo ($matchOtherCatNum?'checked':''); ?> />
										<?php echo (isset($LANG['MATCH_ON_CAT'])?$LANG['MATCH_ON_CAT']:'Match on Other Catalog Numbers'); ?>
									</div>
									<ul style="margin-top:2px">
										<?php
										if($uploadType == $SKELETAL) echo '<li>'.$LANG['APPENDED'].'</li>';
										else echo '<li>'.$recReplaceMsg.'</li>';
										echo '<li>'.$LANG['BOTH_CATS'].'</li>';
										?>
									</ul>
									<?php
								}
								?>
								<div style="margin:10px 0px;">
									<input name="verifyimages" type="checkbox" value="1" />
									<?php echo (isset($LANG['VER_LINKS_MEDIA'])?$LANG['VER_LINKS_MEDIA']:'Verify image links from associatedMedia field'); ?>
								</div>
								<div style="margin:10px 0px;">
									<?php echo (isset($LANG['PROC_STATUS'])?$LANG['PROC_STATUS']:'Processing Status'); ?>:
									<select name="processingstatus">
										<option value=""><?php echo (isset($LANG['NO_SETTING'])?$LANG['NO_SETTING']:'Leave as is / No Explicit Setting'); ?></option>
										<option value="">--------------------------</option>
										<?php
										foreach($processingList as $ps){
											echo '<option value="'.$ps.'">'.ucwords($ps).'</option>';
										}
										?>
									</select>
								</div>
								<div style="margin:20px;">
									<button type="submit" name="action" value="Start Upload"><?php echo (isset($LANG['START_UPLOAD'])?$LANG['START_UPLOAD']:'Start Upload'); ?></button>
								</div>
							</div>
							<?php
							if($uploadType == $SKELETAL){
								echo '<div style="margin-top:15px;">';
								echo (isset($LANG['SKEL_EXPLAIN'])?$LANG['SKEL_EXPLAIN']:'');
								echo '<ul>';
								echo '<li>'.(isset($LANG['SKEL_EXPLAIN_P1'])?$LANG['SKEL_EXPLAIN_P1']:'').'</li>';
								echo '<li>'.(isset($LANG['SKEL_EXPLAIN_P2'])?$LANG['SKEL_EXPLAIN_P2']:'').'</li>';
								echo '<li>'.(isset($LANG['SKEL_EXPLAIN_P3'])?$LANG['SKEL_EXPLAIN_P3']:'').'</li>';
								echo '<li>'.(isset($LANG['SKEL_EXPLAIN_P4'])?$LANG['SKEL_EXPLAIN_P4']:'').'</li>';
								echo '</ul>';
								echo '</div>';
							}
							?>
						</div>
					</fieldset>
					<input type="hidden" name="uspid" value="<?php echo $uspid;?>" />
					<input type="hidden" name="collid" value="<?php echo $collid;?>" />
					<input type="hidden" name="uploadtype" value="<?php echo $uploadType;?>" />
					<input type="hidden" name="ulpath" value="<?php echo $ulPath;?>" />
				</form>
				<?php
			}
		}
	}
	else{
		if(!$isEditor || !$collid){
			echo '<div style="font-weight:bold;font-size:120%;">'.(isset($LANG['NOT_AUTH'])?$LANG['NOT_AUTH']:'ERROR: you are not authorized to upload to this collection').'</div>';
		}
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
