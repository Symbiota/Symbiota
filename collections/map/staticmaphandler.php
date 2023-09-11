<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/MapSupport.php');
include_once($SERVER_ROOT.'/content/lang/collections/map/staticmaphandler.'.$LANG_TAG.'.php');
header('Content-Type: text/html; charset=' . $CHARSET);

$mapManager = new MapSupport();
$taxaList = $mapManager->getTaxaList();

//Set default bounding box for portal
$boundLatMin = -90;
$boundLatMax = 90;
$boundLngMin = -180;
$boundLngMax = 180;
$latCen = 41.0;
$longCen = -95.0;
if(!empty($MAPPING_BOUNDARIES)){
	$coorArr = explode(';', $MAPPING_BOUNDARIES);
	if($coorArr && count($coorArr) == 4){
		$boundLatMin = $coorArr[2];
		$boundLatMax = $coorArr[0];
		$boundLngMin = $coorArr[3];
		$boundLngMax = $coorArr[1];
		$latCen = ($boundLatMax + $boundLatMin)/2;
		$longCen = ($boundLngMax + $boundLngMin)/2;
	}
}
$bounds = [$boundLatMax, $boundLngMax, $boundLatMin, $boundLngMin];

//Redirects User if not an Admin
if(!$IS_ADMIN){
   header("Location: ". $CLIENT_ROOT . '/index.php');
}

?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">
<head>
	<title><?php echo $DEFAULT_TITLE; ?> - Static distribution map generator</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/leafletmap.php');
      ?>
<script 
   src="<?php echo $CLIENT_ROOT?>/js/dom-to-image/dist/dom-to-image.min.js"
   type="text/javascript">
</script>
	<script type="text/javascript">
         let map;
         let bounds;

         async function getTaxaCoordinates(tid, bounds) {
            const response = await fetch(`rpc/getCoordinates.php?tid=${tid}&bounds=${encodeURI(bounds)}`, {
               method: "GET",
               credentials: "same-origin",
               headers: {"Content-Type": "application/json"},
            });
            return await response.json();
         }

         async function buildMaps(e) {
            const data = document.getElementById('service-container');
            let taxaList = JSON.parse(data.getAttribute('data-taxa-list'))

            const leafletControls = document.querySelector('.leaflet-control-container')
            leafletControls.style.display = "none";

            updateMapBounds(bounds);

            let maptype;
            for (let maptype_option of document.getElementsByName("maptype"))  {
               if(maptype_option && maptype_option.checked) {
                  maptype = maptype_option.value;
                  break;
               }
            }

            for (let taxa of taxaList) {
               let coords = await getTaxaCoordinates(taxa.tid, bounds.join(';'));
               if(coords && coords.length > 0) { 
                  await postImage({
                     tid: taxa.tid, 
                     title: taxa.sciname, 
                     coordinates: coords, 
                     maptype, 
                  })
               }
               incrementLoadingBar(taxaList.length);
            }

            //Turn Controls back on when done processing maps
            leafletControls.style.display = "block";
         }

         async function getMapImage(imgName) {
            return await domtoimage.toBlob(document.getElementById('map'), {
               height: 500,
               width: 500
            });
         }

         async function postImage({tid, title, maptype, coordinates}) {
            
            let map_blob = maptype === "dotmap"?
               await buildDotMap(coordinates):
               await buildHeatMap(coordinates);

            let formData = new FormData();
            formData.append('mapupload', map_blob, `map.png`)
            formData.append('tid', tid)
            formData.append('title', title)
            formData.append('maptype', maptype)

            //tid, title, maptype
            let response = await fetch('rpc/postMap.php', {
               method: "POST",
               credentials: "same-origin",
               body: formData
            })
         }
         //getCoordinates -> Build Map -> Build Image -> Unbuild Map ->

         async function buildHeatMap(coordinates) {
            var cfg = {
               "radius": 0.2,
               "maxOpacity": .8,
               "scaleRadius": true,
               "useLocalExtrema": true,
               latField: 'lat',
               lngField: 'lng',
            };
            var heatmapLayer = new HeatmapOverlay(cfg);
            heatmapLayer.setData({
               max: 8,
               data: coordinates
            });
            heatmapLayer.addTo(map.mapLayer);

            let blob = await getMapImage('heatmap');
            map.mapLayer.removeLayer(heatmapLayer);
            return blob;
         }

         async function buildDotMap(coordinates) {

            const markerGroup = L.featureGroup(coordinates.map(coord =>  {
               return L.circleMarker([coord.lat, coord.lng], {
                     radius : 5,
                     color  : '#000000',
                     fillColor: `#B2BEB5`,
                     weight: 2,
                     opacity: 1.0,
                     fillOpacity: 1.0
               });
            })).addTo(map.mapLayer);

            let blob = await getMapImage('heatmap');
            map.mapLayer.removeLayer(markerGroup);
            return blob;
         }

         async function incrementLoadingBar(maxCount) {
            let count = parseInt(document.getElementById('loading-bar-count').innerHTML) + 1;
            document.getElementById('loading-bar-count').innerHTML = count; 


            let new_percent = (count / maxCount) * 100;
            document.getElementById('loading-bar').style.width = `${new_percent}%`;

            if(count === maxCount) {
               document.getElementById('loading-bar').style.width = `0%`;
               document.getElementById('loading-bar-count').innerHTML = 0; 
            } 
         }

         function updateMapBounds() {
            let markers = L.featureGroup()

            L.marker([bounds[0],bounds[1]]).addTo(markers);
            L.marker([bounds[2],bounds[3]]).addTo(markers);

            map.mapLayer.fitBounds(markers.getBounds());
         }

         function initialize() {
            const data = document.getElementById('service-container');
            let latlng = [
               parseFloat(data.getAttribute('data-lat')),
               parseFloat(data.getAttribute('data-lng'))
            ]

            bounds = JSON.parse(
               document.getElementById("service-container")
                  .getAttribute("data-bounds")
            )

            map = new LeafletMap('map', {
               center: latlng, 
               zoom: 6, 
               scale: false, 
            });

            let drawControl = new L.Control.Draw({
               draw: {
                  ...map.DEFAULT_DRAW_OPTIONS,
                  marker: false,
                  polygon: false,
                  circle: false,
                  circle: false,
                  rectangle: true,
               }
            });

			   map.mapLayer.addControl(drawControl);

            map.mapLayer.on('draw:created', function(e) {
               if(e.layerType === "rectangle") {
                  let rec = e.layer.getBounds()
                  let northEast = rec.getNorthEast()
                  let southWest = rec.getSouthWest()

                  document.getElementById("upper_lat").value = northEast.lat;
                  document.getElementById("upper_lng").value = northEast.lng;

                  document.getElementById("lower_lat").value = southWest.lat;
                  document.getElementById("lower_lng").value = southWest.lng;

                  bounds = [northEast.lat, northEast.lng, southWest.lat, southWest.lng];

                  updateMapBounds();
               }
            })

            const boundsInput = document.getElementById("bounds");

            document.getElementById("upper_lat").addEventListener("input", e => {
               let lat = parseFloat(e.target.value);
               if(lat <= 90 && lat >= -90) {
                  bounds[0] = lat;
                  updateMapBounds();
               }
            })
            document.getElementById("upper_lng").addEventListener("input", e => {
               let lng = parseFloat(e.target.value);
               if(lng <= 180 && lng >= -180) { 
                  bounds[1] = lng;
                  updateMapBounds();
               }
            })
            document.getElementById("lower_lat").addEventListener("input", e => {
               let lat = parseFloat(e.target.value);
               if(lat <= 90 && lat >= -90) {
                  bounds[2] = lat;
                  updateMapBounds();
               }
            })
            document.getElementById("lower_lng").addEventListener("input", e => {
               let lng = parseFloat(e.target.value);
               if(lng <= 180 && lng >= -180) { 
                  bounds[3] = lng;
                  updateMapBounds();
               }
            })
         }
	</script>
