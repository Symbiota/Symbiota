<?php
include_once('../config/symbini.php');
if ($LANG_TAG != 'en' && file_exists($SERVER_ROOT . '/content/lang/collections/list.' . $LANG_TAG . '.php')) include_once($SERVER_ROOT . '/content/lang/collections/list.' . $LANG_TAG . '.php');
else include_once($SERVER_ROOT . '/content/lang/collections/list.en.php');
include_once($SERVER_ROOT . '/classes/OccurrenceListManager.php');
header("Content-Type: text/html; charset=" . $CHARSET);

$taxonFilter = array_key_exists('taxonfilter', $_REQUEST) ? filter_var($_REQUEST['taxonfilter'], FILTER_SANITIZE_NUMBER_INT) : 0;
$targetTid = array_key_exists('targettid', $_REQUEST) ? filter_var($_REQUEST['targettid'], FILTER_SANITIZE_NUMBER_INT) : '';
$tabIndex = array_key_exists('tabindex', $_REQUEST) ? filter_var($_REQUEST['tabindex'], FILTER_SANITIZE_NUMBER_INT) : 1;
$cntPerPage = array_key_exists('cntperpage', $_REQUEST) ? filter_var($_REQUEST['cntperpage'], FILTER_SANITIZE_NUMBER_INT) : 100;
$pageNumber = array_key_exists('page', $_REQUEST) ? filter_var($_REQUEST['page'], FILTER_SANITIZE_NUMBER_INT) : 1;
$datasetid = array_key_exists('datasetid', $_REQUEST) ? filter_var($_REQUEST['datasetid'], FILTER_SANITIZE_NUMBER_INT) : '';
$_SESSION['datasetid'] = filter_var($datasetid, FILTER_SANITIZE_NUMBER_INT);

$collManager = new OccurrenceListManager();
$searchVar = $collManager->getQueryTermStr();
if ($targetTid && array_key_exists('mode', $_REQUEST)) $searchVar .= '&mode=voucher&targettid=' . $targetTid;
$occurArr = $collManager->getSpecimenMap($pageNumber, $cntPerPage);

$_SESSION['citationvar'] = $searchVar;
?>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title><?php echo $DEFAULT_TITLE . ' ' . $LANG['PAGE_TITLE']; ?></title>
	<?php
	include_once($SERVER_ROOT . '/includes/head.php');
	include_once($SERVER_ROOT . '/includes/googleanalytics.php');
	?>
	<link href="<?php echo htmlspecialchars($CSS_BASE_PATH, HTML_SPECIAL_CHARS_FLAGS); ?>/symbiota/collections/listdisplay.css" type="text/css" rel="stylesheet" />
	<script src="../js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="../js/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
	<link href="../js/jquery-ui/jquery-ui.min.css" type="text/css" rel="Stylesheet" />
	<script type="text/javascript">
		var urlQueryStr = "<?php if($searchVar) echo $searchVar . '&page=' . $pageNumber; ?>";

		$(document).ready(function() {
			<?php
			if ($searchVar) {
				?>
				sessionStorage.querystr = "<?php echo $searchVar; ?>";
				<?php
			}
			else {
				?>
				if (sessionStorage.querystr) {
					window.location = "list.php?" + sessionStorage.querystr + "&tabindex=<?php echo $tabIndex ?>";
				}
				<?php
			}
			?>

			$('#tabs').tabs({
				active: <?php echo $tabIndex; ?>,
				beforeLoad: function(event, ui) {
					$(ui.panel).html("<p>Loading...</p>");
				}
			});
		});

		function validateOccurListForm(f) {
			if (f.targetdatasetid.value == "") {
				alert("<?php echo (isset($LANG['SELECT_DATASET']) ? $LANG['SELECT_DATASET'] : 'Please select a dataset to append occurrences, or select Create New Dataset'); ?>");
				return false;
			}
			return true;
		}

		function hasSelectedOccid(f) {
			var isSelected = false;
			for (var h = 0; h < f.length; h++) {
				if (f.elements[h].name == "occid[]" && f.elements[h].checked) {
					isSelected = true;
					break;
				}
			}
			if (!isSelected) {
				alert("<?php echo (isset($LANG['SELECT_OCCURRENCE']) ? $LANG['SELECT_OCCURRENCE'] : 'Please select at least one occurrence to be added to the dataset'); ?>");
				return false;
			}
			return true;
		}

		function displayDatasetTools() {
			$('.dataset-div').toggle();
			document.getElementById("dataset-tools").scrollIntoView({
				behavior: 'smooth'
			});
		}
	</script>
	<script src="../js/symb/collections.list.js?ver=10" type="text/javascript"></script>
	<style type="text/css">
		fieldset {
			padding: 15px;
		}

		legend {
			font-weight: bold;
		}

		.checkbox-elem {
			margin: 5px;
			padding: 5px;
			border: 1px dashed orange;
		}

		.ui-tabs .ui-tabs-nav li {
			width: 32%;
		}

		.ui-tabs .ui-tabs-nav li a {
			margin-left: 10px;
		}
	</style>
