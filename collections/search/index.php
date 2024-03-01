<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
include_once('../../config/symbini.php');
include_once('../../content/lang/index.' . $LANG_TAG . '.php');
include_once($SERVER_ROOT . '/classes/CollectionMetadata.php');
include_once($SERVER_ROOT . '/classes/DatasetsMetadata.php');
include_once($SERVER_ROOT.'/content/lang/collections/sharedterms.'.$LANG_TAG.'.php');
include_once($SERVER_ROOT.'/classes/OccurrenceManager.php');
header("Content-Type: text/html; charset=" . $CHARSET);
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/collections/search/index.' . $LANG_TAG . '.php')) include_once($SERVER_ROOT.'/content/lang/collections/search/index.' . $LANG_TAG . '.php');
else include_once($SERVER_ROOT . '/content/lang/collections/search/index.en.php');

$collManager = new OccurrenceManager();
$collectionSource = $collManager->getQueryTermStr();

$SHOULD_INCLUDE_CULTIVATED_AS_DEFAULT = $SHOULD_INCLUDE_CULTIVATED_AS_DEFAULT ?? false;
$collData = new CollectionMetadata();
$siteData = new DatasetsMetadata();

$catId = array_key_exists("catid",$_REQUEST)?$_REQUEST["catid"]:'';
$collManager = new OccurrenceManager();
$collList = $collManager->getFullCollectionList($catId);
$specArr = (isset($collList['spec'])?$collList['spec']:null);
$obsArr = (isset($collList['obs'])?$collList['obs']:null);
?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">

