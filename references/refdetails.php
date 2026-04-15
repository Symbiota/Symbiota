<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ReferenceManager.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
header("Content-Type: text/html; charset=".$CHARSET);


Language::load([
	'collections/loans/loan_langs',
	'collections/editor/includes/determinationtab',
]);

$refId = array_key_exists('refid',$_REQUEST)?$_REQUEST['refid']:0;
$formSubmit = array_key_exists('formsubmit',$_POST)?$_POST['formsubmit']:'';

$refManager = new ReferenceManager();
$refArr = '';

$statusStr = '';
if($formSubmit){
	if($formSubmit == 'Create Reference'){
		$statusStr = $refManager->createReference($_POST);
		$refId = $refManager->getRefId();
	}
	elseif($formSubmit == 'Edit Reference'){
		if($_POST['refGroup'] == 1){
			$statusStr = $refManager->editBookReference($_POST);
		}
		elseif($_POST['refGroup'] == 2){
			$statusStr = $refManager->editPerReference($_POST);
		}
		else{
			$statusStr = $refManager->editReference($_POST);
		}
	}
	elseif($formSubmit == 'batchAddLink'){
		$result = $refManager->batchAddLink($_POST);

		$statusStr = '';

		if($result['success']){
			$statusStr .= '<div style="color:green;">'.$result['success'].' specimens processed successfully</div>';
		}

		if(!empty($result['missing'])){
			$statusStr .= '<div style="color:red;">Unable to locate following catalog numbers:<br/>'.
				implode('<br/>', $result['missing']).'</div>';
		}

		if(!empty($result['duplicate'])){
			$statusStr .= '<div style="color:orange;">Duplicates skipped:<br/>'.
				implode('<br/>', $result['duplicate']).'</div>';
		}

		if(!empty($result['multiple'])){
			$statusStr .= '<div style="color:orange;">Multiple matches found:<br/>'.
				implode('<br/>', $result['multiple']).'</div>';
		}

		if(!empty($result['errors'])){
			$statusStr .= '<div style="color:red;">Errors:<br/>'.
				implode('<br/>', $result['errors']).'</div>';
		}
	}
	elseif($formSubmit == 'deleteRefLink'){
		$statusStr = $refManager->deleteRefLink(
			$_POST['refid'],
			$_POST['targetid'],
			$_POST['type']
    );
}
}
if(isset($_POST['addauthor']) && $_POST['addauthor'] == '1'){
    $refAuthId = intval($_POST['refauthorid']);
    if($refAuthId){
        $statusStr = $refManager->addAuthor($refId, $refAuthId);
    }
}
if(isset($_POST['addreflink']) && $_POST['addreflink'] == '1'){
    $targetid = intval($_POST['targetid']);
	$type = $_POST['type'];
    if($targetid && $type){
        $statusStr = $refManager->addRefLink($refId,$targetid,$type);
    }
}
$refGroup = 0;
$refRank = 0;
$parentChild = 0;
if($refId){
	$refArr = $refManager->getRefArr($refId);
	$childArr = $refManager->getChildArr($refId);
	$authArr = $refManager->getRefAuthArr($refId);
	$fieldArr = $refManager->getRefTypeFieldArr($refArr["ReferenceTypeId"]);
	$refChecklistArr = $refManager->getRefChecklistArr($refId);
	$refDatasetArr = $refManager->getRefDatasetArr($refId);
	$refCollArr = $refManager->getRefCollArr($refId);
	$refOccArr = $refManager->getRefOccArr($refId);
	$refTaxaArr = $refManager->getRefTaxaArr($refId);
	if($refArr["ReferenceTypeId"] == 3 || $refArr["ReferenceTypeId"] == 4 || $refArr["ReferenceTypeId"] == 6 || $refArr["ReferenceTypeId"] == 27){
		$refGroup = 1;
		$parentChild = 1;
		if($refArr["ReferenceTypeId"] == 4){
			$refRank = 1;
		}
		if($refArr["ReferenceTypeId"] == 3 || $refArr["ReferenceTypeId"] == 6){
			$refRank = 2;
		}
		if($refArr["ReferenceTypeId"] == 27){
			$refRank = 3;
		}
	}
	if($refArr["ReferenceTypeId"] == 2 || $refArr["ReferenceTypeId"] == 7 || $refArr["ReferenceTypeId"] == 8 || $refArr["ReferenceTypeId"] == 30){
		$refGroup = 2;
		$parentChild = 1;
		if($refArr["ReferenceTypeId"] == 2 || $refArr["ReferenceTypeId"] == 7 || $refArr["ReferenceTypeId"] == 8){
			$refRank = 1;
		}
		if($refArr["ReferenceTypeId"] == 30){
			$refRank = 2;
		}
	}
}
else{
	header("Location: index.php");
}

