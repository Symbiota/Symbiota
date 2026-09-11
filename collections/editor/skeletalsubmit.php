<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/OccurrenceEditorManager.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load('collections/editor/skeletalsubmit');

header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/editor/skeletalsubmit.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = Sanitize::int($_REQUEST['collid']);
$action = array_key_exists('formaction', $_REQUEST) ? $_REQUEST['formaction'] : '';

$occurrenceEditor = new OccurrenceEditorManager();

if($collid){
	$occurrenceEditor->setCollId($collid);
	$collMap = $occurrenceEditor->getCollMap();
}

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
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET ?>">
	<title><?= $DEFAULT_TITLE.' '.$LANG['OCC_SKEL_SUBMIT'] ?></title>
	<link href="<?= $CSS_BASE_PATH ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/javascript_lang_tags.php');
	?>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		const TAXON_AUTOCOMPLETE_INCLUDE_AUTHOR = <?= (empty($TAXON_AUTOCOMPLETE_INCLUDE_AUTHOR) ? 'false' : 'true') ?>;
		const TAXON_AUTOCOMPLETE_INCLUDE_KINGDOM = <?= (empty($TAXON_AUTOCOMPLETE_INCLUDE_KINGDOM) ? 'false' : 'true') ?>;
		const CLIENT_ROOT = "<?= $CLIENT_ROOT ?>";
	</script>
	<script src="<?= $CLIENT_ROOT ?>/js/symb/taxa.suggest.js?v=2" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/symb/collections.editor.skeletal.js?v=1g" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/symb/collections.editor.autocomplete.js?v=1d" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/symb/shared.js?ver=1" type="text/javascript"></script>
	<style>
		label{  }
		fieldset{ padding: 15px; }
		legend{ font-weight: bold; }
		.icon-img{ width: 16px; }
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = false;
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class='navpath'>
		<a href="../../index.php"><?= $LANG['HOME'] ?></a> &gt;&gt;
		<a href="../misc/collprofiles.php?collid=<?= $collid ?>&emode=1"><?= $LANG['COL_MNGMT'] ?></a> &gt;&gt;
		<b><?= $LANG['OCC_SKEL_SUBMIT'] ?></b>
	</div>
	<!-- inner text -->
	<div role="main" id="innertext">
		<h1 class="page-heading"><?= $LANG['OCC_SKEL_SUBMIT'] . ': ' . $collMap['collectionname'] ?></h1>
		<?php
		if($statusStr){
			echo '<div style="margin:15px;color:red;">'.$statusStr.'</div>';
		}
		if($isEditor){
			?>
			<section class="fieldset-like">
				<h3>
					<span><?= $LANG['SKELETAL_DATA'] ?></span>
					<span onclick="toggle('descriptiondiv')" onkeypress="toggle('descriptiondiv')" tabindex="0"><img src="../../images/info.png" class="icon-img" title="<?= $LANG['TOOL_DESCRIPTION'] ?>" aria-label="<?= $LANG['IMG_TOOL_DESCRIPTION'] ?>"/></span>
					<span id="optionimgspan" onclick="showOptions()" onkeypress="showOptions()" tabindex="0"><img src="../../images/list.png"  class="icon-img" title="<?= $LANG['DISPLAY_OPTIONS'] ?>" aria-label="<?= $LANG['IMG_DISPLAY_OPTIONS'] ?>"/></span>
				</h3>
				<div id="descriptiondiv" style="display:none;margin:10px;width:80%">
					<div style="margin-bottom:5px">
						<?= $LANG['SKELETAL_DESCIPRTION_1']; //This page is typically used to enter skeletal records into the system during the imaging process...?>
					</div>
					<div style="margin-bottom:5px">
						<?= $LANG['SKELETAL_DESCIPRTION_2']; //More complete data can be entered by clicking on the catalog number...?>
					</div>
					<div>
						<?= $LANG['SKELETAL_DESCIPRTION_3']; //Click the Display Option symbol located above scientific name to adjust field display...?>
					</div>
 				</div>
				<div id="optiondiv" style="display:none;position:absolute;background-color:white; z-index: 1;">
					<fieldset style="margin-top: -10px;padding-top:5px">
						<legend><?= $LANG['OPTIONS'] ?></legend>
						<div style="float:right;"><a href="#" onclick="hideOptions()" style="color:red" ><?= $LANG['X_CLOSE'] ?></a></div>
						<div style="text-decoration: underline"><?= $LANG['FIELD_DISPLAY'] ?>:</div>
						<input type="checkbox" onclick="toggleFieldDiv('othercatalognumbersdiv', this.checked)" /> <?= $LANG['OTHER_CAT_NUMS'] ?><br/>
						<input type="checkbox" onclick="toggleFieldDiv('authordiv', this.checked)" CHECKED /> <?= $LANG['AUTHOR'] ?><br/>
						<input type="checkbox" onclick="toggleFieldDiv('familydiv', this.checked)" CHECKED /> <?= $LANG['FAMILY'] ?><br/>
						<input type="checkbox" onclick="toggleFieldDiv('localitysecuritydiv', this)" CHECKED /> <?= $LANG['SECURITY'] ?><br/>
						<input type="checkbox" onclick="toggleFieldDiv('countrydiv', this.checked)" /> <?= $LANG['COUNTRY'] ?><br/>
						<input type="checkbox" onclick="toggleFieldDiv('statediv', this.checked)" CHECKED /> <?= $LANG['STATE_PROVINCE'] ?><br/>
						<input type="checkbox" onclick="toggleFieldDiv('countydiv', this.checked)" CHECKED /> <?= $LANG['COUNTY_PARISH'] ?><br/>
						<input type="checkbox" onclick="toggleFieldDiv('recordedbydiv', this.checked)" /> <?= $LANG['COLLECTOR'] ?><br/>
						<input type="checkbox" onclick="toggleFieldDiv('recordnumberdiv', this.checked)" /> <?= $LANG['COLLECTOR_NO'] ?><br/>
						<input type="checkbox" onclick="toggleFieldDiv('eventdatediv', this.checked)" /> <?= $LANG['COLLECTION_DATE'] ?><br/>
						<input type="checkbox" onclick="toggleFieldDiv('labelprojectdiv', this.checked)" /> <?= $LANG['LABEL_PROJECT'] ?><br/>
						<input type="checkbox" onclick="toggleFieldDiv('processingstatusdiv', this.checked)" /> <?= $LANG['PROCESSING_STATUS'] ?><br/>
						<input type="checkbox" onclick="toggleFieldDiv('languagediv', this.checked)" /> <?= $LANG['LANGUAGE'] ?><br/>
						<input type="checkbox" onclick="toggleFieldDiv('exsiccatadiv', this.checked)" /> <?= $LANG['EXSICCATA'] ?><br/>
						<div style="text-decoration: underline"><?= $LANG['CATNUM_MATCH'] ?>:</div>
						<input name="addaction" type="radio" value="1" checked /> <?= $LANG['RESTRICT_IF_EXISTS'] ?> <br/>
						<input name="addaction" type="radio" value="2" /> <?= $LANG['APPEND_VALUES'] ?>
					</fieldset>
				</div>
				<form id="defaultform" name="defaultform" class="det-form" action="skeletalsubmit.php" method="post" autocomplete="off" onsubmit="return submitDefaultForm(this)">
					<div style="display: flex; justify-content:right; gap: 0.5rem; margin-bottom: 1rem">
						<div>
							<?= $LANG['SESSION'] ?>: <span id="minutes">00</span>:<span id="seconds">00</span><br/>
						</div>
						<div>
							<?= $LANG['COUNT'] ?>: <span id="count">0</span><br/>
						</div>
						<div>
							<?= $LANG['RATE'] ?>: <span id="rate">0</span> <?= $LANG['PER_HOUR'] ?>
						</div>
					</div>

					<div class="flex-form" style="float:right">
							<div>
								<button name="clearform" type="reset" onclick="resetForm()" value="<?= $LANG['CLEAR'] ?>"><?= $LANG['CLEAR'] ?></button>
							</div>
						</div>
					<div class="flex-form">
						<div class="flex-form">
							<div id="scinamediv">
									<label for="fsciname"><?= $LANG['SCINAME'] ?>:</label>
									<input id="fsciname" name="sciname" type="text" value=""/>
									<input id="ftidinterpreted" name="tidinterpreted" type="hidden" value="" />
							</div>
						</div>
						<div class="flex-form">
							<div id="authordiv" class="left-breathing-room-rel">
								<label for="fscientificnameauthorship">
									<?= (isset($LANG['AUTHORSHIP']) ? $LANG['AUTHORSHIP'] : 'Authorship'). ':' ?>
								</label>
								<input id="fscientificnameauthorship" name="scientificnameauthorship" type="text" value="" />
							</div>
						</div>
						<?php
						if($IS_ADMIN || isset($USER_RIGHTS['Taxonomy'])){
							?>
							<div style="float:left;padding:2px 3px;">
								<a href="../../taxa/taxonomy/taxonomyloader.php" target="_blank">
									<img src="../../images/add.png"  class="icon-img" title="<?= $LANG['ADD_NAME_THESAURUS'] ?>" aria-label="<?= $LANG['ADD_NAME_THESAURUS'] ?>" />
								</a>
							</div>
							<?php
						}
						?>
						<div class="flex-form">
							<div id="familydiv">
								<label for="ffamily"><?= $LANG['FAMILY'] ?>:</label> <input id="ffamily" name="family" type="text" tabindex="0" value="" />
							</div>
							<div id="localitysecuritydiv">
								<input id="flocalitysecurity" name="recordsecurity" type="checkbox" tabindex="0" value="1" />
								<label for="flocalitysecurity">
									<?= $LANG['PROTECT_LOCALITY'] ?>
								</label>
							</div>
						</div>
						<div class="flex-form">
							<div id="countrydiv" style="display:none;float:left;margin:3px;">
								<label for="fcountry"><?= $LANG['COUNTRY'] ?></label><br/>
								<input id="fcountry" name="country" type="text" value="" autocomplete="off" />
							</div>
							<div id="statediv">
								<label for="fstateprovince"><?= $LANG['STATE_PROVINCE'] ?>:</label>
								<input id="fstateprovince" name="stateprovince" type="text" value="" autocomplete="off" onchange="localitySecurityCheck(this.form)" />
							</div>
							<div id="countydiv">
								<label for="fcounty"><?= $LANG['COUNTY_PARISH'] ?>:</label>
								<input id="fcounty" name="county" type="text" autocomplete="off" value="" />
							</div>
						</div>
						<div >
							<div id="recordedbydiv" style="display:none;float:left;margin:3px;">
								<label for="frecordedby"><?= $LANG['COLLECTOR'] ?></label><br/>
								<input id="frecordedby" name="recordedby" type="text" value="" />
							</div>
							<div id="recordnumberdiv" style="display:none;float:left;margin:3px;">
								<label for="frecordnumber"><?= $LANG['COLLECTOR_NO'] ?></label><br/>
								<input id="frecordnumber" name="recordnumber" type="text" value="" />
							</div>
							<div id="eventdatediv" style="display:none;float:left;margin:3px;">
								<label><?= $LANG['DATE'] ?></label><br/>
								<input id="feventdate" name="eventdate" type="text" value="" onchange="eventDateChanged(this)" />
							</div>
							<div id="labelprojectdiv" style="display:none;float:left;margin:3px;">
								<label><?= $LANG['LABEL_PROJECT'] ?></label><br/>
								<input id="flabelproject" name="labelproject" type="text" value="" />
							</div>
							<div id="processingstatusdiv" style="display:none;float:left;margin:3px">
								<label><?= $LANG['PROCESSING_STATUS'] ?></label><br/>
								<select id="fprocessingstatus" name="processingstatus" style="margin-top:4px;width:150px">
									<option value=""></option>
									<option>unprocessed</option>
									<option>stage 1</option>
									<option>stage 2</option>
									<option>stage 3</option>
									<option>expert required</option>
									<option>pending review-nfn</option>
									<option>pending review</option>
									<option>reviewed</option>
									<option>closed</option>
								</select>
							</div>
							<div id="languagediv" style="display:none;float:left;margin:3px;">
								<label><?= $LANG['LANGUAGE'] ?></label><br/>
								<select id="flanguage" name="language" style="margin-top:4px">
									<option value=""></option>
									<?php
									$langArr = $occurrenceEditor->getLanguageArr();
									foreach($langArr as $code => $langStr){
										echo '<option value="'.$code.'">'.$langStr.'</option>';
									}
									?>
								</select>
							</div>
							<div id="exsiccatadiv" style="display:none;clear:both;">
								<div id="ometidDiv" style="float:left">
									<label><?= $LANG['EXSTITLE'] ?></label><br/>
									<input id="exstitleinput" name="exstitle" value="" style="width: 600px" />
									<input id="ometidinput" name="ometid" type="hidden" value="" />
								</div>
								<div id="exsnumberDiv">
									<label><?= $LANG['EXSNUMBER'] ?></label><br/>
									<input id="fexsnumber" name="exsnumber" type="text" value="" />
								</div>
							</div>
						</div>

						<div class="flex-form">

							<div style="float:left;">
								<label for="fcatalognumber">
									<?= $LANG['CATALOGNUMBER'] ?>:
								</label>
								<input id="fcatalognumber" name="catalognumber" type="text" style="border-color:green;" required />
							</div>
							<div id="othercatalognumbersdiv" style="display:none;float:left;margin-left:3px;">
								<label><?= $LANG['OTHER_CAT_NUMS'] ?></label><br/>
								<input id="fothercatalognumbers" name="othercatalognumbers" type="text" value="" />
							</div>
							<div>
								<input id="fcollid" name="collid" type="hidden" value="<?= $collid ?>" />
								<button name="recordsubmit" type="submit" value="Add Record"><?= $LANG['ADD_RECORD'] ?></button>
							</div>
						</div>

					</div>
				</form>
			</section>
			<section class="fieldset-like">
				<h2>
					<span><?= $LANG['RECORDS'] ?></span>
				</h2>
				<div id="occurlistdiv">
				</div>
			</section>
			<?php
		}
		else{
			if($collid){
				echo $LANG['NOT_AUTHORIZED'].'<br/>';
				echo $LANG['CONTACT_ADMIN'].'</b> ';
			}
			else{
				echo $LANG['ERROR_NO_ID'];
			}
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
