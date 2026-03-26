<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ChecklistAdmin.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load('checklists/checklistadminmeta');

header('Content-Type: text/html; charset='.$CHARSET);

$clid = array_key_exists('clid', $_REQUEST) ? Sanitize::int($_REQUEST['clid']) : 0;
$pid = array_key_exists('pid', $_REQUEST) ? Sanitize::int($_REQUEST['pid']) : 0;

$clManager = new ChecklistAdmin();
$clManager->setClid($clid);

$clArray = $clManager->getMetaData($pid);
$clArray = $clManager->cleanOutArray($clArray);
$footprint = $clManager->getFootprint();

$defaultArr = array();
if(isset($clArray['defaultsettings']) && $clArray['defaultsettings']){
	$defaultArr = json_decode($clArray['defaultsettings'], true);
}

$excludeParent = 0;
if(!empty($clArray['excludeparent'])) $excludeParent = $clArray['excludeparent'];
elseif(!empty($_REQUEST['excludeparent'])) $excludeParent = Sanitize::int($_REQUEST['excludeparent']);
$clType = '';
if(!empty($clArray['type'])) $clType = $clArray['type'];
elseif($excludeParent) $clType = 'excludespp';
?>
<script type="text/javascript" src="../js/tinymce/tinymce.min.js"></script>
<script src="<?= $CLIENT_ROOT ?>/js/symb/mapAidUtils.js" type="text/javascript"></script>
<script type="text/javascript">
	<?php
	if($excludeParent) echo 'setExclusionChecklistMode();';
	?>

	tinymce.init({
		selector: "textarea",
		width: "100%",
		height: 300,
		menubar: false,
		plugins: "link,charmap,code,paste,textcolor",
		toolbar : "bold italic underline forecolor cut copy paste outdent indent undo redo subscript superscript removeformat link charmap code",
		default_link_target: "_blank",
		paste_as_text: true
	});

	function validateChecklistForm(f){
		if(f.name.value == ""){
			alert("<?= $LANG['NEED_NAME']; ?>");
			return false;
		}
		if(f.latcentroid.value != ""){
			if(f.longcentroid.value == ""){
				alert("<?= $LANG['NEED_LONG']; ?>");
				return false;
			}
			if(!isNumeric(f.latcentroid.value)){
				alert("<?= $LANG['LAT_NUMERIC']; ?>");
				return false;
			}
			if(Math.abs(f.latcentroid.value) > 90){
				alert("<?= $LANG['NO_NINETY']; ?>");
				return false;
			}
		}
		if(f.longcentroid.value != ""){
			if(f.latcentroid.value == ""){
				alert("<?= $LANG['NEED_LAT']; ?>");
				return false;
			}
			if(!isNumeric(f.longcentroid.value)){
				alert("<?= $LANG['LONG_NUMERIC']; ?>");
				return false;
			}
			if(Math.abs(f.longcentroid.value) > 180){
				alert("<?= $LANG['NO_ONE_EIGHTY']; ?>");
				return false;
			}
		}
		if(f.type){
			if(f.type.value == "rarespp" && f.locality.value == ""){
				alert("<?= $LANG['NEED_STATE']; ?>");
				return false;
			}
			else if(f.type.value == "excludespp" && f.excludeparent.value == ""){
				alert("<?= $LANG['NEED_PARENT']; ?>");
				return false;
			}
		}
		return true;
	}

	function checklistTypeChanged(){
		if(document.getElementById("checklisteditform").type.value == "excludespp"){
			setExclusionChecklistMode();
		}
		else{
			document.getElementById("exclude-div").style.display = "none";
			document.getElementById("checklisteditform").excludeparent.value = '';
			document.getElementById("accessDiv").style.display = "block";
			document.getElementById("authorDiv").style.display = "block";
			document.getElementById("locDiv").style.display = "block";
			document.getElementById("inclusiveClDiv").style.display = "block";
			document.getElementById("geoDiv").style.display = "block";
			document.getElementById("externalService-div").style.display = "block";
			document.getElementById("polygon-div").style.display = "block";
		}
	}

	function setExclusionChecklistMode(){
		document.getElementById("exclude-div").style.display = "block";
		document.getElementById("accessDiv").style.display = "none";
		document.getElementById("authorDiv").style.display = "none";
		document.getElementById("locDiv").style.display = "none";
		document.getElementById("inclusiveClDiv").style.display = "none";
		document.getElementById("geoDiv").style.display = "none";
		document.getElementById("externalService-div").style.display = "none";
		document.getElementById("polygon-div").style.display = "none";
		document.getElementById("checklisteditform").activatekey.checked = false;
	}

	function openMappingPointAid() {
		mapWindow=open("<?= $CLIENT_ROOT; ?>/collections/tools/mappointaid.php","mapaid","resizable=0,width=1000,height=800,left=20,top=20");
	    if(mapWindow.opener == null) mapWindow.opener = self;
	}

	function enableDisableExtServiceFields() {
		let xsrv = document.getElementById('externalservice');
		let xsid = document.getElementById('externalserviceid');
		let xstaxonfilter = document.getElementById('externalserviceiconictaxon');
		if(xsrv.value == '') {
			document.getElementById("externalServiceID-div").style.display = "none";
			xsid.setAttribute("disabled","");
			xstaxonfilter.setAttribute("disabled","");
		} else {
			document.getElementById("externalServiceID-div").style.display = "block";
			xsid.removeAttribute("disabled");
			xstaxonfilter.removeAttribute("disabled");
		}
	}
