<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ChecklistAdmin.php');
@include_once($SERVER_ROOT.'/content/lang/checklists/checklistadminmeta.'.$LANG_TAG.'.php');
header('Content-Type: text/html; charset='.$CHARSET);

$clid = array_key_exists('clid',$_REQUEST)?$_REQUEST['clid']:0;
$pid = array_key_exists('pid',$_REQUEST)?$_REQUEST['pid']:0;

//Sanitation
if(!is_numeric($clid)) $clid = 0;
if(!is_numeric($pid)) $pid = 0;

$clManager = new ChecklistAdmin();
$clManager->setClid($clid);

$clArray = $clManager->getMetaData($pid);
$defaultArr = array();
if(isset($clArray['defaultsettings']) && $clArray['defaultsettings']){
	$defaultArr = json_decode($clArray['defaultsettings'], true);
}
?>
<script type="text/javascript" src="../js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
	var f = document.getElementById("checklisteditform");

	if(f.type.value == "excludespp") setExclusionChecklistMode(f);

	tinymce.init({
		selector: "textarea",
		width: "100%",
		height: 300,
		menubar: false,
		plugins: "link,charmap,code,paste",
		toolbar : "bold italic underline cut copy paste outdent indent undo redo subscript superscript removeformat link charmap code",
		default_link_target: "_blank",
		paste_as_text: true
	});

	function validateChecklistForm(f){
		if(f.name.value == ""){
			alert("<?php echo (isset($LANG['NEED_NAME'])?$LANG['NEED_NAME']:'Checklist name field must have a value'); ?>");
			return false;
		}
		if(f.latcentroid.value != ""){
			if(f.longcentroid.value == ""){
				alert("<?php echo (isset($LANG['NEED_LONG'])?$LANG['NEED_LONG']:'If latitude has a value, longitude must also have a value'); ?>");
				return false;
			}
			if(!isNumeric(f.latcentroid.value)){
				alert("<?php echo (isset($LANG['LAT_NUMERIC'])?$LANG['LAT_NUMERIC']:'Latitude must be strictly numeric (decimal format: e.g. 34.2343)'); ?>");
				return false;
			}
			if(Math.abs(f.latcentroid.value) > 90){
				alert("<?php echo (isset($LANG['NO_NINETY'])?$LANG['NO_NINETY']:'Latitude values can not be greater than 90 or less than -90'); ?>");
				return false;
			}
		}
		if(f.longcentroid.value != ""){
			if(f.latcentroid.value == ""){
				alert("<?php echo (isset($LANG['NEED_LAT'])?$LANG['NEED_LAT']:'If longitude has a value, latitude must also have a value'); ?>");
				return false;
			}
			if(!isNumeric(f.longcentroid.value)){
				alert("<?php echo (isset($LANG['LONG_NUMERIC'])?$LANG['LONG_NUMERIC']:'Longitude must be strictly numeric (decimal format: e.g. -112.2343)'); ?>");
				return false;
			}
			if(Math.abs(f.longcentroid.value) > 180){
				alert("<?php echo (isset($LANG['NO_ONE_EIGHTY'])?$LANG['NO_ONE_EIGHTY']:'Longitude values can not be greater than 180 or less than -180'); ?>");
				return false;
			}
		}
		if(!isNumeric(f.pointradiusmeters.value)){
			alert("<?php echo (isset($LANG['NUMERIC_RADIUS'])?$LANG['NUMERIC_RADIUS']:'Point radius must be a numeric value only'); ?>");
			return false;
		}
		if(f.type){
			if(f.type.value == "rarespp" && f.locality.value == ""){
				alert("<?php echo (isset($LANG['NEED_STATE'])?$LANG['NEED_STATE']:'Rare species checklists must have a state value entered into the locality field'); ?>");
				return false;
			}
			else if(f.type.value == "excludespp" && f.excludeparent.value == ""){
				alert("<?php echo (isset($LANG['NEED_PARENT'])?$LANG['NEED_PARENT']:'You need to select a parent checklist to create an Exclude Species Checklist'); ?>");
				return false;
			}
		}
		return true;
	}

	function checklistTypeChanged(f){
		if(f.type.value == "excludespp"){
			setExclusionChecklistMode(f);
		}
		else{
			f.excludeparent.style.display = "none";
			document.getElementById("accessDiv").style.display = "block";
			document.getElementById("authorDiv").style.display = "block";
			document.getElementById("locDiv").style.display = "block";
			document.getElementById("inclusiveClDiv").style.display = "block";
			document.getElementById("geoDiv").style.display = "block";
		}
	}

	function setExclusionChecklistMode(f){
		f.excludeparent.style.display = "inline";
		document.getElementById("accessDiv").style.display = "none";
		document.getElementById("authorDiv").style.display = "none";
		document.getElementById("locDiv").style.display = "none";
		document.getElementById("inclusiveClDiv").style.display = "none";
		document.getElementById("geoDiv").style.display = "none";
		f.activatekey.checked = false;
	}

	function openMappingAid() {
		mapWindow=open("<?php echo $CLIENT_ROOT; ?>/checklists/tools/mappointaid.php?clid=<?php echo $clid; ?>&formname=editclmatadata&latname=latcentroid&longname=longcentroid","mapaid","resizable=0,width=1000,height=800,left=20,top=20");
	    if(mapWindow.opener == null) mapWindow.opener = self;
	}

	function openMappingPolyAid() {
		var latDec = document.getElementById("latdec").value;
		var lngDec = document.getElementById("lngdec").value;
		mapWindow=open("<?php echo $CLIENT_ROOT; ?>/checklists/tools/mappolyaid.php?clid=<?php echo $clid; ?>&formname=editclmatadata&latname=latcentroid&longname=longcentroid&latdef="+latDec+"&lngdef="+lngDec,"mapaid","resizable=0,width=1000,height=800,left=20,top=20");
	    if(mapWindow.opener == null) mapWindow.opener = self;
	}
