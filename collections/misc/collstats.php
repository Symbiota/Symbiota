<!DOCTYPE html>
<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceCollectionProfile.php');
include_once($SERVER_ROOT.'/content/lang/collections/misc/collstats.'.$LANG_TAG.'.php');
header("Content-Type: text/html; charset=".$CHARSET);
ini_set('max_execution_time', 1200); //1200 seconds = 20 minutes

$catID = array_key_exists('catid', $_REQUEST) ? $_REQUEST['catid'] : 0;
if(!$catID && isset($DEFAULTCATID) && $DEFAULTCATID) $catID = $DEFAULTCATID;
$collId = array_key_exists('collid', $_REQUEST) ? $_REQUEST['collid'] : 0;
$cPartentTaxon = array_key_exists('taxon', $_REQUEST) ? $_REQUEST['taxon'] : '';
$cCountry = array_key_exists('country', $_REQUEST) ? $_REQUEST['country'] : '';
$days = array_key_exists('days', $_REQUEST) ? filter_var($_REQUEST['days'], FILTER_SANITIZE_NUMBER_INT) : 365;
$months = array_key_exists('months', $_REQUEST)? filter_var($_REQUEST['months'], FILTER_SANITIZE_NUMBER_INT) : 12;
$action = array_key_exists('submitaction', $_REQUEST) ? $_REQUEST['submitaction'] : '';

//Variable sanitation
if(!preg_match('/^[0-9,]+$/',$catID)) $catID = 0;
if(!preg_match('/^[0-9,]+$/',$collId)) $collId = 0;

