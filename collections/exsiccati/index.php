<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceExsiccatae.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load('collections/exsiccati/index');

header('Content-Type: text/html; charset='.$CHARSET);

$ometid = array_key_exists('ometid',$_REQUEST) ? Sanitize::int($_REQUEST['ometid']) : 0;
$omenid = array_key_exists('omenid',$_REQUEST) ? Sanitize::int($_REQUEST['omenid']) : 0;
$occidToAdd = array_key_exists('occidtoadd',$_REQUEST) ? Sanitize::int($_REQUEST['occidtoadd']) : 0;
$searchTerm = array_key_exists('searchterm',$_POST) ? $_POST['searchterm'] : '';
$specimenOnly = array_key_exists('specimenonly',$_REQUEST) ? Sanitize::int($_REQUEST['specimenonly']) : 0;
$collId = array_key_exists('collid',$_REQUEST) ? Sanitize::int($_REQUEST['collid']) : 0;
$imagesOnly = array_key_exists('imagesonly',$_REQUEST) ? Sanitize::int($_REQUEST['imagesonly']) : 0;
$sortBy = array_key_exists('sortby',$_REQUEST) ? Sanitize::int($_REQUEST['sortby']) : 0;
$formSubmit = array_key_exists('formsubmit',$_REQUEST) ? $_REQUEST['formsubmit'] : '';

/*
if(!$specimenOnly && !$ometid && !array_key_exists('searchterm', $_POST)){
	//Make specimen only the default action
	$specimenOnly = 1;
}
*/

$statusStr = '';
$isEditor = 0;
if($IS_ADMIN || array_key_exists('CollAdmin',$USER_RIGHTS)){
	$isEditor = 1;
}