</script>
<?php
if(!$clid){
	?>
	<div style="float:right;">
		<a href="#" onclick="toggle('checklistDiv')" title="<?php echo (isset($LANG['CREATE_CHECKLIST'])?$LANG['CREATE_CHECKLIST']:'Create a New Checklist'); ?>"><img src="../images/add.png" /></a>
	</div>
	<?php
}
?>
<div id="checklistDiv" style="display:<?php echo ($clid?'block':'none'); ?>;">
	<form id="checklisteditform" action="<?php echo $CLIENT_ROOT; ?>/checklists/checklistadmin.php" method="post" name="editclmatadata" onsubmit="return validateChecklistForm(this)">
		<fieldset style="margin:15px;padding:10px;">
			<legend><b><?php echo ($clid?$LANG['EDITCHECKDET']:$LANG['CREATECHECKDET']); ?></b></legend>
			<div>
				<b><?php echo (isset($LANG['CHECKNAME'])?$LANG['CHECKNAME']:'Checklist Name'); ?></b><br/>
				<input type="text" name="name" style="width:95%" value="<?php echo $clManager->getClName();?>" />
			</div>
			<div id="authorDiv">
				<b><?php echo (isset($LANG['AUTHORS'])?$LANG['AUTHORS']:'Authors');?></b><br/>
				<input type="text" name="authors" style="width:95%" value="<?php echo ($clArray?$clArray["authors"]:''); ?>" />
			</div>
			<div>
				<b><?php echo (isset($LANG['CHECKTYPE'])?$LANG['CHECKTYPE']:'Checklist Type');?></b><br/>
				<?php
				$userClArr = $clManager->getUserChecklistArr();
				?>
				<select name="type" onchange="checklistTypeChanged(this.form)">
					<option value="static"><?php echo (isset($LANG['GENCHECK'])?$LANG['GENCHECK']:'General Checklist');?></option>
					<?php
					if($userClArr){
						?>
						<option value="excludespp" <?php echo ($clArray && $clArray["type"]=='excludespp'?'SELECTED':'') ?>><?php echo (isset($LANG['EXCLUDESPP'])?$LANG['EXCLUDESPP']:'Species Exclusion List'); ?></option>
						<?php
					}
					if(isset($GLOBALS['USER_RIGHTS']['RareSppAdmin']) || $IS_ADMIN){
						echo '<option value="rarespp"'.($clArray && $clArray["type"]=='rarespp'?'SELECTED':'').'>'.(isset($LANG['RARETHREAT'])?$LANG['RARETHREAT']:'Rare, threatened, protected species list').'</option>';
					}
					?>
				</select>
				<?php
				if($userClArr){
					?>
					<select name="excludeparent" style="<?php echo ($clid && isset($clArray['excludeparent'])?'':'display:none'); ?>">
						<option value=""><?php echo (isset($LANG['SELECT_PARENT'])?$LANG['SELECT_PARENT']:'Select a parent checklist'); ?></option>
						<option value="">-------------------------------</option>
						<?php
						foreach($userClArr as $userClid => $userClValue){
							echo '<option value="'.$userClid.'" '.(isset($clArray['excludeparent'])&&$userClid==$clArray['excludeparent']?'SELECTED':'').'>'.$userClValue.'</option>';
						}
						?>
					</select>
					<?php
				}
				?>
			</div>
			<div id="locDiv">
				<b><?php echo (isset($LANG['LOC'])?$LANG['LOC']:'Locality');?></b><br/>
				<input type="text" name="locality" style="width:95%" value="<?php echo ($clArray?$clArray["locality"]:''); ?>" />
			</div>
			<div>
				<b><?php echo (isset($LANG['CITATION'])?$LANG['CITATION']:'Citation');?></b><br/>
				<input type="text" name="publication" style="width:95%" value="<?php echo ($clArray?$clArray["publication"]:''); ?>" />
			</div>
			<div>
				<b><?php echo (isset($LANG['ABSTRACT'])?$LANG['ABSTRACT']:'Abstract');?></b><br/>
				<textarea name="abstract" style="width:95%" rows="6"><?php echo ($clArray?$clArray["abstract"]:''); ?></textarea>
			</div>
			<div>
				<b><?php echo (isset($LANG['NOTES'])?$LANG['NOTES']:'Notes');?></b><br/>
				<input type="text" name="notes" style="width:95%" value="<?php echo ($clArray?$clArray["notes"]:''); ?>" />
			</div>
			<div id="inclusiveClDiv">
				<b><?php echo (isset($LANG['REFERENCE_CHECK'])?$LANG['REFERENCE_CHECK']:'More Inclusive Reference Checklist'); ?>:</b><br/>
				<select name="parentclid">
					<option value=""><?php echo (isset($LANG['NONE'])?$LANG['NONE']:'None Selected'); ?></option>
					<option value="">----------------------------------</option>
					<?php
					$refClArr = $clManager->getReferenceChecklists();
					foreach($refClArr as $id => $name){
						echo '<option value="'.$id.'" '.($clArray && $id==$clArray['parentclid']?'SELECTED':'').'>'.$name.'</option>';
					}
					?>
				</select>
			</div>
			<div id="geoDiv" style="width:100%;margin-top:5px">
				<div style="float:left;">
					<b><?php echo (isset($LANG['LATCENT'])?$LANG['LATCENT']:'Latitude');?></b><br/>
					<input id="latdec" type="text" name="latcentroid" style="width:110px;" value="<?php echo ($clArray?$clArray["latcentroid"]:''); ?>" />
				</div>
				<div style="float:left;margin-left:15px;">
					<b><?php echo (isset($LANG['LONGCENT'])?$LANG['LONGCENT']:'Longitude');?></b><br/>
					<input id="lngdec" type="text" name="longcentroid" style="width:110px;" value="<?php echo ($clArray?$clArray["longcentroid"]:''); ?>" />
				</div>
				<div style="float:left;margin:25px 3px;">
					<a href="#" onclick="openMappingAid();return false;"><img src="../images/world.png" style="width:12px;" /></a>
				</div>
				<div style="float:left;margin-left:15px;">
					<b><?php echo (isset($LANG['REFERENCE_CHECK'])?$LANG['POINTRAD']:'Point Radius (meters)');?></b><br/>
					<input type="text" name="pointradiusmeters" style="width:110px;" value="<?php echo ($clArray?$clArray["pointradiusmeters"]:''); ?>" />
				</div>
			</div>
			<div style="clear:both;margin-top:5px;">
				<fieldset style="width:350px;padding:10px">
					<legend><b><?php echo (isset($LANG['POLYFOOT'])?$LANG['POLYFOOT']:'Polygon Footprint');?></b></legend>
					<span id="polyDefDiv" style="display:<?php echo ($clArray && $clArray["hasfootprintwkt"]?'inline':'none'); ?>;">
						<?php echo (isset($LANG['POLYGON_DEFINED'])?$LANG['POLYGON_DEFINED']:'Polygon footprint defined<br/>Click globe to view/edit'); ?>
					</span>
					<span id="polyNotDefDiv" style="display:<?php echo ($clArray && $clArray["hasfootprintwkt"]?'none':'inline'); ?>;">
						<?php echo (isset($LANG['POLYGON_NOT_DEFINED'])?$LANG['POLYGON_NOT_DEFINED']:'Polygon footprint not defined<br/>Click globe to create polygon');?>
					</span>
					<span style="margin:10px;"><a href="#" onclick="openMappingPolyAid();return false;" title="Create/Edit Polygon"><img src="../images/world.png" style="width:14px;" /></a></span>
					<input type="hidden" id="footprintwkt" name="footprintwkt" value="" />
				</fieldset>
			</div>
			<div style="clear:both;margin-top:5px;">
				<fieldset style="width:300px;">
					<legend><b><?php echo (isset($LANG['DEFAULTDISPLAY'])?$LANG['DEFAULTDISPLAY']:'Default Display Settings');?></b></legend>
					<div>
						<?php
						echo "<input id='dsynonyms' name='dsynonyms' type='checkbox' value='1' ".(isset($defaultArr["dsynonyms"])&&$defaultArr["dsynonyms"]?"checked":"")." /> ".(isset($LANG['DISPLAY_SYNONYMS'])?$LANG['DISPLAY_SYNONYMS']:'Display Synonyms');
						?>
					</div>
					<div>
						<?php
						//Display Common Names: 0 = false, 1 = true
						if($DISPLAY_COMMON_NAMES) echo "<input id='dcommon' name='dcommon' type='checkbox' value='1' ".(($defaultArr&&$defaultArr["dcommon"])?"checked":"")." /> ".$LANG['COMMON'];
						?>
					</div>
					<div>
						<!-- Display as Images: 0 = false, 1 = true  -->
						<input name='dimages' id='dimages' type='checkbox' value='1' <?php echo (($defaultArr&&$defaultArr["dimages"])?"checked":""); ?> onclick="showImagesDefaultChecked(this.form);" />
						<?php echo $LANG['DISPLAYIMG'];?>
					</div>
					<div>
						<!-- Display Details: 0 = false, 1 = true  -->
						<input name='ddetails' id='ddetails' type='checkbox' value='1' <?php echo (($defaultArr&&$defaultArr["ddetails"])?"checked":""); ?> />
						<?php echo $LANG['SHOWDETAILS'];?>
					</div>
					<div>
						<!-- Display as Vouchers: 0 = false, 1 = true  -->
						<input name='dvouchers' id='dvouchers' type='checkbox' value='1' <?php echo (($defaultArr&&$defaultArr["dimages"])?"disabled":(($defaultArr&&$defaultArr["dvouchers"])?"checked":"")); ?>/>
						<?php echo $LANG['NOTESVOUC'];?>
					</div>
					<div>
						<!-- Display Taxon Authors: 0 = false, 1 = true  -->
						<input name='dauthors' id='dauthors' type='checkbox' value='1' <?php echo (($defaultArr&&$defaultArr["dimages"])?"disabled":(($defaultArr&&$defaultArr["dauthors"])?"checked":"")); ?>/>
						<?php echo $LANG['TAXONAUTHOR'];?>
					</div>
					<div>
						<!-- Display Taxa Alphabetically: 0 = false, 1 = true  -->
						<input name='dalpha' id='dalpha' type='checkbox' value='1' <?php echo ($defaultArr&&$defaultArr["dalpha"]?"checked":""); ?> />
						<?php echo $LANG['TAXONABC'];?>
					</div>
					<div>
						<?php
						// Activate Identification key: 0 = false, 1 = true
						$activateKey = $KEY_MOD_IS_ACTIVE;
						if(array_key_exists('activatekey', $defaultArr)) $activateKey = $defaultArr["activatekey"];
						?>
						<input name='activatekey' type='checkbox' value='1' <?php echo ($activateKey?"checked":""); ?> />
						<?php echo (isset($LANG['ACTIVATEKEY'])?$LANG['ACTIVATEKEY']:'Activate Identification Key');?>
					</div>
				</fieldset>
			</div>
			<div id="sortSeqDiv" style="clear:both;margin-top:15px;">
				<b><?php echo (isset($LANG['DEFAULT_SORT'])?$LANG['DEFAULT_SORT']:'Default Sorting Sequence'); ?>:</b>
				<input name="sortsequence" type="text" value="<?php echo ($clArray?$clArray['sortsequence']:'50'); ?>" style="width:40px" />
			</div>
			<div id="accessDiv" style="clear:both;margin-top:15px;">
				<b><?php echo (isset($LANG['ACCESS'])?$LANG['ACCESS']:'Access'); ?>:</b>
				<select name="access">
					<option value="private"><?php echo (isset($LANG['PRIVATE'])?$LANG['PRIVATE']:'Private');?></option>
					<option value="public" <?php echo ($clArray && $clArray["access"]=="public"?"selected":""); ?>><?php echo (isset($LANG['PUBLIC'])?$LANG['PUBLIC']:'Public');?></option>
				</select>
			</div>
			<div style="clear:both;float:left;margin-top:15px;">
				<?php
				if($clid){
					?>
					<input type='submit' name='submit' value='<?php echo (isset($LANG['EDITCHECKLIST'])?$LANG['EDITCHECKLIST']:'Edit Checklist');?>' />
					<input type="hidden" name="submitaction" value="SubmitEdit" />
					<?php
				}
				else{
					?>
					<input type='submit' name='submit' value='<?php echo (isset($LANG['ADDCHECKLIST'])?$LANG['ADDCHECKLIST']:'Add Checklist');?>' />
					<input type="hidden" name="submitaction" value="SubmitAdd" />
					<?php
				}
				?>
			</div>
			<input type="hidden" name="tabindex" value="1" />
			<input type="hidden" name="uid" value="<?php echo $SYMB_UID; ?>" />
			<input type="hidden" name="clid" value="<?php echo $clid; ?>" />
			<input type="hidden" name="pid" value="<?php echo $pid; ?>" />
		</fieldset>
	</form>
