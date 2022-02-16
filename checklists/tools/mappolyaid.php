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
$eMode = array_key_exists("emode",$_REQUEST)?$_REQUEST["emode"]:1;

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
		<script src="<?php echo $CLIENT_ROOT; ?>/js/symb/wktpolygontools.js?ver=2" type="text/javascript"></script>
		<script type="text/javascript">
			var map;
			var selectedShape = null;
			<?php
			if($formSubmit && $formSubmit == 'exit'){
				echo 'window.close();';
			}
			?>

			function initialize(){
				if(opener.document.getElementById("footprintwkt") && opener.document.getElementById("footprintwkt").value != ""){
					if(document.getElementById('footprintwkt').value == ""){
						document.getElementById('footprintwkt').value = opener.document.getElementById("footprintwkt").value;
					}
				}
				var dmLatLng = new google.maps.LatLng(<?php echo $latCenter.','.$lngCenter; ?>);
				var dmOptions = {
					zoom: <?php echo $zoom; ?>,
					center: dmLatLng,
					mapTypeId: google.maps.MapTypeId.TERRAIN,
					scaleControl: true
				};
				map = new google.maps.Map(document.getElementById("map_canvas"), dmOptions);

				var polyOptions = {
					strokeWeight: 0,
					fillOpacity: 0.45,
					editable: true,
					draggable: true
				};

				var drawingManager = new google.maps.drawing.DrawingManager({
					drawingMode: google.maps.drawing.OverlayType.POLYGON,
					drawingControl: true,
					drawingControlOptions: {
						position: google.maps.ControlPosition.TOP_CENTER,
						drawingModes: [
							google.maps.drawing.OverlayType.POLYGON
						]
					},
					markerOptions: {
						draggable: true
					},
					polygonOptions: polyOptions
				});

				drawingManager.setMap(<?php echo ($eMode?'map':'null'); ?>);

				google.maps.event.addListener(drawingManager, 'overlaycomplete', function(e) {
					if (e.type != google.maps.drawing.OverlayType.MARKER) {
						// Switch back to non-drawing mode after drawing a shape.
						drawingManager.setDrawingMode(null);

						var newShapeType = e.type;
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
						if (newShapeType == 'polygon'){
							setPolygonStr(newShape);
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

				// Clear the current selection when the drawing mode is changed or when the map is clicked
				google.maps.event.addListener(drawingManager, 'drawingmode_changed', clearSelection);
				google.maps.event.addListener(map, 'click', clearSelection);
				setPolygon();
			}

			function setPolygon(){
				var pointArr = [];
				var polyBounds = new google.maps.LatLngBounds();
				if(document.getElementById('footprintwkt').value != ''){
					var origFootprintWkt = document.getElementById('footprintwkt').value;
					var footprintWKT = validatePolygon(origFootprintWkt);
					if(footprintWKT != origFootprintWkt){
						document.getElementById("reformatdiv").style.display = "block";
						document.getElementById('footprintwkt').value = footprintWKT;
					}
					footprintWKT = trimPolygon(footprintWKT);
					var strArr = footprintWKT.split(",");
					for(var i=0; i < strArr.length; i++){
						var xy = strArr[i].trim().split(" ");
						var lat = xy[0];
						var lng = xy[1];
						if(!isNumeric(lat) || !isNumeric(lng)){
							document.getElementById("reformatdiv").style.display = "none";
							alert("One or more coordinates are illegal (lat: "+lat+"   long: "+lng+")");
							document.getElementById('footprintwkt').value = origFootprintWkt;
							return false;
						}
						else if(parseInt(Math.abs(lat)) > 90 || parseInt(Math.abs(lng)) > 180){
							document.getElementById("reformatdiv").style.display = "none";
							alert("One or more coordinates are out-of-range or ordered incorrectly (lat: "+lat+"   long: "+lng+")");
							document.getElementById('footprintwkt').value = origFootprintWkt;
							return false;
						}
						var pt = new google.maps.LatLng(lat,lng);
						pointArr.push(pt);
						polyBounds.extend(pt);
					}
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
					map.fitBounds(polyBounds);
					map.panToBounds(polyBounds);
				}
			}

			function isNumeric(n) {
				return !isNaN(parseFloat(n)) && isFinite(n);
			}

			function reformatCoordinates(f){
				reformCoordinates(f);
				resetPolygon();
			}

			function resetPolygon(){
				if(selectedShape) selectedShape.setMap(null);
				setPolygon();
			}

			function setSelection(shape) {
				selectedShape = shape;
				selectedShape.setEditable(<?php echo ($eMode?'true':'false'); ?>);
				if (shape.type == 'polygon') setPolygonStr(shape);
			}

			function clearSelection() {
				if(selectedShape){
					selectedShape.setEditable(false);
					selectedShape = null;
				}
			}

			function setPolygonStr(polygon) {
				var coordinates = [];
				var coordinatesMVC = (polygon.getPath().getArray());
				for(var i=0;i<coordinatesMVC.length;i++){
					var mvcString = coordinatesMVC[i].toString();
					mvcString = mvcString.slice(1, -1);
					var latlngArr = mvcString.split(",");
					coordinates.push(parseFloat(latlngArr[0]).toFixed(6)+" "+parseFloat(latlngArr[1]).toFixed(6));
				}
				if(coordinates[0] != coordinates[i-1]) coordinates.push(coordinates[0]);
				var coordStr = coordinates.toString();
				if(coordStr && coordStr != "" && coordStr != undefined){
					document.getElementById("footprintwkt").value = "POLYGON (("+coordStr+"))";
				}
			}

			function deleteSelectedShape(f) {
				if(selectedShape){
					selectedShape.setMap(null);
					clearSelection();
				}
				f.footprintwkt.value = "";
			}

			function submitPolygonForm(f){
				var str1 = "inline";
				var str2 = "none";
				if(f.clid.value == "" || f.footprintwkt.value == ""){
					str1 = "none";
					str2 = "inline";
				}
				if(opener.document.getElementById("polyDefDiv")){
					opener.document.getElementById("polyDefDiv").style.display = str1;
					opener.document.getElementById("polyNotDefDiv").style.display = str2;
				}
				opener.document.getElementById("footprintwkt").value = f.footprintwkt.value;
				if(f.clid.value == 0){
					window.close();
					return false;
				}
				return true;
			}

			function toggle(target){
				var ele = document.getElementById(target);
				if(ele){
					if(ele.style.display=="none"){
						ele.style.display="";
					}
					else{
						ele.style.display="none";
					}
				}
			}
		</script>
	</head>
	<body style="background-color:#ffffff;" onload="initialize()">
		<div id="map_canvas" style="width:100%;height:600px;"></div>
		<div style="width:100%;">
			<div id="reformatdiv" style="display:none;color:red">Polygon has been reformated. The new polygon must be saved before it is usable!</div>
			<div id="helptext" style="display:none;margin:5px 0px">
				Click on polygon symbol to activate polygon tool and create a shape representing research area.
				Click save button to link polygon to checklist.
				The WKT polygon footprint within the text box can be modifed by hand and rebuilt on map using the Redraw Polygon button.
				A WKT polygon definition can be copied into text area from another application.
				Use Switch Coordinate Order button to convert Long-Lat coordinate pairs to Lat-Long format.
			</div>
			<form name="polygonSubmitForm" method="post" action="mappolyaid.php" onsubmit="return submitPolygonForm(this)">
				<div style="float:left;width:800px">
					<textarea id="footprintwkt" name="footprintwkt" style="width:98%;height:90px;"><?php echo $clManager->getFootprintWkt(); ?></textarea>
					<input name="clid" type="hidden" value="<?php echo $clid; ?>" />
					<input name="latdef" type="hidden" value="<?php echo $latDef; ?>" />
					<input name="lngdef" type="hidden" value="<?php echo $lngDef; ?>" />
					<input name="zoom" type="hidden" value="<?php echo $zoom; ?>" />
				</div>
				<?php
				if($eMode){
					?>
					<div style="float:left">
						<button name="formsubmit" type="submit" value="save">Save Polygon</button>
						<a href="#" onclick="toggle('helptext')"><img alt="Display Help Text" src="../../images/qmark_big.png" style="width:15px;" /></a><br/>
						<button name="formsubmit" type="submit" value="save" onclick="deleteSelectedShape(this.form)">Delete Selected Shape</button><br/>
						<button type="button" onclick="resetPolygon()">Redraw Polygon</button><br/>
						<button type="button" onclick="reformatCoordinates(this.form);">Reformat Coordinates</button>
					</div>
					<?php
				}
				?>
			</form>
		</div>
	</body>
</html>