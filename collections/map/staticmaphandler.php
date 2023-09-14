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
         let coordLayer;

         async function getTaxaCoordinates(tid, bounds) {
            let bounds_str = encodeURI(`${bounds[0][0]};${bounds[0][1]};${bounds[1][0]};${bounds[1][1]}`)

            const response = await fetch(`rpc/getCoordinates.php?tid=${tid}&bounds=${bounds_str}`, {
               method: "GET",
               credentials: "same-origin",
               headers: {"Content-Type": "application/json"},
            });
            return await response.json();
         }

         async function getTaxaList(name) {
            const response = await fetch(`<?php echo $CLIENT_ROOT?>/rpc/gettaxon.php?sciname=${name}`, {
               method: "GET",
               credentials: "same-origin",
               headers: {"Content-Type": "application/json"},
            });
            let res = await response.json();

            return Object.entries(res).map( ([k, v]) => ({tid: k, sciname: v.sciname}));
         }

         async function buildMaps(preview = true) {
            //Clear Old Layer if It Exists
            if(coordLayer) map.mapLayer.removeLayer(coordLayer);

            let mapProgress = document.getElementById("map-generation-progress")

            const data = document.getElementById('service-container');
            let taxaList = JSON.parse(data.getAttribute('data-taxa-list'))

            const leafletControls = document.querySelector('.leaflet-control-container')
            leafletControls.style.display = "none";

            const taxa = document.getElementById('taxa').value;

            if(taxa) taxaList = await getTaxaList(taxa);

            if(!preview) mapProgress.style.display = "block";

            let maptype;
            for (let maptype_option of document.getElementsByName("maptype"))  {
               if(maptype_option && maptype_option.checked) {
                  maptype = maptype_option.value;
                  break;
               }
            }

            let basebounds = getMapBounds()
            let userZoom = map.mapLayer.getZoom();
            let baseZoom = userZoom >= 7 ? userZoom: 7;
            let count = 0;

            for (let taxa of taxaList) {
               let coords = await getTaxaCoordinates(taxa.tid, basebounds);
               count++;

               if(coords && coords.length > 0) { 
                  //Fits bounds within our search bounds for a better image
                  map.mapLayer.fitBounds(coords.map(c => [c.lat, c.lng]));

                  //Scale Back the zoom value if zoomed in too much
                  let newZoom = map.mapLayer.getZoom()
                  map.mapLayer.setZoom(newZoom <= baseZoom? newZoom: baseZoom);

                  coordLayer = generateMap({maptype, coordinates: coords});

                  if(preview) break;

                  if(!preview) {
                     //Wait for Map to Render and Pan to Points
                     await new Promise(r => setTimeout(r, 1000));

                     let map_blob = await getMapImage(); 
                     map.mapLayer.removeLayer(coordLayer);
                     postImage({
                        tid: taxa.tid, 
                        title: taxa.sciname, 
                        map_blob,
                        maptype, 
                     })
                  } 
               } else if (preview && count >= taxaList.length) {
                  alert(`There are no records of ${taxa.scimane} within your bounds!`)
               }

               if(!preview) incrementLoadingBar(taxaList.length);
            }

            map.mapLayer.fitBounds(basebounds);

            //Turn Controls back on when done processing maps
            leafletControls.style.display = "block";
            mapProgress.style.display = "none";
         }

         async function getMapImage() {
            return await domtoimage.toBlob(document.getElementById('map'), {
               height: 500,
               width: 500
            });
         }

         async function postImage({tid, title, maptype, map_blob}) {
            let formData = new FormData();
            formData.append('mapupload', map_blob, `map.png`)
            formData.append('tid', tid)
            formData.append('title', title)
            formData.append('maptype', maptype)
            //tid, title, maptype
            let response = fetch('rpc/postMap.php', {
               method: "POST",
               credentials: "same-origin",
               body: formData
            })
         }

         function generateMap({maptype, coordinates}) {
            return maptype === "dotmap"?
               buildDotMap(coordinates):
               buildHeatMap(coordinates);
         }

         function buildHeatMap(coordinates) {
            //Input is between 1 and 100 and needs to be translated between 0.1
            //and 1
            let radiusInput = document.getElementById("heat-radius").value;
            let heatMaxDensity = document.getElementById("heat-max-density").value;
            let heatMinDensity = document.getElementById("heat-min-density").value;

            var cfg = {
               "radius": parseFloat(radiusInput / 100),
               "maxOpacity": .9,
               "scaleRadius": true,
               "useLocalExtrema": false,
               latField: 'lat',
               lngField: 'lng',
            };
            let heatmapLayer = new HeatmapOverlay(cfg);

            heatmapLayer.addTo(map.mapLayer);

            heatmapLayer.setData({
               max: parseInt(heatMaxDensity) || 3,
               min: parseInt(heatMinDensity) || 1,
               data: coordinates
            });
 
            return heatmapLayer;
         }

         function buildDotMap(coordinates) {

            const markerGroup = L.featureGroup(coordinates.map(coord =>  {
               return L.circleMarker([coord.lat, coord.lng], {
                     radius : 8,
                     color  : '#000000',
                     fillColor: `#B2BEB5`,
                     weight: 2,
                     opacity: 1.0,
                     fillOpacity: 1.0
               });
            })).addTo(map.mapLayer);

            return markerGroup;
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

         function updateMapBounds(new_bounds) {
            map.mapLayer.fitBounds(new_bounds);
         }

         function refreshBoundInputs() {
            mapBounds = map.mapLayer.getBounds();
            let northEast = mapBounds.getNorthEast();
            let southWest = mapBounds.getSouthWest();

            document.getElementById("upper_lat").value = northEast.lat.toFixed(6);
            document.getElementById("upper_lng").value = northEast.lng.toFixed(6);

            document.getElementById("lower_lat").value = southWest.lat.toFixed(6);
            document.getElementById("lower_lng").value = southWest.lng.toFixed(6);
         }

         function getMapBounds() { 
            return [
               [document.getElementById("upper_lat").value, document.getElementById("upper_lng").value],
               [document.getElementById("lower_lat").value, document.getElementById("lower_lng").value]
            ];
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
                  updateMapBounds(e.layer.getBounds());
                  refreshBoundInputs();
               }
            })

            map.mapLayer.on('dragend', () => refreshBoundInputs())
            map.mapLayer.on('zoom', (e) => refreshBoundInputs())

            const boundsInput = document.getElementById("bounds");

            document.getElementById("upper_lat").addEventListener("input", e => {
               let lat = parseFloat(e.target.value);
               if(lat <= 90 && lat >= -90) {
                  let new_bounds = getMapBounds()
                  new_bounds[0][0] = lat;
                  updateMapBounds(new_bounds);
               }
            })
            document.getElementById("upper_lng").addEventListener("input", e => {
               let lng = parseFloat(e.target.value);
               if(lng <= 180 && lng >= -180) { 
                  let new_bounds = getMapBounds()
                  new_bounds[0][1] = lng;
                  updateMapBounds(new_bounds);
               }
            })
            document.getElementById("lower_lat").addEventListener("input", e => {
               let lat = parseFloat(e.target.value);
               if(lat <= 90 && lat >= -90) {
                  let new_bounds = getMapBounds()
                  new_bounds[1][0] = lat;
                  updateMapBounds(new_bounds);
               }
            })
            document.getElementById("lower_lng").addEventListener("input", e => {
               let lng = parseFloat(e.target.value);
               if(lng <= 180 && lng >= -180) { 
                  let new_bounds = getMapBounds()
                  new_bounds[1][1] = lng;
                  updateMapBounds(new_bounds);
               }
            })
         }
	</script>
	<script src="../../js/jquery-3.2.1.min.js?ver=3" type="text/javascript"></script>
	<script src="../../js/jquery-ui/jquery-ui.min.js?ver=3" type="text/javascript"></script>
	<link href="../../js/jquery-ui/jquery-ui.min.css" type="text/css" rel="Stylesheet" />
	<script src="../../js/symb/api.taxonomy.taxasuggest.js?ver=4" type="text/javascript"></script>
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
         <div id="map-generation-progress" style="display: none">
            <div style="background-color:#E9E9ED">
               <div id="loading-bar" style="height:2rem; width:0%; background-color:#1B3D2F"></div>
            </div>
            <div style="text-align: center; padding-top:0.5rem">
               Maps Generated
               <span id="loading-bar-count">0</span>
               <span>/ <?php echo count($taxaList)?></span>
            </div>
         </div>
         <form id="thumbnailBuilder" name="thumbnailBuilder" method="post" action="">
            <fieldset>
               <legend>Map Type</legend>
            <input type="radio" name="maptype" id ="heatmap" value="heatmap" checked>
               <label for="heatmap">
                  Heat Map
                  <span id="heat-radius-container" style="display: flex; align-items:center">
                     <label for="heat-radius">Heat Radius: 0.1</label>
                     <input style="margin: 0 1rem;"type="range" value="70" id="heat-radius" name="heat-radius" min="1" max="100">1
                  </span>
                  <label for="heat-min-density">Min Density: </label>
                  <input style="margin: 0 1rem; width: 5rem;"value="1" id="heat-min-density" name="heat-min-density">
                  <label for="heat-max-density">Max Density: </label>
                  <input style="margin: 0 1rem; width: 5rem;"value="3" id="heat-max-density" name="heat-max-density">
                  <br/>
               </label><br/>
            <input type="radio" name="maptype" id ="dotmap" value="dotmap">
            <label for="dotmap">Dot Map</label><br/>
            </fieldset>


            <label for="taxa"><?php echo $LANG['TYPE_TAXON'] ?>:</label>
            <input id="taxa" type="text" size="60" name="taxa" id="taxa" value="" title="<?php echo $LANG['SEPARATE_MULTIPLE']; ?>" /><br/>


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
            <button type="button" onclick="buildMaps(false)"><?= $LANG['BUILDMAPS'] ?></button>
            <button type="button" onclick="buildMaps(true)"><?= $LANG['PREVIEWMAP'] ?></button>
         </form>
      </div>
      <?php include($SERVER_ROOT . '/includes/footer.php');?>
   </body>
</html>

