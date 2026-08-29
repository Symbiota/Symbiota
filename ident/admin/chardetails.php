<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/KeyCharacterAdmin.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load('ident/admin/chardetails');

header('Content-Type: text/html; charset=' . $CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../ident/admin/index.php');

$cid = array_key_exists('cid', $_REQUEST) ? Sanitize::int($_REQUEST['cid']) : 0;
$tabIndex = array_key_exists('tabindex', $_REQUEST) ? Sanitize::int($_REQUEST['tabindex']) : 0;
$langId = array_key_exists('langid', $_REQUEST) ? $_REQUEST['langid'] : '';
$formSubmit = array_key_exists('formsubmit', $_POST) ? $_POST['formsubmit'] : '';

$isEditor = false;
if($IS_ADMIN || array_key_exists('KeyAdmin', $USER_RIGHTS)) $isEditor = true;

$charManager = new KeyCharacterAdmin();
$charManager->setLangId($langId);
$charManager->setCid($cid);

$statusStr = '';
if($formSubmit && $isEditor){
	if($formSubmit == 'createCharacter'){
		if($charManager->insertCharacter($_POST)){
			$cid = $charManager->getCid();
		}
		else{
			$statusStr = $LANG['ERROR_ADD_TAXON'] . $charManager->getErrorMessage();
		}
	}
	elseif($formSubmit == 'saveCharacterEdit'){
		if(!$charManager->updateCharacter($_POST)){
			$statusStr = $LANG['ERROR_EDIT_TAXON'] . $charManager->getErrorMessage();
		}
	}
	elseif($formSubmit == 'deleteChar'){
		if($charManager->deleteCharacter()){
			$cid = 0;
		}
		else{
			$statusStr = $LANG['ERROR_DELETE_TAXON'] . $charManager->getErrorMessage();
		}
	}
	elseif($formSubmit == 'addState'){
		if(!$charManager->insertCharacterState($_POST)){
			$statusStr = $LANG['ERROR_ADD_STATE']  . $charManager->getErrorMessage();
		}
		$tabIndex = 1;
	}
	elseif($formSubmit == 'saveState'){
		if(!$charManager->updateCharacterState($_POST)){
			$statusStr = $LANG['ERROR_EDIT_STATE'] . $charManager->getErrorMessage();
		}
		$tabIndex = 1;
	}
	elseif($formSubmit == 'deleteState'){
		if(!$charManager->deleteCharacterState($_POST['cs'])){
			$statusStr = $LANG['ERROR_DELETE_STATE'] . $charManager->getErrorMessage();
		}
		$tabIndex = 1;
	}
	elseif($formSubmit == 'uploadImage'){
		if(!$charManager->uploadCharacterStateImage($_POST)){
			$statusStr = $LANG['ERROR_ADD_IMAGE'] . $charManager->getErrorMessage();
		}
		$tabIndex = 1;
	}
	elseif($formSubmit == 'deleteImage'){
		if($charManager->removeCharacterStateImage($_POST['csimgid'])){
			$statusStr = $LANG['SUCCESS_DELETE_IMAGE'];
		}
		else{
			$statusStr = $LANG['ERROR_DELETE_IMAGE'] . $charManager->getErrorMessage();
		}
		$tabIndex = 1;
	}
	elseif($formSubmit == 'Save Taxonomic Relevance'){
		if(!empty($_POST['tid'])){
			if(!$charManager->insertTaxonRelevance($_POST['tid'], $_POST['relation'], $_POST['notes'])){
				$statusStr = $LANG['ERROR_ADD_REL'] . $charManager->getErrorMessage();
			}
			$tabIndex = 2;
		}
	}
	elseif($formSubmit == 'delaxon'){
		if(!$charManager->deleteTaxonRelevance($_POST['tid'])){
			$statusStr = $LANG['ERROR_DELETE_REL'] . $charManager->getErrorMessage();
		}
		$tabIndex = 2;
	}
}

if(!$cid) header('Location: index.php');
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<title><?= $LANG['CHAR_ADMIN'] ?></title>
	<link href="<?= $CSS_BASE_PATH ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../js/symb/shared.js"></script>
	<script type="text/javascript">
		var tabIndex = <?= $tabIndex ?>;

		$(document).ready(function() {
			$('#tabs').tabs({
				active: tabIndex,
				beforeLoad: function( event, ui ) {
					$(ui.panel).html("<p>Loading...</p>");
				}
			});
		});

		function toggleCharState(csId){
			toggle('cs-'+csId+'Div');
			toggle('csplus-'+csId);
		}

		function updateUnits(obj){
			var unitObj = document.getElementById("units");
			if(obj.value == "IN" || obj.value == "RN"){
				unitObj.style.display = "block";
			}
			else{
				unitObj.style.display = "none";
			}
		}

		function validateCharEditForm(f){
			if(f.charname.value == ""){
				alert(<?= json_encode($LANG['ALERT_NAME_NULL']); ?>);
				return false;
			}
			if(f.chartype.value == ""){
				alert(<?= json_encode($LANG['ALERT_TYPE_NULL']); ?>);
				return false;
			}
			if(f.sortsequence.value && !isNumeric(f.sortsequence.value)){
				alert(<?= json_encode($LANG['ALERT_SS_NUM']); ?>);
				return false;
			}
			return true;
		}

		function validateStateAddForm(f){
			if(f.charstatename.value == ""){
				alert(<?= json_encode($LANG['ALERT_STATE_NULL']); ?>);
				return false;
			}
			if(f.sortsequence.value && !isNumeric(f.sortsequence.value)){
				alert(<?= json_encode($LANG['ALERT_SS_NUM']); ?>);
				return false;
			}
			return true;
		}

		function validateStateEditForm(f){
			if(f.sortsequence.value && !isNumeric(f.sortsequence.value)){
				alert(<?= json_encode($LANG['ALERT_SS_NUM']); ?>);
				return false;
			}
			return true;
		}

		function verifyStateIllustForm(f){
			if(!f.urlupload.files[0]){
				alert(<?= json_encode($LANG['ALERT_FILE_UPLOAD']); ?>);
				return false;
			}
			return true;
		}

		function verifyCharStateDeletion(f){
			var cid = f.cid.value;
			var cs = f.cs.value;

			//Restriction when images are linked
			document.getElementById("delvercsimgspan-"+cs).style.display = "block";
			verifyCharStateImages(cid,cs);

			//Restriction when language definitions are linked
			document.getElementById("delvercslangspan-"+cs).style.display = "block";
			verifyCharStateLang(cid,cs);

			//Restriction when descriptions are linked
			document.getElementById("delverdescrspan-"+cs).style.display = "block";
			verifyDescr(cid,cs);

			f.formsubmit.disabled = false;
		}

		function verifyCharStateImages(cid,cs){
			$.ajax({
				type: "POST",
				url: 'rpc/getcharstateimgcnt.php',
				data: { cidinput: cid, csinput: cs }
			}).done(function( msg ) {
				document.getElementById("delvercsimgspan-"+cs).style.display = "none";
				if(msg > 0){
					document.getElementById("delcsimgfaildiv-"+cs).style.display = "block";
				}
				else{
					document.getElementById("delcsimgappdiv-"+cs).style.display = "block";
				}
			});
		}

		function verifyCharStateLang(cid,cs){
			$.ajax({
				type: "POST",
				url: 'rpc/getcharstatelangcnt.php',
				data: { cidinput: cid, csinput: cs }
			}).done(function( msg ) {
				document.getElementById("delvercslangspan-"+cs).style.display = "none";
				if(msg > 0){
					document.getElementById("delcslangfaildiv-"+cs).style.display = "block";
				}
				else{
					document.getElementById("delcslangappdiv-"+cs).style.display = "block";
				}
			});
		}

		function verifyDescr(cid,cs){
			$.ajax({
				type: "POST",
				url: 'rpc/getdescrcnt.php',
				data: { cidinput: cid, csinput: cs }
			}).done(function( msg ) {
				document.getElementById("delverdescrspan-"+cs).style.display = "none";
				if(msg > 0){
					document.getElementById("deldescrfaildiv-"+cs).style.display = "block";
				}
				else{
					document.getElementById("deldescrappdiv-"+cs).style.display = "block";
				}
			});
		}

		function validateTaxonAddForm(f){
			if(f.tid.value == ''){
				alert(<?= json_encode($LANG['ALERT_SELECT_TAXON']); ?>);
				return false;
			}
			return true;
		}

		function openHeadingAdmin(){
			newWindow = window.open("headingadmin.php","headingWin","scrollbars=1,toolbar=0,resizable=1,width=800,height=600,left=50,top=50");
			if (newWindow.opener == null) newWindow.opener = self;
		}

		function openGlossaryPopup(glossid){
			var urlStr = "../../glossary/individual.php?glossid="+glossid;
			glossWindow = window.open(urlStr,'popup','toolbar=0,status=1,scrollbars=1,width=900,height=450,left=20,top=20');
			if(glossWindow.opener == null) glossWindow.opener = self;
			return false;
		}
	</script>
	<style>
		.icon-img{ width: 1.1em }
		fieldset{ margin:15px;padding:15px; }
		legend{ font-weight: bold; }
		label{ font-weight: bold; }
	</style>
</head>
<body>
	<?php
	include($SERVER_ROOT . '/includes/header.php');
	?>
	<div class='navpath'>
		<a href='../../index.php'><?= $LANG['HOME'] ?></a> &gt;&gt;
		<a href='index.php'><b><?= $LANG['CHAR_MANAGE'] ?></b></a>
	</div>
	<div role="main" id="innertext">
		<h1 class="page-heading screen-reader-only"><?= $LANG['TAXON_CHAR_ADMIN'] ?></h1>
		<?php
		if($isEditor){
			if($statusStr){
				?>
				<hr/>
				<div style="margin:15px;color:<?= (strpos($statusStr,'SUCCESS')===0?'green':'red') ?>;">
					<?= Sanitize::outString($statusStr) ?>
				</div>
				<hr/>
				<?php
			}
			$charStateArr = $charManager->getCharacterStateArr();
			$charArr = $charManager->getCharacterArrByCid();
			?>
			<div style="font-weight:bold;font-size:150%;margin:15px;"><?= Sanitize::outString($charArr['charName']) ?></div>
			<div id="tabs" style="margin:0px;">
				<ul>
					<li><a href="#chardetaildiv"><span><?= $LANG['DETAILS'] ?></span></a></li>
					<li><a href="#charstatediv"><span><?= $LANG['CHAR_STATES'] ?></span></a></li>
					<li><a href="taxonomylinkage.php?cid=<?= $cid ?>"><span><?= $LANG['TAXON_LINKAGES'] ?></span></a></li>
					<li><a href="#chardeldiv"><span><?= $LANG['ADMIN'] ?></span></a></li>
				</ul>
				<div id="chardetaildiv">
					<form name="chareditform" action="chardetails.php" method="post" onsubmit="return validateCharEditForm(this)">
						<fieldset>
							<legend><?= $LANG['CHAR_DETAILS'] ?></legend>
							<div style="padding-top:4px;">
								<label for="charname"><?= $LANG['CHAR_NAME'] ?></label><br />
								<input type="text" id="charname" name="charname" maxlength="150" style="width:400px;" value="<?= Sanitize::outString($charArr['charName']) ?>" />
							</div>
							<div style="padding-top:8px;float:left;">
								<div style="float:left;">
									<label for="type"><?= $LANG['TYPE'] ?></label><br />
									<select id="type" name="chartype" style="width:180px;" onchange="updateUnits(this);">
										<option value="UM"><?= $LANG['MULTI_STATE'] ?></option>
										<option value="IN" <?= ($charArr['charType']=='IN'?'SELECTED':'') ?>><?= $LANG['INTEGER'] ?></option>
										<option value="RN" <?= ($charArr['charType']=='RN'?'SELECTED':'') ?>><?= $LANG['REAL_NUMBER'] ?></option>
									</select>
								</div>
								<div id="units" style="display:<?= ((($charArr['charType']=='IN')||($charArr['charType']=='RN'))?'block':'none') ?>;margin-left:15px;float:left;">
									<label for="units"><?= $LANG['UNITS'] ?></label><br />
									<input type="text" id="units" name="units" maxlength="45" style="width:100px;" value="<?= Sanitize::outString($charArr['units']) ?>" title="" />
								</div>
								<div style="margin-left:15px;float:left;">
									<label for="difficultyrank"><?= $LANG['DIFFICULTY'] ?></label><br />
									<select id="difficultyrank" name="difficultyrank" style="width:100px;">
										<option value="1"><?= $LANG['EASY'] ?></option>
										<option value="2" <?= ($charArr['difficultyRank']=='2'?'SELECTED':'') ?>><?= $LANG['INTERMEDIATE'] ?></option>
										<option value="3" <?= ($charArr['difficultyRank']=='3'?'SELECTED':'') ?>><?= $LANG['ADVANCED'] ?></option>
										<option value="4" <?= ($charArr['difficultyRank']=='4'?'SELECTED':'') ?>><?= $LANG['HIDDEN'] ?></option>
									</select>
								</div>
								<div style="float:left;margin-left:15px;">
									<label for="hid"><?= $LANG['GROUPING'] ?></label><br />
									<select id="hid" name="hid">
										<option value=""><?= $LANG['NOT_ASSIGNED'] ?></option>
										<option value="">---------------------</option>
										<?php
										$headingArr = $charManager->getCharacterHeadingArr();
										asort($headingArr);
										foreach($headingArr as $k => $v){
											echo '<option value="' . $k . '" ' . ($k==$charArr['hid']?'SELECTED':'') . '>' . Sanitize::outString($v['name']) . '</option>';
										}
										?>
									</select>
									<a href="#" title="Edit Groupings" onclick="openHeadingAdmin(); return false;"><img src="../../images/edit.png" class="icon-img" alt="Edit Icon" /></a>
								</div>
							</div>
							<div style="padding-top:8px;clear:both;">
								<label for="helpurl"><?= $LANG['HELP_URL'] ?></label><br />
								<input type="text" id="helpurl" name="helpurl" maxlength="500" style="width:90%;" value="<?= Sanitize::outString($charArr['helpUrl']) ?>" />
								<?php
								if($charArr['helpUrl'] && substr($charArr['helpUrl'],0,4) == 'http'){
									echo '<a href="' . Sanitize::outString($charArr['helpUrl']) . '" target="_blank"><img src="../../images/link2.png" class="icon-img" ></a>';
								}
								?>
							</div>
							<?php
							$glossaryArr = $charManager->getGlossaryList();
							if($glossaryArr){
								?>
								<div style="padding-top:8px;padding-bottom:8px;clear:both;">
									<label for="glossid"><?= $LANG['GLOSSARY_LINK'] ?></label><br />
									<select id="glossid" name="glossid" style="max-width: 90%">
										<option value="">------------------------</option>
										<?php
										foreach($glossaryArr as $glossArr){
											foreach($glossArr as $glossID => $gArr){
												echo '<option value="'.$glossID.'" '.($charArr['glossID']==$glossID?'selected':'').'>'.$gArr['term'].' ('.$gArr['lang'].')</option>';
											}
										}
										?>
									</select>
									<?php
									if($charArr['glossID']){
										?>
										<a href="#" onclick="openGlossaryPopup(<?= $charArr['glossID'] ?>);return false;"><img src="../../images/link2.png" class="icon-img"></a>
										<?php
									}
									?>
								</div>
								<?php
							}
							?>
							<div style="padding-top:8px;">
								<label for="description"><?= $LANG['DIFFICULTY'] ?></label><br />
								<input type="text" id="description" name="description" maxlength="255" style="width:90%;" value="<?= Sanitize::outString($charArr['description']) ?>" />
							</div>
							<div style="padding-top:8px;">
								<label for="notes"><?= $LANG['NOTES'] ?></label><br />
								<input type="text" id="notes" name="notes" maxlength="255" style="width:90%;" value="<?= Sanitize::outString($charArr['notes']) ?>" />
							</div>
							<div style="padding-top:8px;">
								<label for="sortsequence"><?= $LANG['SORT_SQNCE'] ?></label><br />
								<input type="text" id="sortsequence" name="sortsequence" style="width:80px;" value="<?= $charArr['sortSequence'] ?>" />
							</div>
							<div style="width:100%;padding-top:6px;">
								<div style="float:left;">
									<input name="cid" type="hidden" value="<?= $cid ?>" />
									<button name="formsubmit" type="submit" value="saveCharacterEdit">Save</button>
								</div>
								<div style="float:right;">
									<label for="enteredby"><?= $LANG['ENTERED_BY'] ?>:</label>
									<input type="text" id="enteredby" name="enteredby" tabindex="96" maxlength="32" style="width:100px;" value="<?= Sanitize::outString($charArr['enteredBy']) ?>" disabled />
								</div>
							</div>
						</fieldset>
					</form>
				</div>
				<div id="charstatediv">
					<div style="float:right;margin:10px;">
						<a href="#" title="<?= $LANG['CREATE_CHAR_STATE'] ?>" onclick="toggle('newstatediv');">
							<img src="../../images/add.png" class="icon-img" alt="<?= $LANG['CREATE_CHAR_STATE'] ?>" />
						</a>
					</div>
					<div id="newstatediv" style="display:<?= ($charStateArr?'none':'block') ?>;">
						<form name="stateaddform" action="chardetails.php" method="post" onsubmit="return validateStateAddForm(this)">
							<fieldset>
								<legend><?= $LANG['ADD_CHAR_STATE'] ?></legend>
								<div style="padding-top:4px;">
									<label for="charstatename"><?= $LANG['CHAR_STATE_NAME'] ?></label><br />
									<input type="text" id="charstatename" name="charstatename" maxlength="255" style="width:400px;" />
								</div>
								<div style="padding-top:4px;">
									<label for="add_description"><?= $LANG['DESCRIPTION'] ?></label><br />
									<input type="text" id="add_description" name="description" maxlength="255" style="width:90%;" />
								</div>
								<?php
								if($glossaryArr){
									?>
									<div style="padding-top:8px;padding-bottom:8px;clear:both;">
										<label for="glossid-state"><?= $LANG['GLOSSARY_LINK'] ?></label><br />
										<select id="glossid-state" name="glossid">
											<option value="">------------------------</option>
											<?php
											foreach($glossaryArr as $glossArr){
												foreach($glossArr as $glossID => $gArr){
													echo '<option value="'.$glossID.'">'.$gArr['term'].' ('.$gArr['lang'].')</option>';
												}
											}
											?>
										</select>
									</div>
									<?php
								}
								?>
								<div style="padding-top:4px;">
									<label for="add_notes"><?= $LANG['NOTES'] ?></label><br />
									<input type="text" id="add_notes" name="notes" style="width:90%;" />
								</div>
								<div style="padding-top:4px;">
									<label for="add_sortsequence"><?= $LANG['SORT_SQNCE'] ?></label><br />
									<input type="text" id="add_sortsequence" name="sortsequence" style="width:80px" />
								</div>
								<div style="width:100%;padding-top:6px;">
									<input name="cid" type="hidden" value="<?= $cid ?>" />
									<button name="formsubmit" type="submit" value="addState"><?= $LANG['ADD_CHAR_STATE'] ?></button>
								</div>
							</fieldset>
						</form>
					</div>
					<?php
					if($charStateArr){
						echo '<h3>Character States</h3>';
						foreach($charStateArr as $cs => $stateArr){
							?>
							<div>
								<div id="csplus-<?= $cs ?>" style="margin:5px;">
									<a href="#" onclick="toggleCharState(<?= $cs ?>);return false;">
										<img src="../../images/plus.png" class="icon-img" >
										<?= Sanitize::outString($stateArr['charStateName']) ?>
									</a>
								</div>
								<div id="<?= 'cs-'.$cs.'Div' ?>" style="display:none;">
									<div style="margin:5px;">
										<a href="#" onclick="toggleCharState(<?= $cs ?>);return false;">
											<img src="../../images/minus.png" class="icon-img" >
											<?= Sanitize::outString($stateArr['charStateName']) ?>
										</a>
									</div>
									<form name="stateeditform-<?= $cs ?>" action="chardetails.php" method="post" onsubmit="return validateStateEditForm(this)">
										<fieldset>
											<legend><?= $LANG['CHAR_STATE_DETAILS'] ?></legend>
											<div>
												<label for="charstatename-<?= $cs ?>"><?= $LANG['CHAR_STATE_NAME'] ?></label><br />
												<input type="text" id="charstatename-<?= $cs ?>" name="charstatename" maxlength="255" style="width:300px;" value="<?= Sanitize::outString($stateArr['charStateName']) ?>" />
											</div>
											<div style="padding-top:2px;">
												<label for="description-<?= $cs ?>"><?= $LANG['DESCRIPTION'] ?></label><br />
												<input type="text" id="description-<?= $cs ?>" name="description" maxlength="255" style="width:90%;" value="<?= Sanitize::outString($stateArr['description']) ?>"/>
											</div>
											<?php
											if($glossaryArr){
												?>
												<div style="padding-top:8px;padding-bottom:8px;clear:both;">
													<label for="glossid-<?= $cs ?>"><?= $LANG['GLOSSARY_LINK'] ?></label><br />
													<select id="glossid-<?= $cs ?>" name="glossid" style="max-width: 90%">
														<option value="">------------------------</option>
														<?php
														foreach($glossaryArr as $glossArr){
															foreach($glossArr as $glossID => $gArr){
																echo '<option value="'.$glossID.'" '.($stateArr['glossID']==$glossID?'selected':'').'>'.$gArr['term'].' ('.$gArr['lang'].')</option>';
															}
														}
														?>
													</select>
													<?php
													if($stateArr['glossID']){
														?>
														<a href="#" onclick="openGlossaryPopup('.$stateArr['glossid'].');return false;"><img src="../../images/link2.png" class="icon-img"></a>
														<?php
													}
													?>
												</div>
												<?php
											}
											?>
											<div style="padding-top:2px;">
												<label for="notes-<?= $cs ?>"><?= $LANG['NOTES'] ?></label><br />
												<input type="text" id="notes-<?= $cs ?>" name="notes" style="width:90%;" value="<?= Sanitize::outString($stateArr['notes']) ?>" />
											</div>
											<div style="padding-top:2px;">
												<div style="float:right;">
													<label for="enteredby-<?= $cs ?>"><?= $LANG['ENTERED_BY'] ?>:</label><br/>
													<input type="text" id="enteredby-<?= $cs ?>" name="enteredby" value="<?= Sanitize::outString($stateArr['enteredBy']) ?>" disabled />
												</div>
												<div>
													<label for="sortsequence-<?= $cs ?>"><?= $LANG['SORT_SQNCE'] ?></label><br />
													<input type="text" id="sortsequence-<?= $cs ?>" name="sortsequence" value="<?= $stateArr['sortSequence'] ?>" style="width:80px" />
												</div>
											</div>
											<div style="width:100%;margin:20px 0px 10px 20px;">
												<input name="cid" type="hidden" value="<?= $cid ?>" />
												<input name="cs" type="hidden" value="<?= $cs ?>" />
												<button name="formsubmit" type="submit" value="saveState"><?= $LANG['SAVE'] ?></button>
											</div>
										</fieldset>
									</form>
									<fieldset>
										<legend>Illustration</legend>
										<?php
										if($imgArr = $charManager->getCharacterStateImageArr()){
											?>
											<div style="padding-top:2px;">
												<a href="<?= Sanitize::outString($imgArr['url']) ?>" target="_blank"><img src="<?= Sanitize::outString($imgArr['url']) ?>" style="width:200px;" /></a>
											</div>
											<form name="stateillustdelform-<?= $imgArr['csImgID'] ?>" action="chardetails.php" method="post" onsubmit="return verifyStateIllustDelForm(this)" >
												<div style="margin:10px;">
													<input name="cid" type="hidden" value="<?= $cid ?>" />
													<input name="cs" type="hidden" value="<?= $cs ?>" />
													<input name="csimgid" type="hidden" value="<?= $imgArr['csImgID'] ?>" />
													<button name="formsubmit" type="submit" value="deleteImage"><?= $LANG['DELETE_IMAGE'] ?></button>
												</div>
											</form>
											<?php
										}
										else{
											?>
											<form name="stateillustform-<?= $cs ?>" action="chardetails.php" method="post" enctype="multipart/form-data" onsubmit="return verifyStateIllustForm(this)" >
												<div style="padding-top:2px;">
													<label for="urlupload-<?= $cs ?>"><?= $LANG['FILE_UPLOAD'] ?>:</label>
													<input id="urlupload-<?= $cs ?>" name="urlupload" type="file" size="50" />
													<input name="MAX_FILE_SIZE" type="hidden" value="1000000" />
												</div>
												<div style="padding-top:2px;">
													<label for="imgnotes-<?= $cs ?>"><?= $LANG['NOTES'] ?>:</label>
													<input id="imgnotes-<?= $cs ?>" name="notes" type="text" style="width:90%" />
												</div>
												<div style="padding-top:2px;">
													<label for="imgsortsequence-<?= $cs ?>"><?= $LANG['SORT'] ?>:</label>
													<input id="imgsortsequence-<?= $cs ?>" name="sortsequence" type="text" />
												</div>
												<div style="padding-top:2px;">
													<input name="cid" type="hidden" value="<?= $cid ?>" />
													<input name="cs" type="hidden" value="<?= $cs ?>" />
													<button name="formsubmit" type="submit" value="uploadImage"><?= $LANG['UPLOAD_IMAGE'] ?></button>
												</div>
											</form>
											<?php
										}
										?>
									</fieldset>
									<form name="statedelform-<?= $cs ?>" action="chardetails.php" method="post" onsubmit="return confirm('<?= $LANG['CONFIRM_DELETE_STATE'] ?>')">
										<fieldset>
											<legend><?= $LANG['DELETE_CHAR_STATE'] ?></legend>
											<div>
												<?= $LANG['NOTE_RECORD_DELETE'] ?>
											</div>
											<div style="margin:15px;">
												<button name="verifycsdelete" type="button" onclick="verifyCharStateDeletion(this.form);return false;"><?= $LANG['EVAL_DELETE'] ?></button>
											</div>
											<div id="delverimgdiv" style="margin:15px;">
												<b><?= $LANG['IMAGE_LINKS'] ?>: </b>
												<span id="delvercsimgspan-<?= $cs ?>" style="color:orange;display:none;"><?= $LANG['CHECK_IMAGE_LINKS'] ?></span>
												<div id="delcsimgfaildiv-<?= $cs ?>" style="display:none;style:0px 10px 10px 10px;">
													<span style="color:red;"><?= $LANG['WARNING'] ?>:</span>
													<?= $LANG['DELETE_WARN_IMAGE'] ?>
												</div>
												<div id="delcsimgappdiv-<?= $cs ?>" style="display:none;">
													<span style="color:green;"><?= $LANG['APPROVE_DELETE'] ?></span>
													<?= $LANG['NO_ASSOC_IMAGE'] ?>
												</div>
											</div>
											<div id="delverlangdiv" style="margin:15px;">
												<b><?= $LANG['LANG_LINKS'] ?>: </b>
												<span id="delvercslangspan-<?= $cs ?>" style="color:orange;display:none;"><?= $LANG['CHECK_LANG_LINKS'] ?></span>
												<div id="delcslangfaildiv-<?= $cs ?>" style="display:none;style:0px 10px 10px 10px;">
													<span style="color:red;"><?= $LANG['WARNING'] ?>:</span>
													<?= $LANG['DELETE_WARN_LANG'] ?>
												</div>
												<div id="delcslangappdiv-<?= $cs ?>" style="display:none;">
													<span style="color:green;"><?= $LANG['APPROVE_DELETE'] ?></span>
													<?= $LANG['NO_ASSOC_LANG'] ?>
												</div>
											</div>
											<div id="delverdescrdiv" style="margin:15px;">
												<b><?= $LANG['DESC_LINKS'] ?>: </b>
												<span id="delverdescrspan-<?= $cs ?>" style="color:orange;display:none;"><?= $LANG['CHECK_DESC_LINKS'] ?></span>
												<div id="deldescrfaildiv-<?= $cs ?>" style="display:none;style:0px 10px 10px 10px;">
													<span style="color:red;"><?= $LANG['WARNING'] ?>:</span>
													<?= $LANG['DELETE_WARN_DESC'] ?>
												</div>
												<div id="deldescrappdiv-<?= $cs ?>" style="display:none;">
													<span style="color:green;"><?= $LANG['APPROVE_DELETE'] ?></span>
													<?= $LANG['NO_ASSOC_DESC'] ?>
												</div>
											</div>
											<div style="margin:15px;">
												<input name="cid" type="hidden" value="<?= $cid ?>" />
												<input name="cs" type="hidden" value="<?= $cs ?>" />
												<button name="formsubmit" type="submit" value="deleteState" disabled><?= $LANG['DELETE_STATE'] ?></button>
											</div>
										</fieldset>
									</form>
								</div>
							</div>
							<?php
						}
					}
					?>
				</div>
				<div id="chardeldiv">
					<form name="delcharform" action="chardetails.php" method="post" onsubmit="return confirm('<?= $LANG['CONFIRM_DELETE_CHAR'] ?>')">
						<fieldset style="width:700px;">
							<legend><b><?= $LANG['DELETE_CHAR'] ?></b></legend>
							<?php
							if($charStateArr){
								?>
								<div style="margin-bottom:15px;">
									<?= $LANG['NOTE_CHAR_DELETE'] ?>
								</div>
								<?php
							}
							?>
							<input name="cid" type="hidden" value="<?= $cid ?>" />
							<button name="formsubmit" type="submit" value="deleteChar" <?php if($charStateArr) echo 'DISABLED' ?>><?= $LANG['DELETE'] ?></button>
						</fieldset>
					</form>
				</div>
			</div>
			<?php
		}
		else{
			if(!$isEditor){
				echo '<h2>' . $LANG['NO_AUTH_ADD_CHAR'] . '</h2>';
			}
			else{
				echo '<h2>' . $LANG['UNKNOWN_ERROR'] . '</h2>';
			}
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
