<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/content/lang/collections/tools/mapaids.'.$LANG_TAG.'.php');
include_once($SERVER_ROOT.'/classes/ChecklistAdmin.php');
header("Content-Type: text/html; charset=".$CHARSET);

$clid = array_key_exists("clid",$_REQUEST)?$_REQUEST["clid"]:0;
$formSubmit = array_key_exists("formsubmit",$_POST)?$_POST["formsubmit"]:0;
$latDef = array_key_exists("latdef",$_REQUEST)?$_REQUEST["latdef"]:'';
$lngDef = array_key_exists("lngdef",$_REQUEST)?$_REQUEST["lngdef"]:'';
$zoom = array_key_exists("zoom",$_REQUEST)&&$_REQUEST["zoom"]?$_REQUEST["zoom"]:5;
$mapMode = array_key_exists("mapmode",$_REQUEST)?$_REQUEST["mapmode"]:'';

if($mapMode == 'polygon'){
	$mapMode = 'google.maps.drawing.OverlayType.POLYGON';
}
elseif($mapMode == 'rectangle'){
	$mapMode = 'google.maps.drawing.OverlayType.RECTANGLE';
}
elseif($mapMode == 'circle'){
	$mapMode = 'google.maps.drawing.OverlayType.CIRCLE';
}
else{
	$mapMode = '';
}

$clManager = new ChecklistAdmin();
$clManager->setClid($clid);

if($formSubmit){
	if($formSubmit == 'save'){
		$clManager->savePolygon($_POST['footprintwkt']);
		$formSubmit = "exit";
	}
}

if($latDef == 0 && $lngDef == 0){
	$latDef = '';
	$lngDef = '';
}

