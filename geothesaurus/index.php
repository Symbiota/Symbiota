<?php
include_once ('../config/symbini.php');
include_once ($SERVER_ROOT . '/classes/GeographicThesaurus.php');
include_once($SERVER_ROOT.'/content/lang/geothesaurus/index.'.$LANG_TAG.'.php');
header("Content-Type: text/html; charset=".$CHARSET);

$geoThesID = array_key_exists('geoThesID', $_REQUEST) ? filter_var($_REQUEST['geoThesID'], FILTER_SANITIZE_NUMBER_INT) : '';
$parentID = array_key_exists('parentID', $_REQUEST) ? filter_var($_REQUEST['parentID'], FILTER_SANITIZE_NUMBER_INT) : '';
$submitAction = array_key_exists('submitaction', $_POST) ? $_POST['submitaction'] : '';

$geoManager = new GeographicThesaurus();

$isEditor = false;
if($IS_ADMIN) $isEditor = true;

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

$geoArr = array();
if(!$geoThesID) $geoArr = $geoManager->getGeograpicList($parentID);
$geoUnit = $geoManager->getGeograpicUnit($geoThesID);
$rankArr = $geoManager->getGeoRankArr();
$parentArr = array();
if($parentID) $parentArr = $geoManager->getGeograpicUnit($parentID);

