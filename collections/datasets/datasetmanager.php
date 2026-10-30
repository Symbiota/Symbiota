<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/OccurrenceDataset.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load('collections/datasets/datasetmanager');

header("Content-Type: text/html; charset=" . $CHARSET);

if (!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/datasets/datasetmanager.php?' . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$datasetId = array_key_exists('datasetid', $_REQUEST) ? Sanitize::int($_REQUEST['datasetid']) : 0;
$tabIndex = array_key_exists('tabindex', $_REQUEST) ? Sanitize::int($_REQUEST['tabindex']) : 0;
$pageNumber = array_key_exists('pagenumber', $_REQUEST) ? Sanitize::int($_REQUEST['pagenumber']) : 1;
$action = array_key_exists('submitaction', $_REQUEST) ? $_REQUEST['submitaction'] : '';

$datasetManager = new OccurrenceDataset();
$statusStr = '';
if($action == 'createNewDataset'){
	if($IS_ADMIN || array_key_exists('ClCreate', $USER_RIGHTS)){
		if($datasetManager->insertDataset($_POST)){
			$datasetId = $datasetManager->getDatasetID();
		}
		else{
			$statusStr = $datasetManager->getErrorMessage();
		}
	}
	else {
		$statusStr = "You don't have permission to create a dataset";
	}
}

if($datasetId){
	$datasetManager->setDatasetID($datasetId);
	$mdArr = $datasetManager->getDatasetMetadata(false);

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

	if ($isEditor) {
		if ($action == 'deleteOccurrences') {
			if(!$datasetManager->deleteOccurrences($_POST['occid'])) {
				$statusStr = count($_POST['occid']) . ' ' . $LANG['OCCUR_REMOVED'];
			} else {
				$statusStr = $datasetManager->getErrorMessage();
			}
		}
		if ($isEditor == 1) {
			if ($action == 'Save Edits') {
				if($datasetManager->updateDataset($_POST)){
					$mdArr = $datasetManager->getDatasetMetadata(false);
					$statusStr = $LANG['DS_EDITS_SAVED'];
				}
				else{
					$statusStr = $datasetManager->getErrorMessage();
				}
			} elseif ($action == 'transferOccurrences') {
				if ($datasetManager->transferOccurrences($_POST['targetDatasetID'])) {
					$statusStr = $LANG['DS_MERGED'];
				} else {
					$statusStr = $datasetManager->getErrorMessage();
				}
			} elseif ($action == 'cloneDataset') {
				if ($datasetManager->cloneDataset()) {
					$statusStr = $LANG['DS_CLONED'];
				} else {
					$statusStr = $datasetManager->getErrorMessage();
				}
			} elseif ($action == 'Delete Dataset') {
				if ($datasetManager->deleteDataset()) {
					header('Location: index.php');
				} else {
					$statusStr = $datasetManager->getErrorMessage();
				}
			} elseif ($action == 'addUser') {
				if ($datasetManager->insertUserRole($_POST['uid'], $_POST['role'])) {
					$statusStr = $LANG['USER_ADDED'];
				} else {
					$statusStr = $datasetManager->getErrorMessage();
				}
			} elseif ($action == 'DelUser') {
				if ($datasetManager->deleteUserRole($_POST['uid'], $_POST['role'])) {
					$statusStr = $LANG['USER_REMOVED'];
				} else {
					$statusStr = $datasetManager->getErrorMessage();
				}
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
		$(document).ready(function() {
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
		});

		function validateUserAddForm(f) {
			if (f.uid.value == "") {
				alert("<?= $LANG['SEL_USER_LIST'] ?>");
				return false;
			}
			return true;
		}
	</script>
	<style>
		.section-title {
			margin: 0px 15px;
			font-weight: bold;
			text-decoration: underline;
		}
		.split-div{
			display: flex;
			justify-content: space-between;
			width: 100%;
			align-items: flex-end;
			padding: 0px 20px;
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
		<a href="index.php"><?= $LANG['PUB_DAT_LIST'] ?></a> &gt;&gt;
		<a href="datasetuserlist.php"><?= $LANG['USER_LIST'] ?></a> &gt;&gt;
		<b><?= $LANG['DS_MANAGER'] ?></b>
	</div>
	<div role="main" id="innertext">
		<h1 class="page-heading screen-reader-only"><?= $LANG['DS_OCC_MANAGER'] ?></h1>
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
			if ($isEditor) {
				$role = '';
				if($isEditor == 1){
					$role = $LANG['DS_ADMIN'];
					if($SYMB_UID == $mdArr['uid']) $role = ' (' . $LANG['OWNER'] . ')';
				}
				elseif($isEditor == 2) $role = $LANG['DS_EDITOR'];
				?>
				<div class="split-div">
					<div>
						<h2 style="margin-bottom:10px;"><?= $mdArr['datasetName'] ?></h2>
						<div title="<?= $LANG['ROLE'] ?>"><?= $LANG['ROLE'] . ': ' . $role ?></div>
					</div>
					<div>
						<a href="index.php?datasetid=<?= $datasetId ?>"><?= $LANG['GOTO_PUBLIC_DISPLAY'] ?></a>
					</div>
				</div>
				<div id="tabs" style="margin:10px;">
					<ul>
						<li><a href="datasetoccurlist.php?targetpage=dsmanager&datasetid=<?= $datasetId ?>&pagenumber=<?= $pageNumber ?>"><span><?= $LANG['OCC_LIST'] ?></span></a></li>
						<?php
						if ($isEditor == 1) {
							?>
							<li><a href="#admintab"><span><?= $LANG['GEN_MANAGEMENT'] ?></span></a></li>
							<li><a href="#accesstab"><span><?= $LANG['USER_ACCESS'] ?></span></a></li>
							<?php
						}
						?>
					</ul>
					<?php
					if ($isEditor == 1) {
						?>
						<div id="admintab">
							<section class="fieldset-like">
								<h2><span><b><?= $LANG['EDITOR'] ?></b></span></h2>
								<form name="editform" action="datasetmanager.php" method="post">
									<div>
										<label for="datasetName"><?= $LANG['NAME'] ?></label>
										<input name="datasetName" id="datasetName" type="text" value="<?= $mdArr['datasetName'] ?>" aria-label="<?= $LANG['NAME'] ?>" style="width:70%" required />
									</div>
									<div>
										<span>
											<input type="radio" name="isPublic" id="isPrivate" value="0" aria-label="<?= $LANG['PRIVATE'] ?>" <?= (empty($mdArr['isPublic']) ? 'CHECKED' : '') ?> />
											<label for="isPrivate"><?= $LANG['PRIVATE'] ?></label>
										</span>
										<span>
											<input type="radio" name="isPublic" id="isPublic" value="1" aria-label="<?= $LANG['PUB_VISIBLE'] ?>" <?= ($mdArr['isPublic'] ? 'CHECKED' : '') ?> />
											<label for="isPublic"><?= $LANG['PUB_VISIBLE'] ?></label>
										</span>
									</div>
									<div>
										<label for="description"><?= $LANG['DESCRIPTION'] ?></label>
										<textarea name="description" id="description" cols="100" rows="10" style="width: 100%;" aria-label="<?= $LANG['DESCRIPTION'] ?>"><?= $mdArr['description'] ?></textarea>
									</div>
									<div>
										<label for="bibliographicCitation"><?= $LANG['CITATION'] ?></label>
										<input name="bibliographicCitation" id="bibliographicCitation" type="text" value="<?= $mdArr['bibliographicCitation'] ?>" style="width:100%" aria-label="<?= $LANG['CITATION'] ?>" />
									</div>
									<div>
										<label for="category"><?= $LANG['CATEGORY'] ?></label>
										<input name="category" id="category" type="text" value="<?= $mdArr['category'] ?>" style="width:350px" aria-label="<?= $LANG['CATEGORY'] ?>" />
									</div>
									<div>
										<label for="datasetIdentifier"><?= $LANG['DATASET_IDENTIFIER'] ?></label>
										<input name="datasetIdentifier" id="dynamicProperties" type="text" value="<?= $mdArr['datasetIdentifier'] ?>" style="width:350px" aria-label="<?= $LANG['DATASET_IDENTIFIER'] ?>" />
									</div>
									<div>
										<label for="notes"><?= $LANG['NOTES_INTERNAL'] ?></label>
										<input name="notes" id="notes" type="text" value="<?= $mdArr['notes'] ?>" style="width:100%" aria-label="<?= $LANG['NOTES_INTERNAL'] ?>" />
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
								$userArr = array();
								$userRoleArr = $datasetManager->getUserRoles($datasetId);
								if($userRoleArr) $userArr = $userRoleArr[$datasetId];
								$roleArr = array('DatasetAdmin' => 'DS_ADMIN', 'DatasetEditor' => 'DS_EDITOR', 'DatasetReader' => 'DS_READER');
								foreach ($roleArr as $roleStr => $labelStr) {
									?>
									<div class="section-title"><?= $LANG[$labelStr] ?></div>
									<div style="margin:15px;">
										<?php
										$hasUser = false;
										foreach($userArr as $userID => $userDetailArr) {
											?>
											<ul>
												<?php
												if(array_key_exists($roleStr, $userDetailArr['roles'])) {
													$hasUser = true;
													$confirmMessage = $LANG['SURE_REM_USER'] . ' ' . $userDetailArr['name'] . '?';
													?>
													<li>
														<?= $userDetailArr['name'] ?>
														<form name="deluserform" method="post" action="datasetmanager.php" style="display:inline;" onsubmit="return confirm('<?= $confirmMessage ?>')">
															<input name="submitaction" type="hidden" value="DelUser" />
															<input name="role" type="hidden" value="<?= $roleStr ?>" />
															<input name="uid" type="hidden" value="<?= $userID ?>" />
															<input name="datasetid" type="hidden" value="<?= $datasetId ?>" />
															<input name="tabindex" type="hidden" value="2" />
															<input name="submitimage" type="image" src="../../images/drop.png" style="width:1em; margin:0px" alt="<?= $LANG['DROP_ICON'] ?>" />
														</form>
													</li>
													<?php
												}
												?>
											</ul>
											<?php
										}
										if(!$hasUser) echo '<div style="margin:15px;">' . $LANG['NONE_ASSIGNED'] . '</div>';
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
											<option value="DatasetAdmin"><?= $LANG['DS_ADMIN'] ?></option>
											<option value="DatasetEditor"><?= $LANG['DS_EDITOR'] ?></option>
											<option value="DatasetReader"><?= $LANG['DS_READER'] ?></option>
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
