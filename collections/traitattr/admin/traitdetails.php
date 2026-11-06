<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceTraitAdmin.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load('collections/traitattr/admin/traitdetails');

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
	elseif($formSubmit == 'addDependency'){
		if(!$traitManager->insertTraitStateDep($_POST)){
			$statusStr = $LANG['ERROR_DELETE_STATE'] . $traitManager->getErrorMessage();
		}
		$tabIndex = 1;
	}
	else if($formSubmit == 'deleteDependency') {
		if(!$traitManager->deleteTraitStateDep($_POST)){
			$statusStr = $LANG['ERROR_DELETE_STATE'] . $traitManager->getErrorMessage();
		}
		$tabIndex = 1;
	}
	elseif($formSubmit == 'addTaxon'){
		if(!empty($_POST['tid'])){
			if(!$traitManager->insertTaxonRelevance($_POST['tid'], $_POST['relation'])){
				$statusStr = $LANG['ERROR_ADD_REL'] . $traitManager->getErrorMessage();
			}
			$tabIndex = 2;
		}
	}
	elseif($formSubmit == 'delTaxon'){
		if(!$traitManager->deleteTaxonRelevance($_POST['tid'])){
			$statusStr = $LANG['ERROR_DELETE_REL'] . $traitManager->getErrorMessage();
		}
		$tabIndex = 2;
	}
		
}