?>

<!DOCTYPE HTML>
<html lang="<?php echo $LANG_TAG ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>">
	<title><?php echo $DEFAULT_TITLE; ?> Reference Management</title>
	<link href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<style type="text/css">
		#tabs a{
			outline-color: transparent;
			font-size: 0.9rem;
			font-weight: normal;
		}
	</style>
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="../js/symb/references.index.js"></script>
	<script type="text/javascript">
		var refid = <?php echo $refId; ?>;
		var parentChild = false;

		<?php
		if($parentChild){
			echo 'parentChild = true;';
		}
		?>
	</script>
	<style type="text/css">
		#sampletable {
			width: 100%;
			table-layout: auto;
		}
		
		#innertext{ max-width: 1400px; }
		.fieldGroupDiv { clear:both; margin-top:2px; height: 25px; }
		.fieldDiv { float:left; margin-left: 10px}
		.displayFieldDiv { margin-bottom: 3px }
		fieldset legend { font-weight: bold; }
		.sample-row td { white-space: break-spaces; }
		.sorting_1 {
		  background-color: #c0c0c0a6 !important;
		}

		.input-group {
		  display: flex;
		  align-items: stretch;
		  width: fit-content;
		}

		.input-addon {
		  padding-left: 0.5em;
		  padding-right: 0.5em;
		  background-color: #eee;
		  border: 1px solid #ccc;
		  border-right: none;
		  display: flex;
		  align-items: center;
		}

		.input-addon.suffix {
		  border-left: none;
		  border-right: 1px solid #ccc;
		}

		#prefix {
			width: 120px;
		}

		#suffix {
			width: 60px;
		}

		.input-addon input {
		  border: none !important;
		  background: transparent;
		  padding: 0;
		  margin: 0;
		  outline: none;
		  font-family: inherit;
		}

		.main-input {
		  border: 1px solid #ccc;
		  width: 350px;
		  outline: none;
		  margin-top: 0;
		}

		.input-group input:focus {
		  outline: 2px solid #88f;
		}

	</style>
