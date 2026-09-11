<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceTraitAdmin.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load('collections/traitarr/admin/traitdetails');

header('Content-Type: text/html; charset=' . $CHARSET);

if(!$SYMB_UID) header('Location: ../../../profile/index.php?refurl=../ident/admin/index.php');

$cid = array_key_exists('cid', $_REQUEST) ? Sanitize::int($_REQUEST['cid']) : 0;
$tabIndex = array_key_exists('tabindex', $_REQUEST) ? Sanitize::int($_REQUEST['tabindex']) : 0;
$langId = array_key_exists('langid', $_REQUEST) ? $_REQUEST['langid'] : '';
$formSubmit = array_key_exists('formsubmit', $_POST) ? $_POST['formsubmit'] : '';

$isEditor = false;
if($IS_ADMIN || array_key_exists('KeyAdmin', $USER_RIGHTS)) $isEditor = true;

$charManager = new OccurrenceTraitAdmin();
$charManager->setLangId($langId);
$charManager->setCid($cid);

if(!$cid) header('Location: index.php');
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<title>Character Admin</title>
	<link href="<?= $CSS_BASE_PATH ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../../js/symb/shared.js"></script>
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
				alert("Character name must not be null");
				return false;
			}
			if(f.chartype.value == ""){
				alert("Character type must not be null");
				return false;
			}
			if(f.sortsequence.value && !isNumeric(f.sortsequence.value)){
				alert("Sort Sequence can only be a numeric value");
				return false;
			}
			return true;
		}

		function validateStateAddForm(f){
			if(f.charstatename.value == ""){
				alert("Character state must not be null");
				return false;
			}
			if(f.sortsequence.value && !isNumeric(f.sortsequence.value)){
				alert("Sort sequence can only be a numeric value");
				return false;
			}
			return true;
		}

		function validateStateEditForm(f){
			if(f.sortsequence.value && !isNumeric(f.sortsequence.value)){
				alert("Sort Sequence field must be numeric");
				return false;
			}
			return true;
		}

		function verifyStateIllustForm(f){
			if(!f.urlupload.files[0]){
				alert("Select a file to upload");
				return false;
			}
			return true;
		}

		function verifyCharStateDeletion(f){
			var cid = f.cid.value;
			var cs = f.cs.value;
			var stateid = f.stateid.value;

			//Restriction when images are linked
			document.getElementById("delvercsimgspan-"+stateid).style.display = "block";
			verifyCharStateImages(cid,cs,stateid);

			//Restriction when language definitions are linked
			document.getElementById("delvercslangspan-"+stateid).style.display = "block";
			verifyCharStateLang(cid,cs,stateid);

			//Restriction when descriptions are linked
			document.getElementById("delverdescrspan-"+stateid).style.display = "block";
			verifyDescr(cid,cs,stateid);

			f.formsubmit.disabled = false;
		}

		function verifyCharStateImages(cid,cs,stateid){
			$.ajax({
				type: "POST",
				url: 'rpc/getcharstateimgcnt.php',
				data: { cidinput: cid, csinput: cs }
			}).done(function( msg ) {
				document.getElementById("delvercsimgspan-"+stateid).style.display = "none";
				if(msg > 0){
					document.getElementById("delcsimgfaildiv-"+stateid).style.display = "block";
				}
				else{
					document.getElementById("delcsimgappdiv-"+stateid).style.display = "block";
				}
			});
		}

		function verifyCharStateLang(cid,cs,stateid){
			$.ajax({
				type: "POST",
				url: 'rpc/getcharstatelangcnt.php',
				data: { cidinput: cid, csinput: cs }
			}).done(function( msg ) {
				document.getElementById("delvercslangspan-"+stateid).style.display = "none";
				if(msg > 0){
					document.getElementById("delcslangfaildiv-"+stateid).style.display = "block";
				}
				else{
					document.getElementById("delcslangappdiv-"+stateid).style.display = "block";
				}
			});
		}

		function verifyDescr(cid,cs,stateid){
			$.ajax({
				type: "POST",
				url: 'rpc/getdescrcnt.php',
				data: { cidinput: cid, csinput: cs }
			}).done(function( msg ) {
				document.getElementById("delverdescrspan-"+stateid).style.display = "none";
				if(msg > 0){
					document.getElementById("deldescrfaildiv-"+stateid).style.display = "block";
				}
				else{
					document.getElementById("deldescrappdiv-"+stateid).style.display = "block";
				}
			});
		}

		function validateTaxonAddForm(f){
			if(f.tid.value == ''){
				alert("Please select a taxonomic name!");
				return false;
			}
			return true;
		}

		function openHeadingAdmin(){
			newWindow = window.open("headingadmin.php","headingWin","scrollbars=1,toolbar=0,resizable=1,width=800,height=600,left=50,top=50");
			if (newWindow.opener == null) newWindow.opener = self;
		}

		function openGlossaryPopup(glossid){
			var urlStr = "../../../glossary/individual.php?glossid="+glossid;
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
		<a href='../../../index.php'>Home</a> &gt;&gt;
		<a href='index.php'><b>Occurrence Trait Management</b></a>
	</div>
	<div role="main" id="innertext">
		<h1 class="page-heading screen-reader-only">Occurrence Trait Administration</h1>
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
					<li><a href="#chardetaildiv"><span>Details</span></a></li>
					<li><a href="#charstatediv"><span>Character States</span></a></li>
					<li><a href="taxonomylinkage.php?cid=<?= $cid ?>"><span>Taxonomic Linkages</span></a></li>
					<li><a href="#chardeldiv"><span>Admin</span></a></li>
				</ul>
				<div id="chardetaildiv">
					<form name="chareditform" action="chardetails.php" method="post" onsubmit="return validateCharEditForm(this)">
						<fieldset>
							<legend>Character Details</legend>
							<div style="padding-top:4px;">
								<label for="charname">Character Name</label><br />
								<input type="text" id="charname" name="charname" maxlength="150" style="width:400px;" value="<?= Sanitize::outString($charArr['charName']) ?>" />
							</div>
							<div style="padding-top:8px;float:left;">
								<div style="float:left;">
									<label for="type">Type</label><br />
									<select id="type" name="chartype" style="width:180px;" onchange="updateUnits(this);">
										<option value="UM">Multi-state</option>
										<option value="IN" <?= ($charArr['charType']=='IN'?'SELECTED':'') ?>>Integer</option>
										<option value="RN" <?= ($charArr['charType']=='RN'?'SELECTED':'') ?>>Real Number</option>
									</select>
								</div>
								<div id="units" style="display:<?= ((($charArr['charType']=='IN')||($charArr['charType']=='RN'))?'block':'none') ?>;margin-left:15px;float:left;">
									<label for="units">Units</label><br />
									<input type="text" id="units" name="units" maxlength="45" style="width:100px;" value="<?= Sanitize::outString($charArr['units']) ?>" title="" />
								</div>
								<div style="margin-left:15px;float:left;">
									<label for="difficultyrank">Difficulty</label><br />
									<select id="difficultyrank" name="difficultyrank" style="width:100px;">
										<option value="1">Easy</option>
										<option value="2" <?= ($charArr['difficultyRank']=='2'?'SELECTED':'') ?>>Intermediate</option>
										<option value="3" <?= ($charArr['difficultyRank']=='3'?'SELECTED':'') ?>>Advanced</option>
										<option value="4" <?= ($charArr['difficultyRank']=='4'?'SELECTED':'') ?>>Hidden</option>
									</select>
								</div>
								<div style="float:left;margin-left:15px;">
									<label for="hid">Grouping</label><br />
									<select id="hid" name="hid">
										<option value="">Not Assigned</option>
										<option value="">---------------------</option>
										<?php
										$headingArr = $charManager->getCharacterHeadingArr();
										asort($headingArr);
										foreach($headingArr as $k => $v){
											echo '<option value="' . $k . '" ' . ($k==$charArr['hid']?'SELECTED':'') . '>' . Sanitize::outString($v['name']) . '</option>';
										}
										?>
									</select>
									<a href="#" title="Edit Groupings" onclick="openHeadingAdmin(); return false;"><img src="../../../images/edit.png" class="icon-img" alt="Edit Icon" /></a>
								</div>
							</div>
							<div style="padding-top:8px;clear:both;">
								<label for="helpurl">Help URL</label><br />
								<input type="text" id="helpurl" name="helpurl" maxlength="500" style="width:90%;" value="<?= Sanitize::outString($charArr['helpUrl']) ?>" />
								<?php
								if($charArr['helpUrl'] && substr($charArr['helpUrl'],0,4) == 'http'){
									echo '<a href="' . Sanitize::outString($charArr['helpUrl']) . '" target="_blank"><img src="../../../images/link2.png" class="icon-img" ></a>';
								}
								?>
							</div>
							<?php
							$glossaryArr = $charManager->getGlossaryList();
							if($glossaryArr){
								?>
								<div style="padding-top:8px;clear:both;">
									<label for="glossid">Glossary link</label><br />
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
										<a href="#" onclick="openGlossaryPopup(<?= $charArr['glossID'] ?>);return false;"><img src="../../../images/link2.png" class="icon-img"></a>
										<?php
									}
									?>
								</div>
								<?php
							}
							?>
							<div style="padding-top:8px;">
								<label for="description">Description</label><br />
								<input type="text" id="description" name="description" maxlength="255" style="width:90%;" value="<?= Sanitize::outString($charArr['description']) ?>" />
							</div>
							<div style="padding-top:8px;">
								<label for="notes">Notes</label><br />
								<input type="text" id="notes" name="notes" maxlength="255" style="width:90%;" value="<?= Sanitize::outString($charArr['notes']) ?>" />
							</div>
							<div style="padding-top:8px;">
								<label for="sortsequence">Sort Sequence</label><br />
								<input type="text" id="sortsequence" name="sortsequence" style="width:80px;" value="<?= $charArr['sortSequence'] ?>" />
							</div>
							<div style="width:100%;padding-top:6px;">
								<div style="float:left;">
									<input name="cid" type="hidden" value="<?= $cid ?>" />
									<button name="formsubmit" type="submit" value="saveCharacterEdit">Save</button>
								</div>
								<div style="float:right;">
									<label for="enteredby">Entered By:</label>
									<input type="text" id="enteredby" name="enteredby" tabindex="96" maxlength="32" style="width:100px;" value="<?= Sanitize::outString($charArr['enteredBy']) ?>" disabled />
								</div>
							</div>
						</fieldset>
					</form>
				</div>
				<div id="charstatediv">
					<div style="float:right;margin:10px;">
						<a href="#" title="Create New Character State" onclick="toggle('newstatediv');">
							<img src="../../../images/add.png" class="icon-img" alt="Create New Character State" />
						</a>
					</div>
					<div id="newstatediv" style="display:<?= ($charStateArr?'none':'block') ?>;">
						<form name="stateaddform" action="chardetails.php" method="post" onsubmit="return validateStateAddForm(this)">
							<fieldset>
								<legend>Add Character State</legend>
								<div style="padding-top:4px;">
									<label for="charstatename">Character State Name</label><br />
									<input type="text" id="charstatename" name="charstatename" maxlength="255" style="width:400px;" />
								</div>
								<div style="padding-top:4px;">
									<label for="add_description">Description</label><br />
									<input type="text" id="add_description" name="description" maxlength="255" style="width:90%;" />
								</div>
								<?php
								if($glossaryArr){
									?>
									<div style="padding-top:8px;clear:both;">
										<label for="glossid">Glossary link</label><br />
										<select id="glossid" name="glossid">
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
									<label for="add_notes">Notes</label><br />
									<input type="text" id="add_notes" name="notes" style="width:90%;" />
								</div>
								<div style="padding-top:4px;">
									<label for="add_sortsequence">Sort Sequence</label><br />
									<input type="text" id="add_sortsequence" name="sortsequence" style="width:80px" />
								</div>
								<div style="width:100%;padding-top:6px;">
									<input name="cid" type="hidden" value="<?= $cid ?>" />
									<button name="formsubmit" type="submit" value="addState">Add Character State</button>
								</div>
							</fieldset>
						</form>
					</div>
					<?php
					if($charStateArr){
						echo '<h3>Character States</h3>';
						foreach($charStateArr as $stateID => $stateArr){
							?>
							<div>
								<div id="csplus-<?= $stateID ?>" style="margin:5px;">
									<a href="#" onclick="toggleCharState(<?= $stateID ?>);return false;">
										<img src="../../../images/plus.png" class="icon-img" >
										<?= Sanitize::outString($stateArr['charStateName']) ?>
									</a>
								</div>
								<div id="<?= 'cs-'.$stateID.'Div' ?>" style="display:none;">
									<div style="margin:5px;">
										<a href="#" onclick="toggleCharState(<?= $stateID ?>);return false;">
											<img src="../../../images/minus.png" class="icon-img" >
											<?= Sanitize::outString($stateArr['charStateName']) ?>
										</a>
									</div>
									<form name="stateeditform-<?= $stateID ?>" action="chardetails.php" method="post" onsubmit="return validateStateEditForm(this)">
										<fieldset>
											<legend>Character State Details</legend>
											<div>
												<label for="charstatename-<?= $stateID ?>">Character State Name</label><br />
												<input type="text" id="charstatename-<?= $stateID ?>" name="charstatename" maxlength="255" style="width:300px;" value="<?= Sanitize::outString($stateArr['charStateName']) ?>" />
											</div>
											<div style="padding-top:2px;">
												<label for="description-<?= $stateID ?>">Description</label><br />
												<input type="text" id="description-<?= $stateID ?>" name="description" maxlength="255" style="width:90%;" value="<?= Sanitize::outString($stateArr['description']) ?>"/>
											</div>
											<?php
											if($glossaryArr){
												?>
												<div style="padding-top:8px;clear:both;">
													<label for="glossid-<?= $stateID ?>">Glossary link</label><br />
													<select id="glossid-<?= $stateID ?>" name="glossid" style="max-width: 90%">
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
														<a href="#" onclick="openGlossaryPopup('.$stateArr['glossid'].');return false;"><img src="../../../images/link2.png" class="icon-img"></a>
														<?php
													}
													?>
												</div>
												<?php
											}
											?>
											<div style="padding-top:2px;">
												<label for="notes-<?= $stateID ?>">Notes</label><br />
												<input type="text" id="notes-<?= $stateID ?>" name="notes" style="width:90%;" value="<?= Sanitize::outString($stateArr['notes']) ?>" />
											</div>
											<div style="padding-top:2px;">
												<div style="float:right;">
													<label for="enteredby-<?= $stateID ?>">Entered By:</label><br/>
													<input type="text" id="enteredby-<?= $stateID ?>" name="enteredby" value="<?= Sanitize::outString($stateArr['enteredBy']) ?>" disabled />
												</div>
												<div>
													<label for="sortsequence-<?= $stateID ?>">Sort Sequence</label><br />
													<input type="text" id="sortsequence-<?= $stateID ?>" name="sortsequence" value="<?= $stateArr['sortSequence'] ?>" style="width:80px" />
												</div>
											</div>
											<div style="width:100%;margin:20px 0px 10px 20px;">
												<input name="cid" type="hidden" value="<?= $cid ?>" />
												<input name="cs" type="hidden" value="<?= $stateArr['cs'] ?>" />
												<button name="formsubmit" type="submit" value="saveState">Save</button>
											</div>
										</fieldset>
									</form>
									<fieldset>
										<legend>Illustration</legend>
										<?php
										$imgArr = $charManager->getCharacterStateImageArr();
										if($imgArr['cs'] === $stateArr['cs']){
											?>
											<div style="padding-top:2px;">
												<a href="<?= Sanitize::outString($imgArr['url']) ?>" target="_blank"><img src="<?= Sanitize::outString($imgArr['url']) ?>" style="width:200px;" /></a>
											</div>
											<form name="stateillustdelform-<?= $imgArr['csImgID'] ?>" action="chardetails.php" method="post" onsubmit="return verifyStateIllustDelForm(this)" >
												<div style="margin:10px;">
													<input name="cid" type="hidden" value="<?= $cid ?>" />
													<input name="cs" type="hidden" value="<?= $stateArr['cs'] ?>" />
													<input name="csimgid" type="hidden" value="<?= $imgArr['csImgID'] ?>" />
													<button name="formsubmit" type="submit" value="deleteImage">Delete Image</button>
												</div>
											</form>
											<?php
										}
										else{
											?>
											<form name="stateillustform-<?= $stateID ?>" action="chardetails.php" method="post" enctype="multipart/form-data" onsubmit="return verifyStateIllustForm(this)" >
												<div style="padding-top:2px;">
													<label for="urlupload-<?= $stateID ?>">File Upload:</label>
													<input id="urlupload-<?= $stateID ?>" name="urlupload" type="file" size="50" />
													<input name="MAX_FILE_SIZE" type="hidden" value="1000000" />
												</div>
												<div style="padding-top:2px;">
													<label for="imgnotes-<?= $stateID ?>">Notes:</label>
													<input id="imgnotes-<?= $stateID ?>" name="notes" type="text" style="width:90%" />
												</div>
												<div style="padding-top:2px;">
													<label for="imgsortsequence-<?= $stateID ?>">Sort:</label>
													<input id="imgsortsequence-<?= $stateID ?>" name="sortsequence" type="text" />
												</div>
												<div style="padding-top:2px;">
													<input name="cid" type="hidden" value="<?= $cid ?>" />
													<input name="cs" type="hidden" value="<?= $stateArr['cs'] ?>" />
													<button name="formsubmit" type="submit" value="uploadImage">Upload Image</button>
												</div>
											</form>
											<?php
										}
										?>
									</fieldset>
									<form name="statedelform-<?= $stateID ?>" action="chardetails.php" method="post" onsubmit="return confirm('Are you sure you want to permanently delete this character state?')">
										<fieldset>
											<legend>Delete Character State</legend>
											<div>
												Record first needs to be evaluated before it can be deleted from the system.
												The evaluation ensures that the deletion will not interfer with
												the integrity of linked data.
											</div>
											<div style="margin:15px;">
												<button name="verifycsdelete" type="button" onclick="verifyCharStateDeletion(this.form);return false;">Evaluate record for deletion</button>
											</div>
											<div id="delverimgdiv" style="margin:15px;">
												<b>Image Links: </b>
												<span id="delvercsimgspan-<?= $stateID ?>" style="color:orange;display:none;">checking image links...</span>
												<div id="delcsimgfaildiv-<?= $stateID ?>" style="display:none;style:0px 10px 10px 10px;">
													<span style="color:red;">Warning:</span>
													One or more images are linked to this charcter state.
													Deleting this character state will also permanently remove these images.
												</div>
												<div id="delcsimgappdiv-<?= $stateID ?>" style="display:none;">
													<span style="color:green;">Approved for deletion.</span>
													No images are directly associated with this character state.
												</div>
											</div>
											<div id="delverlangdiv" style="margin:15px;">
												<b>Language Links: </b>
												<span id="delvercslangspan-<?= $stateID ?>" style="color:orange;display:none;">checking language links...</span>
												<div id="delcslangfaildiv-<?= $stateID ?>" style="display:none;style:0px 10px 10px 10px;">
													<span style="color:red;">Warning:</span>
													Charcter state has links to langauge records.
													Deleting this character state will also permanently remove this data.
												</div>
												<div id="delcslangappdiv-<?= $stateID ?>" style="display:none;">
													<span style="color:green;">Approved for deletion.</span>
													No langage mappings are directly associated with this character state.
												</div>
											</div>
											<div id="delverdescrdiv" style="margin:15px;">
												<b>Description Links: </b>
												<span id="delverdescrspan-<?= $stateID ?>" style="color:orange;display:none;">checking description links...</span>
												<div id="deldescrfaildiv-<?= $stateID ?>" style="display:none;style:0px 10px 10px 10px;">
													<span style="color:red;">Warning:</span>
													One or more descriptions are linked to this charcter state.
													Delete this character state will also permanently remove these descriptions.
												</div>
												<div id="deldescrappdiv-<?= $stateID ?>" style="display:none;">
													<span style="color:green;">Approved for deletion.</span>
													No descriptions are directly associated with this character state.
												</div>
											</div>
											<div style="margin:15px;">
												<input id="stateid" name="stateid" type="hidden" value="<?= $stateID ?>">
												<input name="cid" type="hidden" value="<?= $cid ?>" />
												<input name="cs" type="hidden" value="<?= $stateArr['cs'] ?>" />
												<button name="formsubmit" type="submit" value="deleteState" disabled>Delete State</button>
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
					<form name="delcharform" action="chardetails.php" method="post" onsubmit="return confirm('Are you sure you want to permanently delete this character?')">
						<fieldset style="width:700px;">
							<legend><b>Delete Character</b></legend>
							<?php
							if($charStateArr){
								?>
								<div style="margin-bottom:15px;">
									Character cannot be deleted until all character states are removed
								</div>
								<?php
							}
							?>
							<input name="cid" type="hidden" value="<?= $cid ?>" />
							<button name="formsubmit" type="submit" value="deleteChar" <?php if($charStateArr) echo 'DISABLED' ?>>Delete</button>
						</fieldset>
					</form>
				</div>
			</div>
			<?php
		}
		else{
			if(!$isEditor){
				echo '<h2>You are not authorized to add characters</h2>';
			}
			else{
				echo '<h2>ERROR: unknown error, please contact system administrator</h2>';
			}
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