</head>

<body>
	<?php
	$displayLeftMenu = (isset($collections_listMenu) ? $collections_listMenu : false);
	include($SERVER_ROOT . '/includes/header.php');
	if (isset($collections_listCrumbs)) {
		if ($collections_listCrumbs) {
			echo '<div class="navpath">';
			echo '<a href="../index.php">' . htmlspecialchars($LANG['NAV_HOME'], HTML_SPECIAL_CHARS_FLAGS) . '</a> &gt;&gt; ';
			echo $collections_listCrumbs . ' &gt;&gt; ';
			echo '<b>' . $LANG['NAV_SPECIMEN_LIST'] . '</b>';
			echo '</div>';
		}
	}
	else {
		echo '<div class="navpath">';
		echo '<a href="../index.php">' . htmlspecialchars($LANG['NAV_HOME'], HTML_SPECIAL_CHARS_FLAGS) . '</a> &gt;&gt; ';
		echo '<a href="index.php">' . htmlspecialchars($LANG['NAV_COLLECTIONS'], HTML_SPECIAL_CHARS_FLAGS) . '</a> &gt;&gt; ';
		echo '<a href="harvestparams.php">' . htmlspecialchars($LANG['NAV_SEARCH'], HTML_SPECIAL_CHARS_FLAGS) . '</a> &gt;&gt; ';
		echo '<b>' . $LANG['NAV_SPECIMEN_LIST'] . '</b>';
		echo '</div>';
	}
	?>
	<!-- This is inner text! -->
	<div id="innertext">
		<div id="tabs" style="width:95%;">
			<ul>
				<li>
					<a id="taxatablink" href='<?php echo 'checklist.php?' . htmlspecialchars($searchVar, HTML_SPECIAL_CHARS_FLAGS) . '&taxonfilter=' . htmlspecialchars($taxonFilter, HTML_SPECIAL_CHARS_FLAGS); ?>'>
						<span><?php echo $LANG['TAB_CHECKLIST']; ?></span>
					</a>
				</li>
				<li>
					<a href="#speclist">
						<span><?php echo $LANG['TAB_OCCURRENCES']; ?></span>
					</a>
				</li>
				<li>
					<a href="#maps">
						<span><?php echo $LANG['TAB_MAP']; ?></span>
					</a>
				</li>
			</ul>
			<div id="speclist">
				<div id="queryrecords">
					<div style="float:right;">
						<?php
						if ($SYMB_UID) {
						?>
							<div style="float:left">
								<button class="icon-button" onclick="displayDatasetTools()" title="Dataset Management">
									<img src="../images/dataset.png" style="width:15px;" />
								</button>
							</div>
						<?php
						}
						?>
						<form action="listtabledisplay.php" method="post" style="float:left">
							<button class="icon-button" title="<?php echo (isset($LANG['TABLE_DISPLAY']) ? $LANG['TABLE_DISPLAY'] : 'Table Display'); ?>">
								<img src="../images/table.png" style="width:15px; height:15px" />
							</button>
							<input name="searchvar" type="hidden" value="<?php echo $searchVar; ?>" />
						</form>
						<form action="download/index.php" method="post" style="float:left" onsubmit="targetPopup(this)">
							<button class="icon-button" title="<?php echo $LANG['DOWNLOAD_SPECIMEN_DATA']; ?>">
								<img src="../images/dl2.png" srcset="../images/download.svg" class="svg-icon" style="width:15px; height:15px" />
							</button>
							<input name="searchvar" type="hidden" value="<?php echo $searchVar; ?>" />
							<input name="dltype" type="hidden" value="specimen" />
						</form>
						<div style="float:left">
							<button class="icon-button" onclick="copyUrl()" title="<?php echo (isset($LANG['COPY_TO_CLIPBOARD']) ? $LANG['COPY_TO_CLIPBOARD'] : 'Copy URL to Clipboard'); ?>">
								<img src="../images/dl2.png" srcset="../images/link.svg" class="svg-icon" style="width:15px; height:15px" />
							</button>
						</div>
					</div>
					<div style="margin:5px;">
						<?php
						$collSearchStr = $collManager->getCollectionSearchStr();
						if (strlen($collSearchStr) > 100) {
							$collSearchArr = explode('; ', $collSearchStr);
							$collSearchStr = '';
							$cnt = 0;
							while ($collElem = array_shift($collSearchArr)) {
								$collSearchStr .= $collElem . '; ';
								if ($cnt == 10 && $collSearchArr) {
									$collSearchStr = trim($collSearchStr, '; ') . '<span class="inst-span">... (<a href="#" onclick="$(\'.inst-span\').toggle();return false;">' . htmlspecialchars($LANG['SHOW_ALL'], HTML_SPECIAL_CHARS_FLAGS) . '</a>)</span><span class="inst-span" style="display:none">; ';
								}
								$cnt++;
							}
							if ($cnt > 11) $collSearchStr .= '</span>';
						}
						echo '<div><b>' . $LANG['DATASET'] . ':</b> ' . $collSearchStr . '</div>';
						if ($taxaSearchStr = $collManager->getTaxaSearchStr()) {
							if (strlen($taxaSearchStr) > 300) $taxaSearchStr = substr($taxaSearchStr, 0, 300) . '<span class="taxa-span">... (<a href="#" onclick="$(\'.taxa-span\').toggle();return false;">' . htmlspecialchars($LANG['SHOW_ALL'], HTML_SPECIAL_CHARS_FLAGS) . '</a>)</span><span class="taxa-span" style="display:none;">' . substr($taxaSearchStr, 300) . '</span>';
							echo '<div><b>' . $LANG['TAXA'] . ':</b> ' . $taxaSearchStr . '</div>';
						}
						if ($localSearchStr = $collManager->getLocalSearchStr()) {
							echo '<div><b>' . $LANG['SEARCH_CRITERIA'] . ':</b> ' . $localSearchStr . '</div>';
							$_SESSION['datasetName'] = $localSearchStr;
						}
						?>
					</div>
					<div style="clear:both;"></div>
					<?php
					$paginationStr = '<div><div style="clear:both;"><hr/></div><div style="float:left;margin:5px;">';
					$lastPage = (int)($collManager->getRecordCnt() / $cntPerPage) + 1;
					$startPage = ($pageNumber > 5 ? $pageNumber - 5 : 1);
					$endPage = ($lastPage > $startPage + 10 ? $startPage + 10 : $lastPage);
					$pageBar = '';
					if ($startPage > 1) {
						$pageBar .= '<span class="pagination" style="margin-right:5px;"><a href="list.php?' . htmlspecialchars($searchVar, HTML_SPECIAL_CHARS_FLAGS) . '" >' . htmlspecialchars($LANG['PAGINATION_FIRST'], HTML_SPECIAL_CHARS_FLAGS) . '</a></span>';
						$pageBar .= '<span class="pagination" style="margin-right:5px;"><a href="list.php?' . htmlspecialchars($searchVar, HTML_SPECIAL_CHARS_FLAGS) . '&page=' . htmlspecialchars((($pageNumber - 10) < 1 ? 1 : $pageNumber - 10), HTML_SPECIAL_CHARS_FLAGS) . '">&lt;&lt;</a></span>';
					}
					for ($x = $startPage; $x <= $endPage; $x++) {
						if ($pageNumber != $x) {
							$pageBar .= '<span class="pagination" style="margin-right:3px;margin-right:3px;"><a href="list.php?' . htmlspecialchars($searchVar, HTML_SPECIAL_CHARS_FLAGS) . '&page=' . htmlspecialchars($x, HTML_SPECIAL_CHARS_FLAGS) . '">' . htmlspecialchars($x, HTML_SPECIAL_CHARS_FLAGS) . '</a></span>';
						} else {
							$pageBar .= '<span class="pagination" style="margin-right:3px;margin-right:3px;font-weight:bold;">' . $x . '</span>';
						}
					}
					if (($lastPage - $startPage) >= 10) {
						$pageBar .= '<span class="pagination" style="margin-left:5px;"><a href="list.php?' . htmlspecialchars($searchVar, HTML_SPECIAL_CHARS_FLAGS) . '&page=' . htmlspecialchars((($pageNumber + 10) > $lastPage ? $lastPage : ($pageNumber + 10)), HTML_SPECIAL_CHARS_FLAGS) . '">&gt;&gt;</a></span>';
						$pageBar .= '<span class="pagination" style="margin-left:5px;"><a href="list.php?' . htmlspecialchars($searchVar, HTML_SPECIAL_CHARS_FLAGS) . '&page=' . htmlspecialchars($lastPage, HTML_SPECIAL_CHARS_FLAGS) . '">Last</a></span>';
					}
					$pageBar .= '</div><div style="float:right;margin:5px;">';
					$beginNum = ($pageNumber - 1) * $cntPerPage + 1;
					$endNum = $beginNum + $cntPerPage - 1;
					if ($endNum > $collManager->getRecordCnt()) $endNum = $collManager->getRecordCnt();
					$pageBar .= $LANG['PAGINATION_PAGE'] . ' ' . $pageNumber . ', ' . $LANG['PAGINATION_RECORDS'] . ' ' . $beginNum . '-' . $endNum . ' ' . $LANG['PAGINATION_OF'] . ' ' . $collManager->getRecordCnt();
					$paginationStr .= $pageBar;
					$paginationStr .= '</div><div style="clear:both;"><hr/></div></div>';
					echo $paginationStr;

					//Add search return
					if ($occurArr) {
						?>
						<form name="occurListForm" method="post" action="datasets/datasetHandler.php" onsubmit="return validateOccurListForm(this)" target="_blank">
							<?php include('datasetinclude.php'); ?>
							<table id="omlisttable">
								<?php
								$prevCollid = 0;
								foreach ($occurArr as $occid => $fieldArr) {
									$collId = $fieldArr['collid'];
									if ($collId != $prevCollid) {
										$prevCollid = $collId;
										$isEditor = false;
										if ($SYMB_UID && ($IS_ADMIN || (array_key_exists('CollAdmin', $USER_RIGHTS) && in_array($collId, $USER_RIGHTS['CollAdmin'])) || (array_key_exists('CollEditor', $USER_RIGHTS) && in_array($collId, $USER_RIGHTS['CollEditor'])))) {
											$isEditor = true;
										}
										echo '<tr><td colspan="2"><h2>';
										echo '<a href="misc/collprofiles.php?collid=' . htmlspecialchars($collId, HTML_SPECIAL_CHARS_FLAGS) . '">' . htmlspecialchars($fieldArr["collname"], HTML_SPECIAL_CHARS_FLAGS) . '</a>';
										echo '</h2><hr /></td></tr>';
									}
									echo '<tr><td width="60" valign="top" align="center">';
									echo '<a href="misc/collprofiles.php?collid=' . htmlspecialchars($collId, HTML_SPECIAL_CHARS_FLAGS) . '&acronym=' . htmlspecialchars($fieldArr["instcode"], HTML_SPECIAL_CHARS_FLAGS) . '">';
									if ($fieldArr["icon"]) {
										$icon = (substr($fieldArr["icon"], 0, 6) == 'images' ? '../' : '') . $fieldArr["icon"];
										echo '<img align="bottom" src="' . $icon . '" style="width:35px;border:0px;" />';
									}
									echo '</a>';
									echo '<div style="font-weight:bold;font-size:75%;">';
									$instCode = $fieldArr["instcode"];
									if ($fieldArr["collcode"]) $instCode .= ":" . $fieldArr["collcode"];
									echo $instCode;
									echo '</div>';
									echo '<div style="margin-top:10px"><span class="dataset-div checkbox-elem" style="display:none;"><input name="occid[]" type="checkbox" value="' . $occid . '" /></span></div>';
									echo '</td><td>';
									if ($isEditor || ($SYMB_UID && $SYMB_UID == $fieldArr['obsuid'])) {
										echo '<div style="float:right;" title="' . $LANG['OCCUR_EDIT_TITLE'] . '">';
										echo '<a href="editor/occurrenceeditor.php?occid=' . htmlspecialchars($occid, HTML_SPECIAL_CHARS_FLAGS) . '" target="_blank">';
										echo '<img src="../images/edit.png" srcset="../images/edit.svg" style="width:15px;height:15px;" /></a></div>';
									}
									$targetClid = $collManager->getSearchTerm("targetclid");
									if ($collManager->getClName() && $targetTid && array_key_exists('mode', $_REQUEST)) {
										echo '<div style="float:right;" >';
										echo '<a href="#" onclick="addVoucherToCl(' . $occid . ',' . $targetClid . ',' . $targetTid . ');return false" title="' . $LANG['VOUCHER_LINK_TITLE'] . ' ' . $collManager->getClName() . ';">';
										echo '<img src="../images/voucheradd.png" style="border:solid 1px gray;height:13px;margin-right:5px;" /></a></div>';
									}
									if (isset($fieldArr['img'])) {
										echo '<div style="float:right;margin:5px 25px;">';
										echo '<a href="#" onclick="return openIndPU(' . $occid . ',' . ($targetClid ? $targetClid : "0") . ');">';
										echo '<img src="' . $fieldArr['img'] . '" style="height:70px" /></a></div>';
									}
									echo '<div style="margin:4px;">';
									if (isset($fieldArr['sciname'])) {
										$sciStr = '<span style="font-style:italic;">' . $fieldArr['sciname'] . '</span>';
										if (isset($fieldArr['tid']) && $fieldArr['tid']) $sciStr = '<i> <a target="_blank" href="../taxa/index.php?tid=' . strip_tags($fieldArr['tid']) . '">' . strip_tags($sciStr) . '</a> </i>' ;
										if (isset($fieldArr['author']) && $fieldArr['author']) $sciStr .= ' ' . $fieldArr['author'];
										echo $sciStr;
									} elseif ($fieldArr['localitysecurity'] > 1) {
										echo 'Identification Protected';
									}
									echo '</div>';
									echo '<div style="margin:4px">';
									echo '<span style="width:150px;">' . $fieldArr["catnum"] . '</span>';
									echo '<span style="width:200px;margin-left:30px;">' . $fieldArr["collector"] . '&nbsp;&nbsp;&nbsp;' . (isset($fieldArr["collnum"]) ? $fieldArr["collnum"] : '') . '</span>';
									if (isset($fieldArr["date"])) echo '<span style="margin-left:30px;">' . $fieldArr["date"] . '</span>';
									echo '</div><div style="margin:4px">';
									$localStr = '';
									if ($fieldArr["country"]) $localStr .= $fieldArr["country"];
									if ($fieldArr["state"]) $localStr .= ', ' . $fieldArr["state"];
									if ($fieldArr["county"]) $localStr .= ', ' . $fieldArr["county"];
									if ($fieldArr['locality'] == 'PROTECTED') {
										$localStr .= ', <span style="color:red;">' . $LANG['PROTECTED'] . '</span>';
									} else {
										if ($fieldArr['locality']) $localStr .= ', ' . $fieldArr['locality'];
										if ($fieldArr['declat']) $localStr .= ', ' . $fieldArr['declat'] . ' ' . $fieldArr['declong'];
										if (isset($fieldArr['elev']) && $fieldArr['elev']) $localStr .= ', ' . $fieldArr['elev'] . 'm';
									}
									$localStr = trim($localStr, ' ,');
									echo $localStr;
									echo '</div><div style="margin:4px">';
									echo '<b><a href="#" onclick="return openIndPU(' . $occid . ',' . ($targetClid ? $targetClid : "0") . ');">' . $LANG['FULL_DETAILS'] . '</a></b>';
									echo '</div></td></tr><tr><td colspan="2"><hr/></td></tr>';
								}
								?>
							</table>
						</form>
						<?php
						echo $paginationStr;
						echo '<hr/>';
					}
					else {
						echo '<div><h3>' . $LANG['NO_RESULTS'] . '</h3>';
						$tn = $collManager->getTaxaSearchStr();
						if ($p = strpos($tn, ';')) {
							$tn = substr($tn, 0, $p);
						}
						if ($p = strpos($tn, '=>')) {
							$tn = substr($tn, $p + 2);
						}
						if ($p = strpos($tn, '(')) {
							$tn = substr($tn, 0, $p);
						}
						if ($closeArr = $collManager->getCloseTaxaMatch(trim($tn))) {
							echo '<div style="margin: 40px 0px 200px 20px;font-weight:bold;">';
							echo $LANG['PERHAPS_LOOKING_FOR'] . ' ';
							$outStr = '';
							foreach ($closeArr as $v) {
								$outStr .= '<a href="harvestparams.php?taxa=' . htmlspecialchars($v, HTML_SPECIAL_CHARS_FLAGS) . '">' . htmlspecialchars($v, HTML_SPECIAL_CHARS_FLAGS) . '</a>, ';
							}
							echo trim($outStr, ' ,');
							echo '</div>';
						}
						echo '</div>';
					}
					?>
				</div>
			</div>
			<div id="maps" style="min-height:400px;margin-bottom:10px;">
				<form action="download/index.php" method="post" style="float:right" onsubmit="targetPopup(this)">
					<button class="icon-button" title="<?php echo $LANG['DOWNLOAD_SPECIMEN_DATA']; ?>">
						<img src="../../images/dl2.png" srcset="../images/download.svg" class="svg-icon" style="width:15px; height:15px" />
					</button>
					<input name="searchvar" type="hidden" value="<?php echo $searchVar; ?>" />
					<input name="dltype" type="hidden" value="georef" />
				</form>

				<div style='margin-top:10px;'>
					<h2><?php echo $LANG['GOOGLE_MAP_HEADER']; ?></h2>
				</div>
				<div style='margin:10 0 0 20;'>
					<button>
               <?php if(!empty($LEAFLET)) {?>
                  <a href="#" onclick="openLeafletMapPU();">
							<?php echo $LANG['GOOGLE_MAP_DISPLAY']; ?>
						</a>
               <?php } else {?>
                  <a href="#" onclick="openGoogleMapPU();">
							<?php echo $LANG['GOOGLE_MAP_DISPLAY']; ?>
						</a>
               <?php } ?>
					</button>
				</div>
				<div style='margin:10 0 0 20;'>
					<?php echo $LANG['GOOGLE_MAP_DESCRIPTION']; ?>
				</div>

				<div style='margin-top:10px;'>
					<h2><?php echo $LANG['GOOGLE_EARTH_HEADER']; ?></h2>
				</div>
				<form name="kmlform" action="map/kmlhandler.php" method="post">
					<div style='margin:10 0 0 20;'>
						<?php echo $LANG['GOOGLE_EARTH_DESCRIPTION']; ?>
					</div>
					<div style="margin:20px;">
						<input name="searchvar" type="hidden" value="<?php echo $searchVar; ?>" />
						<button name="formsubmit" type="submit" value="createKML"><?php echo $LANG['CREATE_KML']; ?></button>
					</div>
					<div style='margin:10 0 0 20;'>
						<a href="#" onclick="toggleFieldBox('fieldBox');">
							<?php echo $LANG['GOOGLE_EARTH_EXTRA']; ?>
						</a>
					</div>
					<div id="fieldBox" style="display:none;">
						<fieldset>
							<?php
							$occFieldArr = array(
								'occurrenceid', 'identifiedby', 'dateidentified', 'identificationreferences',
								'identificationremarks', 'taxonremarks', 'recordedby', 'recordnumber', 'associatedcollectors', 'eventdate', 'year', 'month', 'day',
								'verbatimeventdate', 'habitat', 'substrate', 'occurrenceremarks', 'associatedtaxa', 'verbatimattributes',
								'reproductivecondition', 'cultivationstatus', 'establishmentmeans', 'lifestage', 'sex', 'individualcount', 'samplingprotocol', 'preparations',
								'country', 'stateprovince', 'county', 'municipality', 'locality', 'locationremarks', 'coordinateuncertaintyinmeters',
								'verbatimcoordinates', 'georeferencedby', 'georeferenceprotocol', 'georeferencesources', 'georeferenceverificationstatus', 'georeferenceremarks',
								'minimumelevationinmeters', 'maximumelevationinmeters', 'verbatimelevation'
							);
							foreach ($occFieldArr as $k => $v) {
								echo '<div style="float:left;margin-right:5px;">';
								echo '<input type="checkbox" name="kmlFields[]" value="' . $v . '" />' . $v . '</div>';
							}
							?>
						</fieldset>
					</div>
				</form>
			</div>
		</div>
	</div>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>

</html>
