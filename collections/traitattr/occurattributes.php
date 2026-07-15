<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/OccurrenceAttributes.php');
include_once($SERVER_ROOT . '/classes/utilities/GeneralUtil.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load('collections/traitattr/occurattributes');

header("Content-Type: text/html; charset=" . $CHARSET);

if (!$SYMB_UID) header('Location: ' . $CLIENT_ROOT . '/profile/index.php?refurl=../collections/traitattr/occurattributes.php?' . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = Sanitize::int($_REQUEST['collid']);
$mode = Sanitize::int($_REQUEST['mode'] ?? 1);
$traitID = Sanitize::int($_REQUEST['traitid'] ?? '');
$paneX = Sanitize::int($_POST['panex'] ?? '700');
$paneY = Sanitize::int($_POST['paney'] ?? '550');
$imgRes = Sanitize::int($_POST['imgres'] ?? 0);
$submitForm = array_key_exists('submitform', $_POST) ? $_POST['submitform'] : '';

$isEditor = 0;
if ($SYMB_UID) {
	if ($IS_ADMIN) {
		$isEditor = 2;
	} elseif ($collid) {
		//If a page related to collections, one maight want to...
		if (array_key_exists("CollAdmin", $USER_RIGHTS) && in_array($collid, $USER_RIGHTS["CollAdmin"])) {
			$isEditor = 2;
		} elseif (array_key_exists("CollEditor", $USER_RIGHTS) && in_array($collid, $USER_RIGHTS["CollEditor"])) {
			$isEditor = 1;
			$mode = 1;		//Editors are not allowed to review, thus default to edit mode
		}
	}
}

$attrManager = new OccurrenceAttributes();
$attrManager->setCollid($collid);
$attrManager->setFilterAttributes($_POST);
$taxonFilter = $attrManager->getFilterAttribute('taxonfilter');
$tidFilter = $attrManager->getFilterAttribute('tidfilter');
$reviewUid = $attrManager->getFilterAttribute('reviewuid');
$reviewDate = $attrManager->getFilterAttribute('reviewdate');
$reviewStatus = $attrManager->getFilterAttribute('reviewstatus');
$sourceFilter = $attrManager->getFilterAttribute('sourcefilter');
$localFilter = $attrManager->getFilterAttribute('localfilter');
$start = $attrManager->getFilterAttribute('start');

$statusStr = '';
if ($isEditor) {
	if ($submitForm == 'Save and Next') {
		$attrManager->setOccid($_POST['targetoccid']);
		if (!$attrManager->addAttributes($_POST, $SYMB_UID)) {
			$statusStr = $attrManager->getErrorMessage();
		}
	} elseif ($submitForm == 'Set Status and Save') {
		$attrManager->setOccid($_POST['targetoccid']);
		$attrManager->editAttributes($_POST);
	}
}
$imgArr = array();
$occid = 0;
$catNum = '';
if ($traitID) {
	$imgRetArr = array();
	if ($mode == 1) {
		$imgRetArr = $attrManager->getImageUrls($traitID);
		$imgArr = current($imgRetArr);
	} elseif ($mode == 2) {
		$imgRetArr = $attrManager->getReviewUrls($traitID);
		if ($imgRetArr) $imgArr = current($imgRetArr);
	}
	if ($imgRetArr) {
		$catNum = $imgArr['catnum'];
		unset($imgArr['catnum']);
		$occid = key($imgRetArr);
		if ($occid) $attrManager->setOccid($occid);
	}
}
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
	<head>
		<title><?= $LANG['OCC_ATTRIBUTE_BATCH_EDIT'] ?></title>
		<link href="<?= $CSS_BASE_PATH ?>/jquery-ui.css" type="text/css" rel="stylesheet">
		<?php
		include_once($SERVER_ROOT . '/includes/head.php');
		?>
		<script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
		<script src="<?= $CLIENT_ROOT ?>/js/jquery-ui.min.js" type="text/javascript"></script>
		<script src="<?= $CLIENT_ROOT ?>/js/jquery.imagetool-1.7.js?ver=160102" type="text/javascript"></script>
		<script type="text/javascript">
			var activeImgIndex = 1;
			var imgArr = [];
			var imgLgArr = [];
			<?php
			$imgDomain = $MEDIA_DOMAIN;
			if(!$imgDomain) GeneralUtil::getDomain();
			foreach($imgArr as $cnt => $iArr){
				//Regular url
				$url = $iArr['web'];
				if(substr($url,0,1) == '/') $url = $imgDomain.$url;
				echo 'imgArr['.$cnt.'] = "'.$url.'";'."\n";
				//Large Url
				$lgUrl = $iArr['lg'];
				if($lgUrl){
					if(substr($lgUrl,0,1) == '/') $lgUrl = $imgDomain.$lgUrl;
					echo 'imgLgArr['.$cnt.'] = "'.$lgUrl.'";'."\n";
				}
			}
			?>

		$(document).ready(function() {
			setImgRes();

			$("#specimg").imagetool({
				maxWidth: 6000,
				viewportWidth: <?= $paneX ?>,
				viewportHeight: <?= $paneY ?>,
				edgeSensitivity: 25
			});

		});

		function setImgRes() {
			if (imgLgArr[activeImgIndex] != null) {
				if ($("#imgres1").val() == 1) changeImgRes('lg');
			} else {
				if (imgArr[activeImgIndex] != null) {
					$("#specimg").attr("src", imgArr[activeImgIndex]);
					document.getElementById("imgresmed").checked = true;
					var imgResLgRadio = document.getElementById("imgreslg");
					imgResLgRadio.disabled = true;
					imgResLgRadio.title = "<?= $LANG['LARGE_RESOLUTION_IMAGE_NOT_AVAI'] ?>";
				}
			}
			if (imgArr[activeImgIndex] != null) {
				//Do nothing
			} else {
				if (imgLgArr[activeImgIndex] != null) {
					$("#specimg").attr("src", imgLgArr[activeImgIndex]);
					document.getElementById("imgreslg").checked = true;
					var imgResMedRadio = document.getElementById("imgresmed");
					imgResMedRadio.disabled = true;
					imgResMedRadio.title = "<?= $LANG['MED_RESOLUTION_IMAGE_NOT_AVAI'] ?>";
				}
			}
		}

		function changeImgRes(resType) {
			if (resType == 'lg') {
				$("#imgres1").val(1);
				$("#imgres2").val(1);
				if (imgLgArr[activeImgIndex]) {
					$("#specimg").attr("src", imgLgArr[activeImgIndex]);
					$("#imgreslg").prop("checked", true);
				}
			} else {
				$("#imgres1").val(0);
				$("#imgres2").val(0);
				if (imgArr[activeImgIndex]) {
					$("#specimg").attr("src", imgArr[activeImgIndex]);
					$("#imgresmed").prop("checked", true);
				}
			}
		}

		function setPortXY(portWidth, portHeight) {
			$("#panex1").val(portWidth);
			$("#paney1").val(portHeight);
			$("#panex2").val(portWidth);
			$("#paney2").val(portHeight);
		}

		function nextImage() {
			activeImgIndex = activeImgIndex + 1;
			if (activeImgIndex >= imgArr.length) activeImgIndex = 1;
			$("#specimg").attr("src", imgArr[activeImgIndex]);
			$("#specimg").imagetool({
				maxWidth: 6000,
				viewportWidth: $("#panex1").val(),
				viewportHeight: $("#paney1").val()
			});
			//setImgRes();
			$("#labelcnt").html(activeImgIndex);
			return false;
		}

		function skipSpecimen() {
			$("#filterform").submit();
		}

		function verifyFilterForm(f) {
			if (f.taxonfilter.value == "<?= $LANG['ALL_TAXA'] ?>") f.taxonfilter.value = '';
			if (f.traitid.value == "") {
				alert("<?= $LANG['OCC_TRAIT_MUST_SELECTED'] ?>");
				return false;
			}
			if (f.taxonfilter.value != "" && f.tidfilter.value == "") {
				alert("<?= $LANG['TAXON_FILTER_NOT_SYNC_THES'] ?>");
				return false;
			}
			return true;
		}

		function nextReviewRecord(startValue) {
			var f = document.getElementById("reviewform");
			f.start.value = startValue;
			f.submit();
		}

		function verifySubmitForm(f) {

			return true;
		}

		function taxonFilterFocus(formElem) {
			if (formElem.value == "<?= $LANG['ALL_TAXA'] ?>") formElem.value = '';
		}
	</script>
	<script src="<?= $CLIENT_ROOT ?>/js/symb/collections.traitattr.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/symb/shared.js?ver=1" type="text/javascript"></script>
	<style>
		.flex-row { display: flex; }
		.flex-cell { justify-content: space-between }
		#cell-1 { min-height: 700px; }
		#cell-2 { width: 290px; }
		#img-div{ border: 1px solid #ccc; resize: both; min-width: 250px; min-height: 250px; }
		input { margin: 3px; }
		select { margin: 3px; }
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = false;
	include($SERVER_ROOT . '/includes/header.php');
	?>
	<div class="navpath">
		<a href="../../index.php"><?= $LANG['HOME'] ?></a> &gt;&gt;
		<a href="../misc/collprofiles.php?collid=<?= $collid ?>&emode=1"><?= $LANG['COLLECTION_MANAGEMENT'] ?></a> &gt;&gt;
		<?php
		if ($mode == 2) {
			echo '<b>' . $LANG['ATTRIBUTE_REVIEWER'] . '</b>';
		} else {
			echo '<b>' . $LANG['ATTRIBUTE_EDITOR'] . '</b>';
		}
		?>
	</div>
	<?php
	if ($statusStr) {
		echo '<div style="color:red">';
		echo $statusStr;
		echo '</div>';
	}
	?>
	<!-- This is inner text! -->
	<div role="main" id="innertext" style="position:relative;">
		<h1 class="page-heading"><?= $LANG['OCC_ATTRIBUTE_BATCH_EDIT'] ?></h1>
		<?php
		if ($collid) {
			?>
			<div class="flex-row">
				<div id="cell-1" class="flex-cell">
					<?php
					if ($imgArr) {
						?>
						<div>
							<span><input id="imgresmed" name="resradio" type="radio" checked onchange="changeImgRes('med')" /><?= $LANG['MED_RES'] ?></span>
							<span style="margin-left:6px;"><input id="imgreslg" name="resradio" type="radio" onchange="changeImgRes('lg')" /><?= $LANG['HIGH_RES'] ?></span>
							<?php
							if ($occid) {
								if (!$catNum) $catNum = 'Specimen Details';
								echo '<span style="margin-left:50px;">';
								echo '<a href="../individual/index.php?occid=' . $occid . '" target="_blank" title= " ' . $LANG['SPECIMEN_DETAILS'] . ' ">' . htmlspecialchars($catNum, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</a>';
								echo '</span>';
							}
							$imgTotal = count($imgArr);
							if ($imgTotal > 1) echo '<span id="labelcnt" style="margin-left:60px;">1</span> of ' . $imgTotal . ' images ' . ($imgTotal > 1 ? '<a href="#" onclick="nextImage();return false;">&gt;&gt; ' . $LANG['NEXT'] . '</a>' : '');
							if ($occid && $mode != 2) echo '<span style="margin-left:80px" title="' . $LANG['SKIP_SPECIMEN'] . '"><a href="#" onclick="skipSpecimen()">' . $LANG['SKIP'] . ' &gt;&gt;</a></span>';
							?>
						</div>
						<div id="img-div">
							<?php
							$url = $imgArr[1]['web'];
							if (substr($url, 0, 1) == '/') $url = $imgDomain . $url;
							echo '<img id="specimg" src="' . $url . '" />';
							?>
						</div>
						<?php
					} else {
						if ($submitForm) {
							?>
							<div style="margin:50px;font-size:120%;font-weight: bold"><?= $LANG['NO_IMAGES_MATCHING_CRITERIA'] ?></div>
							<?php
						} else {
							?>
							<div style="margin-top:50px;font-size:120%;font-weight: bold">
								<?= $LANG['SELECT_UNSCORED_IMAGE_TRAIT'] ?>
							</div>
							<div style="margin-top:15px;">
								<?= $LANG['TRAIT_TOOL_EXPLAIN'] ?>
								<a href="https://tools.gbif.org/dwca-validator/extension.do?id=http://rs.iobis.org/obis/terms/ExtendedMeasurementOrFact" target="_blank"><?= $LANG['MEASUREMENT_OR_FACT'] ?></a> <?= $LANG['DWC_EXTEN_FILE'] ?>
							</div>
							<?php
						}
					}
					?>
				</div>
				<div  id="cell-2" class="flex-cell">
					<?php
					$attrNameArr = $attrManager->getTraitNames();
					if ($mode == 1) {
						?>
						<form id="filterform" name="filterform" method="post" action="occurattributes.php" onsubmit="return verifyFilterForm(this)">
							<fieldset>
								<legend><b><?= $LANG['FILTER'] ?></b></legend>
								<div>
									<select name="traitid">
										<option value=""><?= $LANG['SELECT_TRAIT_REQ'] ?></option>
										<option value="">------------------------------------</option>
										<?php
										if ($attrNameArr) {
											foreach ($attrNameArr as $ID => $aName) {
												echo '<option value="' . $ID . '" ' . ($traitID == $ID ? 'SELECTED' : '') . '>' . $aName . '</option>';
											}
										} else {
											echo '<option value="0">'.$LANG['NO_ATTRI_AVAILABLE'].'</option>';
										}
										?>
									</select>
								</div>
								<div>
									<select name="localfilter" style="width:250px">
										<option value=""><?= $LANG['ALL_COUNTRIES_STATES'] ?></option>
										<option value="">-----------------------------</option>
										<?php
										$localArr = $attrManager->getLocalFilterOptions();
										foreach ($localArr as $localTerm) {
											echo '<option ' . ($localFilter == $localTerm ? 'selected' : '') . '>' . $localTerm . '</option>';
										}
										?>
									</select>
								</div>
								<div>
									<input id="taxonfilter" name="taxonfilter" type="text" value="<?= ($taxonFilter ? $taxonFilter : $LANG['ALL_TAXA']) ?>" taxonFilterFocus(this) />
									<input id="tidfilter" name="tidfilter" type="hidden" value="<?= $tidFilter ?>" />
								</div>
								<div>
									<input name="collid" type="hidden" value="<?= $collid ?>" />
									<input id="panex1" name="panex" type="hidden" value="<?= $paneX ?>" />
									<input id="paney1" name="paney" type="hidden" value="<?= $paneY ?>" />
									<input id="imgres1" name="imgres" type="hidden" value="<?= $imgRes ?>" />
									<button id="filtersubmit" name="submitform" type="submit" value="Load Images"><?= $LANG['LOAD_IMAGES'] ?></button>

									<span id="verify-span" style="display:none;font-weight:bold;color:green;"><?= $LANG['VERIFY_TAXONOMY'] ?></span>
									<span id="notvalid-span" style="display:none;font-weight:bold;color:red;"><?= $LANG['TAXON_NOT_VALID'] ?></span>
								</div>
								<div style="margin:10px">
									<?php if ($traitID) echo '<b> ' . $LANG['TARGET_SPECIMEN'] . '</b> ' . $attrManager->getSpecimenCount($traitID) ?>
								</div>
								<?php
								if ($isEditor == 2){
									?>
									<div style="text-align:center">
										<hr>
										<?= $LANG['GO_TO'] ?> <a href="occurattributes.php?collid=<?= $collid ?>&mode=2&traitid=<?= $traitID ?>"><?= $LANG['REVIEW_MODE'] ?></a>
									</div>
									<?php
								}
								?>
							</fieldset>
						</form>
						<?php
					} elseif ($mode == 2) {
						?>
						<form id="reviewform" name="reviewform" method="post" action="occurattributes.php" onsubmit="return verifyFilterForm(this)">
							<fieldset>
								<legend><b><?= $LANG['REVIEWER'] ?></b></legend>
								<div>
									<select name="traitid">
										<option value=""><?= $LANG['SELECT_TRAIT_REQ'] ?></option>
										<option value="">------------------------------------</option>
										<?php
										if ($attrNameArr) {
											foreach ($attrNameArr as $ID => $aName) {
												echo '<option value="' . $ID . '" ' . ($traitID == $ID ? 'SELECTED' : '') . '>' . $aName . '</option>';
											}
										} else {
											echo '<option value="0">' . $LANG['NO_ATTRI_AVAILABLE'] . '</option>';
										}
										?>
									</select>
								</div>
								<div>
									<select name="reviewuid">
										<option value=""><?= $LANG['ALL_EDITORS'] ?></option>
										<option value="">-----------------------</option>
										<?php
										$editorArr = $attrManager->getEditorArr();
										foreach ($editorArr as $uid => $name) {
											echo '<option value="' . $uid . '" ' . ($uid == $reviewUid ? 'SELECTED' : '') . '>' . $name . '</option>';
										}
										?>
									</select>
								</div>
								<div>
									<select name="reviewdate">
										<option value=""><?= $LANG['ALL_DATES'] ?></option>
										<option value="">-----------------------</option>
										<?php
										$dateArr = $attrManager->getEditDates();
										foreach ($dateArr as $date) {
											echo '<option ' . ($date == $reviewDate ? 'SELECTED' : '') . '>' . $date . '</option>';
										}
										?>
									</select>
								</div>
								<div>
									<select name="reviewstatus">
										<option value="0"><?= $LANG['NOT_REVIEWED'] ?></option>
										<option value="5" <?= ($reviewStatus == 5 ? 'SELECTED' : '') ?>><?= $LANG['EXPERT_NEEDED'] ?></option>
										<option value="10" <?= ($reviewStatus == 10 ? 'SELECTED' : '') ?>><?= $LANG['REVIEWED'] ?></option>
									</select>
								</div>
								<div>
									<select name="sourcefilter">
										<option value=""><?= $LANG['ALL_SOURCE_TYPE'] ?></option>
										<option value="">-----------------------------</option>
										<?php
										$sourceControlArr = $attrManager->getSourceControlledArr();
										foreach ($sourceControlArr as $sourceTerm) {
											echo '<option ' . ($sourceFilter == $sourceTerm ? 'selected' : '') . '>' . $sourceTerm . '</option>';
										}
										?>
									</select>
								</div>
								<div>
									<select name="localfilter" style="width:250px;">
										<option value=""><?= $LANG['ALL_COUNTRIES_STATES'] ?></option>
										<option value="">-----------------------------</option>
										<?php
										$localArr = $attrManager->getLocalFilterOptions();
										foreach ($localArr as $localTerm) {
											echo '<option ' . ($localFilter == $localTerm ? 'selected' : '') . '>' . $localTerm . '</option>';
										}
										?>
									</select>
								</div>
								<div>
									<input id="taxonfilter" name="taxonfilter" type="text" value="<?= ($taxonFilter ? $taxonFilter : 'All Taxa') ?>" onfocus="taxonFilterFocus(this)" />
									<input id="tidfilter" name="tidfilter" type="hidden" value="<?= $tidFilter ?>" />
								</div>
								<div style="margin:10px;">
									<input name="collid" type="hidden" value="<?= $collid ?>" />
									<input id="panex1" name="panex" type="hidden" value="<?= $paneX ?>" />
									<input id="paney1" name="paney" type="hidden" value="<?= $paneY ?>" />
									<input id="imgres1" name="imgres" type="hidden" value="<?= $imgRes ?>" />
									<input name="mode" type="hidden" value="2" />
									<input name="start" type="hidden" value="" />
									<button name="submitform" type="submit" value="Get Images"><?= $LANG['GET_IMAGES'] ?></button>
								</div>
								<div>
									<?php
									if ($traitID) {
										$rCnt = $attrManager->getReviewCount($traitID);
										echo '<b>' . ($rCnt ? $start + 1 : 0) . ' of ' . $rCnt . ' ' . $LANG['RECORD'] . '</b>';
										if ($rCnt > 1) {
											$next = ($start + 1);
											if ($next >= $rCnt) $next = 0;
											echo ' (<a href="#" onclick="nextReviewRecord(' . ($next) . ')">' . $LANG['NEXT_RECORD'] . ' &gt;&gt;</a>)';
										}
									}
									?>
								</div>
								<?php
								if ($isEditor == 2){
									?>
									<div style="text-align:center">
										<hr>
										<?= $LANG['GO_TO'] ?> <a href="occurattributes.php?collid=<?= $collid?>&mode=1&traitid=<?= $traitID ?>"><?= $LANG['EDIT_MODE'] ?></a>
									</div>
									<?php
								}
								?>
							</fieldset>
						</form>
						<?php
					}
					if ($imgArr) {
						$traitArr = $attrManager->getTraitArr($traitID, ($mode == 2 ? true : false));
						$statusCode = 0;
						$notes = '';
						foreach ($traitArr[$traitID]['states'] as $stArr) {
							if (isset($stArr['statuscode']) && $stArr['statuscode']) $statusCode = $stArr['statuscode'];
							if (isset($stArr['notes']) && $stArr['notes']) $notes = $stArr['notes'];
						}
						?>
						<div id="traitdiv">
							<form name="submitform" method="post" action="occurattributes.php" onsubmit="return verifySubmitForm(this)">
								<fieldset>
									<legend><b><?= $traitArr[$traitID]['name'] ?></b></legend>
									<div style="float:right;margin-right:10px">
										<div class="trianglediv" style="margin:4px 3px;float:right;cursor:pointer" onclick="setAttributeTree(this)" title="<?= $LANG['TOGGLE_ATTRI_TREE'] ?>">
											<img class="triangleright" src="../../images/tochild.png" style="width:1.4em" />
											<img class="triangledown" src="../../images/toparent.png" style="display:none;width:1.4em" />
										</div>
									</div>
									<div><?= $attrManager->echoFormTraits($traitID) ?>
									</div>
									<div style="margin:10px 5px;clear:both">
										<?= $LANG['NOTES'] ?>
										<input name="notes" type="text" style="width:200px" value="<?= $notes ?>" />
									</div>
									<div style="margin-left:5;">
										<?= $LANG['STATUS'] ?>
										<select name="setstatus">
											<?php
											if ($mode == 2) {
												?>
												<option value="0"><?= $LANG['NOT_REVIEWED'] ?></option>
												<option value="5"><?= $LANG['EXPERT_NEEDED'] ?></option>
												<option value="10" selected><?= $LANG['REVIEWED'] ?></option>
												<?php
											} else {
												?>
												<option value="0">---------------</option>
												<option value="5"><?= $LANG['EXPERT_NEEDED'] ?></option>
												<?php
											}
											?>
										</select>
									</div>
									<div style="margin:20px">
										<input name="taxonfilter" type="hidden" value="<?= $taxonFilter ?>" />
										<input name="tidfilter" type="hidden" value="<?= $tidFilter ?>" />
										<input name="localfilter" type="hidden" value="<?= $localFilter ?>" />
										<input name="traitid" type="hidden" value="<?= $traitID ?>" />
										<input name="collid" type="hidden" value="<?= $collid ?>" />
										<input id="panex2" name="panex" type="hidden" value="<?= $paneX ?>" />
										<input id="paney2" name="paney" type="hidden" value="<?= $paneY ?>" />
										<input id="imgres2" name="imgres" type="hidden" value="<?= $imgRes ?>" />
										<input name="targetoccid" type="hidden" value="<?= $occid ?>" />
										<input name="mode" type="hidden" value="<?= $mode ?>" />
										<input name="reviewuid" type="hidden" value="<?= $reviewUid ?>" />
										<input name="reviewdate" type="hidden" value="<?= $reviewDate ?>" />
										<input name="reviewstatus" type="hidden" value="<?= $reviewStatus ?>" />
										<?php
										if ($mode == 2) {
											echo '<button name="submitform" type="submit" value="Set Status and Save">' . $LANG['SET_STATUS_SAVE'] . '</button>';
										} else {
											echo '<button name="submitform" type="submit" value="Save and Next" disabled >' . $LANG['SAVE_NEXT'] . '</button>';
										}
										?>
									</div>
								</fieldset>
							</form>
						</div>
						<?php
					}
					?>
				</div>
			</div>
		<?php
		} else {
			echo '<div><b>' . $LANG['ERROR_CONNECTION_IDENTIFIER'] . '</b></div>';
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>
</html>
