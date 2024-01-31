<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ChecklistManager.php');
@include_once($SERVER_ROOT.'/content/lang/checklists/checklistmap.'.$LANG_TAG.'.php');
header('Content-Type: text/html; charset='.$CHARSET);

$clid = filter_var($_REQUEST['clid'], FILTER_SANITIZE_NUMBER_INT);
$thesFilter = array_key_exists('thesfilter', $_REQUEST) ? filter_var($_REQUEST['thesfilter'], FILTER_SANITIZE_NUMBER_INT) : 1;
$taxonFilter = array_key_exists('taxonfilter', $_REQUEST) ? $_REQUEST['taxonfilter'] : '';

if(!$thesFilter) $thesFilter = 1;

$clManager = new ChecklistManager();
$clManager->setClid($clid);
if($thesFilter) $clManager->setThesFilter($thesFilter);
if($taxonFilter) $clManager->setTaxonFilter($taxonFilter);

$coordArr = $clManager->getVoucherCoordinates();
$clMeta = $clManager->getClMetaData();
$coordJson = json_encode($coordArr);

$coords = [];

foreach($coordArr as $tid => $taxaCoords) {
   foreach($taxaCoords as $coord) {
      $ll = explode(',',$coord['ll']);
      if(count($ll) == 2 && trim($ll[0]) != 0 && trim($ll[1]) != 0) {
         array_push($coords, ['lat' => $ll[0], 'lng' => $ll[1], 'occid' => $coord['occid'], 'notes' => $coord['notes']]);
      }
   }
}
$coordJson = json_encode($coords);
$metaJson = json_encode($clMeta);

