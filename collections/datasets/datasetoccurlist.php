<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/OccurrenceDataset.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load('collections/datasets/datasetoccurlist');

$datasetId = array_key_exists('datasetid', $_REQUEST) ? Sanitize::int($_REQUEST['datasetid']) : 0;
$pageNumber = array_key_exists('pagenumber', $_REQUEST) ? Sanitize::int($_REQUEST['pagenumber']) : 1;

$datasetManager = new OccurrenceDataset();
if($datasetId){
	$datasetManager->setDatasetID($datasetId);
	$mdArr = $datasetManager->getDatasetMetadata();
}
if(!isset($isEditor)){
	$isEditor = 0;
	if(!empty($mdArr['isUserCart'])) {
		$isEditor = 2;
	}
	elseif ($SYMB_UID == $mdArr['uid']) {
		$isEditor = 3;
	}
	elseif($roles = $datasetManager->getUserRoles($datasetId, $SYMB_UID)) {
		if(isset($roles[$datasetId][$SYMB_UID]['roles']['DatasetAdmin'])) {
			$isEditor = 3;
		}
		elseif(isset($roles[$datasetId][$SYMB_UID]['roles']['DatasetEditor'])) {
			$isEditor = 2;
		}
	}
}
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET ?>">
	<script type="text/javascript">

		$(document).ready(function() {
			<?php
			if($isEditor > 1){
				?>
				//Dialog functions
				$(document).on('click', '.edit-notes-btn', function() {
					const $row = $(this).closest('tr');
					const occid = $row.data('occid');
					const notes = $row.find('.occurrence-notes').text().trim();
					const catNum = $row.find('.cat-num').text();
					$('#occidInput').val(occid);
					$('#notesInput').val(notes);
					$('#notesRecordLabel').text(catNum);
					$('#notesError').text('');
					document.getElementById('notesDialog').showModal();
					return false;
				});

				$('#occurrenceNotesForm').on('submit', function(e) {
					e.preventDefault();
					const $saveButton = $('#saveNoteButton');
					const occid = $('#occidInput').val();
					const notes = $('#notesInput').val().trim();
					$saveButton.prop('disabled', true);
					$('#notesError').text('');
					$.ajax({
						url: 'rpc/editoccurrencelink.php',
						type: 'POST',
						dataType: 'json',
						data: { datasetid: <?= $datasetId ?>, occid: occid, notes: notes, action: 'editoccidlink' },
						success: function(response) {
							if (response.status) {
								// Update the notes in the corresponding table row
								$('tr[data-occid="' + occid + '"]').find('.occurrence-notes').text(notes);
								// Close the dialog
								document.getElementById('notesDialog').close();
							} else {
								$('#notesError').text(
									response.error || 'Unable to save notes.'
								);
							}
						},
						error: function() {
							$('#notesError').text(
								'An error occurred while saving the notes.'
							);
						},
						complete: function() {
							$saveButton.prop('disabled', false);
						}
					});
				});

				$('#cancelNotesButton').on('click', function() {
					document.getElementById('notesDialog').close();
				});

				$(document).on('click', '.remove-link-btn', function() {
					const $row = $(this).closest('tr');
					const occid = $row.data('occid');
					const confirmDelete = window.confirm("Are you sure you want to delete this item?");
					if(confirmDelete){
						$.ajax({
							url: 'rpc/editoccurrencelink.php',
							type: 'POST',
							dataType: 'json',
							data: { datasetid: <?= $datasetId ?>, occid: occid, action: 'deloccidlink' },
							success: function(response) {
								if (response.status) {
									$('tr[data-occid="' + occid + '"]').hide();
									let recCnt = $('#rec-cnt').text() - 1;
									$('#rec-cnt').text(recCnt);
								} else {
									alert(response.error || 'Unable to unlink record');
								}
							},
							error: function() {
								alert('Unable to unlink record (2)');
							}
						});
					}
					return false;
				});
				<?php
			}
			?>

			document.addEventListener("DOMContentLoaded", function() {
				const adjustPagination = () => {
					const paginationLinks = document.querySelectorAll(".pagination-link");
					const screenWidth = window.innerWidth;
					let shouldReduceLinks = false;
					let shouldReduceByHalf = false;

					if (screenWidth < 770) {
						shouldReduceLinks = true;
					}
					if (screenWidth < 1200) {
						shouldReduceByHalf = true;
					}

					paginationLinks.forEach(link => {
						const shouldKeepLink = parseInt(link.getAttribute("data-keep-link"));
						const isEven = (parseInt(link.getAttribute("data-even-odd")) || 1) % 2;
						if (shouldReduceByHalf) {
							link.style.display = (isEven) ? "inline-block" : "none";
						}
						if (shouldReduceLinks) {
							link.style.display = (shouldKeepLink) ? "inline-block" : "none";
						}
						if (!shouldReduceByHalf && !shouldReduceLinks) {
							link.style.display = "inline-block";
						}
					});
				}
				window.addEventListener("resize", adjustPagination);
				adjustPagination();
			});

		});

		function selectAll(cb) {
			boxesChecked = true;
			if (!cb.checked) {
				boxesChecked = false;
			}
			var dbElements = document.getElementsByName("occid[]");
			for (i = 0; i < dbElements.length; i++) {
				var dbElement = dbElements[i];
				dbElement.checked = boxesChecked;
			}
		}

		function deleteSelected(){
			let targetForm = document.forms["occurform"];
			if(!validateOccurForm(targetForm)) return false;
			if(!confirm("<?= $LANG['VERIFY_DELETE_SELECTED'] ?>")){
				return false;
			}
			targetForm.submitaction.value = "deleteOccurrences";
			targetForm.targetid.value = "";
			targetForm.submit();
		}

		function transferSelected(sourceForm){
			if(sourceForm.targetid.value == ""){
				alert("<?= $LANG['SELECT_TARGET'] ?>");
				return false;
			}
			let targetForm = document.forms["occurform"];
			if(!validateOccurForm(targetForm)) return false;
			if(!confirm("<?= $LANG['VERIFY_TRANSFER_SELECTED'] ?>")){
				return false;
			}
			targetForm.submitaction.value = "transferOccurrences";
			targetForm.targetid.value = sourceForm.targetid.value;
			targetForm.submit();
		}

		function validateOccurForm(f) {
			let occidChecked = false;
			let dbElements = document.getElementsByName("occid[]");
			for (i = 0; i < dbElements.length; i++) {
				let dbElement = dbElements[i];
				if (dbElement.checked) {
					occidChecked = true;
					break;
				}
			}
			if (!occidChecked) {
				alert("<?= $LANG['PLEASE_SELECT_OCCUR'] ?>");
				return false;
			}
			return true;
		}

		//Popup functions
		function openIndPopup(occid) {
			openPopup("../individual/index.php?occid=" + occid);
		}

		function openPopup(urlStr) {
			var wWidth = 900;
			if (document.body.offsetWidth) wWidth = document.body.offsetWidth * 0.9;
			if (wWidth > 1200) wWidth = 1200;
			newWindow = window.open(urlStr, 'popup', 'scrollbars=1,toolbar=0,resizable=1,width=' + (wWidth) + ',height=600,left=20,top=20');
			if (newWindow.opener == null) newWindow.opener = self;
			newWindow.focus();
			return false;
		}

		function targetDownloadPopup(f) {
			window.open('', 'downloadpopup', 'left=100,top=50,width=900,height=700');
			f.target = 'downloadpopup';
		}

	</script>
	<style>
		label{ display: block; font-weight: bold; }
		button{ display: inline; margin: 10px; }
		.icon-img{ width: 1.2em }
	</style>
