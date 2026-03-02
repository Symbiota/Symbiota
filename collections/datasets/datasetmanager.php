<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/OccurrenceDataset.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load('collections/datasets/datasetmanager');

header("Content-Type: text/html; charset=" . $CHARSET);

if (!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/datasets/datasetmanager.php?' . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$datasetId = $_REQUEST['datasetid'];
$tabIndex = array_key_exists('tabindex', $_REQUEST) ? Sanitize::int($_REQUEST['tabindex']) : 0;
$pageNumber = array_key_exists('pagenumber', $_REQUEST) ? Sanitize::int($_REQUEST['pagenumber']) : 1;
$action = array_key_exists('submitaction', $_REQUEST) ? $_REQUEST['submitaction'] : '';

$datasetManager = new OccurrenceDataset();

$mdArr = $datasetManager->getDatasetMetadata($datasetId);
$role = '';
$roleLabel = '';
$isEditor = 0;
if ($SYMB_UID == $mdArr['uid']) {
	$isEditor = 1;
	$role = 'owner';
} elseif (isset($mdArr['roles'])) {
	if (in_array('DatasetAdmin', $mdArr['roles'])) {
		$isEditor = 1;
		$role = $LANG['ADMINISTRATOR'];
	} elseif (in_array('DatasetEditor', $mdArr['roles'])) {
		$isEditor = 2;
		$role = $LANG['EDITOR'];
		$roleLabel = $LANG['ROLE_LABEL_EDITOR'];
	} elseif (in_array('DatasetReader', $mdArr['roles'])) {
		$isEditor = 3;
		$role = $LANG['READ_ACCESS'];
	}
} elseif ($IS_ADMIN) {
	$isEditor = 1;
	$role = $LANG['SUPERADMIN'];
}

$statusStr = '';
if ($isEditor) {
	if ($isEditor < 3) {
		if ($action == 'Remove Selected Occurrences') {
			if ($datasetManager->removeSelectedOccurrences($datasetId, $_POST['occid'])) {
				//$statusStr = 'Selected occurrences removed successfully';
			} else {
				$statusStr = implode(',', $datasetManager->getErrorArr());
			}
		}
	}
	if ($isEditor == 1) {
		if ($action == 'Save Edits') {
			$isPublic = (isset($_POST['ispublic']) && is_numeric($_POST['ispublic']) ? 1 : 0);
			if ($datasetManager->editDataset($_POST['datasetid'], $_POST['name'], $_POST['notes'], $_POST['description'], $isPublic)) {
				$mdArr = $datasetManager->getDatasetMetadata($datasetId);
				$statusStr = $LANG['DS_EDITS_SAVED'];
			} else {
				$statusStr = implode(',', $datasetManager->getErrorArr());
			}
		} elseif ($action == 'Merge') {
			if ($datasetManager->mergeDatasets($_POST['dsids[]'])) {
				$statusStr = $LANG['DS_MERGED'];
			} else {
				$statusStr = implode(',', $datasetManager->getErrorArr());
			}
		} elseif ($action == 'Clone (make copy)') {
			if ($datasetManager->cloneDatasets($_POST['dsids[]'])) {
				$statusStr = $LANG['DS_CLONED'];
			} else {
				$statusStr = implode(',', $datasetManager->getErrorArr());
			}
		} elseif ($action == 'Delete Dataset') {
			if ($datasetManager->deleteDataset($_POST['datasetid'])) {
				header('Location: index.php');
			} else {
				$statusStr = implode(',', $datasetManager->getErrorArr());
			}
		} elseif ($action == 'addUser') {
			if ($datasetManager->addUser($datasetId, $_POST['uid'], $_POST['role'])) {
				$statusStr = $LANG['USER_ADDED'];
			} else {
				$statusStr = implode(',', $datasetManager->getErrorArr());
			}
		} elseif ($action == 'DelUser') {
			if ($datasetManager->deleteUser($datasetId, $_POST['uid'], $_POST['role'])) {
				$statusStr = $LANG['USER_REMOVED'];
			} else {
				$statusStr = implode(',', $datasetManager->getErrorArr());
			}
		}
	}
}
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET ?>">
	<title><?= $DEFAULT_TITLE . ' ' . $LANG['DS_OCC_MANAGER'] ?></title>
	<link href="<?= $CSS_BASE_PATH ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT . '/includes/head.php');
	?>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../js/symb/shared.js"></script>
	<script type="text/javascript" src="../../js/tinymce/tinymce.min.js"></script>
	<script type="text/javascript">
		// Adds WYSIWYG editor to description field
		tinymce.init({
			selector: '#description',
			plugins: 'link lists image',
			menubar: '',
			toolbar: ['undo redo | bold italic underline | link | alignleft aligncenter alignright | formatselect | bullist numlist | indent outdent | blockquote | image'],
			branding: false,
			default_link_target: "_blank",
			paste_as_text: true
		});
	</script>
	<script type="text/javascript">
		var isDownloadAction = false;
		$(document).ready(function() {
			var dialogArr = new Array("schemanative", "schemadwc");
			var dialogStr = "";
			for (i = 0; i < dialogArr.length; i++) {
				dialogStr = dialogArr[i] + "info";
				$("#" + dialogStr + "dialog").dialog({
					autoOpen: false,
					modal: true,
					position: {
						my: "left top",
						at: "center",
						of: "#" + dialogStr
					}
				});

				$("#" + dialogStr).click(function() {
					$("#" + this.id + "dialog").dialog("open");
				});
			}

			$('#tabs').tabs({
				active: <?= $tabIndex ?>,
				beforeLoad: function(event, ui) {
					$(ui.panel).html("<p><?= $LANG['LOADING'] ?>...</p>");
				}
			});

			$("#userinput").autocomplete({
				source: "../rpc/getuserlist.php",
				minLength: 3,
				autoFocus: true,
				select: function(event, ui) {
					$('#uid-add').val(ui.item.id);
				}
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

		function validateEditForm(f) {
			if (f.name.value == '') {
				alert("<?= $LANG['DS_NOT_NULL'] ?>");
				return false;
			}
			return true;
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

		function validateUserAddForm(f) {
			if (f.uid.value == "") {
				alert("<?= $LANG['SEL_USER_LIST'] ?>");
				return false;
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
		.section-title {
			margin: 0px 15px;
			font-weight: bold;
			text-decoration: underline;
		}
	</style>
</head>

<body>
	<?php
	$displayLeftMenu = (isset($collections_datasets_indexMenu) ? $collections_datasets_indexMenu : false);
	include($SERVER_ROOT . '/includes/header.php');
	?>
	<div class='navpath'>
		<a href='../../index.php'><?= $LANG['HOME'] ?></a> &gt;&gt;
		<a href="../../profile/viewprofile.php?tabindex=1"><?= $LANG['MY_PROF'] ?></a> &gt;&gt;
		<a href="index.php">
			<?= $LANG['RETURN_DS_LISTING'] ?>
		</a> &gt;&gt;
		<b><?= $LANG['DS_MANAGER'] ?></b>
	</div>
	<!-- This is inner text! -->
	<div role="main" id="innertext">
		<h1 class="page-heading"><?= $LANG['DS_OCC_MANAGER'] ?></h1>
		<?php
		if ($statusStr) {
			$color = 'green';
			if (strpos($statusStr, $LANG['ERROR']) !== false) $color = 'red';
			elseif (strpos($statusStr, $LANG['WARNING']) !== false) $color = 'orange';
			elseif (strpos($statusStr, $LANG['NOTICE']) !== false) $color = 'yellow';
			echo '<div style="margin:15px;color:' . $color . ';">';
			echo $statusStr;
			echo '</div>';
		}
		if ($datasetId) {
			echo '<div style="margin:10px 0px 5px 20px;font-weight:bold;font-size:130%;">' . $mdArr['name'] . '</div>';
			if ($role) echo '<div style="margin-left:20px" title="' . $LANG['ROLE'] . '"' . $roleLabel . '>' . $LANG['ROLE'] . ': ' . $role . '</div>';
			if ($isEditor) {
				?>
				<div id="tabs" style="margin:10px;">
					<ul>
						<li><a href="#occurtab"><span><?= $LANG['OCC_LIST'] ?></span></a></li>
						<?php
						if ($isEditor == 1) {
							?>
							<li><a href="#admintab"><span><?= $LANG['GEN_MANAGEMENT'] ?></span></a></li>
							<li><a href="#accesstab"><span><?= $LANG['USER_ACCESS'] ?></span></a></li>
							<?php
						}
						?>
					</ul>
					<div id="occurtab">
						<?php
						$retLimit = 200;
						$maxNumberOfPagesBeforeShowPageJump = 10;
						if ($occArr = $datasetManager->getOccurrences($datasetId, $pageNumber, $retLimit)) {
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
										<form action="datasetmanager.php" method="get" class="gridlike-form" style="margin:0;">
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
										else echo '<a href="datasetmanager.php?datasetid=' . $datasetId . '&pagenumber=' . $x . '" class="pagination-link" data-even-odd="' . $x . '">';
										echo ($x);
										if (($pageNumber) == $x) echo '</b>';
										else echo '</a>';
									}
									echo '<span class="pagination-link"> | ...</span>';
									echo '<a href="datasetmanager.php?datasetid=' . $datasetId . '&pagenumber=' . $pageCount . '" class="pagination-link" data-keep-link="' . $pageCount . '">';
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
										?>
										<tr <?= ($trCnt % 2 ? 'class="alt"' : '') ?>>
											<td>
												<input type="checkbox" name="occid[]" value="<?= $occid ?>" />
											</td>
											<td>
												<?= '<a href="#" onclick="openIndPopup(' . $occid . '); return false;">' . $recArr['catnum'] . '</a>' ?>
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
									if ($occArr && $isEditor < 3) {
										?>
										<button type="submit" name="submitaction" value="Remove Selected Occurrences"><?= $LANG['REM_SEL_OCCS'] ?></button>
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
						?>
					</div>
					<?php
					if ($isEditor == 1) {
						?>
						<div id="admintab">
							<section class="fieldset-like">
								<h2><span><b><?= $LANG['EDITOR'] ?></b></span></h2>
								<form name="editform" action="datasetmanager.php" method="post" onsubmit="return validateEditForm(this)">
									<div>
										<label for="name"><?= $LANG['NAME'] ?></label>
										<input name="name" id="name" type="text" value="<?= $mdArr['name'] ?>" aria-label="<?= $LANG['NAME'] ?>" style="width:70%" />
									</div>
									<div>
										<p>
											<input type="checkbox" name="ispublic" id="ispublic" value="1" aria-label="<?= $LANG['PUB_VISIBLE'] ?>" <?= ($mdArr['ispublic'] ? 'CHECKED' : '') ?> />
											<!-- <b><?= $LANG['PUB_VISIBLE'] ?></b> -->
											<label for="ispublic"><?= $LANG['PUB_VISIBLE'] ?></label>
										</p>
									</div>
									<div>
										<label for="notes"><?= $LANG['NOTES_INTERNAL'] ?></label>
										<input name="notes" id="notes" type="text" value="<?= $mdArr['notes'] ?>" style="width:70%" aria-label="<?= $LANG['NOTES_INTERNAL'] ?>" />
									</div>
									<div>
										<label for="description"><?= $LANG['DESCRIPTION'] ?></label>
										<textarea name="description" id="description" cols="100" rows="10" style="width: 70%;" aria-label="<?= $LANG['DESCRIPTION'] ?>"><?= $mdArr['description'] ?></textarea>
									</div>
									<div style="margin:15px;">
										<input name="tabindex" type="hidden" value="1" />
										<input name="datasetid" type="hidden" value="<?= $datasetId ?>" />
										<button name="submitaction" type="submit" value="Save Edits"><?= $LANG['SAVE_EDITS'] ?></button>
									</div>
								</form>
							</section>
							<section class="fieldset-like">
								<h2><span><b><?= $LANG['DEL_DS'] ?></b></span></h2>
								<form name="editform" action="datasetmanager.php" method="post" onsubmit="return confirm('<?= $LANG['SURE_DEL_DS_PERM'] ?>')">
									<div style="margin:15px;">
										<input name="datasetid" type="hidden" value="<?= $datasetId ?>" />
										<input name="tabindex" type="hidden" value="1" />
										<button class="button-danger" name="submitaction" type="submit" value="Delete Dataset"><?= $LANG['DEL_DS'] ?></button>
									</div>
								</form>
							</section>
						</div>
						<div id="accesstab">
							<div style="margin:25px 10px;">
								<?php
								$userArr = $datasetManager->getUsers($datasetId);
								$roleArr = array('DatasetAdmin' => 'Full Access Users', 'DatasetEditor' => 'Read/Write Users', 'DatasetReader' => 'Read Only Users');
								foreach ($roleArr as $roleStr => $labelStr) {
									?>
									<div class="section-title"><?= $labelStr ?></div>
									<div style="margin:15px;">
										<?php
										if (array_key_exists($roleStr, $userArr)) {
											?>
											<ul>
												<?php
												$uArr = $userArr[$roleStr];
												foreach ($uArr as $uid => $name) {
													?>
													<li>
														<?= $name ?>
														<form name="deluserform" method="post" action="datasetmanager.php" style="display:inline;" onsubmit="return confirm('<?= $LANG['SURE_REM_USER'] . ' ' . $name . '?' ?>')">
															<input name="submitaction" type="hidden" value="DelUser" />
															<input name="role" type="hidden" value="<?= $roleStr ?>" />
															<input name="uid" type="hidden" value="<?= $uid ?>" />
															<input name="datasetid" type="hidden" value="<?= $datasetId ?>" />
															<input name="tabindex" type="hidden" value="2" />
															<input name="submitimage" type="image" src="../../images/drop.png" style="width:1.2em" alt="<?= $LANG['DROP_ICON'] ?>" />
														</form>
													</li>
													<?php
												}
												?>
											</ul>
											<?php
										} else echo '<div style="margin:15px;">' . $LANG['NONE_ASSIGNED'] . '</div>';
										?>
									</div>
									<?php
								}
								?>
							</div>
							<div style="margin:15px;">
								<section class="fieldset-like">
									<h2><span><b><?= $LANG['ADD_USER'] ?></b></span></h2>
									<form name="addform" action="datasetmanager.php" method="post" onsubmit="return validateUserAddForm(this)">
										<div title="<?= $LANG['TYPE_LOGIN'] ?>">
											<?= $LANG['LOGIN_NAME'] ?>:
											<input id="userinput" type="text" style="width:400px;" aria-label="<?= $LANG['LOGIN_NAME'] ?>" />
											<input id="uid-add" name="uid" type="hidden" value="" />
										</div>
										<label for="role"><?= $LANG['ROLE'] ?>:</label>
										<select name="role" id="role">
											<option value="DatasetAdmin"><?= $LANG['FULL_ACCESS'] ?></option>
											<option value="DatasetEditor"><?= $LANG['READ_WRITE_ACCESS'] ?></option>
											<option value="DatasetReader"><?= $LANG['READ_ACCESS'] ?></option>
										</select>
										<div style="margin:10px;">
											<input name="tabindex" type="hidden" value="2" />
											<input name="datasetid" type="hidden" value="<?= $datasetId ?>" />
											<button type="submit" name="submitaction" value="addUser"><?= $LANG['ADD_USER'] ?></button>
										</div>
									</form>
								</section>
							</div>
						</div>
						<?php
					}
					?>
				</div>
				<?php
			} else echo '<div style="margin:30px">' . $LANG['NOT_AUTH'] . '</div>';
		} else echo '<div><b>' . $LANG['DS_NOT_IDENTIFIED'] . '</b></div>';
		?>
	</div>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>
</html>
