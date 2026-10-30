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

	$isEditor = 0;
	if ($SYMB_UID == $mdArr['uid']) {
		$isEditor = 1;
	}
	elseif($roles = $datasetManager->getUserRoles($datasetId, $SYMB_UID)) {
		if (isset($roles[$datasetId][$SYMB_UID]['roles']['DatasetAdmin'])) {
			$isEditor = 1;
		} elseif (isset($roles[$datasetId][$SYMB_UID]['roles']['DatasetEditor'])) {
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
		var isDownloadAction = false;

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

		function validateDataSetForm(f) {
			var dbElements = document.getElementsByName("dsids[]");
			for (i = 0; i < dbElements.length; i++) {
				var dbElement = dbElements[i];
				if (dbElement.checked) return true;
			}
			alert("<?= $LANG['PLS_SELECT_DS'] ?>");

			var confirmStr = '';
			if (f.submitaction.value == "Merge") {
				confirmStr = '<?= $LANG['SURE_MERGE_DS'] ?>';
			} else if (f.submitaction.value == "Clone (make copy)") {
				confirmStr = '<?= $LANG['SURE_CLONE_DS'] ?>';
			} else if (f.submitaction.value == "Delete") {
				confirmStr = '<?= $LANG['SURE_DEL_DS'] ?>';
			}
			if (confirmStr == '') return true;
			return confirm(confirmStr);
		}

		function validateOccurForm(f) {
			var occidChecked = false;
			var dbElements = document.getElementsByName("occid[]");
			for (i = 0; i < dbElements.length; i++) {
				var dbElement = dbElements[i];
				if (dbElement.checked) {
					occidChecked = true;
					break;
				}
			}
			if (!occidChecked) {
				alert("<?= $LANG['PLS_SEL_SPC'] ?>");
				return false;
			}
			if (isDownloadAction) {
				f.action = "../download/index.php";
				targetDownloadPopup(f);
			}
			return true;
		}

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
	</script>
	<style>
	</style>
</head>
<body>
	<div id="occurtab">
		<?php
		if ($datasetId) {
			$targetPage = 'index.php';
			if(!empty($_REQUEST['targetpage']) && $_REQUEST['targetpage'] == 'dsmanager') $targetPage = 'datasetmanager.php';
			$retLimit = 200;
			$maxNumberOfPagesBeforeShowPageJump = 10;
			if ($occArr = $datasetManager->getOccurrenceDetails($datasetId, $pageNumber, $retLimit)) {
				$recordCount = $datasetManager->getOccurrenceCount($datasetId);
				?>
				<div class="gridlike-form-row" style="justify-content: space-between;padding-bottom: 5px;">
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
							<form action="<?= $targetPage ?>" method="get" class="gridlike-form" style="margin:0;">
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
							else echo '<a href="' . $targetPage . '?datasetid=' . $datasetId . '&pagenumber=' . $x . '" class="pagination-link" data-even-odd="' . $x . '">';
							echo ($x);
							if (($pageNumber) == $x) echo '</b>';
							else echo '</a>';
						}
						echo '<span class="pagination-link"> | ...</span>';
						echo '<a href="' . $targetPage . '?datasetid=' . $datasetId . '&pagenumber=' . $pageCount . '" class="pagination-link" data-keep-link="' . $pageCount . '">';
						echo ($pageCount);
						echo '</a>';
						?>
					</div>
					<div style="text-align: right;flex-grow: 0;">
						<?= '<b>' . $LANG['COUNT'] . ': ' . $recordCount . ' ' . $LANG['RECORDS'] . '</b>' ?>
					</div>
				</div>
				<form name="occurform" action="datasetmanager.php" method="post" onsubmit="return validateOccurForm(this)">
					<table class="styledtable" style="font-size:12px;">
						<tr>
							<th><input name="" value="" type="checkbox" onclick="selectAll(this);" title="<?= $LANG['SEL_DESEL_SPCS'] ?>" /></th>
							<th><?= $LANG['CAT_NUM'] ?></th>
							<th><?= $LANG['COLLECTOR'] ?></th>
							<th><?= $LANG['SCI_NAME'] ?></th>
							<th><?= $LANG['LOCALITY'] ?></th>
						</tr>
						<?php
						$trCnt = 0;
						foreach ($occArr as $occid => $recArr) {
							$trCnt++;
							$catNum = $recArr['catnum'];
							if(!$catNum) $catNum = $LANG['UNDEFINED'];
							?>
							<tr <?= ($trCnt % 2 ? 'class="alt"' : '') ?>>
								<td>
									<input type="checkbox" name="occid[]" value="<?= $occid ?>" />
								</td>
								<td>
									<a href="#" onclick="openIndPopup(<?= $occid ?>); return false;"><?= $catNum ?></a>
								</td>
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
					<div style="margin: 15px;">
						<input name="datasetid" type="hidden" value="<?= $datasetId ?>" />
						<?php
						if ($occArr && $isEditor < 3 && $targetPage != 'index.php') {
							?>
							<button type="submit" name="submitaction" value="deleteOccurrences"><?= $LANG['REM_SEL_OCCS'] ?></button>
							<?php
						}
						?>
					</div>
				</form>
				<div style="margin: 15px;">
					<form name="exportAllForm" action="../download/index.php" method="post" onsubmit="targetDownloadPopup(this)">
						<input name="searchvar" type="hidden" value="datasetid=<?= $datasetId ?>" />
						<input name="dltype" type="hidden" value="specimen" />
						<button type="submit" name="submitaction" value="exportAll"><?= $LANG['EXPORT_DS'] ?></button>
					</form>
				</div>
				<?php
			} else {
				?>
				<div style="font-weight:bold; margin:15px"><?= $LANG['NO_OCCS_DS'] ?></div>
				<div style="margin:15px"><?= $LANG['LINK_OCCS_VIA'] . ' <a href="../index.php">' . $LANG['OCC_SEARCH'] . '</a> ' . $LANG['OR_VIA_OCC_PROF'] ?></div>
				<?php
			}
		} else echo '<div><b>' . $LANG['DS_NOT_IDENTIFIED'] . '</b></div>';
		?>
	</div>
</body>
</html>
