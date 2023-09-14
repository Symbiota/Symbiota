<!DOCTYPE html>

<?php
include_once ('../config/symbini.php');
include_once ($SERVER_ROOT . '/classes/GeographicThesaurus.php');
include_once($SERVER_ROOT.'/content/lang/geothesaurus/index.'.$LANG_TAG.'.php');
header("Content-Type: text/html; charset=".$CHARSET);

$geoThesID = array_key_exists('geoThesID', $_REQUEST) ? $_REQUEST['geoThesID'] : '';
$parentID = array_key_exists('parentID', $_REQUEST) ? $_REQUEST['parentID'] : '';
$geoLevel = array_key_exists('geoLevel', $_POST) ? $_POST['geoLevel'] : '';
$submitAction = array_key_exists('submitaction', $_POST) ? $_POST['submitaction'] : '';

// Sanitation
if(!is_numeric($geoThesID)) $geoThesID = 0;
if(!is_numeric($parentID)) $parentID = 0;
if(!is_numeric($geoLevel)) $geoLevel = 0;
$submitAction = htmlspecialchars($submitAction, HTML_SPECIAL_CHARS_FLAGS);

$geoManager = new GeographicThesaurus();

$isEditor = false;
if($IS_ADMIN || array_key_exists('CollAdmin',$USER_RIGHTS)) $isEditor = true;

$statusStr = '';
if($isEditor && $submitAction) {
	if($submitAction == 'submitGeoEdits'){
		$status = $geoManager->editGeoUnit($_POST);
		if(!$status) $statusStr = $geoManager->getErrorMessage();
	}
	elseif($submitAction == 'deleteGeoUnits'){
		$status = $geoManager->deleteGeoUnit($_POST['delGeoThesID']);
		if(!$status) $statusStr = $geoManager->getErrorMessage();
	}
		elseif($submitAction == 'addGeoUnit'){
		$status = $geoManager->addGeoUnit($_POST);
		if(!$status) $statusStr = $geoManager->getErrorMessage();
	}
}

$geoArr = $geoManager->getGeograpicList($parentID);

