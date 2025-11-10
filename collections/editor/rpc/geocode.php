<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/GeographicThesaurus.php');
header("Content-Type: application/json; charset=".$CHARSET);
include_once($SERVER_ROOT . '/rpc/crossPortalHeaders.php');

$lat = $_REQUEST['lat'] ?? false;
$lng = $_REQUEST['lng'] ?? false;

if(!$lat || !$lng) {
	return json_encode([]);
}

$geoThesaurus = new GeographicThesaurus();
$matches = $geoThesaurus->geocode($lng, $lat);

$geo_data = [];

foreach (['country', 'stateprovince', 'county', 'municipality'] as $value) {
	if(isset($_REQUEST[$value]) && $_REQUEST[$value]) {
		$geo_data[$value] = $_REQUEST[$value];
	}
}

$exists = $geoThesaurus->placeExists($geo_data);

echo json_encode(['is_registered' => $exists, 'matches' => $matches ]);
