<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SpecUploadDirect.php');
include_once($SERVER_ROOT.'/classes/SpecUploadFile.php');
include_once($SERVER_ROOT.'/classes/SpecUploadDwca.php');

if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/collections/admin/specupload.' . $LANG_TAG . '.php'))
	include_once($SERVER_ROOT . '/content/lang/collections/admin/specupload.' . $LANG_TAG . '.php');
else include_once($SERVER_ROOT . '/content/lang/collections/admin/specupload.en.php');
header('Content-Type: text/html; charset=' . $CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/admin/specupload.php?' . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = !empty($_REQUEST['collid']) ? filter_var($_REQUEST['collid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$uploadType = !empty($_REQUEST['uploadtype']) ? $_REQUEST['uploadtype'] : '';		//Sanitized after uspid parsing
$uspid = !empty($_REQUEST['uspid']) ? $_REQUEST['uspid'] : '';					//Sanitized after uspid parsing
$autoMap = array_key_exists('automap', $_POST) ? true : false;
$ulPath = !empty($_REQUEST['ulpath']) ? $_REQUEST['ulpath'] : '';
$importIdent = array_key_exists('importident', $_REQUEST) ? true : false;
$importImage = array_key_exists('importimage', $_REQUEST) ? true : false;
$observerUid = !empty($_POST['observeruid']) ? filter_var($_POST['observeruid'], FILTER_SANITIZE_NUMBER_INT) : '';
$updateAction = !empty($_REQUEST['updateaction']) ? $_REQUEST['updateaction'] : '';
$matchCatNum = array_key_exists('matchcatnum', $_REQUEST) ? true : false;
$matchOtherCatNum = !empty($_REQUEST['matchothercatnum']) ? true : false;
$versionData = !empty($_REQUEST['versiondata']) ? true : false;
$verifyImages = !empty($_REQUEST['verifyimages']) ? true : false;
$processingStatus = !empty($_REQUEST['processingstatus']) ? $_REQUEST['processingstatus']: '';
$dbpk = !empty($_REQUEST['dbpk']) ? $_REQUEST['dbpk']: '';
$action = !empty($_REQUEST['action']) ? $_REQUEST['action'] : '';

if(strpos($uspid,'-')){
	$tok = explode('-',$uspid);
	$uspid = $tok[0];
	$uploadType = $tok[1];
}

//Sanitation
$uspid = filter_var($uspid, FILTER_SANITIZE_NUMBER_INT);
$uploadType = filter_var($uploadType, FILTER_SANITIZE_NUMBER_INT);
if(!preg_match('/^[a-zA-Z0-9\s_-]+$/', $processingStatus)) $processingStatus = '';

$FILEUPLOAD = 3; $DWCAUPLOAD = 6; $SKELETAL = 7; $IPTUPLOAD = 8; $NFNUPLOAD = 9; $SYMBIOTA = 13;

$duManager = new SpecUploadBase();
if($uploadType == $FILEUPLOAD || $uploadType == $NFNUPLOAD){
	$duManager = new SpecUploadFile();
	$duManager->setUploadFileName($ulPath);
}
elseif($uploadType == $SKELETAL){
	$duManager = new SpecUploadFile();
	$duManager->setUploadFileName($ulPath);
	$matchCatNum = true;
}
elseif($uploadType == $DWCAUPLOAD || $uploadType == $IPTUPLOAD || $uploadType == $SYMBIOTA){
	$duManager = new SpecUploadDwca();
	$duManager->setTargetPath($ulPath);
	$duManager->setIncludeIdentificationHistory($importIdent);
	$duManager->setIncludeImages($importImage);
	for($i=0;$i<3;$i++){
		if(isset($_POST['filter'.$i])){
			$duManager->addFilterCondition($_POST['filter'.$i], $_POST['condition'.$i], $_POST['value'.$i]);
		}
	}
	if(array_key_exists('publicationGuid',$_REQUEST)) $duManager->setPublicationGuid($_REQUEST['publicationGuid']);
}

$duManager->setCollId($collid);
$duManager->setUspid($uspid);
$duManager->setUploadType($uploadType);
$duManager->setObserverUid($observerUid);
$duManager->setMatchCatalogNumber($matchCatNum);
$duManager->setMatchOtherCatalogNumbers($matchOtherCatNum);
$duManager->setVersionDataEdits($versionData);
$duManager->setVerifyImageUrls($verifyImages);
$duManager->setProcessingStatus($processingStatus);

if($action == 'Automap Fields') $autoMap = true;

$statusStr = '';
$isEditor = 0;
if($IS_ADMIN) $isEditor = 1;
elseif(array_key_exists('CollAdmin', $USER_RIGHTS) && in_array($collid, $USER_RIGHTS['CollAdmin'])) $isEditor = 1;
if($isEditor && $collid){
	$duManager->readUploadParameters();
	$isLiveData = false;
	if($duManager->getCollInfo('managementtype') == 'Live Data') $isLiveData = true;

	//Grab field mapping, if mapping form was submitted
	if($action == 'Reset Field Mapping') $statusStr = $duManager->deleteFieldMap();
	else{
		$duManager->setFieldMaps($_POST);
		if($action == 'saveMapping'){
			$statusStr = $duManager->saveFieldMap($_POST);
			if(!$uspid) $uspid = $duManager->getUspid();
		}
	}
	$duManager->loadFieldMap();
}
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET ?>">
	<title><?= $DEFAULT_TITLE . ' ' . $LANG['SPEC_UPLOAD'] ?></title>
	<link href="<?= $CSS_BASE_PATH ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script src="../../js/symb/shared.js" type="text/javascript"></script>
	<script>
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
						alert("<?= $LANG['ERR_UNIQUE_D'] ?>"+obj.value+")");
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
							alert("<?= $LANG['ERR_UNIQUE_ID'] ?>"+obj.value+")");
							return false;
						}
						idSfArr[idSfArr.length] = obj.value;
					}
				}
				else if(obj.name == "IM-sf[]"){
					if(f.importimage.value == "1"){
						if(imSfArr.indexOf(obj.value) > -1){
							alert("<?= $LANG['ERR_UNIQUE_IM'] ?>"+obj.value+")");
							return false;
						}
						imSfArr[imSfArr.length] = obj.value;
					}
				}
				else if(obj.value != "" && obj.value != "unmapped"){
					if(obj.name == "tf[]"){
						if(tfArr.indexOf(obj.value) > -1){
							alert("<?= $LANG['SAME_TARGET_D'] ?>"+obj.value+")");
							return false;
						}
						tfArr[tfArr.length] = obj.value;
					}
					else if(obj.name == "ID-tf[]"){
						if(f.importident.value == "1"){
							if(idTfArr.indexOf(obj.value) > -1){
								alert("<?= $LANG['SAME_TARGET_ID'] ?>"+obj.value+")");
								return false;
							}
							idTfArr[idTfArr.length] = obj.value;
						}
					}
					else if(obj.name == "IM-tf[]"){
						if(f.importimage.value == "1"){
							if(imTfArr.indexOf(obj.value) > -1){
								alert("<?= $LANG['SAME_TARGET_IM'] ?>"+obj.value+")");
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
					alert("<?= $LANG['NEED_CAT'] ?>");
					return false;
				}
				else if(f.matchcatnum.checked == false && f.matchothercatnum.checked == false){
					alert("<?= $LANG['SEL_MATCH'] ?>");
					return false;
				}
				else{
					if((catalogNumberIndex == 1 && f.matchcatnum.checked == false) || (catalogNumberIndex == 2 && f.matchothercatnum.checked == false)){
						alert("<?= $LANG['ID_NOT_MATCH'] ?>");
						return false;
					}
				}
			}
			if(possibleMappingErr){
				return confirm("<?= $LANG['FIRST_ROW'] ?>");
			}
			return true;
		}

		function verifySaveMapping(f){
			if(f.uspid.value == 0 && $("#newProfileNameDiv").is(':visible')==false){
				$("#newProfileNameDiv").show();
				alert("<?= $LANG['ENTER_PROF'] ?>");
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
	<style>
		.unmapped{ background: yellow; }
		fieldset{  padding: 15px; }
		legend{ font-weight: bold; }
		.field-div{ margin: 10px 0px; }
	</style>
</head>
<body>
	<?php
$displayLeftMenu = (isset($collections_admin_specuploadMenu) ? $collections_admin_specuploadMenu:false);
include($SERVER_ROOT.'/includes/header.php');
?>
<div class="navpath">
	<a href="../../index.php"><?= $LANG['HOME'] ?></a> &gt;&gt;
	<a href="../misc/collprofiles.php?collid=<?= $collid ?>&emode=1"><?= $LANG['COL_MGMNT'] ?></a> &gt;&gt;
	<a href="specuploadmanagement.php?collid=<?= $collid ?>"><?= $LANG['LIST_UPLOAD'] ?></a> &gt;&gt;
	<b><?= $LANG['SPEC_UPLOAD'] ?></b>
</div>
<div role="main" id="innertext">
	<h1 class="page-heading"><?= $LANG['UP_MODULE']; ?></h1>
	<?php
	if($statusStr){
		echo '<hr />';
		echo '<div>'.$statusStr.'</div>';
		echo '<hr />';
	}
	$recReplaceMsg = '<span style="color:orange"><b>' .  $LANG['CAUTION'] . ':</b></span> ' . $LANG['REC_REPLACE'] ;
	if($isEditor && $collid){
		//Grab collection name and last upload date and display for all
		echo '<div style="font-weight:bold;font-size:130%;">'.$duManager->getCollInfo('name').'</div>';
		echo '<div style="margin:0px 0px 15px 15px;"><b>' . $LANG['LAST_UPLOAD_DATE'] . ':</b> ' . ($duManager->getCollInfo('uploaddate') ? $duManager->getCollInfo('uploaddate') : $LANG['NOT_REC'] ) . '</div>';
		$processingList = array('unprocessed' => 'Unprocessed', 'stage 1' => 'Stage 1', 'stage 2' => 'Stage 2', 'stage 3' => 'stage 3', 'pending review' => 'Pending Review',
			'expert required' => 'Expert Required', 'pending review-nfn' => 'Pending Review-NfN', 'reviewed' => 'Reviewed', 'closed' => 'Closed');
		if(!$ulPath) $ulPath = $duManager->uploadFile();
		if(($uploadType == $DWCAUPLOAD || $uploadType == $IPTUPLOAD || $uploadType == $SYMBIOTA) && $ulPath){
			//Data has been uploaded and it's a DWCA upload type
			if($duManager->analyzeUpload()){
				$metaArr = $duManager->getMetaArr();
				if(isset($metaArr['occur'])){
					?>
					<form name="dwcauploadform" action="specuploadmap.php" method="post" onsubmit="return verifyMappingForm(this)">
						<fieldset style="width:95%;">
							<legend><?= $duManager->getTitle() ?></legend>
							<div style="margin:10px;">
								<b><?= $LANG['SOURCE_ID'] ?> (<span style="color:red"><?= $LANG['REQ'] ?></span>): </b>
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
										<?= $LANG['IMPORT_OCCS'] ?> (<a href="#" onclick="toggle('dwcaOccurDiv');return false;"><?= $LANG['VIEW_DETS'] ?></a>)
									</div>
									<div id="dwcaOccurDiv" style="display:none;margin:20px;">
										<div style="margin-bottom:5px">
											<?php $duManager->echoFieldMapTable(true,'occur'); ?>
											<div>
												<?= '* ' . $LANG['UNVER'] ?>
											</div>
										</div>
										<fieldset>
											<legend><?= $LANG['CUSTOM_FILT'] ?></legend>
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
													<?= $LANG['FIELD'] ?>:
													<select name="filter<?= $x ?>" style="margin-right:10px">
														<option value=""><?= $LANG['SEL_FIELD'] ?></option>
														<?php
														foreach($sourceFields as $f){
															echo '<option '.($savedField == strtolower($f) ? 'SELECTED' : '').'>'.$f.'</option>';
														}
														?>
													</select>
													<?= $LANG['COND'] ?>:
													<select name="condition<?= $x ?>" style="margin-right:10px">
														<option value="EQUALS" <?php if($savedCondition == 'EQUALS') echo 'SELECTED'; ?>><?= $LANG['EQUALS'] ?></option>
														<option value="STARTS_WITH" <?php if($savedCondition == 'STARTS_WITH') echo 'SELECTED'; ?>><?= $LANG['STARTS_WITH'] ?></option>
														<option value="LIKE" <?php if($savedCondition == 'LIKE') echo 'SELECTED'; ?>><?= $LANG['LIKE'] ?></option>
														<option value="LESS_THAN" <?php if($savedCondition == 'LESS_THAN') echo 'SELECTED'; ?>><?= $LANG['LESS_THAN'] ?></option>
														<option value="GREATER_THAN" <?php if($savedCondition == 'GREATER_THAN') echo 'SELECTED'; ?>><?= $LANG['GREATER_THAN'] ?></option>
														<option value="IS_NULL" <?php if($savedCondition == 'IS_NULL') echo 'SELECTED'; ?>><?= $LANG['IS_NULL'] ?></option>
														<option value="NOT_NULL" <?php if($savedCondition == 'NOT_NULL') echo 'SELECTED'; ?>><?= $LANG['NOT_NULL'] ?></option>
													</select>
													<?= $LANG['VALUE'] ?>:
													<input name="value<?= $x ?>" type="text" value="<?= $savedValue ?>" />
												</div>
												<?php
											}
											?>
											<div style="margin:5px"><?= '* ' . $LANG['MULT_TERMS'] ?></div>
										</fieldset>
									</div>
									<div>
										<input name="importident" value="1" type="checkbox" <?= (isset($metaArr['ident']) ? 'checked' : 'disabled') ?> />
										<?php
										echo $LANG['IMPORT_ID'];
										if(isset($metaArr['ident'])){
											echo '(<a href="#" onclick="toggle(\'dwcaIdentDiv\');return false;">' . $LANG['VIEW_DETS'] . '</a>)';
											?>
											<div id="dwcaIdentDiv" style="display:none;margin:20px;">
												<?php $duManager->echoFieldMapTable(true,'ident'); ?>
												<div>
													<?= '* ' . $LANG['UNVER'] ?>
												</div>
											</div>
											<?php
										}
										else{
											echo '('. $LANG['NOT_IN_DWC'] . ')';
										}
										?>
									</div>
									<div>
										<input name="importimage" value="1" type="checkbox" <?= (isset($metaArr['image']) ? 'checked' : 'disabled') ?> />
										<?php
										echo $LANG['IMP_IMG'];
										if(isset($metaArr['image'])){
											echo '(<a href="#" onclick="toggle(\'dwcaImgDiv\');return false;">view details</a>)';
											?>
											<div id="dwcaImgDiv" style="display:none;margin:20px;">
												<?php $duManager->echoFieldMapTable(true,'image'); ?>
												<div>
													<?= '* ' . $LANG['UNVER'] ?>
												</div>
											</div>
											<?php
										}
										else{
											echo '('. $LANG['NOT_IN_DWC'] . ')';
										}
										?>
									</div>
									<div class="field-div">
										<?php
										if($uspid) echo '<button type="submit" name="action" value="Reset Field Mapping">' . $LANG['RESET_MAP'] . '</button>';
										echo '<button name="action" type="submit" value="saveMapping" onclick="return verifySaveMapping(this.form)" style="margin-left:5px">' . $LANG['SAVE_MAP'] . '</button> ';
										if(!$uspid){
											echo '<span id="newProfileNameDiv" style="margin-left:15px;color:orange;display:none">';
											echo $LANG['NEW_PROF_TITLE'].': <input type="text" name="profiletitle" style="width:300px" value="'.$duManager->getTitle().'-'.date('Y-m-d').'" />';
											echo '</span>';
										}
										?>

									</div>
									<div style="margin-top:30px;">
										<?php
										if($isLiveData){
											if($duManager->getCollInfo('colltype') == 'General Observations'){
												echo  $LANG['TARGET_USER'] . ': ';
												?>
												<select name="observeruid" required>
													<option value=""><?= $LANG['SEL_TAR_USER']  ?></option>
													<option value="">----------------------------</option>
													<?php
													$obsUidArr = $duManager->getObserverUidArr();
													foreach($obsUidArr as $uid => $userName){
														echo '<option value="'.$uid.'" '.($uid==$observerUid ? 'selected' : '').'>'.$userName.'</option>';
													}
													?>
												</select>
												<?php
											}
											?>
											<div>
												<input name="matchcatnum" type="checkbox" value="1" checked />
												<?= $LANG['MATCH_CAT'] ?>
											</div>
											<div>
												<input name="matchothercatnum" type="checkbox" value="1" <?= ($matchOtherCatNum ? 'checked' : '') ?> />
												<?= $LANG['MATCH_O_CAT'] ?>
											</div>
											<ul style="margin-top:2px">
												<li><?= $recReplaceMsg; ?></li>
												<li><?= $LANG['BOTH_CATS']; ?></li>
											</ul>
											<?php
										}
										?>
										<div style="margin:10px 0px;">
											<input name="verifyimages" type="checkbox" value="1" />
											<?= $LANG['VER_LINKS'] ?>
										</div>
										<div class="field-div">
											<?= $LANG['PROC_STATUS'] ?>:
											<select name="processingstatus">
												<option value=""><?= $LANG['NO_SETTING'] ?></option>
												<option value="">--------------------------</option>
												<?php
												foreach($processingList as $ps){
													echo '<option value="'.$ps.'">'.ucwords($ps).'</option>';
												}
												?>
											</select>
										</div>
										<div style="margin:10px;">
											<button type="submit" name="action" value="Start Upload" onclick="this.form.action = 'specuploadprocessor.php'">
												<?= $LANG['START_UPLOAD'] ?>
											</button>
											<input type="hidden" name="uspid" value="<?= $uspid;?>" />
											<input type="hidden" name="collid" value="<?= $collid;?>" />
											<input type="hidden" name="publicationGuid" value="<?= $duManager->getPublicationGuid();?>" />
											<input type="hidden" name="uploadtype" value="<?= $uploadType ?>" />
											<input type="hidden" name="ulpath" value="<?= htmlspecialchars($ulPath, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) ?>" />
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
				if($duManager->getErrorStr()) echo '<div style="font-weight:bold;">' . $duManager->getErrorStr() . '</div>';
				else echo '<div style="font-weight:bold;">' . $LANG['UNK_ERR'] . '</div>';
			}
		}
		elseif($uploadType == $NFNUPLOAD && $ulPath){
			$duManager->analyzeUpload();
			?>
			<form name="filemappingform" action="specuploadprocessor.php" method="post" onsubmit="return verifyMappingForm(this)">
				<fieldset style="width:95%">
					<legend><?= $LANG['NFN_IMPORT'] ?></legend>
					<?php
					if($duManager->echoFieldMapTable(true, 'spec')){
						?>
						<div style="margin:10px 0px;">
							<?= $LANG['PROC_STATUS'] ?>:
							<select name="processingstatus">
								<option value=""><?= $LANG['NO_SETTING'] ?></option>
								<option value="">--------------------------</option>
								<?php
								foreach($processingList as $ps){
									echo '<option value="'.$ps.'">'.ucwords($ps).'</option>';
								}
								?>
							</select>
						</div>
						<div style="margin:20px;">
							<button type="submit" name="action" value="Start Upload"><?= $LANG['START_UPLOAD'] ?></button>
						</div>
						<?php
					}
					?>
				</fieldset>
				<input name="matchcatnum" type="hidden" value="0" />
				<input name="matchothercatnum" type="hidden" value="0" />
				<input name="uspid" type="hidden" value="<?= $uspid ?>" />
				<input name="collid" type="hidden" value="<?= $collid ?>" />
				<input name="uploadtype" type="hidden" value="<?= $uploadType ?>" />
				<input name="ulpath" type="hidden" value="<?= htmlspecialchars($ulPath, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) ?>" />
			</form>
			<?php
		}
		elseif(($uploadType == $FILEUPLOAD || $uploadType == $SKELETAL) && $ulPath){
			$duManager->analyzeUpload();
			?>
			<form name="filemappingform" action="specuploadmap.php" method="post" onsubmit="return verifyMappingForm(this)">
				<fieldset style="width:95%;">
					<legend style="<?php if($uploadType == $SKELETAL) echo 'background-color:lightgreen'; ?>"><?= $duManager->getTitle(); ?></legend>
					<?php
					if(!$isLiveData && $uploadType != $SKELETAL){
						//Primary key field is required and must be mapped
						?>
						<div style="margin:20px;">
							<b><?= $LANG['SOURCE_ID'] ?> (<span style="color:red"><?= $LANG['REQ'] ?></span>): </b>
							<?php
							$dbpk = $duManager->getDbpk();
							$dbpkOptions = $duManager->getDbpkOptions();
							?>
							<select name="dbpk" onchange="pkChanged(this);">
								<option value=""><?= $LANG['SEL_KEY'] ?></option>
								<option value="">----------------------------------</option>
								<?php
								foreach($dbpkOptions as $f){
									echo '<option value="'.strtolower($f).'" '.($dbpk==strtolower($f) ? 'SELECTED' : '').'>'.$f.'</option>';
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
					<div id="mdiv" style="display:<?= $displayStr; ?>">
						<?php $duManager->echoFieldMapTable($autoMap,'spec'); ?>
						<div>
							<ul>
								<li><?= $LANG['UNVER'] ?></li>
								<li><?= $LANG['SKIPPED'] ?></li>
								<li><?= $LANG['LEARN_MORE'] ?>
									<ul>
									<li><a href="https://symbiota.org/wp-content/uploads/SymbiotaOccurrenceFields.pdf" target="_blank">SymbiotaOccurrenceFields.pdf</a></li>
									<li><a href="https://symbiota.org/symbiota-introduction/loading-specimen-data/" target="_blank"><?= $LANG['LOADING_DATA'] ?></a></li>
								</ul></li>
							</ul>
							<ul>
						</div>
						<div style="margin: 20px 5px;">
							<?php
							if($uspid){
								?>
								<button type="submit" name="action" value="Reset Field Mapping" ><?= $LANG['RESET_MAP'] ?></button>
								<?php
							}
							?>
							<button class="bottom-breathing-room-rel-sm" type="submit" name="action" value="Automap Fields" ><?= $LANG['AUTOMAP'] ?></button>
							<button class="bottom-breathing-room-rel-sm" type="submit" name="action" value="Verify Mapping" ><?= $LANG['VER_MAPPING'] ?></button>
							<button class="bottom-breathing-room-rel-sm" type="submit" name="action" value="saveMapping" onclick="return verifySaveMapping(this.form)" ><?= $LANG['SAVE_MAP'] ?></button>
							<span id="newProfileNameDiv" style="margin-left:15px;color:red;display:none">
								<?= $LANG['NEW_PROF_TITLE'] ?>:
								<input type="text" name="profiletitle" style="width:300px" value="<?= $duManager->getTitle().'-'.date('Y-m-d'); ?>" />
							</span>
						</div>
						<hr />
						<div id="uldiv" style="margin-top:30px;">
							<div class="field-div">
								<select name="updateaction" required>
									<option value="">Select Update Type</option>
									<option value="">-------------------</option>
									<option value="updateTargetedFields" <?php if($updateAction == 'updateTargetedFields') echo 'selected'; ?>>Update Only Fields in File</option>
									<option value="skeletalUpdate" <?php if($updateAction == 'skeletalUpdate') echo 'selected'; ?>>Skeletal Update (only empty fields)</option>
									<option value="replaceFullRecord" <?php if($updateAction == 'replaceFullRecord') echo 'selected'; ?>>Replace Full Record</option>
								</select>
							</div>
							<?php
							if($isLiveData || $uploadType == $SKELETAL){
								if($duManager->getCollInfo('colltype') == 'General Observations'){
									echo $LANG['TARGET_USER'] . ': ';
									echo '<div><select name="observeruid">';
									echo '<option value="">' . $LANG['SEL_TAR_USER'] . '</option>';
									echo '<option value="">----------------------------</option>';
									$obsUidArr = $duManager->getObserverUidArr();
									foreach($obsUidArr as $uid => $userName){
										echo '<option value="'.$uid.'">'.$userName.'</option>';
									}
									echo '</select></div>';
								}
								?>
								<div>
									<input name="matchcatnum" type="checkbox" value="1" checked />
									<?= $LANG['MATCH_CAT'] ?>
								</div>
								<div>
									<input name="matchothercatnum" type="checkbox" value="1" <?= ($matchOtherCatNum ? 'checked' : '') ?> />
									<?= $LANG['MATCH_ON_CAT'] ?>
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
							if($isLiveData){
								?>
								<div class="field-div">
									<input name="versiondata" type="checkbox" value="1">
									<?= $LANG['VERSION_DATA_CHANGES'] ?>
								</div>
								<?php
							}
							?>
							<div class="field-div">
								<input name="verifyimages" type="checkbox" value="1" />
								<?= $LANG['VER_LINKS_MEDIA'] ?>
							</div>
							<div class="field-div">
								<?= $LANG['PROC_STATUS'] ?>:
								<select name="processingstatus">
									<option value=""><?= $LANG['NO_SETTING'] ?></option>
									<option value="">--------------------------</option>
									<?php
									foreach($processingList as $ps){
										echo '<option value="'.$ps.'">'.ucwords($ps).'</option>';
									}
									?>
								</select>
							</div>
							<div style="margin:20px;">
								<button type="submit" name="action" value="Start Upload" onclick="this.form.action = 'specuploadprocessor.php'">
									<?= $LANG['START_UPLOAD']  ?>
								</button>
							</div>
						</div>
						<?php
						if($uploadType == $SKELETAL){
							echo '<div style="margin-top:15px;">';
							echo (isset($LANG['SKEL_EXPLAIN']) ? $LANG['SKEL_EXPLAIN'] : '');
							echo '<ul>';
							echo '<li>' . $LANG['SKEL_EXPLAIN_P1'] . '</li>';
							echo '<li>' . $LANG['SKEL_EXPLAIN_P2'] . '</li>';
							echo '<li>' . $LANG['SKEL_EXPLAIN_P3'] . '</li>';
							echo '<li>' . $LANG['SKEL_EXPLAIN_P4'] . '</li>';
							echo '</ul>';
							echo '</div>';
						}
						?>
					</div>
				</fieldset>
				<input type="hidden" name="uspid" value="<?= $uspid ?>" />
				<input type="hidden" name="collid" value="<?= $collid ?>" />
				<input type="hidden" name="uploadtype" value="<?= $uploadType ?>" />
				<input type="hidden" name="ulpath" value="<?= htmlspecialchars($ulPath, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) ?>" />
			</form>
			<?php
		}
	}
	else{
		if(!$isEditor || !$collid) echo '<div style="font-weight:bold;font-size:120%;">' . $LANG['NOT_AUTH'] . '</div>';
		else{
			echo '<div style="font-weight:bold;font-size:120%;">';
			echo $LANG['PAGE_ERROR'] . ' = ';
			echo ini_get("upload_max_filesize").'; post_max_size = '.ini_get('post_max_size');
			echo $LANG['USE_BACK'];
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