?>
<html lang="<?php echo $LANG_TAG ?>">
<head>
	<title><?php echo $DEFAULT_TITLE; ?> - <?php echo (isset($LANG['GEO_THES_MNGR']) ? $LANG['GEO_THES_MNGR'] : 'Geographic Thesaurus Manager'); ?></title>
	<link href="<?php echo htmlspecialchars($CSS_BASE_PATH, HTML_SPECIAL_CHARS_FLAGS); ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once ($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery.js" type="text/javascript"></script>
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.js" type="text/javascript"></script>
	<script type="text/javascript">
		function toggleEditor(){
			$(".editTerm").toggle();
			$(".editFormElem").toggle();
			$("#editButton-div").toggle();
			$("#edit-legend").toggle();
			$("#unitDel-div").toggle();
		}
	</script>
	<style>
		fieldset{ margin: 10px; padding: 15px; width: 800px }
		legend{ font-weight: bold; }
		label{ text-decoration: underline; }
		#edit-legend{ display: none }
		.field-div{ margin: 3px 0px }
		.editIcon{  }
		.editTerm{ }
		.editFormElem{ display: none }
		#editButton-div{ display: none }
		#unitDel-div{ display: none }
		.button-div{ margin: 15px }
		.link-div{ margin:0px 30px }
		#status-div{ margin:15px; padding: 15px; color: red; }
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = (isset($profile_indexMenu)?$profile_indexMenu:'true');
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
	<a href="../index.php"> <?php echo htmlspecialchars($LANG['HOME'], HTML_SPECIAL_CHARS_FLAGS) ?> </a> &gt;&gt;
		<b> <?php echo htmlspecialchars($LANG['GEO_THES_BLIST'], HTML_SPECIAL_CHARS_FLAGS) ?> </b>
	</div>
	<div id='innertext'>
		<?php
		if($statusStr){
			echo '<div id="status-div">'.$statusStr.'</div>';
		}
		if($geoThesID){
			$geoUnit = $geoManager->getGeograpicUnit($geoThesID);
			$rankArr = $geoManager->getGeoRankArr();
			?>
			<div id="updateGeoUnit-div" style="clear:both;margin-bottom:10px;">
				<fieldset id="edit-fieldset">
					<legend> <?php echo (isset($LANG['EDIT_GEO']) ? $LANG['EDIT_GEO'] : 'Edit Geographic') ?> <span id="edit-legend"> <?php echo (isset($LANG['UNIT']) ? $LANG['UNIT'] : 'Unit') ?> </span></legend>
					<div style="float:right">
						<span class="editIcon"><a href="#" onclick="toggleEditor()"><img class="editimg" src="../images/edit.png" alt="<?php echo (isset($LANG['EDIT']) ? $LANG['EDIT'] : 'Edit'); ?>"/></a></span>

					</div>
					<form name="unitEditForm" action="index.php" method="post">
						<div class="field-div">
							<label> <?php echo (isset($LANG['GEO_UNIT']) ? $LANG['GEO_UNIT'] : 'GeoUnit Name') ?> </label>:
							<span class="editTerm"><?php echo $geoUnit['geoTerm']; ?></span>
							<span class="editFormElem"><input type="text" name="geoTerm" value="<?php echo $geoUnit['geoTerm'] ?>" style="width:200px;" required /></span>
						</div>
						<div class="field-div">
							<label> <?php echo (isset($LANG['ABBR']) ? $LANG['ABBR'] : 'Abbreviation') ?> </label>:
							<span class="editTerm"><?php echo $geoUnit['abbreviation']; ?></span>
							<span class="editFormElem"><input type="text" name="abbreviation" value="<?php echo $geoUnit['abbreviation'] ?>" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label> <?php echo (isset($LANG['ISO2']) ? $LANG['ISO2'] : 'ISO2 Code') ?> </label>:
							<span class="editTerm"><?php echo $geoUnit['iso2']; ?></span>
							<span class="editFormElem"><input type="text" name="iso2" value="<?php echo $geoUnit['iso2'] ?>" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label> <?php echo (isset($LANG['ISO3']) ? $LANG['ISO3'] : 'ISO3 Code') ?> </label>:
							<span class="editTerm"><?php echo $geoUnit['iso3']; ?></span>
							<span class="editFormElem"><input type="text" name="iso3" value="<?php echo $geoUnit['iso3'] ?>"style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label> <?php echo (isset($LANG['NUM_CODE']) ? $LANG['NUM_CODE'] : 'Numeric Code') ?> </label>:
							<span class="editTerm"><?php echo $geoUnit['numCode']; ?></span>
							<span class="editFormElem"><input type="text" name="numCode" value="<?php echo $geoUnit['numCode'] ?>" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label> <?php echo (isset($LANG['GEO_RANK']) ? $LANG['GEO_RANK'] : 'Geography Rank') ?> </label>:
							<span class="editTerm"><?php echo ($geoUnit['geoLevel']?$rankArr[$geoUnit['geoLevel']].' ('.$geoUnit['geoLevel'].')':''); ?></span>
							<span class="editFormElem">
								<select name="geoLevel">
									<option value=""> <?php echo (isset($LANG['SELECT_RANK']) ? $LANG['SELECT_RANK'] : 'Select Rank') ?> </option>
									<option value="">----------------------</option>
									<?php
									foreach($rankArr as $rankID => $rankValue){
										echo '<option value="'.$rankID.'" '.($rankID==$geoUnit['geoLevel']?'selected':'').'>'.$rankValue.'</option>';
									}
									?>
								</select>
							</span>
						</div>
						<div class="field-div">
							<label> <?php echo (isset($LANG['NOTES']) ? $LANG['NOTES'] : 'Notes') ?> </label>:
							<span class="editTerm"><?php echo $geoUnit['notes']; ?></span>
							<span class="editFormElem"><input type="text" name="notes" value="<?php echo $geoUnit['notes'] ?>" maxlength="250" style="width:650px;" /></span>
						</div>
						<?php
						if($geoUnit['geoLevel']){
							if($parentList = $geoManager->getParentGeoTermArr($geoUnit['geoLevel'])){
								$parentStr = '';
								if($geoUnit['parentTerm']) $parentStr = '<a href="index.php?geoThesID=' . htmlspecialchars($geoUnit['parentID'], HTML_SPECIAL_CHARS_FLAGS) . '">' . htmlspecialchars($geoUnit['parentTerm'], HTML_SPECIAL_CHARS_FLAGS) . '</a>';
								?>
								<div class="field-div">
									<label> <?php echo (isset($LANG['PARENT_TERM']) ? $LANG['PARENT_TERM'] : 'Parent term') ?> </label>:
									<span class="editTerm"><?php echo $parentStr; ?></span>
									<span class="editFormElem">
										<select name="parentID">
											<option value=""> <?php echo (isset($LANG['IS_ROOT_TERM']) ? $LANG['IS_ROOT_TERM'] : 'Is a Root Term (i.e. no parent)') ?> </option>
											<?php
											foreach($parentList as $id => $term){
												echo '<option value="'.$id.'" '.($id==$geoUnit['parentID']?'selected':'').'>'.$term.'</option>';
											}
											?>
										</select>
									</span>
								</div>
								<?php
							}
						}
						$acceptedStr = '';
						if($geoUnit['acceptedTerm']) $acceptedStr = '<a href="index.php?geoThesID=' . htmlspecialchars($geoUnit['acceptedID'], HTML_SPECIAL_CHARS_FLAGS) . '">' . htmlspecialchars($geoUnit['acceptedTerm'], HTML_SPECIAL_CHARS_FLAGS) . '</a>';
						?>
						<div class="field-div">
							<label> <?php echo (isset($LANG['ACCEPTED_TERM']) ? $LANG['ACCEPTED_TERM'] : 'Accepted term') ?> </label>:
							<span class="editTerm"><?php echo $acceptedStr; ?></span>
							<span class="editFormElem">
								<select name="acceptedID">
									<option value=""> <?php echo (isset($LANG['IS_ACCEPTED']) ? $LANG['IS_ACCEPTED'] : 'Term is Accepted') ?> </option>
									<option value="">----------------------</option>
									<?php
									$acceptedList = $geoManager->getAcceptedGeoTermArr($geoUnit['geoLevel']);
									foreach($acceptedList as $id => $term){
										echo '<option value="'.$id.'" '.($id==$geoUnit['acceptedID']?'selected':'').'>'.$term.'</option>';
									}
									?>
								</select>
							</span>
						</div>
						<div id="editButton-div" class="button-div">
							<input name="geoThesID" type="hidden" value="<?php echo $geoThesID; ?>" />
							<button type="submit" name="submitaction" value="submitGeoEdits"> <?php echo (isset($LANG['SAVE_EDITS']) ? $LANG['SAVE_EDITS'] : 'Save Edits') ?> </button>
						</div>
					</form>
				</fieldset>
			</div>
			<div id="unitDel-div">
				<form name="unitDeleteForm" action="index.php" method="post">
					<fieldset>
						<legend> <?php echo (isset($LANG['DEL_GEO_UNIT']) ? $LANG['DEL_GEO_UNIT'] : 'Delete Geographic Unit') ?> </legend>
						<div class="button-div">
							<input name="parentID" type="hidden" value="<?php echo $geoUnit['parentID']; ?>" />
							<input name="delGeoThesID" type="hidden"  value="<?php echo $geoThesID; ?>" />

							<!-- We need to decide if we want to allow folks to delete a term and all their children, or only can delete if no children or synonym exists. I'm thinking the later. -->

							<button type="submit" name="submitaction" value="deleteGeoUnits" onclick="return confirm(<?php echo (isset($LANG['CONFIRM_DELETE']) ? $LANG['CONFIRM_DELETE'] : 'Are you sure you want to delete this record?') ?>)" <?php echo ($geoUnit['childCnt']?(isset($LANG['DISABLED']) ? $LANG['DISABLED'] : 'disabled'):''); ?>> <?php echo (isset($LANG['DEL_GEO_UNIT']) ? $LANG['DEL_GEO_UNIT'] : 'Delete Geographic Unit') ?> </button>
						</div>
						<?php
						if($geoUnit['childCnt']) echo '<div>' . (isset($LANG['CANT_DELETE']) ? $LANG['CANT_DELETE'] : '* Record can not be deleted until all child records are deleted') . '</div>';
						?>
					</fieldset>
				</form>
			</div>
			<?php
			echo '<div class="link-div">';
			echo '<div><a href="index.php?' . htmlspecialchars((isset($geoUnit['parentID'])?'parentID='.$geoUnit['parentID']:''), HTML_SPECIAL_CHARS_FLAGS) . '">' . (isset($LANG['SHOW']) ? $LANG['SHOW'] : 'Show') . ' ' . htmlspecialchars((isset($geoUnit['geoLevel'])?$rankArr[$geoUnit['geoLevel']]:''), HTML_SPECIAL_CHARS_FLAGS) . ' ' . (isset($LANG['TERMS']) ? $LANG['TERMS'] : 'terms') . '</a></div>';
			if(isset($geoUnit['childCnt']) && $geoUnit['childCnt']) echo '<div><a href="index.php?parentID=' . htmlspecialchars($geoThesID, HTML_SPECIAL_CHARS_FLAGS) . '">' . (isset($LANG['SHOW_CHILDREN']) ? $LANG['SHOW_CHILDREN'] : 'Show children') . '</a></div>';
			echo '</div>';
		}
		else{
			?>
			<div style="float:right">
				<span class="editIcon"><a href="#" onclick="$('#addGeoUnit-div').toggle();"><img class="editimg" src="../images/add.png" alt="<?php echo (isset($LANG['EDIT']) ? $LANG['EDIT'] : 'Edit'); ?>" /></a></span>
			</div>
			<div id="addGeoUnit-div" style="clear:both;margin-bottom:10px;display:none">
				<!--This should also be visible when !$geoThesID -->
				<fieldset id="new-fieldset">
					<legend> <?php echo (isset($LANG['ADD_GEO_UNIT']) ? $LANG['ADD_GEO_UNIT'] : 'Add Geographic Unit') ?> </legend>
					<form name="unitAddForm" action="index.php" method="post">
						<div class="field-div">
							<label> <?php echo (isset($LANG['GEO_UNIT']) ? $LANG['GEO_UNIT'] : 'GeoUnit Name') ?> </label>:
							<span><input type="text" name="geoTerm" style="width:200px;" required /></span>
						</div>
						<div class="field-div">
							<label> <?php echo (isset($LANG['ABBR']) ? $LANG['ABBR'] : 'Abbreviation') ?> </label>:
							<span><input type="text" name="abbreviation" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label> <?php echo (isset($LANG['ISO2']) ? $LANG['ISO2'] : 'ISO2 Code') ?> </label>:
							<span><input type="text" name="iso2" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label> <?php echo (isset($LANG['ISO3']) ? $LANG['ISO3'] : 'ISO3 Code') ?> </label>:
							<span><input type="text" name="iso3" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label> <?php echo (isset($LANG['NUM_CODE']) ? $LANG['NUM_CODE'] : 'Numeric Code') ?> </label>:
							<span><input type="text" name="numCode" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label> <?php echo (isset($LANG['GEO_RANK']) ? $LANG['GEO_RANK'] : 'Geography Rank') ?> </label>:
							<span>
								<select name="geoLevel">
									<option value=""> <?php echo (isset($LANG['SELECT_RANK']) ? $LANG['SELECT_RANK'] : 'Select Rank') ?> </option>
									<option value="">----------------------</option>
									<?php
									$defaultGeoLevel = 0;
									if($geoArr) $defaultGeoLevel = $geoArr[key($geoArr)]['geoLevel'];
									$rankArr = $geoManager->getGeoRankArr();
									foreach($rankArr as $rankID => $rankValue){
										echo '<option value="'.$rankID.'" '.($defaultGeoLevel==$rankID?'SELECTED':'').'>'.$rankValue.'</option>';
									}
									?>
								</select>
							</span>
						</div>
						<div class="field-div">
							<label> <?php echo (isset($LANG['NOTES']) ? $LANG['NOTES'] : 'Notes') ?> </label>:
							<span><input type="text" name="notes" maxlength="250" style="width:200px;" /></span>
						</div>
						<div class="field-div">
							<label> <?php echo (isset($LANG['PARENT_TERM']) ? $LANG['PARENT_TERM'] : 'Parent term') ?> </label>:
							<span>
								<select name="parentID">
									<option value=""> <?php echo (isset($LANG['SELECT_PARENT']) ? $LANG['SELECT_PARENT'] : 'Select Parent Term') ?> </option>
									<option value="">----------------------</option>
									<option value=""> <?php echo (isset($LANG['IS_ROOT_TERM']) ? $LANG['IS_ROOT_TERM'] : 'Is a Root Term (i.e. no parent)') ?> </option>
									<?php
									$parentList = $geoManager->getParentGeoTermArr();
									foreach($parentList as $id => $term){
										echo '<option value="'.$id.'" '.($parentID == $id?'SELECTED':'').'>'.$term.'</option>';
									}
									?>
								</select>
							</span>
						</div>
						<div class="field-div">
							<label> <?php echo (isset($LANG['ACCEPTED_TERM']) ? $LANG['ACCEPTED_TERM'] : 'Accepted term') ?> </label>:
							<span>
								<select name="acceptedID">
									<option value=""> <?php echo (isset($LANG['SELECT_ACCEPTED']) ? $LANG['SELECT_ACCEPTED'] : 'Select Accepted Term') ?> </option>
									<option value="">----------------------</option>
									<option value=""> <?php echo (isset($LANG['IS_ACCEPTED']) ? $LANG['IS_ACCEPTED'] : 'Term is Accepted') ?> </option>
									<?php
									$acceptedList = $geoManager->getAcceptedGeoTermArr();
									foreach($acceptedList as $id => $term){
										echo '<option value="'.$id.'">'.$term.'</option>';
									}
									?>
								</select>
							</span>
						</div>
						<div id="addButton-div" class="button-div">
							<button type="submit" name="submitaction" value="addGeoUnit"> <?php echo (isset($LANG['ADD_UNIT']) ? $LANG['ADD_UNIT'] : 'Add Unit') ?> </button>
						</div>
					</form>
				</fieldset>
			</div>
			<?php
			if($geoArr){
				$titleStr = '';
				$parentArr = $geoManager->getGeograpicUnit($parentID);
				if($parentID){
					$rankArr = $geoManager->getGeoRankArr();
					$titleStr = '<b>'. $rankArr[$geoArr[key($geoArr)]['geoLevel']] . '</b>' . (isset($LANG['TERMS_WITHIN']) ? $LANG['TERMS_WITHIN'] : 'geographic terms within') . '<b>' . $parentArr['geoTerm'] . '</b>';
				}
				else{
					$titleStr = '<b>' . (isset($LANG['ROOT_TERMS']) ? $LANG['ROOT_TERMS'] : 'Root Terms (terms without parents)') . '</b>';
				}
				echo '<div style=";font-size:1.3em;margin: 10px 0px">'.$titleStr.'</div>';
				echo '<ul>';
				foreach($geoArr as $geoID => $unitArr){
					$termDisplay = '<a href="index.php?geoThesID=' . htmlspecialchars($geoID, HTML_SPECIAL_CHARS_FLAGS) . '">' . htmlspecialchars($unitArr['geoTerm'], HTML_SPECIAL_CHARS_FLAGS) . '</a>';
					if($unitArr['abbreviation']) $termDisplay .= ' ('.$unitArr['abbreviation'].') ';
					else{
						$codeStr = '';
						if($unitArr['iso2']) $codeStr = $unitArr['iso2'].', ';
						if($unitArr['iso3']) $codeStr .= $unitArr['iso3'].', ';
						if($unitArr['numCode']) $codeStr .= $unitArr['numCode'].', ';
						if($codeStr) $termDisplay .= ' ('.trim($codeStr,', ').') ';
					}
					if($unitArr['acceptedTerm']) $termDisplay .= ' => <a href="index.php?geoThesID=' . htmlspecialchars($unitArr['acceptedID'], HTML_SPECIAL_CHARS_FLAGS) . '">' . htmlspecialchars($unitArr['acceptedTerm'], HTML_SPECIAL_CHARS_FLAGS) . '</a>';
					elseif(isset($unitArr['childCnt']) && $unitArr['childCnt']) $termDisplay .= ' - <a href="index.php?parentID=' . htmlspecialchars($geoID, HTML_SPECIAL_CHARS_FLAGS) . '">' . htmlspecialchars($unitArr['childCnt'], HTML_SPECIAL_CHARS_FLAGS) . (isset($LANG['CHILDREN']) ? $LANG['CHILDREN'] : 'children') . '</a>';
					echo '<li>'.$termDisplay.'</li>';
				}
				echo '</ul>';
				if($parentID) echo '<div class="link-div"><a href="index.php?parentID=' . htmlspecialchars($parentArr['parentID'], HTML_SPECIAL_CHARS_FLAGS) . '">' . (isset($LANG['SHOW_LIST']) ? $LANG['SHOW_LIST'] : 'Show Parent list') . '</a></div>';
			}
			else echo '<div>' . (isset($LANG['NO_RECORDS']) ? $LANG['NO_RECORDS'] : 'No records returned') . '</div>';
			if($geoThesID || $parentID) echo '<div class="link-div"><a href="index.php">Show base list</a></div>';
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>