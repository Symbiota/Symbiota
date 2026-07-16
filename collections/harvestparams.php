<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/content/lang/collections/harvestparams.'.$LANG_TAG.'.php');
include_once($SERVER_ROOT.'/classes/OccurrenceManager.php');
include_once($SERVER_ROOT.'/classes/OccurrenceAttributeSearch.php');
header('Content-Type: text/html; charset=' . $CHARSET);

$collManager = new OccurrenceManager();
$paleoTimes = $collManager->getPaleoTimes();
$searchVar = $collManager->getQueryTermStr();
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<title><?= $DEFAULT_TITLE.' '.$LANG['PAGE_TITLE']; ?></title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
    include_once($SERVER_ROOT.'/includes/googleanalytics.php');
    ?>
	<link href="<?= $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<script src="<?= $CLIENT_ROOT; ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT; ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/symb/searchform.js?ver=3" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT; ?>/js/symb/collections.list.js?ver=2" type="text/javascript"></script>
	<script src="../js/symb/collections.harvestparams.js?ver=5" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/symb/mapAidUtils.js?ver=1" type="text/javascript"></script>
	<script src="../js/symb/collections.traitsearch.js?ver=8" type="text/javascript"></script> <!-- Contains search-by-trait modifications -->
	<script src="../js/symb/wktpolygontools.js?ver=1c" type="text/javascript"></script>
	<script src="../js/symb/taxonomy.taxasuggest.js?ver=e1" type="text/javascript"></script>
	<script type="text/javascript">
		const paleoTimes = <?= json_encode($paleoTimes ?? []) ?>;
		$(document).ready(function() {
			setSessionQueryStr();
			setHarvestParamsForm(document.harvestparams);

			//Set and initiate Autcomplete
			setTaxaSuggestRootPath("<?= $CLIENT_ROOT ?>");
			setMultipleTermSupport(true);
			initiateTaxaSuggest("taxa", "tid", document.getElementById("taxontype").value);
		});
	</script>

	<style type="text/css">
		hr{ clear:both; margin: 10px 0px }
		button{ margin: 2px }
		select{ margin-bottom: 4px }
		.catHeaderDiv { font-weight:bold; font-size: 18px }
		.coordBoxDiv { float:left; border:2px solid brown; padding:10px; margin:5px; white-space: nowrap; }
		.coordBoxDiv .labelDiv { font-weight:bold;float:left }
		.coordBoxDiv .iconDiv { float: left; margin-left: 5px; }
		.coordBoxDiv .iconDiv img { width:18px; }
		.coordBoxDiv .elemDiv { clear:both; }
	</style>
