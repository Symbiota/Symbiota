<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/OccurrenceCollectionProfile.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load('collections/misc/collmetadata');

header('Content-Type: text/html; charset=' . $CHARSET);

if (!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/misc/collmetadata.php?' . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = array_key_exists('collid', $_REQUEST) ? filter_var($_REQUEST['collid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$tabIndex = array_key_exists('tabindex', $_REQUEST) ? filter_var($_REQUEST['tabindex'], FILTER_SANITIZE_NUMBER_INT) : 0;
$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';

$isEditor = 0;
if ($IS_ADMIN) $isEditor = 1;
elseif ($collid) {
	if (array_key_exists('CollAdmin', $USER_RIGHTS) && in_array($collid, $USER_RIGHTS['CollAdmin'])) {
		$isEditor = 1;
	}
}

$connType = 'readonly';
if($isEditor && ($action || !$collid)) $connType = 'write';
$collManager = new OccurrenceCollectionProfile($connType);
$collManager->setCollid($collid);

$statusStr = '';
if ($isEditor) {
	if ($action == 'saveEdits') {
		$statusStr = $collManager->collectionUpdate($_POST);
		if ($statusStr === true) {
			header('Location: collprofiles.php?collid=' . $collid);
		} else {
			$statusStr = $collManager->getErrorMessage();
		}
	}
	elseif ($action == 'newCollection') {
		if ($IS_ADMIN) {
			if (empty($_POST['collType']))
				$statusStr = '<span style="color:var(--danger-color);">Please select a Dataset Type before submitting.</span>';
			else {
				$newCollid = $collManager->collectionInsert($_POST);
				if ($newCollid) {
					$statusStr = '<span style="color:green">' . $LANG['ADD_SUCCESS'] . '!</span><br/>' .
						$LANG['ADD_STUFF'] . '.';
					$collid = $newCollid;
					$tabIndex = 1;
				}
				else $statusStr = $collManager->getErrorMessage();
			}
		}
	}
	elseif ($action == 'saveResourceLink') {
		if (!$collManager->saveResourceLink($_POST)) $statusStr = $collManager->getErrorMessage();
		$tabIndex = 1;
	}
	elseif ($action == 'saveContact') {
		if (!$collManager->saveContact($_POST)) $statusStr = $collManager->getErrorMessage();
		$tabIndex = 1;
	}
	elseif ($action == 'deleteContact') {
		if (!$collManager->deleteContact($_POST['contactIndex'])) $statusStr = $collManager->getErrorMessage();
		$tabIndex = 1;
	}
	elseif ($action == 'Link Address') {
		if (!$collManager->linkAddress($_POST['iid'])) $statusStr = $collManager->getErrorMessage();
	}
	elseif (array_key_exists('removeiid', $_GET)) {
		if (!$collManager->removeAddress($_GET['removeiid'])) $statusStr = $collManager->getErrorMessage();
	}
}
$collData = current($collManager->getCollectionMetadata());
$collManager->cleanOutArr($collData);
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">

<head>
	<title><?= $DEFAULT_TITLE . ' ' . ($collid ? $collData['collectionname'] : '') . ' ' . $LANG['COL_PROFS'] ?></title>
	<link href="<?= $CSS_BASE_PATH ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT . '/includes/head.php');
	?>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script src="../../js/symb/shared.js?ver=1" type="text/javascript"></script>
	<script type="text/javascript" src="../../js/tinymce/tinymce.min.js"></script>
	<script>
		// Adds WYSIWYG editor to description field
		tinymce.init({
			selector: '#full-description',
			plugins: 'code link lists image',
			menubar: '',
			toolbar: ['undo redo | bold italic underline | link | alignleft aligncenter alignright | formatselect | bullist numlist | indent outdent | blockquote | image | code | charmap'],
			branding: false,
			default_link_target: "_blank",
			paste_as_text: false,
			block_unsupported_drop: true,
			images_file_types: 'jpg,jpeg,png,gif',
			images_upload_url: 'tinymceimagehandler.php',
			a11y_advanced_options: true,
			init_instance_callback: function (editor) {
				var iframeBody = editor.getBody();
				iframeBody.setAttribute('aria-label', <?= json_encode($LANG['TINYMCE_INFO'], JSON_UNESCAPED_UNICODE) ?>);
			}
		});

		$(function() {
			var dialogArr = new Array("instcode", "collcode", "pedits", "pubagg", "rights", "rightsholder", "accessrights", "guid", "colltype", "management", "icon", "collectionid", "sourceurl", "sort");
			var dialogStr = "";
			for (i = 0; i < dialogArr.length; i++) {
				dialogStr = dialogArr[i] + "info";
				$("#" + dialogStr + "dialog").dialog({
					autoOpen: false,
					modal: true,
					position: {
						my: "left",
						at: "center",
						of: "#" + dialogStr
					}
				});
				$("#" + dialogStr).click(function() {
					$("#" + this.id + "dialog").dialog("open");
				});
			}

			$('#tabs').tabs({
				select: function(event, ui) {
					return true;
				},
				active: <?= $tabIndex ?>,
				beforeLoad: function(event, ui) {
					$(ui.panel).html("<?= $LANG['LOADING'] ?>");
				}
			});
		});

		function verifyCollEditForm(f) {
			if (f.managementType && f.managementType.value == "Snapshot") {
				if (f.guidTarget.value == "symbiotaUUID") {
					alert("<?= $LANG['CANNOT_GUID'] ?>");
					return false;
				}
			}
			if (!isNumeric(f.latitudeDecimal.value) || !isNumeric(f.longitudeDecimal.value)) {
				alert("<?= $LANG['NEED_DECIMAL'] ?>");
				return false;
			}
			if (f.rights.value == "") {
				alert("<?= $LANG['NEED_RIGHTS'] ?>");
				return false;
			}
			if (f.sortSeq && !isNumeric(f.sortSeq.value)) {
				alert("<?= $LANG['SORT_NUMERIC'] ?>");
				return false;
			}
			return verifyIconURL(f);
		}

		function managementTypeChanged(selElem) {
			if (selElem.managementType.value == "Live Data") $(".sourceurl-div").hide();
			else $(".sourceurl-div").show();
			checkManagementTypeGuidSource(selElem);
		}

		function checkManagementTypeGuidSource(f) {
			if (f.managementType.value == "Snapshot" && f.guidTarget.value == "symbiotaUUID") {
				alert("<?= $LANG['CANNOT_GUID'] ?>");
				f.guidTarget.value = '';
			} else if (f.managementType.value == "Aggregate" && f.guidTarget.value != "" && f.guidTarget.value != "occurrenceId") {
				alert("<?= $LANG['AGG_GUID'] ?>");
				f.guidTarget.value = 'occurrenceId';
			}
			if (!f.guidTarget.value) f.publishToGbif.checked = false;
		}

		function checkGUIDSource(f) {
			if (f.publishToGbif.checked == true) {
				if (!f.guidTarget.value) {
					alert("<?= $LANG['NEED_GUID'] ?>");
					f.publishToGbif.checked = false;
				}
			}
		}

		function verifyAddAddressForm(f) {
			if (f.iid.value == "") {
				alert("<?= $LANG['SEL_INST'] ?>");
				return false;
			}
			return true;
		}

		function verifyIconImage(f) {
			var iconImageFile = document.getElementById("iconFile").value;
			let extTest = verifyIconFileExt(iconImageFile);
			if(!extTest) return false;
			else{
				var fr = new FileReader;
				fr.onload = function() {
					var img = new Image;
					img.onload = function() {
						if ((img.width > 500) || (img.height > 500)) {
							document.getElementById("iconFile").value = '';
							img = '';
							alert("<?= $LANG['MUST_SMALL'] ?>");
						}
					};
					img.src = fr.result;
				};
				fr.readAsDataURL(document.getElementById("iconFile").files[0]);
			}
		}

		function verifyIconURL(f) {
			let iconImageFile = document.getElementById("iconurl").value;
			return verifyIconFileExt(iconImageFile);
		}

		function verifyIconFileExt(fileName) {
			if(!fileName) return true;
			const ext = fileName.split(".").pop().toLowerCase();
			const approvedExts = ["jpg", "jpeg", "png", "gif"];
			if (!approvedExts.includes(ext)) {
				alert("<?= $LANG['NOT_SUPPORTED'] ?>");
				return false;
			}
			return true;
		}
	</script>
	<style>
		fieldset {
			background-color: #f9f9f9;
			padding: 15px
		}

		legend {
			font-weight: bold;
		}

		.field-block {
			margin: 5px 0px;
		}

		.field-label {}

		.max-width-fit-75 {
			max-width: 90%;
			width: 75rem;
		}

		.url-input{
			max-width: 100%;
			width:600px;
		}
	</style>
</head>

<body>
	<?php
	$displayLeftMenu = (isset($collections_misc_collmetadataMenu) ? $collections_misc_collmetadataMenu : true);
	include($SERVER_ROOT . '/includes/header.php');
	echo '<div class="navpath">';
	echo '<a href="../../index.php">' . $LANG['HOME'] . '</a> &gt;&gt; ';
	if ($collid) {
		echo '<a href="collprofiles.php?collid=' . $collid . '&emode=1">' . $LANG['COL_MGMNT'] . '</a> &gt;&gt; ';
		echo '<b>' . $collData['collectionname'] . ' ' . $LANG['META_EDIT'] . '</b>';
	}
	else echo '<b>' . $LANG['CREATE_COLL'] . '</b>';
	echo '</div>';
	?>
	<div role="main" id="innertext">
		<?php
		if ($statusStr) {
			?>
			<hr />
			<div style="margin:20px;">
				<?= $statusStr ?>
			</div>
			<hr />
			<?php
		}
		?>
		<div id="tabs" style="margin:0px;">
			<?php
			if ($isEditor) {
				if ($collid) echo '<h1 class="page-heading">' . $LANG['EDIT_METADATA'] . ': ' . $collData['collectionname'] . (array_key_exists('institutioncode', $collData) ? ' (' . $collData['institutioncode'] . ')' : '') . '</h1>';
				?>
				<ul>
					<li><a href="#colleditor"><?= $LANG['COL_META_EDIT'] ?></a></li>
					<?php
					if ($collid) echo '<li><a href="collmetaresources.php?collid=' . $collid . '">' . $LANG['CONT_RES'] . '</a></li>';
					?>
				</ul>
				<div id="colleditor">
					<h1 class="page-heading screen-reader-only"><?= $LANG['COLLECTION_METADATA_EDITOR'] ?></h1>
					<section class="fieldset-like">
						<h2> <span> <?= ($collid ? 'Edit' : 'Add New') . ' ' . $LANG['COL_INFO'] ?> </span> </h2>
						<form id="colleditform" name="colleditform" action="collmetadata.php" method="post" enctype="multipart/form-data" onsubmit="return verifyCollEditForm(this)">
							<div class="field-block">
								<span class="field-elem">
									<label for="institutionCode"> <?= $LANG['INST_CODE'] ?>: </label>
									<span class="screen-reader-only">
										<?php
											echo $LANG['NAME_ONE'] . ' ';
										?>
									</span>
									<input id="institutionCode" type="text" name="institutionCode" value="<?= ($collid ? $collData['institutioncode'] : '') ?>" required />
									<a id="instcodeinfo" href="#" onclick="return false" tabindex="0"	>
										<img src="../../images/info.png" style="width:1.2em;" alt="<?= $LANG['MORE_INFO'] ?>" title="<?= $LANG['MORE_INST_CODE'] ?>"/>
									</a>
									<span id="instcodeinfodialog" aria-live="polite">
										<?php
											echo $LANG['NAME_ONE'] . ' ';
											echo '<a href="http://rs.tdwg.org/dwc/terms/index.htm#institutionCode" target="_blank">' . $LANG['DWC_DEF'] . '</a>.';
										?>
									</span>
								</span>
							</div>
							<div class="field-block">
								<span class="field-elem">
									<label for="collectionCode"> <?= $LANG['COLL_CODE'] ?>: </label>
									<span class="screen-reader-only">
										<?php
											echo $LANG['NAME_ACRO'] . ' ';
										?>
									</span>
									<input id="collectionCode" type="text" name="collectionCode" value="<?= ($collid ? $collData['collectioncode'] : '') ?>" />
									<a id="collcodeinfo" href="#" onclick="return false" tabindex="0">
										<img src="../../images/info.png" style="width:1.2em;" alt="<?= $LANG['MORE_INFO'] ?>" title="<?= $LANG['MORE_COLL_CODE'] ?>"/>
									</a>
									<span id="collcodeinfodialog" aria-live="polite">
										<?php
										echo $LANG['NAME_ACRO'] . ' ';
										echo '<a href="http://rs.tdwg.org/dwc/terms/index.htm#institutionCode" target="_blank">' . $LANG['DWC_DEF'] . '</a>.'
										?>
									</span>
								</span>
							</div>
							<div class="field-block">
								<span class="field-elem">
									<label for="collectionName"> <?= $LANG['COLL_NAME'] ?>: </label>
									<input id="collectionName" type="text" name="collectionName" value="<?= ($collid ? $collData['collectionname'] : '') ?>" class="max-width-fit-75" required />
								</span>
							</div>
							<div class="field-block">
								<div class="field-elem">
									<label for="full-description"  tabindex="0" > <?= $LANG['DESC'];?>: </label>
									<textarea id="full-description" name="fullDescription" style="width:95%;height:90px;"><?= ($collid ? $collData["fulldescription"] : '') ?></textarea>
								</div>
							</div>
							<div class="field-block">
								<span class="field-elem">
									<label for="decimallatitude"> <?= $LANG['LAT'] ?>: </label>
									<input id="decimallatitude" name="latitudeDecimal" type="text" value="<?= ($collid ? $collData["latitudedecimal"] : '') ?>" />
									<a href="#" onclick="openPopup('../tools/mappointaid.php?errmode=0');return false;" tabindex="0"><img src="../../images/world.png" alt="<?= $LANG['MAP'] ?>" style="width:1.2em;" /></a>
								</span>
							</div>
							<div class="field-block">
								<span class="field-elem">
									<label for="decimallongitude"> <?= $LANG['LONG'] ?>: </label>
									<input id="decimallongitude" name="longitudeDecimal" type="text" value="<?= ($collid ? $collData["longitudedecimal"] : '') ?>" />
								</span>
							</div>
							<?php
							$fullCatArr = $collManager->getCategoryArr();
							if ($fullCatArr) {
								?>
								<div class="field-block">
									<span class="field-elem">
									<label for="ccpk"> <?= $LANG['CATEGORY'] ?>: </label>
										<select id="ccpk" name="ccpk">
											<option value=""><?= $LANG['NO_CATEGORY'] ?></option>
											<option value="">-------------------------------------------</option>
											<?php
											$catArr = $collManager->getCollectionCategories();
											foreach ($fullCatArr as $ccpk => $category) {
												echo '<option value="' . $ccpk . '" ' . ($collid && array_key_exists($ccpk, $catArr) ? 'SELECTED' : '') . '>' . $category . '</option>';
											}
											?>
										</select>
									</span>
								</div>
								<?php
							}
							?>
							<div class="field-block">
								<span class="field-elem">
									<span class="screen-reader-only">
										<?php
										echo $LANG['EXPLAIN_PUBLIC'] . ' ';
										?>
									</span>
									<input id="publicEdits" type="checkbox" name="publicEdits" value="1" <?= ($collData && $collData['publicedits'] ? 'CHECKED' : '') ?> />
									<label for="publicEdits"> <?= $LANG['ALLOW_PUBLIC_EDITS'] ?> </label>
									<a id="peditsinfo" href="#" onclick="return false" tabindex="0">
										<img src="../../images/info.png" style="width:1.2em;" alt="<?= $LANG['MORE_INFO'] ?>" title="<?= $LANG['MORE_PUB_EDITS'] ?>"/>
									</a>
									<span id="peditsinfodialog" aria-live="polite">
										<?= $LANG['EXPLAIN_PUBLIC'] ?>
									</span>
								</span>
							</div>
							<div class="field-block">
								<span class="field-elem">
								<a class="screen-reader-only">
									<?php
									echo $LANG['LEGAL_DOC'] . ' ';
									?>
								</a>
								<label for="rights"> <?= $LANG['LICENSE'] ?>: </label>
								<?php
									if (isset($RIGHTS_TERMS)) {
										?>
										<select id="rights" name="rights">
											<?php
											$hasOrphanTerm = true;
											if (!$collid) $hasOrphanTerm = false;
											$rightsCurrent = strtolower(substr($collData['rights'], strpos($collData['rights'], '//'), -4));
											foreach ($RIGHTS_TERMS as $k => $v) {
												$selectedTerm = '';
												$rightsValue = strtolower(substr($v, strpos($v, '//'), -4));
												if ($collid && $rightsCurrent == $rightsValue) {
													$selectedTerm = 'SELECTED';
													$hasOrphanTerm = false;
												}
												echo '<option value="' . $v . '" ' . $selectedTerm . '>' . $k . '</option>' . "\n";
											}
											if ($hasOrphanTerm && array_key_exists('rights', $collData)) {
												echo '<option value="' . $collData['rights'] . '" SELECTED>' . $collData['rights'] . ' [' . $LANG['ORPHANED'] . ']</option>' . "\n";
											}
											?>
										</select>
										<?php
									}
									else {
										?>
										<input type="text" name="rights" value="<?= ($collid ? $collData['rights'] : '') ?>" style="width:90%;" />
										<?php
									}
									?>
									<a id="rightsinfo" href="#" onclick="return false" tabindex="0">
										<img src="../../images/info.png" style="width:1.2em;" alt="<?= $LANG['MORE_INFO'] ?>" title="<?= $LANG['MORE_INFO_RIGHTS'] ?>"/>
									</a>
									<span id="rightsinfodialog" aria-live="polite">
										<?php
										echo $LANG['LEGAL_DOC'] . ' ';
										echo '<a href="http://rs.tdwg.org/dwc/terms/index.htm#dcterms:license" target="_blank">';
										echo $LANG['DWC_DEF'] . '</a>.'
										?>
									</span>
								</span>
							</div>
							<div class="field-block">
								<span class="field-elem">
									<label for="rightsHolder"> <?= $LANG['RIGHTS_HOLDER'] ?>: </label>
									<span class="screen-reader-only">
									<?php
											echo $LANG['HOLDER_DEF'] . ' ';
										?>
									</span>
									<input type="text" id="rightsHolder" name="rightsHolder" value="<?= ($collid ? $collData["rightsholder"] : '') ?>" style="max-width: 100%; width:600px" />
									<a id="rightsholderinfo" href="#" onclick="return false" tabindex="0">
										<img src="../../images/info.png" style="width:1.2em;" alt="<?= $LANG['MORE_INFO'] ?>" title="<?= $LANG['MORE_INFO_RIGHTS_H'] ?>"/>
									</a>
									<span id="rightsholderinfodialog" aria-live="polite">
										<?php
										echo $LANG['HOLDER_DEF'] . ' ';
										echo '<a href="http://rs.tdwg.org/dwc/terms/index.htm#dcterms:rightsHolder" target="_blank">' . $LANG['DWC_DEF'] . '</a>.'
										?>
									</span>
								</span>
							</div>
							<div class="field-block">
								<span class="field-elem">
									<label for="accessRights"> <?= $LANG['ACCESS_RIGHTS'] ?>: </label>
									<span class="screen-reader-only">
										<?php
											echo $LANG['ACCESS_DEF'] . ' ';
										?>
									</span>
									<input type="text" id="accessRights" name="accessRights" value="<?= ($collid ? $collData["accessrights"] : '') ?>" style="max-width: 100%; width:600px" />
									<a id="accessrightsinfo" href="#" onclick="return false" tabindex="0">
										<img src="../../images/info.png" style="width:1.2em;" alt="<?= $LANG['MORE_INFO'] ?>" title="<?= $LANG['MORE_INFO_ACCESS_RIGHTS'] ?>"/>
									</a>
									<span id="accessrightsinfodialog" aria-live="polite">
										<?php
										echo $LANG['ACCESS_DEF'] . ' ';
										echo '<a href="http://rs.tdwg.org/dwc/terms/index.htm#dcterms:accessRights" target="_blank">' . $LANG['DWC_DEF'] . '</a>.';
										?>
									</span>
								</span>
							</div>
							<?php
							if ($IS_ADMIN) {
								$collTypeValue = '';
								if($collid){
									if($collData['colltype'] == 'Observations') $collTypeValue = 'obs';
									elseif($collData['colltype'] == 'General Observations') $collTypeValue = 'go';
									elseif($collData['colltype'] == 'Fossil Specimens') $collTypeValue = 'fs';
									elseif($collData['colltype'] == 'Preserved Specimens') $collTypeValue = 'ps';
								}
								?>
								<div class="field-block">
									<span class="field-elem">
										<label for="collType"> <?= $LANG['DATASET_TYPE'] ?>: </label>
										<select id="collType" name="collType" onchange="toggleFossilWarning()" required>
											<?php if (!empty($GLOBALS['ACTIVATE_PALEO'])): ?> <option value="" <?= ($collTypeValue == '' ? 'SELECTED' : '') ?>><?= $LANG['SELECT_DATASET_TYPE'] ?? '— Select dataset type —' ?></option><?php endif; ?>
											<option value="Preserved Specimens" <?= ($collTypeValue == 'ps' ? 'SELECTED' : '') ?>><?= $LANG['PRES_SPECS'] ?></option>
											<option value="Fossil Specimens" <?= ($collTypeValue == 'fs' ? 'SELECTED' : '') ?>><?= $LANG['FOSSIL_SPECS'] ?></option>
											<option value="Observations" <?= ($collTypeValue == 'obs' ? 'SELECTED' : '') ?>><?= $LANG['OBSERVATIONS'] ?></option>
											<option value="General Observations" <?= ($collTypeValue == 'go' ? 'SELECTED' : '') ?>><?= $LANG['PERS_OBS_MAN'] ?></option>
										</select>
										<a id="colltypeinfo" href="#" onclick="return false" tabindex="0">
											<img src="../../images/info.png" style="width:1.2em;" alt="<?= $LANG['MORE_INFO'] ?>" title="<?= $LANG['MORE_COL_TYPE'] ?>"/>
										</a>
										<span id="colltypeinfodialog" aria-live="polite">
											<?= $LANG['COL_TYPE_DEF'] ?>
										</span>
										<span id="fossilWarning" style="display:none; color:var(--danger-color);">
											<b> <?= $LANG['FOSSIL_WARN_1'] ?>
												<a href="https://dwc.tdwg.org/terms/#dwc:basisOfRecord" target="_blank" style="color:inherit; text-decoration:underline;">dwc:basisOfRecord</a> .</b>
											<b><?= $LANG['FOSSIL_WARN_2'] ?></b><?= ' ' . $LANG['FOSSIL_WARN_3'] ?>
										</span>
									</span>
								</div>
								<div class="field-block">
									<fieldset>
										<legend>
											<?= $LANG['MANAGEMENT'] ?>
											<a id="managementinfo" href="#" onclick="return false" tabindex="0">
												<img src="../../images/info.png" style="width:1.1em;" alt="<?= $LANG['MORE_INFO'] ?>" title="<?= $LANG['MORE_INFO_TYPE'] ?>"/>
											</a>
										</legend>
										<input class="top-breathing-room-rel-sm" id="snapshot" type="radio" name="managementType" value="Snapshot" CHECKED> <label for="snapshot">  <?= $LANG['SNAPSHOT'] ?> </label> <br/>
										<input id="liveData" type="radio" name="managementType" value="Live Data" <?= ($collid && $collData['managementtype'] == 'Live Data' ? 'CHECKED' : '') ?>> <label for="liveData"> <?= $LANG['LIVE_DATA'] ?> </label> <br/>
										<input id="aggregate" type="radio" name="managementType" value="Aggregate" <?= ($collid && $collData['managementtype'] == 'Aggregate' ? 'CHECKED' : '') ?>> <label for="aggregate"> <?= $LANG['AGGREGATE'] ?> </label>
										<script src="../../js/symb/collections.misc.collmetadata.js"></script>
									</fieldset>
									<span id="managementinfodialog" aria-live="polite">
										<?= $LANG['SNAPSHOT_DEF'] ?>
									</span>
								</div>
								<?php
							}
							?>
							<div class="field-block">
								<fieldset>
									<legend>
										<?= $LANG['GUID_SOURCE'] ?>
										<a id="guidinfo" href="#" onclick="return false" tabindex="0">
											<img src="../../images/info.png" style="width:1.1em;" alt="<?= $LANG['MORE_INFO'] ?>" title="<?= $LANG['MORE_INFO_GUID'] ?>"/>
										</a>
									</legend>
									<?php
									$guidSource = '';
									if($collid && !empty($collData['guidtarget'])){
										$guidSource = $collData['guidtarget'];
									}
									?>
									<input class="top-breathing-room-rel-sm" id="occurrenceId" type="radio" name="guidTarget" value="occurrenceId" <?= ($guidSource == 'occurrenceId' ? 'CHECKED' : '') ?>> <label for="occurrenceId">  <?= $LANG['OCCURRENCE_ID'] ?> </label> <br/>
									<input id="catalogNumber" type="radio" name="guidTarget" value="catalogNumber" <?= ($guidSource == 'catalogNumber' ? 'CHECKED' : '') ?>> <label for="catalogNumber"> <?= $LANG['CAT_NUM'] ?> </label> <br/>
									<input id="symbiotaUUID" type="radio" name="guidTarget" value="symbiotaUUID" <?= ($guidSource == 'symbiotaUUID' ? 'CHECKED' : '') ?>> <label for="symbiotaUUID">  <?= $LANG['SYMB_GUID'] ?> </label>
									<script src="../../js/symb/collections.misc.collmetadata.js"></script>
								</fieldset>
								<span id="guidinfodialog" aria-live="polite">
									<?php
									echo $LANG['OCCID_DEF_1'];
									echo ' <a href="http://rs.tdwg.org/dwc/terms/index.htm#occurrenceID" target="_blank">';
									echo $LANG['OCCURRENCEID'] . '</a>';
									echo (isset($LANG['OCCID_DEF_2']) ? ' ' . $LANG['OCCID_DEF_2'] : '');
									?>
								</span>
							</div>
							<?php
							if (isset($GBIF_USERNAME) && isset($GBIF_PASSWORD) && isset($GBIF_ORG_KEY) && $GBIF_ORG_KEY) {
								?>
								<div class="field-block">
									<span class="field-label"><?= $LANG['PUBLISH_TO_AGGS'] ?>:</span>
									<span class="field-elem">
										GBIF <input type="checkbox" name="publishToGbif" value="1" onchange="checkGUIDSource(this.form);" <?= ($collData['publishtogbif'] ? 'CHECKED' : '') ?> />
										<a id="pubagginfo" href="#" onclick="return false" tabindex="0">
											<img src="../../images/info.png" style="width:1.2em;" alt="<?= $LANG['MORE_INFO'] ?>" title="<?= $LANG['MORE_INFO_AGGREGATORS'] ?>"/>
										</a>
										<!--
										<span>
											iDigBio <input type="checkbox" name="publishToIdigbio" value="1" onchange="checkGUIDSource(this.form);" <?= ($collData['publishtoidigbio'] ? 'CHECKED' : '') ?> />
										</span>
										 -->
										<span id="pubagginfodialog" aria-live="polite">
											<?= $LANG['ACTIVATE_GBIF'] ?>.
										</span>
									</span>
								</div>
								<?php
							}
							?>
							<div class="field-block">
								<div class="sourceurl-div" style="display:<?= ($collData["managementtype"] == 'Live Data' ? 'none' : 'inline') ?>">
									<span class="field-label"><?= $LANG['SOURCE_REC_URL'] ?>:</span>

									<span class="field-elem">
										<input type="text" name="individualUrl" class="url-input" value="<?= ($collid ? $collData["individualurl"] : '') ?>" title="<?= $LANG['DYNAMIC_LINK_REC'] ?>" />
										<a id="sourceurlinfo" href="#" onclick="return false" tabindex="0">
											<img src="../../images/info.png" style="width:1.2em;" alt="<?= $LANG['MORE_INFO'] ?>" title="<?= $LANG['MORE_INFO_SOURCE'] ?>"/>
										</a>
										<span id="sourceurlinfodialog" aria-live="polite">
											<?php
											echo $LANG['ADVANCE_SETTING'];
											echo ':http://swbiodiversity.org/seinet/collections/individual/index.php?occid=--DBPK--&quot; ';
											echo $LANG['ADVANCE_SETTING_2'];
											echo ' &quot;http://www.inaturalist.org/observations/--DBPK--&quot; ';
											echo $LANG['ADVANCE_SETTING_3'];
											?>
										</span>
									</span>
								</div>
							</div>
							<div class="field-block">
								<span class="field-elem">
									<span class="icon-elem" style="display:<?= (($collid && $collData["icon"]) ? 'none;' : 'inline') ?>">
										<input type='hidden' name='MAX_FILE_SIZE' value='20000000' />
										<label for="iconFile"> <?= $LANG['ICON_URL'] ?>: </label>
										<input id="iconFile" name='iconFile' type='file' onchange="verifyIconImage(this.form);" />
									</span>
									<span class="icon-elem" style="display:<?= (($collid && $collData["icon"]) ? 'inline' : 'none') ?>">
										<label for="iconurl"><?= $LANG['ICON_URL'] ?>: </label>
										<input class="url-input" type='text' name='iconUrl' id='iconurl' value="<?= ($collid ? $collData["icon"] : '') ?>" onchange="verifyIconURL(this.form);" />
									</span>
									<a id="iconinfo" href="#" onclick="return false" title="<?= $LANG['WHAT_ICON'] ?>" tabindex="0"><img src="../../images/info.png" style="width:1.2em;" alt="<?= $LANG['MORE_INFO'] ?>"/></a>
									<span id="iconinfodialog" aria-live="polite">
										<?= $LANG['UPLOAD_ICON'] ?>
									</span>
								</span>
								<span class="icon-elem" style="display:<?= (($collid && $collData["icon"]) ? 'none;' : 'inline') ?>">
									<a href="#" onclick="toggleElement('.icon-elem');return false;"><?= $LANG['ENTER_URL'] ?></a>
								</span>
								<span class="icon-elem" style="display:<?= (($collid && $collData["icon"]) ? 'inline' : 'none;') ?>">
									<a href="#" onclick="toggleElement('.icon-elem');return false;">
										<?= $LANG['UPLOAD_LOCAL'] ?>
									</a>
								</span>
							</div>
							<?php
							if ($IS_ADMIN) {
								?>
								<div class="field-block" style="clear:both">
									<span class="field-elem">
										<label for="sortSeq"> <?= $LANG['SORT_SEQUENCE'] ?>: </label>
										<span class="screen-reader-only">
											<?php
												echo $LANG['LEAVE_IF_ALPHABET'] . ' ';
											?>
										</span>
										<input id="sortSeq" type="text" name="sortSeq" value="<?= ($collid ? $collData["sortseq"] : '') ?>" />
										<a id="sortinfo" href="#" onclick="return false" tabindex="0">
											<img src="../../images/info.png" style="width:1.2em;" alt="<?= $LANG['MORE_INFO'] ?>" title="<?= $LANG['MORE_SORTING'] ?>"/>
										</a>
										<span id="sortinfodialog" aria-live="polite">
											<?= $LANG['LEAVE_IF_ALPHABET'] ?>
										</span>
									</span>
								</div>
								<?php
							}
							?>
							<div class="field-block">
								<span class="field-elem">
									<label for="collectionID"> <?= $LANG['COLLECTION_ID'] ?>: </label>
									<span class="screen-reader-only">
											<?php
												echo $LANG['EXPLAIN_COLLID'] . ' ';
											?>
									</span>
									<input id="collectionID" type="text" name="collectionID" value="<?= ($collid ? $collData["collectionid"] : '') ?>" style="max-width: 100%; width:400px" />
									<a id="collectionidinfo" href="#" onclick="return false" tabindex="0">
										<img src="../../images/info.png" style="width:1.2em;" alt="<?= $LANG['MORE_INFO'] ?>" title="<?= $LANG['MORE_INFO'] ?>"/>
									</a>
									<span id="collectionidinfodialog" aria-live="polite">
										<?= $LANG['EXPLAIN_COLLID'] .
											' <a href="https://dwc.tdwg.org/terms/#dwc:collectionID" target="_blank">' . $LANG['DWC_COLLID'] .
											'</a>): ' . $LANG['EXPLAIN_COLLID_2'] . ' (<a href="http://grbio.org" target="_blank">http://grbio.org</a>).';
										?>
									</span>
								</span>
							</div>
							<?php
							if ($collid) {
								?>
								<div class="field-block">
									<span class="field-label"><?= $LANG['SECURITY_KEY'] ?>:</span>
									<span class="field-elem">
										<?= $collData['securitykey'] ?>
									</span>
								</div>
								<div class="field-block">
									<span class="field-label"><?= $LANG['RECORDID'] ?>:</span>
									<span class="field-elem">
										<?= $collData['recordid'] ?>
									</span>
								</div>
								<?php
							}
							?>
							<div class="field-block">
								<div style="margin:20px;">
									<?php
									if ($collid) {
										?>
										<input type="hidden" name="securityKey" value="<?= $collData['securitykey'] ?>" />
										<input type="hidden" name="recordID" value="<?= $collData['recordid'] ?>" />
										<input type="hidden" name="collid" value="<?= $collid ?>" />
										<button type="submit" name="action" value="saveEdits"><?= $LANG['SAVE_EDITS'] ?></button>
										<?php
									}
									else {
										?>
										<button type="submit" name="action" value="newCollection"><?= $LANG['CREATE_COLL_2'] ?></button>
										<?php
									}
									?>
								</div>
							</div>
						</form>
								</section>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>

</html>