if(!$traitID) header('Location: index.php');
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<title><?= $LANG['TRAIT_ADMIN'] ?></title>
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
		<a href='../../../index.php'><?= $LANG['HOME'] ?></a> &gt;&gt;
		<a href='index.php'><b><?= $LANG['TRAIT_MGMT'] ?></b></a>
	</div>
	<div role="main" id="innertext">
		<h1 class="page-heading screen-reader-only"><?= $LANG['OCCUR_TRAIT_ADMIN'] ?></h1>
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
			$traitList = $traitManager->getTraitArr();
			$traitArr = $traitManager->getTraitArrById();
			$traitStateArr = $traitManager->getTraitStateArr();
			$groupArr = $traitManager->getProjectGroups();
			?>
			<div style="font-weight:bold;font-size:150%;margin:15px;"><?= Sanitize::outString($traitArr['traitName']) ?></div>
			<div id="tabs" style="margin:0px;">
				<ul>
					<li><a href="#traitdetaildev"><span><?= $LANG['DETAILS'] ?></span></a></li>
					<li><a href="#traitstatediv"><span><?= $LANG['TRAIT_STATES'] ?></span></a></li>
					<li><a href="taxonomylinkage.php?traitid=<?= $traitID ?>"><span><?= $LANG['TAXON_LINKAGES'] ?></span></a></li>
					<li><a href="#traitdeldiv"><span><?= $LANG['ADMIN'] ?></span></a></li>
				</ul>
				<div id="traitdetaildev">
					<form name="traiteditform" action="traitdetails.php" method="post">
						<fieldset>
							<legend><?= $LANG['TRAIT_DETAILS'] ?></legend>
							<div style="padding-top:4px;">
								<label for="traitname"><?= $LANG['TRAIT_NAME'] ?></label><span style="color:red;">*</span><br />
								<input type="text" id="traitname" name="traitname" maxlength="150" style="width:400px;" value="<?= Sanitize::outString($traitArr['traitName']) ?>" required />
							</div>
							<div style="padding-top:8px;float:left;">
								<div style="float:left;">
									<label for="type"><?= $LANG['TYPE'] ?></label><br />
									<select id="type" name="traittype" style="width:180px;">
										<option value="UM">UM</option>
										<option value="OM">OM</option>
										<option value="TF">TF</option>
										<option value="NU">NU</option>
									</select>
								</div>
								<div id="units" style="margin-left:15px;float:left;">
									<label for="units"><?= $LANG['UNITS'] ?></label><br />
									<input type="text" id="units" name="units" maxlength="45" style="width:100px;" value="<?= Sanitize::outString($traitArr['units']) ?>" />
								</div>
								<div style="margin-left:15px;float:left;">
									<label for="projectGroup"><?= $LANG['PROJECT_GROUP'] ?>:</label><br />
									<input type="text" id="projectGroup" name="projectGroup" list="groups" autocomplete="off" maxlength="255" value="<?= Sanitize::outString($traitArr['projectGroup']) ?>"/>
									<datalist id="groups">
										<?php
										foreach($groupArr as $group) {
											echo '<option value="'. $group['projectGroup'].'">';
										}
										?>
									</datalist>
								</div>
							</div>

							<div style="padding-top:8px;clear:both;">
								<label for="refurl"><?= $LANG['REF_URL'] ?></label><br />
								<input type="text" id="refurl" name="refurl" maxlength="500" style="width:90%;" value="<?= Sanitize::outString($traitArr['refUrl']) ?>" />
								<?php
								if($traitArr['refUrl'] && substr($traitArr['refUrl'],0,4) == 'http'){
									echo '<a href="' . Sanitize::outString($traitArr['refUrl']) . '" target="_blank"><img src="../../../images/link2.png" class="icon-img" ></a>';
								}
								?>
							</div>
							<div style="padding-top:8px;">
								<label for="description"><?= $LANG['DESCRIPTION'] ?></label><br />
								<input type="text" id="description" name="description" maxlength="255" style="width:90%;" value="<?= Sanitize::outString($traitArr['description']) ?>" />
							</div>
							<div style="padding-top:8px;">
								<label for="notes"><?= $LANG['NOTES'] ?></label><br />
								<input type="text" id="notes" name="notes" maxlength="255" style="width:90%;" value="<?= Sanitize::outString($traitArr['notes']) ?>" />
							</div>
							<div style="padding-top:8px;">
								<label for="isPublic"><?= $LANG['IS_PUBLIC'] ?>:</label>
								<input type="hidden" name="isPublic" value="0"/>
								<input type="checkbox" id="isPublic" name="isPublic" value="1" <?= $traitArr['isPublic'] === 1 ? 'checked' : '' ?> />

								<label for="includeInSearch" style="margin-left:20px;"><?= $LANG['IN_SEARCH'] ?>:</label>
								<input type="hidden" name="includeInSearch" value="0"/>
								<input type="checkbox" id="includeInSearch" name="includeInSearch" value="1" <?= $traitArr['includeInSearch'] === 1 ? 'checked' : '' ?> />
							</div>
							<div style="padding-top:8px;">
							<label for="dynamicproperties"><?= $LANG['INPUT_TYPE'] ?>:</label>
								<select id="dynamicproperties" name="dynamicproperties">
									<option value="radio" <?= $traitArr['dynamicProperties'] == '[{"controlType":"radio"}]' ? 'selected' : ''; ?> ><?= $LANG['RADIO_BTN'] ?></option>
									<option value="checkbox" <?= $traitArr['dynamicProperties'] == '[{"controlType":"checkbox"}]' ? 'selected' : ''; ?> ><?= $LANG['CHECKBOX'] ?></option>
									<option value="select" <?= $traitArr['dynamicProperties'] == '[{"controlType":"select"}]' ? 'selected' : ''; ?> ><?= $LANG['SELECT'] ?></option>
								</select>
							</div>
							<div style="width:100%;padding-top:6px;">
								<div style="float:left;">
									<input name="traitid" type="hidden" value="<?= $traitID ?>" />
									<button name="formsubmit" type="submit" value="saveTraitEdit"><?= $LANG['SAVE'] ?></button>
								</div>
							</div>
						</fieldset>
					</form>
				</div>
				<div id="traitstatediv">
					<div style="float:right;margin:10px;">
						<button class="clear-icon-button" title="<?= $LANG['CREATE_TRAIT_STATE'] ?>" aria-label="<?= $LANG['CREATE_TRAIT_STATE'] ?>" onclick="toggle('newstatediv');">
							<img src="../../../images/add.png" class="icon-img" />
						</button>
					</div>
					<div id="newstatediv" style="display:<?= ($traitStateArr?'none':'block') ?>;">
						<form name="stateaddform" action="traitdetails.php" method="post" onsubmit="return validateStateAddForm(this)">
							<fieldset>
								<legend><?= $LANG['ADD_TRAIT_STATE'] ?></legend>
								<div style="padding-top:4px;">
									<label for="statename"><?= $LANG['TRAIT_STATE_NAME'] ?></label><span style="color:red;">*</span><br />
									<input type="text" id="statename" name="statename" maxlength="255" style="width:400px;" required/>
								</div>
								<div style="padding-top:4px;">
									<label for="add_description"><?= $LANG['DESCRIPTION'] ?></label><br />
									<input type="text" id="add_description" name="description" maxlength="255" style="width:90%;" />
								</div>
								<div style="padding-top:4px;">
									<label for="add_refurl"><?= $LANG['REF_URL'] ?></label><br />
									<input type="text" id="add_refurl" name="refurl" maxlength="255" style="width:90%;" />
								</div>
								<div style="padding-top:4px;">
									<label for="add_notes"><?= $LANG['NOTES'] ?></label><br />
									<input type="text" id="add_notes" name="notes" style="width:90%;" />
								</div>
								<div style="padding-top:4px;">
									<label for="sortseq"><?= $LANG['SORT_SQNCE'] ?></label></span><br />
									<input type="number" id="sortseq" name="sortseq" style="width:80px;" />
								</div>
								<div style="width:100%;padding-top:6px;">
									<input name="traitid" type="hidden" value="<?= $traitID ?>" />
									<button name="formsubmit" type="submit" value="addState"><?= $LANG['ADD_TRAIT_STATE'] ?></button>
								</div>
							</fieldset>
						</form>
					</div>
					<?php
					if($traitStateArr){
						echo '<h3>' . $LANG['TRAIT_STATES']. '</h3>';
						foreach($traitStateArr as $stateID => $stateArr){
							$isOpen = (isset($_POST['statecode']) && ($_POST['statecode']) === $stateArr['statecode']);
							?>
							<div>
								<div id="statecodeplus-<?= $stateID ?>" style="margin:5px; <?= $isOpen ? 'display:none;' : '' ?>">
									<a href="#" onclick="toggleCharState(<?= $stateID ?>);return false;">
										<img src="../../../images/plus.png" class="icon-img" >
										<?= Sanitize::outString($stateArr['statename']) ?>
									</a>
								</div>
								<div id="<?= 'statecode-'.$stateID.'Div' ?>" style="<?= $isOpen ? 'display:block;' : 'display:none;' ?>">
									<div style="margin:5px;">
										<a href="#" onclick="toggleCharState(<?= $stateID ?>);return false;">
											<img src="../../../images/minus.png" class="icon-img" >
											<?= Sanitize::outString($stateArr['statename']) ?>
										</a>
									</div>
									<form name="stateeditform-<?= $stateID ?>" action="traitdetails.php" method="post">
										<fieldset>
											<legend><?= $LANG['TRAIT_STATE_DETAILS'] ?></legend>
											<div style="padding-top:4px;">
												<label for="statename-<?= $stateID ?>"><?= $LANG['TRAIT_STATE_NAME'] ?></label><span style="color:red;">*</span><br />
												<input type="text" id="statename-<?= $stateID ?>" name="statename" maxlength="255" style="width:300px;" value="<?= Sanitize::outString($stateArr['statename']) ?>" required />
											</div>
											<div style="padding-top:4px;">
												<label for="description-<?= $stateID ?>"><?= $LANG['DESCRIPTION'] ?></label><br />
												<input type="text" id="description-<?= $stateID ?>" name="description" maxlength="255" style="width:90%;" value="<?= Sanitize::outString($stateArr['description']) ?>"/>
											</div>
											<div style="padding-top:4px;">
												<label for="refurl-<?= $stateID ?>"><?= $LANG['REF_URL'] ?></label><br />
												<input type="text" id="refurl-<?= $stateID ?>" name="refurl" maxlength="500" style="width:90%;" value="<?= Sanitize::outString($stateArr['refUrl']) ?>" />
												<?php
												if($stateArr['refUrl'] && substr($stateArr['refUrl'],0,4) == 'http'){
													echo '<a href="' . Sanitize::outString($stateArr['refUrl']) . '" target="_blank"><img src="../../../images/link2.png" class="icon-img" ></a>';
												}
												?>
											</div>
											<div style="padding-top:4px;">
												<label for="notes-<?= $stateID ?>"><?= $LANG['NOTES'] ?></label><br />
												<input type="text" id="notes-<?= $stateID ?>" name="notes" style="width:90%;" value="<?= Sanitize::outString($stateArr['notes']) ?>" />
											</div>
											<div style="padding-top:4px;">
												<label for="sortseq-<?= $stateID ?>"><?= $LANG['SORT_SQNCE'] ?></label><br />
												<input type="number" id="sortseq-<?= $stateID ?>" name="sortseq" style="width:80px;" value="<?= Sanitize::outString($stateArr['sortseq']) ?>" />
											</div>
											<div style="width:100%;margin:10px 0px 10px 0px;">
												<input name="traitid" type="hidden" value="<?= $traitID ?>" />
												<input name="statecode" type="hidden" value="<?= $stateArr['statecode'] ?>" />
												<button name="formsubmit" type="submit" value="saveState"><?= $LANG['SAVE'] ?></button>
											</div>
										</fieldset>
									</form>
									<div>
										<fieldset>
											<button type="button" class="clear-icon-button" style="float:right;" onclick="toggle('newdepform-<?= $stateID ?>');" title="<?= $LANG['ADD_NEW_DEPEND'] ?>" aria-label="<?= $LANG['ADD_NEW_DEPEND'] ?>">
												<img src="../../../images/add.png" class="icon-img"/>			
											</button>
											<div id="newdepform-<?= $stateID ?>" style="display:none;">
												<form method="post">
													<fieldset style="display:flex; align-items:center; gap:10px;">
														<legend><?= $LANG['ADD_NEW_DEPEND'] ?></legend>
														<label for="dependency-<?= $stateID ?>"><?= $LANG['DEPENDENCY'] ?>:</label>
														<select name="traitID" id="dependency-<?= $stateID ?>">
															<?php
															$stateDepArr = array_column($traitManager->getStateDepArr($stateID), 'traitID'); # Just the numbers
															# Dependency name="traitID" needs to be different from a state's name="traitid"
															foreach ($traitList as $trait) {
																if ($trait['traitID'] !== (int)$traitID && !in_array($trait['traitID'], $stateDepArr)) {
																	echo '<option value="' . $trait['traitID'] . '">' . $trait['traitName'] . '</option>';
																}		
															}
															?>
														</select>
														<input name="traitid" type="hidden" value="<?= $traitID ?>" />
														<input name="parentstateid" type="hidden" value="<?= $stateID ?>" />
														<input name="statecode" type="hidden" value="<?= $stateArr['statecode'] ?>" />
														<button name="formsubmit" type="submit" value="addDependency"><?= $LANG['ADD'] ?></button>
													</fieldset>
												</form>
											</div>
											<legend><?= $LANG['DEPENDENCIES'] ?>:</legend>
											<?php
												if($stateDepArr){
													echo '<form method="post">
													<input name="traitid" type="hidden" value="'. $traitID .'" />
													<input name="parentstateid" type="hidden" value="'. $stateID .'"/>
													<input name="statecode" type="hidden" value="' . $stateArr['statecode'] . '" />
													<input name="formsubmit" type="hidden" value="deleteDependency" /><ul style="padding-left:20px;">';
													foreach ($stateDepArr as $dependency){
														$trait = $traitManager->getTraitArrById2($dependency);
														echo '<li style="display:flex; gap:10px; margin-bottom:10px;">
														<a href="traitdetails.php?traitid=' . $trait['traitID'] . '">' . $trait['traitName'] . '</a>
														<button type="submit" name="traitID" value="' . $trait['traitID'] . '" class="clear-icon-button" title="'. $LANG['DELETE_DEPEND'] .'" aria-label="'. $LANG['DELETE_DEPEND'] . ' ' . $trait['traitName'] . '"><img src="../../../images/del.png" class="icon-img"/></button></li>';
													}
													echo '</ul></form>';
											}
												else {
													echo $LANG['NO_DEPEND'];
												}
											?>
										</fieldset>
									</div>	
									<form name="statedelform-<?= $stateID ?>" action="traitdetails.php" method="post" onsubmit="return confirm('<?= $LANG['CONFIRM_DELETE_STATE'] . $stateArr['statename'] ?>?')">
										<fieldset>
											<legend><?= $LANG['DELETE_TRAIT_STATE'] ?></legend>
											<div style="margin:15px;">
												<input id="stateid" name="stateid" type="hidden" value="<?= $stateID ?>">
												<input name="traitid" type="hidden" value="<?= $traitID ?>" />
												<input name="statecode" type="hidden" value="<?= $stateArr['statecode'] ?>" />
												<button name="formsubmit" type="submit" value="deleteState"><?= $LANG['DELETE_STATE'] ?></button>
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
					<form name="deltraitform" action="traitdetails.php" method="post" onsubmit="return confirm('<?= $LANG['CONFIRM_DELETE_TRAIT'] . $traitArr['traitName'] ?>?')">
						<fieldset style="width:700px;">
							<legend><b><?= $LANG['DELETE_TRAIT'] ?></b></legend>
							<?php
							if($traitStateArr){
								?>
								<div style="margin-bottom:15px;">
									<?= $LANG['NOTE_TRAIT_DELETE'] ?>
								</div>
								<?php
							}
							?>
							<input name="traitid" type="hidden" value="<?= $traitID ?>" />
							<button name="formsubmit" type="submit" value="deleteTrait" <?php if($traitStateArr) echo 'DISABLED' ?>><?= $LANG['DELETE'] ?></button>
						</fieldset>
					</form>
				</div>
			</div>
			<?php
		}
		else{
			if(!$isEditor){
				echo '<h2>' . $LANG['NO_AUTH_ADD_TRAIT'] . '</h2>';
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
