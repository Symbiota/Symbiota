<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceGeorefTools.php');
header("Content-Type: text/html; charset=".$CHARSET);

$country = array_key_exists('country',$_REQUEST)?$_REQUEST['country']:'';
$state = array_key_exists('state',$_REQUEST)?$_REQUEST['state']:'';
$county = array_key_exists('county',$_REQUEST)?$_REQUEST['county']:'';
$locality = array_key_exists('locality',$_REQUEST)?$_REQUEST['locality']:'';
$searchType = array_key_exists('searchtype',$_POST)?$_POST['searchtype']:1;
$collType = array_key_exists('colltype',$_POST)?$_POST['colltype']:0;
$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$submitAction = array_key_exists('submitaction',$_POST)?$_POST['submitaction']:'';

//Remove country, state, county from beginning of string
if(!$country || !$state || !$county){
	$locArr = explode(";",$locality);
	$locality = trim(array_pop($locArr));
	//if(!$country && $locArr) $country = trim(array_shift($locArr));
	//if(!$state && $locArr) $state = trim(array_shift($locArr));
	//if(!$county && $locArr) $county = trim(array_shift($locArr));
}
$locality = trim(preg_replace('/[\[\]\)\d\.\-,\s]*$/', '', $locality),'( ');

$geoManager = new OccurrenceGeorefTools();

$clones = $geoManager->getGeorefClones($locality, $country, $state, $county, $searchType, ($collType?$collid:'0'));

$latCen = 41.0;
$lngCen = -95.0;
$coorArr = explode(";",$mappingBoundaries);
if($coorArr && count($coorArr) == 4){
	$latCen = ($coorArr[0] + $coorArr[2])/2;
	$lngCen = ($coorArr[1] + $coorArr[3])/2;
}