$exsManager = new OccurrenceExsiccatae($formSubmit?'write':'readonly');
if($isEditor && $formSubmit){
	if($formSubmit == 'Add Exsiccata Title'){
		$statusStr = $exsManager->addTitle($_POST,$PARAMS_ARR['un']);
	}
	elseif($formSubmit == 'Save'){
		$statusStr = $exsManager->editTitle($_POST,$PARAMS_ARR['un']);
	}
	elseif($formSubmit == 'Delete Exsiccata'){
		$statusStr = $exsManager->deleteTitle($ometid);
		if(!$statusStr) $ometid = 0;
	}
	elseif($formSubmit == 'Merge Exsiccatae'){
		$statusStr = $exsManager->mergeTitles($ometid,$_POST['targetometid']);
		if(!$statusStr) $ometid = $_POST['targetometid'];
	}
	elseif($formSubmit == 'Add New Number'){
		$statusStr = $exsManager->addNumber($_POST);
	}
	elseif($formSubmit == 'Save Edits'){
		$statusStr = $exsManager->editNumber($_POST);
	}
	elseif($formSubmit == 'Delete Number'){
		$statusStr = $exsManager->deleteNumber($omenid);
		$omenid = 0;
	}
	elseif($formSubmit == 'Transfer Number'){
		$statusStr = $exsManager->transferNumber($omenid,trim($_POST['targetometid'], 'k'));
	}
	elseif($formSubmit == 'Add Specimen Link'){
		$statusStr = $exsManager->addOccLink($_POST);
	}
	elseif($formSubmit == 'Save Specimen Link Edit'){
		$exsManager->editOccLink($_POST);
	}
	elseif($formSubmit == 'Delete Link to Specimen'){
		$exsManager->deleteOccLink($omenid,$_POST['occid']);
	}
	elseif($formSubmit == 'Transfer Specimen'){
		$statusStr = $exsManager->transferOccurrence($omenid,$_POST['occid'],trim($_POST['targetometid'],'k'),$_POST['targetexsnumber']);
	}
}
if($formSubmit == 'dlexs' || $formSubmit == 'dlexs_titleOnly'){
	$titleOnly = false;
	if($formSubmit == 'dlexs_titleOnly') $titleOnly = true;
	$exsManager->exportExsiccatiAsCsv($searchTerm, $specimenOnly, $imagesOnly, $collId, $titleOnly);
	exit;
}
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<title><?= $DEFAULT_TITLE ?> Exsiccatae</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<link href="<?= $CSS_BASE_PATH ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script src="../../js/symb/shared.js?ver=130926" type="text/javascript"></script>
	<script type="text/javascript">
		function toggleExsEditDiv(){
			toggle('exseditdiv');
			document.getElementById("numadddiv").style.display = "none";
		}

		function toggleNumAddDiv(){
			toggle('numadddiv');
			document.getElementById("exseditdiv").style.display = "none";
		}

		function toggleNumEditDiv(){
			toggle('numeditdiv');
			document.getElementById("occadddiv").style.display = "none";
		}

		function toggleOccAddDiv(){
			toggle('occadddiv');
			document.getElementById("numeditdiv").style.display = "none";
		}

		function verfifyExsAddForm(f){
			if(f.title.value == ""){
				alert("<?= $LANG['TITLE_CANNOT_EMPTY'] ?>");
				return false;
			}
			return true;
		}

		function verifyExsEditForm(f){
			if(f.title.value == ""){
				alert("<?= $LANG['TITLE_CANNOT_EMPTY'] ?>");
				return false;
			}
			return true;
		}

		function verifyExsMergeForm(f){
			if(!f.targetometid || !f.targetometid.value){
				alert("<?= $LANG['SEL_TARGET_EXS'] ?>");
				return false;
			}
			else{
				return confirm("<?= $LANG['SURE_MERGE_EXS'] ?>");
			}
		}

		function verifyNumAddForm(f){
			if(f.exsnumber.value == ""){
				alert("<?= $LANG['NUM_CANNOT_EMPTY'] ?>");
				return false;
			}
			return true;
		}

		function verifyNumEditForm(f){
			if(f.exsnumber.value == ""){
				alert("<?= $LANG['NUM_CANNOT_EMPTY'] ?>");
				return false;
			}
			return true;
		}

		function verifyNumTransferForm(f){
			if(t.targetometid == ""){
				alert("<?= $LANG['SEL_TARGET_EXS'] ?>");
				return false;
			}
			else{
				return confirm("<?= $LANG['SURE_MERGE_EXS'] ?>");
			}
		}

		function verifyOccAddForm(f){
			if(f.occaddcollid.value == ""){
				alert("<?= $LANG['PLS_SEL_COLL'] ?>");
				return false;
			}
			if(f.identifier.value == "" && (f.recordedby.value == "" || f.recordnumber.value == "")){
				alert("<?= $LANG['CATNUM_COLL_CANNOT_EMPTY'] ?>");
				return false;
			}
			if(f.ranking.value && !isNumeric(f.ranking.value)){
				alert("<?= $LANG['RANKING_MUST_NUM'] ?>");
				return false;
			}
			return true;
		}

		function verifyOccEditForm(f){
			if(f.collid.options[0].selected == true || f.collid.options[1].selected){
				alert("<?= $LANG['PLS_SEL_COLL'] ?>");
				return false;
			}
			if(f.occid.value == ""){
				alert("<?= $LANG['OCCID_CANNOT_EMPTY'] ?>");
				return false;
			}
			return true;
		}

		function verifyOccTransferForm(f){
			if(f.targetometid.value == ""){
				alert("<?= $LANG['PLS_SEL_EXS_TITLE'] ?>");
				return false;
			}
			if(f.targetexsnumber.value == ""){
				alert("<?= $LANG['PLS_SEL_EXS_NUM'] ?>");
				return false;
			}
			return true;
		}

		function specimenOnlyChanged(cbObj){
			var divObj = document.getElementById('qryextradiv');
			var f = cbObj.form;
			if(cbObj.checked == true){
				divObj.style.display = "block";
			}
			else{
				divObj.style.display = "none";
				f.imagesonly.checked = false;
				f.collid.options[0].selected = true;
			}
			f.submit();
		}

		function initiateExsTitleLookup(inputObj){
			//To be used to convert title lookups to jQuery autocomplete functions

		}

		function openIndPU(occId){
			var wWidth = 900;
			if(document.body.offsetWidth) wWidth = document.body.offsetWidth*0.9;
			if(wWidth > 1200) wWidth = 1200;
			newWindow = window.open('../individual/index.php?occid='+occId,'indspec' + occId,'scrollbars=1,toolbar=1,resizable=1,width='+(wWidth)+',height=600,left=20,top=20');
			if(newWindow.opener == null) newWindow.opener = self;
			return false;
		}

		<?php
		$selectLookupArr = array();
		if($ometid || $omenid) $selectLookupArr = $exsManager->getSelectLookupArr();
		if($ometid) unset($selectLookupArr[$ometid]);
		if($omenid){
			//Exsiccata number section can have a large number of ometid select look ups; using javascript makes page more efficient
			$selectValues = '';
			//Added "k" prefix to key so that Chrom would maintain the correct sort order
			foreach($selectLookupArr as $k => $vStr){
				$selectValues .= ',k'.$k.': "'.$vStr.'"';
			}
			?>
			function buildExsSelect(selectObj){
				var selectValues = {<?= substr($selectValues,1) ?>};

				for(key in selectValues) {
					try{
						selectObj.add(new Option(selectValues[key], key), null);
					}
					catch(e){ //IE
						selectObj.add(new Option(selectValues[key], key));
					}
				}
			}
			<?php
		}
		?>
	</script>
	<style type="text/css">
		#option-div { margin: 5px; width: 320px; text-align: left; float: right; min-height: 325px; }
		#option-div fieldset { background-color:#f2f2f2; }
		.field-div { margin: 2px 0px; }
		.exs-div { margin-bottom: 5px }
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = (isset($collections_exsiccati_index)?$collections_exsiccati_index:false);
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class='navpath'>
		<a href="../../index.php"><?= $LANG['HOME'] ?></a> &gt;&gt;
		<?php
		if($ometid || $omenid){
			echo '<a href="index.php"><b>' . $LANG['RET_MAIN_EXS_INDEX'] . '</b></a>';
		}
		else{
			echo '<a href="index.php"><b>' . $LANG['EXS_INDEX'] . '</b></a>';
		}
		?>
	</div>
	<!-- This is inner text! -->
	<div role="main" id="innertext" style="width:95%;">
		<h1 class="page-heading"><?= $LANG['EXS'] ?></h1>
		<?php
		if($statusStr){
			echo '<hr/>';
			echo '<div style="margin:10px;color:' . (strpos($statusStr,'SUCCESS') === false ? 'red' : 'green') . ';">' . Sanitize::outString($statusStr) . '</div>';
			echo '<hr/>';
		}
		if(!$ometid && !$omenid){
			?>
			<div id="option-div">
				<form name="optionform" action="index.php" method="post">
					<fieldset>
					    <legend><b><?= $LANG['OPTIONS'] ?></b></legend>
				    	<div>
				    		<b><?= $LANG['SEARCH'] ?>:</b>
							<input type="text" name="searchterm" value="<?= Sanitize::outString($searchTerm) ?>" size="20" onchange="this.form.submit()" />
						</div>
						<div title="<?= $LANG['INCL_WO_SPECS'] ?>">
							<input type="checkbox" name="specimenonly" value="1" <?= ($specimenOnly ? 'CHECKED' : '') ?> onchange="specimenOnlyChanged(this)" />
							<?= $LANG['DISP_ONLY_W_SPECS'] ?>
						</div>
						<div id="qryextradiv" style="margin-left:15px;display:<?= ($specimenOnly ? 'block' : 'none') ?>;" title="including without linked specimen records">
							<div>
								<?= $LANG['LIMIT_TO'] ?>:
								<select name="collid" style="width:230px;" onchange="this.form.submit()">
									<option value=""><?= $LANG['ALL_COLLS'] ?></option>
									<option value="">-----------------------</option>
									<?php
									$acroArr = $exsManager->getCollArr('all');
									foreach($acroArr as $id => $collTitle){
										echo '<option value="' . $id . '" ' . ($id==$collId ? 'SELECTED' : '') . '>' . $collTitle . '</option>';
									}
									?>
								</select>
							</div>
							<div>
							    <input name='imagesonly' type='checkbox' value='1' <?= ($imagesOnly?"CHECKED":"") ?> onchange="this.form.submit()" />
							    <?= $LANG['DISP_ONLY_W_IMGS'] ?>
							</div>
						</div>
						<div style="margin:5px 0px 0px 5px;">
							<?= $LANG['DISP_SORT_BY'] ?>:<br />
							<input type="radio" name="sortby" value="0" <?= ($sortBy == 0?"CHECKED":"") ?> onchange="this.form.submit()"> <?= $LANG['TITLE'] ?>
							<input type="radio" name="sortby" value="1" <?= ($sortBy == 1?"CHECKED":"") ?> onchange="this.form.submit()"> <?= $LANG['ABB'] ?>
						</div>
						<div style="float:left">
							<button class="icon-button" name="formsubmit" type="submit" value="rebuildList"><?= $LANG['REBUILD_LIST'] ?></button>
						</div>
						<div style="float:right">
							<div>
								<span title="Exsiccata download: titles only">
									<button class="icon-button" name="formsubmit" type="submit" value="dlexs_titleOnly">
										<svg style="width:1.2em;height:1.2em;margin-right:0.3em" " xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24">
											<path d="M480-320 280-520l56-58 104 104v-326h80v326l104-104 56 58-200 200ZM240-160q-33 0-56.5-23.5T160-240v-120h80v120h480v-120h80v120q0 33-23.5 56.5T720-160H240Z" />
										</svg>
										<?= $LANG['TITLES'] ?>
									</button>
								</span>
								<span title="Exsiccata download: with numbers and occurrences">
									<button class="icon-button" name="formsubmit" type="submit" value="dlexs">
										<svg style="width:1.2em;height:1.2em;margin-right:0.3em" " xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24">
											<path d="M480-320 280-520l56-58 104 104v-326h80v326l104-104 56 58-200 200ZM240-160q-33 0-56.5-23.5T160-240v-120h80v120h480v-120h80v120q0 33-23.5 56.5T720-160H240Z" />
										</svg>
										<?= $LANG['OCCS'] ?>
									</button>
								</span>
							</div>
						</div>
					</fieldset>
				</form>
			</div>
			<div style="font-weight:bold;font-size:120%;"><?= $LANG['EXS_TITLES'] ?></div>
			<?php
			if($isEditor){
				?>
				<div style="cursor:pointer;float:right;" onclick="toggle('exsadddiv');" title="<?= $LANG['EDIT_EXS_NUM'] ?>">
					<img style="border:0px;" src="../../images/add.png" style="width:1.3em" />
				</div>
				<div id="exsadddiv" style="display:none;">
					<form name="exsaddform" action="index.php" method="post" onsubmit="return verfifyExsAddForm(this)">
						<fieldset style="margin:10px;padding:15px;">
							<legend><b><?= $LANG['ADD_NEW_EXS'] ?></b></legend>
							<div class="field-div">
								<?= $LANG['TITLE'] ?>:<br/><input name="title" type="text" value="" style="width:90%;" />
							</div>
							<div class="field-div">
								<?= $LANG['ABBR'] ?>:<br/><input name="abbreviation" type="text" value="" style="width:480px;" />
							</div>
							<div class="field-div">
								<?= $LANG['EDITOR'] ?>:<br/><input name="editor" type="text" value="" style="width:300px;" />
							</div>
							<div class="field-div">
								<?= $LANG['NUM_RANGE'] ?>:<br/><input name="exsrange" type="text" value="" />
							</div>
							<div class="field-div">
								<?= $LANG['DATE_RANGE'] ?>:<br/>
								<input name="startdate" type="text" value="" /> -
								<input name="enddate" type="text" value="" />
							</div>
							<div class="field-div">
								<?= $LANG['SOURCE'] ?>:<br/><input name="source" type="text" value="" style="width:480px;" />
							</div>
							<div class="field-div">
								<?= $LANG['SOURCE_ID_INDEXS'] ?>:<br/><input name="sourceidentifier" type="text" value="" style="width:90%;" />
							</div>
							<div class="field-div">
								<?= $LANG['NOTES'] ?>:<br/><input name="notes" type="text" value="" style="width:90%" />
							</div>
							<div style="margin:10px;">
								<button name="formsubmit" type="submit" value="Add Exsiccata Title" ><?= $LANG['ADD_EXS_TITLE'] ?></button>
							</div>
						</fieldset>
					</form>
				</div>
				<?php
			}
			?>
			<ul>
				<?php
				$titleArr = $exsManager->getTitleArr($searchTerm, $specimenOnly, $imagesOnly, $collId, $sortBy);
				if($titleArr){
					foreach($titleArr as $k => $tArr){
						?>
						<li>
							<?php
							echo '<div class="exs-div">';
							echo '<div class="exstitle-div"><a href="index.php?ometid=' . $k . '&specimenonly=' . $specimenOnly . '&imagesonly=' . $imagesOnly . '&collid=' . $collId . '&sortBy=' . $sortBy . '">';
							echo $tArr['title'];
							echo '</a></div>';
							$extra = '';
							if($tArr['editor']) $extra  = $tArr['editor'];
							if($tArr['exsrange']) $extra .= ' [' . $tArr['exsrange'] . ']';
							if($extra) echo '<div class="exseditor-div" style="margin-left:15px;">' . $extra . '</div>';
							echo '</div>';
							?>
						</li>
						<?php
					}
				}
				else echo '<div style="margin:20px;font-size:120%;">' . $LANG['NO_EXS_MATCHING'] . '</div>';
				?>
			</ul>
			<?php
		}
		elseif($ometid){
			if($exsArr = $exsManager->getTitleObj($ometid)){
				?>
				<div>
					<?php
					if($isEditor){
						?>
						<div style="float:right;">
							<span style="cursor:pointer;" onclick="toggleExsEditDiv('exseditdiv');" title="<?= $LANG['EDIT_EXS'] ?>">
								<img style="width:1.5em;border:0px;" src="../../images/edit.png" />
							</span>
							<span style="cursor:pointer;" onclick="toggleNumAddDiv('numadddiv');" title="<?= $LANG['ADD_EXS_NUM'] ?>">
								<img style="width:1.5em;border:0px;" src="../../images/add.png" />
							</span>
						</div>
						<?php
					}
					echo '<div style="font-weight:bold;font-size:120%;">'.$exsArr['title'].'</div>';
					if(isset($exsArr['sourceidentifier'])){
						if(preg_match('/^http.+IndExs.+={1}(\d+)$/', $exsArr['sourceidentifier'], $m)) echo ' (<a href="'.$exsArr['sourceidentifier'].'" target="_blank">IndExs #'.$m[1].'</a>)';
					}
					if($exsArr['abbreviation']) echo '<div>Abbreviation: ' . $exsArr['abbreviation'] . '</div>';
					if($exsArr['editor']) echo '<div>Editor(s): ' . $exsArr['editor'] . '</div>';
					if($exsArr['exsrange']) echo '<div>Range: ' . $exsArr['exsrange'] . '</div>';
					if($exsArr['notes']) echo '<div>Notes: ' . $exsArr['notes'] . '</div>';
					?>
				</div>
				<div id="exseditdiv" style="display:none;">
					<form name="exseditform" action="index.php" method="post" onsubmit="return verifyExsEditForm(this);">
						<fieldset style="margin:10px;padding:15px;">
							<legend><b>Edit Title</b></legend>
							<div class="field-div">
								<?= $LANG['TITLE'] ?>:<br/><input name="title" type="text" value="<?= $exsArr['title'] ?>" style="width:90%;" />
							</div>
							<div class="field-div">
								<?= $LANG['ABBR'] ?>:<br/><input name="abbreviation" type="text" value="<?= $exsArr['abbreviation'] ?>" style="width:500px;" />
							</div>
							<div class="field-div">
								<?= $LANG['EDITOR'] ?>:<br/><input name="editor" type="text" value="<?= $exsArr['editor'] ?>" style="width:300px;" />
							</div>
							<div class="field-div">
								<?= $LANG['NUM_RANGE'] ?>:<br/><input name="exsrange" type="text" value="<?= $exsArr['exsrange'] ?>" />
							</div>
							<div class="field-div">
								<?= $LANG['DATE_RANGE'] ?>:<br/>
								<input name="startdate" type="text" value="<?= $exsArr['startdate'] ?>" /> -
								<input name="enddate" type="text" value="<?= $exsArr['enddate'] ?>" />
							</div>
							<div class="field-div">
								<?= $LANG['SOURCE'] ?>:<br/><input name="source" type="text" value="<?= $exsArr['source'] ?>" style="width:480px;" />
							</div>
							<div class="field-div">
								<?= $LANG['SOURCE_ID_INDEXS'] ?>:<br/><input name="sourceidentifier" type="text" value="<?= $exsArr['sourceidentifier'] ?>" style="width:90%" />
							</div>
							<div class="field-div">
								<?= $LANG['NOTES'] ?>:<br/><input name="notes" type="text" value="<?= $exsArr['notes'] ?>" style="width:90%" />
							</div>
							<div style="margin:10px;">
								<input name="ometid" type="hidden" value="<?= $ometid ?>" />
								<button name="formsubmit" type="submit" value="Save" ><?= $LANG['SAVE'] ?></button>
							</div>
						</fieldset>
					</form>
					<form name="exdeleteform" action="index.php" method="post" onsubmit="return confirm('<?= $LANG['SURE_DELETE_EXS'] ?>');">
						<fieldset style="margin:10px;padding:15px;">
							<legend><b><?= $LANG['DEL_EXS'] ?></b></legend>
							<div style="margin:10px;">
								<input name="ometid" type="hidden" value="<?= $ometid ?>" />
								<button name="formsubmit" type="submit" value="Delete Exsiccata" ><?= $LANG['DEL_EXS'] ?></button>
							</div>
						</fieldset>
					</form>
					<form name="exmergeform" action="index.php" method="post" onsubmit="return verifyExsMergeForm(this);">
						<fieldset style="margin:10px;padding:15px;">
							<legend><b><?= $LANG['MERGE_EXS'] ?></b></legend>
							<div style="margin:10px;">
								<?= $LANG['TARGET_EXS'] ?>:<br/>
								<select name="targetometid" style="max-width:90%;">
									<option value="">-------------------------------</option>
									<?php
									foreach($selectLookupArr as $titleId => $titleStr){
										echo '<option value="' . $titleId . '">' . $titleStr . '</option>';
									}
									?>
								</select>
							</div>
							<div style="margin:10px;">
								<input name="ometid" type="hidden" value="<?= $ometid ?>" />
								<button name="formsubmit" type="submit" value="Merge Exsiccatae" ><?= $LANG['MERGE_EXS'] ?></button>
							</div>
						</fieldset>
					</form>
				</div>
				<hr/>
				<div id="numadddiv" style="display:none;">
					<form name="numaddform" action="index.php" method="post" onsubmit="return verifyNumAddForm(this);">
						<fieldset style="margin:10px;padding:15px;">
							<legend><b><?= $LANG['ADD_EXS_NUM'] ?></b></legend>
							<div style="margin:2px;">
								<?= $LANG['EXS_NUM'] ?>: <input name="exsnumber" type="text" />
							</div>
							<div style="margin:2px;">
								<?= $LANG['NOTES'] ?>: <input name="notes" type="text" style="width:90%" />
							</div>
							<div style="margin:10px;">
								<input name="ometid" type="hidden" value="<?= $ometid ?>" />
								<button name="formsubmit" type="submit" value="Add New Number" ><?= $LANG['ADD_NEW_NUM'] ?></button>
							</div>
						</fieldset>
					</form>
				</div>
				<div style="margin-left:10px;">
					<ul>
						<?php
						$exsNumArr = $exsManager->getExsNumberArr($ometid,$specimenOnly,$imagesOnly,$collId);
						if($exsNumArr){
							foreach($exsNumArr as $k => $numArr){
								?>
								<li>
									<?php
									echo '<div><a href="index.php?omenid=' . $k . '">';
									echo '#' . $numArr['number'];
									if($numArr['sciname']) echo ' - <i>' . $numArr['sciname'] . '</i>';
									if($numArr['occurstr']) echo ', ' . $numArr['occurstr'];
									echo '</a></div>';
									if($numArr['notes']) echo '<div style="margin-left:15px;">' . $numArr['notes'] . '</div>';
									?>
								</li>
								<?php
							}
						}
						else{
							echo '<div style="font-weight:bold;font-size:110%;">';
							echo $LANG['NO_EXS_NUMS'] . ' ';
							echo '</div>';
						}
						?>
					</ul>
				</div>
				<?php
			}
			else{
				echo '<div style="font-weight:bold;font-size:110%;">';
				echo $LANG['UNABLE_LOCATE_REC'];
				echo '</div>';
			}
		}
		elseif($omenid){
			if($mdArr = $exsManager->getExsNumberObj($omenid)){
				if($isEditor){
					?>
					<div style="float:right;">
						<span style="cursor:pointer;" onclick="toggleNumEditDiv('numeditdiv');" title="<?= $LANG['EDIT_EXS_NUM'] ?>">
							<img style="width:1.5em;border:0px;" src="../../images/edit.png"/>
						</span>
						<span style="cursor:pointer;" onclick="toggleOccAddDiv('occadddiv');" title="<?= $LANG['ADD_OCC_TO_EXS_NUM'] ?>">
							<img style="width:1.5em;border:0px;" src="../../images/add.png" />
						</span>
					</div>
					<?php
				}
				?>
				<div style="font-weight:bold;font-size:120%;">
					<?php
					echo '<a href="index.php?ometid=' . $mdArr['ometid'] . '">' . $mdArr['title'] . '</a> #' . $mdArr['exsnumber'];
					?>
				</div>
				<div style="margin-left:15px;">
					<?php
					echo $mdArr['abbreviation'] . '</br>';
					echo $mdArr['editor'];
					if($mdArr['exsrange']) echo ' [' . $mdArr['exsrange'] . ']';
					if($mdArr['notes']) echo '</br>' . $mdArr['notes'];
					if(isset($mdArr['sourceidentifier'])){
						if(preg_match('/^http.+IndExs.+={1}(\d+)$/', $mdArr['sourceidentifier'], $m)){
							echo '<br/><a href="' . $mdArr['sourceidentifier'] . '" target="_blank">IndExs #' . $m[1] . '</a>';
						}
					}
					?>
				</div>
				<div id="numeditdiv" style="display:none;">
					<form name="numeditform" action="index.php" method="post" onsubmit="return verifyNumEditForm(this)">
						<fieldset style="margin:10px;padding:15px;">
							<legend><b><?= $LANG['EDIT_EXS_NUM'] ?></b></legend>
							<div style="margin:2px;">
								<?= $LANG['NUMBER'] ?>: <input name="exsnumber" type="text" value="<?= $mdArr['exsnumber'] ?>" />
							</div>
							<div style="margin:2px;">
								<?= $LANG['NOTES'] ?>Notes: <input name="notes" type="text" value="<?= $mdArr['notes'] ?>" style="width:90%;" />
							</div>
							<div style="margin:10px;">
								<input name="omenid" type="hidden" value="<?= $omenid ?>" />
								<button name="formsubmit" type="submit" value="Save Edits" ><?= $LANG['SAVE_EDITS'] ?></button>
							</div>
						</fieldset>
					</form>
					<form name="numdelform" action="index.php" method="post" onsubmit="return confirm('<?= $LANG['SURE_DEL_EXS_NUM'] ?>')">
						<fieldset style="margin:10px;padding:15px;">
							<legend><b><?= $LANG['DEL_EXS_NUM'] ?></b></legend>
							<div style="margin:10px;">
								<input name="omenid" type="hidden" value="<?= $omenid ?>" />
								<input name="ometid" type="hidden" value="<?= $mdArr['ometid'] ?>" />
								<button name="formsubmit" type="submit" value="Delete Number" ><?= $LANG['DEL_NUM'] ?></button>
							</div>
						</fieldset>
					</form>
					<form name="numtransferform" action="index.php" method="post" onsubmit="return verifyNumTransferForm(this);">
						<fieldset style="margin:10px;padding:15px;">
							<legend><b><?= $LANG['TRANSFER_EXS_NUM'] ?></b></legend>
							<div style="margin:10px;">
								<?= $LANG['TARGET_EXS'] ?><br/>
								<select name="targetometid" style="max-width:90%;" onfocus="buildExsSelect(this)">
									<option value="">-------------------------------</option>
								</select>
							</div>
							<div style="margin:10px;">
								<input name="omenid" type="hidden" value="<?= $omenid ?>" />
								<input name="ometid" type="hidden" value="<?= $mdArr['ometid'] ?>" />
								<button name="formsubmit" type="submit" value="Transfer Number" ><?= $LANG['TRANSFER_NUM'] ?></button>
							</div>
						</fieldset>
					</form>
				</div>
				<div id="occadddiv" style="display:<?= ($occidToAdd?'block':'none') ?>;">
					<form name="occaddform" action="index.php" method="post" onsubmit="return verifyOccAddForm(this)">
						<fieldset style="margin:10px;padding:15px;">
							<legend><b><?= $LANG['ADD_OCC_TO_EXS'] ?></b></legend>
							<div style="margin:2px;">
								<?= $LANG['COLL'] ?>:  <br/>
								<select name="occaddcollid">
									<option value=""><?= $LANG['SEL_COLL'] ?></option>
									<option value="">----------------------</option>
									<?php
									$collArr = $exsManager->getCollArr();
									foreach($collArr as $id => $collName){
										echo '<option value="' . $id . '">' . $collName . '</option>';
									}
									?>
									<option value="occid"><?= $LANG['SYMB_PK_OCCID'] ?></option>
								</select>
							</div>
							<div style="margin:10px 0px;height:40px;">
								<div style="margin:2px;float:left;">
									<?= $LANG['CATNUM'] ?> <br/>
									<input name="identifier" type="text" value="" />
								</div>
								<div style="padding:10px;float:left;">
									<b>- <?= $LANG['OR'] ?> -</b>
								</div>
								<div style="margin:2px;float:left;">
									<?= $LANG['COLLECTOR_LAST'] ?>: <br/>
									<input name="recordedby" type="text" value="" />
								</div>
								<div style="margin:2px;float:left;">
									<?= $LANG['NUMBER'] ?>: <br/>
									<input name="recordnumber" type="text" value="" />
								</div>
							</div>
							<div style="margin:2px;clear:both;">
								<?= $LANG['RANKING'] ?>: <br/>
								<input name="ranking" type="text" value="" />
							</div>
							<div style="margin:2px;">
								<?= $LANG['NOTES'] ?>: <br/>
								<input name="notes" type="text" value="" style="width:500px;" />
							</div>
							<div style="margin:10px;">
								<input name="omenid" type="hidden" value="<?= $omenid ?>" />
								<button name="formsubmit" type="submit" value="Add Specimen Link" ><?= $LANG['ADD_SPEC_LINK'] ?></button>
							</div>
						</fieldset>
					</form>
				</div>
				<hr/>
				<div style="margin:15px 10px 0px 0px;">
					<?php
					$occurArr = $exsManager->getExsOccArr($omenid);
					if($exsOccArr = array_shift($occurArr)){
						?>
						<table style="width:90%;">
							<?php
							foreach($exsOccArr as $k => $occArr){
								?>
								<tr>
									<td>
										<div style="font-weight:bold;">
											<?= $occArr['collname'] ?>
										</div>
										<div style="">
											<div style="">
												<?= $LANG['CATNUM'] ?>: <?= $occArr['catalognumber'] ?>
											</div>
											<?php
											if($occArr['occurrenceid']){
												echo '<div style="float:right;">';
												echo $occArr['occurrenceid'];
												echo '</div>';
											}
											?>
										</div>
										<div style="clear:both;">
											<?php
											echo $occArr['recby'];
											echo ($occArr['recnum']?' #' . $occArr['recnum'] . ' ':' s.n. ');
											echo '<span style="margin-left:70px;">' . $occArr['eventdate'] . '</span> ';
											?>
										</div>
										<div style="clear:both;">
											<?php
											echo '<i>' . $occArr['sciname'] . '</i> ';
											echo $occArr['author'];
											?>
										</div>
										<div>
											<?php
											echo $occArr['country'];
											echo (($occArr['country'] && $occArr['state'])?', ':'') . $occArr['state'];
											echo ($occArr['county'] ? ', ' . $occArr['county'] : '');
											echo ($occArr['locality'] ? ', ' . $occArr['locality'] : '');
											?>
										</div>
										<div>
											<?= $occArr['notes'] ?>
										</div>
										<div>
											<a href="#" onclick="openIndPU(<?= $k ?>)">
												Full Record Details
											</a>
										</div>
									</td>
									<td style="width:100px;">
										<?php
										if(array_key_exists('img',$occArr)){
											$imgArr = array_shift($occArr['img']);
											?>
											<a href="<?= $imgArr['url'] ?>">
												<img src="<?= $imgArr['tnurl'] ?>" style="width:75px;" />
											</a>
											<?php
										}
										if($isEditor){
											?>
											<div style="cursor:pointer;float:right;" onclick="toggle('occeditdiv-<?= $k ?>');" title="<?= $LANG['EDIT_OCC_LINK'] ?>">
												<img style="border:0px;" src="../../images/edit.png"/>
											</div>
											<?php
										}
										?>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<div id="occeditdiv-<?= $k ?>" style="display:none;">
											<form name="occeditform-<?= $k ?>" action="index.php" method="post" onsubmit="return verifyOccEditForm(this)">
												<fieldset style="margin:10px;padding:15px;">
													<legend><b><?= $LANG['EDIT_OCC_LINK'] ?></b></legend>
													<div style="margin:2px;">
														<?= $LANG['RANKING'] ?>: <input name="ranking" type="text" value="<?= $occArr['ranking'] ?>" />
													</div>
													<div style="margin:2px;">
														<?= $LANG['NOTES'] ?>: <input name="notes" type="text" value="<?= $occArr['notes'] ?>" style="width:450px;" />
													</div>
													<div style="margin:10px;">
														<input name="omenid" type="hidden" value="<?= $omenid ?>" />
														<input name="occid" type="hidden" value="<?= $k ?>" />
														<button name="formsubmit" type="submit" value="Save Specimen Link Edit" /><?= $LANG['SAVE_SPEC_LINK_EDIT'] ?></button>
													</div>
												</fieldset>
											</form>
											<form name="occdeleteform-<?= $k ?>" action="index.php" method="post" onsubmit="return confirm('<?= $LANG['SURE_DEL_SPEC_LINK'] ?>')">
												<fieldset style="margin:10px;padding:15px;">
													<legend><b><?= $LANG['DEL_SPEC_LINK'] ?></b></legend>
													<div style="margin:10px;">
														<input name="omenid" type="hidden" value="<?= $omenid ?>" />
														<input name="occid" type="hidden" value="<?= $k ?>" />
														<button name="formsubmit" type="submit" value="Delete Link to Specimen" ><?= $LANG['DEL_SPEC_LINK'] ?></button>
													</div>
												</fieldset>
											</form>
											<form name="occtransferform-<?= $k ?>" action="index.php" method="post" onsubmit="return verifyOccTransferForm(this)">
												<fieldset style="margin:10px;padding:15px;">
													<legend><b><?= $LANG['TRANS_SPEC_LINK'] ?></b></legend>
													<div style="margin:10px;">
														<?= $LANG['TARGET_EXS'] ?><br/>
														<select name="targetometid" style="max-width:90%;" onfocus="buildExsSelect(this)">
															<option value=""><?= $LANG['SEL_TAR_EXS'] ?></option>
															<option value="">-------------------------------</option>
														</select>
													</div>
													<div style="margin:10px;">
														<?= $LANG['TARGET_EXS_NUM'] ?><br/>
														<input name="targetexsnumber" type="text" value="" />
													</div>
													<div style="margin:10px;">
														<input name="omenid" type="hidden" value="<?= $omenid ?>" />
														<input name="occid" type="hidden" value="<?= $k ?>" />
														<button name="formsubmit" type="submit" value="Transfer Specimen" ><?= $LANG['TRANSFER_SPEC'] ?></button>
													</div>
												</fieldset>
											</form>
										</div>
										<div style="margin:10px 0px 10px 0px;">
											<hr/>
										</div>
									</td>
								</tr>
								<?php
							}
							?>
						</table>
						<?php
					}
					else{
						echo '<li>' . $LANG['NO_SPECS_WITH_EX_NUM'] . '</li>';
					}
					?>
				</div>
				<?php
			}
			else{
				echo '<div style="font-weight:bold;font-size:110%;">';
				echo $LANG['UNABLE_LOCATE_REC'];
				echo '</div>';
			}
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
