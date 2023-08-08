<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ChecklistManager.php');
@include_once($SERVER_ROOT.'/content/lang/checklists/checklistmap.'.$LANG_TAG.'.php');

header('Content-Type: text/html; charset='.$CHARSET);

$clid = filter_var($_REQUEST['clid'], FILTER_SANITIZE_NUMBER_INT);
$thesFilter = array_key_exists('thesfilter',$_REQUEST)?filter_var($_REQUEST['thesfilter'], FILTER_SANITIZE_NUMBER_INT):1;
$taxonFilter = array_key_exists('taxonfilter',$_REQUEST)?filter_var($_REQUEST['taxonfilter'], FILTER_SANITIZE_STRING):'';

if(!$thesFilter) $thesFilter = 1;

$clManager = new ChecklistManager();
$clManager->setClid($clid);
if($thesFilter) $clManager->setThesFilter($thesFilter);
if($taxonFilter) $clManager->setTaxonFilter($taxonFilter);

$coordArr = $clManager->getVoucherCoordinates();
$clMeta = $clManager->getClMetaData();

?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE.' - '.(isset($LANG['COORD_MAP'])?$LANG['COORD_MAP']:'Checklist Coordinate Map'); ?></title>
	<?php
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	include_once($SERVER_ROOT.'/includes/leafletMap.php');
	include_once($SERVER_ROOT.'/includes/googleMap.php');
?>

<script type="text/javascript" src="<?php echo $CLIENT_ROOT; ?>/js/jquery.js"></script>
<script src="<?php echo $CLIENT_ROOT; ?>/js/symb/wktpolygontools.js" type="text/javascript"></script>
<script type="text/javascript"> 

   let voucherCoords;
   let checklistMeta;
   let checklistPolygon;
   let puWin;

   function leafletInit() {
      let markers = L.markerClusterGroup();

      for(let tid of Object.keys(voucherCoords)) {
         for(let pt of voucherCoords[tid]) {
            const latlng = pt.ll.split(',').map(v=>parseFloat(v));
            markers.addLayer(
               L.marker(latlng)
               .bindTooltip(pt.notes)
               .on('click', () => openIndPU(pt.occid)))
         }
      }

      const MapOptions = {
         center: [41,-95],
         zoom: 7 
      };

      let map = new LeafletMap('map_canvas', MapOptions );
      map.enableDrawing({
         control: false,
         drawColor: {opacity: 0.85, fillOpacity: 0.55, color: '#000' }
      });

      map.mapLayer.addLayer(markers)

      if(checklistMeta['footprintwkt']) {
         try {
            const latlngs = parseWkt(checklistMeta['footprintwkt']);
            const leaflet_poly = L.polygon(latlngs);

            const bounds = leaflet_poly.getBounds();

            map.mapLayer.setView(bounds.getCenter());
            map.mapLayer.fitBounds(bounds);

            leaflet_poly.addTo(map.mapLayer);
         } catch (e) {
            alert(e.message);
         }
      } else {
         map.mapLayer.fitBounds(markers.getBounds());
      }
   }

   function googleInit() {
      let map = new GoogleMap('map_canvas');

		let vIcon = new google.maps.MarkerImage("../images/google/smpin_red.png");
		let pIcon = new google.maps.MarkerImage("../images/google/smpin_blue.png");

		var llBounds = new google.maps.LatLngBounds();

      for(let tid of Object.keys(voucherCoords)) {
         for(let pt of voucherCoords[tid]) {
            const latlng = pt.ll.split(',').map(v=>parseFloat(v));
				let coord = new google.maps.LatLng(latlng[0], latlng[1]);
            llBounds.extend(coord);

            if(pt.occid) {
               let m = new google.maps.Marker({
                  position: coord, 
                  map: map.mapLayer, 
                  title: pt.notes, 
                  icon: vIcon
               });
				   google.maps.event.addListener(m, "click", function(){ openIndPU(coord.occid) });

            } else {
               let m = new google.maps.Marker({
                  position: coord, 
                  map: map.mapLayer, 
                  title: pt.sciname, 
                  icon: pIcon
               });
            }
         }
      }

      if(checklistPolygon) {
        new google.maps.Polygon({
            paths: checklistPolygon.map(pt=> new google.maps.LatLng(pt[0], pt[1])),
				strokeWeight: 2,
				fillOpacity: 0.4,
				map: map.mapLayer
         })
      }
      map.mapLayer.fitBounds(llBounds)
   }
   function initialize() {
      //Load Server Data from HTML Data Attributes
      $('.service-container').each(function() {
         let container = $(this);
         voucherCoords = container.data('voucher-coords');
         checklistMeta = container.data('checklist-meta');
      })

      //Load Polygon
      if(checklistMeta['footprintwkt']) {
         try {
            checklistPolygon = parseWkt(checklistMeta['footprintwkt']);
         } catch(e) {
            checklistPolygon = false;
            alert(`Couldn't load Checklist Footprint: ${e}`)
         }
      }
      if("<?php echo $LEAFLET?>") {
         leafletInit()
      } else {
         googleInit();
      }
   }

   function openIndPU(occId){
      if(puWin != null) puWin.close();
      var puWin = window.open('../collections/individual/index.php?occid='+occId,'indspec' + occId,'scrollbars=1,toolbar=0,resizable=1,width=900,height=600,left=20,top=20');
      if(puWin.opener == null) puWin.opener = self;
      setTimeout(function () { puWin.focus(); }, 0.5);
      return false;
   }
</script>
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
	<div id='map_canvas'></div>
   <div class="service-container" 
      data-voucher-coords="<?= htmlspecialchars(json_encode($coordArr, 4)) ?>"
      data-checklist-meta="<?= htmlspecialchars(json_encode($clMeta, 2)) ?>"
      data-taxon-filter="<?= htmlspecialchars($taxonFilter) ?>"
      data-thes-filter="<?= htmlspecialchars($thesFilter) ?>"
      data-clid="<?= htmlspecialchars($clid) ?>"
   />
	<style>
		html, body, #map_canvas {
			width: 100%;
			height: 100%;
			margin: 0;
			padding: 0;
		}
	</style>
</body>
</html>
