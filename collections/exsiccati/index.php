<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceExsiccatae.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/collections/exsiccati/index.' . $LANG_TAG . '.php')) include_once($SERVER_ROOT.'/content/lang/collections/exsiccati/index.' . $LANG_TAG . '.php');
else include_once($SERVER_ROOT . '/content/lang/collections/exsiccati/index.en.php');
header('Content-Type: text/html; charset='.$CHARSET);

$ometid = array_key_exists('ometid',$_REQUEST) ? filter_var($_REQUEST['ometid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$omenid = array_key_exists('omenid',$_REQUEST) ? filter_var($_REQUEST['omenid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$occidToAdd = array_key_exists('occidtoadd',$_REQUEST) ? filter_var($_REQUEST['occidtoadd'], FILTER_SANITIZE_NUMBER_INT) : 0;
$searchTerm = array_key_exists('searchterm',$_POST) ? htmlspecialchars($_POST['searchterm'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) : '';
$specimenOnly = array_key_exists('specimenonly',$_REQUEST) ? filter_var($_REQUEST['specimenonly'], FILTER_SANITIZE_NUMBER_INT) : 0;
$collId = array_key_exists('collid',$_REQUEST) ? filter_var($_REQUEST['collid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$imagesOnly = array_key_exists('imagesonly',$_REQUEST) ? filter_var($_REQUEST['imagesonly'], FILTER_SANITIZE_NUMBER_INT) : 0;
$sortBy = array_key_exists('sortby',$_REQUEST) ? filter_var($_REQUEST['sortby'], FILTER_SANITIZE_NUMBER_INT) : 0;
$formSubmit = array_key_exists('formsubmit',$_REQUEST) ? htmlspecialchars($_REQUEST['formsubmit'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE):'';

//Sanitation
if(!is_numeric($ometid)) $ometid = 0;
if(!is_numeric($omenid)) $omenid = 0;
if(!is_numeric($occidToAdd)) $occidToAdd = 0;
$searchTerm = htmlspecialchars($searchTerm, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
if(!is_numeric($specimenOnly)) $specimenOnly = 0;
if(!is_numeric($collId)) $collId = 0;
if(!is_numeric($imagesOnly)) $imagesOnly = 0;
if(!is_numeric($sortBy)) $sortBy = 0;

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
		$statusStr = $exsManager->transferNumber($omenid,trim($_POST['targetometid'],'k'));
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
$selectLookupArr = array();
if($ometid || $omenid) $selectLookupArr = $exsManager->getSelectLookupArr();
if($ometid) unset($selectLookupArr[$ometid]);
?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Exsiccatae</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<link href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.min.js" type="text/javascript"></script>
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
		if($omenid){
			//Exsiccata number section can have a large number of ometid select look ups; using javascript makes page more efficient
			$selectValues = '';
			//Added "k" prefix to key so that Chrom would maintain the correct sort order
			foreach($selectLookupArr as $k => $vStr){
				$selectValues .= ',k'.$k.': "'.$vStr.'"';
			}
			?>
			function buildExsSelect(selectObj){
				var selectValues = {<?php echo substr($selectValues,1); ?>};

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
		#option-div { margin: 5px; width: 300px; text-align: left; float: right; min-height: 325px; }
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
			echo '<div style="margin:10px;color:' . (strpos($statusStr,'SUCCESS') === false ? 'red' : 'green') . ';">' . htmlspecialchars($statusStr, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</div>';
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
							<input type="text" name="searchterm" value="<?php echo $searchTerm;?>" size="20" onchange="this.form.submit()" />
						</div>
						<div title="<?= $LANG['INCL_WO_SPECS'] ?>">
							<input type="checkbox" name="specimenonly" value="1" <?php echo ($specimenOnly?"CHECKED":"");?> onchange="specimenOnlyChanged(this)" />
							<?= $LANG['DISP_ONLY_W_SPECS'] ?>
						</div>
						<div id="qryextradiv" style="margin-left:15px;display:<?php echo ($specimenOnly?'block':'none'); ?>;" title="including without linked specimen records">
							<div>
								<?= $LANG['LIMIT_TO'] ?>:
								<select name="collid" style="width:230px;" onchange="this.form.submit()">
									<option value=""><?= $LANG['ALL_COLLS'] ?></option>
									<option value="">-----------------------</option>
									<?php
									$acroArr = $exsManager->getCollArr('all');
									foreach($acroArr as $id => $collTitle){
										echo '<option value="' . htmlspecialchars($id, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '" ' . ($id==$collId?'SELECTED':'') . '>' . htmlspecialchars($collTitle, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</option>';
									}
									?>
								</select>
							</div>
							<div>
							    <input name='imagesonly' type='checkbox' value='1' <?php echo ($imagesOnly?"CHECKED":""); ?> onchange="this.form.submit()" />
							    <?= $LANG['DISP_ONLY_W_IMGS'] ?>
							</div>
						</div>
						<div style="margin:5px 0px 0px 5px;">
							<?= $LANG['DISP_SORT_BY'] ?>:<br />
							<input type="radio" name="sortby" value="0" <?php echo ($sortBy == 0?"CHECKED":""); ?> onchange="this.form.submit()"> <?= $LANG['TITLE'] ?>
							<input type="radio" name="sortby" value="1" <?php echo ($sortBy == 1?"CHECKED":""); ?> onchange="this.form.submit()"> <?= $LANG['ABB'] ?>
						</div>
						<div style="margin-top:5px">
							<div>
								<span title="Exsiccata download: titles only"><button name="formsubmit" type="submit" value="dlexs_titleOnly"><img src="../../images/dl.png" style="width:1.2em;margin-right:0.3em" /><?= $LANG['TITLES'] ?></button></span>
								<span title="Exsiccata download: with numbers and occurrences"><button name="formsubmit" type="submit" value="dlexs"><img src="../../images/dl.png" style="width:1.2em;margin-right:0.3em" /><?= $LANG['OCCS'] ?></button></span>
							</div>
						</div>
						<div>
							<button name="formsubmit" type="submit" value="rebuildList"><?= $LANG['REBUILD_LIST'] ?></button>
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
						<fieldset style="margin:10px;padding:15px;background-color:#B0C4DE;">
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
							echo '<div class="exstitle-div"><a href="index.php?ometid=' . htmlspecialchars($k, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '&specimenonly=' . htmlspecialchars($specimenOnly, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '&imagesonly=' . htmlspecialchars($imagesOnly, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '&collid=' . htmlspecialchars($collId, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '&sortBy=' . htmlspecialchars($sortBy, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '">';
							echo $tArr['title'];
							echo '</a></div>';
							$extra = '';
							if($tArr['editor']) $extra  = $tArr['editor'];
							if($tArr['exsrange']) $extra .= ' [' . $tArr['exsrange'] . ']';
							if($extra) echo '<div class="exseditor-div" style="margin-left:15px;">' . htmlspecialchars($extra, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</div>';
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
					if($exsArr['abbreviation']) echo '<div>Abbreviation: ' . htmlspecialchars($exsArr['abbreviation'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</div>';
					if($exsArr['editor']) echo '<div>Editor(s): ' . htmlspecialchars($exsArr['editor'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</div>';
					if($exsArr['exsrange']) echo '<div>Range: ' . htmlspecialchars($exsArr['exsrange'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</div>';
					if($exsArr['notes']) echo '<div>Notes: ' . htmlspecialchars($exsArr['notes'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</div>';
					?>
				</div>
				<div id="exseditdiv" style="display:none;">
					<form name="exseditform" action="index.php" method="post" onsubmit="return verifyExsEditForm(this);">
						<fieldset style="margin:10px;padding:15px;background-color:#B0C4DE;">
							<legend><b>Edit Title</b></legend>
							<div class="field-div">
								<?= $LANG['TITLE'] ?>:<br/><input name="title" type="text" value="<?php echo $exsArr['title']; ?>" style="width:90%;" />
							</div>
							<div class="field-div">
								<?= $LANG['ABBR'] ?>:<br/><input name="abbreviation" type="text" value="<?php echo $exsArr['abbreviation']; ?>" style="width:500px;" />
							</div>
							<div class="field-div">
								<?= $LANG['EDITOR'] ?>:<br/><input name="editor" type="text" value="<?php echo $exsArr['editor']; ?>" style="width:300px;" />
							</div>
							<div class="field-div">
								<?= $LANG['NUM_RANGE'] ?>:<br/><input name="exsrange" type="text" value="<?php echo $exsArr['exsrange']; ?>" />
							</div>
							<div class="field-div">
								<?= $LANG['DATE_RANGE'] ?>:<br/>
								<input name="startdate" type="text" value="<?php echo $exsArr['startdate']; ?>" /> -
								<input name="enddate" type="text" value="<?php echo $exsArr['enddate']; ?>" />
							</div>
							<div class="field-div">
								<?= $LANG['SOURCE'] ?>:<br/><input name="source" type="text" value="<?php echo $exsArr['source']; ?>" style="width:480px;" />
							</div>
							<div class="field-div">
								<?= $LANG['SOURCE_ID_INDEXS'] ?>:<br/><input name="sourceidentifier" type="text" value="<?php echo $exsArr['sourceidentifier']; ?>" style="width:90%" />
							</div>
							<div class="field-div">
								<?= $LANG['NOTES'] ?>:<br/><input name="notes" type="text" value="<?php echo $exsArr['notes']; ?>" style="width:90%" />
							</div>
							<div style="margin:10px;">
								<input name="ometid" type="hidden" value="<?php echo $ometid; ?>" />
								<button name="formsubmit" type="submit" value="Save" ><?= $LANG['SAVE'] ?></button>
							</div>
						</fieldset>
					</form>
					<form name="exdeleteform" action="index.php" method="post" onsubmit="return confirm('<?= $LANG['SURE_DELETE_EXS'] ?>');">
						<fieldset style="margin:10px;padding:15px;background-color:#B0C4DE;">
							<legend><b><?= $LANG['DEL_EXS'] ?></b></legend>
							<div style="margin:10px;">
								<input name="ometid" type="hidden" value="<?php echo $ometid; ?>" />
								<button name="formsubmit" type="submit" value="Delete Exsiccata" ><?= $LANG['DEL_EXS'] ?></button>
							</div>
						</fieldset>
					</form>
					<form name="exmergeform" action="index.php" method="post" onsubmit="return verifyExsMergeForm(this);">
						<fieldset style="margin:10px;padding:15px;background-color:#B0C4DE;">
							<legend><b><?= $LANG['MERGE_EXS'] ?></b></legend>
							<div style="margin:10px;">
								<?= $LANG['TARGET_EXS'] ?>:<br/>
								<select name="targetometid" style="max-width:90%;">
									<option value="">-------------------------------</option>
									<?php
									foreach($selectLookupArr as $titleId => $titleStr){
										echo '<option value="' . htmlspecialchars($titleId, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '">' . htmlspecialchars($titleStr, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</option>';
									}
									?>
								</select>
							</div>
							<div style="margin:10px;">
								<input name="ometid" type="hidden" value="<?php echo $ometid; ?>" />
								<button name="formsubmit" type="submit" value="Merge Exsiccatae" ><?= $LANG['MERGE_EXS'] ?></button>
							</div>
						</fieldset>
					</form>
				</div>
				<hr/>
				<div id="numadddiv" style="display:none;">
					<form name="numaddform" action="index.php" method="post" onsubmit="return verifyNumAddForm(this);">
						<fieldset style="margin:10px;padding:15px;background-color:#B0C4DE;">
							<legend><b><?= $LANG['ADD_EXS_NUM'] ?></b></legend>
							<div style="margin:2px;">
								<?= $LANG['EXS_NUM'] ?>: <input name="exsnumber" type="text" />
							</div>
							<div style="margin:2px;">
								<?= $LANG['NOTES'] ?>: <input name="notes" type="text" style="width:90%" />
							</div>
							<div style="margin:10px;">
								<input name="ometid" type="hidden" value="<?php echo $ometid; ?>" />
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
									echo '<div><a href="index.php?omenid=' . htmlspecialchars($k, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '">';
									echo '#' . htmlspecialchars($numArr['number'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
									if($numArr['sciname']) echo ' - <i>' . htmlspecialchars($numArr['sciname'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</i>';
									if($numArr['occurstr']) echo ', ' . htmlspecialchars($numArr['occurstr'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
									echo '</a></div>';
									if($numArr['notes']) echo '<div style="margin-left:15px;">' . htmlspecialchars($numArr['notes'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</div>';
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
					echo htmlspecialchars($mdArr['abbreviation'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</br>';
					echo htmlspecialchars($mdArr['editor'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
					if($mdArr['exsrange']) echo ' [' . htmlspecialchars($mdArr['exsrange'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . ']';
					if($mdArr['notes']) echo '</br>' . htmlspecialchars($mdArr['notes'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
					if(isset($mdArr['sourceidentifier'])){
						if(preg_match('/^http.+IndExs.+={1}(\d+)$/', $mdArr['sourceidentifier'], $m)) echo '<br/><a href="' . htmlspecialchars($mdArr['sourceidentifier'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '" target="_blank">IndExs #' . htmlspecialchars($m[1], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</a>';
					}
					?>
				</div>
				<div id="numeditdiv" style="display:none;">
					<form name="numeditform" action="index.php" method="post" onsubmit="return verifyNumEditForm(this)">
						<fieldset style="margin:10px;padding:15px;background-color:#B0C4DE;">
							<legend><b><?= $LANG['EDIT_EXS_NUM'] ?></b></legend>
							<div style="margin:2px;">
								<?= $LANG['NUMBER'] ?>: <input name="exsnumber" type="text" value="<?php echo $mdArr['exsnumber']; ?>" />
							</div>
							<div style="margin:2px;">
								<?= $LANG['NOTES'] ?>Notes: <input name="notes" type="text" value="<?php echo $mdArr['notes']; ?>" style="width:90%;" />
							</div>
							<div style="margin:10px;">
								<input name="omenid" type="hidden" value="<?php echo $omenid; ?>" />
								<button name="formsubmit" type="submit" value="Save Edits" ><?= $LANG['SAVE_EDITS'] ?></button>
							</div>
						</fieldset>
					</form>
					<form name="numdelform" action="index.php" method="post" onsubmit="return confirm('<?= $LANG['SURE_DEL_EXS_NUM'] ?>')">
						<fieldset style="margin:10px;padding:15px;background-color:#B0C4DE;">
							<legend><b><?= $LANG['DEL_EXS_NUM'] ?></b></legend>
							<div style="margin:10px;">
								<input name="omenid" type="hidden" value="<?php echo $omenid; ?>" />
								<input name="ometid" type="hidden" value="<?php echo $mdArr['ometid']; ?>" />
								<button name="formsubmit" type="submit" value="Delete Number" ><?= $LANG['DEL_NUM'] ?></button>
							</div>
						</fieldset>
					</form>
					<form name="numtransferform" action="index.php" method="post" onsubmit="return verifyNumTransferForm(this);">
						<fieldset style="margin:10px;padding:15px;background-color:#B0C4DE;">
							<legend><b><?= $LANG['TRANSFER_EXS_NUM'] ?></b></legend>
							<div style="margin:10px;">
								<?= $LANG['TARGET_EXS'] ?><br/>
								<select name="targetometid" style="max-width:90%;" onfocus="buildExsSelect(this)">
									<option value="">-------------------------------</option>
								</select>
							</div>
							<div style="margin:10px;">
								<input name="omenid" type="hidden" value="<?php echo $omenid; ?>" />
								<input name="ometid" type="hidden" value="<?php echo $mdArr['ometid']; ?>" />
								<button name="formsubmit" type="submit" value="Transfer Number" ><?= $LANG['TRANSFER_NUM'] ?></button>
							</div>
						</fieldset>
					</form>
				</div>
				<div id="occadddiv" style="display:<?php echo ($occidToAdd?'block':'none') ?>;">
					<form name="occaddform" action="index.php" method="post" onsubmit="return verifyOccAddForm(this)">
						<fieldset style="margin:10px;padding:15px;background-color:#B0C4DE;">
							<legend><b><?= $LANG['ADD_OCC_TO_EXS'] ?></b></legend>
							<div style="margin:2px;">
								<?= $LANG['COLL'] ?>:  <br/>
								<select name="occaddcollid">
									<option value=""><?= $LANG['SEL_COLL'] ?></option>
									<option value="">----------------------</option>
									<?php
									$collArr = $exsManager->getCollArr();
									foreach($collArr as $id => $collName){
										echo '<option value="' . htmlspecialchars($id, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '">' . htmlspecialchars($collName, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</option>';
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
								<input name="omenid" type="hidden" value="<?php echo $omenid; ?>" />
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
											<?php
											echo htmlspecialchars($occArr['collname'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
											?>
										</div>
										<div style="">
											<div style="">
												<?= $LANG['CATNUM'] ?>: <?php echo htmlspecialchars($occArr['catalognumber'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>
											</div>
											<?php
											if($occArr['occurrenceid']){
												echo '<div style="float:right;">';
												echo htmlspecialchars($occArr['occurrenceid'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
												echo '</div>';
											}
											?>
										</div>
										<div style="clear:both;">
											<?php
											echo htmlspecialchars($occArr['recby'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
											echo ($occArr['recnum']?' #' . htmlspecialchars($occArr['recnum'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . ' ':' s.n. ');
											echo '<span style="margin-left:70px;">' . htmlspecialchars($occArr['eventdate'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</span> ';
											?>
										</div>
										<div style="clear:both;">
											<?php
											echo '<i>' . htmlspecialchars($occArr['sciname'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</i> ';
											echo htmlspecialchars($occArr['author'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
											?>
										</div>
										<div>
											<?php
											echo htmlspecialchars($occArr['country'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
											echo (($occArr['country'] && $occArr['state'])?', ':'') . htmlspecialchars($occArr['state'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
											echo ($occArr['county'] ? ', ' . htmlspecialchars($occArr['county'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) : '');
											echo ($occArr['locality'] ? ', ' . htmlspecialchars($occArr['locality'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) : '');
											?>
										</div>
										<div>
											<?php echo htmlspecialchars(($occArr['notes']?$occArr['notes']:''), ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>
										</div>
										<div>
											<a href="#" onclick="openIndPU(<?php echo htmlspecialchars($k, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>)">
												Full Record Details
											</a>
										</div>
									</td>
									<td style="width:100px;">
										<?php
										if(array_key_exists('img',$occArr)){
											$imgArr = array_shift($occArr['img']);
											?>
											<a href="<?php echo htmlspecialchars($imgArr['url'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>">
												<img src="<?php echo htmlspecialchars($imgArr['tnurl'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>" style="width:75px;" />
											</a>
											<?php
										}
										if($isEditor){
											?>
											<div style="cursor:pointer;float:right;" onclick="toggle('occeditdiv-<?php echo $k; ?>');" title="<?= $LANG['EDIT_OCC_LINK'] ?>">
												<img style="border:0px;" src="../../images/edit.png"/>
											</div>
											<?php
										}
										?>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<div id="occeditdiv-<?php echo $k; ?>" style="display:none;">
											<form name="occeditform-<?php echo $k; ?>" action="index.php" method="post" onsubmit="return verifyOccEditForm(this)">
												<fieldset style="margin:10px;padding:15px;background-color:#B0C4DE;">
													<legend><b><?= $LANG['EDIT_OCC_LINK'] ?></b></legend>
													<div style="margin:2px;">
														<?= $LANG['RANKING'] ?>: <input name="ranking" type="text" value="<?php echo htmlspecialchars($occArr['ranking'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>" />
													</div>
													<div style="margin:2px;">
														<?= $LANG['NOTES'] ?>: <input name="notes" type="text" value="<?php echo htmlspecialchars($occArr['notes'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>" style="width:450px;" />
													</div>
													<div style="margin:10px;">
														<input name="omenid" type="hidden" value="<?php echo $omenid; ?>" />
														<input name="occid" type="hidden" value="<?php echo $k; ?>" />
														<button name="formsubmit" type="submit" value="Save Specimen Link Edit" /><?= $LANG['SAVE_SPEC_LINK_EDIT'] ?></button>
													</div>
												</fieldset>
											</form>
											<form name="occdeleteform-<?php echo $k; ?>" action="index.php" method="post" onsubmit="return confirm('<?= $LANG['SURE_DEL_SPEC_LINK'] ?>')">
												<fieldset style="margin:10px;padding:15px;background-color:#B0C4DE;">
													<legend><b><?= $LANG['DEL_SPEC_LINK'] ?></b></legend>
													<div style="margin:10px;">
														<input name="omenid" type="hidden" value="<?php echo $omenid; ?>" />
														<input name="occid" type="hidden" value="<?php echo $k; ?>" />
														<button name="formsubmit" type="submit" value="Delete Link to Specimen" ><?= $LANG['DEL_SPEC_LINK'] ?></button>
													</div>
												</fieldset>
											</form>
											<form name="occtransferform-<?php echo $k; ?>" action="index.php" method="post" onsubmit="return verifyOccTransferForm(this)">
												<fieldset style="margin:10px;padding:15px;background-color:#B0C4DE;">
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
														<input name="omenid" type="hidden" value="<?php echo $omenid; ?>" />
														<input name="occid" type="hidden" value="<?php echo $k; ?>" />
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