</head>
<body>
	<?php
	$displayLeftMenu = (isset($reference_indexMenu)?$reference_indexMenu:false);
	include($SERVER_ROOT.'/includes/header.php');
	if(isset($reference_indexCrumbs)){
		if($reference_indexCrumbs){
			?>
			<div class='navpath'>
				<a href='../index.php'>Home</a> &gt;&gt;
				<?php echo $reference_indexCrumbs; ?>
				<a href='index.php'> <b>Reference Management</b></a>
			</div>
			<?php
		}
	}
	else{
		?>
		<div class='navpath'>
			<a href='../index.php'>Home</a> &gt;&gt;
			<a href='index.php'> <b>Reference Management</b></a>
		</div>
		<?php
	}
	?>
	<!-- This is inner text! -->
	<div role="main" id="innertext">
		<h1 class="page-heading">Reference Management</h1>
		<?php
		if($SYMB_UID){
			if($statusStr){
				?>
				<div style="margin:15px;color:red;">
					<?php echo $statusStr; ?>
				</div>
				<?php
			}
			?>
			<div id="tabs" style="margin:0px;">
				<ul>
					<li><a href="#refdetaildiv">Reference Details</a></li>
					<li><a href="#refoccdiv">Linked Occurrences</a></li>
					<li><a href="#reflinksdiv">Other Linked Resources</a></li>
					<li><a href="#refadmindiv">Admin</a></li>
				</ul>

				<div id="refdetaildiv" style="">
					<div style="width:300px;float:right;">
						<form name='authorform' id='authorform' action='refdetails.php' method='post'>
							<input type="hidden" name="refid" value="<?php echo $refId; ?>">
							<fieldset>
								<legend><b>Authors</b></legend>
								<div>
									<div>
										<b>Add Author By Last Name: </b>
									</div>
									<div>
								<select name="refauthorid" id="refauthorid" style="width:220px;">
									<option value="">Select Author</option>
									<?php
									$allAuthors = $refManager->getAuthList();

									foreach($allAuthors as $id => $authorArr){
										echo '<option value="'.htmlspecialchars($id, ENT_QUOTES).'">'.
											htmlspecialchars($authorArr['authorName'], ENT_QUOTES).
											'</option>';
									}
									?>
								</select>
									<input type="hidden" name="addauthor" value="1">
									<button type="submit">Add</button>
									</div>
								</div>
								<hr />
								<div id="authorlistdiv">
									<?php
									if($authArr){
										echo '<ul>';
										foreach($authArr as $k => $v){
											echo '<li>';
											echo '<a href="authoreditor.php?authid=' . htmlspecialchars($k, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '" target="_blank">' . htmlspecialchars($v, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</a>';
											echo ' <input type="image" style="width:1.3em;margin-left:5px;" src="../images/del.png" onclick="deleteRefAuthor('.$k.');" title="Delete author">';
											echo '</li>';
										}
										echo '</ul>';
									}
									else{
										echo '<div><b>There are currently no authors connected with this reference.</b></div>';
									}
									?>
								</div>
								<button type="button" onclick="window.location.href='authoreditor.php';">
									Manage Authors
								</button>
							</fieldset>
						</form>
					</div>
					<div id="refdetails" style="overflow:auto;">
						<form name="referenceeditform" id="referenceeditform" action="refdetails.php" method="post" onsubmit="return verifyEditRefForm(this.form);">
							<div style="width:400px;">
								<div style="width:200px;padding-top:6px;float:left;">
									<div>
										<b>Reference Type: </b>
									</div>
									<div>
										<select name="ReferenceTypeId" id="ReferenceTypeId" style="width:200px;" onchange="verifyRefTypeChange();">
											<option value="">Select Reference Type</option>
											<option value="">-------------------------------</option>
											<?php
											$typeArr = $refManager->getRefTypeArr();
											foreach($typeArr as $k => $v){
												echo '<option value="'.$k.'" '.($refArr['ReferenceTypeId']==$k?'SELECTED':'').'>'.$v.'</option>';
											}
											?>
										</select>
									</div>
								</div>
								<div style="width:100px;margin-top:25px;float:right;">
									<b>Published: </b><input type="checkbox" id="ispublishedcheck" onchange="updateIspublished(this.form);" value="" <?php echo (!$refArr['ispublished']?'':'checked'); ?> />
								</div>
							</div>
							<?php
							if($fieldArr['Title']){
								?>
								<div style="clear:both;padding-top:6px;float:left;">
									<div>
										<b><?php echo $fieldArr['Title']; ?>: </b>
									</div>
									<div>
										<textarea name="title" id="title" rows="10" style="width:380px;height:40px;resize:vertical;" ><?php echo $refArr['title']; ?></textarea>
									</div>
								</div>
								<?php
							}
							if($fieldArr['Pages']){
								?>
								<div style="clear:both;padding-top:6px;float:left;">
									<div>
										<b><?php echo $fieldArr['Pages']; ?>: </b>
									</div>
									<div>
										<input type="text" name="pages" id="pages" tabindex="100" maxlength="45" style="width:200px;" value="<?php echo $refArr['pages']; ?>" onchange="" title="" />
									</div>
								</div>
								<?php
							}
							if($fieldArr['TypeWork']){
								?>
								<div style="clear:both;padding-top:6px;float:left;">
									<div>
										<b><?php echo $fieldArr['TypeWork']; ?>: </b>
									</div>
									<div>
										<textarea name="typework" id="typework" rows="10" style="width:380px;height:40px;resize:vertical;" ><?php echo $refArr['typework']; ?></textarea>
									</div>
								</div>
								<?php
							}
							if($fieldArr['Section']){
								?>
								<div style="clear:both;padding-top:6px;float:left;">
									<div>
										<b><?php echo $fieldArr['Section']; ?>: </b>
									</div>
									<div>
										<input type="text" name="section" id="section" tabindex="100" maxlength="45" style="width:150px;" value="<?php echo $refArr['section']; ?>" onchange="" title="" />
									</div>
								</div>
								<?php
							}
							if($fieldArr['SecondaryTitle']){
								?>
								<div style="clear:both;padding-top:6px;float:left;">
									<div>
										<b><?php echo $fieldArr['SecondaryTitle']; ?>: </b>
									</div>
									<div>
										<textarea name="secondarytitle" id="secondarytitle" rows="10" onchange="" style="width:380px;height:40px;resize:vertical;" ><?php echo $refArr['secondarytitle']; ?></textarea>
									</div>
								</div>
								<?php
							}
							if($fieldArr['TertiaryTitle']){
								?>
								<div style="clear:both;padding-top:6px;float:left;">
									<div>
										<b><?php echo $fieldArr['TertiaryTitle']; ?>: </b>
									</div>
									<div>
										<textarea name="tertiarytitle" id="tertiarytitle" onchange="" rows="10" style="width:380px;height:40px;resize:vertical;" ><?php echo $refArr['tertiarytitle']; ?></textarea>
									</div>
								</div>
								<?php
							}
							if($fieldArr['AlternativeTitle']){
								?>
								<div style="clear:both;padding-top:6px;float:left;">
									<div>
										<b><?php echo $fieldArr['AlternativeTitle']; ?>: </b>
									</div>
									<div>
										<textarea name="alternativetitle" id="alternativetitle" rows="10" style="width:380px;height:40px;resize:vertical;" ><?php echo $refArr['alternativetitle']; ?></textarea>
									</div>
								</div>
								<?php
							}
							if($fieldArr['ShortTitle']){
								?>
								<div style="clear:both;padding-top:6px;float:left;">
									<div>
										<b><?php echo $fieldArr['ShortTitle']; ?>: </b>
									</div>
									<div>
										<textarea name="shorttitle" id="shorttitle" onchange="" rows="10" style="width:380px;height:40px;resize:vertical;" ><?php echo $refArr['shorttitle']; ?></textarea>
									</div>
								</div>
								<?php
							}
							if($fieldArr['Volume']){
								?>
								<div style="clear:both;padding-top:6px;float:left;">
									<div>
										<b><?php echo $fieldArr['Volume']; ?>: </b>
									</div>
									<div>
										<input type="text" name="volume" id="volume" onchange="" tabindex="100" maxlength="45" style="width:100px;" value="<?php echo $refArr['volume']; ?>" onchange="" title="" />
									</div>
								</div>
								<?php
							}
							if($fieldArr['Number']){
								?>
								<div style="clear:both;padding-top:6px;float:left;">
									<div>
										<b><?php echo $fieldArr['Number']; ?>: </b>
									</div>
									<div>
										<input type="text" name="number" id="number" onchange="" tabindex="100" maxlength="45" style="width:100px;" value="<?php echo $refArr['number']; ?>" onchange="" title="" />
									</div>
								</div>
								<?php
							}
							if($fieldArr['NumberVolumes']){
								?>
								<div style="clear:both;padding-top:6px;float:left;">
									<div>
										<b><?php echo $fieldArr['NumberVolumes']; ?>: </b>
									</div>
									<div>
										<input type="text" name="numbervolumnes" id="numbervolumnes" onchange="" tabindex="100" maxlength="45" style="width:100px;" value="<?php echo $refArr['numbervolumnes']; ?>" onchange="" title="" />
									</div>
								</div>
								<?php
							}
							if($fieldArr['Date']){
								?>
								<div style="clear:both;padding-top:6px;float:left;">
									<div>
										<b><?php echo $fieldArr['Date']; ?>: </b>
									</div>
									<div>
										<input type="text" name="pubdate" id="pubdate" onchange="" tabindex="100" maxlength="45" style="width:150px;" value="<?php echo $refArr['pubdate']; ?>" onchange="" title="" />
									</div>
								</div>
								<?php
							}
							if($fieldArr['Edition']){
								?>
								<div style="clear:both;padding-top:6px;float:left;">
									<div>
										<b><?php echo $fieldArr['Edition']; ?>: </b>
									</div>
									<div>
										<input type="text" name="edition" id="edition" onchange="" tabindex="100" maxlength="45" style="width:150px;" value="<?php echo $refArr['edition']; ?>" onchange="" title="" />
									</div>
								</div>
								<?php
							}
							if($fieldArr['Publisher']){
								?>
								<div style="clear:both;padding-top:6px;float:left;">
									<div>
										<b><?php echo $fieldArr['Publisher']; ?>: </b>
									</div>
									<div>
										<input type="text" name="publisher" id="publisher" onchange="" tabindex="100" maxlength="150" style="width:300px;" value="<?php echo $refArr['publisher']; ?>" onchange="" title="" />
									</div>
								</div>
								<?php
							}
							if($fieldArr['PlacePublished']){
								?>
								<div style="clear:both;padding-top:6px;float:left;">
									<div>
										<b><?php echo $fieldArr['PlacePublished']; ?>: </b>
									</div>
									<div>
										<input type="text" name="placeofpublication" id="placeofpublication" onchange="" tabindex="100" maxlength="45" style="width:300px;" value="<?php echo $refArr['placeofpublication']; ?>" onchange="" title="" />
									</div>
								</div>
								<?php
							}
							if($fieldArr['ISBN_ISSN']){
								?>
								<div style="clear:both;padding-top:6px;float:left;">
									<div>
										<b><?php echo $fieldArr['ISBN_ISSN']; ?>: </b>
									</div>
									<div>
										<input type="text" name="isbn_issn" id="isbn_issn" onchange="" tabindex="100" maxlength="45" style="width:300px;" value="<?php echo $refArr['isbn_issn']; ?>" onchange="" title="" />
									</div>
								</div>
								<?php
							}
							?>
							<div style="clear:both;padding-top:6px;float:left;">
								<div>
									<b>GUID: </b>
								</div>
								<div>
									<input type="text" name="guid" id="guid" tabindex="100" maxlength="45" style="width:350px;" value="<?php echo $refArr['guid']; ?>" onchange="" title="" />
								</div>
							</div>
							<div style="clear:both;padding-top:6px;float:left;">
								<div>
									<b>URL: </b>
								</div>
								<div>
									<textarea name="url" id="url" rows="10" style="width:380px;height:40px;resize:vertical;" ><?php echo $refArr['url']; ?></textarea>
								</div>
							</div>
							<div style="clear:both;padding-top:6px;float:left;">
								<div>
									<b>Notes: </b>
								</div>
								<div>
									<textarea name="notes" id="notes" rows="10" style="width:380px;height:40px;resize:vertical;" ><?php echo $refArr['notes']; ?></textarea>
								</div>
							</div>
							<div style="clear:both;padding-top:8px;float:left;">
								<input name="refid" type="hidden" value="<?php echo $refId; ?>" />
								<input name="parentRefId" id="parentRefId" type="hidden" value="<?php echo $refArr['parentrefid']; ?>" />
								<input name="parentRefId2" id="parentRefId2" type="hidden" value="<?php echo $refArr['parentrefid2']; ?>" />
								<input name="refGroup" id="refGroup" type="hidden" value="<?php echo $refGroup; ?>" />
								<input name="ispublished" id="ispublished" type="hidden" value="<?php echo $refArr['ispublished']; ?>" />
								<div id="dynamicInput"></div>
								<button name="formsubmit" type="submit" value="Edit Reference">Save Edits</button>
							</div>
						</form>
					</div>
				</div>

				<div id='refoccdiv' style="">
					<?php
						echo '<div id="referenceoccurlink">';
						if($refOccArr){
							?>
							<form name="sampleListingForm">
								<fieldset id="samplePanel">
								<legend>Sample Listing</legend>
								<div style="float:left">Records displayed: <?php echo count($refOccArr); ?></div>
								<div>
									<table id="sampletable" class="styledtable">
										<thead>
											<tr>
												<?php
												$headerOutArr = current($refOccArr);
												echo '<th><input name="selectall" type="checkbox" onclick="selectAll(this)" /></th>';
												$headerArr = array('collectionCode' => 'Collection',
															'catalogNumber' =>'Catalog Number','sciname'=>'Scientific Name', 
															'recordedBy'=>'Collector', 'eventDate'=>'Collection Date', 
															'occid'=> 'occid');
												$rowCnt = 1;
												foreach($headerArr as $fieldName => $headerTitle){
													if(array_key_exists($fieldName, $headerOutArr) || $fieldName == 'occid'){
														echo '<th>'.$headerTitle.'</th>';
														$rowCnt++;
													}
												}
												?>
											</tr>
										</thead>
										<tbody>
											<?php
											$tagArr = array();
											foreach($refOccArr as $id => $sampleArr){
												echo '<tr>';

												echo '<td>';
												echo '<input id="scbox-'.$sampleArr['occid'].'" name="scbox[]" type="checkbox" value="'.$sampleArr['occid'].'" />';
												echo '</td>';

												echo '<td>'.$sampleArr['collectionCode'].'</td>';
												echo '<td>'.$sampleArr['catalogNumber'].'</td>';
												echo '<td>'.$sampleArr['sciname'].'</td>';
												echo '<td>'.$sampleArr['recordedBy'].'</td>';
												echo '<td>'.$sampleArr['eventDate'].'</td>';

												echo '<td style="text-align:center">';
												echo '<a href="'.$CLIENT_ROOT.'/collections/individual/index.php?occid='.$sampleArr['occid'].'" target="_blank">'.$sampleArr['occid'].'</a><br><br>';
												echo '<a href="'.$CLIENT_ROOT.'/collections/editor/occurrenceeditor.php?occid='.$sampleArr['occid'].'" target="_blank"><img src="../images/edit.png" style="width:13px" /></a>';
												echo '</td>';

												echo '</tr>';
											}
											?>
										</tbody>
									</table>
								</fieldset>
							</form>
							<?php
						}
						else{
							echo 'There are no occurrences linked with this reference';
						}
						?>
						</div>
						<div id="batchOcc-div">
							<fieldset>
								<legend><?php echo 'Batch add occurrences'; ?></legend>
								<div  class="info-div"><?php echo 'Batch add multiple occurrences by entering a list of catalog numbers on separate lines or delimited by commas.'; ?></div>
								<form name="batchaddform" action="refdetails.php" method="post">
									<div class="field-div">
										<label><?php echo $LANG['CATNUMS']; ?>:</label><br/>
										<textarea name="catalogNumbers" cols="6" style="width:700px"></textarea>
									</div>
									<div class="field-div">
										<label>Target:</label>
										<span class="radio-span"><input name="targetidentifier" type="radio" value="allid" /> <?php echo $LANG['ALL_IDS']; ?></span>
										<span class="radio-span"><input name="targetidentifier" type="radio" value="catnum" checked /> <?php echo $LANG['CATNO']; ?></span>
										<span class="radio-span"><input name="targetidentifier" type="radio" value="other" /> <?php echo $LANG['OTHER_CATNUMS'] . '  (Review multiple match warnings)'; ?></span>
									</div>
									<div class="field-div">
										<input name="refid" type="hidden" value="<?php echo $refId; ?>" />
										<div style="float:left;margin-top:15px;margin-left:15px">
											<button name="formsubmit" type="submit" value="batchAddLink"><?php echo 'Add Occurrences'; ?></button>
										</div>
									</div>
								</form>
							</fieldset>
				</div>
				</div>
				<div id="reflinksdiv" style="">
					<div style="width:100%;">
						<?php
						if($refChecklistArr || $refDatasetArr || $refCollArr || $refTaxaArr){
						?>
							<div style="width:100%;">
								<form name='checklistform' id='checklistform' action='refdetails.php' method='post'>
									<input type="hidden" name="refid" value="<?php echo $refId; ?>">
									<fieldset>
										<legend><b>Checklists</b></legend>
										<div>
											<div>
												<b>Add Checklist By Name: </b>
											</div>
											<div>
										<select name="targetid" id="refchecklistid" style="width:220px;">
											<option value="">Select Checklist</option>
											<?php
											$allChecklists = $refManager->getChecklists();

											foreach($allChecklists as $clid => $checkArr){
												echo '<option value="'.htmlspecialchars($clid, ENT_QUOTES).'">'
													. htmlspecialchars($checkArr['name'], ENT_QUOTES) .
													'</option>';
											}
											
											?>
										</select>
											<input type="hidden" name="addreflink" value="1">
											<input name="refid" type="hidden" value="<?php echo $refId; ?>" />
											<input name="type" type="hidden" value="<?php echo 'checklist'; ?>" />
											<button type="submit">Add</button>
											</div>
										</div>
										<hr />
										<div id="checklistlistdiv">
											<?php
											if($refChecklistArr){
												echo '<ul>';
												foreach($refChecklistArr as $k => $v){
													echo '<li>';
													echo '<a href="../checklists/checklist.php?clid=' . htmlspecialchars($k, ENT_QUOTES) . '">' 
														. htmlspecialchars($v, ENT_QUOTES) . 
														'</a>';													
														echo '<img src="../images/del.png" 
															style="width:14px; cursor:pointer;" 
															onclick="deleteRefLink('.$refId.','.$k.',\'checklist\')" 
															title="Delete link">';
													echo '</li>';
												}
												echo '</ul>';
											}
											else{
												echo '<div><b>There are currently no checklists linked to this reference.</b></div>';
											}
											?>
										</div>
									</fieldset>
								</form>
							</div><br />
							<div style="width:100%;">
								<form name='datasetform' id='datasetform' action='refdetails.php' method='post'>
									<input type="hidden" name="refid" value="<?php echo $refId; ?>">
									<fieldset>
										<legend><b>Datasets</b></legend>
										<div>
											<div>
												<b>Add Dataset By Name: </b>
											</div>
											<div>
										<select name="targetid" id="refdatasetid" style="width:220px;">
											<option value="">Select Dataset</option>
											<?php
											$allDatasets = $refManager->getDatasets();

											foreach($allDatasets as $datasetID => $datasetArr){
												echo '<option value="'.htmlspecialchars($datasetID, ENT_QUOTES).'">'
													. htmlspecialchars($datasetArr['name'], ENT_QUOTES) .
													'</option>';
											}
											
											?>
										</select>
											<input type="hidden" name="addreflink" value="1">
											<input name="refid" type="hidden" value="<?php echo $refId; ?>" />
											<input name="type" type="hidden" value="<?php echo 'dataset'; ?>" />
											<button type="submit">Add</button>
											</div>
										</div>
										<hr />
										<div id="datasetlistdiv">
											<?php
											if($refDatasetArr){
												echo '<ul>';
												foreach($refDatasetArr as $k => $v){
													echo '<li>';
													echo '<a href="../collections/datasets/datasetmanager.php?datasetid=' . htmlspecialchars($k, ENT_QUOTES) . '">' 
														. htmlspecialchars($v, ENT_QUOTES) . 
														'</a>';															
													echo '<img src="../images/del.png" 
															style="width:14px; cursor:pointer;" 
															onclick="deleteRefLink('.$refId.','.$k.',\'dataset\')" 
															title="Delete link">';
													echo '</li>';
												}
												echo '</ul>';
											}
											else{
												echo '<div><b>There are currently no datasets linked to this reference.</b></div>';
											}
											?>
										</div>
									</fieldset>
								</form>
							</div><br />
								<div style="width:100%;">
								<form name='collectionform' id='collectionform' action='refdetails.php' method='post'>
									<input type="hidden" name="refid" value="<?php echo $refId; ?>">
									<fieldset>
										<legend><b>Collections</b></legend>
										<div>
											<div>
												<b>Add Collection By Name: </b>
											</div>
											<div>
										<select name="targetid" id="collID" style="width:220px;">
											<option value="">Select Collection</option>
											<?php
											$allCollections = $refManager->getCollections();

											foreach($allCollections as $collID => $collArr){
												echo '<option value="'.htmlspecialchars($collID, ENT_QUOTES).'">'
													. htmlspecialchars($collArr['collectionName'], ENT_QUOTES) .
													'</option>';
											}
											
											?>
										</select>
											<input type="hidden" name="addreflink" value="1">
											<input name="refid" type="hidden" value="<?php echo $refId; ?>" />
											<input name="type" type="hidden" value="<?php echo 'collection'; ?>" />
											<button type="submit">Add</button>
											</div>
										</div>
										<hr />
										<div id="collectionlistdiv">
											<?php
											if($refCollArr){
												echo '<ul>';
												foreach($refCollArr as $k => $v){
													echo '<li>';
													echo '<a href="../collections/misc/collprofiles.php?collid=' . htmlspecialchars($k, ENT_QUOTES) . '">' 
														. htmlspecialchars($v, ENT_QUOTES) . 
														'</a>';															
													echo '<img src="../images/del.png" 
															style="width:14px; cursor:pointer;" 
															onclick="deleteRefLink('.$refId.', '.$k.', \'collection\')" 
															title="Delete link">';
													echo '</li>';
												}
												echo '</ul>';
											}
											else{
												echo '<div><b>There are currently no collections linked to this reference.</b></div>';
											}
											?>
										</div>
									</fieldset>
								</form>
							</div><br />
						<?php

							echo '<b>Taxa links:</b>';
							echo '<div id="referencetaxalink">';
							if($refTaxaArr){
								echo '<ul>';
								foreach($refTaxaArr as $k => $v){

									echo '<li style="display:flex; align-items:center; gap:6px;">';

									echo '<a href="../taxa/index.php?taxon=' . htmlspecialchars($k, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '" target="_blank" >';
									echo $v;
									echo '</a>';

									echo '<img src="../images/del.png" 
											style="width:14px; cursor:pointer;" 
											onclick="deleteRefLink('.$refId.', '.$k.', \'taxon\')" 
											title="Delete link">';

									echo '</li>';
								}
								echo '</ul>';
							}
							else{
								echo 'There are no taxa linked with this reference';
							}
							echo '</div>';
						}
						else{
							echo '<h2>There are no resources linked with this reference</h2>';
						}
						?>
					</div>
				</div>

				<div id="refadmindiv" style="">
					<form name="delrefform" action="index.php" method="post" onsubmit="return confirm('Are you sure you want to permanently delete this reference?')">
						<fieldset style="width:350px;margin:20px;padding:20px;">
							<legend><b>Delete Reference</b></legend>
							<?php
							if($refChecklistArr || $refDatasetArr ||$refCollArr || $refOccArr || $refTaxaArr){
								echo '<div style="font-weight:bold;margin-bottom:15px;">';
								echo 'Reference cannot be deleted until all linked records are removed.';
								echo '</div>';
							}
							if($childArr){
								echo '<div style="font-weight:bold;margin-bottom:15px;">';
								echo 'Reference is a parent reference and cannot be deleted until all child references are deleted.';
								echo '</div>';
							}
							?>
							<input name="formsubmit" type="submit" value="Delete Reference" <?php if($childArr || $refChecklistArr || $refCollArr || $refDatasetArr || $refOccArr || $refTaxaArr) echo 'DISABLED'; ?> />
							<input name="refid" type="hidden" value="<?php echo $refId; ?>" />
						</fieldset>
					</form>
				</div>
			</div>
			<?php
		}
		else{
			if(!$SYMB_UID){
				echo 'Please <a href="../profile/index.php?refurl=../references/index.php">login</a>';
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