</head>
   <body onload="initialize()">
      <?php include($SERVER_ROOT . '/includes/header.php');?>
      <div id="service-container"
         data-taxa-list="<?= htmlspecialchars(json_encode($taxaList))?>"
         data-bounds="<?= htmlspecialchars(json_encode($bounds))?>"
         data-lat="<?= htmlspecialchars($latCen)?>"
         data-lng="<?= htmlspecialchars($longCen)?>"
      ></div>
      <div id="innertext">
         <div style="display:flex; justify-content:center">
            <div id="map" style="width:50rem;height:50rem;"></div>
         </div>
         <br/>
         <div style="background-color:#E9E9ED">
            <div id="loading-bar" style="height:2rem; width:0%; background-color:#1B3D2F"></div>
         </div>
         <div style="text-align: center; padding-top:0.5rem">
            Maps Generated
            <span id="loading-bar-count">0</span>
            <span>/ <?php echo count($taxaList)?></span>
         </div>
         <form id="thumbnailBuilder" name="thumbnailBuilder" method="post" action="">
            <div>Map Type</div>
            <input type="radio" name="maptype" id ="heatmap" value="heatmap" checked>
            <label for="heatmap">Heat Map</label><br>
            <input type="radio" name="maptype" id ="dotmap" value="dotmap">
            <label for="dotmap">Dot Map</label><br>

            <label>Upper Bound</label><br/>
            <label>Lat</label>
            <input id="upper_lat" onkeydown="return event.key != 'Enter';" value="<?php echo $boundLatMax?>" placeholder="<?php echo $boundLatMax?>"/>
            <label>Lng</label>
            <input id="upper_lng" onkeydown="return event.key != 'Enter';" value="<?php echo $boundLngMax?>" placeholder="<?php echo $boundLngMax?>"/><br>

            <label>Lower Bound</label><br/>
            <label>Lat</label>
            <input id="lower_lat" onkeydown="return event.key != 'Enter';" value="<?php echo $boundLatMin?>" placeholder="<?php echo $boundLatMin?>"/>
            <label>Lng</label>
            <input id="lower_lng" onkeydown="return event.key != 'Enter';" value="<?php echo $boundLngMin?>" placeholder="<?php echo $boundLngMin?>"/><br>
<!---
            <label for="taxon">Taxon</label><br>
            <input id="taxon"/><br/>
--->
<!---
         Form options to be added now:
         - map type (radio button): heat map, dot map
         - bounding box (set of text boxes): fields filled with above default bounding box values, but provides user ability to adjust. Maybe add the bounding box assist tool to help user define a new box?
         - replace (radio button): all maps, maps of set type (heat or dot), none
         - Target a specific taxon (text box with autocomplete that displays only accepted taxa of rankid 220 or greater)
         Form options to add later:
         - replace maps older than a certain date (date text box)
--->
            <button type="button" onclick="buildMaps()"><?= $LANG['BUILDMAPS'] ?></button>
         </form>
      </div>
      <?php include($SERVER_ROOT . '/includes/footer.php');?>
   </body>
</html>

