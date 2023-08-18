<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceMapManager.php');
header("Content-Type: text/html; charset=".$CHARSET);

$clid = array_key_exists('clid',$_REQUEST)?$_REQUEST['clid']:0;
$gridSize = array_key_exists('gridSizeSetting',$_REQUEST)?$_REQUEST['gridSizeSetting']:10;
$minClusterSize = array_key_exists('minClusterSetting',$_REQUEST)?$_REQUEST['minClusterSetting']:50;

$occurManager = new OccurrenceMapManager();
$coordArr = $occurManager->getMappingData(0);

//Build taxa mapping key
$taxaKey = Array();
$taxaArr = $occurManager->getTaxaArr();
if(array_key_exists('taxa', $taxaArr)){
	foreach($taxaArr['taxa'] as $scinameStr => $snArr){
		if(isset($snArr['tid'])){
			$snTid = key($snArr['tid']);
			$taxaKey[$snTid]['t'] = $scinameStr;
			if(array_key_exists('TID_BATCH', $snArr)){
				foreach($snArr['TID_BATCH'] as $synTid => $synValue){
					$taxaKey[$synTid]['s'] = $snTid;
				}
			}
			if(array_key_exists('synonyms', $snArr)){
				foreach($snArr['synonyms'] as $synTid => $synSciname){
					$taxaKey[$synTid]['s'] = $snTid;
					$taxaKey[$synTid]['t'] = $synSciname;
				}
			}
		}
	}
}

$markerCnt = 0;
$spCnt = 0;
$minLng = 180; $minLat = 90; $maxLng = -180; $maxLat = -90;
$defaultColor = 'B2BEB5';
$iconColors = array('FC6355','5781FC','FCf357','00E13C','E14f9E','55D7D7','FF9900','7E55FC');
$legendArr = Array();
foreach($coordArr as $sciName => $valueArr){
   $tid = 0;
   if(array_key_exists('tid', $valueArr)){
      $tid = $valueArr['tid'];
      unset($valueArr['tid']);
      if(isset($taxaKey[$tid])){
         if(isset($taxaKey[$tid]['s'])){
            $correctedTid = $taxaKey[$tid]['s'];
            if(isset($taxaKey[$tid]['t'])) $legendArr[$correctedTid]['s'][] = $taxaKey[$tid]['t'];
            $tid = $correctedTid;
         }
         if(!isset($legendArr[$tid]['t'])){
            $legendArr[$tid]['t'] = $taxaKey[$tid]['t'];
            $legendArr[$tid]['c'] = $iconColors[(count($legendArr)%8)];
         }
      }
   }
   $iconColor = 0;
   if(isset($legendArr[$tid])){
      $iconColor = $legendArr[$tid]['c'];
      $legendArr[$tid]['points'][$spCnt] = $valueArr;
   }
   else{
      foreach($legendArr as $lTid => $legArr){
         if(isset($legArr['t']) && strpos($sciName, $legArr['t']) === 0){
            $iconColor = $legArr['c'];
            $legendArr[$lTid]['points'][$spCnt] = $valueArr;
            break;
         }
      }
      if(!$iconColor){
         foreach($taxaKey as $tkTid => $tkArr){
            if(isset($tkArr['t']) && strpos($sciName, $tkArr['t']) === 0){
               $legendArr[$tkTid]['t'] = $tkArr['t'];
               $iconColor = $iconColors[(count($legendArr)%8)];
               $legendArr[$tkTid]['c'] = $iconColor;
               $legendArr[$tkTid]['points'][$spCnt] = $valueArr;
               break;
            }
         }
      }
      if(!$iconColor){
         $legendArr['last']['c'] = $defaultColor;
         $legendArr['last']['points'][$spCnt] = $valueArr;
         $iconColor = $defaultColor;
      }
   }
   $spCnt++;
}

