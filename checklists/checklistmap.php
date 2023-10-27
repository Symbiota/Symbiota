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
	//include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');

	// If checklist is associated with an external service (i.e., iNaturalist), transfer some server-side data to client-side
	if($clMeta['dynamicProperties']){
		$dynamPropsArr = json_decode($clMeta['dynamicProperties'], true);
		if(isset($dynamPropsArr['externalservice']) && $dynamPropsArr['externalservice'] == 'inaturalist') {
			echo '<script src="../js/symb/checklists.externalserviceapi.js"></script>';
			echo '<script>';
			echo 'const urltail = ".grid.json?mappable=true&project_id='. ($dynamPropsArr['externalserviceid']?$dynamPropsArr['externalserviceid']:'').'&rank='. ($dynamPropsArr['externalservicerank']?$dynamPropsArr['externalservicerank']:'species').'&iconic_taxa='. ($dynamPropsArr['externalserviceiconictaxon']?$dynamPropsArr['externalserviceiconictaxon']:'').'&quality_grade='. ($dynamPropsArr['externalservicegrade']?$dynamPropsArr['externalservicegrade']:'research').'&order=asc&order_by=updated_at";';
			echo 'const inatprojid = "'. ($dynamPropsArr['externalserviceid']?$dynamPropsArr['externalserviceid']:'') .'";';
			echo 'const inaticonic = "'. ($dynamPropsArr['externalserviceiconictaxon']?$dynamPropsArr['externalserviceiconictaxon']:'') .'";';
			echo 'const inatNE = "'.($dynamPropsArr['externalservicene']?$dynamPropsArr['externalservicene']:'') .'";';
			echo 'const inatSW = "'.($dynamPropsArr['externalservicesw']?$dynamPropsArr['externalservicesw']:'') .'";';
			echo 'const latcentroid = '.($clMeta['latcentroid']?$clMeta['latcentroid']:0) .';';
			echo 'const longcentroid = '.($clMeta['longcentroid']?$clMeta['longcentroid']:0) .';';
			echo 'const pointradiusmeters = '.($clMeta['pointradiusmeters']?$clMeta['pointradiusmeters']:0) .';';
			echo '</script>';
		}

	} 
	?>
	<script src="//maps.googleapis.com/maps/api/js?v=3.exp&libraries=drawing<?php echo (isset($GOOGLE_MAP_KEY) && $GOOGLE_MAP_KEY?'&key='.$GOOGLE_MAP_KEY:''); ?>&callback=Function.prototype"></script>
	<script type="text/javascript">
		var map;
		var puWin;

		function initialize(){
			var dmOptions = {
				zoom: 3,
				center: new google.maps.LatLng(41,-95),
				mapTypeId: google.maps.MapTypeId.TERRAIN,
				scaleControl: true
			};

			var llBounds = new google.maps.LatLngBounds();
			<?php
			if($coordArr){
				?>
				map = new google.maps.Map(document.getElementById("map_canvas"), dmOptions);
				var vIcon = new google.maps.MarkerImage("../images/google/smpin_red.png");
				var pIcon = new google.maps.MarkerImage("../images/google/smpin_blue.png");
				var inatIcon = new google.maps.MarkerImage("../images/google/smpin_green.png");
				<?php
				$mCnt = 0;
				foreach($coordArr as $tid => $cArr){
					foreach($cArr as $pArr){
						$llArr = explode(',', $pArr['ll']);
						if(trim($llArr[0]) != 0 && trim($llArr[1]) != 0) {
							// This is a preventative measure to reduce the chance of points in table fmchklstcoordinates 
							// without legitimate coordinates (i.e., 0, 0) from adversely affecting map center and extent.
							echo "var pt = new google.maps.LatLng(".$pArr['ll'].");";
							echo "llBounds.extend(pt);";
							if(array_key_exists('occid',$pArr)){
								echo 'var m'.$mCnt.' = new google.maps.Marker({position: pt, map:map, title:"'. $pArr['notes'].'", icon:vIcon});';
								echo 'google.maps.event.addListener(m'.$mCnt.', "click", function(){ openIndPU('.$pArr['occid'].'); });';
							}
							else{
								echo 'var m'.$mCnt.' = new google.maps.Marker({position: pt, map:map, title:"'.$pArr['sciname'].'", icon:pIcon});';
							}
							$mCnt++;
						}
					}
				}
			
				$clMeta = $clManager->getClMetaData();
				?>
				map.fitBounds(llBounds);
				map.panToBounds(llBounds);


				function ll2slippytile(lon, lat, zoom) {
					// https://wiki.openstreetmap.org/wiki/Slippy_map_tilenames#Lon..2Flat._to_tile_numbers_2
					let n = Math.pow(2, zoom);
					xtile = Math.floor( n * ((lon + 180) / 360) );
					ytile = Math.floor( n * (1 - (Math.log(Math.tan((lat * Math.PI/180)) + (1 / Math.cos((lat * Math.PI/180)))) / Math.PI)) / 2 );
					return {x:xtile, y:ytile};
				}

				async function requestTileCoords(tileurls) {
					const resps = await Promise.all(tileurls.map(async (url) => {
						const resp = await fetch(url);
						// Throttle to < 100 requests per minute as per iNaturalist API guidelines 
						await new Promise(governor => setTimeout(governor, 600));
						return resp;
					}));
					const resppromises = resps.map(result => result.json());
					const inatprojectpoints = await Promise.all(resppromises);
					let returnarr = []; // flatten all promise responses into single object
					for(const i in inatprojectpoints) { returnarr = Object.assign(returnarr, inatprojectpoints[i].data) }
					return returnarr;
				}


				let north = 90;
				let south = -90;
				let east = 180;
				let west = -180;
				if(pointradiusmeters != 0) {  // Try Symbiota bounds
					console.log("Using Symbiota bounds");
					const coordoffset = pointradiusmeters/111319.9;  // based on approximate length of a decimal degree at the equator, if change in distance with latitude is really critical, someone can get fancy with the math in the future
					north = latcentroid + coordoffset;
					south = latcentroid - coordoffset;
					east = longcentroid + coordoffset;
					west = longcentroid - coordoffset;
				} else if( (inatNE != '' || inatSW != '') && (inatNE != 0 && inatSW != 0) ) {  // Try iNat bounds
					console.log("Using iNaturalist bounds");
					let neArr = inatNE.split('|');
					let swArr = inatSW.split('|');
					north = parseFloat(neArr[0]);
					south = parseFloat(swArr[0]);
					east = parseFloat(neArr[1]);
					west = parseFloat(swArr[1]);
			
				} else {  // Use map extent
					console.log("Using current map bounds");
					const ne = llBounds.getNorthEast();
					const sw = llBounds.getSouthWest();
					north = ne.lat();
					south = sw.lat();
					east = ne.lng();
					west = sw.lng();
				}
				
				// Guesstimate approximate tile centers by taking the 25% and 75% positions in x and y
				const xdiff = Math.abs(east - west); 
				const ydiff = Math.abs(north - south);
				const diff = (xdiff + ydiff) / 2;
				let zoom = Math.round(Math.log2( 180/diff ));
				const x25 = west + (0.25 * xdiff);
				const x75 = east - (0.25 * xdiff);
				const y25 = south + (0.25 * ydiff);
				const y75 = north - (0.25 * ydiff);
				let nwtile = ll2slippytile(x25,y25,zoom);
				let setile = ll2slippytile(x75,y75,zoom);
				let netile = ll2slippytile(x75,y25,zoom);
				let swtile = ll2slippytile(x25,y75,zoom);


				// Optimize request based on a zoom level that will return 4 tiles within current bounds

				// Start with NW tile
				let alltileurls = [`https://api.inaturalist.org/v1/points/${zoom}/${nwtile['x']}/${nwtile['y']}${urltail}`];

				// Check rectangularity condition of llbounds converted to tiles (2x2, 2x1, 1x2)
				if(setile != nwtile) {
					// 1x1 check; theoretically, a 1x1 should just be a 2x2 at a lower zoom level
					alltileurls.push(`https://api.inaturalist.org/v1/points/${zoom}/${setile['x']}/${setile['y']}${urltail}`);
					if(netile != setile || nwtile != swtile) {
						// 1x2 check, if succeeds, then 2x2
						alltileurls.push(`https://api.inaturalist.org/v1/points/${zoom}/${netile['x']}/${netile['y']}${urltail}`);
						alltileurls.push(`https://api.inaturalist.org/v1/points/${zoom}/${swtile['x']}/${swtile['y']}${urltail}`);
					} else if(setile != swtile || netile != nwtile) {
						// 2x1 check, if succeeds, then 2x2
						alltileurls.push(`https://api.inaturalist.org/v1/points/${zoom}/${netile['x']}/${netile['y']}${urltail}`);
						alltileurls.push(`https://api.inaturalist.org/v1/points/${zoom}/${swtile['x']}/${swtile['y']}${urltail}`);
					} 
					// if either of above fail, then it's a 1x2 or 2x1. Using the nw + se tiles 
					// (requests already in place because the 1x1 check passed) will cover both cases.
				}

				requestTileCoords(alltileurls)
					.then(result => {
						var inatmarker;
						for( const inatid in result ) {
							let lat = result[inatid].latitude;
							let lon = result[inatid].longitude;
							let pt = new google.maps.LatLng(lat,lon);
							llBounds.extend(pt);
							inatmarker = new google.maps.Marker({
								position: pt,
								map: map, 
								title: "iNaturalist-" + inatid, 
								icon: inatIcon
							});
							google.maps.event.addListener(inatmarker, 'click', (function(inatmarker, inatid) {
								return function() {
									window.open(`https://www.inaturalist.org/observations/${inatid}`, '_blank')
								}
							})(inatmarker, inatid));
						}
						map.fitBounds(llBounds);
						map.panToBounds(llBounds);
					})
					.catch(error => {error.message;});

				<?php

				//Check for and add checklist polygon

				if($clMeta['footprintwkt']){
					//Add checklist polygon
					$wkt = $clMeta['footprintwkt'];
					if(substr($wkt,0,7) == 'POLYGON') $wkt = substr($wkt,7);
					else if(substr($wkt,0,12) == 'MULTIPOLYGON') $wkt = substr($wkt,12);
					$coordArr = explode('),(', $wkt);
					foreach($coordArr as $k => $polyFrag){
						if($pointArr = explode(',', trim($polyFrag,' (),'))){
							echo 'var polyPointArr'.$k.' = [];';
							foreach($pointArr as $pointStr){
								$llArr = explode(' ', trim($pointStr));
								if($llArr[0] > 90 || $llArr[0] < -90) break;
								?>
								var polyPt = new google.maps.LatLng(<?php echo $llArr[0].','.$llArr[1]; ?>);
								polyPointArr<?php echo $k; ?>.push(polyPt);
								llBounds.extend(polyPt);
								<?php
							}
							?>
							var footPoly<?php echo $k; ?> = new google.maps.Polygon({
								paths: polyPointArr<?php echo $k; ?>,
								strokeWeight: 2,
								fillOpacity: 0.4,
								map: map
							});
							<?php
						}
					}
				}
				?>
			<?php
			}
			?>
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
	<div id='map_canvas'></div>
</body>
</html>