?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE.' - '.(isset($LANG['COORD_MAP'])?$LANG['COORD_MAP']:'Checklist Coordinate Map'); ?></title>
	<?php
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	include_once($SERVER_ROOT.'/includes/leafletMap.php');
	include_once($SERVER_ROOT.'/includes/googleMap.php');
	?>

   <script src="<?php echo $CLIENT_ROOT?>/js/symb/wktpolygontools.js" type="text/javascript"></script>
   <script src="<?php echo $CLIENT_ROOT?>/js/symb/MapShapeHelper.js" type="text/javascript"></script>

	<script type="text/javascript">
      var map;
      var puWin;
      let occurCoords = [];
      let clMeta;
      let cl_footprint_shape;

      function leafletInit() {
			var dmOptions = {
				zoom: 3,
				center: [41,-95],
         };
         map = new LeafletMap("map_canvas", dmOptions);
         const leafletSmallPin = img => L.icon({
            iconUrl: img,
            iconSize:     [12, 20],
            iconAnchor:   [6, 20],
            popupAnchor:  [0, -12],
            tooltipAnchor:  [0, -12]
         });

         var symb_occur = leafletSmallPin('../images/google/smpin_red.png')
         var symb = leafletSmallPin('../images/google/smpin_blue.png')
         var inat_icon = leafletSmallPin('../images/google/smpin_green.png')

         let markers = []
         for(let coord of occurCoords) {
            let marker = L.marker([coord.lat, coord.lng], {
               icon: coord.occid? symb_occur: symb,
            })
            .bindTooltip(`<div style="font-size:1.2rem">${coord.notes}</div>`)
            .on('click', () => openIndPU(coord.occid));
            markers.push(marker);
         }
         let markerGroup = new L.FeatureGroup(markers);

         map.enableDrawing({...map.DEFAULT_DRAW_OPTIONS, control: false});
         if(cl_footprint_shape) map.drawShape(cl_footprint_shape);

         markerGroup.addTo(map.mapLayer)

         map.mapLayer.fitBounds(markerGroup.getBounds());

         //Only for inaturalist for now
         if(clMeta.dynamicProperties && clMeta.dynamicProperties.externalserviceid) {
            //TODO (Logan) Only grabs 200 records for now setup multi request
            //fill in. Note this must be throttled to be less than 100 requests
            //per minute for iNaturalist guidelines
            getInatProjectOccurrences(clMeta.dynamicProperties.externalserviceid).then(res => {
               let inat_markers = [];
               for(let occur of res.results) {
                  if(occur.geojson && occur.geojson.type === "Point") {
                     let marker = L.marker(occur.geojson.coordinates.reverse(), {
                        icon: inat_icon,
                     })
                     .bindTooltip(`<div style="font-size:1.2rem">iNaturalist-${occur.id}</div>`)
                     .on('click', () => window.open(occur.uri, '_blank'));

                     inat_markers.push(marker);
                  }
               }

               let inat_markerGroup = new L.FeatureGroup(inat_markers);
               inat_markerGroup.addTo(map.mapLayer)
            });
         }

      }

      function googleInit() {
         var vIcon = new google.maps.MarkerImage("../images/google/smpin_red.png");
         var pIcon = new google.maps.MarkerImage("../images/google/smpin_blue.png");
         var inatIcon = new google.maps.MarkerImage("../images/google/smpin_green.png");

			var dmOptions = {
				zoom: 3,
				center: new google.maps.LatLng(41,-95),
				mapTypeId: google.maps.MapTypeId.TERRAIN,
				scaleControl: true
         };
         map = new GoogleMap("map_canvas", dmOptions);

			var bounds = new google.maps.LatLngBounds();

         for(let coord of occurCoords) {
            let marker = new google.maps.Marker({
					position: new google.maps.LatLng(coord.lat, coord.lng),
               title: coord.notes,
               icon: coord.occid? vIcon: pIcon,
               map: map.mapLayer,
					zIndex: google.maps.Marker.MAX_ZINDEX
               });
            bounds.extend(marker.getPosition());

            google.maps.event.addListener(marker, 'click', function() {
               openIndPU(coord.occid)
            })
         }

         map.mapLayer.fitBounds(bounds);

         map.enableDrawing({...map.DEFAULT_DRAW_OPTIONS, polygon: false, rectangle: false, circle: false});
         if(cl_footprint_shape) map.drawShape(cl_footprint_shape);

         //Only for inaturalist for now
         if(clMeta.dynamicProperties && clMeta.dynamicProperties.externalserviceid) {
            //TODO (Logan) Only grabs 200 records for now setup multi request
            //fill in. Note this must be throttled to be less than 100 requests
            //per minute for iNaturalist guidelines
            getInatProjectOccurrences(clMeta.dynamicProperties.externalserviceid).then(res => {
               for(let occur of res.results) {
                  if(occur.geojson && occur.geojson.type === "Point") {
                     let marker = new google.maps.Marker({
                        position: new google.maps.LatLng(occur.geojson.coordinates[1], occur.geojson.coordinates[0]),
                        title: occur.uri,
                        title: "iNaturalist-" + occur.id,
                        icon: inatIcon,
                        map: map.mapLayer,
                        zIndex: google.maps.Marker.MAX_ZINDEX
                     });

                     google.maps.event.addListener(marker, 'click', () => {
                        window.open(occur.uri, '_blank');
                     });
                  }
               }
            });
         }
      }

      function parseNested(str) {
          try {
              return JSON.parse(str, (_, val) => {
                  if (typeof val === 'string')
                      return parseNested(val)
                  return val
              })
          } catch (e) {
              return str
          }
      }

		// Note Need to Throttle to < 100 requests per minute as per iNaturalist API guidelines
      async function getInatProjectOccurrences(inat_proj_id) {

         let url = `https://api.inaturalist.org/v1/observations?project_id=${inat_proj_id}&geo=true&mappable=true&per_page=200`;
         let response = await fetch(url, {
            method: "GET",
            headers: {
               "Content-Type": "application/json",
            }
         })

         return await response.json();
      }

		function initialize() {
         try {
            let data = document.getElementById('service-container')
            occurCoords = JSON.parse(data.getAttribute('data-occur-coords'));
            clMeta = parseNested(data.getAttribute('data-cl-meta'));
         } catch (err) {
            alert("Failed to load occurence data")
         }

         if(clMeta && clMeta.footprintwkt) {
            cl_footprint_shape = loadMapShape("polygon", { polygonLoader: () => clMeta.footprintwkt });
         }

         <?php if(empty($GOOGLE_MAP_KEY)):?>
            leafletInit();
         <?php else:?>
            googleInit();
         <?php endif?>
      }

		function openIndPU(occId){
			if(puWin != null) puWin.close();
			var puWin = window.open('../collections/individual/index.php?occid='+occId,'indspec' + occId,'scrollbars=1,toolbar=0,resizable=1,width=900,height=600,left=20,top=20');
			if(puWin.opener == null) puWin.opener = self;
			setTimeout(function () { puWin.focus(); }, 0.5);
			return false;
      }
	</script>
	<style>
		html, body, #map_canvas {
			width: 100%;
			height: 100%;
			margin: 0;
			padding: 0;
		}
	</style>
</head>
<body style="background-color:#ffffff;" onload="initialize();">
<?php
	if(!$coordArr){
		?>
		<div style='font-size:120%;font-weight:bold;'>
			<?php echo (isset($LANG['NO_COORDS'])?$LANG['NO_COORDS']:'Your query apparently does not contain any records with coordinates that can be mapped'); ?>.
		</div>
		<div style="margin:15px;">
			<?php echo (isset($LANG['MAYBE_RARE'])?$LANG['MAYBE_RARE']:'It may be that the vouchers have rare/threatened status that require the locality coordinates be hidden'); ?>.
		</div>
		<?php
	}
	?>
   <div id="service-container"
      data-occur-coords="<?= htmlspecialchars($coordJson) ?>"
      data-cl-meta="<?= htmlspecialchars($metaJson)?>"
   />
	<div id='map_canvas'></div>
</body>
</html>
