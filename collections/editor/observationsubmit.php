<?php
//TODO: add code to automatically select hide locality details when taxon/state match name on list
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceEditorManager.php');
include_once($SERVER_ROOT.'/classes/Media.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/collections/editor/observationsubmit.'.$LANG_TAG.'.php'))
	include_once($SERVER_ROOT.'/content/lang/collections/editor/observationsubmit.'.$LANG_TAG.'.php');
else include_once($SERVER_ROOT.'/content/lang/collections/editor/observationsubmit.en.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/editor/observationsubmit.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collId  = array_key_exists('collid', $_REQUEST) ? filter_var($_REQUEST['collid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$clid  = array_key_exists('clid', $_REQUEST) ? filter_var($_REQUEST['clid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$recordedBy = array_key_exists('recordedby', $_POST) ? $_POST['recordedby'] : 0;
$uncertaintyInMeters = array_key_exists('coordinateuncertaintyinmeters', $_POST) ? filter_var($_POST['coordinateuncertaintyinmeters'], FILTER_SANITIZE_NUMBER_INT) : 50;
$action = array_key_exists('action', $_POST) ? $_POST['action'] : '';

//Sanitation
$recordedBy = htmlspecialchars($recordedBy);

$MAX_MEDIA_COUNT = 5;

$occurManager = new OccurrenceEditorManager();
$occurManager->setCollId($collId);
$collMap = $occurManager->getCollMap();
$mediaErrors = [];

if(!$collId && $collMap) $collId = $collMap['collid'];

$isEditor = 0;
$occid = 0;
if($collMap){
	if($IS_ADMIN){
		$isEditor = 1;
	}
	elseif(array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collId,$USER_RIGHTS['CollAdmin'])){
		$isEditor = 1;
	}
	elseif(array_key_exists("CollEditor",$USER_RIGHTS) && in_array($collId,$USER_RIGHTS['CollEditor'])){
		$isEditor = 1;
	}
	if($isEditor && $action == "Submit"){
		$occurManager->addOccurrence($_POST);
		$occid = $occurManager->getOccId();

		$path = get_occurrence_upload_path(
			$collMap['institutioncode'],
			$collMap['collectioncode'],
		);

		for($i = 1; $i <= min($MAX_MEDIA_COUNT, count($_FILES)); $i++) {
			$file = ($_FILES['imgfile' . $i] ?? false);
			if($file && $file['error'] === 0) {
				Media::uploadAndInsert(
					[
						'caption' => $_POST['caption' . $i] ?? null,
						'notes' => $_POST['notes' . $i] ?? null,
						'occid' => $occid
					],
					$file, 
					new LocalStorage($path)
				);
				
				if($errors = Media::getErrors()) {
					$mediaErrors[$i] = $errors;
				} else {
					$mediaErrors[$i] = false;
				}
			}
		}
	}
	if(!$recordedBy) $recordedBy = $occurManager->getUserName();
}
$clArr = $occurManager->getUserChecklists();
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET ?>">
	<title><?= $DEFAULT_TITLE . ' ' . $LANG['OBS_SUBMIT'] ?></title>
	<link href="<?= $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<style>
		.imgSubmitDiv{ padding:10px; width:700px; border:1px solid grey; background-color:#F5F5F5; }
	</style>
	<script type="text/javascript">
		<?php
		$maxUpload = ini_get('upload_max_filesize');
		$maxUpload = str_replace("M", "000000", $maxUpload);
		if($maxUpload > 10000000) $maxUpload = 10000000;
		echo 'var maxUpload = '.$maxUpload.";\n";
		?>
	</script>
	<script src="<?= $CLIENT_ROOT; ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT; ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script src="../../js/symb/collections.coordinateValidation.js?ver=1" type="text/javascript"></script>
	<script src="../../js/symb/collections.editor.observations.js?ver=1" type="text/javascript"></script>
	<style>
		#dmsdiv{ display: none; clear: both; padding: 15px; width: 565px; background-color: #f2f2f2; border: 2px outset #E8EEFA; }
		#dmsButton { margin: 0px 3px; display: inline; }
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = false;
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="../../index.php"><?= $LANG['HOME'] ?></a> &gt;&gt;
		<a href="../../profile/viewprofile.php?tabindex=1"><?= $LANG['PERS_MNGT'] ?></a> &gt;&gt;
		<b><?= $LANG['OBS_SUB'] ?></b>
	</div>
	<div role="main" id="innertext">
		<h1 class="page-heading"><?= $collMap['collectionname'] ?? $LANG['NO_COLLECTION']; ?></h1>
		<?php
		if($action || (isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post' && empty($_FILES) && empty($_POST))){
			?>
			<hr />
			<div style="margin:15px;font-weight:bold;">
				<?php

				if($mediaErrors) {
					echo '<div>';
					echo $LANG['MEDIA_STATUS'] . ':<ol>';
					foreach($mediaErrors as $mediaNumber => $e){
						if($e) {
							echo '<li style="color:red;">Media #' . $mediaNumber . ' ';
							echo $LANG['ERROR'] . ': '. $e[0];
							echo '</li>';
						} else {
							echo '<li style="color:green;">Media #' . $mediaNumber . ' ';
							echo $LANG['SUCCESS_IMAGE'];
							echo '</li>';
						}
					}
					echo '</ol>';
					echo '</div>';
				}

				if($occid) {
					?>
					<div style="font:weight;margin-top:10px;">
						<?= $LANG['OPEN']; ?> <a href="../individual/index.php?occid=<?= $occid ?>" target="_blank" rel="noopener"><?= $LANG['OCC_DET_VIEW'] ?></a> <?= $LANG['TO_SEE_NEW'] ?>
					</div>
					<?php
					if($clid){
						$checklistName = 'target';
						if(isset($clArr[$clid])) $checklistName = htmlspecialchars($clArr[$clid], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
						?>
						<div style="font:weight;margin-top:10px;">
							<?= $LANG['GO_TO'] ?> <a href="../../checklists/checklist.php?clid=<?= $clid ?>" target="_blank" rel="noopener"><?= $checklistName ?></a> <?= $LANG['CHECKLIST'] ?>
						</div>
						<?php
					}
				}
				$errArr = $occurManager->getErrorArr();
				if($errArr){
					echo '<div style="color:red;">';
					echo $LANG['ERROR'].':<ol>';
					foreach($errArr as $e){
						echo '<li>'.$e.'</li>';
					}
					echo '</ol>';
					echo '</div>';
				}
				if(!$action){
					echo $LANG['UNK_ERROR'];
				}
				?>
			</div>
			<hr />
			<?php
		}
		if($isEditor){
			?>
			<div>* <?= $LANG['FIELD_NAMES_REQ']; ?></div>
			<div style="margin:10px;">
				<form id='obsform' name='obsform' action='observationsubmit.php' method='post' enctype='multipart/form-data' onsubmit="return verifyObsForm(this)">
					<fieldset>
						<legend><b><?= $LANG['IMAGES']; ?></b></legend>
				    	<!-- following line sets MAX_FILE_SIZE (must precede the file input field)  -->
						<input type='hidden' name='MAX_FILE_SIZE' value='<?= $maxUpload; ?>' />
						<?php
						for($x=1; $x <= $MAX_MEDIA_COUNT; $x++){
							?>
							<div class="imgSubmitDiv" id="img<?= $x; ?>div" style="<?php if($x > 1) echo 'display:none'; ?>">
								<div style="margin-bottom: 10px;">
									<label for="imgfile<?= $x; ?>"><?= $LANG['IMAGE'].' '.$x; ?>:</label>
									<input name='imgfile<?= $x; ?>' id='imgfile<?= $x; ?>' type='file' onchange="verifyImageSize(this)" <?php if($x == 1) echo 'required'; ?> />
								</div>
								<div>
									<section class="flex-form">
										<div style="margin-bottom: 10px;">
											<label for="caption<?= $x; ?>"><?= $LANG['CAPTION']; ?>:</label>
											<input name="caption<?= $x; ?>" id="caption<?= $x; ?>" type="text" style="width:200px;" />
										</div>
										<div style="margin-bottom: 10px;">
											<label for="notes<?= $x; ?>"><?= $LANG['IMG_REMARKS']; ?>:</label>
											<input name="notes<?= $x; ?>" id="notes<?= $x; ?>" type="text" style="width:275px;" />
										</div>
									</section>
									<?php
									if($x < $MAX_MEDIA_COUNT){
										?>
										<div style="margin-bottom: 10px;">
											<a href="#" onclick="toggle('img<?= ($x+1); ?>div');return false">
												<?= $LANG['ADD_ANOTHER']; ?>
											</a>
										</div>
										<?php
									}
									?>
								</div>
							</div>
							<?php
						}
						?>
					</fieldset>
					<!-- <div style="margin-left:10px;clear:both">* Uploading web-ready images recommended. Upload image size can not be greater than <?= ($maxUpload/1000000); ?>MB</div>  -->
					<fieldset>
						<legend><b><?= $LANG['OBSERVATION']; ?></b></legend>
						<div style="clear:both;" class="p1">
							<section class="flex-form">
								<div>
									<span>
										<label for="sciname"><?= $LANG['SCINAME']; ?>:</label>
										<input type="text" id="sciname" name="sciname" maxlength="250" style="width:390px;" required />
										<input type="hidden" id="tidinterpreted" name="tidinterpreted" value="" />
									</span>
									<span stlye="margin-left: 10px">
										<label for="scientificnameauthorship"><?= $LANG['AUTHOR']; ?>:</label>
										<input type="text" name="scientificnameauthorship" id="scientificnameauthorship" maxlength="100" value="" />
									</span>
								</div>
							</section>
							<div style="clear:both;" class="flex-form">
								<label for="family"><?= $LANG['FAMILY']; ?>:</label>
								<input type="text" name="family" id="family" size="30" maxlength="50" value="" />
							</div>
						</div>
						<div style="clear:both;" class="flex-form">
							<div>
								<span>
									<label for="recordedby"><?= $LANG['OBSERVER']; ?>:</label>
									<input type="text" name="recordedby" id="recordedby" maxlength="255" style="width:250px;" value="<?= htmlspecialchars($recordedBy, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) ?>" required />
								</span>
								<span stlye="margin-left: 10px">
									<label for="recordnumber"><?= $LANG['NUMBER']; ?>:</label>
									<input type="text" name="recordnumber" id="recordnumber" maxlength="45" style="width:80px;" title="Observer Number, if observer uses a numbering system " />
								</span>
								<span>
									<label for="eventdate"><?= $LANG['DATE']; ?>:</label>
									<input type="text" id="eventdate" name="eventdate" style="width:120px;" onchange="verifyDate(this);" title="format: yyyy-mm-dd" required />
									<a href="#" style="margin:15px 0px 0px 5px;" onclick="toggle('obsextradiv');return false" title="<?= $LANG['EDIT_BTN'] ?>" aria-label="<?= $LANG['EDIT_BTN'] ?>">
										<img src="../../images/editplus.png" style="width:1.5em;" alt="<?= $LANG['IMG_EDIT'] ?>"/>
									</a>
								</span>
							</div>
						</div>
						<div id="obsextradiv" style="clear:both;padding:3px 0px 0px 10px;margin-bottom:20px;display:none;">
							<div>
								<label for="associatedcollectors"><?= $LANG['ASSOC_OBSERVERS']; ?>:</label>
								<input type="text" name="associatedcollectors" id="associatedcollectors" maxlength="255" style="width:350px;" value="" />
							</div>
							<section class="flex-form">
								<div>
									<span>
										<label for="identifiedby"><?= $LANG['IDED_BY']; ?>:</label>
										<input type="text" name="identifiedby" id="identifiedby" maxlength="255" style="" value="" />
									</span>
									<span stlye="margin-left: 10px">
										<label for="dateidentified"><?= $LANG['DATE_IDED']; ?>:</label>
										<input type="text" name="dateidentified" id="dateidentified" maxlength="45" style="" value="" />
									</span>
								</div>
							</section>
							<div>
								<label for="identificationreferences"><?= $LANG['ID_REFS']; ?>:</label>
								<input type="text" name="identificationreferences" id="identificationreferences" style="width:450px;" title="cf, aff, etc" />
							</div>
							<div style="clear:both;padding:3px 0px 0px 0px;" >
								<label for="taxonremarks"><?= $LANG['ID_REMARKS']; ?>:</label>
								<input type="text" name="taxonremarks" id="taxonremarks" style="width:500px;" value="" />
							</div>
						</div>
					</fieldset>
					<fieldset style="margin-top:10px;">
						<legend><b><?= $LANG['LOCALITY']; ?></b></legend>
						<div style="clear:both;" class="flex-form">
							<div>
								<span>
									<label for="country"><?= $LANG['COUNTRY']; ?>:</label>
									<input type="text" name="country" id="country" style="width:150px;" value="" required />
								</span>
								<span stlye="margin-left: 10px">
									<label for="stateprovince"><?= $LANG['STATE_PROVINCE']; ?>:</label>
									<input type="text" name="stateprovince" id="stateprovince" style="width:150px;" value="" required />
								</span>
								<span stlye="margin-left: 10px">
									<label for="county"><?= $LANG['COUNTY_PARISH']; ?>:</label>
									<input type="text" name="county" id="county" style="width:150px;" value="" />
								</span>
							</div>
						</div>
						<div style="clear:both;margin:4px 0px 2px 0px;">
							<label for="locality"><?= $LANG['LOCALITY']; ?>:</label>
							<input type="text" name="locality" id="locality" style="width:95%;" value="" required />
						</div>
						<div style="clear:both;margin-bottom:5px;">
							<input type="checkbox" name="recordsecurity" id="recordsecurity" style="" value="1" title="<?= $LANG['HIDE_LOC_SHORT']; ?>" />
							<label for="recordsecurity"><?= $LANG['HIDE_LOC_LONG']; ?></label>
						</div>
						<div style="clear:both;" class="flex-form">
							<div>
								<span>
									<label for="decimallatitude"><?= $LANG['LATITUDE']; ?>:</label>
									<input type="text" id="decimallatitude" name="decimallatitude" maxlength="10" style="width:100px;" value="" onchange="verifyLatValue(this.form, '<?= $CLIENT_ROOT?>')" title="Decimal Format (eg 34.5436)" required />
								</span>
								<span>
									<label for="decimallongitude"><?= $LANG['LONGITUDE']; ?>:</label>
									<input type="text" id="decimallongitude" name="decimallongitude" maxlength="13" style="width:100px;" value="" onchange="verifyLngValue(this.form, '<?= $CLIENT_ROOT?>')" title="Decimal Format (eg -112.5436)" required />
								</span>
								<span style="margin-top:10px; margin-left:3px; margin-bottom:10px" >
									<a tabindex="0" onclick="openMappingAid('obsform','decimallatitude','decimallongitude');return false;">
										<img src="../../images/world.png" style="width:1.3em;" title="Coordinate Map Aid" alt="<?= $LANG['IMG_GLOBE'] ?>" />
									</a>
									<button id="dmsButton" type="button" onclick="toggle('dmsdiv');"><?= $LANG['DMS']; ?></button>
								</span>
								<span>
									<label for="coordinateuncertaintyinmeters"><?= $LANG['UNCERTAINTY_M']; ?>:</label>
									<input type="text" id="coordinateuncertaintyinmeters" name="coordinateuncertaintyinmeters" maxlength="10" style="width:110px;" onchange="inputIsNumeric(this, 'Lat/long uncertainty')" title="Uncertainty in Meters" value="<?= $uncertaintyInMeters; ?>" required />
								</span>
								<span>
									<label for="geodeticdatum"><?= $LANG['DATUM']; ?>:</label>
									<input type="text" name="geodeticdatum" id="geodeticdatum" maxlength="255" style="width:80px;" />
								</span>
							</div>
							<div>
								<span>
									<label for="minimumelevationinmeters"><?= $LANG['ELEV_M']; ?>:</label>
									<input type="text" name="minimumelevationinmeters" id="minimumelevationinmeters" maxlength="6" style="width:95px;" value="" onchange="verifyElevValue(this)" title="Minumum Elevation In Meters" />
								</span>
								<span>
									<label for="verbatimelevation"><?= $LANG['ELEV_FT']; ?>:</label>
									<input type="text" name="verbatimelevation" id="verbatimelevation" style="width:85px;" value="" onchange="convertElevFt(this.form)" title="Minumum Elevation In Feet" />
								</span>
							</div>
							<div>
								<label for="georeferenceremarks"><?= $LANG['GEO_REMARKS']; ?>:</label>
								<input type="text" name="georeferenceremarks" id="georeferenceremarks" maxlength="255" style="width:500px;" value="" />
							</div>
						</div>
						<div id="dmsdiv">
							<section class="flex-form">
								<div>
									<div>
										<em><?= $LANG['LATITUDE']; ?>: </em>
									</div>
									<span>
										<input id="latdeg" style="width:50px;" title="<?= $LANG['DEG']; ?>">
									</span>
									<span>
										<input id="latmin" style="width:60px;" title="<?= $LANG['MIN']; ?>">
									</span>
									<span>
										<input id="latsec" style="width:60px;" title="<?= $LANG['SEC']; ?>">
									</span>
									<span>
										<select id="latns">
											<option><?= $LANG['N']; ?></option>
											<option><?= $LANG['S']; ?></option>
										</select>
									</span>
								</div>
							</section>
							<section class="flex-form">
								<div>
									<em><?= $LANG['LONGITUDE']; ?>:</em>
								</div>
								<div>
									<span>
										<input id="lngdeg" style="width:50px;" title="<?= $LANG['DEG']; ?>" />
									</span>
									<span>
										<input id="lngmin" style="width:60px;" title="<?= $LANG['MIN']; ?>" />
									</span>
									<span>
										<input id="lngsec" style="width:60px;" title="<?= $LANG['SEC']; ?>" />
									</span>
									<span>
										<select id="lngew">
											<option><?= $LANG['E']; ?></option>
											<option SELECTED><?= $LANG['W']; ?></option>
										</select>
									</span>
								</div>
							</section>
							<div style="margin:5px;">
								<input type="button" value="Insert Lat/Long Values" onclick="insertLatLng(this.form)" />
							</div>
						</div>
					</fieldset>
					<fieldset style="margin-top:10px;">
						<legend><b><?= $LANG['MISC']; ?></b></legend>
						<div style="padding:3px;">
							<label for="habitat"><?= $LANG['HABITAT']; ?>:</label>
							<input type="text" name="habitat" id="habitat" style="width:600px;" value="" />
						</div>
						<div style="padding:3px;">
							<label for="substrate"><?= $LANG['SUBSTRATE']; ?>:</label>
							<input type="text" name="substrate" id="substrate" style="width:600px;" value="" />
						</div>
						<div style="padding:3px;">
							<label for="associatedtaxa"><?= $LANG['ASSOC_TAXA']; ?>:</label>
							<input type="text" name="associatedtaxa" id="associatedtaxa" style="width:600px;" value="" />
						</div>
						<div style="padding:3px;">
							<label for="verbatimattributes"><?= $LANG['DESC_ORG']; ?>:</label>
							<input type="text" name="verbatimattributes" id="verbatimattributes" style="width:600px;" value="" />
						</div>
						<div style="padding:3px;">
							<label for="occurrenceremarks"><?= $LANG['GENERAL_NOTES']; ?>:</label>
							<input type="text" name="occurrenceremarks" id="occurrenceremarks" style="width:600px;" value="" title="Occurrence Remarks" />
						</div>
						<section class="flex-form">
							<div style="padding:3px;">
								<span>
									<label for="reproductivecondition"><?= $LANG['REP_COND']; ?>:</label>
									<input type="text" name="reproductivecondition" id="reproductivecondition" maxlength="255" style="width:140px;" value="" >
								</span>
							</div>
							<div style="padding:3px;">
								<span style="margin-right: 20px">
									<label for="establishmentmeans"><?= $LANG['EST_MEANS']; ?>:</label>
									<input type="text" name="establishmentmeans" id="establishmentmeans" maxlength="32" style="width: 230px;" value="" >
								</span>
								<span title="<?= $LANG['CULT_CAPT_EG'] ?>">
									<input type="checkbox" name="cultivationstatus" id="repcond" style="" value="" />
									<label for="repcond"> <?= $LANG['CULT_CAPT']; ?></label>
								</span>
							</div>
						</section>
					</fieldset>
					<?php
					if($clArr){
						?>
						<fieldset class="top-breathing-room-rel">
							<legend><b><?= $LANG['LINK_CHECK']; ?></b></legend>
							<label for="clid"><?= $LANG['SP_LIST']; ?>:</label>
							<select name='clid' id='clid'>
								<option value="0"><?= $LANG['SEL_CHECKLIST']; ?></option>
								<option value="0">------------------------------</option>
								<?php
								foreach($clArr as $id => $clName){
									echo '<option value="' . $id . '" ' . ($id==$clid?'SELECTED':'') . '>' . $clName . '</option>';
								}
								?>
							</select>
						</fieldset>
						<?php
					}
					?>
					<div class="top-breathing-room-rel">
						<input type="hidden" name="collid" value="<?= $collId; ?>" />
						<button type="submit" name="action" value="Submit"><?= $LANG['SUBMIT']; ?></button>
					</div>
				</form>
			</div>
			<?php
		}
		else{
			echo $LANG['NOT_AUTH'].' ';
			echo '<br/><b>'.$LANG['CONTACT_ADMIN'].'</b> ';
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
