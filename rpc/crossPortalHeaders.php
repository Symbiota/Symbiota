<?php
include_once('../config/symbini.php');

header('Content-Type: application/json; charset='.$CHARSET);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');


if(!isset($EXTERNAL_PORTAL_HOSTS)) {
	$EXTERNAL_PORTAL_HOSTS = [];

	$conn = MySQLiConnectionFactory::getCon('readonly');
	$portals = $conn->query(<<<sql
		SELECT portalName, urlRoot from portalindex p;
	sql)->fetch_all(MYSQLI_ASSOC);

	foreach($portals as $portal) {
		$origin_parts = preg_split('/[\/]+/', $portal['urlRoot']);

		if(count($origin_parts) >= 2) {
			$portal['origin'] = $origin_parts[1];
			array_push($EXTERNAL_PORTAL_HOSTS, $portal);
		}
	}

	array_push($EXTERNAL_PORTAL_HOSTS, [
		'origin' => 'moz-extension://262fea4d-6e09-4206-bb68-04f616ffac83'
	]);
	//echo json_encode(['origin' => $_SERVER['HTTP_ORIGIN']]);
}

if (isset($_SERVER['HTTP_ORIGIN']) && array_search($_SERVER['HTTP_ORIGIN'], array_column($EXTERNAL_PORTAL_HOSTS, 'origin'))) {
	$origin = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_ORIGIN'];

	header('Access-Control-Allow-Origin: ' . $origin);
	header('Access-Control-Allow-Headers: ' . $origin);
	json_encode(['msg' => 'has header']);
} elseif(isset($_SERVER['HTTP_ORIGIN'])) {
	echo json_encode($_SERVER);
} else {
  echo json_encode(['msg' => 'has header']);
}
?>