//Loop Through all coords have color, pos and display string
$boundLatMin = -90;
$boundLatMax = 90;
$boundLngMin = -180;
$boundLngMax = 180;
$latCen = 41.0;
$longCen = -95.0;
if(isset($MAPPING_BOUNDARIES)){
   $coorArr = explode(";",$MAPPING_BOUNDARIES);
   if($coorArr && count($coorArr) == 4){
      $boundLatMin = $coorArr[2];
      $boundLatMax = $coorArr[0];
      $boundLngMin = $coorArr[3];
      $boundLngMax = $coorArr[1];
      $latCen = ($boundLatMax + $boundLatMin)/2;
      $longCen = ($boundLngMax + $boundLngMin)/2;
   }
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> - Google Map</title>
	<?php
	   include_once($SERVER_ROOT.'/includes/head.php');
	   include_once($SERVER_ROOT.'/includes/leafletmap.php');
	?>
	<script type="text/javascript">
      let occurCoords;
      let colorLegend;
      let clid;
      let map;
		let useLLDecimal = true;

      function leafletInit() {
         L.DivIcon.CustomColor = L.DivIcon.extend({
            createIcon: function(oldIcon) {
               var icon = L.DivIcon.prototype.createIcon.call(this, oldIcon);
               icon.style.backgroundColor = this.options.color;
               return icon;
            }
         })

         let bounds = new L.featureGroup();

         map = new LeafletMap('map_canvas');

         const checkLatLng = (latlng) => {
            return (
               (!isNaN(latlng[0]) && latlng[0] <= 90 && latlng[0] >= -90) && 
               (!isNaN(latlng[1]) && latlng[1] <= 180 && latlng[1] >= -180)
            )
         }

         for(let tid of Object.keys(colorLegend)) {
            let colorGroup = colorLegend[tid]

            //Leaftlet Cluster Override
            function colorCluster(cluster) {
               let childCount = cluster.getChildCount();
               return new L.DivIcon.CustomColor({ 
               html: `<div style="background-color: #${colorGroup.c}CC;"><span>` + childCount + '</span></div>', 
                  className: 'marker-cluster', 
                  iconSize: new L.Point(40, 40),
                  color: `#${colorGroup.c}77`,
               });
            } 

            let taxaCluster = L.markerClusterGroup({
               iconCreateFunction: colorCluster 
            });

            for(let groupId of Object.keys(colorGroup.points)) {
               let taxaGroup = colorGroup.points[groupId];
               for(let occid of Object.keys(taxaGroup)) {
                  const occur = taxaGroup[occid];
                  const latlng = [parseFloat(occur.lat), parseFloat(occur.lng)];
                  let displayStr = `${occur.instcode}${occur.collcode}`;

                  if(!checkLatLng(latlng)) continue;

                  if(occur.catnum) {
                     if(!isNaN(occur.catnum)) {
                        displayStr = `${displayStr}-${occur.catnum}`;
                     } else {
                        displayStr = occur.catnum;
                     }
                  } else if(occur.collector) {
                     displayStr = `${displayStr}-${occur.collector}`;
                  } else if(occur.ocatnum) {
                     displayStr = `${displayStr}-${occur.ocatnum}`;
                  }

                  //Add marker based on occurence type
                  let marker = (occur.colltype === "spec"?
                  L.circleMarker(latlng, {
                     radius : 8,
                     color  : '#000000',
                     weight: 2,
                     fillColor: `#${colorGroup.c}`,
                     opacity: 1.0,
                     fillOpacity: 1.0
                  }):
                  L.marker(latlng, {
                     icon: getObservationSvg({
                        color: `#${colorGroup.c}`, 
                        size: 30
                     })
                  }))
                  .bindTooltip(`<div style="font-size:1.2rem">${displayStr}</div>`)
                  .on('click', function() { openIndPU(occid, clid) })

                  taxaCluster.addLayer(marker)
                  bounds.addLayer(marker)
               }
            }
            map.mapLayer.addLayer(taxaCluster);
         }
         map.mapLayer.fitBounds(bounds.getBounds());
      }

      function initialize() {
         try {
            let data = document.getElementById('service-container') 
            occurCoords = JSON.parse(data.getAttribute('data-occur-coords'));
            colorLegend = JSON.parse(data.getAttribute('data-legend'))
            clid = JSON.parse(data.getAttribute('data-clid'))
         } catch (err) {
            alert("Failed to load occurence data")
         }
         //Keeping Google and leaflet files seperate for sake of saving repeat
         //work when trying to move away from google maps.
         leafletInit();
      }

      function addRefPoint() {
			let lat = document.getElementById("lat").value;
		   let lng = document.getElementById("lng").value;
			let title = document.getElementById("title").value;

			if(!useLLDecimal){
				var latdeg = document.getElementById("latdeg").value;
				var latmin = document.getElementById("latmin").value;
				var latsec = document.getElementById("latsec").value;
				var latns = document.getElementById("latns").value;
				var longdeg = document.getElementById("longdeg").value;
				var longmin = document.getElementById("longmin").value;
				var longsec = document.getElementById("longsec").value;
				var longew = document.getElementById("longew").value;
				if(latdeg != null && longdeg != null){
					if(latmin == null) latmin = 0;
					if(latsec == null) latsec = 0;
					if(longmin == null) longmin = 0;
					if(longsec == null) longsec = 0;
					lat = latdeg*1 + latmin/60 + latsec/3600;
					lng = longdeg*1 + longmin/60 + longsec/3600;
					if(latns == "S") lat = lat * -1;
					if(longew == "W") lng = lng * -1;
				}
			}

			if(lat === null && lng === null){
				window.alert("Enter values in the latitude and longitude fields");
         } else if(lat < -180 || lat > 180 || lng < -180 || lng > 180) {
					window.alert("Latitude and Longitude must be of values between -180 and 180 (" + lat + ";" + lng + ")");
         } else {
            var addPoint = true;
            if(lng > 0) addPoint = window.confirm("Longitude is positive, which will put the marker in the eastern hemisphere (e.g. Asia).\nIs this what you want?");
            if(!addPoint) lng = -1*lng;

            console.log(title)

            map.mapLayer.addLayer(
               L.marker([lat, lng])
               .bindTooltip(`<div style="font-size:1.2rem">${title}</div>`)
            )
         }
      }

		function toggleLatLongDivs(){
			var divs = document.getElementsByTagName("div");
			for (i = 0; i < divs.length; i++) {
				var obj = divs[i];
				if(obj.getAttribute("class") == "latlongdiv" || obj.getAttribute("className") == "latlongdiv"){
					if(obj.style.display=="none"){
						obj.style.display="block";
					}
					else{
						obj.style.display="none";
					}
				}
			}
			if(useLLDecimal){
				useLLDecimal = false;
			}
			else{
				useLLDecimal = true;
			}
		}

		function openIndPU(occId,clid){
			newWindow = window.open('../individual/index.php?occid='+occId+'&clid='+clid,'indspec' + occId,'scrollbars=1,toolbar=0,resizable=1,width=1100,height=800,left=20,top=20');
			if (newWindow.opener == null) newWindow.opener = self;
			setTimeout(function () { newWindow.focus(); }, 0.5);
	}

	</script>
</head>
<body style="width:100%; min-width: 900px" onload="initialize();">
	<?php
	if(!$coordArr){
		?>
			<div style="font-size:120%;font-weight:bold;">
				Your query apparently does not contain any records with coordinates that can be mapped.
			</div>
			<div style="margin-left:20px;">
				Either the records in the query are not georeferenced (no lat/long)<br/>
			</div>
			<div style="margin-left:100px;">
				-or-
			</div>
			<div style="margin-left:20px;">
				Rare/threatened status requires the locality coordinates be hidden.
			</div>
		<?php
	}
?>
   <div id="service-container" 
      data-occur-coords="<?= htmlspecialchars(json_encode($coordArr, 4)) ?>"
      data-clid="<?= htmlspecialchars($clid) ?>"
      data-legend="<?= htmlspecialchars(json_encode($legendArr)) ?>"
   />
	<div id="map_canvas" style="width:100%;height:80vh"></div>
	<div style="width:500px;float:left;">
		<fieldset>
			<legend>Legend</legend>
			<div style="float: left; margin-right: 25px; margin-bottom: 10px">
				<?php
				$tailItem = '';
				foreach($legendArr as $subArr){
					echo '<div>';
					if(isset($subArr['t'])){
						echo '<svg xmlns="http://www.w3.org/2000/svg" style="height:12px;width:12px;margin-bottom:-2px;"><g><rect x="1" y="1" width="11" height="10" fill="#'.$subArr['c'].'" stroke="#000000" stroke-width="1px" /></g></svg> ';
						echo '= <i>'.$subArr['t'].'</i> ';
						if(isset($subArr['s'])) echo ' ('.implode(', ', $subArr['s']).')';
					}
					else{
						$tailItem = '<div>';
						$tailItem .= '<svg xmlns="http://www.w3.org/2000/svg" style="height:12px;width:12px;margin-bottom:-2px;"><g><rect x="1" y="1" width="11" height="10" fill="#'.$subArr['c'].'" stroke="#000000" stroke-width="1px" /></g></svg> ';
						$tailItem .= '= non-indexed taxa';
						$tailItem .= '</div>';
					}
					echo '</div>';
				}
				echo $tailItem;
				?>
			</div>
			<div style="float: left;">
				<div>
					<svg xmlns="http://www.w3.org/2000/svg" style="height:15px;width:15px;margin-bottom:-2px;">">
						<g>
							<circle cx="7.5" cy="7.5" r="7" fill="white" stroke="#000000" stroke-width="1px" ></circle>
						</g>
					</svg> = Collection
				</div>
				<div>
					<svg style="height:14px;width:14px;margin-bottom:-2px;">" xmlns="http://www.w3.org/2000/svg">
						<g>
							<path stroke="#000000" d="m6.70496,0.23296l-6.70496,13.48356l13.88754,0.12255l-7.18258,-13.60611z" stroke-width="1px" fill="white"/>
						</g>
					</svg> = Observation
				</div>
			</div>
		</fieldset>
	</div>
	<div style="width:400px;float:left;">
		<fieldset>
			<legend>Add Point of Reference</legend>
			<div>
				<div>
					Marker Name: <input name='title' id='title' size='15' type='text' />
				</div>
				<div class="latlongdiv">
					<div>
						<div style="float:left;margin-right:5px">Latitude decimal: <input name='lat' id='lat' size='10' type='text' /> </div>
						<div style="float:left;">eg: 34.57</div>
					</div>
					<div style="margin-top:5px;clear:both">
						<div style="float:left;margin-right:5px">Longitude decimal: <input name='lng' id='lng' size='10' type='text' /> </div>
						<div style="float:left;">eg: -112.38</div>
					</div>
					<div style='font-size:80%;margin-top:5px;clear:both'>
						<a href='#' onclick='toggleLatLongDivs();'>Enter in D:M:S format</a>
					</div>
				</div>
				<div class='latlongdiv' style='display:none;clear:both'>
					<div>
						Latitude:
						<input name='latdeg' id='latdeg' size='2' type='text' />&deg;
						<input name='latmin' id='latmin' size='4' type='text' />&prime;
						<input name='latsec' id='latsec' size='4' type='text' />&Prime;
						<select name='latns' id='latns'>
							<option value='N'>N</option>
							<option value='S'>S</option>
						</select>
					</div>
					<div style="margin-top:5px;">
						Longitude:
						<input name='longdeg' id='longdeg' size='2' type='text' />&deg;
						<input name='longmin' id='longmin' size='4' type='text' />&prime;
						<input name='longsec' id='longsec' size='4' type='text' />&Prime;
						<select name='longew' id='longew'>
							<option value='E'>E</option>
							<option value='W' selected>W</option>
						</select>
					</div>
					<div style='font-size:80%;margin-top:5px;'>
						<a href='#' onclick='toggleLatLongDivs();'>Enter in Decimal format</a>
					</div>
				</div>
				<div style="margin-top:10px;">
					<input type='submit' value='Add Marker' onclick='addRefPoint();' />
				</div>
			</div>
		</fieldset>
	</div>
</body>
</html>

