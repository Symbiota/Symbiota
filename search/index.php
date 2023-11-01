<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
include_once('../config/symbini.php');
include_once('../content/lang/index.' . $LANG_TAG . '.php');
include_once($SERVER_ROOT . '/classes/CollectionMetadata.php');
include_once($SERVER_ROOT . '/classes/DatasetsMetadata.php');
header("Content-Type: text/html; charset=" . $CHARSET);

$collData = new CollectionMetadata();
$siteData = new DatasetsMetadata();
?>
<html>

<head>
	<title><?php echo $DEFAULT_TITLE; ?> Sample Search</title>
	<?php
	$activateJQuery = true;
	if (file_exists($SERVER_ROOT . '/includes/head.php')) {
		include_once($SERVER_ROOT . '/includes/head.php');
	} else {
		echo '<link href="' . $CLIENT_ROOT . '/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
		echo '<link href="' . $CLIENT_ROOT . '/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
	}
	echo '<link href="' . $CLIENT_ROOT . '/search/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
	echo '<link href="' . $CLIENT_ROOT . '/search/css/app.css" type="text/css" rel="stylesheet" />';
	
	echo '<link href="' . $CLIENT_ROOT . '/search/css/tables.css" type="text/css" rel="stylesheet" />';
	?>
	<script src="js/jquery-3.2.1.min.js" type="text/javascript"></script>
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
		<h1>Sample Search</h1>
		<div id="error-msgs" class="errors"></div>
		<form id="params-form">
			<!-- Criteria forms -->
			
			<div class="accordions">
				<!-- Taxonomy -->
				<section>
					<!-- Accordion selector -->
					<input type="checkbox" id="taxonomy" class="accordion-selector" checked=true />

					<!-- Accordion header -->
					<label for="taxonomy" class="accordion-header">Taxonomy</label>

					<!-- Taxonomy -->
					<div id="search-form-taxonomy" class="content">
						<div id="taxa-text" class="input-text-container">
							<label for="taxa" class="input-text--outlined">
								<input type="text" name="taxa" id="taxa" data-chip="Taxa">
								<span data-label="Taxon"></span></label>
							<span class="assistive-text">Type at least 4 characters for quick suggestions. Separate multiple with commas.</span>
						</div>
						<div class="select-container">
							<select name="taxontype">
								<option value="1">Any name</option>
								<option value="2">Scientific name</option>
								<option value="3">Family</option>
								<option value="4">Taxonomic group</option>
								<option value="5">Common name</option>
							</select>
							<span class="assistive-text">Taxon type.</span>
						</div>
						<div>
							<input type="checkbox" name="usethes" id="usethes" data-chip="Include Synonyms" value="1" checked>
							<span class="ml-1">Include Synonyms</span>
						</div>
					</div>
				</section>

				<!-- Colections -->
				<section>
					<!-- Accordion selector -->
					<input type="checkbox" id="collections" class="accordion-selector" checked=true />
					<!-- Accordion header -->
					<label for="collections" class="accordion-header">Collections</label>
					<!-- Accordion content -->
					<div class="content">
						<div id="search-form-colls">
							<!-- Open NEON Collections modal -->
							<div>
								<input id="all-neon-colls-quick" data-chip="All Biorepo Collections" class="all-selector" type="checkbox" checked="true" data-form-id="biorepo-collections-list">
								<span id="neon-modal-open" class="material-icons expansion-icon">add_box</span>
								<span>All NEON Biorepository Collections</span>
							</div>
							<!-- External Collections -->
							<div>
								<ul id="neonext-collections-list">
									<li>
										<input id="all-neon-ext" data-chip="All Add NEON Colls" type="checkbox" class="all-selector" data-form-id='neonext-collections-list'>
										<span class="material-icons expansion-icon">add_box</span>
										<span>All Additional NEON Collections</span>
										<?php if ($collsArr = $collData->getCollMetaByCat('Additional NEON Collections')) {
											echo '<ul class="collapsed">';
											foreach ($collsArr as $result) {
												echo "<li><input type='checkbox' name='db' value='{$result["collid"]}' class='child' data-ccode='{$result["institutioncode"]} {$result["collectioncode"]}'><span class='ml-1'><a href='../../collections/misc/collprofiles.php?collid={$result["collid"]}' target='_blank' rel='noopener noreferrer'>{$result["collectionname"]} ({$result["institutioncode"]}  {$result["collectioncode"]})</span></a></li>";
											}
											echo '</ul>';
										}; ?>
									</li>
								</ul>
								<ul id="ext-collections-list">
									<li>
										<input id="all-ext" data-chip="All Ext Colls" type="checkbox" class="all-selector" data-form-id='ext-collections-list'>
										<span class="material-icons expansion-icon">add_box</span>
										<span>All Other Collections from NEON sites</span>
										<?php if ($collsArr = $collData->getCollMetaByCat('Other Collections from NEON sites')) {
											echo '<ul class="collapsed">';
											foreach ($collsArr as $result) {
												echo "<li><input type='checkbox' name='db' value='{$result["collid"]}' class='child' data-ccode='{$result["institutioncode"]}'><span class='ml-1'><a href='../../collections/misc/collprofiles.php?collid={$result["collid"]}' target='_blank' rel='noopener noreferrer'>{$result["collectionname"]} ({$result["institutioncode"]})</span></a></li>";
											}
											echo '</ul>';
										}; ?>
									</li>
								</ul>
							</div>
						</div>
						<p>Visit the <a href="<?php echo $CLIENT_ROOT . '/collections/misc/collprofiles.php'; ?>" target="_blank" rel="noopener noreferrer">Collections Information Page</a> for a full list of collections hosted by this portal.</p>
					</div>
					<!-- NEON Biorepository Collections Modal -->
					<div class="modal" id="biorepo-collections-list">
						<div class="modal-content">
							<button id="neon-modal-close" class="btn" style="width:auto !important">Accept and close</button>
							<div id="colls-modal">
								<div>
									<h3>Activate a single criterion to filter collections</h3>
									<label class="tab tab-active"><input type="radio" name="collChoice" value="taxonomic-cat" checked="true"> Taxonomic Group</label>
									<label class="tab"><input type="radio" name="collChoice" value="neon-theme"> NEON Theme</label>
									<label class="tab"><input type="radio" name="collChoice" value="sample-type"> Sample Type</label>
								</div>
							</div>
						</div>
					</div>
				</section>
				
				<!-- Sample Properties -->
				<section>
					<!-- Accordion selector -->
					<input type="checkbox" id="sample" class="accordion-selector" checked=true />
					<!-- Accordion header -->
					<label for="sample" class="accordion-header">Sample Properties</label>
					<!-- Accordion content -->
					<div class="content">
						<div id="search-form-sample">
							<div>
								<div>
									<input type="checkbox" name="includeothercatnum" id="includeothercatnum" value="1" data-chip="Include other IDs" checked>
									<label for="includeothercatnum">Include other catalog numbers and GUIds</label>
								</div>
								<div class="input-text-container">
									<label for="" class="input-text--outlined">
										<input type="text" name="catnum" data-chip="Catalog Number">
										<span data-label="Catalog Number"></span>
									</label>
									<span class="assistive-text">Separate multiple with commas.</span>
								</div>
							</div>
							<div>
								<div>
									<input type="checkbox" name="hasimages" value=1 data-chip="Only with images">
									<label for="hasimages">Limit to specimens with images</label>
								</div>
								<div>
									<input type="checkbox" name="hasgenetic" value=1 data-chip="Only with genetic">
									<label for="hasgenetic">Limit to specimens with genetic data</label>
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
					<label for="locality" class="accordion-header">Locality</label>
					<!-- Accordion content -->
					<div class="content">
						<div id="search-form-locality">
							<div>
								<div>
									<div class="input-text-container">
										<label for="state" class="input-text--outlined">
											<input type="text" name="state" id="state" data-chip="State">
											<span data-label="State"></span>
										</label>
										<span class="assistive-text">Separate multiple with commas.</span>
									</div>
									<div class="input-text-container">
										<label for="county" class="input-text--outlined">
											<input type="text" name="county" id="county" data-chip="County">
											<span data-label="County"></span>
										</label>
										<span class="assistive-text">Separate multiple with commas.</span>
									</div>
									<div class="input-text-container">
										<label for="local" class="input-text--outlined">
											<input type="text" name="local" id="local" data-chip="Locality">
											<span data-label="Locality"></span>
										</label>
										<span class="assistive-text" style="line-height:1.7em">Separate multiple with commas. Accepts NEON Domain and/or Site names and codes.</span>
									</div>
								</div>
								<div class="grid grid--half">
									<div class="input-text-container">
										<label for="elevlow" class="input-text--outlined">
											<input type="number" step="any" name="elevlow" id="elevlow" data-chip="Min Elevation">
											<span data-label="Minimum Elevation"></span>
										</label>
										<span class="assistive-text">Number in meters.</span>
									</div>
									<div class="input-text-container">
										<label for="elevhigh" class="input-text--outlined">
											<input type="number" step="any" name="elevhigh" id="elevhigh" data-chip="Max Elevation">
											<span data-label="Maximum Elevation"></span>
										</label>
										<span class="assistive-text">Number in meters.</span>
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
					<label for="lat-long" class="accordion-header">Latitude & Longitude</label>
					<!-- Accordion content -->
					<div class="content">
						<div id="search-form-latlong">
							<div id="bounding-box-form">
								<h3>Bounding Box</h3>
								<button onclick="openCoordAid('rectangle');return false;">Select in map</button>
								<div class="input-text-container">
									<label for="upperlat" class="input-text--outlined">
										<input type="number" step="any" min="-90" max="90" id="upperlat" name="upperlat" data-chip="Upper Lat">
										<select class="mt-1" id="upperlat_NS" name="upperlat_NS">
											<option value="">Select N/S</option>
											<option id="ulN" value="N">N</option>
											<option id="ulS" value="S">S</option>
										</select>
										<span data-label="Northern Latitude"></span>
									</label>
									<span class="assistive-text">Values between -90 and 90.</span>
								</div>
								<div class="input-text-container">
									<label for="bottomlat" class="input-text--outlined">
										<input type="number" step="any" min="-90" max="90" id="bottomlat" name="bottomlat" data-chip="Bottom Lat">
										<select class="mt-1" id="bottomlat_NS" name="bottomlat_NS">
											<option value="">Select N/S</option>
											<option id="blN" value="N">N</option>
											<option id="blS" value="S">S</option>
										</select>
										<span data-label="Southern Latitude"></span>
									</label>
									<span class="assistive-text">Values between -90 and 90.</span>
								</div>
								<div class="input-text-container">
									<label for="leftlong" class="input-text--outlined">
										<input type="number" step="any" min="-180" max="180" id="leftlong" name="leftlong" data-chip="Left Long">
										<select class="mt-1" id="leftlong_EW" name="leftlong_EW">
											<option value="">Select W/E</option>
											<option id="llW" value="W">W</option>
											<option id="llE" value="E">E</option>
										</select>
										<span data-label="Western Longitude"></span>
									</label>
									<span class="assistive-text">Values between -180 and 180.</span>
								</div>
								<div class="input-text-container">
									<label for="rightlong" class="input-text--outlined">
										<input type="number" step="any" min="-180" max="180" id="rightlong" name="rightlong" data-chip="Right Long">
										<select class="mt-1" id="rightlong_EW" name="rightlong_EW">
											<option value="">Select W/E</option>
											<option id="rlW" value="W">W</option>
											<option id="rlE" value="E">E</option>
										</select>
										<span data-label="Eastern Longitude"></span>
									</label>
									<span class="assistive-text">Values between -180 and 180.</span>
								</div>
							</div>
							<div id="polygon-form">
								<h3>Polygon (WKT footprint)</h3>
								<button onclick="openCoordAid('polygon');return false;">Select in map</button>
								<div class="text-area-container">
									<label for="footprintwkt" class="text-area--outlined">
										<textarea id="footprintwkt" name="footprintwkt" wrap="off" cols="30%" rows="5"></textarea>
										<span data-label="Polygon"></span>
									</label>
									<span class="assistive-text">Select in map with button or paste values.</span>
								</div>
							</div>
							<div id="point-radius-form">
								<h3>Point-Radius</h3>
								<button onclick="openCoordAid('circle');return false;">Select in map</button>
								<div class="input-text-container">
									<label for="pointlat" class="input-text--outlined">
										<input type="number" step="any" min="-90" max="90" id="pointlat" name="pointlat" data-chip="Point Lat">
										<select class="mt-1" id="pointlat_NS" name="pointlat_NS">
											<option value="">Select N/S</option>
											<option id="N" value="N">N</option>
											<option id="S" value="S">S</option>
										</select>
										<span data-label="Latitude"></span>
									</label>
									<span class="assistive-text">Values between -90 and 90.</span>
								</div>
								<div class="input-text-container">
									<label for="pointlong" class="input-text--outlined">
										<input type="number" step="any" min="-180" max="180" id="pointlong" name="pointlong" data-chip="Point Long">
										<select class="mt-1" id="pointlong_EW" name="pointlong_EW">
											<option value="">Select W/E</option>
											<option id="W" value="W">W</option>
											<option id="E" value="E">E</option>
										</select>
										<span data-label="Longitude"></span>
									</label>
									<span class="assistive-text">Values between -180 and 180.</span>
								</div>
								<div class="input-text-container">
									<label for="radius" class="input-text--outlined">
										<input type="number" min="0" step="any" id="radius" name="radius" data-chip="Radius">
										<select class="mt-1" id="radiusunits" name="radiusunits">
											<option value="">Select Unit</option>
											<option value="km">Kilometers</option>
											<option value="mi">Miles</option>
										</select>
										<span data-label="Radius"></span>
									</label>
									<span class="assistive-text">Any positive values.</span>
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
					<label for="coll-event" class="accordion-header">Collecting Event</label>
					<!-- Accordion content -->
					<div class="content">
						<div id="search-form-coll-event">
							<div class="input-text-container">
								<label for="eventdate1" class="input-text--outlined">
									<input type="text" name="eventdate1" data-chip="Event Date Start">
									<span data-label="Collection Start Date"></span>
								</label>
								<span class="assistive-text">Single date or start date of range (ex: YYYY-MM-DD or similar format).</span>
							</div>
							<div class="input-text-container">
								<label for="eventdate2" class="input-text--outlined">
									<input type="text" name="eventdate2" data-chip="Event Date End">
									<span data-label="Collection End Date"></span>
								</label>
								<span class="assistive-text">Single date or end date of range (ex: YYYY-MM-DD or similar format).</span>
							</div>
						</div>
					</div>
				</section>
			</div>
			
			<!-- Criteria panel -->
			<div id="criteria-panel" style="position: sticky; top: 0; height: 100vh">
				<button id="search-btn">Search</button>
				<button id="reset-btn">Reset</button>
				<h2>Criteria</h2>
				<div id="chips"></div>
			</div>
		</form>
	</div>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>
<script src="js/searchform.js" type="text/javascript"></script>
<script src="<?php echo $CLIENT_ROOT . '/search/js/alerts.js?v=202107'; ?>" type="text/javascript"></script>
<script src="<?php echo $CLIENT_ROOT . '/js/jquery-ui-1.12.1/jquery-ui.min.js'; ?>" type="text/javascript"></script>
<script src="<?php echo $CLIENT_ROOT . '/js/symb/api.taxonomy.taxasuggest.js'; ?>" type="text/javascript"></script>
<script>
	let alerts = [{
		'alertMsg': 'Looking for the previous search form? You can still use it here: <a href="<?php echo $CLIENT_ROOT ?>/collections/harvestparams.php" alt="Traditional Sample Search Form">previous Sample Search Page</a>.'
	}];
	handleAlerts(alerts, 3000);
</script>

</html>