</div>

<div>
	<?php
	if(array_key_exists("userid",$_REQUEST)){
		$userId = $_REQUEST["userid"];
		echo '<div style="font-weight:bold;font:bold 14pt;">'.(isset($LANG['ASSIGNED_CHECKLISTS'])?$LANG['ASSIGNED_CHECKLISTS']:'Checklists assigned to your account').'</div>';
		$listArr = $clManager->getManagementLists($userId);
		if(array_key_exists('cl',$listArr)){
			$clArr = $listArr['cl'];
			?>
			<ul>
			<?php
			foreach($clArr as $kClid => $vName){
				?>
				<li>
					<a href="../checklists/checklist.php?clid=<?php echo $kClid; ?>&emode=0">
						<?php echo $vName; ?>
					</a>
					<a href="../checklists/checklistadmin.php?clid=<?php echo $kClid; ?>&emode=1">
						<img src="../images/edit.png" style="width:15px;border:0px;" title="<?php echo (isset($LANG['EDITCHECKLIST'])?$LANG['EDITCHECKLIST']:'Edit Checklist');?>" />
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
				<div><?php echo (isset($LANG['NO_CHECKLISTS'])?$LANG['NO_CHECKLISTS']:'You have no personal checklists');?></div>
				<div style="margin-top:5px">
					<a href="#" onclick="toggle('checklistDiv')"><?php echo (isset($LANG['CLICK_TO_CREATE'])?$LANG['CLICK_TO_CREATE']:'Click here to create a new checklist');?></a>
				</div>
			</div>
			<?php
		}

		echo '<div style="font-weight:bold;font:bold 14pt;margin-top:25px;">'.(isset($LANG['PROJ_ADMIN'])?$LANG['PROJ_ADMIN']:'Inventory Project Administration').'</div>';
		if(array_key_exists('proj',$listArr)){
			$projArr = $listArr['proj'];
			?>
			<ul>
			<?php
			foreach($projArr as $pid => $projName){
				?>
				<li>
					<a href="../projects/index.php?pid=<?php echo $pid; ?>&emode=0">
						<?php echo $projName; ?>
					</a>
					<a href="../projects/index.php?pid=<?php echo $pid; ?>&emode=1">
						<img src="../images/edit.png" style="width:15px;border:0px;" title="<?php echo (isset($LANG['EDIT_PROJECT'])?$LANG['EDIT_PROJECT']:'Edit Project');?>" />
					</a>
				</li>
				<?php
			}
			?>
			</ul>
			<?php
		}
		else{
			echo '<div style="margin:10px;">'.(isset($LANG['NO_PROJECTS'])?$LANG['NO_PROJECTS']:'There are no Projects for which you have administrative permissions').'</div>';
		}
	}
	?>
</div>