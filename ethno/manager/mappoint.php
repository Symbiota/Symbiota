<?php
include_once('../../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);

$latCenter = 0;
$lngCenter = 0;
if(isset($MAPPING_BOUNDARIES) && $MAPPING_BOUNDARIES){
	$boundaryArr = explode(";",$MAPPING_BOUNDARIES);
	$latCenter = ($boundaryArr[0]>$boundaryArr[2]?((($boundaryArr[0]-$boundaryArr[2])/2)+$boundaryArr[2]):((($boundaryArr[2]-$boundaryArr[0])/2)+$boundaryArr[0]));
	$lngCenter = ($boundaryArr[1]>$boundaryArr[3]?((($boundaryArr[1]-$boundaryArr[3])/2)+$boundaryArr[3]):((($boundaryArr[3]-$boundaryArr[1])/2)+$boundaryArr[1]));
}
else{
	$latCenter = 42.877742;
	$lngCenter = -97.380979;
}
?>
<html>
	<head>
		<title><?php echo $DEFAULT_TITLE; ?></title>
	</head>
	<body style="background-color:#ffffff;">
	<script src="//maps.googleapis.com/maps/api/js?<?php echo (isset($GOOGLE_MAP_KEY) && $GOOGLE_MAP_KEY?'key='.$GOOGLE_MAP_KEY:''); ?>"></script>
	<script type="text/javascript">
		var map;
		var marker;
      	
        function initialize(){

			var dmOptions = {
				zoom: 5,
				center: new google.maps.LatLng(<?php echo $latCenter.','.$lngCenter; ?>),
				mapTypeId: google.maps.MapTypeId.TERRAIN,
				scaleControl: true
			};

	    	map = new google.maps.Map(document.getElementById("map"), dmOptions);

			google.maps.event.addListener(map, 'click', function(event) {
				if(marker) marker.setMap(null);
				marker = new google.maps.Marker({
					position: event.latLng,
					map: map
				});
				document.getElementById("latbox").value = event.latLng.lat().toFixed(5);
				document.getElementById("lonbox").value = event.latLng.lng().toFixed(5);
			});
        }

        function updateParentForm() {
            opener.document.getElementById("decimalLatitude").value = document.getElementById("latbox").value;
            opener.document.getElementById("decimalLongitude").value = document.getElementById("lonbox").value;
            self.close();
            return false;
        }

		google.maps.event.addDomListener(window, 'load', initialize);

	</script>
    <div>Click once to capture coordinates. Click on the Submit Coordinate button to transfer Coordinates.</div>
    <div id='map' style='width: 100%; height: 90%;'></div>
	<form id="mapForm" onsubmit="return updateParentForm();">
		<div>
			Latitude: <input type="text" id="latbox" size="13" name="lat" value="" />&nbsp;&nbsp;&nbsp;
			Longitude: <input type="text" id="lonbox" size="13" name="lon" value="" /> &nbsp;&nbsp;&nbsp;
			<input type="submit" name="addcoords" value="Submit" />
		</div>
	</form>
  </body>
</html>
