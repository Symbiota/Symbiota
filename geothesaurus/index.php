<?php
include_once ('../config/symbini.php');
include_once ($SERVER_ROOT . '/classes/GeographicThesaurus.php');
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
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> - Geographic Thesaurus Manager</title>
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
	<style type="text/css">
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
		<a href="../index.php">Home</a> &gt;&gt;
		<b><a href="index.php">Geographic Thesaurus Base List</a></b>
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
					<legend>Edit Geographic<span id="edit-legend"> Unit</span></legend>
					<div style="float:right">
						<span class="editIcon"><a href="#" onclick="toggleEditor()"><img class="editimg" src="../images/edit.png" /></a></span>

					</div>
					<form name="unitEditForm" action="index.php" method="post">
						<div class="field-div">
							<label>GeoUnit Name</label>:
							<span class="editTerm"><?php echo $geoUnit['geoTerm']; ?></span>
							<span class="editFormElem"><input type="text" name="geoTerm" value="<?php echo $geoUnit['geoTerm'] ?>" style="width:200px;" required /></span>
						</div>
						<div class="field-div">
							<label>Abbreviation</label>:
							<span class="editTerm"><?php echo $geoUnit['abbreviation']; ?></span>
							<span class="editFormElem"><input type="text" name="abbreviation" value="<?php echo $geoUnit['abbreviation'] ?>" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label>ISO2 Code</label>:
							<span class="editTerm"><?php echo $geoUnit['iso2']; ?></span>
							<span class="editFormElem"><input type="text" name="iso2" value="<?php echo $geoUnit['iso2'] ?>" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label>ISO3 Code</label>:
							<span class="editTerm"><?php echo $geoUnit['iso3']; ?></span>
							<span class="editFormElem"><input type="text" name="iso3" value="<?php echo $geoUnit['iso3'] ?>"style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label>Numeric Code</label>:
							<span class="editTerm"><?php echo $geoUnit['numCode']; ?></span>
							<span class="editFormElem"><input type="text" name="numCode" value="<?php echo $geoUnit['numCode'] ?>" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label>Geography Rank</label>:
							<span class="editTerm"><?php echo ($geoUnit['geoLevel']?$rankArr[$geoUnit['geoLevel']].' ('.$geoUnit['geoLevel'].')':''); ?></span>
							<span class="editFormElem">
								<select name="geoLevel">
									<option value="">Select Rank</option>
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
							<label>Notes</label>:
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
									<label>Parent term</label>:
									<span class="editTerm"><?php echo $parentStr; ?></span>
									<span class="editFormElem">
										<select name="parentID">
											<option value="">Is a Root Term (e.g. no parent)</option>
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
							<label>Accepted term</label>:
							<span class="editTerm"><?php echo $acceptedStr; ?></span>
							<span class="editFormElem">
								<select name="acceptedID">
									<option value="">Term is Accepted</option>
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
							<button type="submit" name="submitaction" value="submitGeoEdits">Save Edits</button>
						</div>
					</form>
				</fieldset>
			</div>
			<div id="unitDel-div">
				<form name="unitDeleteForm" action="index.php" method="post">
					<fieldset>
						<legend>Delete Geographic Unit</legend>
						<div class="button-div">
							<input name="parentID" type="hidden" value="<?php echo $geoUnit['parentID']; ?>" />
							<input name="delGeoThesID" type="hidden"  value="<?php echo $geoThesID; ?>" />

							<!-- We need to decide if we want to allow folks to delete a term and all their children, or only can delete if no children or synonym exists. I'm thinking the later. -->

							<button type="submit" name="submitaction" value="deleteGeoUnits" onclick="return confirm('Are you sure you want to delete this record?')" <?php echo ($geoUnit['childCnt']?'disabled':''); ?>>Delete Geographic Unit</button>
						</div>
						<?php
						if($geoUnit['childCnt']) echo '<div>* Record can not be deleted until all child records are deleted</div>';
						?>
					</fieldset>
				</form>
			</div>
			<?php
			echo '<div class="link-div">';
			echo '<div><a href="index.php?' . htmlspecialchars((isset($geoUnit['parentID'])?'parentID='.$geoUnit['parentID']:''), HTML_SPECIAL_CHARS_FLAGS) . '">Show ' . htmlspecialchars((isset($geoUnit['geoLevel'])?$rankArr[$geoUnit['geoLevel']]:''), HTML_SPECIAL_CHARS_FLAGS) . ' terms</a></div>';
			if(isset($geoUnit['childCnt']) && $geoUnit['childCnt']) echo '<div><a href="index.php?parentID=' . htmlspecialchars($geoThesID, HTML_SPECIAL_CHARS_FLAGS) . '">Show children</a></div>';
			echo '</div>';
		}
		else{
			?>
			<div style="float:right">
				<span class="editIcon"><a href="#" onclick="$('#addGeoUnit-div').toggle();"><img class="editimg" src="../images/add.png" /></a></span>
			</div>
			<div id="addGeoUnit-div" style="clear:both;margin-bottom:10px;display:none">
				<!--This should also be visible when !$geoThesID -->
				<fieldset id="new-fieldset">
					<legend>Add Geographic Unit</legend>
					<form name="unitAddForm" action="index.php" method="post">
						<div class="field-div">
							<label>GeoUnit Name</label>:
							<span><input type="text" name="geoTerm" style="width:200px;" required /></span>
						</div>
						<div class="field-div">
							<label>Abbreviation</label>:
							<span><input type="text" name="abbreviation" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label>ISO2 Code</label>:
							<span><input type="text" name="iso2" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label>ISO3 Code</label>:
							<span><input type="text" name="iso3" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label>Numeric Code</label>:
							<span><input type="text" name="numCode" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label>Geography Rank</label>:
							<span>
								<select name="geoLevel">
									<option value="">Select Rank</option>
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
							<label>Notes</label>:
							<span><input type="text" name="notes" maxlength="250" style="width:200px;" /></span>
						</div>
						<div class="field-div">
							<label>Parent term</label>:
							<span>
								<select name="parentID">
									<option value="">Select Parent Term</option>
									<option value="">----------------------</option>
									<option value="">Is a Root Term (e.g. no parent)</option>
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
							<label>Accepted term</label>:
							<span>
								<select name="acceptedID">
									<option value="">Select Accepted Term</option>
									<option value="">----------------------</option>
									<option value="">Term is Accepted</option>
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
							<button type="submit" name="submitaction" value="addGeoUnit">Add Unit</button>
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
					$titleStr = '<b>'.$rankArr[$geoArr[key($geoArr)]['geoLevel']].'</b> geographic terms within <b>'.$parentArr['geoTerm'].'</b>';
				}
				else{
					$titleStr = '<b>Root Terms (terms without parents)</b>';
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
					elseif(isset($unitArr['childCnt']) && $unitArr['childCnt']) $termDisplay .= ' - <a href="index.php?parentID=' . htmlspecialchars($geoID, HTML_SPECIAL_CHARS_FLAGS) . '">' . htmlspecialchars($unitArr['childCnt'], HTML_SPECIAL_CHARS_FLAGS) . ' children</a>';
					echo '<li>'.$termDisplay.'</li>';
				}
				echo '</ul>';
				if($parentID) echo '<div class="link-div"><a href="index.php?parentID=' . htmlspecialchars($parentArr['parentID'], HTML_SPECIAL_CHARS_FLAGS) . '">Show Parent list</a></div>';
			}
			else echo '<div>No records returned</div>';
			if($geoThesID || $parentID) echo '<div class="link-div"><a href="index.php">Show base list</a></div>';
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>