</script>
<style>
	label{ font-weight: bold; display:block; }
	fieldset { margin:15px;padding:10px; }
	legend { font-weight: bold; }
</style>
<?php
if(!$clid){
	?>
	<div style="float:right;">
		<a href="#" onclick="toggle('checklistDiv')" title="<?= $LANG['CREATE_CHECKLIST'] ?>"><img src="../images/add.png" style="width:1.5em;" /></a>
	</div>
	<?php
}
$displayform = false;
if($clid || !empty($_REQUEST['excludeparent'])) $displayform = true;
?>
<div id="checklistDiv" style="display:<?= ($displayform ? 'block' : 'none') ?>;">
	<form id="checklisteditform" action="<?= $CLIENT_ROOT; ?>/checklists/checklistadmin.php" method="post" name="editclmatadata" onsubmit="return validateChecklistForm(this)">
		<fieldset>
			<legend><b><?= ($clid?$LANG['EDITCHECKDET']:$LANG['CREATECHECKDET']); ?></b></legend>
			<div>
				<label><?= $LANG['CHECKNAME']; ?></label>
				<input type="text" name="name" style="width:95%" value="<?= $clManager->getClName();?>" />
			</div>
			<div id="authorDiv">
				<label><?= $LANG['AUTHORS'];?></label>
				<input type="text" name="authors" style="width:95%" value="<?= ($clArray?$clArray["authors"]:''); ?>" />
			</div>
			<div>
				<label><?= $LANG['CHECKTYPE'];?></label>
				<?php
				$userClArr = $clManager->getUserChecklistArr();
				?>
				<select name="type" onchange="checklistTypeChanged(this.form)">
					<option value="static"><?= $LANG['GENCHECK'];?></option>
					<?php
					if($userClArr){
						?>
						<option value="excludespp" <?= ($clType == 'excludespp' ? 'SELECTED' : '') ?>><?= $LANG['EXCLUDESPP'] ?></option>
						<?php
					}
					if(isset($GLOBALS['USER_RIGHTS']['RareSppAdmin']) || $IS_ADMIN){
						echo '<option value="rarespp"' . ($clType == 'rarespp' ? 'SELECTED' : '') . '>' . $LANG['RARETHREAT'] . '</option>';
					}
					?>
				</select>
			</div>
			<?php
			if($userClArr){
				?>
				<div id="exclude-div" style="margin-left:15px;<?= $excludeParent ? '' : 'display:none' ?>">
					<label><?= $LANG['FOR_PARENT_LIST'] ?></label>
					<select name="excludeparent">
						<option value=""><?= $LANG['SELECT_PARENT'] ?></option>
						<option value="">-------------------------------</option>
						<?php
						foreach($userClArr as $userClid => $userClValue){
							echo '<option value="' . $userClid . '" ' . ($userClid==$excludeParent ? 'SELECTED' : '') . '>' . $userClValue . '</option>';
						}
						?>
					</select>
				</div>
				<?php
			}

			$dynamPropsArr = array();
			if(isset($clArray['dynamicProperties']) && $clArray['dynamicProperties']){
				$dynamPropsArr = json_decode($clArray['dynamicProperties'], true);
			}
			?>
			<div id="externalService-div" class="top-breathing-room-rel">
				<label><?= $LANG['EXTSERVICE']; ?></label>
				<select name="externalservice" id="externalservice" onchange="enableDisableExtServiceFields()">
					<option value=""></option>
					<option value="">-------------------------------</option>
					<option value="inaturalist" <?= ((isset($dynamPropsArr['externalservice']) && $dynamPropsArr['externalservice']=='inaturalist')?'selected':''); ?>><?= $LANG['INATURALIST']; ?></option>
				</select>
				<div id="externalServiceID-div" style="margin-left:15px;<?= empty($dynamPropsArr['externalservice']) ? 'display:none' : '' ?>" class="top-breathing-room-rel">
					<div>
						<label><?= $LANG['EXTSERVICEID']; ?></label>
						<input type="text" name="externalserviceid" id="externalserviceid" style="width: 350px" value="<?= ($dynamPropsArr?$dynamPropsArr['externalserviceid']:''); ?>" />
					</div>
					<div>
						<label><?= $LANG['EXTSERVICETAXON']; ?></label>
						<input type="text" name="externalserviceiconictaxon" id="externalserviceiconictaxon" style="width: 350px" value="<?= ($dynamPropsArr?$dynamPropsArr['externalserviceiconictaxon']:''); ?>" />
					</div>
				</div>
			</div>
			<div id="locDiv" class="top-breathing-room-rel">
				<label><?= $LANG['LOC']; ?></label>
				<input type="text" name="locality" style="width:95%" value="<?= ($clArray?$clArray["locality"]:''); ?>" />
			</div>
			<div class="top-breathing-room-rel">
				<label><?= $LANG['CITATION']; ?></label>
				<input type="text" name="publication" style="width:95%" value="<?= ($clArray?$clArray["publication"]:''); ?>" />
			</div>
			<div class="top-breathing-room-rel">
				<label><?= $LANG['ABSTRACT']; ?></label>
				<textarea name="abstract" style="width:95%" rows="6"><?= ($clArray?$clArray["abstract"]:''); ?></textarea>
			</div>
			<div class="top-breathing-room-rel">
				<label><?= $LANG['NOTES']; ?></label>
				<input type="text" name="notes" style="width:95%" value="<?= ($clArray?$clArray["notes"]:''); ?>" />
			</div>
			<div id="inclusiveClDiv">
				<label><?= $LANG['REFERENCE_CHECK']; ?>:</label>
				<select name="parentclid">
					<option value=""><?= $LANG['NONE']; ?></option>
					<option value="">----------------------------------</option>
					<?php
					$refClArr = $clManager->getReferenceChecklists();
					foreach($refClArr as $id => $name){
						echo '<option value="'.$id.'" '.($clArray && $id==$clArray['parentclid']?'SELECTED':'').'>'.$name.'</option>';
					}
					?>
				</select>
			</div>
			<div id="geoDiv" style="width:100%" class="top-breathing-room-rel">
				<div style="float:left;">
					<label><?= $LANG['LATCENT']; ?></label>
					<input id="decimallatitude" type="text" name="latcentroid" style="width:110px;" value="<?= ($clArray?$clArray["latcentroid"]:''); ?>" />
				</div>
				<div style="float:left;margin-left:15px;">
					<label><?= $LANG['LONGCENT']; ?></label>
					<input id="decimallongitude" type="text" name="longcentroid" style="width:110px;" value="<?= ($clArray?$clArray["longcentroid"]:''); ?>" />
				</div>
				<div style="float:left;margin:25px 3px;">
					<a href="#" onclick="openMappingPointAid();return false;"><img src="../images/world.png" style="width:1em;" /></a>
				</div>
				<div style="float:left;margin-left:15px;">
					<label><?= $LANG['POINTRAD']; ?></label>
					<input type="number" id="coordinateuncertaintyinmeters" name="pointradiusmeters" style="width:110px;" value="<?= ($clArray?$clArray["pointradiusmeters"]:''); ?>" />
				</div>
			</div>
			<div id="polygon-div" style="clear:both" class="top-breathing-room-rel">
				<fieldset style="width:350px;">
					<legend><b><?= $LANG['POLYFOOT']; ?></b></legend>
					<span id="polyDefDiv" style="display:<?= ($clArray && $clArray["hasfootprintwkt"]?'inline':'none'); ?>;">
						<?= $LANG['POLYGON_DEFINED']; ?>
					</span>
					<span id="polyNotDefDiv" style="display:<?= ($clArray && $clArray["hasfootprintwkt"]?'none':'inline'); ?>;">
						<?= $LANG['POLYGON_NOT_DEFINED']; ?>
					</span>
					<span style="margin:10px;"><a href="#" onclick="openCoordAid({map_mode:MAP_MODES.POLYGON, client_root: '<?= $CLIENT_ROOT?>', polygon_text_type: POLYGON_TEXT_TYPES.<?= isset($footprint['type']) && $footprint['type'] === 'wkt' ? 'WKT': 'GEOJSON' ?>});return false;" title="<?= $LANG['CREATE_EDIT_POLYGON']; ?>"><img src="../images/world.png" style="width:1em;" /></a></span>
					<input type="hidden" id="footprintwkt" name="footprint<?= Sanitize::outString($footprint['type'] ?? 'geoJson') ?>" value="<?= Sanitize::outString($footprint['footprint'] ?? '') ?>" />
				</fieldset>
			</div>
			<div style="clear:both;" class="top-breathing-room-rel">
				<fieldset style="width:600px;">
					<legend><b><?= $LANG['DEFAULTDISPLAY']; ?></b></legend>
					<div>
						<?php
						echo "<input id='dsynonyms' name='dsynonyms' type='checkbox' value='1' " . (isset($defaultArr["dsynonyms"])&&$defaultArr["dsynonyms"]?"checked":"") . " /> " . $LANG['DISPLAY_SYNONYMS'];
						?>
					</div>
					<div>
						<?php
						//Display Common Names: 0 = false, 1 = true
						if($DISPLAY_COMMON_NAMES) echo "<input id='dcommon' name='dcommon' type='checkbox' value='1' " . (($defaultArr&&$defaultArr["dcommon"])?"checked":"") . " /> " . $LANG['COMMON'];
						?>
					</div>
					<div>
						<!-- Display as Images: 0 = false, 1 = true  -->
						<input name='dimages' id='dimages' type='checkbox' value='1' <?= (($defaultArr&&$defaultArr["dimages"])?"checked":""); ?> onclick="showImagesDefaultChecked(this.form);" />
						<?= $LANG['DISPLAYIMAGES'];?>
					</div>
					<div>
						<!-- Display as Voucher Images: 0 = false, 1 = true  -->
						<input name='dvoucherimages' id='dvoucherimages' type='checkbox' value='1' <?= ((isset($defaultArr['dvoucherimages'])&&$defaultArr['dvoucherimages'])?"checked":""); ?> />
						<?= $LANG['DISPLAYVOUCHERIMAGES'];?>
					</div>
					<div>
						<!-- Display Details: 0 = false, 1 = true  -->
						<input name='ddetails' id='ddetails' type='checkbox' value='1' <?= (($defaultArr&&$defaultArr["ddetails"])?"checked":""); ?> />
						<?= $LANG['SHOWDETAILS'];?>
					</div>
					<div>
						<!-- Display as Vouchers: 0 = false, 1 = true  -->
						<input name='dvouchers' id='dvouchers' type='checkbox' value='1' <?= (($defaultArr&&$defaultArr["dimages"])?"disabled":(($defaultArr&&$defaultArr["dvouchers"])?"checked":"")); ?>/>
						<?= $LANG['NOTESVOUC'];?>
					</div>
					<div>
						<!-- Display Taxon Authors: 0 = false, 1 = true  -->
						<input name='dauthors' id='dauthors' type='checkbox' value='1' <?= (($defaultArr&&$defaultArr["dimages"])?"disabled":(($defaultArr&&$defaultArr["dauthors"])?"checked":"")); ?>/>
						<?= $LANG['TAXONAUTHOR'];?>
					</div>
					<div>
						<!-- Display Taxa Alphabetically: 0 = false, 1 = true  -->
						<input name='dalpha' id='dalpha' type='checkbox' value='1' <?= (!empty($defaultArr['dalpha'])?'checked':''); ?> />
						<?= $LANG['TAXONABC'];?>
					</div>
					<div>
						<!-- Display Taxa Alphabetically: 0 = false, 1 = true  -->
						<input name='dsubgenera' id='dsubgenera' type='checkbox' value='1' <?= (!empty($defaultArr['dsubgenera'])?'checked':''); ?> >
						<?= $LANG['SHOWSUBGENERA'];?>
					</div>
					<div>
						<?php
						// Activate Identification key: 0 = false, 1 = true
						$activateKey = $KEY_MOD_IS_ACTIVE;
						if(array_key_exists('activatekey', $defaultArr??[])) $activateKey = $defaultArr["activatekey"];
						?>
						<input name='activatekey' type='checkbox' value='1' <?= ($activateKey?"checked":""); ?> />
						<?= $LANG['ACTIVATEKEY']; ?>
					</div>
				</fieldset>
			</div>
			<div id="sortSeqDiv" style="clear:both;margin-top:15px;">
				<b><?= $LANG['DEFAULT_SORT']; ?>:</b>
				<input name="sortsequence" type="number" value="<?= ($clArray?$clArray['sortsequence']:'50'); ?>" style="width:40px" />
			</div>
			<div id="accessDiv" style="clear:both;margin-top:15px;">
				<b><?= $LANG['ACCESS']; ?>:</b>
				<select name="access">
					<option value="private"><?= $LANG['PRIVATE']; ?></option>
					<option value="private-strict" <?= ($clArray && $clArray['access']=='private-strict'?'selected':''); ?>><?= $LANG['PRIVATE_STRICT']; ?></option>
					<option value="public" <?= ($clArray && $clArray['access']=='public'?'selected':''); ?>><?= $LANG['PUBLIC']; ?></option>
				</select>
			</div>
			<div style="clear:both;float:left;margin-top:15px;">
				<?php
				if($clid){
					echo '<button type="submit" name="submitaction" value="submitEdit">' . $LANG['SAVE_EDITS'] . '</button>';
				}
				else{
					echo '<button type="submit" name="submitaction" value="submitAdd">' . $LANG['ADDCHECKLIST'] . '</button>';
				}
				?>
			</div>
			<input type="hidden" name="tabindex" value="1" />
			<input type="hidden" name="uid" value="<?= $SYMB_UID; ?>" />
			<input type="hidden" name="clid" value="<?= $clid; ?>" />
			<input type="hidden" name="pid" value="<?= $pid; ?>" />
		</fieldset>
	</form>
