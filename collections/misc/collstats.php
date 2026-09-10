<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/OccurrenceCollectionProfile.php');
include_once($SERVER_ROOT . '/classes/CollectionFormManager.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load(['collections/misc/collstats','collections/search/index']);

header("Content-Type: text/html; charset=" . $CHARSET);
ini_set('max_execution_time', 1200); //1200 seconds = 20 minutes

$catID = array_key_exists('catid', $_REQUEST) ? filter_var($_REQUEST['catid'], FILTER_SANITIZE_NUMBER_INT) : 0;
if(!$catID && isset($DEFAULTCATID) && $DEFAULTCATID) $catID = $DEFAULTCATID;
$collId = array_key_exists('collid', $_REQUEST) ? $_REQUEST['collid'] : 0; // can't sanitize here as int because this could be a comma-delimited set of collIds

$cParentTaxon = isset($_REQUEST['taxon']) ? $_REQUEST['taxon'] : '';
$cCountry = isset($_REQUEST['country']) ? $_REQUEST['country'] : '';
$days = array_key_exists('days', $_REQUEST) ? Sanitize::int($_REQUEST['days']) : 365;
$months = array_key_exists('months', $_REQUEST)? Sanitize::int($_REQUEST['months']) : 12;
$action = array_key_exists('submitaction', $_REQUEST) ? $_REQUEST['submitaction'] : '';

$collManager = new OccurrenceCollectionProfile();

$collectionFormManager = new CollectionFormManager();
$requestSuppliedCatOrd = (array_key_exists('catOrd', $_REQUEST) && $collectionFormManager->areCollectionIdsValid($_REQUEST['catOrd'])) ? explode(',', $_REQUEST['catOrd']) : null;
$requestSuppliedCatExpnd = (array_key_exists('catExpnd', $_REQUEST) && $collectionFormManager->areCollectionCategoriesValid($_REQUEST['catExpnd'])) ? explode(',', $_REQUEST['catExpnd']) : null;
$requestSuppliedCatChk = (array_key_exists('catChk', $_REQUEST) && $collectionFormManager->areCollectionCategoriesValid($_REQUEST['catChk'])) ? explode(',', $_REQUEST['catChk']) : null;

//Variable sanitation
if(!preg_match('/^[0-9,]+$/',$catID)) $catID = 0;
if(!preg_match('/^[0-9,]+$/',$collId)) $collId = 0;

//if($collId) $collManager->setCollectionId($collId);
$collList = $collManager->getStatCollectionList($catID);
$specArr = (isset($collList['spec'])?$collList['spec']:null);
$obsArr = (isset($collList['obs'])?$collList['obs']:null);

$collIdArr = array();
$resultsTemp = array();
$familyArr = array();
$countryArr = array();
$results = array();
$collStr = '';
if($collId){
	$collIdArr = explode(",",$collId);
	if($action == "Show Coll Stats" && (!$cParentTaxon && !$cCountry)){
		$collNameMap = array();
		if(isset($specArr['cat'])){
			foreach($specArr['cat'] as $catArr){
				foreach($catArr as $cid => $cArr){
					if(is_numeric($cid) && is_array($cArr) && array_key_exists('collname', $cArr)){
						$collNameMap[$cid] = $cArr['collname'];
					}
				}
			}
		}
		if(isset($specArr['coll'])){
			foreach($specArr['coll'] as $cid => $cArr){
				if(is_numeric($cid) && array_key_exists('collname', $cArr)){
					$collNameMap[$cid] = $cArr['collname'];
				}
			}
		}
		if(isset($obsArr['cat'])){
			foreach($obsArr['cat'] as $catArr){
				foreach($catArr as $cid => $cArr){
					if(is_numeric($cid) && is_array($cArr) && array_key_exists('collname', $cArr)){
						$collNameMap[$cid] = $cArr['collname'];
					}
				}
			}
		}
		if(isset($obsArr['coll'])){
			foreach($obsArr['coll'] as $cid => $cArr){
				if(is_numeric($cid) && array_key_exists('collname', $cArr)){
					$collNameMap[$cid] = $cArr['collname'];
				}
			}
		}

		foreach($collIdArr as $collidSel){
			if(!is_numeric($collidSel)) continue;
			$collidSel = (int)$collidSel;
			$collManager->setCollid($collidSel);
			$basicStats = $collManager->getBasicStats();
			if(!$basicStats) continue;

			$collectionName = array_key_exists($collidSel, $collNameMap) ? $collNameMap[$collidSel] : ('Collection '.$collidSel);
			$dynPropTempArr = array();
			if(array_key_exists('dynamicProperties', $basicStats) && $basicStats['dynamicProperties']){
				$dynPropTempArr = json_decode($basicStats['dynamicProperties'], true);
				if(!is_array($dynPropTempArr)) $dynPropTempArr = array();
			}

			$totalImageCount = 0;
			if(array_key_exists('imgcnt', $dynPropTempArr) && $dynPropTempArr['imgcnt']){
				$imgSpecCnt = $dynPropTempArr['imgcnt'];
				if(strpos($imgSpecCnt, ':') !== false){
					$imgCntArr = explode(':', $imgSpecCnt);
					$imgSpecCnt = (count($imgCntArr) > 1 ? $imgCntArr[1] : 0);
				}
				$totalImageCount = (int)$imgSpecCnt;
			}

			$resultsTemp[$collectionName]['collid'] = $collidSel;
			$resultsTemp[$collectionName]['CollectionName'] = $collectionName;
			$resultsTemp[$collectionName]['recordcnt'] = (array_key_exists('recordcnt', $basicStats) ? (int)$basicStats['recordcnt'] : 0);
			$resultsTemp[$collectionName]['georefcnt'] = (array_key_exists('georefcnt', $basicStats) ? (int)$basicStats['georefcnt'] : 0);
			$resultsTemp[$collectionName]['familycnt'] = (array_key_exists('familycnt', $basicStats) ? (int)$basicStats['familycnt'] : 0);
			$resultsTemp[$collectionName]['genuscnt'] = (array_key_exists('genuscnt', $basicStats) ? (int)$basicStats['genuscnt'] : 0);
			$resultsTemp[$collectionName]['speciescnt'] = (array_key_exists('speciescnt', $basicStats) ? (int)$basicStats['speciescnt'] : 0);
			$resultsTemp[$collectionName]['TotalTaxaCount'] = (array_key_exists('TotalTaxaCount', $dynPropTempArr) ? (int)$dynPropTempArr['TotalTaxaCount'] : 0);
			$resultsTemp[$collectionName]['OccurrenceImageCount'] = $totalImageCount;
			$resultsTemp[$collectionName]['speciesID'] = (array_key_exists('SpecimensCountID', $dynPropTempArr) ? (int)$dynPropTempArr['SpecimensCountID'] : 0);
			$resultsTemp[$collectionName]['types'] = (array_key_exists('TypeCount', $dynPropTempArr) ? (int)$dynPropTempArr['TypeCount'] : 0);
			$resultsTemp[$collectionName]['dynamicProperties'] = (array_key_exists('dynamicProperties', $basicStats) ? $basicStats['dynamicProperties'] : '');

			if($collStr) $collStr .= ', ';
			$collStr .= $collectionName;

			if(array_key_exists('families', $dynPropTempArr) && is_array($dynPropTempArr['families'])){
				foreach($dynPropTempArr['families'] as $famName => $famArr){
					if(!array_key_exists($famName, $familyArr)){
						$familyArr[$famName]['SpecimensPerFamily'] = 0;
						$familyArr[$famName]['GeorefSpecimensPerFamily'] = 0;
						$familyArr[$famName]['IDSpecimensPerFamily'] = 0;
						$familyArr[$famName]['IDGeorefSpecimensPerFamily'] = 0;
					}
					$familyArr[$famName]['SpecimensPerFamily'] += (array_key_exists('SpecimensPerFamily', $famArr) ? (int)$famArr['SpecimensPerFamily'] : 0);
					$familyArr[$famName]['GeorefSpecimensPerFamily'] += (array_key_exists('GeorefSpecimensPerFamily', $famArr) ? (int)$famArr['GeorefSpecimensPerFamily'] : 0);
					$familyArr[$famName]['IDSpecimensPerFamily'] += (array_key_exists('IDSpecimensPerFamily', $famArr) ? (int)$famArr['IDSpecimensPerFamily'] : 0);
					$familyArr[$famName]['IDGeorefSpecimensPerFamily'] += (array_key_exists('IDGeorefSpecimensPerFamily', $famArr) ? (int)$famArr['IDGeorefSpecimensPerFamily'] : 0);
				}
			}

			if(array_key_exists('countries', $dynPropTempArr) && is_array($dynPropTempArr['countries'])){
				foreach($dynPropTempArr['countries'] as $countryName => $countArr){
					if(!array_key_exists($countryName, $countryArr)){
						$countryArr[$countryName]['CountryCount'] = 0;
						$countryArr[$countryName]['GeorefSpecimensPerCountry'] = 0;
						$countryArr[$countryName]['IDSpecimensPerCountry'] = 0;
						$countryArr[$countryName]['IDGeorefSpecimensPerCountry'] = 0;
					}
					$countryArr[$countryName]['CountryCount'] += (array_key_exists('CountryCount', $countArr) ? (int)$countArr['CountryCount'] : 0);
					$countryArr[$countryName]['GeorefSpecimensPerCountry'] += (array_key_exists('GeorefSpecimensPerCountry', $countArr) ? (int)$countArr['GeorefSpecimensPerCountry'] : 0);
					$countryArr[$countryName]['IDSpecimensPerCountry'] += (array_key_exists('IDSpecimensPerCountry', $countArr) ? (int)$countArr['IDSpecimensPerCountry'] : 0);
					$countryArr[$countryName]['IDGeorefSpecimensPerCountry'] += (array_key_exists('IDGeorefSpecimensPerCountry', $countArr) ? (int)$countArr['IDGeorefSpecimensPerCountry'] : 0);
				}
			}
		}

		ksort($resultsTemp, SORT_STRING | SORT_FLAG_CASE);
		ksort($familyArr, SORT_STRING | SORT_FLAG_CASE);
		ksort($countryArr, SORT_STRING | SORT_FLAG_CASE);

		$_SESSION['statsFamilyArr'] = $familyArr;
		$_SESSION['statsCountryArr'] = $countryArr;
	}
}
if($action != "Update Statistics"){
	?>
	<!DOCTYPE html>
	<html lang="<?= $LANG_TAG ?>">
		<head>
			<meta name="keywords" content="Natural history collections statistics" />
			<title><?= $DEFAULT_TITLE . ' ' . $LANG['COL_STATS'] ?></title>
			<link href="<?= $CSS_BASE_PATH ?>/jquery-ui.css" type="text/css" rel="stylesheet">
			<link href="<?= $CSS_BASE_PATH ?>/searchStyles.css?ver=1" type="text/css" rel="stylesheet">
			<link href="<?= $CSS_BASE_PATH ?>/searchStylesInner.css" type="text/css" rel="stylesheet">
			<?php
			include_once($SERVER_ROOT.'/includes/head.php');
			?>
			<link href="<?= $CSS_BASE_PATH ?>/symbiota/collections/listdisplay.css" type="text/css" rel="stylesheet" />
			<link href="<?= $CSS_BASE_PATH ?>/symbiota/collections/sharedCollectionStyling.css" type="text/css" rel="stylesheet" />
            <script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
			<script src="<?= $CLIENT_ROOT ?>/js/jquery-ui.min.js" type="text/javascript"></script>
			<script src="../../js/symb/collections.index.js" type="text/javascript"></script>
			<script type="text/javascript">
				$(document).ready(function() {
					if(!navigator.cookieEnabled){
						alert("<?= $LANG['NEED_COOKIES'] ?>");
					}
					$("#tabs").tabs({<?= (($action == "Show Coll Stats")?'active: 1':'') ?>});

					const taxonInput = document.querySelector('#taxon');
					if(taxonInput){
						taxonInput.addEventListener('focus', (event) => {
							taxaSuggest.config.clientRoot = "<?= $CLIENT_ROOT ?>";
							taxaSuggest.config.minLength = 2;
							taxaSuggest.initiate("taxon");
						});
					}

				});

				function toggleDisplayListOfCollectionsAnalyzed(){
					toggleById("colllist");
					toggleById("colllistlabel");
				}

				function toggleStatsPerColl(){
					toggleById("statspercollbox");
					toggleById("showstatspercoll");
					toggleById("hidestatspercoll");

					document.getElementById("geodistbox").style.display="none";
					document.getElementById("showgeodist").style.display="block";
					document.getElementById("hidegeodist").style.display="none";
					document.getElementById("famdistbox").style.display="none";
					document.getElementById("showfamdist").style.display="block";
					document.getElementById("hidefamdist").style.display="none";
					return false;
				}

				function toggleFamilyDist(){
					toggleById("famdistbox");
					toggleById("showfamdist");
					toggleById("hidefamdist");

					document.getElementById("geodistbox").style.display="none";
					document.getElementById("showgeodist").style.display="block";
					document.getElementById("hidegeodist").style.display="none";
					document.getElementById("statspercollbox").style.display="none";
					document.getElementById("showstatspercoll").style.display="block";
					document.getElementById("hidestatspercoll").style.display="none";
					return false;
				}

				function toggleGeoDist(){
					toggleById("geodistbox");
					toggleById("showgeodist");
					toggleById("hidegeodist");

					document.getElementById("famdistbox").style.display="none";
					document.getElementById("showfamdist").style.display="block";
					document.getElementById("hidefamdist").style.display="none";
					document.getElementById("statspercollbox").style.display="none";
					document.getElementById("showstatspercoll").style.display="block";
					document.getElementById("hidestatspercoll").style.display="none";
					return false;
				}

				function toggleById(target){
					if(target != null){
						var obj = document.getElementById(target);
						var style = window.getComputedStyle(obj);

						if(style.display=="none" || style.display==""){
							obj.style.display="block";
						}
						else {
							obj.style.display="none";
						}
					}
					return false;
				}

			</script>
			<style>
				.icon-mrgn-rel {
					margin-bottom: 0.6rem;
				}
				.gridlike-form-row-align {
					flex: 1;
					text-align: center;
				}
				.gridlike-form-no-margin {
					display: flex;
					flex-direction: column;
				}
			</style>
		</head>
		<body>
			<?php
			$displayLeftMenu = (isset($collections_misc_collstatsMenu)?$collections_misc_collstatsMenu:false);
			include($SERVER_ROOT.'/includes/header.php');
			?>
			<div class='navpath'>
				<a href='../../index.php'><?= $LANG['HOME'] ?></a> &gt;&gt;
				<a href='collprofiles.php'><?= $LANG['COLLECTIONS'] ?></a> &gt;&gt;
				<b><?= $LANG['COL_STATS'] ?></b>
			</div>
			<div role="main" id="innertext" class="inntertext-tab pin-things-here inner-search">
				<h1 class="page-heading"><?= $LANG['SELECT_COLS'] ?></h1>
				<div id="error-msgs" class="errors"></div>
				<div id="tabs" class="tabby">
					<ul class="full-tab">
						<li><a href="#specobsdiv"><?= $LANG['COLLECTIONS'] ?></a></li>
						<?php
						if($action == "Show Coll Stats"){
							echo '<li><a href="#statsdiv">' . $LANG['STATISTICS'] . '</a></li>';
						}
						?>
					</ul>

					<div id="specobsdiv" class="pin-things-here">
							<form class="content" name="params-form" id="params-form" action="collstats.php" method="post" style="grid-template-columns: none;">
								<input type="hidden" name="submitaction" id="submitaction-hidden">
								<div>
									<?php
									if($SYMB_UID && ($IS_ADMIN || array_key_exists("CollAdmin",$USER_RIGHTS))){
										?>
										<div style="display: flex; justify-content: flex-end; position: sticky; top: 1rem;">
											<button style="width: 312px; margin-right: 0.5rem;" id="view-stats" type="submit" name="submitaction" value="Show Coll Stats"><?= $LANG['VIEW_STATS'] ?></button>
										</div>
										<fieldset class="fieldset-padding flex-form">
											<legend><b><?= $LANG['REC_CRITERIA'] ?></b></legend>
											<div class="record-criteria-inputs">
												<label for="taxon"><?= $LANG['PARENT_CRITERIA'] ?>: </label>
												<input type="text" id="taxon" name="taxon" size="43" value="<?= Sanitize::outString($cParentTaxon) ?>" />
											</div>
											<div class="record-criteria-inputs">
												<label for="country"><?= $LANG['COUNTRY'] ?>: </label>
												<input type="text" id="country" name="country" size="43" value="<?= Sanitize::outString($cCountry) ?>" />
											</div>
										</fieldset>
										<fieldset style="margin-top:1rem;" class="fieldset-padding flex-form">
											<div class="content">
												<div id="search-form-colls">
													<!-- Open Collections modal -->
													<div id="specobsdiv">
														<?php
														include($SERVER_ROOT . '/collections/collectionForm.php');
														?>
													</div>
												</div>
											</div>
										</fieldset>
									<?php
									$collArrIndex = 0;
									if($specArr){
										$collCnt = 0;
										if(isset($specArr['cat'])){
											$categoryArr = $specArr['cat'];
											?>
											<!--  -->
											<?php
										}
										if(isset($specArr['coll'])){
											$collArr = $specArr['coll'];
											?>
											<section class="gridlike-form">
											<?php
											if(!isset($specArr['cat'])){
												echo '</section>';
											}
										}
										$collArrIndex++;
									}
									if($obsArr){
										$collCnt = 0;
										if(isset($obsArr['coll'])){
											$collArr = $obsArr['coll'];
											?>
											<section>
												<fieldset class="fieldset-padding">
													<h2 class="section-heading"><?= $LANG['PERSONAL_OBSERVATION_COLLECTIONS'] ?></h2>
													<fieldset class="observation-fieldset">
													<?php
													foreach($collArr as $collid => $cArr){
														?>
															<div>
																<input id="db-<?= $collid ?>" name="db[]" value="<?= $collid ?>" type="checkbox" onclick="uncheckAll();" <?= ($collIdArr&&in_array($collid,$collIdArr)?'checked':'') ?> />
																<label for="db-<?= $collid ?>"><?= $LANG['SELECT_DESELECT'] ?></label>
															</div>
															<div class="gridlike-form-row bottom-breathing-room-rel">
																<div class="collectiontitle">
																	<a href = 'collprofiles.php?collid=<?= $collid ?>'>
																		<?php
																		$codeStr = ' ('.$cArr['instcode'];
																		if($cArr['collcode']) $codeStr .= '-'.$cArr['collcode'];
																		$codeStr .= ')';
																		echo $cArr["collname"].$codeStr;
																		?>
																		- <?= $LANG['MORE_INFO'] ?>
																	</a>
																</div>
															</div>
														<?php
														$collCnt++;
													}
													?>
													</fieldset>
												</fieldset>
											</section>
											<?php
										}
										$collArrIndex++;
									}
									?>
									<div class="clr">&nbsp;</div>
									<input type="hidden" name="collid" id="colltxt" value="" />
									<input type="hidden" name="days" value="<?= $days ?>" />
									<input type="hidden" name="months" value="<?= $months ?>" />
								</div>
                            </form>
                            <?php
                        }
						else{
							echo '<div class="top-marg"><div class="heavy-txt">' . $LANG['NO_COLLECTIONS'] . '</div></div>';
							echo '</div></form>';
						}
						?>
					</div>

                    <?php
					if($action == "Show Coll Stats"){
						?>
						<div id="statsdiv">
							<div class="mn-ht">
								<div>
									<h1><?= $LANG['SEL_COL_STATS'] ?></h1>
									<div class="big-fnt-margin">
										<div id="colllistlabel"><a href="#" onclick="return toggleDisplayListOfCollectionsAnalyzed();"><?= $LANG['DISPLAY_LIST'] ?></a></div>
										<div id="colllist" class="dsply-none">
											<?= $collStr ?>
										</div>
									</div>
									<fieldset class="stats-display-fieldset">
										<legend><?= $LANG['GENERAL_STATISTICS']?></legend>
										<form name="statscsv" id="statscsv" action="collstatscsv.php" method="post" onsubmit="">
											<div class="stat-csv-margin gridlike-form-no-margin">
												<div class="gridlike-form-row">
													<div id="showstatspercoll" class="float-and-no-display" >
														<a href="#" onclick="return toggleStatsPerColl()"><?= $LANG['SHOW_PER_COL'] ?></a>
													</div>
													<div id="hidestatspercoll" class="float-and-block" >
														<a href="#" onclick="return toggleStatsPerColl()"><?= $LANG['HIDE_STATS'] ?></a>
													</div>
													<div class="stat-csv-float-margins icon-mrgn-rel" title="<?= $LANG['SAVE_CSV'] ?>">
														<input type="hidden" name="collids" id="collids" value='<?= $collId ?>' />
														<input type="hidden" name="taxon" value='<?= Sanitize::outString($cParentTaxon) ?>' />
														<input type="hidden" name="country" value='<?= Sanitize::outString($cCountry) ?>' />
														<input type="hidden" name="action" id="action" value='<?= $LANG['DOWNLOAD_STATS'] ?>' />
														<input type="image" name="action" src="../../images/dl.png" style="width:1.3em" onclick="" />
														<!--input type="submit" name="action" value="Download Stats per Coll" src="../../images/dl.png" / -->
													</div>
												</div>
											</div>
										</form>
									</fieldset>
										<fieldset class="extra-stats bottom-breathing-room-rel">
											<legend><?= $LANG['EXTRA_STATS'] ?></legend>
											<form name="famstatscsv" id="famstatscsv" action="collstatscsv.php" method="post" onsubmit="">
												<!-- <div class='legend'> -->
												<!-- </div> -->
												<div class="gridlike-form-no-margin">
													<div class="stat-csv-margin gridlike-form-row">
														<div id="showfamdist" class="float-and-block" >
															<a href="#" onclick="return toggleFamilyDist()"><?= $LANG['SHOW_FAMILY'] ?></a>
														</div>
														<div id="hidefamdist" class="float-and-no-display" >
															<a href="#" onclick="return toggleFamilyDist()"><?= $LANG['HIDE_FAMILY'] ?></a>
														</div>
														<div class="stat-csv-float-margins icon-mrgn-rel" title="<?= $LANG['SAVE_CSV'] ?>">
															<input type="hidden" name="action" value='Download Family Dist'/>
															<input type="image" name="action" src="../../images/dl.png" style="width:1.3em" onclick="" />
														</div>
													</div>
												</div>
											</form>
											<form name="geostatscsv" id="geostatscsv" action="collstatscsv.php" method="post" onsubmit="">
												<div class="clr gridlike-form-no-margin">
													<div class="gridlike-form-row">
														<div id="showgeodist" class="float-and-block" >
															<a href="#" onclick="return toggleGeoDist()"><?= $LANG['SHOW_GEO'] ?></a>
														</div>
														<div id="hidegeodist" class="float-and-no-display">
															<a href="#" onclick="return toggleGeoDist();"><?= $LANG['HIDE_GEO'] ?></a>
														</div>
														<div class="stat-csv-float-margins icon-mrgn-rel" title="<?= $LANG['SAVE_CSV'] ?>">
															<input type="hidden" name="action" value='Download Geo Dist' />
															<input type="image" name="action" src="../../images/dl.png" style="width:1.3em" onclick="" />
														</div>
													</div>
												</div>
											</form>
                                            <?php
                                            if(!$cParentTaxon && !$cCountry){
												$specimenCount = array_key_exists('SpecimenCount', $results) ? $results['SpecimenCount'] : 0;
                                                ?>
                                                <div class="top-breathing-room-rel">
                                                    <form name="orderstats" class="no-btm-mrgn" action="collorderstats.php" method="post" target="_blank">
                                                        <input type="hidden" name="collid" id="collid" value='<?= $collId ?>'/>
                                                        <input type="hidden" name="totalcnt" id="totalcnt" value='<?= $specimenCount ?>'/>
                                                        <button type="submit" name="action" value="Load Order Distribution"><?= $LANG['LOAD_ORDER'] ?></button>
                                                    </form>
                                                </div>
                                                <?php
                                            }
                                            ?>
										</fieldset>
										<?php
										if(!$cParentTaxon && !$cCountry){
                                            if ($SYMB_UID && ($IS_ADMIN || array_key_exists("CollAdmin", $USER_RIGHTS))) {
                                                ?>
                                                <fieldset id="yearstatsbox" class="yearstatbox-width">
                                                    <legend><b><?= $LANG['YEAR_STATS'] ?></b></legend>
                                                    <form name="yearstats" class="no-btm-mrgn" action="collyearstats.php" method="post" target="_blank" class="flex-form">
                                                        <input type="hidden" name="collid" id="collid" value='<?= $collId ?>'/>
                                                        <input type="hidden" name="days" value="<?= $days ?>"/>
                                                        <input type="hidden" name="months" value="<?= $months ?>"/>
                                                        <div class="yearstatbox-left-float">
                                                            <?= $LANG['YEARS'] ?>: <input type="text" id="years" size="5" name="years" value="1" />
                                                        </div>
                                                        <div class="yearstatbox-submit-btn-margin">
                                                            <button type="submit" name="action" value="Load Stats"><?= $LANG['LOAD_STATS'] ?></button>
                                                        </div>
                                                    </form>
                                                </fieldset>
                                                <?php
                                            }
                                        }
                                        ?>
									<div class="clr"> </div>
								</div>

								<fieldset id="statspercollbox" class="statspercollbox">
									<legend><b><?= $LANG['STATS_PER_COL'] ?></b></legend>
									<section class="gridlike-form">
										<section class="gridlike-form-row bottom-breathing-room-rel">
											<div class="cntr-text gridlike-form-row-align"><?= $LANG['COLLECTION'] ?></div>
											<div class="cntr-text gridlike-form-row-align"><?= $LANG['OCCS'] ?></div>
											<div class="cntr-text gridlike-form-row-align"><?= $LANG['G_GEOREFERENCED'] ?></div>
											<div class="cntr-text gridlike-form-row-align"><?= $LANG['IMAGED'] ?></div>
											<div class="cntr-text gridlike-form-row-align"><?= $LANG['SPECIES_ID'] ?></div>
											<div class="cntr-text gridlike-form-row-align"><?= $LANG['F_FAMILIES'] ?></div>
											<div class="cntr-text gridlike-form-row-align"><?= $LANG['G_GENERA'] ?></div>
											<div class="cntr-text gridlike-form-row-align"><?= $LANG['S_SPECIES'] ?></div>
											<div class="cntr-text gridlike-form-row-align"><?= $LANG['T_TOTAL_TAXA'] ?></div>
											<!-- <th class="cntr-text">Types</th> -->
										</section>
										<?php
										foreach($resultsTemp as $name => $data){
											echo '<section class="gridlike-form-row bottom-breathing-room-rel">';
											echo '<div class="gridlike-form-row-align">'.wordwrap($name,40,"<br />\n",true).'</div>';
											echo '<div class="gridlike-form-row-align">'.(array_key_exists('recordcnt',$data)?$data['recordcnt']:0).'</div>';
											echo '<div class="gridlike-form-row-align">'.(array_key_exists('georefcnt',$data)?$data['georefcnt']:0).'</div>';
											echo '<div class="gridlike-form-row-align">'.(array_key_exists('OccurrenceImageCount',$data)?$data['OccurrenceImageCount']:0).'</div>';
											echo '<div class="gridlike-form-row-align">'.(array_key_exists('speciesID',$data)?$data['speciesID']:0).'</div>';
											echo '<div class="gridlike-form-row-align">'.(array_key_exists('familycnt',$data)?$data['familycnt']:0).'</div>';
											echo '<div class="gridlike-form-row-align">'.(array_key_exists('genuscnt',$data)?$data['genuscnt']:0).'</div>';
											echo '<div class="gridlike-form-row-align">'.(array_key_exists('speciescnt',$data)?$data['speciescnt']:0).'</div>';
											echo '<div class="gridlike-form-row-align">'.(array_key_exists('TotalTaxaCount',$data)?$data['TotalTaxaCount']:0).'</div>';
											//echo '<td>'.(array_key_exists('types',$data)?$data['types']:0).'</td>';
											echo '</section>';
										}
										?>
									</section>
								</fieldset>
								<fieldset id="famdistbox" class="famdistbox">
									<legend><b><?= $LANG['FAM_DIST'] ?></b></legend>
									<section class="gridlike-form">
										<section class="gridlike-form-row bottom-breathing-room-rel">
											<div class="cntr-text gridlike-form-row-align">
											<?= $LANG['FAMILY'] ?>
										</div>
											<div class="cntr-text gridlike-form-row-align">
											<?= $LANG['SPECIMENS'] ?>
										</div>
											<div class="cntr-text gridlike-form-row-align">
											<?= $LANG['G_GEOREFERENCED'] ?>
										</div>
											<div class="cntr-text gridlike-form-row-align">
											<?= $LANG['SPECIES_ID'] ?>
										</div>
											<div class="cntr-text gridlike-form-row-align">
												<?= $LANG['G_GEOREFERENCED'] ?>
												<br />
												<?= $LANG['AND'] ?>
												<br />
												<?= $LANG['SPECIES_ID'] ?>
											</div>
										</section>
										<?php
										$total = 0;
										foreach($familyArr as $name => $data){
											echo '<section class="gridlike-form-row">';
											echo '<div class="gridlike-form-row-align">'.wordwrap($name,52,"<br />\n",true).'</div>';
											echo '<div class="gridlike-form-row-align">';
											if(count($resultsTemp) == 1){
												echo '<a href="../list.php?db[]=' . $collId . '&reset=1&taxa=' . Sanitize::outString($name) . '" target="_blank" rel="noopener noreferrer">';
											}
											echo number_format($data['SpecimensPerFamily']);
											if(count($resultsTemp) == 1){
												echo '</a>';
											}
											echo '</div>';
											try {
												echo '<div class="gridlike-form-row-align">'.($data['GeorefSpecimensPerFamily'] ? round(100*($data['GeorefSpecimensPerFamily']/$data['SpecimensPerFamily'])) : 0).'%</div>';
												echo '<div class="gridlike-form-row-align">'.($data['IDSpecimensPerFamily'] ? round(100*($data['IDSpecimensPerFamily']/$data['SpecimensPerFamily'])) : 0).'%</div>';
												echo '<div class="gridlike-form-row-align">'.($data['IDGeorefSpecimensPerFamily'] ? round(100*($data['IDGeorefSpecimensPerFamily']/$data['SpecimensPerFamily'])) : 0).'%</div>';
											} catch (Exception $e) {
												error_log('Exception: ' . $e->getMessage());
											}
											echo '</section>';
											$total = $total + $data['SpecimensPerFamily'];
										}
										?>
									</section>
									<div class="top-marg">
										<b><?= $LANG['SPEC_W_FAMILY'] ?>:</b> <?= number_format($total) ?><br />
										<?php
										if ($results){
											echo $LANG['SPEC_WO_FAMILY'] ?>: <?= number_format($results['SpecimenCount']-$total);
										}
										?><br />
									</div>
								</fieldset>
								<fieldset id="geodistbox" class="geodistbox">
									<legend><b><?= $LANG['GEO_DIST'] ?></b></legend>
									<section class="gridlike-form">
										<section class="gridlike-form-row bottom-breathing-room-rel">
											<div class="cntr-text gridlike-form-row-align">
											<?= $LANG['COUNTRY'] ?>
										</div>
											<div class="cntr-text gridlike-form-row-align">
											<?= $LANG['SPECIMENS'] ?>
										</div>
											<div class="cntr-text gridlike-form-row-align">
											<?= $LANG['G_GEOREFERENCED'] ?>
										</div>
											<div class="cntr-text gridlike-form-row-align">
											<?= $LANG['SPECIES_ID'] ?>
										</div>
											<div class="cntr-text gridlike-form-row-align">
												<?= $LANG['G_GEOREFERENCED'] ?>
												<br />
												<?= $LANG['AND'] ?>
												<br />
												<?= $LANG['SPECIES_ID'] ?>
											</div>
										</section>
										<?php
										$total = 0;
										foreach($countryArr as $name => $data){
											echo '<section class="gridlike-form-row">';
											echo '<div class="gridlike-form-row-align">'.wordwrap($name,52,"<br />\n",true).'</div>';
											echo '<div class="gridlike-form-row-align">';
											if(count($resultsTemp) == 1){
												echo '<a href="../list.php?db[]=' . $collId . '&reset=1&country=' . Sanitize::outString($name) . '" target="_blank" rel="noopener noreferrer">';
											}
											echo number_format($data['CountryCount']);
											if(count($resultsTemp) == 1){
												echo '</a>';
											}
											echo '</div>';
											try {
												echo '<div class="gridlike-form-row-align">'.($data['GeorefSpecimensPerCountry'] ? round(100*($data['GeorefSpecimensPerCountry']/$data['CountryCount'])) : 0).'%</div>';
												echo '<div class="gridlike-form-row-align">'.($data['IDSpecimensPerCountry'] ? round(100*($data['IDSpecimensPerCountry']/$data['CountryCount'])) : 0).'%</div>';
												echo '<div class="gridlike-form-row-align">'.($data['IDGeorefSpecimensPerCountry'] ? round(100*($data['IDGeorefSpecimensPerCountry']/$data['CountryCount'])) : 0).'%</div>';
											} catch (Exception $e) {
												error_log('Exception: ' . $e->getMessage());
											}
											echo '</section>';
											$total = $total + $data['CountryCount'];
										}
										?>
									</section>
									<div class="top-marg">
										<b><?= $LANG['SPEC_W_COUNTRY'] ?>:</b> <?= number_format($total) ?><br />
										<?php
										if ($results){
											echo $LANG['SPEC_WO_COUNTRY'] ?>: <?= number_format(($results['SpecimenCount']-$total)+$results['SpecimensNullLatitude']);
										}
										?>
										<br />
									</div>
								</fieldset>
							</div>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<!-- end inner text -->
			<?php
			include($SERVER_ROOT.'/includes/footer.php');
			?>
		</body>
		<script src="<?= $CLIENT_ROOT ?>/js/symb/searchform.js?ver=2" type="text/javascript"></script>
		<script src="<?= $CLIENT_ROOT ?>/js/alerts.js?v=202107" type="text/javascript"></script>
		<script src="<?= $CLIENT_ROOT ?>/js/symb/collections.list.js?ver=20171215>" type="text/javascript"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				// setSessionQueryStr();
				setSearchForm(document.getElementById("params-form"));
				toggleAccordionsFromSessionStorage(sessionStorage.getItem("querystr" + getCurrentPage() + "/" + "accordionIds") ?.split(",") || []);
				document.getElementById("params-form").addEventListener("submit", function(event) {
					const submitter = event.submitter;
					const submitActionValue = submitter.value;
					document.getElementById("submitaction-hidden").value = submitActionValue;
					if (!submitter) return;
					event.preventDefault();
					const dbElements = document.getElementsByName("db[]");
					let hasCollSelected = false;
					let collIds = "";
					for(i = 0; i < dbElements.length; i++){
						const dbElement = dbElements[i];
						if(dbElement.checked && !isNaN(dbElement.value)){
							if(hasCollSelected == true) collIds = collIds+",";
							collIds = collIds + dbElement.value;
							hasCollSelected = true;
						}
					}
					if(hasCollSelected == true){
						const collElem = document.getElementById("colltxt");
						collElem.value = collIds;
						const submitForm = document.getElementById("params-form");
						storeFormDataInSessionStorage(submitForm);
						submitForm.submit();
					}
					else{
						alert("<?= $LANG['CHOOSE_ONE'] ?>");
						return false;
					}
				});
				document.getElementById("reset-btn").addEventListener("click", function (event) {
					document.getElementById("params-form").reset();
					clearPageSpecificSessionStorageItems();
					checkTheCollectionsThatShouldBeCheckedBasedOnConfig();
					closeAllCategories();
					expandCategoriesBasedOnConfig();
					updateChip(event, isInitialConfig=true);
				});
			});
		</script>
	</html>
	<?php
}
?>