<head>
	<title><?php echo $DEFAULT_TITLE; ?><?php echo $LANG['SAMPLE_SEARCH'] ?></title>
	<?php
	$activateJQuery = true;
	if (file_exists($SERVER_ROOT . '/includes/head.php')) {
		include_once($SERVER_ROOT . '/includes/head.php');
	} else {
		echo '<link href="' . $CLIENT_ROOT . '/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
		echo '<link href="' . $CLIENT_ROOT . '/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
	}
	echo '<link href="' . $CLIENT_ROOT . '/collections/search/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
	echo '<link href="' . $CLIENT_ROOT . '/collections/search/css/app.css" type="text/css" rel="stylesheet" />';
	
	echo '<link href="' . $CLIENT_ROOT . '/collections/search/css/tables.css" type="text/css" rel="stylesheet" />';
	echo '<link href="' . $CLIENT_ROOT . '/css/v202209/symbiota/collections/sharedCollectionStyling.css" type="text/css" rel="stylesheet" />';
	?>
	<script src="<?php echo $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script>
		const clientRoot = '<?php echo $CLIENT_ROOT; ?>';
	</script>
	<?php include_once($SERVER_ROOT . '/includes/googleanalytics.php'); ?>
	<!-- Search-specific styles -->
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
	<?php
	include($SERVER_ROOT . '/includes/header.php');
	?>
	<!-- This is inner text! -->
	<div id="innertext" class="inner-search">
		<h1><?php echo $LANG['SAMPLE_SEARCH'] ?></h1>
		<div id="error-msgs" class="errors"></div>
		<form id="params-form" action="javascript:void(0);">
			<!-- Criteria forms -->
			
			<div class="accordions">
				<!-- Taxonomy -->
				<section>
					<!-- Accordion selector -->
					<input type="checkbox" id="taxonomy" class="accordion-selector" checked />

					<!-- Accordion header -->
					<label for="taxonomy" class="accordion-header"><?php echo $LANG['TAXONOMY'] ?></label>

					<!-- Taxonomy -->
					<div id="search-form-taxonomy" class="content">
						<div id="taxa-text" class="input-text-container">
							<label for="taxa" class="input-text--outlined">
								<span class="skip-link"><?php echo $LANG['TAXON'] ?></span>
								<input type="text" name="taxa" id="taxa" data-chip="<?php echo $LANG['TAXON'] ?>">
								<span data-label="<?php echo $LANG['TAXON'] ?>"></span>
							</label>
							<span class="assistive-text"><?php echo $LANG['TYPE_CHAR_FOR_SUGGESTIONS'] ?></span>
						</div>
						<div class="select-container">
							<label for="taxontype" class="skip-link"><?php echo $LANG['TAXON_TYPE'] ?></label>
							<select name="taxontype" id="taxontype">
								<option value="1"><?php echo $LANG['ANY_NAME'] ?></option>
								<option value="2"><?php echo $LANG['SCIENTIFIC_NAME'] ?></option>
								<option value="3"><?php echo $LANG['FAMILY'] ?></option>
								<option value="4"><?php echo $LANG['TAXONOMIC GROUP'] ?></option>
								<option value="5"><?php echo $LANG['COMMON_NAME'] ?></option>
							</select>
							<span class="assistive-text"><?php echo $LANG['TAXON_TYPE'] ?></span>
						</div>
						<div>
							<input type="checkbox" name="usethes" id="usethes" data-chip="<?php echo $LANG['INCLUDE_SYNONYMS'] ?>" value="1" checked>
							<label for="usethes">
								<span class="ml-1"><?php echo $LANG['INCLUDE_SYNONYMS'] ?></span>
							</label>
						</div>
					</div>
				</section>

				<!-- Colections -->
				<section>
					<!-- Accordion selector -->
					<input type="checkbox" id="collections" class="accordion-selector" checked />
					<!-- Accordion header -->
					<label for="collections" class="accordion-header"><?php echo $LANG['COLLECTIONS'] ?></label>
					<!-- Accordion content -->
					<div class="content">
						<div id="search-form-colls">
							<!-- Open Collections modal -->
							<div id="specobsdiv">
								<?php 
								include_once('./collectionContent.php');
								?>
							</div>
							
						</div>
					</div>
				</section>
				
				<!-- Sample Properties -->
				<section>
					<!-- Accordion selector -->
					<input type="checkbox" id="sample" class="accordion-selector" checked />
					<!-- Accordion header -->
					<label for="sample" class="accordion-header"><?php echo $LANG['SAMPLE_PROPERTIES'] ?></label>
					<!-- Accordion content -->
					<div class="content">
						<div id="search-form-sample">
							<div>
								<div>
									<input type="checkbox" name="includeothercatnum" id="includeothercatnum" value="1" data-chip="<?php echo $LANG['INCLUDE_OTHER_IDS'] ?>" checked>
									<label for="includeothercatnum"><?php echo $LANG['INCLUDE_CATA_NUM_GUIDs'] ?></label>
								</div>
								<div class="input-text-container">
									<label for="catnum" class="input-text--outlined">
										<span class="skip-link"><?php echo $LANG['CATALOG_NUMBER'] ?></span>
										<input type="text" name="catnum" id="catnum" data-chip="<?php echo $LANG['CATALOG_NUMBER'] ?>">
										<span data-label="<?php echo $LANG['CATALOG_NUMBER'] ?>"></span>
									</label>
									<span class="assistive-text"><?php echo $LANG['SEPARATE_MULTIPLE_W_COMMA'] ?></span>
								</div>
							</div>
							<div>
								<div>
									<input type='checkbox' name='typestatus' id='typestatus' value='1' data-chip="<?php echo $LANG['ONLY_TYPE_SPECIMENS'] ?>" />
									<label for="typestatus"><?php echo $LANG['TYPE'] ?></label>
								</div>
								<div>
									<input type="checkbox" name="hasimages" id="hasimages" value=1 data-chip="<?php echo $LANG['ONLY_WITH_IMAGES'] ?>">
									<label for="hasimages"><?php echo $LANG['LIMIT_TO_SPECIMENS_W_IMAGES'] ?></label>
								</div>
								<div>
									<input type="checkbox" name="hasgenetic" id="hasgenetic" value=1 data-chip="<?php echo $LANG['ONLY_WITH_GENETIC'] ?>">
									<label for="hasgenetic"><?php echo $LANG['LIMIT_TO_SPECIMENS_W_GENETIC_DATA'] ?></label>
								</div>
								<div>
									<input type='checkbox' name='hascoords' id='hascoords' value='1' data-chip="<?php echo $LANG['ONLY_WITH_COORDINATES'] ?>" />
									<label for="hascoords"><?php echo $LANG['HAS_COORDS'] ?></label>
								</div>
								<div>
									<input type='checkbox' name='includecult' id='includecult' value='1' data-chip="<?php echo $LANG['INCLUDE_CULTIVATED'] ?>" <?php echo $SHOULD_INCLUDE_CULTIVATED_AS_DEFAULT ? 'checked' : '' ?> />
									<label for="includecult"><?php echo $LANG['INCLUDE_CULTIVATED'] ?></label>
								</div>
							</div>
						</div>
					</div>
				</section>
				
				<!-- Locality -->
				<section>
					<!-- Accordion selector -->
					<input type="checkbox" id="locality" name="locality" class="accordion-selector" />
					<!-- Accordion header -->
					<label for="locality" class="accordion-header"><?php echo $LANG['LOCALITY'] ?></label>
					<!-- Accordion content -->
					<div class="content">
						<div id="search-form-locality">
							<div>
								<div>
									<div class="input-text-container">
										<label for="country" class="input-text--outlined">
											<span class="skip-link"><?php echo $LANG['COUNTRY'] ?></span>
											<input type="text" name="country" id="country" data-chip="<?php echo $LANG['COUNTRY'] ?>">
											<span data-label="<?php echo $LANG['COUNTRY'] ?>"></span>
										</label>
										<span class="assistive-text"><?php echo $LANG['SEPARATE_MULTIPLE_W_COMMA'] ?></span>
									</div>
									<div class="input-text-container">
										<label for="state" class="input-text--outlined">
											<span class="skip-link"><?php echo $LANG['STATE'] ?></span>
											<input type="text" name="state" id="state" data-chip="<?php echo $LANG['STATE'] ?>">
											<span data-label="<?php echo $LANG['STATE'] ?>"></span>
										</label>
										<span class="assistive-text"><?php echo $LANG['SEPARATE_MULTIPLE_W_COMMA'] ?></span>
									</div>
									<div class="input-text-container">
										<label for="county" class="input-text--outlined">
											<span class="skip-link"><?php echo $LANG['COUNTY'] ?></span>
											<input type="text" name="county" id="county" data-chip="<?php echo $LANG['COUNTY'] ?>">
											<span data-label="<?php echo $LANG['COUNTY'] ?>"></span>
										</label>
										<span class="assistive-text"><?php echo $LANG['SEPARATE_MULTIPLE_W_COMMA'] ?></span>
									</div>
									<div class="input-text-container">
										<label for="local" class="input-text--outlined">
											<span class="skip-link"><?php echo $LANG['LOCALITY_LOCALITIES'] ?></span>
											<input type="text" name="local" id="local" data-chip="<?php echo $LANG['LOCALITY'] ?>">
											<span data-label="<?php echo $LANG['LOCALITY_LOCALITIES'] ?>"></span>
										</label>
										<span class="assistive-text" style="line-height:1.7em"><?php echo $LANG['SEPARATE_MULTIPLE_W_COMMA'] ?></span>
									</div>
								</div>
								<div class="grid grid--half">
									<div class="input-text-container">
										<label for="elevlow" class="input-text--outlined">
											<span class="skip-link"><?php echo $LANG['MINIMUM_ELEVATION'] ?></span>
											<input type="number" step="any" name="elevlow" id="elevlow" data-chip="<?php echo $LANG['MIN_ELEVATION'] ?>">
											<span data-label="<?php echo $LANG['MINIMUM_ELEVATION'] ?>"></span>
										</label>
										<span class="assistive-text"><?php echo $LANG['NUMBER_IN_METERS'] ?></span>
									</div>
									<div class="input-text-container">
										<label for="elevhigh" class="input-text--outlined">
											<span class="skip-link"><?php echo $LANG['MAXIMUM_ELEVATION'] ?></span>
											<input type="number" step="any" name="elevhigh" id="elevhigh" data-chip="<?php echo $LANG['MAX_ELEVATION'] ?>">
											<span data-label="<?php echo $LANG['MAXIMUM_ELEVATION'] ?>"></span>
										</label>
										<span class="assistive-text"><?php echo $LANG['NUMBER_IN_METERS'] ?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>

				<!-- Latitude & Longitude -->
				<section>
					<!-- Accordion selector -->
					<input type="checkbox" id="lat-long" class="accordion-selector" />
					<!-- Accordion header -->
					<label for="lat-long" class="accordion-header"><?php echo $LANG['LATITUDE_LONGITUDE'] ?></label>
					<!-- Accordion content -->
					<div class="content">
						<div id="search-form-latlong">
							<div id="bounding-box-form">
								<h3><?php echo $LANG['BOUNDING_BOX'] ?></h3>
								<button onclick="openCoordAid('rectangle');return false;"><?php echo $LANG['SELECT_IN_MAP'] ?></button>
								<div class="input-text-container">
										<label for="upperlat" class="input-text--outlined">
											<span class="skip-link"><?php echo $LANG['UPPER_LATITUDE'] ?></span>
											<input type="number" step="any" min="-90" max="90" id="upperlat" name="upperlat" data-chip="<?php echo $LANG['UPPER_LAT'] ?>">
											<span data-label="<?php echo $LANG['UPPER_LATITUDE'] ?>"></span>
											<span class="assistive-text"><?php echo $LANG['VALUE_BETWEEN_NUM'] ?></span>
										</label>

										<label for="upperlat_NS" class="input-text--outlined">
											<span class="skip-link"><?php echo $LANG['SELECT_UPPER_LAT_DIRECTION_NORTH_SOUTH'] ?></span>
											<select class="mt-1" id="upperlat_NS" name="upperlat_NS">
												<option value=""><?php echo $LANG['SELECT_NORTH_SOUTH'] ?></option>
												<option id="ulN" value="N"><?php echo $LANG['NORTH'] ?></option>
												<option id="ulS" value="S"><?php echo $LANG['SOUTH'] ?></option>
											</select>
										</label>
								</div>
								<div class="input-text-container">
									<label for="bottomlat" class="input-text--outlined">
										<span class="skip-link"><?php echo $LANG['BOTTOM_LATITUDE'] ?></span>
										<input type="number" step="any" min="-90" max="90" id="bottomlat" name="bottomlat" data-chip="<?php echo $LANG['BOTTOM_LAT'] ?>">
										<span data-label="<?php echo $LANG['SOUTHERN_LATITUDE'] ?>"></span>
										<span class="assistive-text"><?php echo $LANG['VALUE_BETWEEN_NUM'] ?></span>
									</label>
									<label for="bottomlat_NS">
										<span class="skip-link"><?php echo $LANG['SELECT_BOTTOM_LAT_DIREC_NORTH_SOUTH'] ?></span>
										<select class="mt-1" id="bottomlat_NS" name="bottomlat_NS">
											<option value=""><?php echo $LANG['SELECT_NORTH_SOUTH'] ?></option>
											<option id="blN" value="N"><?php echo $LANG['NORTH'] ?></option>
											<option id="blS" value="S"><?php echo $LANG['SOUTH'] ?></option>
										</select>
									</label>
								</div>
								<div class="input-text-container">
									<label for="leftlong" class="input-text--outlined">
										<span class="skip-link"><?php echo $LANG['LEFT_LONGITUDE'] ?></span>
										<input type="number" step="any" min="-180" max="180" id="leftlong" name="leftlong" data-chip="<?php echo $LANG['LEFT_LONG'] ?>">
										<span data-label="<?php echo $LANG['WESTERN_LONGITUDE'] ?>"></span>
										<span class="assistive-text"><?php echo $LANG['VALUES_BETWEEN_NEG180_TO_180'] ?></span>
									</label>
									<label for="leftlong_EW" class="input-text--outlined">
										<span class="skip-link"><?php echo $LANG['SELECT_LEFT_LONG_DIREC_WEST_EAST'] ?></span>
										<select class="mt-1" id="leftlong_EW" name="leftlong_EW">
											<option value=""><?php echo $LANG['SELECT_WEST_EAST'] ?></option>
											<option id="llW" value="W"><?php echo $LANG['WEST'] ?></option>
											<option id="llE" value="E"><?php echo $LANG['EAST'] ?></option>
										</select>
									</label>
								</div>
								<div class="input-text-container">
									<label for="rightlong" class="input-text--outlined">
										<span class="skip-link"><?php echo $LANG['RIGHT_LONGITUDE'] ?></span>
										<input type="number" step="any" min="-180" max="180" id="rightlong" name="rightlong" data-chip="<?php echo $LANG['RIGHT_LONG'] ?>">
										<span data-label="<?php echo $LANG['EASTERN_LONGITUDE'] ?>"></span>
										<span class="assistive-text"><?php echo $LANG['VALUES_BETWEEN_NEG180_TO_180'] ?></span>
									</label>
										<label for="rightlong_EW" class="input-text--outlined">
											<span class="skip-link"><?php echo $LANG['SELECT_RIGHT_LONG_DIREC_WEST_EAST'] ?></span>
											<select class="mt-1" id="rightlong_EW" name="rightlong_EW">
												<option value=""><?php echo $LANG['SELECT_WEST_EAST'] ?></option>
												<option id="rlW" value="W"><?php echo $LANG['WEST'] ?></option>
												<option id="rlE" value="E"><?php echo $LANG['EAST'] ?></option>
											</select>
										</label>
								</div>
							</div>
							<div id="polygon-form">
								<h3><?php echo $LANG['POLYGON_WKT_FOOTPRINT'] ?></h3>
								<button onclick="openCoordAid('polygon');return false;"><?php echo $LANG['SELECT_MAP_POLYGON'] ?></button>
								<div class="text-area-container">
									<label for="footprintwkt" class="text-area--outlined">
										<span class="skip-link"><?php echo $LANG['POLYGON'] ?></span>
										<textarea id="footprintwkt" name="footprintwkt" class="full-width-pcnt" rows="5"></textarea>
										<span data-label="<?php echo $LANG['POLYGON'] ?>"></span>
									</label>
									<span class="assistive-text"><?php echo $LANG['SELECT_MAP_BUTTON_PASTE'] ?></span>
								</div>
							</div>
							<div id="point-radius-form">
								<h3><?php echo $LANG['POINT_RADIUS'] ?></h3>
								<button onclick="openCoordAid('circle');return false;"><?php echo $LANG['SELECT_MAP_PR'] ?></button>
								<div class="input-text-container">
									<label for="pointlat" class="input-text--outlined">
										<span class="skip-link"><?php echo $LANG['POINT_LATITUDE'] ?></span>
										<input type="number" step="any" min="-90" max="90" id="pointlat" name="pointlat" data-chip="<?php echo $LANG['POINT_LAT'] ?>">
										<span data-label="<?php echo $LANG['LATITUDE'] ?>"></span>
										<span class="assistive-text"><?php echo $LANG['VALUE_BETWEEN_NUM'] ?></span>
									</label>
									<label for="pointlat_NS" class="input-text--outlined">
										<span class="skip-link"><?php echo $LANG['POINT_LAT_DIREC_NORTH_SOUTH'] ?></span>
										<select class="mt-1" id="pointlat_NS" name="pointlat_NS">
											<option value=""><?php echo $LANG['SELECT_NORTH_SOUTH'] ?></option>
											<option id="N" value="N"><?php echo $LANG['NORTH'] ?></option>
											<option id="S" value="S"><?php echo $LANG['SOUTH'] ?></option>
										</select>
									</label>
								</div>
								<div class="input-text-container">
									<label for="pointlong" class="input-text--outlined">
										<span class="skip-link"><?php echo $LANG['POINT_LONGITUDE'] ?></span>
										<input type="number" step="any" min="-180" max="180" id="pointlong" name="pointlong" data-chip="<?php echo $LANG['POINT_LONG'] ?>">
										<span data-label="<?php echo $LANG['LONGITUDE'] ?>"></span>
										<span class="assistive-text"><?php echo $LANG['VALUES_BETWEEN_NEG180_TO_180'] ?></span>
									</label>
									<label for="pointlong_EW" class="input-text--outlined">
										<span class="skip-link"><?php echo $LANG['POINT_LONGITUDE_DIREC_EAST_WEST'] ?></span>
										<select class="mt-1" id="pointlong_EW" name="pointlong_EW">
											<option value=""><?php echo $LANG['SELECT_WEST_EAST'] ?></option>
											<option id="W" value="W"><?php echo $LANG['WEST'] ?></option>
											<option id="E" value="E"><?php echo $LANG['EAST'] ?></option>
										</select>
									</label>
								</div>
								<div class="input-text-container">
									<label for="radius" class="input-text--outlined">
										<span class="skip-link"><?php echo $LANG['RADIUS'] ?></span>
										<input type="number" min="0" step="any" id="radius" name="radius" data-chip="<?php echo $LANG['RADIUS'] ?>">
										<span data-label="<?php echo $LANG['RADIUS'] ?>"></span>
										<span class="assistive-text"><?php echo $LANG['ANY_POSITIVE_VALUES'] ?></span>
									</label>
									<label for="radiusunits" class="input-text--outlined">
										<span class="skip-link"><?php echo $LANG['SELECT_RADIUS_UNITS'] ?></span>
										<select class="mt-1" id="radiusunits" name="radiusunits">
											<option value=""><?php echo $LANG['SELECT_UNIT'] ?></option>
											<option value="km"><?php echo $LANG['KILOMETERS'] ?></option>
											<option value="mi"><?php echo $LANG['MILES'] ?></option>
										</select>
									</label>
								</div>
							</div>
						</div>
					</div>
				</section>
				<!-- Collecting Event -->
				<section>
					<!-- Accordion selector -->
					<input type="checkbox" id="coll-event" class="accordion-selector" />
					<!-- Accordion header -->
					<label for="coll-event" class="accordion-header"><?php echo $LANG['COLLECTING_EVENT'] ?></label>
					<!-- Accordion content -->
					<div class="content">
						<div id="search-form-coll-event">
							<div class="input-text-container">
								<label for="eventdate1" class="input-text--outlined">
									<span class="skip-link"><?php echo $LANG['COLLECTION_START_DATE'] ?></span>
									<input type="text" name="eventdate1" id="eventdate1" data-chip="<?php echo $LANG['EVENT_DATE_START'] ?>">
									<span data-label="<?php echo $LANG['COLLECTION_START_DATE'] ?>"></span>
								</label>
								<span class="assistive-text"><?php echo $LANG['SINGLE_DATE_START_DATE'] ?></span>
							</div>
							<div class="input-text-container">
								<label for="eventdate2" class="input-text--outlined">
									<span class="skip-link"><?php echo $LANG['COLLECTION_END_DATE'] ?></span>
									<input type="text" name="eventdate2" id="eventdate2" data-chip="<?php echo $LANG['EVENT_DATE_END'] ?>">
									<span data-label="<?php echo $LANG['COLLECTION_END_DATE'] ?>"></span>
								</label>
								<span class="assistive-text"><?php echo $LANG['SINGLE_DATE_END_DATE'] ?></span>
							</div>
							<div class="input-text-container">
								<label for="collector" class="input-text--outlined">
									<span class="skip-link"><?php echo $LANG['COLLECTOR_LAST_NAME'] ?></span>
									<input type="text" id="collector" size="32" name="collector" value="" title="<?php echo $LANG['SEPARATE_MULTIPLE']; ?>" data-chip="<?php echo $LANG['COLLECTOR_LAST'] ?>" />
									<span data-label="<?php echo $LANG['COLLECTOR_LASTNAME']; ?>:"></span>
								</label>
							</div>
							<div class="input-text-container">
								<label for="collnum" class="input-text--outlined">
									<span class="skip-link"><?php echo $LANG['COLLECTOR_NUMBER_'] ?></span>
									<input type="text" id="collnum" size="31" name="collnum" value="" title="<?php echo htmlspecialchars($LANG['TITLE_TEXT_2'], HTML_SPECIAL_CHARS_FLAGS); ?>" data-chip="<?php echo $LANG['COLLECTOR_NUMBER'] ?>" />
									<span data-label="<?php echo $LANG['COLLECTOR_NUMBER']; ?>:"></span>
								</label>
							</div>
						</div>
					</div>
				</section>
			</div>
			
			<!-- Criteria panel -->
			<div id="criteria-panel" style="position: sticky; top: 0; height: 100vh">
				<button id="search-btn" onclick="simpleSearch()"><?php echo $LANG['SEARCH'] ?></button>
				<button id="reset-btn"><?php echo $LANG['RESET'] ?></button>
				<h2><?php echo $LANG['CRITERIA'] ?></h2>
				<div id="chips"></div>
			</div>
		</form>
	</div>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>