?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<title><?= $DEFAULT_TITLE; ?> - <?= $LANG['GEOTHES_TITLE'] ?></title>
	<?php
	include_once ($SERVER_ROOT.'/includes/head.php');
	include_once ($SERVER_ROOT.'/includes/leafletMap.php');
	?>

	<style>
		fieldset{ margin: 10px; padding: 15px; width: 600px }
		legend{ font-weight: bold; }
		.fieldset-like span{ font-weight: bold; }
		#innertext{ min-height: 500px; }
		label{ text-decoration: underline; }
		#edit-legend{ display: none }
		.field-div{ margin: 3px 0px }
		.editIcon{ }
		.editTerm{ }
		.editFormElem{ display: none; }
		#editButton-div{ display: none; }
		#unitDel-div{ display: none; }
		.button-div{ margin: 15px }
		#status-div{ margin:15px; padding: 15px; color: red; }
	</style>
	<script src="https://unpkg.com/terraformer@1.0.8"></script>
	<script src="https://unpkg.com/terraformer-wkt-parser@1.1.2"></script>
	<script type="text/javascript">
		function toggleEditor(){
			toggle(".editTerm");
			toggle(".editFormElem");
			toggle("#editButton-div", "block");
			toggle("#edit-legend");
			toggle("#unitDel-div", "block");
		}

		function toggle (target, defaultDisplay = "inline"){
			const targetList = document.querySelectorAll(target);
			for (let i = 0; i < targetList.length; i++) {
				let targetDisplay = window.getComputedStyle(targetList[i]).getPropertyValue('display');
			targetList[i].style.display = (targetDisplay == 'none') ? defaultDisplay : 'none';
			}
		}

		function leafletInit() {
			const wkt_form = document.getElementById('footprintwkt');
			const map_container = document.getElementById('map_canvas');

			if(!wkt_form || !wkt_form.value || !map_container) {
				if(map_container) map_container.style.display = "none";
				return;
			}
			else { 
				map_container.style.display = "block";
			}

			let map = new LeafletMap('map_canvas', {center: [0,0], zoom: 1});

			map.enableDrawing({
				polyline: false,
				control: false,
				circlemarker: false,
				marker: false,
				drawColor: {opacity: 0.85, fillOpacity: 0.55, color: '#000' }
			});

			const geoJson = Terraformer.WKT.parse(wkt_form.value);
			map.drawShape({type: "polygon", latlngs: geoJson.coordinates[0], wkt: wkt_form.value})
		}

		function openCoordAid(id="footprintwkt") {
			mapWindow = open(
				`../collections/tools/mapcoordaid.php?mapmode=polygon&map_mode_strict=true&wkt_input_id=${id}`,
				"polygon",
				"resizable=0,width=900,height=630,left=20,top=20",
			);
			if (mapWindow.opener == null) mapWindow.opener = self;
			mapWindow.focus();
		}

		function init() {
			try {
				leafletInit();
			} catch(e) {
				console.log("Leaflet Map failed to load")
			}
		}
	</script>
	</head>
	<body onload="init()">
		<div 
			id="service-container" 
			data-geo-unit="<?php echo htmlspecialchars(json_encode($geoUnit))?>"
		/>
		<?php
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
			<a href="../index.php">
				<?= $LANG['NAV_HOME'] ?> </a> &gt;&gt;
			<b> <?= $LANG['NAV_GEOTHES'] ?> </b>
		</div>
		<div id='innertext'>
			<?php
			if($statusStr){
			echo '<div id="status-div">'.$statusStr.'</div>';
			}
			?>
			<section class="fieldset-like" style="float:right; min-width: 175px">
				<h3>
					<span>
						<?= $LANG['NAVIGATION_PANEL'] ?>
					</span>
				</h3>
				<ul>
					<?php
					echo '<li><a href="index.php">'.$LANG['SHOW_BASE_LIST'].'</a></li>';
					if($geoThesID && !empty($geoUnit['parentID'])){
					echo '<li><a href="index.php?geoThesID=' . $geoUnit['parentID'] . '">' . $LANG['SHOW_PARENT'] . '</a></li>';
					}elseif($parentID){
					echo '<li><a href="index.php?geoThesID=' . $parentID . '">' . $LANG['SHOW_PARENT'] . '</a></li>';
					}
					$parID = false;
					if(!empty($geoUnit['parentID'])) $parID = $geoUnit['parentID'];
					elseif($parentID && isset($parentArr['parentID'])) $parID = $parentArr['parentID'];
					if($parID !== false){
					echo '<li><a href="index.php?parentID=' . $parID . '">' . $LANG['SHOW_PARENT_NODE'] . '</a></li>';
					}
					if(isset($geoUnit['childCnt'])) echo '<li><a href="index.php?parentID=' . $geoThesID . '">' . $LANG['SHOW_CHILDREN'] . '</a></li>';
					echo '<li style="margin-top:10px"><a href="harvester.php">'.$LANG['GOTO_HARVESTER'].'</a></li>';
					?>
				</ul>
			</section>
			<div id="addGeoUnit-div" style="clear:both;margin-bottom:10px;display:none">
				<form name="unitAddForm" action="index.php" method="post">
					<fieldset id="new-fieldset">
						<legend> <?= $LANG['ADD_GEO_UNIT'] ?> </legend>
						<div class="field-div">
							<label> <?= $LANG['GEO_UNIT_NAME'] ?> </label>:
							<span><input type="text" name="geoTerm" style="width:200px;" required /></span>
						</div>
						<div class="field-div">
							<label> <?= $LANG['ABBR'] ?> </label>:
							<span><input type="text" name="abbreviation" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label> <?= $LANG['ISO2'] ?> </label>:
							<span><input type="text" name="iso2" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label> <?= $LANG['ISO3'] ?> </label>:
							<span><input type="text" name="iso3" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label> <?= $LANG['NUM_CODE'] ?> </label>:
							<span><input type="text" name="numCode" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label> <?= $LANG['GEO_RANK'] ?> </label>:
							<span>
								<select name="geoLevel" required>
									<option value=""> <?= $LANG['SELECT_RANK'] ?> </option>
									<option value="">----------------------</option>
									<?php
									$defaultGeoLevel = false;
									if($geoArr) $defaultGeoLevel = $geoArr[key($geoArr)]['geoLevel'];
									foreach($rankArr as $rankID => $rankValue){
									if($geoThesID){
									//Grabs the next highest rankid when matched
									if($defaultGeoLevel == 'getNextRankid') $defaultGeoLevel = $rankID;
									if($rankID == $geoUnit['geoLevel']) $defaultGeoLevel = 'getNextRankid';
									}
									echo '<option value="'.$rankID.'" '.($defaultGeoLevel === $rankID?'SELECTED':'').'>'.$rankValue.'</option>';
									}
									?>
								</select>
							</span>
						</div>
						<div class="field-div">
							<label> <?= $LANG['NOTES'] ?> </label>:
							<span><input type="text" name="notes" maxlength="250" style="width:200px;" /></span>
						</div>
						<div class="field-div">
							<label> <?= $LANG['PARENT_TERM'] ?> </label>:
							<span>
								<select name="parentID">
									<option value=""> <?= $LANG['SELECT_PARENT'] ?> </option>
									<option value="">----------------------</option>
									<option value=""> <?= $LANG['IS_ROOT_TERM'] ?> </option>
									<?php
									$parentList = $geoManager->getParentGeoTermArr();
									foreach($parentList as $id => $term){
									echo '<option value="'.$id.'" '.($parentID == $id || $geoThesID == $id?'SELECTED':'').'>'.$term.'</option>';
									}
									?>
								</select>
							</span>
						</div>
						<div class="field-div">
							<label> <?= $LANG['ACCEPTED_TERM'] ?> </label>:
							<span>
								<select name="acceptedID">
									<option value=""> <?= $LANG['SELECT_ACCEPTED'] ?> </option>
									<option value="">----------------------</option>
									<option value=""> <?= $LANG['IS_ACCEPTED'] ?> </option>
									<?php
									$acceptedList = $geoManager->getAcceptedGeoTermArr();
									foreach($acceptedList as $id => $term){
									echo '<option value="'.$id.'">'.$term.'</option>';
									}
									?>
								</select>
							</span>
						</div>
						<div class="field-div">
							<label><?=$LANG['POLYGON']?></label>:
							<a onclick="openCoordAid('addfootprintwkt')">
								<img src='../images/world.png' style='width:10px;border:0' alt='Image of the globe' /> <?= $LANG['EDIT_POLYGON']?> 
							</a>
							<span><textarea id="addfootprintwkt" name="polygon" style="margin-top: 0.5rem; width:98%;height:90px;"></textarea></span>
						</div>
						<div id="addButton-div" class="button-div">
							<button type="submit" name="submitaction" value="addGeoUnit"> <?= $LANG['ADD_UNIT'] ?> </button>
						</div>
					</fieldset>
				</form>
			</div>
			<?php
			if($geoThesID && $geoUnit){
			?>
			<div id="updateGeoUnit-div" style="margin-bottom:10px;">
				<form name="unitEditForm" action="index.php" method="post">
					<fieldset id="edit-fieldset">
						<legend><span id="edit-legend"><?= $LANG['EDIT'] ?></span> <?= $LANG['GEO_UNIT'] ?> </legend>
						<div style="float:right">
							<span class="editIcon" title="Add child term"><a href="#" onclick="toggle('#addGeoUnit-div');"><img class="editimg" src="../images/add.png" alt="<?= $LANG['EDIT'] ?>" /></a></span>
							<span class="editIcon" title="Edit term"><a href="#" onclick="toggleEditor()"><img class="editimg" src="../images/edit.png" alt="<?= $LANG['EDIT']; ?>"></a></span>
						</div>
						<div class="field-div">
							<label> <?= $LANG['GEO_UNIT_NAME'] ?> </label>:
							<span class="editTerm"><?= $geoUnit['geoTerm']; ?></span>
							<span class="editFormElem" style="display: none"><input type="text" name="geoTerm" value="<?php echo $geoUnit['geoTerm'] ?>" style="width:200px;" required /></span>
						</div>
						<div class="field-div">
							<label> <?= $LANG['ABBR'] ?> </label>:
							<span class="editTerm"><?= $geoUnit['abbreviation']; ?></span>
							<span class="editFormElem"><input type="text" name="abbreviation" value="<?= $geoUnit['abbreviation'] ?>" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label> <?= $LANG['ISO2'] ?> </label>:
							<span class="editTerm"><?= $geoUnit['iso2']; ?></span>
							<span class="editFormElem"><input type="text" name="iso2" value="<?= $geoUnit['iso2'] ?>" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label> <?= $LANG['ISO3'] ?> </label>:
							<span class="editTerm"><?= $geoUnit['iso3']; ?></span>
							<span class="editFormElem"><input type="text" name="iso3" value="<?= $geoUnit['iso3'] ?>"style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label> <?= $LANG['NUM_CODE'] ?> </label>:
							<span class="editTerm"><?= $geoUnit['numCode']; ?></span>
							<span class="editFormElem"><input type="text" name="numCode" value="<?= $geoUnit['numCode'] ?>" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label> <?= $LANG['GEO_RANK'] ?> </label>:
							<span class="editTerm"><?= ($geoUnit['geoLevel']?$rankArr[$geoUnit['geoLevel']].' ('.$geoUnit['geoLevel'].')':''); ?></span>
							<span class="editFormElem">
								<select name="geoLevel" required>
									<option value=""> <?= $LANG['SELECT_RANK'] ?> </option>
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
							<label> <?= $LANG['NOTES'] ?> </label>:
							<span class="editTerm"><?= $geoUnit['notes']; ?></span>
							<span class="editFormElem"><input type="text" name="notes" value="<?= $geoUnit['notes'] ?>" maxlength="250" style="width:650px;" /></span>
						</div>
						<?php
						if($geoUnit['geoLevel']){
						if($parentList = $geoManager->getParentGeoTermArr($geoUnit['geoLevel'])){
						$parentStr = '';
						if($geoUnit['parentTerm']) $parentStr = '<a href="index.php?geoThesID=' . $geoUnit['parentID'] . '">' . $geoUnit['parentTerm'] . '</a>';
						?>
						<div class="field-div">
							<label> <?= $LANG['PARENT_TERM'] ?> </label>:
							<span class="editTerm"><?= $parentStr; ?></span>
							<span class="editFormElem">
								<select name="parentID">
									<option value=""> <?= $LANG['IS_ROOT_TERM'] ?> </option>
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
						if($geoUnit['acceptedTerm']) $acceptedStr = '<a href="index.php?geoThesID=' . $geoUnit['acceptedID'] . '">' . htmlspecialchars($geoUnit['acceptedTerm'], HTML_SPECIAL_CHARS_FLAGS) . '</a>';
						?>
						<div class="field-div">
							<label> <?= $LANG['ACCEPTED_TERM'] ?> </label>:
							<span class="editTerm"><?php echo $acceptedStr; ?></span>
							<span class="editFormElem">
								<select name="acceptedID">
									<option value=""> <?= $LANG['IS_ACCEPTED'] ?> </option>
									<option value="">----------------------</option>
									<?php
									$acceptedList = $geoManager->getAcceptedGeoTermArr($geoUnit['geoLevel'], $geoUnit['parentID']);
									foreach($acceptedList as $id => $term){
									echo '<option value="'.$id.'" '.($id==$geoUnit['acceptedID']?'selected':'').'>'.$term.'</option>';
								}
								?>
								</select>
							</span>
						</div>
						<div class="field-div">
						<label><?= $LANG['POLYGON']?></label>:
						<span class="editTerm">
							<?= $geoUnit['wkt'] !== null? $LANG['YES_POLYGON']: $LANG['NO_POLYGON'] ?>
						</span>
						<div class="editTerm" id="map_canvas" style="margin: 1rem 0; width:100%; height:20rem"></div>
						<a class="editFormElem" onclick="openCoordAid()">
							<img src='../images/world.png' style='width:10px;border:0' alt='Image of the globe' /> <?= $LANG['EDIT_POLYGON']?> 
						</a>
						<span class="editFormElem" style="margin-top: 0.5rem">
							<textarea id="footprintwkt" name="polygon" style="margin-top: 0.5rem; width:98%;height:90px;"><?= isset($geoUnit['wkt'])?trim($geoUnit['wkt']): null ?></textarea>
						</span>
					</div>
						<div id="editButton-div" class="button-div">
							<input name="geoThesID" type="hidden" value="<?= $geoThesID; ?>" />
							<button type="submit" name="submitaction" value="submitGeoEdits"> <?= $LANG['SAVE_EDITS'] ?> </button>
						</div>
					</fieldset>
				</form>
			</div>
			<div id="unitDel-div">
				<form name="unitDeleteForm" action="index.php" method="post">
					<fieldset>
						<legend> <?= $LANG['DEL_GEO_UNIT'] ?> </legend>
						<div class="button-div">
							<input name="parentID" type="hidden" value="<?= $geoUnit['parentID']; ?>" />
							<input name="delGeoThesID" type="hidden"  value="<?= $geoThesID; ?>" />
							<!-- We need to decide if we want to allow folks to delete a term and all their children, or only can delete if no children or synonym exists. I'm thinking the later. -->
							<button type="submit" name="submitaction" value="deleteGeoUnits" onclick="return confirm(<?= $LANG['CONFIRM_DELETE'] ?>)" <?= ($geoUnit['childCnt'] ? 'disabled' : ''); ?>> <?= $LANG['DEL_GEO_UNIT'] ?> </button>
						</div>
						<?php
						if($geoUnit['childCnt']) echo '<div>* ' . $LANG['CANT_DELETE'] . '</div>';
						?>
					</fieldset>
				</form>
			</div>
			<?php
			}
			else{
			if($geoArr){
			$titleStr = '';
			if($parentID){
			$titleStr = '<b>'. $rankArr[$geoArr[key($geoArr)]['geoLevel']] . '</b> ' . $LANG['TERMS_WITHIN'] . ' <b>' . $parentArr['geoTerm'] . '</b>';
			}
			else{
			$titleStr = '<b>' . $LANG['ROOT_TERMS'] . '</b>';
			}
			?>
			<div style="font-size:1.3em;margin: 10px 0px">
				<?= $titleStr ?>
				<span class="editIcon" title="Add term to list">
					<a href="#" onclick="toggle('#addGeoUnit-div');"><img class="editimg" src="../images/add.png" alt="<?= $LANG['EDIT'] ?>" /></a>
				</span>
			</div>
			<?php
			echo '<ul>';
			foreach($geoArr as $geoID => $unitArr){
			$termDisplay = '<a href="index.php?geoThesID=' . $geoID . '">' . htmlspecialchars($unitArr['geoTerm'], HTML_SPECIAL_CHARS_FLAGS) . '</a>';
			if($unitArr['abbreviation']) $termDisplay .= ' ('.$unitArr['abbreviation'].') ';
			else{
			$codeStr = '';
			if($unitArr['iso2']) $codeStr = $unitArr['iso2'].', ';
			if($unitArr['iso3']) $codeStr .= $unitArr['iso3'].', ';
			if($unitArr['numCode']) $codeStr .= $unitArr['numCode'].', ';
			if($codeStr) $termDisplay .= ' ('.trim($codeStr,', ').') ';
			}
			if($unitArr['acceptedTerm']) $termDisplay .= ' => <a href="index.php?geoThesID=' . $unitArr['acceptedID'] . '">' . htmlspecialchars($unitArr['acceptedTerm'], HTML_SPECIAL_CHARS_FLAGS) . '</a>';
			elseif(isset($unitArr['childCnt']) && $unitArr['childCnt']) $termDisplay .= ' - <a href="index.php?parentID=' . $geoID . '">' . $unitArr['childCnt'] . ' ' . $LANG['CHILDREN'] . '</a>';
			echo '<li>'.$termDisplay.'</li>';
			}
			echo '</ul>';
			}
			else{
			echo '<div>';
			if($parentID || !$isEditor) echo $LANG['NO_RECORDS'];
			else echo '<a href="harvester.php">'.$LANG['GOTO_HARVESTER'].'</a>';
			echo '</div>';
			}
			}
			?>
		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
