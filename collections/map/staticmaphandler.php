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
			//Only display JS if user is logged with SuperAdmin permissions
//			Form actions will trigger JS to do following via AJAX:
//			1) Grab accepted taxa list (currently set to return 1000 records at a time)
//			2) For each taxon:
//				a) grab coordinates to be mapped
//				b) Create map image using LeafLet
//				c) Post image to server and save URL to database
         //
         let map 
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
            let bounds = JSON.parse(data.getAttribute('data-bounds'))

            let markers = L.featureGroup()

            L.marker([bounds[0],bounds[1]]).addTo(markers);
            L.marker([bounds[2],bounds[3]]).addTo(markers);

            map.mapLayer.fitBounds(markers.getBounds());

            let maptype;
            for (let maptype_option of document.getElementsByName("maptype"))  {
               if(maptype_option && maptype_option.checked) {
                  maptype = maptype_option.value;
                  break;
               }
            }

            /*

            for (let taxa of taxaList) {
               let coords = await getTaxaCoordinates(taxa.tid, bounds.join(';'));
               if(coords && coords.length > 0) { 
                  if(maptype === "dotmap") {
                     await buildDotMap(coords);
                  } else {
                     await buildHeatMap(coords);
                  }
               }
               incrementLoadingBar(taxaList.length);
            }*/
            let coords = await getTaxaCoordinates(taxaList[50].tid, bounds.join(';'));  
            if(coords && coords.length > 0) console.log(coords);

            await postImage({
               tid: taxaList[50].tid, 
               title: taxaList[50].sciname, 
               coordinates: coords, 
               maptype, 
            })

            //map.mapLayer.clearLayers();
         }

         async function downloadMap(name) {
            const url = await domtoimage.toPng(document.getElementById('map'))
            // Creating an anchor(a) tag of HTML
            const a = document.createElement("a");

            // Passing the blob downloading url
            a.setAttribute("href", url);

            // Setting the anchor tag attribute for downloading
            // and passing the download file name
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, "0");
            var mm = String(today.getMonth() + 1).padStart(2, "0"); //January is 0!
            var yyyy = today.getFullYear();

            today = mm + "-" + dd + "-" + yyyy;
            a.setAttribute("download", name + "-" + today + ".png");

            // Performing a download with click
            a.click();
         }

         async function getMapImage(imgName) {
            return await domtoimage.toBlob(document.getElementById('map'))
         }

         async function postImage({tid, title, maptype, coordinates}) {
            
            let map_blob = maptype === "dotmap"?
               await buildDotMap(coordinates):
               await buildHeatMap(coordinates);

            let formData = new FormData();
            formData.append('mapupload', map_blob, `${title}.${maptype}.png`)
            formData.append('tid', tid)
            formData.append('title', title)
            formData.append('maptype', maptype)

            //tid, title, maptype
            let response = await fetch('rpc/postMap.php', {
               method: "POST",
               credentials: "same-origin",
               //headers: {"Content-Type": "application/formdata"},
               body: formData
            })

            console.log(await response.json())
         }
         //getCoordinates -> Build Map -> Build Image -> Unbuild Map ->

         async function buildHeatMap(coordinates) {
            var cfg = {
               "radius": 0.15,
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

            //await downloadMap('heatmap');
            let blob = await getMapImage('heatmap');
            //map.mapLayer.removeLayer(heatmapLayer);
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

            //await downloadMap('heatmap');

            let blob = await getMapImage('heatmap');
            //map.mapLayer.removeLayer(markerGroup);
            return blob;
         }

         async function incrementLoadingBar(maxCount) {
            let count = parseInt(document.getElementById('loading-bar-count').innerHTML) + 1;
            document.getElementById('loading-bar-count').innerHTML = count; 

            let new_percent = (count / maxCount) * 100;
            document.getElementById('loading-bar').style.width = `${new_percent}%`;
         }

         function updateBounds() {
         }


         function initialize() {
            const data = document.getElementById('service-container');
            let latlng = [
               parseFloat(data.getAttribute('data-lat')),
               parseFloat(data.getAttribute('data-lng'))
            ]

            map = new LeafletMap('map', {
               center: latlng, 
               zoom: 6, 
               scale: false, 
               layer_control: false,
               zoomControl: false
            });
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

            <label>Bounds</label><br/>
            <input id="bounds" style="width:20rem" value="<?php echo implode(';', $bounds)?>" placholder="<?php echo implode(';', $bounds)?>"/><br>

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

