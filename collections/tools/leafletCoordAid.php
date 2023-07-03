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
	<title><?php echo $DEFAULT_TITLE; ?> - Taxon Map</title>
	<?php
	   include_once($SERVER_ROOT.'/includes/head.php');
	   include_once($SERVER_ROOT.'/includes/leafletMap.php');
	?>
   <meta charset="utf-8">

	<script src="<?php echo $CLIENT_ROOT; ?>/js/symb/wktpolygontools.js" type="text/javascript"></script>
	<style>
		#map { width:100%; height:100vh; }
	</style>
</head>
<body style="background-color:#ffffff;width:100%;height:100%;image-rendering: auto;
  image-rendering: crisp-edges;
  image-rendering: pixelated; ">

		<div style="float:right;margin-top:5px;margin-right:15px;">
			<button name="closebutton" type="button" onclick="self.close()">Save and Close</button>
		</div>
		<div id="helptext">
			Click on shape symbol to create a rectangle, circle, or polygon.<br/>Close mapping tool to transfer shape definition to search form.
		</div>
      <div id="map"></div>
      <script>

      /* Type Definitions
       *
       * lat: -90 < float < 90;
       * lng: -180 < float < 180;
       *
       */

      /* Shape Defintions:
       *
       * Polygon {
       *    type: polygon, 
       *    latlngs: [[lat, lng]...],
       *    wkt: String (Wkt format),
       * }
       *
       * Rectangle {
       *    type: "rectangle",
       *    upperLat: lat,
       *    lowerLat: lat,
       *    rightLng: lng,
       *    leftLng: lng,
       * }
       *
       * Circle { 
       *    type: "circle"
       *    radius: float,
       *    center [lat, lng]
       * }
       */

      const setField = (id, v) => {
         var elem = opener.document.getElementById(id);
         if(elem) elem.value = v;
      };

      const getField = (id) => {
         var elem = opener.document.getElementById(id);
         return elem? elem.value: null;
      };

		function isNumeric(n) {
			return !isNaN(parseFloat(n)) && isFinite(n);
		}

      function setRectangle(upperLat, lowerLat, leftLng, rightLng) {

         setField("upperlat_NS", upperLat > 0 ? "N": "S")
         setField("upperlat", Math.abs(upperLat));

         setField("bottomlat_NS", lowerLat > 0 ? "N": "S")
         setField("bottomlat", Math.abs(lowerLat));

         setField("leftlong_EW", leftLng > 0 ? "E": "W")
         setField("leftlong", Math.abs(leftLng));

         setField("rightlong_EW", rightLng> 0 ? "E": "W")
         setField("rightlong", Math.abs(rightLng));
      }
      
      function setCircle(radius, center_lat, center_lng) {
         setField("radius", isNaN(radius)? radius: Math.abs(radius))
         setField("radiusunits", "km")

         setField("pointlat_NS", center_lat > 0? "N": "S")
         setField("pointlat", Math.abs(center_lat))

         setField("pointlong_EW", center_lng > 0? "E": "W")
         setField("pointlong", Math.abs(center_lng))
      }

      function setPolygon(wkt) {
         setField("footprintwkt", wkt);
      }

      /* setShapeToSearchForm: 
       *
       * Sets Shape data to search form.
       *
       * activeShape: Shape Type (See Def at top of script)
       * 
       */
      function setShapeToSearchForm(activeShape) {
         //Clear Form
         setField("pointlat", "")
         setField("pointlong", "")
         setField("radius", "")
         setField("radiusunits", "")

         setField("footprintwkt", "")

         setField("upperlat", "")
         setField("bottomlat", "")
         setField("leftlong", "")
         setField("rightlong", "")

         //If Active Shape is null bail
         if(!activeShape)
            return;

         switch(activeShape.type) {
            case "polygon":
               setPolygon(activeShape.wkt)
               break;
            case "rectangle":
               const rec = activeShape 
               setRectangle(rec.upperLat, rec.lowerLat, rec.leftLng, rec.rightLng);
               break;
            case "circle":
               const circ = activeShape 
               setCircle(circ.radius, circ.center.lat, circ.center.lng)
               break;
         }
      }

      /* LoadShape Reads Coordinates from Form: 
       *
       * mapMode: enum ("polygon", "rectangle", "circle")
       *
       * Returns A Shape (See top of script):
       */
      function loadShape(mapMode) {
         switch(mapMode) {
            case "polygon":
               let origFootprintWkt = getField("footprintwkt");
               if(origFootprintWkt) { 
						var footprintWKT = validatePolygon(origFootprintWkt);

						if(footprintWKT != origFootprintWkt) setField("footprintwkt", footprintWKT);
 
						footprintWKT = trimPolygon(footprintWKT);

						var pointArr = [];
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
							pointArr.push([lat, lng]);
                  }

                  return { type: "polygon", latlngs: pointArr, wkt: getField("footprintwkt")};
               }
            break;
            case "rectangle":
               const upperLat = getField("upperlat");
               const lowerLat= getField("bottomlat");
               const leftLng = getField("leftlong");
               const rightLng = getField("rightlong");

               if(isNumeric(upperLat) && isNumeric(lowerLat) && isNumeric(leftLng) && isNumeric(rightLng)) {
                  return {
                     type: "rectangle",
                     upperLat: upperLat * (getField("upperlat_NS") === "N"? 1: -1),
                     rightLng: rightLng * (getField("rightlong_EW") === "E"? 1: -1),

                     lowerLat: lowerLat * (getField("bottomlat_NS") === "N"? 1: -1),
                     leftLng: leftLng * (getField("leftlong_EW") === "E"? 1: -1),
                  }
               }
            break;
            case "circle":
               const radius = getField("radius");
               const pointlat = getField("pointlat");
               const pointlng = getField("pointlong");
               if(isNumeric(radius) && isNumeric(pointlng) && isNumeric(pointlng)) {
                  return {
                     type: "circle",
                     radius: parseFloat(radius),
                     latlng: [
                        pointlat * (getField("pointlat_NS") === "N"? 1: -1), 
                        pointlng * (getField("pointlong_EW") === "E"? 1: -1)
                     ]
                  }
               }
               break;
            default:
               console.log(mapMode)
            break;
         } 
      }
      let formShape = loadShape("<?php echo $mapMode?>")

      //LEAFLET SPECIFIC

      const MapOptions = {
         center: [<?php echo $latCenter?>, <?php echo $latCenter?>],
         zoom: <?php echo $zoom?>
      }

      var map = new LeafletMap('map', MapOptions );

      map.enableDrawing({
         polyline: false,
         circlemarker: false,
         marker: false,
         drawColor: {opacity: 0.85, fillOpacity: 0.55, color: '#000' }
      }, setShapeToSearchForm);

      if(formShape) {
         map.drawShape(formShape)
         map.mapLayer.setView([map.activeShape.center.lat, map.activeShape.center.lng])
         map.mapLayer.fitBounds(map.activeShape.layer.getBounds())
      }
      //LEAFLET SPECIFIC END

   </script>
</body>
</html>
