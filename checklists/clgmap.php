<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ChecklistManager.php');
@include_once($SERVER_ROOT.'/content/lang/header.'.$LANG_TAG.'.php');
header("Content-Type: text/html; charset=".$CHARSET);

$pid = $_REQUEST['pid'];
$target = array_key_exists('target',$_REQUEST)?$_REQUEST['target']:'checklists';

//Sanitation
$pid = htmlspecialchars($pid, HTML_SPECIAL_CHARS_FLAGS);
if(!is_numeric($pid)) $pid = 0;

$clManager = new ChecklistManager();
$clManager->setProj($pid);
?>
<html>
   <head>
      <?php 
        include_once($SERVER_ROOT.'/includes/leafletMap.php');
	     include_once($SERVER_ROOT.'/includes/googleMap.php');
      ?>
      <script type="text/javascript" src="<?php echo $CLIENT_ROOT; ?>/js/jquery.js"></script>
		<title><?php echo $DEFAULT_TITLE.' - '.(isset($LANG['H_INVENTORIES'])?$LANG['H_INVENTORIES']:'Species Checklists'); ?></title>
		<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />

		<script type="text/javascript">

         let infoWins = new Array();
         let checklists;
         let pid;

         function navigateToCheckList(clid, pid) { 
            window.location.href = `../ident/key.php?clid=${clid}&pid=${pid}&taxon=All+Species`;
         }

         function leafletInit() {
            let map = new LeafletMap('map_canvas');
            const markers = [];

            for(let checklistId of Object.keys(checklists)) {
               const checklist = checklists[checklistId];
               const latlng = [parseFloat(checklist.lat), parseFloat(checklist.lng)];
               markers.push(L.marker(latlng)
                  .bindTooltip(checklist.name)
                  .bindPopup(`<div style=\'width:300px;\'>
                     <b>${checklist.name}</b><br/>Double Click to open
                     </div>`)
                  .on('dblclick', () => navigateToCheckList(checklistId, pid)));
            }
            const markerGroup = L.featureGroup(markers).addTo(map.mapLayer);
            map.mapLayer.fitBounds(markerGroup.getBounds());
         }

         function googleInit() {

            let map = new GoogleMap('map_canvas');
		      let bounds = new google.maps.LatLngBounds();
            let infoWins = new Array();

			   function closeAllInfoWins(){
				   for(let w = 0; w < infoWins.length; w++ ) {
					   let win = infoWins[w];
					   win.close();
				   }
			   }

            for(let checklistId of Object.keys(checklists)) {
               const checklist = checklists[checklistId];
               let coord = new google.maps.LatLng(parseFloat(checklist.lat), parseFloat(checklist.lng));
               bounds.extend(coord);

               let m = new google.maps.Marker({
                  position: coord, 
                  map: map.mapLayer, 
                  title: checklist.name, 
               })
               const infoWin = new google.maps.InfoWindow({
                  content: `<div style=\'width:300px;\'>
                     <b>${checklist.name}</b><br/>Double Click to open
                  </div>` 
               });

               infoWins.push(infoWin);

               google.maps.event.addListener(m, 'click', function(e){ 
                  closeAllInfoWins(); 
                  infoWin.open(map.mapLayer, m); 
               });

               google.maps.event.addListener(m, "dblclick", function(){ 
                 closeAllInfoWins();
                 m.setAnimation(google.maps.Animation.BOUNCE);
                 navigateToCheckList(checklistId, pid);
               });
            }

            map.mapLayer.fitBounds(bounds);
         }

			function initialize(){

            //Load Server Data from HTML Data Attributes
            $('.service-container').each(function() {
               let container = $(this);
               checklists = container.data('checklists');
               pid = container.data('pid');
            })

            if("<?php echo $LEAFLET?>") {
               leafletInit();
            } else {
               googleInit();
            }
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
	<body style="background-color:#ffffff;" onload="initialize()">
		<div id="map_canvas"></div>
      <div class="service-container" 
        data-checklists="<?= htmlspecialchars(json_encode($clManager->getResearchPoints()))?>"
        data-pid="<?= htmlspecialchars($pid)?>"
   />
	</body>
</html>