</head>
<body>
	<div id="occurtab">
		<?php
		if ($datasetId) {
			$sourcePage = 'index.php';
			if(!empty($_REQUEST['sourcepage']) && $_REQUEST['sourcepage'] == 'dsmanager'){
				$sourcePage = 'datasetmanager.php';
			}
			$retLimit = 200;
			$maxNumberOfPagesBeforeShowPageJump = 10;
			if ($occArr = $datasetManager->getOccurrenceDetails($datasetId, $pageNumber, $retLimit)) {
				$occurDetailArr = Sanitize::out($occArr);
				$recordCount = $datasetManager->getOccurrenceCount($datasetId);
				if($occurDetailArr && $isEditor > 1){
					?>
					<section class="fieldset-like">
						<h2><span><b><?= $LANG['ACTION_PANEL'] ?></b></span></h2>
						<div style="display: inline-flex; justify-content: space-evenly;width:100%;">
							<div>
								<form name="exportAllForm" action="../download/index.php" method="post" onsubmit="targetDownloadPopup(this)" style="display: inline">
									<input name="searchvar" type="hidden" value="datasetid=<?= $datasetId ?>" />
									<input name="dltype" type="hidden" value="specimen" />
									<button type="submit" name="submitaction" value="exportAll"><?= $LANG['EXPORT_DS'] ?></button>
								</form>
							</div>
							<div>
								<button type="button" onclick="deleteSelected()"><?= $LANG['REMOVE_SELECTED'] ?></button>
							</div>
							<div>
								<form name="clearDataset-Form" action="datasetmanager.php" method="post" style="display: inline">
									<input name="datasetid" type="hidden" value="<?= $datasetId ?>" >
									<button type="submit" name="submitaction" value="deleteAll" onclick="return confirm('<?= $LANG['VERIFY_REMOVE_ALL'] ?>')"><?= $LANG['REMOVE_ALL'] ?></button>
								</form>
							</div>
						</div>
						<hr>
						<div style="display: inline-flex; justify-content: space-evenly;width:100%;">
							<form name="transferDataset-Form" action="datasetmanager.php" method="post">
								<input name="datasetid" type="hidden" value="<?= $datasetId ?>" >
								<div>
									<select id="targetid" name="targetid" required>
										<option value=""><?= $LANG['SELECT_TARGET'] ?></option>
										<option value="0"><?= $LANG['CLONE_DATASET'] ?></option>
										<option value="">-------------------</option>
										<?php
										$dsList = $datasetManager->getDatasetsByUser();
										foreach($dsList as $id => $unitArr){
											if($datasetId == $id) continue;
											echo '<option value="' . $id . '">' . Sanitize::outString($unitArr['datasetName']) . '</option>';
										}
										?>
									</select>
								</div>
								<div style="display: inline-flex; justify-content: space-evenly;width:100%;">
									<button type="button" name="submitaction" value="transferSelected" onclick="transferSelected(this.form)"><?= $LANG['TRANSFER_SELECTED'] ?></button>
									<button type="submit" name="submitaction" value="transferAll" onclick="return confirm('<?= $LANG['VERIFY_TRANSFER_ALL'] ?>')"><?= $LANG['TRANSFER_ALL'] ?></button>
								</div>
							</form>
						</div>
					</section>
					<?php
				}
				?>
				<div class="gridlike-form-row" style="justify-content: space-between;">
					<div class="gridlike-form-row" style="text-align: left; flex-wrap: wrap; flex-grow: 1; gap: 5px;">
						<?php
						$pageCount = ceil($recordCount / $retLimit);
						if (($pageNumber) > $pageCount) $pageNumber = 1;
						echo $LANG['PAGE'] . '<b> ' . ($pageNumber) . '</b> ' . $LANG['OF'] . ' <b>' . $pageCount . '</b> : ';
						//sliding window
						echo '<a href="datasetmanager.php?datasetid=' . $datasetId . '&pagenumber=1" class="pagination-link" data-keep-link="1">';
						echo (1);
						echo '</a>';
						echo '<span class="pagination-link"> ...</span>';
						$beginning = max(2, $pageNumber - 5);
						$end = min($pageNumber, min($pageCount + max(1, $pageNumber - 5), ($maxNumberOfPagesBeforeShowPageJump / 2) + max(1, $pageNumber - 5)));
						for ($x = $beginning; $x < $end; $x++) {
							//first x
							// for ($x = 1; $x < min($pageNumber, $maxNumberOfPagesBeforeShowPageJump / 2); $x++) {
							if ($x > 2) echo '<span class="pagination-link" data-even-odd="' . $x . '"> | </span>';
							if (($pageNumber) == $x) echo '<b>';
							else echo '<a href="datasetmanager.php?datasetid=' . $datasetId . '&pagenumber=' . $x . '" class="pagination-link" data-even-odd="' . $x . '">';
							echo ($x);
							if (($pageNumber) == $x) echo '</b>';
							else echo '</a>';
						}
						?>
						<div id="jump-to-page" name="jump-to-page">
							<form action="<?= $sourcePage ?>" method="get" class="gridlike-form" style="margin:0;">
								<div class="gridlike-form-row">
									<input type="hidden" name="datasetid" value="<?= $datasetId ?>">
									<label class="screen-reader-only" for="jumpToPage">Jump to Page:</label>
									<input placeholder="Jump to Page" type="number" id="jumpToPage" name="pagenumber" min="1" max="<?= $pageCount ?>" required style="margin:0; min-width:9vw;">
									<button type="submit">Go</button>
								</div>
							</form>
						</div>
						<?php
						// sliding window
						$theLastX = $pageCount - ($maxNumberOfPagesBeforeShowPageJump / 2);
						$beginning = max($pageNumber + 1, min($pageNumber + 1, $theLastX));
						$end = min($pageNumber + 1 + ($maxNumberOfPagesBeforeShowPageJump / 2), $pageCount - 1);
						for ($x = $beginning; $x <= $end; $x++) {
							// last x
							// for ($x = max($pageCount - ($maxNumberOfPagesBeforeShowPageJump / 2), $pageNumber) + 1; $x <= $pageCount; $x++) {
							if ($x > 1) echo '<span class="pagination-link" data-even-odd="' . $x . '"> | </span>';
							if (($pageNumber) == $x) echo '<b>';
							else echo '<a href="' . $sourcePage . '?datasetid=' . $datasetId . '&pagenumber=' . $x . '" class="pagination-link" data-even-odd="' . $x . '">';
							echo ($x);
							if (($pageNumber) == $x) echo '</b>';
							else echo '</a>';
						}
						echo '<span class="pagination-link"> | ...</span>';
						echo '<a href="' . $sourcePage . '?datasetid=' . $datasetId . '&pagenumber=' . $pageCount . '" class="pagination-link" data-keep-link="' . $pageCount . '">';
						echo ($pageCount);
						echo '</a>';
						?>
					</div>
					<div style="text-align: right;flex-grow: 0;">
						<?= '<b>' . $LANG['COUNT'] . ': <span id="rec-cnt">' . $recordCount . '</span> ' . $LANG['RECORDS'] . '</b>' ?>
					</div>
				</div>
				<form name="occurform" action="datasetmanager.php" method="post" onsubmit="return validateOccurForm(this)">
					<input name="datasetid" type="hidden" value="<?= $datasetId ?>" >
					<input name="targetid" type="hidden" value="" >
					<input name="submitaction" type="hidden" value="" >
					<table class="styledtable" style="font-size:12px;">
						<tr>
							<?php
							if($isEditor > 1){
								?>
								<th><input name="" value="" type="checkbox" onclick="selectAll(this);" title="<?= $LANG['SEL_DESEL_SPCS'] ?>" /></th>
								<?php
							}
							?>
							<th><?= $LANG['CAT_NUM'] ?></th>
							<?php
							if($isEditor > 1){
								?>
								<th><?= $LANG['DATASET_NOTES'] ?></th>
								<?php
							}
							?>
							<th><?= $LANG['COLLECTOR'] ?></th>
							<th><?= $LANG['SCI_NAME'] ?></th>
							<th><?= $LANG['LOCALITY'] ?></th>
						</tr>
						<?php
						$trCnt = 0;
						foreach ($occurDetailArr as $occid => $recArr) {
							$trCnt++;
							$catNum = $recArr['catnum'];
							if(!$catNum) $catNum = $LANG['UNDEFINED'];
							?>
							<tr id="tr-<?= $occid ?>" data-occid="<?= $occid ?>" <?= ($trCnt % 2 ? 'class="alt"' : '') ?>>
								<?php
								if($isEditor > 1){
									$removeTitleStr = 'Unlink occurrence from dataset';
									$editTitleStr = 'Edit notes for occurrence ' . $catNum;
									?>
									<td>
										<div><input type="checkbox" name="occid[]" value="<?= $occid ?>" /></div>
										<div aria-label="<?= $removeTitleStr ?>" title="<?= $removeTitleStr ?>">
											<a href="#" class="remove-link-btn"><img src="<?= $CLIENT_ROOT ?>/images/del.png" class="icon-img"></a>
										</div>
										<div aria-label="<?= $editTitleStr ?>" title="<?= $editTitleStr ?>">
											<a href="#" class="edit-notes-btn"><img src="<?= $CLIENT_ROOT ?>/images/edit.png" class="icon-img"></a>
										</div>
									</td>
									<?php
								}
								?>
								<td>
									<a href="#" onclick="openIndPopup(<?= $occid ?>); return false;" class="cat-num"><?= $catNum ?></a>
								</td>
								<?php
								if($isEditor > 1){
									?>
									<td class="occurrence-notes"><?= $recArr['notes'] ?></td>
									<?php
								}
								?>
								<td>
									<?= $recArr['coll'] ?>
								</td>
								<td>
									<?= $recArr['sciname'] ?>
								</td>
								<td>
									<?= $recArr['loc'] ?>
								</td>
							</tr>
							<?php
						}
						?>
					</table>
				</form>
				<?php
				if($isEditor > 1){
					?>
					<dialog id="notesDialog" class="modal-form">
						<form id="occurrenceNotesForm">
							<div class="dialog-header">
								<h3>Edit Occurrence Notes (<span id="notesRecordLabel"></span>)</h3>
							</div>
							<div class="dialog-body">
								<input type="hidden" name="occid" id="occidInput">
								<label for="notesInput">Notes</label>
								<textarea id="notesInput" name="notes" rows="8" style="width:900px;"></textarea>
								<div id="notesError" class="dialog-error" role="alert"></div>
							</div>
							<div class="dialog-footer">
								<button type="submit" id="saveNotesButton" style="display:inline">Save</button>
								<button type="button" id="cancelNotesButton" style="display:inline">Cancel</button>
							</div>
						</form>
					</dialog>
					<?php
				}
			} else {
				?>
				<div style="font-weight:bold; margin:15px"><?= $LANG['NO_OCCS_DS'] ?></div>
				<?php
			}
		} else echo '<div><b>' . $LANG['DS_NOT_IDENTIFIED'] . '</b></div>';
		?>
	</div>
</body>
</html>
