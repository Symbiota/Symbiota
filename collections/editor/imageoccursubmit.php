<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/OccurrenceEditorManager.php');
include_once($SERVER_ROOT . '/classes/Media.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load('collections/editor/imageoccursubmit');

header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/editor/imageoccursubmit.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid  = array_key_exists('collid', $_REQUEST) ? Sanitize::int($_REQUEST['collid']) : 0;
$action = array_key_exists('action',$_POST)?$_POST['action']:'';

$occurManager = new OccurrenceEditorManager();
$occurManager->setCollid($collid);
$collMap = $occurManager->getCollMap();

$statusStr = '';
$isEditor = 0;
if($collid){
	if($IS_ADMIN){
		$isEditor = 1;
	}
	elseif(array_key_exists('CollAdmin', $USER_RIGHTS) && in_array($collid, $USER_RIGHTS['CollAdmin'])){
		$isEditor = 1;
	}
	elseif(array_key_exists('CollEditor', $USER_RIGHTS) && in_array($collid, $USER_RIGHTS['CollEditor'])){
		$isEditor = 1;
	}
}
if($isEditor){
	if($action == 'Submit Occurrence') {
		if($occurManager->addOccurrence($_POST) && ($occid = $occurManager->getOccid())) {
			try {
				$occur_map = $occurManager->getOccurMap()[$occid];
				$path = get_occurrence_upload_path(
					$occur_map['institutioncode'],
					$occur_map['collectioncode'],
					$occur_map['catalognumber']
				);

				$_POST['occid'] = $occid;

				Media::uploadAndInsert(
					$_POST,
					$_FILES['imgfile'],
					StorageFactory::make($path)
				);

				if($errors = Media::getErrors()) {
					$statusStr = "ERROR: " . array_pop($errors);
				} else {
					$statusStr = $LANG['NEW_RECORD_CREATED'].': <a href="occurrenceeditor.php?occid=' . $occid . '" target="_blank" rel="noopener">' . $occid . '</a>';
				}
			} catch(Exception $e) {
				$statusStr = "ERROR: " . $e->getMessage();
			}

		} else {
			$statusStr = $occurManager->getErrorStr();
		}
	}
}
if($collid && file_exists('includes/config/occurVarColl'.$collid.'.php')){
	//Specific to particular collection
	include('includes/config/occurVarColl'.$collid.'.php');
}
elseif(file_exists('includes/config/occurVarDefault.php')){
	//Specific to Default values for portal
	include('includes/config/occurVarDefault.php');
}
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET; ?>">
	<title><?= $DEFAULT_TITLE.' '.$LANG['IMAGE_SUBMIT'] ?></title>
	<link href="<?= $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
    ?>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/symb/collections.imageoccursubmit.js?ver=1" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/symb/collections.editor.tools.js?ver=1" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/symb/shared.js?ver=141119" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/symb/localitySuggest.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/symb/taxa.suggest.js?v=2" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/symb/collections.editor.autocomplete.js?v=1" type="text/javascript"></script>
	<script type="text/javascript">
		const TAXON_AUTOCOMPLETE_INCLUDE_AUTHOR = <?= (empty($TAXON_AUTOCOMPLETE_INCLUDE_AUTHOR) ? 'false' : 'true') ?>;
		const TAXON_AUTOCOMPLETE_INCLUDE_KINGDOM = <?= (empty($TAXON_AUTOCOMPLETE_INCLUDE_KINGDOM) ? 'false' : 'true') ?>;
		const CLIENT_ROOT = "<?= $CLIENT_ROOT ?>";

		$(document).ready(function() {

			$("#catalognumber").keydown(function(evt){
				var evt  = (evt) ? evt : ((event) ? event : null);
				if ((evt.keyCode == 13)) { return false; }
			});

		});

		//Validate forms
		function validateImgOccurForm(f){
			if(f.imgurl.value == "" && f.imgfile.value == ""){
				alert("Local image must be select or a image URL entered");
				return false;
			}

			return true;
		}

		//Misc
		function dwcDoc(dcTag){
			dwcWindow=open("https://docs.symbiota.org/Editor_Guide/Editing_Searching_Records/symbiota_data_fields#"+dcTag,"dwcaid","width=1250,height=300,left=20,top=20,scrollbars=1");
			//dwcWindow=open("http://rs.tdwg.org/dwc/terms/index.htm#"+dcTag,"dwcaid","width=1250,height=300,left=20,top=20,scrollbars=1");
			if(dwcWindow.opener == null) dwcWindow.opener = self;
			dwcWindow.focus();
			return false;
		}

		function validateImgOccurForm(f){
			if(f.imgfile.value == "" && f.imgurl.value == ""){
				alert("<?= $LANG['SELECT_IMAGE'] ?>");
				return false;
			}
			else{
				if(f.imgfile.value != ""){
					var fName = f.imgfile.value.toLowerCase();
					if(fName.indexOf(".jpg") == -1 && fName.indexOf(".jpeg") == -1 && fName.indexOf(".gif") == -1 && fName.indexOf(".png") == -1){
						alert("<?= $LANG['IMAGE_TYPE'] ?>");
						return false;
					}
				}
				else if(f.imgurl.value != ""){
					var fileName = f.imgurl.value;
					if(fileName.substring(0,4).toLowerCase() != 'http'){
						alert("<?= $LANG['IMAGE_PATH_URL'] ?> ("+fileName.substring(0,4).toLowerCase()+")");
						return false
					}
					//Test to make sure file is correct mime type
					$.ajax({
						type: "POST",
						url: "rpc/getImageMime.php",
						async: false,
						data: { url: fileName }
					}).success(function( retStr ) {
						if(retStr == "image/jpeg" || retStr == "image/gif" || retStr == "image/png"){
							return true;
						}
						else{
							alert("<?= $LANG['IMAGE_FILE_TYPE'] ?>"+retStr+")");
							return false;
						}
					});
				}
			}
			return true;
		}
	</script>
</head>
<body>
	<?php
	$displayLeftMenu = false;
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class='navpath'>
		<a href="../../index.php"><?= $LANG['HOME'] ?></a> &gt;&gt;
		<a href="../misc/collprofiles.php?collid=<?= $collid ?>&emode=1"><?= $LANG['COL_MNT'] ?></a> &gt;&gt;
		<b><?= $LANG['OCC_IMAGE_SUBMIT'] ?></b>
	</div>
	<div role="main" id="innertext">
		<h1 class="page-heading"><?= $LANG['IMAGE_SUBMIT'] . ': ' . $collMap['collectionname']; ?></h1>
		<?php
		if($statusStr){
			echo '<div style="margin:15px;color:'.(stripos($statusStr,'error') !== false?'red':'green').';">'.$statusStr.'</div>';
		}
		if($isEditor){
			?>
			<form id='imgoccurform' name='imgoccurform' action='imageoccursubmit.php' method='post' enctype='multipart/form-data' onsubmit="return validateImgOccurForm(this)">
				<fieldset style="padding:15px;">
					<legend><b><?= $LANG['MANUAL_UPLOAD'] ?></b></legend>
					<div class="targetdiv">
						<input type='hidden' name='MAX_FILE_SIZE' value='10000000' />
						<div>
							<input name='imgfile' type='file' aria-label="<?= (isset($LANG['UPLOAD']) ? $LANG['UPLOAD'] : 'Upload the File'); ?>" accept="<?= implode(",", $ALLOWED_MEDIA_MIME_TYPES) ?>"/>
						</div>
						<div id="newimagediv"></div>
						<div style="margin:10px 0px;">
							* <?= $LANG['WEB_READY_RECOMMENDED'] ?>
						</div>
					</div>
					<div class="targetdiv" style="display:none;">
						<div style="margin-bottom:10px;">
							<?= $LANG['ENTER_URL_EXPLAIN'] ?>
						</div>
						<div>
							<b><?= $LANG['IMAGE_URL'] ?>:</b><br/>
							<!-- <input type='text' name='imgurl' size='70' /> -->
							<input type='text' name='originalUrl' size='70' />
						</div>
						<div>
							<b><?= $LANG['MEDIUM_URL'] ?>:</b><br/>
							<input type='text' name='weburl' size='70' />
						</div>
						<div>
							<b><?= $LANG['THUMBNAIL_URL'] ?>:</b><br/>
							<!-- <input type='text' name='tnurl' size='70' /> -->
							<input type='text' name='thumbnailUrl' size='70' />
						</div>
						<div>
							<input type="checkbox" name="copytoserver" value="1" <?= (isset($_POST['copytoserver'])&&$_POST['copytoserver']?'checked':''); ?> />
							<?= $LANG['COPY_LARGE'] ?>
						</div>
					</div>
					<div style="float:right;text-decoration:underline;font-weight:bold;">
						<div class="targetdiv">
							<a href="#" onclick="toggle('targetdiv');return false;"><?= $LANG['ENTER_URL'] ?></a>
						</div>
						<div class="targetdiv" style="display:none;">
							<a href="#" onclick="toggle('targetdiv');return false;"><?= $LANG['UPLOAD_LOCAL'] ?></a>
						</div>
					</div>
					<div>
						<input type="checkbox" id="nolgimage" name="nolgimage" value="1" <?= (isset($_POST['nolgimage'])&&$_POST['nolgimage']?'checked':''); ?>/>
						<label for="nolgimage"> <?= $LANG['DONT_MAP_LARGE'] ?> </label>
					</div>
					<div style="margin-top:10px;">
						<?php
						$processingStatusArr = array();
						if(isset($PROCESSINGSTATUS) && $PROCESSINGSTATUS){
							$processingStatusArr = $PROCESSINGSTATUS;
						}
						else{
							$processingStatusArr = array('unprocessed','unprocessed/NLP','stage 1','stage 2','stage 3','pending review-nfn','pending review','expert required','reviewed','closed');
						}
						?>
						<label for="processingstatus"> <b><?= (isset($LANG['PROCESSING_STATUS']) ? $LANG['PROCESSING_STATUS'] : 'Processing Status'); ?>:</b> </label>
						<select id="processingstatus" name="processingstatus">
							<option value=''><?= $LANG['NO_SET_STATUS'] ?></option>
							<option value=''>-------------------</option>
							<?php
							$pStatus = (isset($_POST['processingstatus']) ? $_POST['processingstatus'] : 'unprocessed');
							foreach($processingStatusArr as $v){
								$keyOut = strtolower($v);
								echo '<option value="'.$keyOut.'" '.($pStatus==$keyOut?'SELECTED':'').'>'.ucwords($v).'</option>';
							}
							?>
						</select>
					</div>
				</fieldset>
				<fieldset style="padding:15px;">
					<legend><b><?= $LANG['SKELETAL_DATA'] ?></b></legend>
					<div style="margin:3px;">
						<label for="catalognumber"> <b> <?= (isset($LANG['CAT_NUM']) ? $LANG['CAT_NUM'] : 'Catalog Number'); ?>:</b> </label>
						<input id="catalognumber" name="catalognumber" type="text" onchange="<?php if(!defined('CATNUMDUPECHECK') || CATNUMDUPECHECK) echo 'searchCatalogNumber(this.form, true)'; ?>" />
					</div>
					<div style="margin:3px;">
						<label for="sciname"> <b><?= (isset($LANG['SCINAME']) ? $LANG['SCINAME'] : 'Scientific Name');?>:</b> </label>
						<input id="sciname" name="sciname" type="text" value="<?= (isset($_POST['sciname']) ? $_POST['sciname'] : ''); ?>" style="width:300px"/>
						<input name="scientificnameauthorship" type="text" value="<?= (isset($_POST['scientificnameauthorship']) ? $_POST['scientificnameauthorship'] : ''); ?>" aria-label="<?= (isset($LANG['SCINAMEAUTH']) ? $LANG['SCINAMEAUTH'] : 'Scientific Name Authorship');?>" /><br/>
						<input type="hidden" id="tidinterpreted" name="tidinterpreted" value="<?= (isset($_POST['tidinterpreted']) ? $_POST['tidinterpreted'] : ''); ?>" />
						<label for="family"> <b><?= (isset($LANG['FAMILY']) ? $LANG['FAMILY'] : 'Family')?>:</b> </label>
						<input id="family" name="family" type="text" value="<?= (isset($_POST['family']) ? $_POST['family'] : ''); ?>" />
					</div>
					<div>
						<div style="float:left;margin:3px;">
							<label for="country"><b><?= (isset($LANG['COUNTRY']) ? $LANG['COUNTRY'] : 'Country')?>:</b><br/> </label>
							<input id="country" name="country" type="text" value="<?= (isset($_POST['country']) ? $_POST['country'] : ''); ?>" />
						</div>
						<div style="float:left;margin:3px;">
						<label for="state"><b><?= (isset($LANG['STATE_PROVINCE']) ? $LANG['STATE_PROVINCE'] : 'State/Province')?>:</b><br/> </label>
							<input id="state" name="stateprovince" type="text" value="<?= (isset($_POST['stateprovince']) ? $_POST['stateprovince'] : ''); ?>" />
						</div>
						<div style="float:left;margin:3px;">
						<label for="county"><b><?= (isset($LANG['COUNTY']) ? $LANG['COUNTY'] : 'County')?>:</b><br/> </label>
							<input id="county" name="county" type="text" value="<?= (isset($_POST['county']) ? $_POST['county'] : ''); ?>" />
						</div>
					</div>
					<div style="clear:both;margin:3px;">
						<?php
						if(isset($TESSERACT_PATH) && $TESSERACT_PATH){
							?>
							<div style="float:left;">
								<input name="tessocr" type="checkbox" value=1 <?php if(isset($_POST['tessocr'])) echo 'checked'; ?> />
								<?= $LANG['OCR_TEXT_ENGINE'] ?>
							</div>
							<?php
						}
						?>
						<div style="float:left;margin:8px 0px 0px 20px;">(<a href="#" onclick="toggle('manualocr')"><?= $LANG['MANUAL_OCR'] ?></a>)</div>
					</div>
					<div id="manualocr" style="clear:both;display:none;margin:3px;">
						<b><?= $LANG['OCR_TEXT'] ?></b><br/>
						<textarea name="ocrblock" style="width:100%;height:100px;"></textarea><br/>
						<b><?= $LANG['SOURCE'] ?>:</b> <input type="text" name="ocrsource" value="" />
					</div>
				</fieldset>
				<div style="margin:10px;clear:both;">
					<input type="hidden" name="collid" value="<?= $collid; ?>" />
					<input type="submit" name="action" value="Submit Occurrence" />
					<input type="reset" name="reset" value="Reset Form" />
				</div>
			</form>
			<?php
		}
		else{
			echo $LANG['NOT_AUTH'].' ';
			echo '<br/><b>'.$LANG['CONTACT_ADMIN'].'</b> ';
		}
		?>
	</div>
		<script>
			window.initLocalitySuggest({
				country: {
					id: 'country',
				},
				state_province: {
					id: 'state',
				},
				county: {
					id: 'county',
				},
			})
		</script>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
