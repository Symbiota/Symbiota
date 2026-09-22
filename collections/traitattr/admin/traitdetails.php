<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceTraitAdmin.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load('collections/traitarr/admin/traitdetails');

header('Content-Type: text/html; charset=' . $CHARSET);

if(!$SYMB_UID) header('Location: ../../../profile/index.php?refurl=../ident/admin/index.php');

$traitID = array_key_exists('traitid', $_REQUEST) ? Sanitize::int($_REQUEST['traitid']) : 0;
$tabIndex = array_key_exists('tabindex', $_REQUEST) ? Sanitize::int($_REQUEST['tabindex']) : 0;
$langId = array_key_exists('langid', $_REQUEST) ? $_REQUEST['langid'] : '';
$formSubmit = array_key_exists('formsubmit', $_POST) ? $_POST['formsubmit'] : '';

$isEditor = false;
if($IS_ADMIN || array_key_exists('KeyAdmin', $USER_RIGHTS)) $isEditor = true;

$traitManager = new OccurrenceTraitAdmin();
$traitManager->setTraitID($traitID);

var_dump($_POST);

$statusStr = '';
if($formSubmit && $isEditor){
	if($formSubmit == 'createTrait'){
		if($traitManager->insertTrait($_POST)){
			$traitID = $traitManager->getTraitID();
		}
		else{
			$statusStr = "Error Creating Trait";
		}
	}
	elseif($formSubmit == 'saveTraitEdit'){
		if(!$traitManager->updateTrait($_POST)){
			$statusStr = "Error Editing Trait";
		}
	}
	elseif($formSubmit == 'addState'){
		if(!$traitManager->insertTraitState($_POST)){
			$statusStr = $LANG['ERROR_ADD_STATE']  . $traitManager->getErrorMessage();
		}
		$tabIndex = 1;
	}
	elseif($formSubmit == 'saveState'){
		if(!$traitManager->updateTraitState($_POST)){
			$statusStr = $LANG['ERROR_EDIT_STATE'] . $traitManager->getErrorMessage();
		}
		$tabIndex = 1;
	}
	elseif($formSubmit == 'deleteState'){
		if(!$traitManager->deleteTraitState($_POST['stateid'])){
			$statusStr = $LANG['ERROR_DELETE_STATE'] . $traitManager->getErrorMessage();
		}
		$tabIndex = 1;
	}
	elseif($formSubmit == 'deleteTrait'){
		if($traitManager->deleteTrait()){
			$traitID = 0;
		}
		else{
			$statusStr = $LANG['ERROR_DELETE_TAXON'] . $traitManager->getErrorMessage();
		}
	}
}

