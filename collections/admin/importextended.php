<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceImport.php');
if($LANG_TAG != 'en' && !file_exists($SERVER_ROOT.'/content/lang/collections/admin/importextended.'.$LANG_TAG.'.php')) $LANG_TAG = 'en';
include_once($SERVER_ROOT.'/content/lang/collections/admin/importextended.'.$LANG_TAG.'.php');
header('Content-Type: text/html; charset=' . $CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/admin/importextended.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = array_key_exists('collid', $_REQUEST) ? $_REQUEST['collid'] : 0;
$importType = array_key_exists('importType', $_REQUEST) ? $_REQUEST['importType'] : 0;
$createNew = array_key_exists('createNew', $_POST) ? $_POST['createNew'] : 0;
$fileName = array_key_exists('fileName', $_POST) ? $_POST['fileName'] : 0;
$action = array_key_exists('submitAction', $_POST) ? $_POST['submitAction'] : '';

//Sanitation
$collid = filter_var($collid, FILTER_SANITIZE_NUMBER_INT);
$importType = filter_var($importType, FILTER_SANITIZE_NUMBER_INT);
$createNew = filter_var($createNew, FILTER_SANITIZE_NUMBER_INT);

$importManager = new OccurrenceImport();
$importManager->setCollid($collid);
$importManager->setImportType($importType);
$importManager->setImportFileName($fileName);

$isEditor = false;
if($IS_ADMIN || (array_key_exists('CollAdmin', $USER_RIGHTS) && in_array($collid, $USER_RIGHTS['CollAdmin']))){
	$isEditor = true;
}
?>
<html>
	<head>
		<title><?= $DEFAULT_TITLE ?> Import Extended Data</title>
		<?php
		include_once($SERVER_ROOT.'/includes/head.php');
		?>

		<script>
			function verifyFileSize(inputObj){
				if (!window.FileReader) {
					//alert("The file API isn't supported on this browser yet.");
					return;
				}
				<?php
				$maxUpload = ini_get('upload_max_filesize');
				$maxUpload = str_replace("M", "000000", $maxUpload);
				if($maxUpload > 10000000) $maxUpload = 10000000;
				echo 'var maxUpload = '.$maxUpload.";\n";
				?>
				var file = inputObj.files[0];
				if(file.size > maxUpload){
					var msg = "<?= $LANG['IMPORT_FILE'] ?>"+file.name+" ("+Math.round(file.size/100000)/10+"<?= $LANG['IS_BIGGER'] ?>"+(maxUpload/1000000)+"MB).";
					if(file.name.slice(-3) != "zip") msg = msg + "<?= $LANG['MAYBE_ZIP'] ?>";
					alert(msg);
				}
			}

			function validateInitiateForm(f){
				if(f.importfile.value == ""){
					alert("Select a file to import");
					return false;
				}
				if(f.importType.value == ""){
					alert("Select an import type ");
					return false;
				}
				return true;
			}

			function validateMappingForm(f){
				var sourceArr = [];
				var targetArr = [];
				var catalogNumberIndex = 0;
				for(var i=0;i<f.length;i++){
					var obj = f.elements[i];
					if(obj.name == "sf[]"){
						if(sourceArr.indexOf(obj.value) > -1){
							alert("<?= $LANG['ERR_UNIQUE_D'] ?>"+obj.value+")");
							return false;
						}
						sourceArr[sourceArr.length] = obj.value;
					}
					else if(obj.value != ""){
						if(obj.name == "tf[]"){
							if(targetArr.indexOf(obj.value) > -1){
								alert("<?= $LANG['SAME_TARGET_D'] ?>"+obj.value+")");
								return false;
							}
							targetArr[targetArr.length] = obj.value;
						}
					}
					if(obj.name == "tf[]"){
						if(obj.value == "catalognumber"){
							catalogNumberIndex++;
						}
						else if(obj.value == "othercatalognumbers"){
							catalogNumberIndex++;
						}
						else if(obj.value == "occurrenceid"){
							catalogNumberIndex++;
						}
					}
				}
				if(catalogNumberIndex == 0){
					alert("<?= $LANG['NEED_CAT'] ?>");
					return false;
				}
				return true;
			}
		</script>

		<style>
			fieldset{ margin: 10px; padding: 10px; }
			legend{ font-weight: bold; }
			.index-li{ margin-left: 10px; }
			button{ margin: 10px 15px }
		</style>
	</head>
	<body>
		<?php
		$displayLeftMenu = false;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
			<a href="../../index.php">Home</a> &gt;&gt;
			<a href="../misc/collprofiles.php?collid=<?= $collid ?>&emode=1">Collection Control Panel</a> &gt;&gt;
			<a href="importextended.php?collid=<?= $collid ?>"><b>Extended Data Importer</b></a>
		</div>
		<!-- This is inner text! -->
		<div id="innertext">
			<h2><?= $importManager->getCollMeta('collName'); ?> Extended Data Import</h2>
			<div class="pageDescription-div">
				<div>This tool is used to batch import CSV data files containing data associated with occurrence records. </div>
				<div>Import files must contain one of the following occurrence identifiers, which is used to identify which occurrence record to link the data.</div>
				<ol>
					<li>Required for all imports: occurrenceIDs, catalog number, and/or other catalog number</li>
					<li>Imports</li>
					<li class="indent-li">originalUrl (large derivative) - required</li>
					<li class="indent-li">webUrl (medium derivative)</li>
					<li class="indent-li">thumbnailUrl (thumbnail derivative): If not provided, use Thumbnail Builder to generate a local cache of thumbnails</li>
					<li>Occurrence Associations</li>
					<li class="indent-li">relationship type - required: </li>
				</ol>
				<div>Import steps include: 1) Select import type. 2) Select import file. 3) Map data fields 4) Import data.</div>
				<div>If this is the first time using this tool, I recommend uploading a small file of records and review imported data to ensure import works as expected.
				Contact your portal administrator if you need additional input or assistance. </div>
			</div>
			<?php
			if(!$isEditor){
				echo '<h2>ERROR: not authorized to access this page</h2>';
			} elseif(!$collid){
				echo '<h2>ERROR: Collection identifier not set</h2>';
			} else{
				if($action == 'importData'){
					$importManager->setCreateNewRecord($createNew);
					echo '<ul>';
					$importManager->loadFileData($_POST);
					echo '</ul>';
				} elseif($action == 'initiateImport'){
					if($importManager->setImportFile()){
						?>
						<form name="mappingform" action="importextended.php" method="post" onsubmit="return validateMappingForm(this)">
							<fieldset>
								<legend><b>Image File Upload Map</b></legend>
								<div style="margin:15px;">
									<table class="styledtable" style="width:600px;font-family:Arial;font-size:12px;">
										<tr><th>Source Field</th><th>Target Field</th></tr>
										<?php
										$targetFieldMap = $importManager->getTargetFieldArr();
										$sourceFieldArr = $importManager->getSourceFieldArr();
										foreach($sourceFieldArr as $i => $sourceField){
											echo '<tr><td>';
											echo $sourceField;
											$sourceField = strtolower($sourceField);
											echo '<input type="hidden" name="sf['.$i.']" value="'.$sourceField.'" />';
											$translatedSourceField = $importManager->getTranslation($sourceField);
											echo '</td><td>';
											echo '<select name="tf['.$i.']" style="background:'.(array_key_exists($translatedSourceField, $targetFieldMap)?'':'yellow').'">';
											echo '<option value="">Select Target Field</option>';
											echo '<option value="">-------------------------</option>';
											foreach($targetFieldMap as $k => $v){
												echo '<option value="' . $k . '" ' . ($k == $translatedSourceField ? 'SELECTED' : '') . '>' . $v . '</option>';
											}
											echo '</select>';
											echo '</td></tr>';
										}
										?>
									</table>
								</div>
								<div style="margin:15px;">
									<input name="createNew" type="checkbox" value ="1" <?= ($createNew?'checked':'') ?> /> Link image to new blank record if catalog number does not exist
								</div>
								<div style="margin:15px;">
									<input name="collid" type="hidden" value="<?= $collid; ?>" />
									<input name="importType" type="hidden" value="<?= $importType ?>" />
									<input name="fileName" type="hidden" value="<?= htmlspecialchars($importManager->getImportFileName(), HTML_SPECIAL_CHARS_FLAGS); ?>" />
									<button name="submitAction" type="submit" value="importData"><?= $LANG['IMPORT_DATA'] ?></button>
								</div>
							</fieldset>
						</form>
						<?php
					}
					else echo 'ERROR setting import file: '.$importManager->getErrorMessage();
				} else{
					?>
					<form name="initiateImportForm" action="importextended.php" method="post" enctype="multipart/form-data" onsubmit="return validateInitiateForm(this)">
						<fieldset>
							<legend>Initial Import</legend>
							<div class="formField-div">
								<input name="importfile" type="file" size="50" onchange="verifyFileSize(this)" />
							</div>
							<div class="formField-div">
								<label for="importType">Import Type: </label>
								<select name="importType">
									<option value="">-------------------</option>
									<option value="1">Determinations</option>
									<option value="2">Image Field Map</option>
									<option value="3">Material Sample</option>
									<option value="4">Occurrence Associations</option>
								</select>
							</div>
							<div class="formField-div">
								<input name="collid" type="hidden" value="<?= $collid ?>" >
								<input name="MAX_FILE_SIZE" type="hidden" value="10000000" />
								<button name="submitAction" type="submit" value="initiateImport"><?= $LANG['INITIALIZE_IMPORT'] ?></button>
							</div>
						</fieldset>
					</form>
					<?php
				}
			}
			?>
		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>