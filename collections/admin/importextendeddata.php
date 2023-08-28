<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceImport.php');
header("Content-Type: text/html; charset=".$CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/admin/importextendeddata.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = array_key_exists('collid', $_REQUEST) ? $_REQUEST['collid'] : 0;
$importType = array_key_exists('importtype', $_REQUEST) ? $_REQUEST['importtype'] : 0;
$createNew = array_key_exists('createnew', $_POST) ? $_POST['createnew'] : 0;
$fileName = array_key_exists('filename', $_POST) ? $_POST['filename'] : 0;
$action = array_key_exists('submitaction', $_POST) ? $_POST['submitaction'] : '';

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
		<style>
			.index-li{ margin-left: 10px; }
		</style>
	</head>
	<body>
		<?php
		$displayLeftMenu = false;
		include($SERVER_ROOT.'/includes/header.php');
		echo '<div class="navpath">';
		echo '<a href="../../index.php">Home</a> &gt;&gt; ';
		echo '<a href="../misc/collprofiles.php?collid='.$collid.'&emode=1">Collection Control Panel</a> &gt;&gt; ';
		echo '<b>Uploader</b>';
		echo '</div>';
		?>
		<!-- This is inner text! -->
		<div id="innertext">
			<h2><?= $specManager->getCollectionName(); ?></h2>
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
					<li class="indent-li"></li>
				</ol>
				<div>Import steps include: 1) Select import type. 2) Select import file. 3) Map data fields 4) Import data.</div>
				<div>If this is the first time using this tool, I recommend uploading a small file of records and review imported data to ensure import works as expected.
				Contact your portal administrator if you need additional input or assistance. </div>
			</div>
			<?php
			if(!$isEditor){
				echo '<h2>ERROR: not authorized to access this page</h2>';
			}
			elseif(!$collid){
				echo '<h2>ERROR: Collection identifier not set</h2>';
			}
			else{
				if($action == 'importData'){
					$importManager->setCreateNewRecord($createNew);
					echo '<ul>';
					$importManager->loadFileData($_POST);
					echo '</ul>';
				}
				elseif($action == 'initiateImport'){
					$importManager->setImportFile();
					?>
					<form name="mappingform" action="importextendeddata.php" method="post" onsubmit="return validateMappingForm(this)">
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
										echo '<select name="tf['.$i.']" style="background:'.(array_key_exists($sourceField, $targetFieldMap)?'':'yellow').'">';
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
								<input name="createnew" type="checkbox" value ="1" <?= ($createNew?'checked':'') ?> /> Link image to new blank record if catalog number does not exist
							</div>
							<div style="margin:15px;">
								<input name="collid" type="hidden" value="<?= $collid; ?>" />
								<input name="importType" type="hidden" value="<?= $importType ?>" />
								<input name="filename" type="hidden" value="<?= htmlspecialchars($importManager->getImportFileName(), HTML_SPECIAL_CHARS_FLAGS); ?>" />
								<button name="submitaction" type="submit" value="importData">Import Data</button>
							</div>
						</fieldset>
					</form>
					<?php
				}
				else{
					?>
					<form name="initiateImportForm" action="importextendeddata.php" method="post" onsubmit="return validateInitiateForm(this)">
						<div class="formField-div">
							<label>Import Type: </label>
							<select name="importType">
								<option value="">-------------------</option>
								<option value="1">Determinations</option>
								<option value="2">Image Field Map</option>
								<option value="3">Material Sample</option>
								<option value="4">Occurrence Associations</option>
							</select>


						</div>
						<div class="formField-div">
							<button name="submitaction" type="submit" value="initiateImport"></button>
						</div>
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