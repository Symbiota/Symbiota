<?php
include_once('../config/symbini.php');
//include_once($SERVER_ROOT.'/classes/DynamicChecklistManager.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load('checklists/dynamicmap');

header('Content-Type: text/html; charset='.$CHARSET);

$tid = array_key_exists('tid',$_REQUEST)?$_REQUEST['tid']:0;
$taxa = array_key_exists('taxa',$_REQUEST)?$_REQUEST['taxa']:'';
$interface = array_key_exists('interface',$_REQUEST)&&$_REQUEST['interface']?$_REQUEST['interface']:'checklist';
$latCen = array_key_exists('lat',$_REQUEST)?$_REQUEST['lat']:'';
$longCen = array_key_exists('long',$_REQUEST)?$_REQUEST['long']:'';
$zoomInt = array_key_exists('zoom',$_REQUEST)?$_REQUEST['zoom']:'';

//Sanitation
if(!is_numeric($tid)) $tid = 0;
$taxa = htmlspecialchars($taxa, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
if($interface && $interface != 'key') $interface = 'checklist';

//$dynClManager = new DynamicChecklistManager();
if(!$latCen || !$longCen){
	$latCen = 41.0;
	$longCen = -95.0;
	$coorArr = explode(";",$MAPPING_BOUNDARIES);
	if($coorArr && count($coorArr) == 4){
		$latCen = ($coorArr[0] + $coorArr[2])/2;
		$longCen = ($coorArr[1] + $coorArr[3])/2;
	}
}
if(!$zoomInt){
	$zoomInt = 5;
	$coordRange = 50;
	if($coorArr && count($coorArr) == 4) $coordRange = ($coorArr[0] - $coorArr[2]);
	if($coordRange < 20) $zoomInt = 6;
	elseif($coordRange > 35 && $coordRange < 40) $zoomInt = 4;
	elseif($coordRange > 40) $zoomInt = 3;
}
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<title><?= $DEFAULT_TITLE . ' - ' . $LANG['CHECKLIST_GENERATOR'] ?></title>
	<link href="<?= $CSS_BASE_PATH ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT . '/includes/head.php');
	include_once($SERVER_ROOT . '/includes/leafletMap.php');
	?>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script src="//maps.googleapis.com/maps/api/js?<?= (!empty($GOOGLE_MAP_KEY) && $GOOGLE_MAP_KEY != 'DEV' ? 'key=' . $GOOGLE_MAP_KEY : '') ?>"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/symb/taxa.suggest.js?v=1" type="text/javascript"></script>

	<script type="text/javascript">
		var map;
		var currentMarker;
		var zoomLevel = 5;
		var submitCoord = false;

		//Map Global Vars from php
		let latCent;
		let lngCent;
		let mapZoom;

		$(document).ready(function() {

			//Auto-complete for taxon search field
			const taxaInput = document.querySelector("#taxa");
			if(taxaInput){
				taxaInput.addEventListener("focus", (event) => {
					taxaSuggest.config.clientRoot = "<?= $CLIENT_ROOT ?>";
					taxaSuggest.config.includeAuthor = <?= (empty($TAXON_AUTOCOMPLETE_INCLUDE_AUTHOR) ? 'false' : 'true') ?>;
					taxaSuggest.config.includeKingdom = <?= (empty($TAXON_AUTOCOMPLETE_INCLUDE_KINGDOM) ? 'false' : 'true') ?>;
					taxaSuggest.initiate("taxa", function(result){
						if(result.item){
							$("#tid").val(result.item.id);
						}
						else{
							$("#tid").val("");
							if(this.value != ""){
								alert("<?= $LANG['SELECT_FROM_LIST'] ?>");
							}
						}
					});
				});
			}

		});

		function getRadius() {
			const radius = document.getElementById('radius').value;
			const radiusUnits = document.getElementById('radiusunits').value;

			if(radiusUnits === "km") return radius * 1000;
			const MILES_TO_METERS = 1609.344;

			return radius * MILES_TO_METERS;
		}

		function onRadiusChange(eventFunction) {
			let radiusInput = document.getElementById('radius');
			if(radiusInput) {
				radiusInput.addEventListener('change', eventFunction);
				//Need because input clears on focus
				radiusInput.addEventListener('focus', eventFunction);
			}

			let radiusUnits = document.getElementById('radiusunits');
			if(radiusUnits) {
				radiusUnits.addEventListener('change', eventFunction);
			}
		}

		function leafletInit() {

			let dmOptions = {
				zoom: mapZoom,
				center: [latCent, lngCent],
			};

			map = new LeafletMap('map_canvas',
				dmOptions,
				JSON.parse(`<?= json_encode($GEO_JSON_LAYERS ?? []) ?>`)
			)

			let markerGroup = new L.layerGroup().addTo(map.mapLayer);
			let latlng;

			function drawMarker(center) {
				//Clear Layers In Between Clicks
				if(markerGroup) markerGroup.clearLayers();

				latlng = center;

				//Render Marker
				L.marker(center).addTo(markerGroup);

				//Render Radius if Input
				let radius = getRadius();
				if(radius > 0) {
					let circle = L.circle(center, radius)
					.setStyle(map.DEFAULT_SHAPE_OPTIONS)
					.addTo(markerGroup);
				}
			}

			map.mapLayer.on('click', e => {
				drawMarker(e.latlng);
				updateMarkerPosition(e.latlng.lat, e.latlng.lng);
			});

			onRadiusChange(e => {
				if(latlng) drawMarker(latlng);
			});
		}

		function googleInit() {
			var dmLatLng = new google.maps.LatLng(latCent, lngCent);
			var dmOptions = {
				zoom: mapZoom,
				center: dmLatLng,
				mapTypeId: google.maps.MapTypeId.TERRAIN
			};

			map = new google.maps.Map(document.getElementById("map_canvas"), dmOptions);

			let marker;
			let circle;
			let latlng;

			google.maps.event.addListener(map, 'click', function(event) {
				if(marker) marker.setMap();
				if(circle) circle.setMap();
				latlng = event.latLng;

				marker = new google.maps.Marker({
					position: event.latLng,
					map: map
				});

				let radius = getRadius();
				if(radius > 0) {
					circle = new google.maps.Circle({
						center: event.latLng,
						radius: radius,
						clickable: false,
						map: map
					});
				}

				updateMarkerPosition(event.latLng.lat(), event.latLng.lng());
			});

			onRadiusChange(e => {
				if(circle) circle.setMap();
				if(!latlng) return;

				const new_radius = getRadius();
				if(new_radius > 0) {
					circle = new google.maps.Circle({
						center: latlng,
						clickable: false,
						radius: new_radius,
						map: map
					});
				}
			});
		}

		function initialize(){
			try {
				const data = document.getElementById('service-container');
				latCent = parseFloat(data.getAttribute('data-latCen'))
				lngCent = parseFloat(data.getAttribute('data-lngCen'))
				mapZoom = parseInt(data.getAttribute('data-mapZoom'))
			} catch {
				alert("Failed to load map centering");
			}

			<?php
			if(empty($GOOGLE_MAP_KEY)) {
				?>
				leafletInit();
				<?php
			} else {
				?>
				googleInit();
				<?php
			}
			?>
		}

		function updateMarkerPosition(lat, lng) {
			lat = lat.toFixed(5);
			lng = lng.toFixed(5);

			document.getElementById("latbox").value = lat;
			document.getElementById("lngbox").value = lng;
			document.getElementById("latlngspan").innerHTML = lat + ", " + lng;
			document.mapForm.buildchecklistbutton.disabled = false;
			submitCoord = true;
		}

		function checkSubmitForm(f){
			if(!submitCoord){
				alert("<?= $LANG['CLICK_MAP'] ?>");
				return false;
			}
			if(f.taxa.value != "" && f.tid.value == ""){
				alert("<?= $LANG['SELECT_FROM_LIST'] ?>");
				return false;
			}
			return true;
		}

		function showMoreDetails(){
			document.getElementById("moredetails").style.display = 'none';
			document.getElementById("moreinfo").style.display = "inline";
			document.getElementById("lessdetails").style.display = "inline";
		}

		function showLessDetails(){
			document.getElementById("moredetails").style.display = 'inline';
			document.getElementById("moreinfo").style.display = "none";
			document.getElementById("lessdetails").style.display = "none";
		}

	</script>
</head>
<body style="background-color:#ffffff;" onload="initialize()">
	<div
		id="service-container"
		class="service-container"
		data-latCen="<?= Sanitize::float($latCen) ?>"
		data-lngCen="<?= Sanitize::float($longCen) ?>"
		data-mapZoom="<?= Sanitize::int($zoomInt) ?>"
	></div>
	<?php
		$displayLeftMenu = false;
		include($SERVER_ROOT . '/includes/header.php');
		?>
		<div class='navpath'>
			<a href='../index.php'><?= $LANG['HOME'] ?></a> &gt;
			<b><?= $LANG['DYNAMIC_MAP'] ?></b>
		</div>
		<div class="flex-form" id='innertext'>
			<h1 class="page-heading screen-reader-only"><?= $LANG['DYNAMIC_MAP'] ?></h1>
			<div style="margin-left: 2rem; margin-bottom: 1rem;">
				<?= $LANG['CAPTURE_COORDS'] ?>
				<span id="moredetails" style="font-size:80%;" >
					<a href="#" onclick="showMoreDetails()"><?= $LANG['MORE_DETAILS'] ?></a>
				</span>
				<span id="moreinfo" style="display:none;">
					<?= $LANG['RADIUS_DESCRIPTION'] ?>
				</span>
				<span id="lessdetails" style="font-size:80%;display:none;">
					<a href="#" onclick="showLessDetails()"><?= $LANG['LESS_DETAILS'] ?></a>
				</span>
			</div>
			<div>
				<form name="mapForm" action="dynamicchecklist.php" method="post" onsubmit="return checkSubmitForm(this);" class="flex-form">
					<div>
						<span style="margin-right: 20px;">
							<label for="taxa"><?= $LANG['TAXON_FILTER'] ?>:</label>
							<input id="taxa" name="taxa" type="text" value="<?= $taxa ?>" style="width: 350px" />
							<input id="tid" name="tid" type="hidden" value="<?= $tid ?>" />
						</span>
						<span>
							<label for="radius"><?= $LANG['RADIUS'] ?>:</label>
							<input name="radius" id="radius" value="(optional)" type="text" style="width:140px;" onfocus="this.value = ''" />
							<select id="radiusunits" name="radiusunits">
								<option value="km"><?= $LANG['KM'] ?></option>
								<option value="mi"><?= $LANG['MILES'] ?></option>
							</select>
						</span>
					</div>
					<div>
						<span id="button-span" style="margin-right: 15px">
							<button type="submit" name="buildchecklistbutton" value="buildChecklist" style="display:inline" disabled ><?= $LANG['BUILD_CHECKLIST'] ?></button>
							<input type="hidden" name="interface" value="<?= $interface ?>" />
							<input type="hidden" id="latbox" name="lat" value="" />
							<input type="hidden" id="lngbox" name="lng" value="" />
						</span>
						<span id="point-span">
							<b><?= $LANG['POINT'] ?>:</b>
							<span id="latlngspan"> &lt; <?= $LANG['CLICK_MAP'] ?> &gt; </span>
						</span>
					</div>
				</form>
			</div>
			<div id='map_canvas' style='width:100%; height:650px; clear:both;'></div>
		</div>
	<?php
	include_once($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
