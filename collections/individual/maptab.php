<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load('collections/individual/index');
header('Content-Type: text/html; charset=' . $CHARSET);

$decLat = 0;
$decLng = 0;
$coordError = 0;

if(!empty($_REQUEST['declat']) && !empty($_REQUEST['declng'])){
	$decLat = Sanitize::float($_REQUEST['declat']);
	$decLng = Sanitize::float($_REQUEST['declng']);
	if(!empty($_REQUEST['coorderror'])) $coordError = Sanitize::float($_REQUEST['coorderror']);
}
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
	<head>
		<title><?= $DEFAULT_TITLE . ' - ' . $LANG['OCCURRENCE_PROFILE'] ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET; ?>">
		<link href="<?= $CSS_BASE_PATH ?>/jquery-ui.css" type="text/css" rel="stylesheet">
		<?php
		include_once($SERVER_ROOT.'/includes/head.php');
		include_once($SERVER_ROOT.'/includes/leafletMap.php');
		include_once($SERVER_ROOT.'/includes/googleanalytics.php');
		include_once($SERVER_ROOT.'/includes/googleMap.php');
		?>
		<link href="<?= $CSS_BASE_PATH ?>/symbiota/collections/individual/index.css?ver=1" type="text/css" rel="stylesheet" >
		<link href="<?= $CSS_BASE_PATH ?>/symbiota/collections/individual/popup.css" type="text/css" rel="stylesheet" >
		<script src="<?= $CLIENT_ROOT; ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
		<script src="<?= $CLIENT_ROOT; ?>/js/jquery-ui.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			var map;
			var mapInit = false;
			const coordError = <?= $coordError ?>;

			initializeMap();

			function initializeMap(){
				<?php if(empty($GOOGLE_MAP_KEY)): ?>
					leafletInit();
				<?php else: ?>
					googleInit();
				<?php endif ?>
			}

			function googleInit() {
				var mLatLng = new google.maps.LatLng(<?= $decLat . ',' . $decLng ?>);
				var dmOptions = {
					zoom: 8,
					center: mLatLng,
					marker: mLatLng,
					mapTypeId: google.maps.MapTypeId.TERRAIN,
					scaleControl: true
				};
				map = new google.maps.Map(document.getElementById("map_canvas"), dmOptions);
				//Add marker
				var marker = new google.maps.Marker({
					position: mLatLng,
					map: map
				});

				if(coordError > 0) {
					new google.maps.Circle({
					  center: mLatLng,
					  radius: coordError,
					  map: map
					})
				}
			}

			function leafletInit() {
				let mLatLng = [<?= $decLat . ',' . $decLng ?>];

				map = new LeafletMap("map_canvas", {
					center: mLatLng,
					zoom: 8,
				},
					JSON.parse(`<?= json_encode($GEO_JSON_LAYERS ?? []) ?>`)
				);

				if(coordError > 0) {
					map.enableDrawing({...map.DEFAULT_DRAW_OPTIONS, control: false})
					map.drawShape({type: "circle", radius: coordError, latlng: mLatLng})
				}
				const marker = L.marker(mLatLng).addTo(map.mapLayer);
				map.mapLayer.setZoom(8)
			}
		</script>
	</head>
	<body>
		<div id="maptab">
			<div id='map_canvas' style='width:100%;height:600px;'></div>
		</div>
	</body>
</html>