</head>
<body>
<div id="service-container" data-search-var="<?= $searchVar; ?>"></div>
<div id="all_collections_parent_container" data-config='<?= json_encode([
	'CURRENT_URL' => $_SERVER['REQUEST_URI'],
]) ?>'></div>
<?php
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class='navpath'>
		<a href="../index.php"><?= $LANG['NAV_HOME'] ?></a> &gt;&gt;
		<a href="index.php"><?= $LANG['NAV_COLLECTIONS'] ?></a> &gt;&gt;
		<b><?= $LANG['NAV_SEARCH']; ?></b>
	</div>
	<div role="main" id="innertext">
		<h1 class="page-heading bottom-breathing-room-rel top-breathing-room-rel"><?= $LANG['SEARCH']; ?></h1>
		<form name="harvestparams" id="harvestparams" action="list.php" method="post" onsubmit="return checkHarvestParamsForm(this, '<?= $_SERVER['REQUEST_URI']; ?>');">
			<hr/>
			<div>
				<div>
					<div class="catHeaderDiv"><?= $LANG['TAXON_HEADER']; ?></div>
				</div>
				<section class="flex-form" style="justify-content: space-between">
					<div>
						<div>
							<input type='checkbox' name='usethes' id='usethes' value='1' CHECKED />
							<label for="usethes" style="margin:10px 0px 0px 5px;"><?= $LANG['INCLUDE_SYNONYMS']; ?></label>
						</div>
						<div>
							<label for="taxontype"><?= $LANG['SELECT_TAXON_TYPE'] ?>:</label>
							<select id="taxontype" name="taxontype">
								<?php
								$taxonType = 1;
								if(isset($DEFAULT_TAXON_SEARCH) && $DEFAULT_TAXON_SEARCH) $taxonType = $DEFAULT_TAXON_SEARCH;
								$taxonTypeRange = 6;
								if(isset($DISPLAY_COMMON_NAMES) && !$DISPLAY_COMMON_NAMES) $taxonTypeRange = 5;
								for($h=1;$h<$taxonTypeRange;$h++){
									echo '<option value="'.$h.'" '.($taxonType==$h?'SELECTED':'').'>'.$LANG['SELECT_1-'.$h].'</option>';
								}
								?>
							</select>
						</div>
						<div>
							<label for="taxa"><?= $LANG['TYPE_TAXON'] ?>:</label>
							<input id="taxa" type="text" size="60" name="taxa" id="taxa" value="" title="<?= $LANG['SEPARATE_MULTIPLE']; ?>">
						</div>
					</div>
					<div>
						<div><button type="submit" style="width:150px"><?= $LANG['BUTTON_NEXT_LIST'] ?></button></div>
						<div><button type="button" style="width:150px" onclick="displayTableView(this.form)"><?= $LANG['BUTTON_NEXT_TABLE'] ?></button></div>
						<div><button type="reset" style="width:150px" onclick="resetHarvestParamsForm()"><?= $LANG['BUTTON_RESET'] ?></button></div>
					</div>
				</section>
			</div>
			<hr/>
			<div>
				<div class="catHeaderDiv"><?= $LANG['LOCALITY_CRITERIA']; ?></div>
			</div>
			<div>
				<label for="country"><?= $LANG['COUNTRY']; ?>:</label>
				<input type="text" id="country" size="43" name="country" value="" title="<?= $LANG['SEPARATE_MULTIPLE']; ?>" />
			</div>
			<div>
				<label for="state"><?= $LANG['STATE']; ?>:</label>
				<input type="text" id="state" size="37" name="state" value="" title="<?= $LANG['SEPARATE_MULTIPLE']; ?>" />
			</div>
			<div>
				<label for="county"><?= $LANG['COUNTY']; ?>:</label>
				<input type="text" id="county" size="37"  name="county" value="" title="<?= $LANG['SEPARATE_MULTIPLE']; ?>" />
			</div>
			<div>
				<label for="locality"><?= $LANG['LOCALITY']; ?>:</label>
				<input type="text" id="locality" size="43" name="local" value="" />
			</div>
			<section class="flex-form">
				<div>
					<label for="elevlow"><?= $LANG['ELEV_INPUT_1']; ?>:</label>
					<input type="text" id="elevlow" size="10" name="elevlow" value="" onchange="cleanNumericInput(this);" />
				</div>
				<div>
					<label for="elevhigh"><?= $LANG['ELEV_INPUT_2']; ?>:</label>
					<input type="text" id="elevhigh" size="10" name="elevhigh" value="" onchange="cleanNumericInput(this);" />
				</div>
			</section>
			<hr>
			<div class="catHeaderDiv"><?= $LANG['LAT_LNG_HEADER']; ?></div>
			<div>
				<div class="coordBoxDiv">
					<div class="labelDiv">
						<?= $LANG['LL_BOUND_TEXT']; ?>
					</div>
					<div class="iconDiv">
						<a href="#" onclick="openCoordAid({map_mode: MAP_MODES.RECTANGLE, client_root: '<?= $CLIENT_ROOT?>'});return false;"><img src="../images/map.png" title="<?= $LANG['MAP_AID'] ?>" /></a>
					</div>
					<div class="elemDiv">
						<div>
							<label for="upperlat"><?= $LANG['LL_BOUND_NLAT']; ?>:</label>
							<input type="text" id="upperlat" name="upperlat" size="7" value="" onchange="cleanNumericInput(this);">
							<label for="upperlat_NS"><?= $LANG['DIRECTION'] ?>:</label>
							<select id="upperlat_NS" name="upperlat_NS">
								<option id="ulN" value="N"><?= $LANG['LL_N_SYMB']; ?></option>
								<option id="ulS" value="S"><?= $LANG['LL_S_SYMB']; ?></option>
							</select>
						</div>
						<div>
							<label for="bottomlat"><?= $LANG['LL_BOUND_SLAT']; ?>:</label>
							<input type="text" id="bottomlat" name="bottomlat" size="7" value="" onchange="cleanNumericInput(this);">
							<label for="bottomlat_NS"><?= $LANG['DIRECTION'] ?>:</label>
							<select id="bottomlat_NS" name="bottomlat_NS">
								<option id="blN" value="N"><?= $LANG['LL_N_SYMB']; ?></option>
								<option id="blS" value="S"><?= $LANG['LL_S_SYMB']; ?></option>
							</select>
						</div>
						<div>
							<label for="leftlong"><?= $LANG['LL_BOUND_WLNG']; ?>:</label>
							<input type="text" id="leftlong" name="leftlong" size="7" value="" onchange="cleanNumericInput(this);">
							<label for="leftlong_EW"><?= $LANG['DIRECTION'] ?>:</label>
							<select id="leftlong_EW" name="leftlong_EW">
								<option id="llW" value="W"><?= $LANG['LL_W_SYMB']; ?></option>
								<option id="llE" value="E"><?= $LANG['LL_E_SYMB']; ?></option>
							</select>
						</div>
						<div>
							<label for="rightlong"><?= $LANG['LL_BOUND_ELNG']; ?>:</label>
							<input type="text" id="rightlong" name="rightlong" size="7" value="" onchange="cleanNumericInput(this);" style="margin-left:3px;">
							<label for="rightlong_EW"><?= $LANG['DIRECTION'] ?>:</label>
							<select id="rightlong_EW" name="rightlong_EW">
								<option id="rlW" value="W"><?= $LANG['LL_W_SYMB']; ?></option>
								<option id="rlE" value="E"><?= $LANG['LL_E_SYMB']; ?></option>
							</select>
						</div>
					</div>
				</div>
				<div class="coordBoxDiv">
					<div class="labelDiv">
						<label for="footprintwkt">
							<?= $LANG['LL_POLYGON_TEXT'] ?>
						</label>
					</div>
					<div class="iconDiv">
						&nbsp;<a href="#" onclick="openCoordAid({map_mode: MAP_MODES.POLYGON, polygon_text_type: POLYGON_TEXT_TYPES.GEOJSON, client_root: '<?= $CLIENT_ROOT?>'});return false;"><img src="../images/map.png" title="<?= $LANG['MAP_AID'] ?>" /></a>
					</div>
					<div class="elemDiv">
						<textarea id="footprintwkt" name="footprintGeoJson" style="zIndex:999;width:100%;height:90px" onchange="cleanPolygon(this)"></textarea>
					</div>
				</div>
				<div class="coordBoxDiv">
					<div class="labelDiv">
						<?= $LANG['LL_P-RADIUS_TEXT']; ?>
					</div>
					<div class="iconDiv">
						<a href="#" onclick="openCoordAid({map_mode: MAP_MODES.CIRCLE, client_root: '<?= $CLIENT_ROOT?>'});return false;"><img src="../images/map.png" title="<?= $LANG['MAP_AID'] ?>" /></a>
					</div>
					<div class="elemDiv">
						<section class="flex-form">
							<div>
								<label for="pointlat"><?= $LANG['LL_P-RADIUS_LAT']; ?>:</label>
								<input type="text" id="pointlat" name="pointlat" size="7" value="" onchange="cleanNumericInput(this);">
								<label for="pointlat_NS"><?= $LANG['DIRECTION'] ?>:</label>
								<select id="pointlat_NS" name="pointlat_NS">
									<option id="N" value="N"><?= $LANG['LL_N_SYMB']; ?></option>
									<option id="S" value="S"><?= $LANG['LL_S_SYMB']; ?></option>
								</select>
							</div>
						</section>
						<section class="flex-form">
							<div>
								<label for="pointlong"><?= $LANG['LL_P-RADIUS_LNG']; ?>:</label>
								<input type="text" id="pointlong" name="pointlong" size="7" value="" onchange="cleanNumericInput(this);">
								<label for="pointlong_EW"><?= $LANG['DIRECTION'] ?>:</label>
								<select id="pointlong_EW" name="pointlong_EW">
									<option id="W" value="W"><?= $LANG['LL_W_SYMB']; ?></option>
									<option id="E" value="E"><?= $LANG['LL_E_SYMB']; ?></option>
								</select>
							</div>
						</section>
						<section class="flex-form">
							<div>
								<label for="radius"><?= $LANG['LL_P-RADIUS_RADIUS']; ?>:</label>
								<input type="text" id="radius" name="radius" size="5" value="" onchange="cleanNumericInput(this);">
							</div>
							<div>
								<label for="radiusunits"><?= $LANG['DISTANCE_UNIT'] ?>:</label>
								<select id="radiusunits" name="radiusunits">
									<option value="km"><?= $LANG['LL_P-RADIUS_KM']; ?></option>
									<option value="mi"><?= $LANG['LL_P-RADIUS_MI']; ?></option>
								</select>
							</div>
						</section>
					</div>
				</div>
			</div>
			<hr/>
			<div class="catHeaderDiv"><?= $LANG['COLLECTOR_HEADER']; ?></div>
			<div>
				<label for="collector"><?= $LANG['COLLECTOR_LASTNAME']; ?>:</label>
				<input type="text" id="collector" size="32" name="collector" value="" title="<?= $LANG['SEPARATE_MULTIPLE']; ?>" />
			</div>
			<div>
				<label for="collnum"><?= $LANG['COLLECTOR_NUMBER']; ?>:</label>
				<input type="text" id="collnum" size="31" name="collnum" value="" title="<?= $LANG['TITLE_TEXT_2']; ?>" />
			</div>
			<section class="flex-form">
				<div>
					<label for="eventdate1"><?= $LANG['COLLECTOR_DATE']; ?>:</label>
					<input type="text" id="eventdate1" size="32" name="eventdate1" style="width:100px;" value="" title="<?= $LANG['TITLE_TEXT_3']; ?>" /> -
				</div>
				<div>
					<label for="eventdate2"><?= $LANG['COLLECTOR_DATE_END']; ?>:</label>
					<input type="text" id="eventdate2" size="32" name="eventdate2" style="width:100px;" value="" title="<?= $LANG['TITLE_TEXT_4']; ?>" />
				</div>
			</section>
			<hr/>
			<div style="float:left">
				<div>
					<div class="catHeaderDiv"><?= $LANG['SPECIMEN_HEADER']; ?></div>
				</div>
				<div>
					<label for="catnum"><?= $LANG['CATALOG_NUMBER']; ?>:</label>
					<input type="text" id="catnum" size="32" name="catnum" value="" title="<?= $LANG['SEPARATE_MULTIPLE']; ?>" />
					<input name="includeothercatnum" id="includeothercatnum" type="checkbox" value="1" checked />
					<label for="includeothercatnum"><?= $LANG['INCLUDE_OTHER_CATNUM']?></label>
				</div>
				<?php
				if($matSampleTypeArr = $collManager->getMaterialSampleTypeArr()){
					?>
					<div>
						<label for="materialsampletype"><?= $LANG['MATERIAL_SAMPLE_TYPE'] ?></label>
						<select name="materialsampletype" id="materialsampletype">
							<option value="">---------------</option>
							<option value="all-ms"><?= $LANG['ALL_MATERIAL_SAMPLE'] ?></option>
							<?php
							foreach($matSampleTypeArr as $matSampeType){
								echo '<option value="' . $matSampeType . '">' . $matSampeType . '</option>';
							}
							?>
						</select>
					</div>
					<?php
				}
				?>
				<div>
					<input type='checkbox' name='typestatus' id='typestatus' value='1' />
					<label for="typestatus"><?= $LANG['TYPE'] ?></label>
				</div>
				<div>
					<input type='checkbox' name='hasimages' id='hasimages' value='1' />
					<label for="hasimages"><?= $LANG['HAS_IMAGE'] ?></label>
				</div>
				<div>
					<input type='checkbox' name='hasgenetic' id='hasgenetic' value='1' />
					<label for="hasgenetic"><?= $LANG['HAS_GENETIC'] ?></label>
				</div>
				<div>
					<input type='checkbox' name='hascoords' id='hascoords' value='1' />
					<label for="hascoords"><?= $LANG['HAS_COORDS'] ?></label>
				</div>
				<div>
					<input type='checkbox' name='includecult' id='includecult' value='1' <?= !empty($SHOULD_INCLUDE_CULTIVATED_AS_DEFAULT) ? 'checked' : '' ?> />
					<label for="includecult"><?= $LANG['INCLUDE_CULTIVATED'] ?></label>
				</div>
			</div>
			<?php
			if(!empty($ACTIVATE_PALEO)) {
				$gtsTermArr = $collManager->getPaleoGtsTerms();
				?>
				<hr/>
				<div id="searchFormPaleo" style="float:left">
					<div>
						<div class="catHeaderDiv bottom-breathing-room-rel-sm"><?= $LANG['GEO_CONTEXT']; ?></div>
					</div>
					<div>
						<div>
							<div>
								<div>
									<label for="lateInterval"><?= $LANG['LATE_INT'] ?>:</label>
									<select name="lateInterval" type="text" id="lateInterval">
										<option value=""></option>
										<?php
										$lateIntervalTerm = '';
										if(isset($occArr['lateInterval'])) $lateIntervalTerm = $occArr['lateInterval'];
										if($lateIntervalTerm && !array_key_exists($lateIntervalTerm, $gtsTermArr)){
											echo '<option value="'.$lateIntervalTerm.'" SELECTED>'.$lateIntervalTerm.' - mismatched term</option>';
											echo '<option value="">---------------------------</option>';
										}
										foreach($gtsTermArr as $term => $rankid){
											echo '<option value="'.$term.'" '.($lateIntervalTerm==$term?'SELECTED':'').'>'.$term.'</option>';
										}
										?>
									</select>
								</div>
								<label for="earlyInterval"><?= $LANG['EARLY_INT'] ?>:</label>
									<select name="earlyInterval" type="text" id="earlyInterval">
										<option value=""></option>
										<?php
										$earlyIntervalTerm = '';
										if(isset($occArr['earlyInterval'])) $earlyIntervalTerm = $occArr['earlyInterval'];
										if($earlyIntervalTerm && !array_key_exists($earlyIntervalTerm, $gtsTermArr)){
											echo '<option value="'.$earlyIntervalTerm.'" SELECTED>'.$earlyIntervalTerm.' - mismatched term</option>';
											echo '<option value="">---------------------------</option>';
										}
										foreach($gtsTermArr as $term => $rankid){
											echo '<option value="'.$term.'" '.($earlyIntervalTerm==$term?'SELECTED':'').'>'.$term.'</option>';
										}
										?>
									</select>
								</div>
						</div>
						<div>
							<div>
								<label for="lithogroup"><?= $LANG['LITHOGROUP']?>:</label>
									<input type="text" name="lithogroup" id="lithogroup"/>
							</div>
							<div>
								<label for="formation"> <?= $LANG['FORMATION']?>:</label>
									<input type="text" name="formation" id="formation"/>
							</div>
							<div>
								<label for="member"><?= $LANG['MEMBER']?>:</label>
									<input type="text" name="member" id="member"/>
							</div>
							<div>
								<label for="bed"><?= $LANG['BED']?>:</label>
									<input type="text" name="bed" id="bed"/>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
			if(!empty($SEARCH_BY_TRAITS)) {
				$attribSearch = new OccurrenceAttributeSearch();
				$traitArr = $attribSearch->getTraitSearchArr($SEARCH_BY_TRAITS);
				if($traitArr){
					?>
					<hr/>
					<div style="float:left">
						<div>
							<div class="catHeaderDiv"><?= $LANG['TRAIT_HEADER']; ?></div>
							<div><?= $LANG['TRAIT_DESCRIPTION']; ?></div>
							<input type="hidden" id="SearchByTraits" value="true">
						</div>
						<?php
						foreach($traitArr as $traitID => $traitData){
							if(!isset($traitData['dependentTrait'])) {
								?>
								<fieldset style="margin-top:10px;display:inline;min-width:500px">
									<legend><b>Trait: <?= $traitData['name']; ?></b></legend>
									<div style="float:right">
										<div class="trianglediv" style="margin:4px 3px;float:right;cursor:pointer" onclick="setAttributeTree(this)" title="Toggle attribute tree open/close">
											<img class="triangleright" src="../images/triangleright.png" style="width:1.3em;display:none" />
											<img class="triangledown" src="../images/triangledown.png" style="width:1.3em;" />
										</div>
									</div>
									<div class="traitDiv" style="margin-left:5px;float:left">
										<?php $attribSearch->echoTraitSearchForm($traitID); ?>
									</div>
								</fieldset>
								<?php
							}
						}
						?>
					</div>
					<?php
				}
			}
			?>

			<div style="float:right;">
				<div><button type="submit" style="width:100%"><?= $LANG['BUTTON_NEXT_LIST'] ?></button></div>
				<div><button type="button" style="width:100%" onclick="displayTableView(this.form)"><?= $LANG['BUTTON_NEXT_TABLE'] ?></button></div>
				<div><button type="reset" style="width:100%" onclick="resetHarvestParamsForm()"><?= $LANG['BUTTON_RESET'] ?></button></div>
			</div>
			<div>
				<input name="comingFrom" type="hidden" value="harvestparams" >
				<input type="hidden" name="db" value="<?= $collManager->getSearchTerm('db', $_SERVER['REQUEST_URI']); ?>" />
			</div>
			<hr/>
		</form>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
