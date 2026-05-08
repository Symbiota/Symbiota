<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT . '/classes/ReferenceManager.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

header("Content-Type: text/html; charset=".$CHARSET);


Language::load([
	'collections/loans/loan_langs',
	'collections/editor/includes/determinationtab',
	'collections/search/index',
	'references/index'
]);

$isEditor = false;
if($IS_ADMIN) $isEditor = true;
elseif(array_key_exists('SuperAdmin',$USER_RIGHTS) || array_key_exists('SuperAdmin',$USER_RIGHTS)) $isEditor = true;

function post_str($key, $maxLen = 1000){
    return isset($_POST[$key])
        ? substr(trim($_POST[$key]), 0, $maxLen)
        : '';
}

$data = [
    'bibliographicCitation' => post_str('bibliographicCitation', 5000),
    'identifier'            => post_str('identifier', 255),
    'url'                   => post_str('url', 500),
    'title'                 => post_str('title', 2000),
    'creator'               => post_str('creator', 2000),
    'date'                  => post_str('date', 50),
    'source'                => post_str('source', 500),
    'description'           => post_str('description', 5000),
    'subject'               => post_str('subject', 500),
    'language'              => post_str('language', 20),
    'rights'                => post_str('rights', 500),
    'type'                  => post_str('type', 50),
    'taxonRemarks'          => post_str('taxonRemarks', 2000),
    'datasetID'             => post_str('datasetID', 255),
];

$refId = array_key_exists('refid', $_REQUEST) ? Sanitize::int($_REQUEST['refid']) : 0;
$formSubmit = array_key_exists('formsubmit', $_POST) ? $_POST['formsubmit'] : '';

$refManager = new ReferenceManager();
$refArr = '';