<script src="js/searchform.js" type="text/javascript"></script>
<script src="<?php echo $CLIENT_ROOT . '/collections/search/js/alerts.js?v=202107'; ?>" type="text/javascript"></script>
<script src="<?php echo $CLIENT_ROOT . '/js/jquery-ui.min.js'; ?>" type="text/javascript"></script>
<script src="<?php echo $CLIENT_ROOT . '/js/symb/api.taxonomy.taxasuggest.js'; ?>" type="text/javascript"></script>
<script src="<?php echo $CLIENT_ROOT . '/js/symb/collections.index.js?ver=20171215' ?>" type="text/javascript"></script>
<script>
	let alerts = [{
		'alertMsg': '<?php echo $LANG['ALERT_MSG_PREVIOUS_SEARCH_FORM'] ?> <a href="<?php echo $CLIENT_ROOT ?>/collections/harvestparams.php" alt="Traditional Sample Search Form"><?= $LANG['PREVIOUS_SAMPLE_SEARCH']; ?></a>.'
	}];
	handleAlerts(alerts, 3000);

	// resize the autocomplete window width to match the input width (from https://stackoverflow.com/questions/5643767/jquery-ui-autocomplete-width-not-set-correctly)
	jQuery.ui.autocomplete.prototype._resizeMenu = function () {
		var ul = this.menu.element;
		ul.outerWidth(this.element.outerWidth());
	}

	const collectionSource = <?php echo $collectionSource ?>;

	if(collectionSource){
		// go through all collections and set them all to unchecked
		const collectionCheckboxes = document.querySelectorAll('input[id^="coll"]');
		collectionCheckboxes.forEach(collection => {
			collection.checked = false;
		});

		//go through all collections and set the parent collections to unchecked
		const parentCollectionCheckboxes = document.querySelectorAll('input[id^="cat-"]');
		parentCollectionCheckboxes.forEach(collection => {
			collection.checked = false;
		});

		// set the one with collectionSource as checked
		const targetCheckbox = document.querySelectorAll('input[id^="coll-' + collectionSource + '"]');
		targetCheckbox.forEach(collection => {
			collection.checked = true;
		});
		//do the same for collections with slightly different format
		const targetCheckboxAlt = document.querySelectorAll('input[id^="collection-' + collectionSource + '"]');
		targetCheckboxAlt.forEach(collection => {
			collection.checked = true;
		});
		updateChip();
	}

</script>

</html>