if(!$traitID) header('Location: index.php');
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<title>Trait Admin</title>
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

		function toggleCharState(statecodeId){
			toggle('statecode-'+statecodeId+'Div');
			toggle('statecodeplus-'+statecodeId);
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
			if(f.traitname.value == ""){
				alert("Trait name must not be null");
				return false;
			}
			if(f.traittype.value == ""){
				alert("Trait type must not be null");
				return false;
			}
			return true;
		}

		function validateStateAddForm(f){
			if(f.statename.value == ""){
				alert("Trait state must not be null");
				return false;
			}
			return true;
		}

		function validateStateEditForm(f){
			if(f.sortseq.value && !isNumeric(f.sortseq.value)){
				alert("Sort Sequence field must be numeric");
				return false;
			}
			return true;
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
			$traitArr = $traitManager->getTraitArrById();
			$traitStateArr = $traitManager->getTraitStateArr();
			?>
			<div style="font-weight:bold;font-size:150%;margin:15px;"><?= Sanitize::outString($traitArr['traitName']) ?></div>
			<div id="tabs" style="margin:0px;">
				<ul>
					<li><a href="#traitdetaildev"><span>Details</span></a></li>
					<li><a href="#traitstatediv"><span>Trait States</span></a></li>
					<li><a href="taxonomylinkage.php?traitid=<?= $traitID ?>"><span>Taxonomic Linkages</span></a></li>
					<li><a href="#traitdeldiv"><span>Admin</span></a></li>
				</ul>
				<div id="traitdetaildev">
					<form name="traiteditform" action="traitdetails.php" method="post" onsubmit="return validateCharEditForm(this)">
						<fieldset>
							<legend>Trait Details</legend>
							<div style="padding-top:4px;">
								<label for="traitname">Trait Name</label><br />
								<input type="text" id="traitname" name="traitname" maxlength="150" style="width:400px;" value="<?= Sanitize::outString($traitArr['traitName']) ?>" />
							</div>
							<div style="padding-top:8px;float:left;">
								<div style="float:left;">
									<label for="type">Type</label><br />
									<select id="type" name="traittype" style="width:180px;">
										<option value="UM">UM</option>
										<option value="OM">OM</option>
										<option value="TF">TF</option>
										<option value="NU">NU</option>
									</select>
								</div>
								<div id="units" style="margin-left:15px;float:left;">
									<label for="units">Units</label><br />
									<input type="text" id="units" name="units" maxlength="45" style="width:100px;" value="<?= Sanitize::outString($traitArr['units']) ?>" />
								</div>
								<div style="float:left;margin-left:15px;">
									<label for="hid">Grouping</label><br />
									<select id="hid" name="hid">
										<option value="">Not Assigned</option>
										<option value="">---------------------</option>
										<?php
										$headingArr = []; //$traitManager->getCharacterHeadingArr();
										asort($headingArr);
										foreach($headingArr as $k => $v){
											echo '<option value="' . $k . '" ' . ($k==$traitArr['hid']?'SELECTED':'') . '>' . Sanitize::outString($v['name']) . '</option>';
										}
										?>
									</select>
									<a href="#" title="Edit Groupings" onclick="openHeadingAdmin(); return false;"><img src="../../../images/edit.png" class="icon-img" alt="Edit Icon" /></a>
								</div>
							</div>
							<div style="padding-top:8px;clear:both;">
								<label for="refurl">Reference URL</label><br />
								<input type="text" id="refurl" name="refurl" maxlength="500" style="width:90%;" value="<?= Sanitize::outString($traitArr['refUrl']) ?>" />
								<?php
								if($traitArr['refUrl'] && substr($traitArr['refUrl'],0,4) == 'http'){
									echo '<a href="' . Sanitize::outString($traitArr['refUrl']) . '" target="_blank"><img src="../../../images/link2.png" class="icon-img" ></a>';
								}
								?>
							</div>
							<div style="padding-top:8px;">
								<label for="description">Description</label><br />
								<input type="text" id="description" name="description" maxlength="255" style="width:90%;" value="<?= Sanitize::outString($traitArr['description']) ?>" />
							</div>
							<div style="padding-top:8px;">
								<label for="notes">Notes</label><br />
								<input type="text" id="notes" name="notes" maxlength="255" style="width:90%;" value="<?= Sanitize::outString($traitArr['notes']) ?>" />
							</div>
							<div style="padding-top:8px;">
								<label for="traitname">isPublic:</label>
								<input type="hidden" name="isPublic" value="0"/>
								<input type="checkbox" id="isPublic" name="isPublic" value="1" <?= $traitArr['isPublic'] === 1 ? 'checked' : '' ?> />
							</div>
							<div style="padding-top:8px;">
							<label for="dynamicproperties">Dynamic Properties/Input Type:</label>
								<select id="dynamicproperties" name="dynamicproperties">
									<option value="radio" <?= $traitArr['dynamicProperties'] == '[{"controlType":"radio"}]' ? 'selected' : ''; ?> >Radio Button</option>
									<option value="checkbox" <?= $traitArr['dynamicProperties'] == '[{"controlType":"checkbox"}]' ? 'selected' : ''; ?> >Checkbox</option>
									<option value="select" <?= $traitArr['dynamicProperties'] == '[{"controlType":"select"}]' ? 'selected' : ''; ?> >Select</option>
								</select>
							</div>
							<div style="width:100%;padding-top:6px;">
								<div style="float:left;">
									<input name="traitid" type="hidden" value="<?= $traitID ?>" />
									<button name="formsubmit" type="submit" value="saveTraitEdit">Save</button>
								</div>
							</div>
						</fieldset>
					</form>
				</div>
				<div id="traitstatediv">
					<div style="float:right;margin:10px;">
						<a href="#" title="Create New Trait State" onclick="toggle('newstatediv');">
							<img src="../../../images/add.png" class="icon-img" alt="Create New Trait State" />
						</a>
					</div>
					<div id="newstatediv" style="display:<?= ($traitStateArr?'none':'block') ?>;">
						<form name="stateaddform" action="traitdetails.php" method="post" onsubmit="return validateStateAddForm(this)">
							<fieldset>
								<legend>Add Trait State</legend>
								<div style="padding-top:4px;">
									<label for="statename">Trait State Name</label><br />
									<input type="text" id="statename" name="statename" maxlength="255" style="width:400px;" />
								</div>
								<div style="padding-top:4px;">
									<label for="add_description">Description</label><br />
									<input type="text" id="add_description" name="description" maxlength="255" style="width:90%;" />
								</div>
								<div style="padding-top:4px;">
									<label for="add_description">Reference URL</label><br />
									<input type="text" id="add_refurl" name="refurl" maxlength="255" style="width:90%;" />
								</div>
								<div style="padding-top:4px;">
									<label for="add_notes">Notes</label><br />
									<input type="text" id="add_notes" name="notes" style="width:90%;" />
								</div>
								<div style="padding-top:4px;">
									<label for="sortseq">Sort Sequence</label><br />
									<input type="number" id="sortseq" name="sortseq" style="width:80px;" />
								</div>
								<div style="width:100%;padding-top:6px;">
									<input name="traitid" type="hidden" value="<?= $traitID ?>" />
									<button name="formsubmit" type="submit" value="addState">Add Trait State</button>
								</div>
							</fieldset>
						</form>
					</div>
					<?php
					if($traitStateArr){
						echo '<h3>Trait States</h3>';
						foreach($traitStateArr as $stateID => $stateArr){
							?>
							<div>
								<div id="statecodeplus-<?= $stateID ?>" style="margin:5px;">
									<a href="#" onclick="toggleCharState(<?= $stateID ?>);return false;">
										<img src="../../../images/plus.png" class="icon-img" >
										<?= Sanitize::outString($stateArr['statename']) ?>
									</a>
								</div>
								<div id="<?= 'statecode-'.$stateID.'Div' ?>" style="display:none;">
									<div style="margin:5px;">
										<a href="#" onclick="toggleCharState(<?= $stateID ?>);return false;">
											<img src="../../../images/minus.png" class="icon-img" >
											<?= Sanitize::outString($stateArr['statename']) ?>
										</a>
									</div>
									<form name="stateeditform-<?= $stateID ?>" action="traitdetails.php" method="post" onsubmit="return validateStateEditForm(this)">
										<fieldset>
											<legend>Trait State Details</legend>
											<div>
												<label for="statename-<?= $stateID ?>">Trait State Name</label><br />
												<input type="text" id="statename-<?= $stateID ?>" name="statename" maxlength="255" style="width:300px;" value="<?= Sanitize::outString($stateArr['statename']) ?>" />
											</div>
											<div style="padding-top:4px;">
												<label for="description-<?= $stateID ?>">Description</label><br />
												<input type="text" id="description-<?= $stateID ?>" name="description" maxlength="255" style="width:90%;" value="<?= Sanitize::outString($stateArr['description']) ?>"/>
											</div>
											<div style="padding-top:4px;">
												<label for="refurl">Reference URL</label><br />
												<input type="text" id="refurl" name="refurl" maxlength="500" style="width:90%;" value="<?= Sanitize::outString($stateArr['refUrl']) ?>" />
												<?php
												if($stateArr['refUrl'] && substr($stateArr['refUrl'],0,4) == 'http'){
													echo '<a href="' . Sanitize::outString($stateArr['refUrl']) . '" target="_blank"><img src="../../../images/link2.png" class="icon-img" ></a>';
												}
												?>
											</div>
											<div style="padding-top:4px;">
												<label for="notes-<?= $stateID ?>">Notes</label><br />
												<input type="text" id="notes-<?= $stateID ?>" name="notes" style="width:90%;" value="<?= Sanitize::outString($stateArr['notes']) ?>" />
											</div>
											<div style="padding-top:4px;">
												<label for="sortseq-<?= $stateID ?>">Sort Sequence</label><br />
												<input type="number" id="sortseq-<?= $stateID ?>" name="sortseq" style="width:80px;" value="<?= Sanitize::outString($stateArr['sortseq']) ?>" />
											</div>
											<?php 
												$stateDepArr = $traitManager->getStateDepArr($stateID);
												if($stateDepArr){
											?>
												<div>
													<fieldset>
													<legend>Dependencies (Trait and States that Appear when this Trait is Selected):</legend>
													<?php
														echo '<ul>';
														foreach ($stateDepArr as $trait){
															echo '<li><a href="traitdetails.php?traitid=' . $trait['traitid'] . '">' . $trait['traitid'] . '</a></li>';
														}
													echo '</ul>';
													?>
													</fieldset>
												</div>
											<?php
											}
											?>
											<div style="width:100%;margin:10px 0px 10px 0px;">
												<input name="traitid" type="hidden" value="<?= $traitID ?>" />
												<input name="statecode" type="hidden" value="<?= $stateArr['statecode'] ?>" />
												<button name="formsubmit" type="submit" value="saveState">Save</button>
											</div>
										</fieldset>
									</form>
									<form name="statedelform-<?= $stateID ?>" action="traitdetails.php" method="post" onsubmit="return confirm('Are you sure you want to permanently delete this character state?')">
										<fieldset>
											<legend>Delete Trait State</legend>
											<div style="margin:15px;">
												<input id="stateid" name="stateid" type="hidden" value="<?= $stateID ?>">
												<input name="traitid" type="hidden" value="<?= $traitID ?>" />
												<input name="statecode" type="hidden" value="<?= $stateArr['statecode'] ?>" />
												<button name="formsubmit" type="submit" value="deleteState">Delete State</button>
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
				<div id="traitdeldiv">
					<form name="deltraitform" action="traitdetails.php" method="post" onsubmit="return confirm('Are you sure you want to permanently delete this character?')">
						<fieldset style="width:700px;">
							<legend><b>Delete Trait</b></legend>
							<?php
							if($traitStateArr){
								?>
								<div style="margin-bottom:15px;">
									Trait cannot be deleted until all character states are removed
								</div>
								<?php
							}
							?>
							<input name="traitid" type="hidden" value="<?= $traitID ?>" />
							<button name="formsubmit" type="submit" value="deleteTrait" <?php if($traitStateArr) echo 'DISABLED' ?>>Delete</button>
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