?>
<html>
	<head>
		<title>Georeference Clone Tool</title>
		<?php
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<script src="//www.google.com/jsapi"></script>
		<script src="//maps.googleapis.com/maps/api/js?<?php echo (isset($GOOGLE_MAP_KEY) && $GOOGLE_MAP_KEY?'key='.$GOOGLE_MAP_KEY:''); ?>"></script>
		<script type="text/javascript">
			var map;
			var infoWins = new Array();

			function initialize(){
				var dmLatLng = new google.maps.LatLng(<?php echo $latCen.",".$lngCen; ?>);
				var dmOptions = {
					zoom: 3,
					center: dmLatLng,
					mapTypeId: google.maps.MapTypeId.TERRAIN,
					scaleControl: true
				};
				map = new google.maps.Map(document.getElementById("map_canvas"), dmOptions);

				<?php
				$minLng = 180;
				$minLat = 90;
				$maxLng = -180;
				$maxLat = -90;

				foreach($clones as $id => $occArr){
					if($occArr['lat'] < $minLat) $minLat = $occArr['lat'];
					if($occArr['lat'] > $maxLat) $maxLat = $occArr['lat'];
					if($occArr['lng'] < $minLng) $minLng = $occArr['lng'];
					if($occArr['lng'] > $maxLng) $maxLng = $occArr['lng'];

					$outStr = '<div>'.$occArr['lat'].' '.$occArr['lng'].' ';
					if($occArr['err']) $outStr .= ' (+-'.$occArr['err'].'m)';
					if($occArr['georefby']) $outStr .= '<br/>Georeferenced by: '.$occArr['georefby'];
					$outStr .= '<br/>'.$occArr['cnt'].' matching records<br/>';
					$outStr .= $occArr['locality'].'<br/>';
					$outStr .= "<a href='#' onclick='cloneCoord(".$occArr['lat'].','.$occArr['lng'].','.($occArr['err']?$occArr['err']:'0').")' title='Clone Coordinates'><b>Use Coordinates</b></a>";
					$outStr .= '</div>';
					?>
					var m<?php echo $id; ?> = new google.maps.Marker({
						position: new google.maps.LatLng(<?php echo $occArr['lat'].','.$occArr['lng']; ?>),
						map: map
					});

					google.maps.event.addListener(m<?php echo $id; ?>, 'click', function(){
						for( var w = 0; w < infoWins.length; w++ ) {
							var win = infoWins[w];
							win.close();
						}
						var iWin = new google.maps.InfoWindow({ content: <?php echo '"'.$outStr.'"'; ?> });
						infoWins.push( iWin );
						iWin.open(map,m<?php echo $id; ?>);
					});
					<?php
				}
				?>

				var swLatLng = new google.maps.LatLng(<?php echo $minLat.','.$minLng; ?>);
				var neLatLng = new google.maps.LatLng(<?php echo $maxLat.','.$maxLng; ?>);
				var llBounds = new google.maps.LatLngBounds(swLatLng, neLatLng);
				map.fitBounds(llBounds);
			}

			function cloneCoord(lat,lng,err){
				try{
					if(err == 0) err = "";
					opener.document.getElementById("decimallatitude").value = lat;
					opener.document.getElementById("decimallongitude").value = lng;
					opener.document.getElementById("coordinateuncertaintyinmeters").value = err;
					opener.document.getElementById("decimallatitude").onchange();
					opener.document.getElementById("decimallongitude").onchange();
					opener.document.getElementById("coordinateuncertaintyinmeters").onchange();
				}
				catch(myErr){
				}
				finally{
					self.close();
					return false;
				}
			}

			function verifyCloneForm(f){
				if(f.locality.value == ""){
					alert("Locality field must have a value");
					return false
				}
				if(document.getElementById("deepsearch").checked == true){
					var locArr = f.locality.value.split(" ");
					if(locArr.length > 4){
						alert("Locality field cannot contain more than 4 words while doing a Deep Search. Just enter a few keywords.");
						return false
					}
				}
				return true;
			}
		</script>
	</head>
	<body style="background-color:#ffffff;" onload="initialize()">
		<!-- This is inner text! -->
		<div id="innertext">
			<fieldset style="padding:10px;">
				<legend><b>Search Form</b></legend>
				<form name="cloneform" action="georefclone.php" method="post" onsubmit="return verifyCloneForm(this)">
					<div>
						Locality:
						<input name="locality" type="text" value="<?php echo $locality; ?>" style="width:600px" />
					</div>
					<div>
						<input id="exactinput" name="searchtype" type="radio" value="1" <?php echo ($searchType=='1'?'checked':''); ?> /> Exact Match
						<input id="wildsearch" name="searchtype" type="radio" value="2" <?php echo ($searchType=='2'?'checked':''); ?> /> Contains
						<input id="deepsearch" name="searchtype" type="radio" value="3" <?php echo ($searchType=='3'?'checked':''); ?> /> Deep Search
					</div>
					<?php
					if($collid){
						?>
						<div>
							<input name="colltype" type="radio" value="0" <?php echo ($collType?'':'checked'); ?> /> Search all collections
							<input name="colltype" type="radio" value="1" <?php echo ($collType?'checked':''); ?> /> Target collection only
						</div>
						<?php
					}
					?>
					<div style="float:left;margin:5px 20px;">
						<input name="country" type="hidden" value="<?php echo $country; ?>" />
						<input name="state" type="hidden" value="<?php echo $state; ?>" />
						<input name="county" type="hidden" value="<?php echo $county; ?>" />
						<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
						<input name="submitaction" type="submit" value="Search" />
					</div>
				</form>
			</fieldset>
			<?php
			if($clones){
				?>
				<div style="margin:3px;font-weight:bold;">
					Click on markers to view and clone coordinates
				</div>
				<div id='map_canvas' style='width:750px; height:600px; clear:both;'></div>
				<?php
			}
			else{
				?>
				<div style="margin:30px"><h2>Search failed to return specimen matches</h2></div>
				<?php
			}
			?>
		</div>
	</body>
</html>