$latCenter = 0; $lngCenter = 0;
if(is_numeric($latDef) && is_numeric($lngDef)){
	$latCenter = $latDef;
	$lngCenter = $lngDef;
	$zoom = 12;
}
elseif($MAPPING_BOUNDARIES){
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
		<title><?php echo $DEFAULT_TITLE; ?> - Coordinate Aid</title>
		<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
		<script src="//maps.googleapis.com/maps/api/js?v=3.exp&libraries=drawing<?php echo (isset($GOOGLE_MAP_KEY) && $GOOGLE_MAP_KEY?'&key='.$GOOGLE_MAP_KEY:''); ?>"></script>
		<script src="<?php echo $CLIENT_ROOT; ?>/js/symb/wktpolygontools.js" type="text/javascript"></script>
		<script type="text/javascript">
			var map;
			var activeShape = null;

			function initialize(){
				var dmOptions = {
					zoom: <?php echo $zoom; ?>,
					center: new google.maps.LatLng(<?php echo $latCenter.','.$lngCenter; ?>),
					mapTypeId: google.maps.MapTypeId.TERRAIN,
					scaleControl: true
				};
				map = new google.maps.Map(document.getElementById("map_canvas"), dmOptions);

				var drawingManager = new google.maps.drawing.DrawingManager({
					<?php if($mapMode) echo 'drawingMode: '.$mapMode.','; ?>
					drawingControl: true,
					drawingControlOptions: {
						position: google.maps.ControlPosition.TOP_CENTER,
						drawingModes: [
							google.maps.drawing.OverlayType.POLYGON,
							google.maps.drawing.OverlayType.RECTANGLE,
							google.maps.drawing.OverlayType.CIRCLE
						]
					},
					markerOptions: {
						draggable: true
					},
					polygonOptions: {
						strokeWeight: 0,
						fillOpacity: 0.45,
						editable: true,
						draggable: true
					}
				});

				drawingManager.setMap(map);

				google.maps.event.addListener(drawingManager, 'click', function(e) {
					alert("control clicked on");
					alert(drawingManager.getDrawingMode());
				});

				drawingManager.addListener('click', function(e) {
					alert("control clicked on 2");
					alert(drawingManager.getDrawingMode());
				});

				google.maps.event.addListener(drawingManager, 'at_insert', function(e) {
					alert("at insert");
					alert(drawingManager.getDrawingMode());
				});

				drawingManager.addListener('at_insert', function(e) {
					alert("at insert2");
					alert(drawingManager.getDrawingMode());
				});

				google.maps.event.addListener(drawingManager, 'overlaycomplete', function(e) {
					if (e.type != google.maps.drawing.OverlayType.MARKER) {
						// Switch back to non-drawing mode after drawing a shape.
						drawingManager.setDrawingMode(null);

						var shapeType = e.type;
						// Add an event listener that selects the newly-drawn shape when the user
						// mouses down on it.
						var newShape = e.overlay;
						newShape.type = e.type;
						google.maps.event.addListener(newShape, 'click', function() {
							setSelection(newShape);
						});
						google.maps.event.addListener(newShape, 'dragend', function() {
							setSelection(newShape);
						});
						if(shapeType == 'circle'){
							google.maps.event.addListener(newShape, 'radius_changed', function() {
								setSelection(newShape);
							});
							google.maps.event.addListener(newShape, 'center_changed', function() {
								setSelection(newShape);
							});
						}
						else if(shapeType == 'rectangle'){
							google.maps.event.addListener(newShape, 'bounds_changed', function() {
								setSelection(newShape);
							});
						}
						else if(shapeType == 'polygon'){
							google.maps.event.addListener(newShape.getPath(), 'insert_at', function() {
								setSelection(newShape);
							});
							google.maps.event.addListener(newShape.getPath(), 'remove_at', function() {
								setSelection(newShape);
							});
							google.maps.event.addListener(newShape.getPath(), 'set_at', function() {
								setSelection(newShape);
							});
						}
						setSelection(newShape);
					}
				});
				//Set shape based on values within form
				var bounds;
				if(drawingManager.getDrawingMode() == "circle"){
					if(opener.document.getElementById("pointlat").value != '' && opener.document.getElementById("pointlong").value != '' && opener.document.getElementById("radius").value != ''){
						drawingManager.setDrawingMode(null);
						var pointLat = opener.document.getElementById("pointlat").value;
						var pointLng = opener.document.getElementById("pointlong").value;
						var radius = opener.document.getElementById("radius").value;
						var radiusUnits = opener.document.getElementById("radiusunits").value;
						if(radiusUnits == "mi"){
							radius = Math.round(radius*1609);
						}
						else{
							radius = radius*1000;
						}
						if(opener.document.getElementById("pointlat_NS").value == "S") pointLat = pointLat*-1;
						if(opener.document.getElementById("pointlong_EW").value == "W") pointLng = pointLng*-1;

						var newShape = new google.maps.Circle({
							strokeWeight: 0,
							fillOpacity: 0.45,
							editable: true,
							draggable: false,
							map: map,
				            center: new google.maps.LatLng(pointLat, pointLng),
				            radius: radius
						});
						newShape.type = 'circle';
						google.maps.event.addListener(newShape, 'click', function() { setSelection(newShape); });
						google.maps.event.addListener(newShape, 'dragend', function() { setSelection(newShape); });
						google.maps.event.addListener(newShape, 'radius_changed', function() { setSelection(newShape); });
						google.maps.event.addListener(newShape, 'center_changed', function() { setSelection(newShape); });
						bounds = newShape.getBounds();
						setSelection(newShape);
					}
				}
				else if(drawingManager.getDrawingMode() == "polygon"){
					if(opener.document.getElementById("footprintwkt").value != ''){
						//Set polygon
						bounds = new google.maps.LatLngBounds();
						drawingManager.setDrawingMode(null);
						var pointArr = [];
						var origFootprintWkt = opener.document.getElementById("footprintwkt").value;
						var footprintWKT = validatePolygon(origFootprintWkt);
						if(footprintWKT != origFootprintWkt){
							opener.document.getElementById("footprintwkt").value = footprintWKT;
						}
						footprintWKT = trimPolygon(footprintWKT);
						var strArr = footprintWKT.split(",");
						for(var i=1; i < strArr.length; i++){
							var xy = strArr[i].trim().split(" ");
							var lat = xy[0];
							var lng = xy[1];
							if(!isNumeric(lat) || !isNumeric(lng)){
								alert("One or more coordinates are illegal (lat: "+lat+"   long: "+lng+")");
								opener.document.getElementById("footprintwkt").value = origFootprintWkt;
								return false;
							}
							else if(parseInt(Math.abs(lat)) > 90 || parseInt(Math.abs(lng)) > 180){
								alert("One or more coordinates are out-of-range or ordered incorrectly (lat: "+lat+"   long: "+lng+")");
								opener.document.getElementById("footprintwkt").value = origFootprintWkt;
								return false;
							}
							var pt = new google.maps.LatLng(lat,lng);
							pointArr.push(pt);
							bounds.extend(pt);
						}
						if(pointArr.length > 0){
							var footPoly = new google.maps.Polygon({
								paths: pointArr,
								strokeWeight: 0,
								fillOpacity: 0.45,
								editable: true,
								draggable: false,
								map: map
							});
							footPoly.type = 'polygon';
							google.maps.event.addListener(footPoly, 'click', function() { setSelection(footPoly); });
							google.maps.event.addListener(footPoly, 'dragend', function() { setSelection(footPoly); });
							google.maps.event.addListener(footPoly.getPath(), 'insert_at', function() { setSelection(footPoly); });
							google.maps.event.addListener(footPoly.getPath(), 'remove_at', function() { setSelection(footPoly); });
							google.maps.event.addListener(footPoly.getPath(), 'set_at', function() { setSelection(footPoly); });
							setSelection(footPoly);
						}
					}
				}
				else if(drawingManager.getDrawingMode() == "rectangle"){
					if(opener.document.getElementById("upperlat").value != "" && opener.document.getElementById("bottomlat").value != "" && opener.document.getElementById("leftlong").value != "" && opener.document.getElementById("rightlong").value != ""){
						drawingManager.setDrawingMode(null);
						var northLat = opener.document.getElementById("upperlat").value;
						var southLat = opener.document.getElementById("bottomlat").value;
						var westLng = opener.document.getElementById("leftlong").value;
						var eastLng = opener.document.getElementById("rightlong").value;
						if(opener.document.getElementById("upperlat_NS")){
							if(opener.document.getElementById("upperlat_NS").value == "S") northLat = northLat*-1;
							if(opener.document.getElementById("bottomlat_NS").value == "S") southLat = southLat*-1;
							if(opener.document.getElementById("leftlong_EW").value == "W") westLng = westLng*-1;
							if(opener.document.getElementById("rightlong_EW").value == "W") eastLng = eastLng*-1;
						}

						var newShape = new google.maps.Rectangle({
							strokeWeight: 0,
							fillOpacity: 0.45,
							editable: true,
							draggable: false,
							map: map,
							bounds: new google.maps.LatLngBounds(new google.maps.LatLng(southLat, westLng), new google.maps.LatLng(northLat, eastLng))
						});
						newShape.type = 'rectangle';
						google.maps.event.addListener(newShape, 'click', function() { setSelection(newShape); });
						google.maps.event.addListener(newShape, 'dragend', function() { setSelection(newShape); });
						google.maps.event.addListener(newShape, 'bounds_changed', function() { setSelection(newShape); });
						bounds = newShape.getBounds();
						setSelection(newShape);
					}
				}
				if(bounds){
					map.fitBounds(bounds);
					map.panToBounds(bounds);
				}
			}

			function setSelection(shape) {
				if(activeShape && activeShape != shape) activeShape.setMap(null);
				activeShape = null;
				activeShape = shape;
				activeShape.setDraggable(true);
				activeShape.setEditable(true);
				setShapeToSearchForm();
			}

			function setShapeToSearchForm(){
				//Clear all coordinate values
				if(opener.document.getElementById("pointlat")) opener.document.getElementById("pointlat").value = "";
				if(opener.document.getElementById("pointlong")) opener.document.getElementById("pointlong").value = "";
				if(opener.document.getElementById("radius")) opener.document.getElementById("radius").value = "";
				if(opener.document.getElementById("radiusunits")) opener.document.getElementById("radiusunits").value = "km";
				if(opener.document.getElementById("footprintwkt")) opener.document.getElementById("footprintwkt").value = "";
				if(opener.document.getElementById("upperlat")) opener.document.getElementById("upperlat").value = "";
				if(opener.document.getElementById("bottomlat")) opener.document.getElementById("bottomlat").value = "";
				if(opener.document.getElementById("leftlong")) opener.document.getElementById("leftlong").value = "";
				if(opener.document.getElementById("rightlong")) opener.document.getElementById("rightlong").value = "";
				//Add shapes
				var shapeType = activeShape.type;

				if(shapeType == "rectangle"){
					var latUpperValue = activeShape.getBounds().getNorthEast().lat();
					latUpperValue = parseFloat(latUpperValue).toFixed(6);
					if(opener.document.getElementById("upperlat_NS")){
						if(latUpperValue > 0) opener.document.getElementById("upperlat_NS").value = 'N';
						else if(latUpperValue < 0) opener.document.getElementById("upperlat_NS").value = 'S';
						latUpperValue = Math.abs(latUpperValue);
					}
					opener.document.getElementById("upperlat").value = latUpperValue;

					var latBottomValue = activeShape.getBounds().getSouthWest().lat();
					latBottomValue = parseFloat(latBottomValue).toFixed(6);
					if(opener.document.getElementById("bottomlat_NS")){
						if(latBottomValue > 0) opener.document.getElementById("bottomlat_NS").value = 'N';
						else if(latBottomValue < 0) opener.document.getElementById("bottomlat_NS").value = 'S';
						latBottomValue = Math.abs(latBottomValue);
					}
					opener.document.getElementById("bottomlat").value = latBottomValue;

					var lngLeftValue = activeShape.getBounds().getSouthWest().lng();
					lngLeftValue = parseFloat(lngLeftValue).toFixed(6);
					if(opener.document.getElementById("leftlong_EW")){
						if(lngLeftValue > 0) opener.document.getElementById("leftlong_EW").value = 'E';
						else if(lngLeftValue < 0) opener.document.getElementById("leftlong_EW").value = 'W';
						lngLeftValue = Math.abs(lngLeftValue);
					}
					opener.document.getElementById("leftlong").value = lngLeftValue;

					var lngRightValue = activeShape.getBounds().getNorthEast().lng();
					lngRightValue = parseFloat(lngRightValue).toFixed(6);
					if(opener.document.getElementById("rightlong_EW")){
						if(lngRightValue > 0) opener.document.getElementById("rightlong_EW").value = 'E';
						else if(lngRightValue < 0) opener.document.getElementById("rightlong_EW").value = 'W';
						lngRightValue = Math.abs(lngRightValue);
					}
					opener.document.getElementById("rightlong").value = lngRightValue;
				}
				else if(shapeType == "polygon"){
					var coordinates = [];
					var coordinatesMVC = (activeShape.getPath().getArray());
					for(var i=0;i<coordinatesMVC.length;i++){
						var mvcString = coordinatesMVC[i].toString();
						mvcString = mvcString.slice(1, -1);
						var latlngArr = mvcString.split(",");
						coordinates.push(parseFloat(latlngArr[0]).toFixed(6)+" "+parseFloat(latlngArr[1]).toFixed(6));
					}
					if(coordinates[0] != coordinates[i]) coordinates.push(coordinates[0]);
					var coordStr = coordinates.toString();
					if(coordStr && coordStr != "" && coordStr != undefined){
						opener.document.getElementById("footprintwkt").value = "POLYGON (("+coordStr+"))";
					}
				}
				else if(shapeType == "circle"){
					var rad = (activeShape.getRadius());
					var radius = Math.round(rad/1000);
					opener.document.getElementById("radius").value = radius;

					var latValue = activeShape.getCenter().lat();
					if(latValue > 0) opener.document.getElementById("pointlat_NS").value = 'N';
					else if(latValue < 0) opener.document.getElementById("pointlat_NS").value = 'S';
					opener.document.getElementById("pointlat").value = Math.abs(parseFloat(latValue)).toFixed(6);

					var lngValue = activeShape.getCenter().lng();
					if(lngValue > 0) opener.document.getElementById("pointlong_EW").value = 'E';
					else if(lngValue < 0) opener.document.getElementById("pointlong_EW").value = 'W';
					opener.document.getElementById("pointlong").value = Math.abs(parseFloat(lngValue)).toFixed(6);
				}
			}

			function isNumeric(n) {
				return !isNaN(parseFloat(n)) && isFinite(n);
			}
		</script>
	</head>
	<body style="background-color:#ffffff;" onload="initialize()">
		<div style="float:right;margin-top:5px;margin-right:15px;">
			<button name="closebutton" type="button" onclick="self.close()">Save and Close</button>
		</div>
		<div id="helptext">
			Click on shape symbol to create a rectangle, circle, or polygon.<br/>Close mapping tool to transfer shape definition to search form.
		</div>
		<div id='map_canvas' style='width:100%;height:600px;'></div>
		<div>
		</div>
	</body>
</html>
