<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT . '/classes/ReferenceManager.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

header("Content-Type: text/html; charset=".$CHARSET);


Language::load([
	'collections/loans/loan_langs',
	'collections/editor/includes/determinationtab',
	'collections/search/index'
]);

$refId = array_key_exists('refid', $_REQUEST) ? Sanitize::int($_REQUEST['refid']) : 0;
$formSubmit = array_key_exists('formsubmit', $_POST) ? $_POST['formsubmit'] : '';

$refManager = new ReferenceManager();
$refArr = '';

$statusStr = '';
if($formSubmit){
	if($formSubmit == 'Create Reference'){
		$statusStr = $refManager->createReference($_POST);
		$newRefId = $refManager->getRefId();

		if($newRefId){
			header("Location: refdetails.php?refid=".$newRefId);
			exit;
		}
	}
	elseif($formSubmit == 'Edit Reference'){
			$statusStr = $refManager->editReference($_POST);
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
}

$action = $_POST['action'] ?? '';

switch($action){
    case 'addreflink':
        $targetid = Sanitize::int($_POST['targetid'] ?? 0);
        $type = $_POST['type'] ?? '';

        error_log("ADDREFLINK HIT: targetid=$targetid type=$type");

        if($targetid && $type){
            $statusStr = $refManager->addRefLink($refId,$targetid,$type);
            error_log("RESULT: " . $statusStr);
        } else {
            error_log("MISSING targetid or type");
        }
        break;
    case 'linkoccur':
        $targetid = Sanitize::int($_POST['targetid'] ?? 0);
        $type = $_POST['type'] ?? '';

        error_log("LINKOCCUR HIT: targetid=$targetid type=$type");

        if($targetid && $type){
            $statusStr = $refManager->linkOccurFromResource($refId,$targetid,$type);
            error_log("RESULT: " . $statusStr);
        } else {
            error_log("MISSING targetid or type");
        }
        break;
    case 'deletereflink':
        $targetid = Sanitize::int($_POST['targetid'] ?? 0);
        $type = $_POST['type'] ?? '';

		$statusStr = $refManager->deleteRefLink($refId, $targetid, $type);
        error_log("DELETE REF LINK: targetid=$targetid type=$type");
        break;
	case 'collocclink':
		$statusStr = $refManager->linkCollFromOccur($refId);
		error_log("ADD REF LINK FROM COLL: " . $statusStr);
        break;
	case 'deleteoccurrences':
		if(!empty($_POST['scbox']) && is_array($_POST['scbox'])){
			foreach($_POST['scbox'] as $occid){
				$occid = Sanitize::int($occid);
				if($occid){
					$statusStr = $refManager->deleteRefLink($refId, $occid, 'occurrence');
				}
			}
			error_log("STATUS: " . $statusStr);
		}
		break;
}


if($refId){
	$refArr = $refManager->getRefArr($refId);
	$refChecklistArr = $refManager->getRefChecklistArr($refId);
	$refDatasetArr = $refManager->getRefDatasetArr($refId);
	$refCollArr = $refManager->getRefCollArr($refId);
	$refOccArr = $refManager->getRefOccArr($refId);
	$refTaxaArr = $refManager->getRefTaxaArr($refId);
}
else{
	$refArr = [
		'bibliographicCitation' => '',
		'identifier' => '',
		'title' => '',
		'creator' => '',
		'date' => '',
		'source' => '',
		'description' => '',
		'subject' => '',
		'language' => '',
		'rights' => '',
		'type' => '',
		'taxonRemarks' => '',
		'url' => ''
	];

	$refChecklistArr = [];
	$refDatasetArr = [];
	$refCollArr = [];
	$refOccArr = [];
	$refTaxaArr = [];
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
	<script src="<?php echo $CLIENT_ROOT; ?>/js/symb/api.taxonomy.taxasuggest.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../js/datatables/datatables.css" />
	<script src="../js/datatables/datatables.js"></script>
	<script type="text/javascript">
		var refid = <?php echo $refId; ?>;

	function fetchDOI(){
		const doi = document.getElementById("doiInput").value.trim();

		if(!doi){
			alert("Enter a DOI");
			return;
		}

		fetch("https://api.crossref.org/works/" + encodeURIComponent(doi))
			.then(res => res.json())
			.then(data => {
				if(!data.message){
					alert("Reference not found, ensure that you are using the full DOI and not a URL");
					return;
				}

				const d = data.message;

				if(d.title && d.title.length){
					document.getElementById("title").value = d.title[0];
				}

				if(d.language){
					document.querySelector('[name="language"]').value = d.language;
				}

				if(d.author){
					let authors = d.author.map(a => 
						(a.given ? a.given + " " : "") + (a.family || "")
					);
					document.getElementById("creator").value = authors.join(", ");
				}

				if(d.issued && d.issued["date-parts"]){
					document.querySelector('[name="date"]').value =
						d.issued["date-parts"][0].join("-");
				}

				if(d["container-title"] && d["container-title"].length){
					document.querySelector('[name="source"]').value =  decodeHTML(d["container-title"][0])
				}

				if(d.license && d.license.length){
					let licenseObj = d.license.find(l => l["content-version"] === "vor") || d.license[0];

					if(licenseObj && licenseObj.URL){
						document.querySelector('[name="rights"]').value = licenseObj.URL;
					}
				}

				document.querySelector('[name="identifier"]').value = d.DOI || doi;

				document.querySelector('[name="url"]').value = d.DOI ? "https://doi.org/" + d.DOI.trim().toLowerCase() : "";

				if(d.type){
					document.querySelector('[name="type"]').value = d.type;
				}

				let citation = "";

				if(d.author && d.author.length){
					let authors = d.author.slice(0, 20).map(a => {
						let initials = "";
						if(a.given){
							initials = a.given.split(" ")
								.map(n => n.charAt(0).toUpperCase() + ".")
								.join(" ");
						}
						return (a.family || "") + ", " + initials;
					});
					if(d.author.length > 20){
						authors.push("...");
					}
					citation += authors.join(", ") + ". ";
				}

				if(d.issued && d.issued["date-parts"]){
					let year = d.issued["date-parts"][0][0];
					if(year){
						citation += "(" + year + "). ";
					}
				}

				if(d.title && d.title.length){
					let title = d.title[0];
					title = title.charAt(0).toUpperCase() + title.slice(1).toLowerCase();
					citation += title + ". ";
				}

				if(d["container-title"] && d["container-title"].length){
					let journal = decodeHTML(d["container-title"][0]);
					citation += journal;
				}

				if(d.volume){
					citation += ", " + d.volume;
				}

				if(d.issue){
					citation += "(" + d.issue + ")";
				}

				if(d.page){
					citation += ": " + d.page;
				}

				if(d.volume || d.issue || d.page){
					citation += ". ";
				}

				if(d.DOI){
					citation += "https://doi.org/" + d.DOI;
				}

				document.getElementById("bibliographicCitation").value = citation;

			})
			.catch(err => {
				console.error(err);
				alert("Error fetching DOI data");
			});
	}

	</script>
	<style type="text/css">

		#occurrenceSampleTable {
			font-size: 12px;
			width: 100%;
			table-layout: auto;
			border: 1.5px solid #c0c0c0a6;
		}

		#innertext{ max-width: 1400px; }
		.fieldGroupDiv { clear:both; margin-top:2px; }
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
		  border: none;
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

		#refdetails { max-width: 1000px; } 

		.fieldGroupDiv input {
			width: 500px;
		}

		.fieldGroupDiv textarea {
			width: 100%;
			box-sizing: border-box;
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
					<?php if($refId) { ?>
						<li><a href="#refoccdiv">Linked Occurrences</a></li>
						<li><a href="#reftaxadiv">Linked Taxa</a></li>
						<li><a href="#reflinksdiv">Other Linked Resources</a></li>
						<li><a href="#refadmindiv">Admin</a></li>
					<?php } ?>

				</ul>

				<div id="refdetaildiv">
					<div id="refdetails" style="overflow:auto;">
						<form name="referenceeditform" id="referenceeditform"
							action="refdetails.php"
							method="post"
							onsubmit="return verifyEditRefForm(this);">

							<input type="hidden" name="refid" value="<?php echo $refId; ?>" />

							<a href="https://rs.gbif.org/extension/gbif/1.0/references.xml"><h1>Literature References Extension Fields</h1></a>

							<div style="margin-bottom:15px;">
								<b>Import details from DOI:</b><br>
								<input type="text" id="doiInput" placeholder="Enter DOI (e.g. 10.1000/xyz123)" style="width:300px;">
								<button type="button" onclick="fetchDOI()">Fetch</button>
							</div>

							<div class="fieldGroupDiv">
								<b>Bibliographic Citation:</b>
								<textarea name="bibliographicCitation" id="bibliographicCitation"><?php echo $refArr['bibliographicCitation']; ?></textarea>
							</div>

							<div class="fieldGroupDiv">
								<b>Identifier (DOI):</b>
								<input type="text" name="identifier" value="<?php echo $refArr['identifier']; ?>">
							</div>
							
							<div class="fieldGroupDiv">
								<b>URL:</b>
								<input type="text" name="url" value="<?php echo $refArr['url']; ?>">
							</div>

							<div class="fieldGroupDiv">
								<b>Title:</b></br>
								<textarea name="title" id="title"><?php echo $refArr['title']; ?></textarea>
							</div>

							<div class="fieldGroupDiv">
								<b>Creator(s):</b>
								<textarea name="creator" id="creator"><?php echo $refArr['creator']; ?></textarea>
							</div>

							<div class="fieldGroupDiv">
								<b>Date:</b>
								<input type="text" name="date" value="<?php echo $refArr['date']; ?>">
							</div>

							<div class="fieldGroupDiv">
								<b>Source (e.g., Journal):</b>
								<input type="text" name="source" value="<?php echo $refArr['source']; ?>">
							</div>

							<div class="fieldGroupDiv">
								<b>Description:</b></br>
								<textarea name="description"><?php echo $refArr['description']; ?></textarea>
							</div>

							<div class="fieldGroupDiv">
								<b>Subject:</b>
								<input type="text" name="subject" value="<?php echo $refArr['subject']; ?>">
							</div>

							<div class="fieldGroupDiv">
								<b>Language:</b>
								<select name="language">
									<?php $l = $refArr['language']; ?>
									<option value="">-- Select Language --</option>
									<option value="en" <?= $l=='en'?'selected':'' ?>>English</option>
									<option value="es" <?= $l=='es'?'selected':'' ?>>Spanish</option>
									<option value="fr" <?= $l=='fr'?'selected':'' ?>>French</option>
									<option value="de" <?= $l=='de'?'selected':'' ?>>German</option>
									<option value="ja" <?= $l=='ja'?'selected':'' ?>>Japanese</option>
									<option value="zh" <?= $l=='zh'?'selected':'' ?>>Chinese</option>
									<option value="ru" <?= $l=='ru'?'selected':'' ?>>Russian</option>
									<option value="pt" <?= $l=='pt'?'selected':'' ?>>Portuguese</option>
									<option value="ar" <?= $l=='ar'?'selected':'' ?>>Arabic</option>
								</select>
							</div>

							<div class="fieldGroupDiv">
								<b>Rights:</b>
								<input type="text" name="rights" value="<?php echo $refArr['rights']; ?>">
							</div>

							<div class="fieldGroupDiv">
								<b>Reference Type:</b>
								<select name="type" style="width:300px;">
									<?php $t = $refArr['type']; ?>
									<option value="">-- Select Type --</option>
									<option value="journal-article" <?= $t=='journal-article'?'selected':'' ?>>Journal Article</option>
									<option value="book" <?= $t=='book'?'selected':'' ?>>Book</option>
									<option value="book-chapter" <?= $t=='book-chapter'?'selected':'' ?>>Book Chapter</option>
									<option value="proceedings-article" <?= $t=='proceedings-article'?'selected':'' ?>>Proceedings Article</option>
									<option value="posted-content" <?= $t=='posted-content'?'selected':'' ?>>Posted Content (Preprint, etc.)</option>
									<option value="report" <?= $t=='report'?'selected':'' ?>>Report</option>
									<option value="dataset" <?= $t=='dataset'?'selected':'' ?>>Dataset</option>
									<option value="peer-review" <?= $t=='peer-review'?'selected':'' ?>>Peer Review</option>
									<option value="grant" <?= $t=='grant'?'selected':'' ?>>Grant</option>
								</select>
							</div>

							<div class="fieldGroupDiv">
								<b>Taxon Remarks:</b></br>
								<textarea name="taxonRemarks"><?php echo $refArr['taxonRemarks']; ?></textarea>
							</div>

							<div style="margin-top:30px;">
								<button name="formsubmit" type="submit" value="<?php echo $refId ? 'Edit Reference' : 'Create Reference'; ?>">
									<?php echo $refId ? 'Save Edits' : 'Create Reference'; ?>
								</button>
							</div>
						</form>
					</div>
				</div>
				<?php if ($refId) { ?>
				<div id='refoccdiv' style="">
					<?php
						echo '<div id="referenceoccurlink">';
							?>
							<form name="sampleListingForm" method="post" action="refdetails.php">
								<fieldset id="samplePanel">
								<legend>Sample Listing</legend>
								<?php
								if($refOccArr){
								?>
								<div style="float:left">Linked Occurrences: <?php echo count($refOccArr); ?></div>
								<div>
										<table id="occurrenceSampleTable" class="cell-border stripe hover">										
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
												echo '<a href="'.$CLIENT_ROOT.'/collections/individual/index.php?occid='.$sampleArr['occid'].'" target="_blank">'.$sampleArr['occid'].'</a>    ';
												echo '<a href="'.$CLIENT_ROOT.'/collections/editor/occurrenceeditor.php?occid='.$sampleArr['occid'].'" target="_blank"><img src="../images/edit.png" style="width:13px" /></a>';
												echo '</td>';

												echo '</tr>';
											}
											?>
										</tbody>
									</table>
									<?php
									echo '<div style="margin-top:10px;">';
									echo '<input type="hidden" name="action" value="deleteoccurrences">';
									echo '<input type="hidden" name="refid" value="'.$refId.'">';
									echo '<button type="submit" onclick="return confirm(\'Delete links to selected samples?\')">
       					 				Delete Links to Selected Samples
      									</button>';
									echo '</div>';
								}
								else{
									echo 'There are no occurrences linked with this reference. </br></br>';
								}
								?>
								</fieldset>
							</form>
						</br>
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
				<div id="reftaxadiv">

					<fieldset>
						<legend><b>Linked Taxa</b></legend>

						<div id="referencetaxalink">
							<?php if($refTaxaArr): ?>
								<ul>
									<?php foreach($refTaxaArr as $k => $v): ?>
										<li style="display:flex; align-items:center; gap:6px;">

											<a href="../taxa/index.php?taxon=<?= htmlspecialchars($k) ?>" target="_blank">
												<?= htmlspecialchars($v) ?>
											</a>

											<form method="post" action="refdetails.php" style="margin:0;">
												<input type="hidden" name="refid" value="<?= $refId ?>">
												<input type="hidden" name="targetid" value="<?= $k ?>">
												<input type="hidden" name="type" value="taxon">
												<input type="hidden" name="action" value="deletereflink">
												<button type="submit" style="border:none;background:none;padding:0;">
													<img src="../images/del.png" style="width:14px;" title="Delete link">
												</button>
											</form>

										</li>
									<?php endforeach; ?>
								</ul>
							<?php else: ?>
								<div><b>No taxa linked to this reference.</b></div>
							<?php endif; ?>
						</div>
					</fieldset>

					<br>
					<form method="post" action="refdetails.php">
						<fieldset>
							<legend><b>Add Taxon</b></legend>

							<input type="hidden" name="refid" value="<?= $refId ?>">
							<input type="hidden" name="action" value="addreflink">
							<input type="hidden" name="type" value="taxon">

							<input type="hidden" name="targetid" id="taxa_targetid">

							<div>
								<label><b>Search Taxon:</b></label><br>
								<input type="text" id="taxa" style="width:350px;">
							</div>

							<div style="margin-top:10px;">
								<label>Type:</label>
								<select id="taxontype" name="taxontype">
									<option value="2">Scientific Name</option>
									<option value="3">Family</option>
									<option value="4">Taxonomic Group</option>
								</select>
							</div>

							<div style="margin-top:10px;">
								<label>
								<input type="checkbox" id="usethes" name="usethes" value="1" checked>
									Include synonyms
								</label>
							</div>

							<div style="margin-top:12px;">
								<button type="submit">Add Taxon</button>
							</div>

						</fieldset>
					</form>

				</div>
				<div id="reflinksdiv" style="">
					<div style="width:100%;">
							<div style="width:100%;">
								<form name='checklistform' id='checklistform' action='refdetails.php' method='post'>
									<input type="hidden" name="refid" value="<?php echo $refId; ?>">
									<input type="hidden" name="action" value="addreflink">
									<input type="hidden" name="type" value="checklist">
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
											<button type="submit">Add</button>
										</form>
										</div>
										<hr />
										<div id="checklistlistdiv">
											<?php
											if($refChecklistArr){
												echo '<ul>';
												foreach($refChecklistArr as $k => $v){
													echo '<li style="display:flex; align-items:center; gap:6px;">';

													echo '<a href="../checklists/checklist.php?clid=' . htmlspecialchars($k, ENT_QUOTES) . '">'
														. htmlspecialchars($v, ENT_QUOTES) .
														'</a>';

													echo '<form method="post" action="refdetails.php" style="margin:0;">';
													echo '<input type="hidden" name="refid" value="'.$refId.'">';
													echo '<input type="hidden" name="targetid" value="'.$k.'">';
													echo '<input type="hidden" name="type" value="checklist">';
													echo '<input type="hidden" name="action" value="deletereflink">';
													echo '<button type="submit" style="border:none;background:none;padding:0;">';
													echo '<img src="../images/del.png" style="width:14px;">';
													echo '</button>';
													echo '</form>';

													echo '<form method="post" action="refdetails.php" style="margin:0;">';
													echo '<input type="hidden" name="refid" value="'.$refId.'">';
													echo '<input type="hidden" name="targetid" value="'.$k.'">';
													echo '<input type="hidden" name="type" value="checklist">';
													echo '<input type="hidden" name="action" value="linkoccur">';
													echo '<button type="submit"
														onclick="return confirm(\'Link ALL occurrences from this checklist to the reference?\')">
														Link Associated Occurrences To Reference
														</button>';	
													echo '</form>';

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
									<input type="hidden" name="action" value="addreflink">
									<input type="hidden" name="type" value="dataset">

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
											<button type="submit">Add</button>
										</form>
										</div>
										<hr />
										<div id="datasetlistdiv">
											<?php
											if($refDatasetArr){
												echo '<ul>';
												foreach($refDatasetArr as $k => $v){
													echo '<li style="display:flex; align-items:center; gap:6px;">';

													echo '<a href="../collections/datasets/datasetmanager.php?datasetid=' . htmlspecialchars($k, ENT_QUOTES) . '">'
														. htmlspecialchars($v, ENT_QUOTES) .
														'</a>';	

													echo '<form method="post" action="refdetails.php" style="margin:0;">';
													echo '<input type="hidden" name="refid" value="'.$refId.'">';
													echo '<input type="hidden" name="targetid" value="'.$k.'">';
													echo '<input type="hidden" name="type" value="dataset">';
													echo '<input type="hidden" name="action" value="deletereflink">';
													echo '<button type="submit" style="border:none;background:none;padding:0;">';
													echo '<img src="../images/del.png" style="width:14px;">';
													echo '</button>';
													echo '</form>';
													
													echo '<form method="post" action="refdetails.php" style="margin:0;">';
													echo '<input type="hidden" name="refid" value="'.$refId.'">';
													echo '<input type="hidden" name="targetid" value="'.$k.'">';
													echo '<input type="hidden" name="type" value="dataset">';
													echo '<input type="hidden" name="action" value="linkoccur">';
													echo '<button type="submit"
														onclick="return confirm(\'Link ALL occurrences from this dataset to the reference?\')">
														Link Associated Occurrences To Reference
														</button>';													
													echo '</form>';

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
									<input type="hidden" name="action" value="addreflink">
									<input type="hidden" name="type" value="collection">
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
											<button type="submit">Add</button>
										</form>
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
													echo '<form method="post" action="refdetails.php" style="display:inline;margin:0;padding:0;">
														<input type="hidden" name="refid" value="'.$refId.'">
														<input type="hidden" name="targetid" value="'.$k.'">
														<input type="hidden" name="type" value="collection">
														<input type="hidden" name="action" value="deletereflink">
														<button type="submit" style="border:none;background:none;padding:0;margin:0;display:inline;">
															<img src="../images/del.png" style="width:14px;vertical-align:middle;" title="Delete link">
														</button>
													</form>';
													echo '</li>';
												}
												echo '</ul>';
											}
											else{
												echo '<div><b>There are currently no collections linked to this reference.</b></div>';
											}
											?>
										</div>
										<div id="collectionoccurlinkdiv">
											<form method="post" action="refdetails.php" style="margin:0;">
													<input type="hidden" name="refid" value="<?php echo $refId; ?>">
													<input type="hidden" name="action" value="collocclink">
													<button type="submit"
														onclick="return confirm('This will link all collections associated with linked occurrences. Continue?')">
														Link Collections to Reference Based on Occurrences
													</button>
											</form>
										</div>
									</fieldset>
								</form>
							</div><br />
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
							?>
							<input name="formsubmit" type="submit" value="Delete Reference" <?php if($refChecklistArr || $refCollArr || $refDatasetArr || $refOccArr || $refTaxaArr) echo 'DISABLED'; ?> />
							<input name="refid" type="hidden" value="<?php echo $refId; ?>" />
						</fieldset>
					</form>
				</div>
				<?php } ?> 
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