$collManager = new OccurrenceCollectionProfile();

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
	if($action == "Run Statistics" && (!$cPartentTaxon && !$cCountry)){
		$resultsTemp = $collManager->runStatistics($collId);
		$results['FamilyCount'] = $resultsTemp['familycnt'];
		$results['GeneraCount'] = $resultsTemp['genuscnt'];
		$results['SpeciesCount'] = $resultsTemp['speciescnt'];
		$results['TotalTaxaCount'] = $resultsTemp['TotalTaxaCount'];
		$results['TotalImageCount'] = $resultsTemp['TotalImageCount'];
		unset($resultsTemp['familycnt']);
		unset($resultsTemp['genuscnt']);
		unset($resultsTemp['speciescnt']);
		unset($resultsTemp['TotalTaxaCount']);
		unset($resultsTemp['TotalImageCount']);
		ksort($resultsTemp, SORT_STRING | SORT_FLAG_CASE);
		$c = 0;
		foreach($resultsTemp as $k => $collArr){
			$dynPropTempArr = array();
			$familyTempArr = array();
			$countryTempArr = array();
			if($c>0) $collStr .= ", ";
			$collStr .= $collArr['CollectionName'];
			if(array_key_exists("SpecimenCount",$results)){
				$results['SpecimenCount'] = $results['SpecimenCount'] + $collArr['recordcnt'];
			}
			else{
				$results['SpecimenCount'] = $collArr['recordcnt'];
			}

			if(array_key_exists("GeorefCount",$results)){
				$results['GeorefCount'] = $results['GeorefCount'] + $collArr['georefcnt'];
			}
			else{
				$results['GeorefCount'] = $collArr['georefcnt'];
			}

			if($collArr['dynamicProperties']){
				$dynPropTempArr = json_decode($collArr['dynamicProperties'],true);

				if(is_array($dynPropTempArr)){
					$resultsTemp[$k]['speciesID'] = $dynPropTempArr['SpecimensCountID'];
					$resultsTemp[$k]['types'] = $dynPropTempArr['TypeCount'];

					if(array_key_exists("SpecimensCountID",$results)){
						$results['SpecimensCountID'] = $results['SpecimensCountID'] + $dynPropTempArr['SpecimensCountID'];
					}
					else{
						$results['SpecimensCountID'] = $dynPropTempArr['SpecimensCountID'];
					}

					if(array_key_exists("TypeCount",$results)){
						$results['TypeCount'] = $results['TypeCount'] + $dynPropTempArr['TypeCount'];
					}
					else{
						$results['TypeCount'] = $dynPropTempArr['TypeCount'];
					}

					if(array_key_exists("families",$dynPropTempArr)){
						$familyTempArr = $dynPropTempArr['families'];
						foreach($familyTempArr as $k => $famArr){
							if(array_key_exists($k,$familyArr)){
								$familyArr[$k]['SpecimensPerFamily'] = $familyArr[$k]['SpecimensPerFamily'] + $famArr['SpecimensPerFamily'];
								$familyArr[$k]['GeorefSpecimensPerFamily'] = $familyArr[$k]['GeorefSpecimensPerFamily'] + $famArr['GeorefSpecimensPerFamily'];
								$familyArr[$k]['IDSpecimensPerFamily'] = $familyArr[$k]['IDSpecimensPerFamily'] + $famArr['IDSpecimensPerFamily'];
								$familyArr[$k]['IDGeorefSpecimensPerFamily'] = $familyArr[$k]['IDGeorefSpecimensPerFamily'] + $famArr['IDGeorefSpecimensPerFamily'];
							}
							else{
								$familyArr[$k]['SpecimensPerFamily'] = $famArr['SpecimensPerFamily'];
								$familyArr[$k]['GeorefSpecimensPerFamily'] = $famArr['GeorefSpecimensPerFamily'];
								$familyArr[$k]['IDSpecimensPerFamily'] = $famArr['IDSpecimensPerFamily'];
								$familyArr[$k]['IDGeorefSpecimensPerFamily'] = $famArr['IDGeorefSpecimensPerFamily'];
							}
						}
						ksort($familyArr, SORT_STRING | SORT_FLAG_CASE);
					}

					if(array_key_exists("countries",$dynPropTempArr)){
						$countryTempArr = $dynPropTempArr['countries'];
						foreach($countryTempArr as $k => $countArr){
							if(array_key_exists($k,$countryArr)){
								$countryArr[$k]['CountryCount'] = $countryArr[$k]['CountryCount'] + $countArr['CountryCount'];
								$countryArr[$k]['GeorefSpecimensPerCountry'] = $countryArr[$k]['GeorefSpecimensPerCountry'] + $countArr['GeorefSpecimensPerCountry'];
								$countryArr[$k]['IDSpecimensPerCountry'] = $countryArr[$k]['IDSpecimensPerCountry'] + $countArr['IDSpecimensPerCountry'];
								$countryArr[$k]['IDGeorefSpecimensPerCountry'] = $countryArr[$k]['IDGeorefSpecimensPerCountry'] + $countArr['IDGeorefSpecimensPerCountry'];
							}
							else{
								$countryArr[$k]['CountryCount'] = $countArr['CountryCount'];
								$countryArr[$k]['GeorefSpecimensPerCountry'] = $countArr['GeorefSpecimensPerCountry'];
								$countryArr[$k]['IDSpecimensPerCountry'] = $countArr['IDSpecimensPerCountry'];
								$countryArr[$k]['IDGeorefSpecimensPerCountry'] = $countArr['IDGeorefSpecimensPerCountry'];
							}
						}
						ksort($countryArr, SORT_STRING | SORT_FLAG_CASE);
					}
				}
			}
			$c++;
		}
		$results['SpecimensNullLatitude'] = $results['SpecimenCount'] - $results['GeorefCount'];
	}
    elseif($action == "Run Statistics" && ($cPartentTaxon || $cCountry)){
        $resultsTemp = $collManager->runStatisticsQuery($collId,$cPartentTaxon,$cCountry);
        $familyArr = $resultsTemp['families'];
        ksort($familyArr, SORT_STRING | SORT_FLAG_CASE);
        $countryArr = $resultsTemp['countries'];
        ksort($countryArr, SORT_STRING | SORT_FLAG_CASE);
        unset($resultsTemp['families']);
        unset($resultsTemp['countries']);
        ksort($resultsTemp, SORT_STRING | SORT_FLAG_CASE);
        $c = 0;
        foreach($resultsTemp as $k => $collArr){
            if($c>0) $collStr .= ", ";
            $collStr .= $collArr['CollectionName'];
            if(array_key_exists("SpecimenCount",$results)){
                $results['SpecimenCount'] = $results['SpecimenCount'] + $collArr['recordcnt'];
            }
            else{
                $results['SpecimenCount'] = $collArr['recordcnt'];
            }

            if(array_key_exists("GeorefCount",$results)){
                $results['GeorefCount'] = $results['GeorefCount'] + $collArr['georefcnt'];
            }
            else{
                $results['GeorefCount'] = $collArr['georefcnt'];
            }

            if(array_key_exists("FamilyCount",$results)){
                $results['FamilyCount'] = $results['FamilyCount'] + $collArr['familycnt'];
            }
            else{
                $results['FamilyCount'] = $collArr['familycnt'];
            }

            if(array_key_exists("GeneraCount",$results)){
                $results['GeneraCount'] = $results['GeneraCount'] + $collArr['genuscnt'];
            }
            else{
                $results['GeneraCount'] = $collArr['genuscnt'];
            }

            if(array_key_exists("SpeciesCount",$results)){
                $results['SpeciesCount'] = $results['SpeciesCount'] + $collArr['speciescnt'];
            }
            else{
                $results['SpeciesCount'] = $collArr['speciescnt'];
            }

            if(array_key_exists("TotalTaxaCount",$results)){
                $results['TotalTaxaCount'] = $results['TotalTaxaCount'] + $collArr['TotalTaxaCount'];
            }
            else{
                $results['TotalTaxaCount'] = $collArr['TotalTaxaCount'];
            }

            if(array_key_exists("TotalImageCount",$results)){
                $results['TotalImageCount'] = $results['TotalImageCount'] + $collArr['OccurrenceImageCount'];
            }
            else{
                $results['TotalImageCount'] = $collArr['OccurrenceImageCount'];
            }

            if(array_key_exists("SpecimensCountID",$results)){
                $results['SpecimensCountID'] = $results['SpecimensCountID'] + $collArr['speciesID'];
            }
            else{
                $results['SpecimensCountID'] = $collArr['speciesID'];
            }

            if(array_key_exists("TypeCount",$results)){
                $results['TypeCount'] = $results['TypeCount'] + $collArr['types'];
            }
            else{
                $results['TypeCount'] = $collArr['types'];
            }

            $c++;
        }
        $results['SpecimensNullLatitude'] = $results['SpecimenCount'] - $results['GeorefCount'];
    }
	if($action == "Update Statistics"){
		$collManager->batchUpdateStatistics($collId);
		echo '<script type="text/javascript">window.location="collstats.php?collid='.$collId.'"</script>';
	}
    $_SESSION['statsFamilyArr'] = $familyArr;
    $_SESSION['statsCountryArr'] = $countryArr;
}
if($action != "Update Statistics"){
	?>
	<html lang="<?php echo $LANG_TAG ?>">
		<head>
			<meta name="keywords" content="Natural history collections statistics" />
			<title><?php echo $DEFAULT_TITLE.' '.(isset($LANG['COL_STATS'])?$LANG['COL_STATS']:'Collection Statistics'); ?></title>
			<link href="<?php echo htmlspecialchars($CSS_BASE_PATH, HTML_SPECIAL_CHARS_FLAGS); ?>/jquery-ui.css" type="text/css" rel="stylesheet">
			<?php
			include_once($SERVER_ROOT.'/includes/head.php');
			?>
			<link href="<?php echo htmlspecialchars($CSS_BASE_PATH, HTML_SPECIAL_CHARS_FLAGS); ?>/symbiota/collections/listdisplay.css" type="text/css" rel="stylesheet" />
			<link href="<?php echo htmlspecialchars($CSS_BASE_PATH, HTML_SPECIAL_CHARS_FLAGS); ?>/symbiota/collections/sharedCollectionStyling.css" type="text/css" rel="stylesheet" />
            <script src="../../js/jquery.js" type="text/javascript"></script>
			<script src="../../js/jquery-ui.js" type="text/javascript"></script>
			<script src="../../js/symb/collections.index.js" type="text/javascript"></script>
			<script type="text/javascript">
				$(document).ready(function() {
					if(!navigator.cookieEnabled){
						alert("<?php echo (isset($LANG['NEED_COOKIES'])?$LANG['NEED_COOKIES']:'Your browser cookies are disabled. To be able to login and access your profile, they must be enabled for this domain.'); ?>");
					}
					$("#tabs").tabs({<?php echo ($action == "Run Statistics"?'active: 1':''); ?>});

                    function split( val ) {
                        return val.split( /,\s*/ );
                    }
                    function extractLast( term ) {
                        return split( term ).pop();
                    }

                    $( "#taxon" )
                    // don't navigate away from the field on tab when selecting an item
                        .bind( "keydown", function( event ) {
                            if ( event.keyCode === $.ui.keyCode.TAB &&
                                $( this ).data( "autocomplete" ).menu.active ) {
                                event.preventDefault();
                            }
                        })
                        .autocomplete({
                            source: function( request, response ) {
                                $.getJSON( "rpc/speciessuggest.php", {
                                    term: extractLast( request.term )
                                }, response );
                            },
                            search: function() {
                                // custom minLength
                                var term = extractLast( this.value );
                                if ( term.length < 4 ) {
                                    return false;
                                }
                            },
                            focus: function() {
                                // prevent value inserted on focus
                                return false;
                            },
                            select: function( event, ui ) {
                                var terms = split( this.value );
                                // remove the current input
                                terms.pop();
                                // add the selected item
                                terms.push( ui.item.value );
                                this.value = terms.join( ", " );
                                return false;
                            }
                        },{});
				});

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
						if(obj.style.display=="none" || obj.style.display==""){
							obj.style.display="block";
						}
						else {
							obj.style.display="none";
						}
					}
					return false;
				}

				function changeCollForm(f){
					var dbElements = document.getElementsByName("db[]");
					var c = false;
					var collid = "";
					for(i = 0; i < dbElements.length; i++){
						var dbElement = dbElements[i];
						if(dbElement.checked && !isNaN(dbElement.value)){
							if(c == true) collid = collid+",";
							collid = collid + dbElement.value;
							c = true;
						}
					}
					if(c == true){
						var collobj = document.getElementById("colltxt");
						collobj.value = collid;
						document.getElementById("collform").submit();
					}
					else{
						alert("<?php echo (isset($LANG['CHOOSE_ONE'])?$LANG['CHOOSE_ONE']:'Please choose at least one collection!'); ?>");
						return false;
					}
				}
			</script>
		</head>
		<body>
			<?php
			$displayLeftMenu = (isset($collections_misc_collstatsMenu)?$collections_misc_collstatsMenu:false);
			include($SERVER_ROOT.'/includes/header.php');
			if(isset($collections_misc_collstatsCrumbs)){
				if($collections_misc_collstatsCrumbs){
					echo "<div class='navpath'>";
					echo "<a href='../../index.php'>Home</a> &gt;&gt; ";
					echo $collections_misc_collstatsCrumbs.' &gt;&gt; ';
					echo "<b>".(isset($LANG['COL_STATS'])?$LANG['COL_STATS']:'Collection Statistics')."</b>";
					echo "</div>";
				}
			}
			else{
				?>
				<div class='navpath'>
					<a href='../../index.php'><?php echo htmlspecialchars((isset($LANG['HOME'])?$LANG['HOME']:'Home'), HTML_SPECIAL_CHARS_FLAGS); ?></a> &gt;&gt;
					<a href='collprofiles.php'><?php echo htmlspecialchars((isset($LANG['COLLECTIONS'])?$LANG['COLLECTIONS']:'Collections'), HTML_SPECIAL_CHARS_FLAGS); ?></a> &gt;&gt;
					<b><?php echo (isset($LANG['COL_STATS'])?$LANG['COL_STATS']:'Collection Statistics'); ?></b>
				</div>
				<?php
			}
			?>
			<!-- This is inner text! -->
			<div id="innertext">
				<h1><?php echo (isset($LANG['SELECT_COLS'])?$LANG['SELECT_COLS']:'Select Collections to be Analyzed'); ?></h1>
				<div id="tabs" class="tabby">
					<ul class="full-tab">
						<li><a href="#specobsdiv"><?php echo htmlspecialchars((isset($LANG['COLLECTIONS'])?$LANG['COLLECTIONS']:'Collections'), HTML_SPECIAL_CHARS_FLAGS); ?></a></li>
						<?php
                        if($action == "Run Statistics"){
							echo '<li><a href="#statsdiv">' . htmlspecialchars((isset($LANG['STATISTICS'])?$LANG['STATISTICS']:'Statistics'), HTML_SPECIAL_CHARS_FLAGS) . '</a></li>';
						}
						?>
					</ul>

					<div id="specobsdiv" class="pin-things-here">
						<?php
						if($specArr || $obsArr){
							?>
							<form name="collform" id="collform" action="collstats.php" method="post" onsubmit="return changeCollForm(this);">
								<div>
									<?php
									if($SYMB_UID && ($IS_ADMIN || array_key_exists("CollAdmin",$USER_RIGHTS))){
										?>
										<fieldset class="fieldset-padding flex-form">
											<legend><b><?php echo (isset($LANG['REC_CRITERIA'])?$LANG['REC_CRITERIA']:'Record Criteria'); ?></b></legend>
											<div class="record-criteria-inputs">
												<label for="taxon"><?php echo (isset($LANG['PARENT_CRITERIA'])?$LANG['PARENT_CRITERIA']:'Parent Taxon'); ?>: </label>
												<input type="text" id="taxon" name="taxon" size="43" value="<?php echo $cPartentTaxon; ?>" />
											</div>
											<div class="record-criteria-inputs">
												<label for="country"><?php echo (isset($LANG['COUNTRY'])?$LANG['COUNTRY']:'Country'); ?>: </label>
												<input type="text" id="country" name="country" size="43" value="<?php echo $cCountry; ?>" />
											</div>
										</fieldset>
										<?php
									}
									?>
									<div class="specimen-header-margin">
										<hr/>
										<h2><?php echo $LANG['SPECIMEN_COLLECTIONS'] ?></h2>
									</div>
									<div class="special-indent">
										<input id="dballcb" name="db[]" class="specobs" value='all' type="checkbox" onclick="selectAll(this);" />
										<label for="dballcb"><?php echo (isset($LANG['SEL_OR_DESEL'])?$LANG['SEL_OR_DESEL']:'Select/Deselect All'); ?> <a href="collprofiles.php"><?php echo htmlspecialchars((isset($LANG['COLLECTIONS'])?$LANG['COLLECTIONS']:'Collections'), HTML_SPECIAL_CHARS_FLAGS); ?></a></label>
									</div>
									<?php
									$collArrIndex = 0;
									if($specArr){
										$collCnt = 0;
										if(isset($specArr['cat'])){
											$categoryArr = $specArr['cat'];
											?>
											<div class="sticky-buttons" id="statistics-button-panel">
												<div>
													<button type="submit" name="submitaction" value="Run Statistics"><?php echo (isset($LANG['VIEW_STATS'])?$LANG['VIEW_STATS']:'View Statistics'); ?></button>
												</div>
												<?php
												if($SYMB_UID && $IS_ADMIN){
													?>
													<div class="submit-button-div">
														<button type="submit" name="submitaction" value="Update Statistics"><?php echo (isset($LANG['UPDATE_STATS'])?$LANG['UPDATE_STATS']:'Update Statistics'); ?></button>
													</div>
													<?php
												}
												?>
											</div>
											<section class="gridlike-form">
												<?php
												$cnt = 0;
												foreach($categoryArr as $catid => $catArr){
													$name = $catArr['name'];
													if($catArr['acronym']) $name .= ' ('.$catArr['acronym'].')';
													$catIcon = $catArr['icon'];
													unset($catArr['name']);
													unset($catArr['acronym']);
													unset($catArr['icon']);
													$idStr = $collArrIndex.'-'.$catid;
													?>
													<section class="gridlike-form-row">
														<div>
															<input id="cat-<?php echo $idStr; ?>-Input" name="cat[]" value="<?php echo $catid; ?>" type="checkbox" onclick="selectAllCat(this,'cat-<?php echo $idStr; ?>')" <?php echo ($collIdArr&&($collIdArr==array_keys($catArr))?'checked':''); ?> />
															<label for="cat-<?php echo $idStr; ?>-Input">
																<?php echo $name; ?> (<?php echo isset($LANG['SPECIMEN']) ? $LANG['SPECIMEN'] : "Specimen" ?>) <?php echo $LANG['SELECT_DESELECT'] ?>
															</label>
														</div>
														<div>
															<a href="#" onclick="toggleCat('<?php echo $idStr; ?>');return false;" class="condense-expand-flex" >
																<div class="condense-expand-button-set">
																	<img id="plus-<?php echo $idStr; ?>" src="../../images/plus.png" alt="plus sign to expand menu" style="<?php echo (($DEFAULTCATID && $DEFAULTCATID != $catid)?'':'display:none;') ?> width:1em;" />
																	<img id="minus-<?php echo $idStr; ?>" src="../../images/minus.png" alt="minus sign to condense menu" style="<?php echo (($DEFAULTCATID && $DEFAULTCATID != $catid)?'display:none;':'') ?> width:1em;" />
																	<p id="ptext-<?php echo $idStr; ?>" style="<?php echo (($DEFAULTCATID && $DEFAULTCATID != $catid)?'':'display:none;') ?>">
																		<?php echo $LANG['EXPAND'] ?>
																	</p>
																	<p id="mtext-<?php echo $idStr; ?>" style="<?php echo (($DEFAULTCATID && $DEFAULTCATID != $catid)?'display:none;':'') ?>" >
																		<?php echo $LANG['CONDENSE'] ?>
																	</p>
																</div>
															</a>
														</div>
													</section>
													<section class="gridlike-form-row bottom-breathing-room-relative">
														<div>
															<div id="cat-<?php echo $idStr; ?>" style="<?php echo (($DEFAULTCATID && $DEFAULTCATID != $catid)?'display:none;':'') ?>">
																<section class="gridlike-form">
																	<fieldset class="fieldset-padding-rem">
																		<legend>
																			<?php echo $name; ?> (<?php echo isset($LANG['SPECIMEN']) ? $LANG['SPECIMEN'] : "Specimen" ?>)
																		</legend>
																	<?php
																	foreach($catArr as $collid => $collName2){
																		?>
																		<div class="gridlike-form-row bottom-breathing-room-relative">
																				<input id="db-<?php echo $collid?>" name="db[]" value="<?php echo $collid; ?>" type="checkbox" class="cat-<?php echo $idStr; ?>" onclick="unselectCat('cat-<?php echo $idStr; ?>-Input')" <?php echo ($collIdArr&&in_array($collid,$collIdArr)?'checked':''); ?> />
																				<label for="db-<?php echo $collid?>">
																					<a href='collprofiles.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>'>
																						<?php
																						$codeStr = ' ('.$collName2['instcode'];
																						if($collName2['collcode']) $codeStr .= '-'.$collName2['collcode'];
																						$codeStr .= ')';
																						echo $collName2["collname"].$codeStr;
																						?>
																						 - <?php echo (isset($LANG['MORE_INFO'])?$LANG['MORE_INFO']:'more info'); ?>
																					</a>
																				</label>
																			</div>
																			<?php
																		$collCnt++;
																	}
																	?>
																	</fieldset>
																</section>
															</div>
														</div>
													</section>
													<?php
													$cnt++;
												}
												?>
											</section>
											<?php
										}
										if(isset($specArr['coll'])){
											$collArr = $specArr['coll'];
											?>
											<section class="gridlike-form-row">
												<?php
												foreach($collArr as $collid => $cArr){
													?>
													<div class="gridlike-form-row bottom-breathing-room-relative">
														<div>
															<input id="current-collid" name="current-collid" value="<?php echo $collid; ?>" type="checkbox" onclick="uncheckAll();" <?php echo ($collIdArr&&in_array($collid,$collIdArr)?'checked':''); ?> />
															<label for="current-collid">Collection ID TODO</label>
														</div>
														<div>
															<a href='collprofiles.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>'>
																<?php
																$codeStr = ' ('.$cArr['instcode'];
																if($cArr['collcode']) $codeStr .= '-'.$cArr['collcode'];
																$codeStr .= ')';
																echo $cArr["collname"].$codeStr;
																?>
																 - <?php echo (isset($LANG['MORE_INFO'])?$LANG['MORE_INFO']:'more info'); ?>
															</a>
														</div>
													</div>
													<?php
													$collCnt++;
												}
												?>
											</section>
											<div>
												<div>
													<button type="submit" name="submitaction" value="Run Statistics"><?php echo (isset($LANG['VIEW_STATS'])?$LANG['VIEW_STATS']:'Run Observation Statistics'); ?></button>
												</div>
												<?php
												if($SYMB_UID && $IS_ADMIN){
													?>
													<div class="submit-button-div">
														<button type="submit" name="submitaction" value="Update Statistics"><?php echo (isset($LANG['UPDATE_OBSERVATION_STATS'])?$LANG['UPDATE_OBSERVATION_STATS']:'Update Observation Statistics'); ?></button>
													</div>
													<?php
												}
												?>
											</div>
											<?php
										}
										$collArrIndex++;
									}
									if($specArr && $obsArr) echo "<hr style=\"clear:both;margin:20px 0px;\"/><h2>{$LANG['OBSERVATION_COLLECTIONS']}</h2>";
									if($obsArr){
										$collCnt = 0;
										if(isset($obsArr['cat'])){
											$categoryArr = $obsArr['cat'];
											?>
											<section class="gridlike-form">
												<?php
												$cnt = 0;
												foreach($categoryArr as $catid => $catArr){
													$name = $catArr['name'];
													if($catArr['acronym']) $name .= ' ('.$catArr['acronym'].')';
													$catIcon = $catArr['icon'];
													unset($catArr['name']);
													unset($catArr['acronym']);
													unset($catArr['icon']);
													$idStr = $collArrIndex.'-'.$catid;
													?>
													<div class="gridlike-form-row bottom-breathing-room-relative">
														<div>
															<input id="cat-<?php echo $idStr; ?>-Input" name="cat[]" value="<?php echo $catid; ?>" type="checkbox" onclick="selectAllCat(this,'cat-<?php echo $idStr; ?>')" <?php echo ($collIdArr&&($collIdArr==array_keys($catArr))?'checked':''); ?> />
															<label for="cat-<?php echo $idStr; ?>-Input">
																<?php echo $name; ?> (<?php echo isset($LANG['OBSERVATION']) ? $LANG['OBSERVATION'] : "Observation" ?>)  <?php echo $LANG['SELECT_DESELECT'] ?>
															</label>
														</div>
														<div>
															<a href="#" onclick="toggleCat('<?php echo $idStr; ?>');return false;" class="condense-expand-flex">
																<div class="condense-expand-button-set">
																	<img id="plus-<?php echo $idStr; ?>" alt="plus sign to expand menu" src="../../images/plus.png" style="<?php echo ('width:1em;' . ($DEFAULTCATID && $DEFAULTCATID != $catid)?'':'display:none;') ?>" />
																	<img id="minus-<?php echo $idStr; ?>" alt="minus sign to condense menu" src="../../images/minus.png" style="<?php echo 'width:1em;' . (($DEFAULTCATID && $DEFAULTCATID != $catid)?'display:none;':'') ?>" />
																	<p id="ptext-<?php echo $idStr; ?>" style="<?php echo (($DEFAULTCATID && $DEFAULTCATID != $catid)?'':'display:none;') ?>">
																		<?php echo $LANG['EXPAND'] ?>
																	</p>
																	<p id="mtext-<?php echo $idStr; ?>" style="<?php echo (($DEFAULTCATID && $DEFAULTCATID != $catid)?'display:none;':'') ?>" >
																		<?php echo $LANG['CONDENSE'] ?>
																	</p>
																</div>
															</a>
														</div>
													</div>
													<div>
														<div class="gridlike-form-row bottom-breathing-room-relative" id="cat-<?php echo $idStr; ?>" style="<?php echo (($DEFAULTCATID && $DEFAULTCATID != $catid)?'display:none;':'') ?> margin-left:2rem;">
															<section class="gridlike-form">
																<fieldset class="fieldset-observation-padding-rem">
																	<legend>
																		<?php echo $name; ?> (<?php echo isset($LANG['OBSERVATION']) ? $LANG['OBSERVATION'] : "Observation" ?>)
																	</legend>
																	<?php
																		foreach($catArr as $collid => $collName2){
																			?>
																				<div class="gridlike-form-row bottom-breathing-room-relative">
																					<div>
																						<input class="input-margin cat-<?php echo $idStr; ?>" id="db-<?php echo $collid ?>" name="db[]" value="<?php echo $collid; ?>" type="checkbox" onclick="unselectCat('cat-<?php echo $idStr; ?>-Input')" <?php echo ($collIdArr&&in_array($collid,$collIdArr)?'checked':''); ?> />
																						<label for="db-<?php echo $collid ?>">
																							<a class="rel-fontsize" href = 'collprofiles.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>'>
																								<?php
																								$codeStr = ' ('.$collName2['instcode'];
																								if($collName2['collcode']) $codeStr .= '-'.$collName2['collcode'];
																								$codeStr .= ')';
																								echo $collName2["collname"].$codeStr;
																								?>
																								 - <?php echo (isset($LANG['MORE_INFO'])?$LANG['MORE_INFO']:'more info'); ?>
																							</a>
																						</label>
																					</div>
																				</div>
																			<?php
																			$collCnt++;
																		}
																	?>
																</fieldset>
															</section>
														</div>
													<?php
													$cnt++;
													}
													?>
													</div>
											</section>
											<?php
										}
										if(isset($obsArr['coll'])){
											$collArr = $obsArr['coll'];
											?>
											<section class="gridlike-form-row">
												<fieldset class="observation-fieldset">
													<?php
													foreach($collArr as $collid => $cArr){
														?>
															<div>
																<input id="db-<?php echo $collid ?>" name="db[]" value="<?php echo $collid; ?>" type="checkbox" onclick="uncheckAll();" <?php echo ($collIdArr&&in_array($collid,$collIdArr)?'checked':''); ?> />
																<label for="db-<?php echo $collid ?>"><?php echo $LANG['SELECT_DESELECT'] ?></label>
															</div>
															<div class="gridlike-form-row bottom-breathing-room-relative">
															<div class="collectiontitle">
																<a href = 'collprofiles.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>'>
																	<?php
																	$codeStr = ' ('.$cArr['instcode'];
																	if($cArr['collcode']) $codeStr .= '-'.$cArr['collcode'];
																	$codeStr .= ')';
																	echo $cArr["collname"].$codeStr;
																	?>
																	 - <?php echo (isset($LANG['MORE_INFO'])?$LANG['MORE_INFO']:'more info'); ?>
																</a>
															</div>
														</div>
														<?php
														$collCnt++;
													}
													?>
												</fieldset>
											</section>
											<div class="run-stat-btn">
												<div>
													<button type="submit" name="submitaction" value="Run Statistics"><?php echo (isset($LANG['VIEW_STATS'])?$LANG['VIEW_STATS']:'View Statistics'); ?></button>
												</div>
												<?php
												if($SYMB_UID && $IS_ADMIN){
													?>
													<div class="submit-button-div">
														<button type="submit" name="submitaction" value="Update Statistics" /><?php echo (isset($LANG['UPDATE_STATS'])?$LANG['UPDATE_STATS']:'Update Statistics'); ?></button>
													</div>
													<?php
												}
												?>
											</div>
											<?php
										}
										$collArrIndex++;
									}
									?>
									<div class="clr">&nbsp;</div>
									<input type="hidden" name="collid" id="colltxt" value="" />
									<input type="hidden" name="days" value="<?php echo $days; ?>" />
									<input type="hidden" name="months" value="<?php echo $months; ?>" />
								</div>
                            </form>
                            <?php
                        }
						else{
							echo '<div class="top-marg"><div class="heavy-txt">'.(isset($LANG['NO_COLLECTIONS'])?$LANG['NO_COLLECTIONS']:'There are currently no collections to analyze.').'</div></div>';
						}
						?>
					</div>

                    <?php
					if($action == "Run Statistics"){
						?>
						<div id="statsdiv">
							<div class="mn-ht">
								<div>
									<h1><?php echo (isset($LANG['SEL_COL_STATS'])?$LANG['SEL_COL_STATS']:'Selected Collection Statistics'); ?></h1>
									<div class="big-fnt-margin">
										<div id="colllistlabel"><a href="#" onclick="toggle('colllist');toggle('colllistlabel');"><?php echo (isset($LANG['DISPLAY_LIST'])?$LANG['DISPLAY_LIST']:'Display List of Collections Analyzed'); ?></a></div>
										<div id="colllist" class="dsply-none">
											<?php echo $collStr; ?>
										</div>
									</div>
									<fieldset class="stats-display-fieldset">
										<ul class="stats-display-ul">
											<?php
											echo "<li>";
											echo ($results['SpecimenCount']?number_format($results['SpecimenCount']):0)." ".(isset($LANG['OCC_RECORDS'])?$LANG['OCC_RECORDS']:'occurrence records');
											echo "</li>";
											echo "<li>";
											$percGeo = '';
											if($results['SpecimenCount'] && $results['GeorefCount']){
												$percGeo = (100* ($results['GeorefCount'] / $results['SpecimenCount']));
											}
											echo ($results['GeorefCount']?number_format($results['GeorefCount']):0).($percGeo?" (".($percGeo>1?round($percGeo):round($percGeo,2))."%)":'')." ".(isset($LANG['GEOREFERENCED'])?$LANG['GEOREFERENCED']:'georeferenced');
											echo "</li>";
											echo "<li>";
											$percImg = '';
											if($results['SpecimenCount'] && $results['TotalImageCount']){
												$percImg = (100* ($results['TotalImageCount'] / $results['SpecimenCount']));
											}
											echo ($results['TotalImageCount']?number_format($results['TotalImageCount']):0).($percImg?" (".($percImg>1?round($percImg):round($percImg,2))."%)":'')." ".(isset($LANG['OCCS_IMAGED'])?$LANG['OCCS_IMAGED']:'occurrences imaged');
											echo "</li>";
											echo "<li>";
											$percId = '';
											if($results['SpecimenCount'] && $results['SpecimensCountID']){
												$percId = (100* ($results['SpecimensCountID'] / $results['SpecimenCount']));
											}
											echo ($results['SpecimensCountID']?number_format($results['SpecimensCountID']):0).($percId?" (".($percId>1?round($percId):round($percId,2))."%)":'')." ".(isset($LANG['IDED_TO_SP'])?$LANG['IDED_TO_SP']:'identified to species');
											echo "</li>";
											echo "<li>";
											echo ($results['FamilyCount']?number_format($results['FamilyCount']):0)." ".(isset($LANG['FAMILIES'])?$LANG['FAMILIES']:'families');
											echo "</li>";
											echo "<li>";
											echo ($results['GeneraCount']?number_format($results['GeneraCount']):0)." ".(isset($LANG['GENERA'])?$LANG['GENERA']:'genera');
											echo "</li>";
											echo "<li>";
											echo ($results['SpeciesCount']?number_format($results['SpeciesCount']):0)." ".(isset($LANG['SPECIES'])?$LANG['SPECIES']:'species');
											echo "</li>";
											echo "<li>";
											echo ($results['TotalTaxaCount']?number_format($results['TotalTaxaCount']):0)." ".(isset($LANG['TOTAL_TAXA'])?$LANG['TOTAL_TAXA']:'total taxa (including subsp. and var.)');
											echo "</li>";
											/*echo "<li>";
											echo ($results['TypeCount']?number_format($results['TypeCount']):0)." type specimens";
											echo "</li>";*/
											?>
										</ul>
										<form name="statscsv" id="statscsv" action="collstatscsv.php" method="post" onsubmit="">
											<div class="stat-csv-margin">
												<div id="showstatspercoll" class="float-and-block" >
													<a href="#" onclick="return toggleStatsPerColl()"><?php echo (isset($LANG['SHOW_PER_COL'])?$LANG['SHOW_PER_COL']:'Show Statistics per Collection'); ?></a>
												</div>
												<div id="hidestatspercoll" class="float-and-no-display" >
													<a href="#" onclick="return toggleStatsPerColl()"><?php echo (isset($LANG['HIDE_STATS'])?$LANG['HIDE_STATS']:'Hide Statistics per Collection'); ?></a>
												</div>
												<div class="stat-csv-float-margins" title="<?php echo (isset($LANG['SAVE_CSV'])?$LANG['SAVE_CSV']:'Save CSV'); ?>">
													<input type="hidden" name="collids" id="collids" value='<?php echo $collId; ?>' />
                                                    <input type="hidden" name="taxon" value='<?php echo $cPartentTaxon; ?>' />
                                                    <input type="hidden" name="country" value='<?php echo $cCountry; ?>' />
													<input type="hidden" name="action" id="action" value='<?php echo (isset($LANG['DOWNLOAD_STATS'])?$LANG['DOWNLOAD_STATS']:'Download Stats per Coll'); ?>' />
													<input type="image" name="action" src="../../images/dl.png" style="width:1.3em" onclick="" />
													<!--input type="submit" name="action" value="Download Stats per Coll" src="../../images/dl.png" / -->
												</div>
											</div>
										</form>
									</fieldset>
									<div>
										<fieldset class="special-indent extra-stats">
											<form name="famstatscsv" id="famstatscsv" action="collstatscsv.php" method="post" onsubmit="">
												<div class='legend'><b><?php echo (isset($LANG['EXTRA_STATS'])?$LANG['EXTRA_STATS']:'Extra Statistics'); ?></b></div>
												<div class="stat-csv-margin">
													<div id="showfamdist" class="float-and-block" >
														<a href="#" onclick="return toggleFamilyDist()"><?php echo (isset($LANG['SHOW_FAMILY'])?$LANG['SHOW_FAMILY']:'Show Family Distribution'); ?></a>
													</div>
													<div id="hidefamdist" class="float-and-no-display" >
														<a href="#" onclick="return toggleFamilyDist()"><?php echo (isset($LANG['HIDE_FAMILY'])?$LANG['HIDE_FAMILY']:'Hide Family Distribution'); ?></a>
													</div>
													<div class="stat-csv-float-margins" title="<?php echo (isset($LANG['SAVE_CSV'])?$LANG['SAVE_CSV']:'Save CSV'); ?>">
														<input type="hidden" name="action" value='Download Family Dist'/>
														<input type="image" name="action" src="../../images/dl.png" style="width:1.3em" onclick="" />
													</div>
												</div>
											</form>
											<form name="geostatscsv" id="geostatscsv" action="collstatscsv.php" method="post" onsubmit="">
												<div class="clr">
													<div id="showgeodist" class="float-and-block" >
														<a href="#" onclick="return toggleGeoDist()"><?php echo (isset($LANG['SHOW_GEO'])?$LANG['SHOW_GEO']:'Show Geographic Distribution'); ?></a>
													</div>
													<div id="hidegeodist" class="float-and-no-display">
														<a href="#" onclick="return toggleGeoDist();"><?php echo (isset($LANG['HIDE_GEO'])?$LANG['HIDE_GEO']:'Hide Geographic Distribution'); ?></a>
													</div>
													<div class="stat-csv-float-margins" title="<?php echo (isset($LANG['SAVE_CSV'])?$LANG['SAVE_CSV']:'Save CSV'); ?>">
														<input type="hidden" name="action" value='Download Geo Dist' />
														<input type="image" name="action" src="../../images/dl.png" style="width:1.3em" onclick="" />
													</div>
												</div>
											</form>
                                            <?php
                                            if(!$cPartentTaxon && !$cCountry){
                                                ?>
                                                <div class="mrgn-tp">
                                                    <form name="orderstats" class="no-btm-mrgn" action="collorderstats.php" method="post" target="_blank">
                                                        <input type="hidden" name="collid" id="collid" value='<?php echo $collId; ?>'/>
                                                        <input type="hidden" name="totalcnt" id="totalcnt" value='<?php echo $results['SpecimenCount']; ?>'/>
                                                        <button type="submit" name="action" value="Load Order Distribution"><?php echo $LANG['LOAD_ORDER']; ?></button>
                                                    </form>
                                                </div>
                                                <?php
                                            }
                                            ?>
										</fieldset>
										<?php
										if(!$cPartentTaxon && !$cCountry){
                                            if ($SYMB_UID && ($IS_ADMIN || array_key_exists("CollAdmin", $USER_RIGHTS))) {
                                                ?>
                                                <fieldset id="yearstatsbox" class="yearstatbox-width">
                                                    <legend><b><?php echo (isset($LANG['YEAR_STATS'])?$LANG['YEAR_STATS']:'Year Stats'); ?></b></legend>
                                                    <form name="yearstats" class="no-btm-mrgn" action="collyearstats.php" method="post" target="_blank" class="flex-form">
                                                        <input type="hidden" name="collid" id="collid" value='<?php echo $collId; ?>'/>
                                                        <input type="hidden" name="days" value="<?php echo $days; ?>"/>
                                                        <input type="hidden" name="months" value="<?php echo $months; ?>"/>
                                                        <div class="yearstatbox-left-float">
                                                            <?php echo (isset($LANG['YEARS'])?$LANG['YEARS']:'Years'); ?>: <input type="text" id="years" size="5" name="years" value="1" />
                                                        </div>
                                                        <div class="yearstatbox-submit-btn-margin">
                                                            <button type="submit" name="action" value="Load Stats"><?php echo (isset($LANG['LOAD_STATS'])?$LANG['LOAD_STATS']:'Load Stats'); ?></button>
                                                        </div>
                                                    </form>
                                                </fieldset>
                                                <?php
                                            }
                                        }
                                        ?>
									</div>
									<div class="clr"> </div>
								</div>

								<fieldset id="statspercollbox" class="statspercollbox">
									<legend><b><?php echo (isset($LANG['STATS_PER_COL'])?$LANG['STATS_PER_COL']:'Statistics per Collection'); ?></b></legend>
									<section class="gridlike-form">
										<section class="gridlike-form-row">
											<div class="cntr-text"><?php echo (isset($LANG['COLLECTION'])?$LANG['COLLECTION']:'Collection'); ?></div>
											<div class="cntr-text"><?php echo (isset($LANG['OCCS'])?$LANG['OCCS']:'Occurrences'); ?></div>
											<div class="cntr-text"><?php echo (isset($LANG['G_GEOREFERENCED'])?$LANG['G_GEOREFERENCED']:'Georeferenced'); ?></div>
											<div class="cntr-text"><?php echo (isset($LANG['IMAGED'])?$LANG['IMAGED']:'Imaged'); ?></div>
											<div class="cntr-text"><?php echo (isset($LANG['SPECIES_ID'])?$LANG['SPECIES_ID']:'Species ID'); ?></div>
											<div class="cntr-text"><?php echo (isset($LANG['F_FAMILIES'])?$LANG['F_FAMILIES']:'Families'); ?></div>
											<div class="cntr-text"><?php echo (isset($LANG['G_GENERA'])?$LANG['G_GENERA']:'Genera'); ?></div>
											<div class="cntr-text"><?php echo (isset($LANG['S_SPECIES'])?$LANG['S_SPECIES']:'Species'); ?></div>
											<div class="cntr-text"><?php echo (isset($LANG['T_TOTAL_TAXA'])?$LANG['T_TOTAL_TAXA']:'Total Taxa'); ?></div>
											<!-- <th class="cntr-text">Types</th> -->
										</section>
										<?php
										foreach($resultsTemp as $name => $data){
											echo '<section class="gridlike-form-row">';
											echo '<div>'.wordwrap($name,40,"<br />\n",true).'</div>';
											echo '<div>'.(array_key_exists('recordcnt',$data)?$data['recordcnt']:0).'</div>';
											echo '<div>'.(array_key_exists('georefcnt',$data)?$data['georefcnt']:0).'</div>';
											echo '<div>'.(array_key_exists('OccurrenceImageCount',$data)?$data['OccurrenceImageCount']:0).'</div>';
											echo '<div>'.(array_key_exists('speciesID',$data)?$data['speciesID']:0).'</div>';
											echo '<div>'.(array_key_exists('familycnt',$data)?$data['familycnt']:0).'</div>';
											echo '<div>'.(array_key_exists('genuscnt',$data)?$data['genuscnt']:0).'</div>';
											echo '<div>'.(array_key_exists('speciescnt',$data)?$data['speciescnt']:0).'</div>';
											echo '<div>'.(array_key_exists('TotalTaxaCount',$data)?$data['TotalTaxaCount']:0).'</div>';
											//echo '<td>'.(array_key_exists('types',$data)?$data['types']:0).'</td>';
											echo '</section>';
										}
										?>
									</section>
								</fieldset>
								<fieldset id="famdistbox" class="famdistbox">
									<legend><b><?php echo (isset($LANG['FAM_DIST'])?$LANG['FAM_DIST']:'Family Distribution'); ?></b></legend>
									<section class="gridlike-form">
										<section class="gridlike-form-row">
											<div class="cntr-text">
											<?php echo (isset($LANG['FAMILY'])?$LANG['FAMILY']:'Family'); ?>
										</div>
											<div class="cntr-text">
											<?php echo (isset($LANG['SPECIMENS'])?$LANG['SPECIMENS']:'Specimens'); ?>
										</div>
											<div class="cntr-text">
											<?php echo (isset($LANG['G_GEOREFERENCED'])?$LANG['G_GEOREFERENCED']:'Georeferenced'); ?>
										</div>
											<div class="cntr-text">
											<?php echo (isset($LANG['SPECIES_ID'])?$LANG['SPECIES_ID']:'Species ID'); ?>
										</div>
											<div class="cntr-text">
												<?php echo (isset($LANG['G_GEOREFERENCED'])?$LANG['G_GEOREFERENCED']:'Georeferenced'); ?>
												<br />
												<?php echo (isset($LANG['AND'])?$LANG['AND']:'and'); ?>
												<br />
												<?php echo (isset($LANG['SPECIES_ID'])?$LANG['SPECIES_ID']:'Species ID'); ?>
											</div>
										</section>
										<?php
										$total = 0;
										foreach($familyArr as $name => $data){
											echo '<section class="gridlike-form-row">';
											echo '<div>'.wordwrap($name,52,"<br />\n",true).'</div>';
											echo '<div>';
											if(count($resultsTemp) == 1){
												echo '<a href="../list.php?db[]=' . htmlspecialchars($collId, HTML_SPECIAL_CHARS_FLAGS) . '&reset=1&taxa=' . htmlspecialchars($name, HTML_SPECIAL_CHARS_FLAGS) . '" target="_blank">';
											}
											echo number_format($data['SpecimensPerFamily']);
											if(count($resultsTemp) == 1){
												echo '</a>';
											}
											echo '</div>';
											echo '<div>'.($data['GeorefSpecimensPerFamily']?round(100*($data['GeorefSpecimensPerFamily']/$data['SpecimensPerFamily'])):0).'%</div>';
											echo '<div>'.($data['IDSpecimensPerFamily']?round(100*($data['IDSpecimensPerFamily']/$data['SpecimensPerFamily'])):0).'%</div>';
											echo '<div>'.($data['IDGeorefSpecimensPerFamily']?round(100*($data['IDGeorefSpecimensPerFamily']/$data['SpecimensPerFamily'])):0).'%</div>';
											echo '</section>';
											$total = $total + $data['SpecimensPerFamily'];
										}
										?>
									</section>
									<div class="top-marg">
										<b><?php echo (isset($LANG['SPEC_W_FAMILY'])?$LANG['SPEC_W_FAMILY']:'Total Specimens with Family'); ?>:</b> <?php echo number_format($total); ?><br />
										<?php echo (isset($LANG['SPEC_WO_FAMILY'])?$LANG['SPEC_WO_FAMILY']:'Specimens without Family'); ?>: <?php echo number_format($results['SpecimenCount']-$total); ?><br />
									</div>
								</fieldset>
								<fieldset id="geodistbox" class="geodistbox">
									<legend><b><?php echo (isset($LANG['GEO_DIST'])?$LANG['GEO_DIST']:'Geographic Distribution'); ?></b></legend>
									<section class="gridlike-form">
										<section class="gridlike-form-row">
											<th class="cntr-text"><?php echo (isset($LANG['COUNTRY'])?$LANG['COUNTRY']:'Country'); ?></th>
											<th class="cntr-text"><?php echo (isset($LANG['SPECIMENS'])?$LANG['SPECIMENS']:'Specimens'); ?></th>
											<th class="cntr-text"><?php echo (isset($LANG['G_GEOREFERENCED'])?$LANG['G_GEOREFERENCED']:'Georeferenced'); ?></th>
											<th class="cntr-text"><?php echo (isset($LANG['SPECIES_ID'])?$LANG['SPECIES_ID']:'Species ID'); ?></th>
											<th class="cntr-text"><?php echo (isset($LANG['G_GEOREFERENCED'])?$LANG['G_GEOREFERENCED']:'Georeferenced'); ?><br /><?php echo (isset($LANG['AND'])?$LANG['AND']:'and'); ?><br /><?php echo (isset($LANG['SPECIES_ID'])?$LANG['SPECIES_ID']:'Species ID'); ?></th>
										</section>
										<?php
										$total = 0;
										foreach($countryArr as $name => $data){
											echo '<section class="gridlike-form-row">';
											echo '<div>'.wordwrap($name,52,"<br />\n",true).'</div>';
											echo '<div>';
											if(count($resultsTemp) == 1){
												echo '<a href="../list.php?db[]=' . htmlspecialchars($collId, HTML_SPECIAL_CHARS_FLAGS) . '&reset=1&country=' . htmlspecialchars($name, HTML_SPECIAL_CHARS_FLAGS) . '" target="_blank">';
											}
											echo number_format($data['CountryCount']);
											if(count($resultsTemp) == 1){
												echo '</a>';
											}
											echo '</div>';
											echo '<div>'.($data['GeorefSpecimensPerCountry']?round(100*($data['GeorefSpecimensPerCountry']/$data['CountryCount'])):0).'%</div>';
											echo '<div>'.($data['IDSpecimensPerCountry']?round(100*($data['IDSpecimensPerCountry']/$data['CountryCount'])):0).'%</div>';
											echo '<div>'.($data['IDGeorefSpecimensPerCountry']?round(100*($data['IDGeorefSpecimensPerCountry']/$data['CountryCount'])):0).'%</div>';
											echo '</section>';
											$total = $total + $data['CountryCount'];
										}
										?>
									</section>
									<div class="top-marg">
										<b><?php echo (isset($LANG['SPEC_W_COUNTRY'])?$LANG['SPEC_W_COUNTRY']:'Total Specimens with Country'); ?>:</b> <?php echo number_format($total); ?><br />
										<?php echo (isset($LANG['SPEC_WO_COUNTRY'])?$LANG['SPEC_WO_COUNTRY']:'Specimens without Country or Georeferencing'); ?>: <?php echo number_format(($results['SpecimenCount']-$total)+$results['SpecimensNullLatitude']); ?><br />
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
	</html>
	<?php
}
?>