</div>

<div>
	<?php
	if(array_key_exists("userid",$_REQUEST)){
		?>
		<section class="fieldset-like">
			<h2><span><?= $LANG['ASSIGNED_CHECKLISTS']; ?></span></h2>
			<?php
			$userId = $_REQUEST["userid"];
			$listArr = $clManager->getManagementLists($userId);
			if(array_key_exists('cl',$listArr)){
				$clArr = $listArr['cl'];
				?>
				<ul>
					<?php
					foreach($clArr as $kClid => $vName){
						?>
						<li>
							<a href="../checklists/checklist.php?clid=<?= $kClid ?>&emode=0">
								<?= Sanitize::outString($vName) ?>
							</a>
							<a href="../checklists/checklistadmin.php?clid=<?= $kClid ?>&emode=1">
								<img src="../images/edit.png" style="width:1em;border:0px;" title="<?= $LANG['EDITCHECKLIST'] ?>" />
							</a>
						</li>
						<?php
					}
					?>
				</ul>
				<?php
			}
			else{
				?>
				<div style="margin:10px;">
					<div><?= $LANG['NO_CHECKLISTS']; ?></div>
					<div class="top-breathing-room-rel">
						<a href="#" onclick="toggle('checklistDiv')"><?= $LANG['CLICK_TO_CREATE'] ?></a>
					</div>
				</div>
				<?php
			}
			?>
		</section>

		<section class="fieldset-like">
			<h2><span><?= $LANG['PROJ_ADMIN']; ?></span></h2>
			<?php
			if(array_key_exists('proj',$listArr)){
				$projArr = $listArr['proj'];
				?>
				<ul>
				<?php
				foreach($projArr as $pid => $projName){
					?>
					<li>
						<a href="../projects/index.php?pid=<?= $pid ?>&emode=0">
							<?= Sanitize::outString($projName) ?>
						</a>
						<a href="../projects/index.php?pid=<?= $pid ?>&emode=1">
							<img src="../images/edit.png" style="width:1em;border:0px;" title="<?= $LANG['EDIT_PROJECT'] ?>" />
						</a>
					</li>
					<?php
				}
				?>
				</ul>
				<?php
			}
			else{
				echo '<div style="margin:10px;">' . $LANG['NO_PROJECTS'] . '</div>';
			}
			?>
		</section>
		<?php
	}
	?>
</div>