$statusStr = '';
if($formSubmit){
	if($formSubmit === 'Create Reference'){
		$statusStr = $refManager->createReference($data);
		$newRefId = $refManager->getRefId();

		if($newRefId){
			header("Location: refdetails.php?refid=".$newRefId);
			exit;
		}
	}
	elseif($formSubmit === 'Edit Reference'){
		$data['refid'] = $refId;
		$statusStr = $refManager->editReference($data);
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
		'datasetID' => '',
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
	<title><?php echo $DEFAULT_TITLE . ' ' . ($LANG['REF_MGMT'] ?? 'Reference Management'); ?></title>
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
					let rawTitle = stripHTML(d.title[0]);
					rawTitle = cleanTitle(rawTitle);

					document.getElementById("title").value = rawTitle;
				}

				if(d.language){
					document.querySelector('[name="language"]').value = d.language;
				}

				if(d.author){
					let authors = d.author.map(a => {
						let given = a.given ? toTitleCase(a.given) : "";
						let family = a.family ? toTitleCase(a.family) : "";
						return (given ? given + " " : "") + family;
					});
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
						let family = a.family ? toTitleCase(a.family) : "";

						let initials = "";
						if(a.given){
							initials = toTitleCase(a.given)
								.split(" ")
								.map(n => n.charAt(0).toUpperCase() + ".")
								.join(" ");
						}

						return family + ", " + initials;
					});
					if(d.author.length > 20){
						authors.push("...");
					}
					citation += authors.join(", ") + " ";
				}

				if(d.issued && d.issued["date-parts"]){
					let year = d.issued["date-parts"][0][0];
					if(year){
						citation += "(" + year + "). ";
					}
				}

				if(d.title && d.title.length){
					let rawTitle = stripHTML(d.title[0]);
					rawTitle = cleanTitle(rawTitle);
					citation += rawTitle + ". ";
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
				<a href='index.php'> <b><?= htmlspecialchars($LANG['REF_MGMT'] ?? 'Reference Management'); ?></b></a>
			</div>
			<?php
		}
	}
	else{
		?>
		<div class='navpath'>
			<a href='../index.php'>Home</a> &gt;&gt;
			<a href='index.php'> <b><?= htmlspecialchars($LANG['REF_MGMT'] ?? 'Reference Management'); ?></b></a>
		</div>
		<?php
	}
	?>
	<!-- This is inner text! -->
	<div role="main" id="innertext">
		<h1 class="page-heading"><?= htmlspecialchars($LANG['REF_MGMT'] ?? 'Reference Management'); ?></h1>		
		<?php
		if($isEditor){
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
						<li><a href="#refdetaildiv"><?= $LANG['REF_DETAILS'] ?? 'Reference Details' ?></a></li>
					<?php if($refId) { 
						?>
						<li><a href="#refoccdiv"><?= $LANG['LINKED_OCC'] ?? 'Linked Occurrences' ?></a></li>
						<li><a href="#reftaxadiv"><?= $LANG['LINKED_TAXA'] ?? 'Linked Taxa' ?></a></li>
						<li><a href="#reflinksdiv"><?= $LANG['OTHER_LINKS'] ?? 'Other Linked Resources' ?></a></li>
						<li><a href="#refadmindiv"><?= $LANG['ADMIN'] ?? 'Admin' ?></a></li>
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

							<?php if ($refId) {
								echo '<h2><a href="../references/publicref.php?refid=' . htmlspecialchars($refId, ENT_QUOTES) . '">'
									. htmlspecialchars('Public Reference Page', ENT_QUOTES) .
									'</a></h2>';	
							} ?>
							<div style="margin-bottom:15px;">
								<b><?= $LANG['IMPORT_DOI'] ?? 'Import details from DOI:' ?></b><br>
								<input type="text" id="doiInput" placeholder="<?= $LANG['DOI_PLACEHOLDER'] ?? 'Enter DOI (e.g. 10.1000/xyz123)' ?>">
								<button type="button" onclick="fetchDOI()">
									<?= $LANG['FETCH'] ?? 'Fetch' ?>
								</button>
							</div>

							<div class="fieldGroupDiv">
								<b><?= $LANG['BIB_CIT_REQ'] ?? 'Bibliographic Citation (required):' ?></b>
								<textarea name="bibliographicCitation" id="bibliographicCitation"><?php echo htmlspecialchars($refArr['bibliographicCitation'] ?? '', ENT_QUOTES) ?></textarea>
							</div>

							<div class="fieldGroupDiv">
								<b><?= $LANG['IDENTIFIER'] ?? 'Identifier (DOI):' ?></b>
								<input type="text" name="identifier" value="<?= htmlspecialchars($refArr['identifier'] ?? '', ENT_QUOTES) ?>">

							</div>
							
							<div class="fieldGroupDiv">
								<b><?= $LANG['URL'] ?? 'URL:' ?></b>
								<input type="text" name="url" value="<?= htmlspecialchars($refArr['url'] ?? '', ENT_QUOTES) ?>">
							</div>

							<div class="fieldGroupDiv">
								<b><?= $LANG['TITLE'] ?? 'Title:' ?></b></br>
								<textarea name="title" id="title"><?php echo htmlspecialchars($refArr['title'] ?? '', ENT_QUOTES) ?></textarea>
							</div>

							<div class="fieldGroupDiv">
								<b><?= $LANG['CREATOR'] ?? 'Creator(s):' ?></b>
								<textarea name="creator" id="creator"><?php echo htmlspecialchars($refArr['creator'] ?? '', ENT_QUOTES) ?></textarea>
							</div>

							<div class="fieldGroupDiv">
								<b><?= $LANG['DATE'] ?? 'Date:' ?></b>
								<input type="text" name="date" value="<?php echo htmlspecialchars($refArr['date'] ?? '', ENT_QUOTES) ?>">
							</div>

							<div class="fieldGroupDiv">
								<b><?= $LANG['SOURCE'] ?? 'Source (e.g., Journal):' ?></b>
								<input type="text" name="source" value="<?php echo htmlspecialchars($refArr['source'] ?? '', ENT_QUOTES) ?>">
							</div>

							<div class="fieldGroupDiv">
								<b><?= $LANG['DESCRIPTION'] ?? 'Description:' ?></b>
								<textarea name="description"><?php echo htmlspecialchars($refArr['description'] ?? '', ENT_QUOTES) ?></textarea>
							</div>

							<div class="fieldGroupDiv">
								<b><?= $LANG['SUBJECT'] ?? 'Subject:' ?></b>
								<input type="text" name="subject" value="<?php echo htmlspecialchars($refArr['subject'] ?? '', ENT_QUOTES) ?>">
							</div>

							<div class="fieldGroupDiv">
								<b><?= $LANG['LANGUAGE'] ?? 'Language:' ?></b>
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
								<b><?= $LANG['RIGHTS'] ?? 'Rights:' ?></b>
								<input type="text" name="rights" value="<?php echo htmlspecialchars($refArr['rights'] ?? '', ENT_QUOTES) ?>">
							</div>

							<div class="fieldGroupDiv">
								<b><?= $LANG['REF_TYPE'] ?? 'Reference Type:' ?></b>
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
								<b><?= $LANG['TAXON_REMARKS'] ?? 'Taxon Remarks:' ?></b></br>
								<textarea name="taxonRemarks"><?php echo htmlspecialchars($refArr['taxonRemarks'] ?? '', ENT_QUOTES) ?></textarea>
							</div>
							<div class="fieldGroupDiv">
								<b><?= $LANG['DATASET_ID'] ?? 'DatasetID:' ?></b></br>
								<input type="text" name="datasetID" value="<?php echo htmlspecialchars($refArr['datasetID'] ?? '', ENT_QUOTES) ?>">
							</div>

							<div style="margin-top:30px;">
								<button name="formsubmit" type="submit"
									value="<?= $refId ? 'Edit Reference' : 'Create Reference'; ?>">
									<?= $refId
										? ($LANG['SAVE_EDITS'] ?? 'Save Edits')
										: ($LANG['CREATE_REF'] ?? 'Create Reference'); ?>
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
								<legend><?= $LANG['SAMPLE_LISTING'] ?? 'Sample Listing' ?></legend>
								<?php
								if($refOccArr){
								?>
								<div style="float:left"><?php echo $LANG['LINKED_OCC']. ': ' . count($refOccArr); ?></div>
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
									echo "<button type='submit' onclick=\"return confirm('"
										. htmlspecialchars($LANG['CONFIRM_DEL_OCC'] ?? 'Delete links to selected occurrences?', ENT_QUOTES)
										. "')\">
										" . htmlspecialchars($LANG['DELETE_SELECTED_OCC'] ?? 'Delete Links to Selected occurrences', ENT_QUOTES) . "
										</button>";
									echo '</div>';
								}
								else{
									echo $LANG['NO_OCC_LINKED'] ?? 'There are no occurrences linked with this reference.<br><br>';
								}
								?>
								</fieldset>
							</form>
						</br>
						</div>
						<div id="batchOcc-div">
							<fieldset>
							<legend><?= $LANG['BATCH_ADD_OCC'] ?? 'Batch add occurrences' ?></legend>
								<div class="info-div">
									<?= $LANG['BATCH_ADD_DESC'] ?? 'Batch add multiple occurrences by entering a list of catalog numbers...' ?>
								</div>								
								<form name="batchaddform" action="refdetails.php" method="post">
									<div class="field-div">
										<label><?php echo $LANG['CATNUMS']; ?>:</label><br/>
										<textarea name="catalogNumbers" cols="6" style="width:700px"></textarea>
									</div>
									<div class="field-div">
										<label>Target:</label>
										<span class="radio-span"><input name="targetidentifier" type="radio" value="allid" /> <?php echo $LANG['ALL_IDS']; ?></span>
										<span class="radio-span"><input name="targetidentifier" type="radio" value="catnum" checked /> <?php echo $LANG['CATNO']; ?></span>
										<span class="radio-span"><input name="targetidentifier" type="radio" value="other" /> <?php echo $LANG['OTHER_CATNUMS']; ?></span>
									</div>
									<div class="field-div">
										<input name="refid" type="hidden" value="<?php echo $refId; ?>" />
										<div style="float:left;margin-top:15px;margin-left:15px">
											<button name="formsubmit" type="submit" value="batchAddLink"><?php echo $LANG['ADD_OCC'] ?? 'Add Occurrences';?></button>
										</div>
									</div>
								</form>
							</fieldset>
				</div>
				</div>
				<div id="reftaxadiv">

					<fieldset>
						<legend><b><?= $LANG['LINKED_TAXA'] ?? 'Linked Taxa' ?></b></legend>
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
												<button type="submit"
													onclick="return confirm('<?= htmlspecialchars($LANG['CONFIRM_DELETE_LINK'] ?? 'Delete this link?', ENT_QUOTES) ?>')"
													style="border:none;background:none;padding:0;">
													 <img src="../images/del.png" style="width:14px;">
												</button>
											</form>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php else: ?>
							<div><b><?= $LANG['NO_TAXA'] ?? 'No taxa linked to this reference.' ?></b></div>
							<?php endif; ?>
						</div>
					</fieldset>

					<br>
					<form method="post" action="refdetails.php">
						<fieldset>
						<legend><b><?= $LANG['ADD_TAXON'] ?? 'Add Taxon' ?></b></legend>

							<input type="hidden" name="refid" value="<?= $refId ?>">
							<input type="hidden" name="action" value="addreflink">
							<input type="hidden" name="type" value="taxon">

							<input type="hidden" name="targetid" id="taxa_targetid">

							<div>
							<label><b><?= $LANG['SEARCH_TAXON'] ?? 'Search Taxon:' ?></b></label>
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
									<?php echo htmlspecialchars($LANG['INCLUDE_SYN'] ?? 'Include Synonyms', ENT_QUOTES); ?>
								</label>
							</div>

							<div style="margin-top:12px;">
								<button type="submit"><?= $LANG['ADD'] ?? 'Add' ?></button>
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
									<legend><b><?= $LANG['CHECKLISTS'] ?? 'Checklists' ?></b></legend>
										<div>
											<div>
												<b><?= $LANG['ADD_CHECKLIST'] ?? 'Add Checklists By Name' ?></b>
											</div>
											<div>
										<div>
											<input type="text" id="checklist_search" placeholder="Search checklist..." style="width:300px;">
											<input type="hidden" name="targetid" id="checklist_targetid">
											<button type="submit">Add</button>
										</div>
										</select>
										</form>
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
														onclick="return confirm(\''. $LANG['CONFIRM_LINK_CHECKLIST'] . '\')">'
														. $LANG['LINK_OCC_FROM_CHECKLIST'] .
														'</button>';	
													echo '</form>';

													echo '</li>';
												}
												echo '</ul>';
											}
											else{
												echo '<div><b>' . ($LANG['NO_CHECKLIST'] ?? 'No checklists linked.') . '</b></div>';											}
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
										<legend><b><?=$LANG['DATASETS'] ?? 'Datasets' ?></b></legend>
										<div>
											<div>
												<b><?=$LANG['ADD_DATASET'] ?? 'Add Dataset' ?> </b>
											</div>
											<div>
										<div>
											<input type="text" id="dataset_search" placeholder="Search dataset..." style="width:300px;">
											<input type="hidden" name="targetid" id="dataset_targetid">
											<button type="submit">Add</button>
										</div>
										</select>
										</form>
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
														onclick="return confirm(\''. $LANG['CONFIRM_LINK_DATASET'] . '\')">'
														. $LANG['LINK_OCC_FROM_DATASET'] .
														'</button>';												
													echo '</form>';

													echo '</li>';
												}
												echo '</ul>';
											}
											else{
												echo '<div><b>' . $LANG['NO_DATASET'] . '</b></div>';
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
										<legend><b><?=$LANG['COLLECTIONS'] ?? 'Collections'?></b></legend>
										<div>
											<div>
												<b><?=$LANG['ADD_COLLECTION'] ?? 'Add Collection By Name'?></b>
											</div>
											<div>
																						<div>
											<input type="text" id="collection_search" placeholder="Search collections..." style="width:300px;">
											<input type="hidden" name="targetid" id="collection_targetid">
											<button type="submit">Add</button>
										</div>
										</form>
										</div>
										<hr />
										<div id="collectionlistdiv">
											<?php
											if($refCollArr){
												echo '<ul>';
												foreach($refCollArr as $k => $v){
													echo '<li style="display:flex; align-items:center; gap:6px;">';
													echo '<a href="../collections/misc/collprofiles.php?collid=' . htmlspecialchars($k, ENT_QUOTES) . '">'
														. htmlspecialchars($v, ENT_QUOTES) .
														'</a>';
													echo '<form method="post" action="refdetails.php" style="display:inline;margin:0;padding:0;">
														<input type="hidden" name="refid" value="'.$refId.'">
														<input type="hidden" name="targetid" value="'.$k.'">
														<input type="hidden" name="type" value="collection">
														<input type="hidden" name="action" value="deletereflink">';
													echo '<button type="submit"
														onclick="return confirm(\'' . htmlspecialchars($LANG['CONFIRM_DELETE_LINK'] ?? 'Delete this link?', ENT_QUOTES) . '\')"
														style="border:none;background:none;padding:0;">';
													echo '<img src="../images/del.png" style="width:14px;">';
													echo '</button>';
													echo '</form>';
													echo '</li>';
												}
												echo '</ul>';
											}
											else{
												echo '<div><b>' . $LANG['NO_COLLECTION'] . '</b></div>';
											}
											?>
										</div>
										<div id="collectionoccurlinkdiv">
											<form method="post" action="refdetails.php" style="margin:0;">
													<input type="hidden" name="refid" value="<?php echo $refId; ?>">
													<input type="hidden" name="action" value="collocclink">
													<button type="submit"
														onclick="return confirm('<?= htmlspecialchars($LANG['CONFIRM_LINK_COLLECTION'] ?? 'This will link all collections associated with linked occurrences. Continue?', ENT_QUOTES) ?>')">
														<?= htmlspecialchars($LANG['LINK_COLLECTION_FROM_OCC'] ?? 'Link Collections to Reference Based on Occurrences') ?>
													</button>
											</form>
										</div>
									</fieldset>
								</form>
							</div><br />
					</div>
				</div>
				<div id="refadmindiv" style="">
					<form name="delrefform" action="index.php" method="post" onsubmit="return confirm($LANG['CONFIRM_DELETE_REF'])">
						<fieldset style="width:350px;margin:20px;padding:20px;">
							<legend><b><?= $LANG['DELETE_REF'] ?? 'Delete Reference' ?></b></legend>
							<?php
							if($refChecklistArr || $refDatasetArr ||$refCollArr || $refOccArr || $refTaxaArr){
								echo '<div style="font-weight:bold;margin-bottom:15px;">';
								echo $LANG['DELETE_BLOCKED'] ?? 'Reference cannot be deleted until all linked records are removed.';
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
		elseif(!$SYMB_UID){
				echo 'Please <a href="../profile/index.php?refurl=../references/index.php">login</a>';
		}
		else {
			echo 'You do not have permissions to view this page';
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
