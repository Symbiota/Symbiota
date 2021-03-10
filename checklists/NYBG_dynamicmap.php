<?php
include_once('../config/symbini.php');
//include_once($SERVER_ROOT.'/classes/DynamicChecklistManager.php');
header('Content-Type: text/html; charset='.$CHARSET);

$tid = array_key_exists('tid',$_REQUEST)?$_REQUEST['tid']:0;
$taxa = array_key_exists('taxa',$_REQUEST)?$_REQUEST['taxa']:'';
$interface = array_key_exists('interface',$_REQUEST)&&$_REQUEST['interface']?$_REQUEST['interface']:'checklist';

//Sanitation
if(!is_numeric($tid)) $tid = 0;
$taxa = filter_var($taxa,FILTER_SANITIZE_STRING);
if($interface && $interface != 'key') $interface = 'checklist';

//$dynClManager = new DynamicChecklistManager();

$latCen = 41.0;
$longCen = -95.0;
$coorArr = explode(";","40.938972;-73.671203;40.482417;-74.312054");
if($coorArr && count($coorArr) == 4){
	$latCen = ($coorArr[0] + $coorArr[2])/2;
	$longCen = ($coorArr[1] + $coorArr[3])/2;
}
$coordRange = 50;
if($coorArr && count($coorArr) == 4) $coordRange = ($coorArr[0] - $coorArr[2]);
$zoomInt = 5;
if($coordRange < 20){
	$zoomInt = 6;
}
elseif($coordRange > 35 && $coordRange < 40){
	$zoomInt = 4;
}
elseif($coordRange > 40){
	$zoomInt = 3;
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> - Dynamic Checklist Generator</title>
	<?php
    $activateJQuery = true;
    if(file_exists($SERVER_ROOT.'/includes/head.php')){
      include_once($SERVER_ROOT.'/includes/head.php');
    }
    else{
      echo '<link href="'.$CLIENT_ROOT.'/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
      echo '<link href="'.$CLIENT_ROOT.'/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
      echo '<link href="'.$CLIENT_ROOT.'/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
    }
	?>
	<script src="../js/jquery.js" type="text/javascript"></script>
	<script src="../js/jquery-ui.js" type="text/javascript"></script>
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
	<script src="//maps.googleapis.com/maps/api/js?<?php echo (isset($GOOGLE_MAP_KEY) && $GOOGLE_MAP_KEY?'key='.$GOOGLE_MAP_KEY:''); ?>"></script>

	<script type="text/javascript">
	    var map;
	    var currentMarker;
	  	var zoomLevel = 5;
	  	var submitCoord = false;

        $(document).ready(function() {
        	$( "#taxa" ).autocomplete({
        		source: function( request, response ) {
        			$.getJSON( "rpc/uppertaxasuggest.php", { term: request.term }, response );
        		},
        		minLength: 2,
        		autoFocus: true,
        		select: function( event, ui ) {
        			if(ui.item){
        				$( "#tid" ).val(ui.item.id);
        			}
				}
        	});

        });

	    function initialize(){
	    	var dmLatLng = new google.maps.LatLng(<?php echo $latCen.",".$longCen; ?>);
	    	var dmOptions = {
				zoom: <?php echo $zoomInt; ?>,
				center: dmLatLng,
				mapTypeId: google.maps.MapTypeId.TERRAIN
			};

	    	map = new google.maps.Map(document.getElementById("map_canvas"), dmOptions);

			google.maps.event.addListener(map, 'click', function(event) {
	            mapZoom = map.getZoom();
	            startLocation = event.latLng;
	            setTimeout("placeMarker()", 500);
	        });
	    }

	    function placeMarker() {
			if(currentMarker) currentMarker.setMap();
	        if(mapZoom == map.getZoom()){
	            var marker = new google.maps.Marker({
	                position: startLocation,
	                map: map
	            });
				currentMarker = marker;

		        var latValue = startLocation.lat();
		        var lonValue = startLocation.lng();
		        latValue = latValue.toFixed(5);;
		        lonValue = lonValue.toFixed(5);
				document.getElementById("latbox").value = latValue;
                document.getElementById("lngbox").value = lonValue;
                document.getElementById("latlngspan").innerHTML = latValue + ", " + lonValue;
                document.mapForm.buildchecklistbutton.disabled = false;
                submitCoord = true;
			}
	    }

		function checkForm(){
			if(submitCoord) return true;
			alert("You must first click on map to capture coordinate points");
			return false;
		}
	</script>
</head>
<body style="background-color:#ffffff;" onload="initialize()">
	<?php
		$displayLeftMenu = false;
		include($SERVER_ROOT.'/includes/header.php');
		if(isset($checklists_dynamicmapCrumbs)){
			if($checklists_dynamicmapCrumbs){
				echo "<div class='navpath'>";
				echo "<a href='../index.php'>Home</a> &gt; ";
				echo $checklists_dynamicmapCrumbs;
				echo "<b>NYBG > Dynamic Map</b>";
				echo "</div>";
			}
		}
		else{
			?>
			<div class='navpath'>
				<a href='../index.php'>Home</a> &gt;
				<b>NYBG > Dynamic Map</b>
			</div>
			<?php
		}
		?>
		<div id='innertext'>
			<div>
				Pan, zoom and click on map to capture coordinates, then submit coordinates to build a species list.
				<span id="moredetails" style="cursor:pointer;color:blue;font-size:80%;" onclick="this.style.display='none';document.getElementById('moreinfo').style.display='inline';document.getElementById('lessdetails').style.display='inline';">
					More Details
				</span>
				<span id="moreinfo" style="display:none;">
					If a radius is defined, species lists are generated using specimen data collected within the defined area.
					If a radius is not supplied, the area is sampled in concentric rings until the sample size is determined to
					best represent the local species diversity. In other words, poorly collected areas will have a larger radius sampled.
					Setting the taxon filter will limit the return to species found within that taxonomic group.
				</span>
				<span id="lessdetails" style="cursor:pointer;color:blue;font-size:80%;display:none;" onclick="this.style.display='none';document.getElementById('moreinfo').style.display='none';document.getElementById('moredetails').style.display='inline';">
					Less Details
				</span>
			</div>
			<div style="margin-top:5px;">
				<form name="mapForm" action="dynamicchecklist.php" method="post" onsubmit="return checkForm();">
					<div style="float:left;width:300px;">
						<div>
							<input type="submit" name="buildchecklistbutton" value="Build Checklist" disabled />
							<input type="hidden" name="interface" value="<?php echo $interface; ?>" />
							<input type="hidden" id="latbox" name="lat" value="" />
							<input type="hidden" id="lngbox" name="lng" value="" />
						</div>
						<div>
							<b>Point (Lat, Long):</b>
							<span id="latlngspan"> &lt; Click on map &gt; </span>
						</div>
					</div>
					<div style="float:left;">
						<div style="margin-right:35px;">
							<b>Taxon Filter:</b> <input id="taxa" name="taxa" type="text" value="<?php echo $taxa; ?>" />
							<input id="tid" name="tid" type="hidden" value="<?php echo $tid; ?>" />
						</div>
						<div>
							<b>Radius:</b>
							<input name="radius" value="(optional)" type="text" style="width:140px;" onfocus="this.value = ''" />
							<select name="radiusunits">
								<option value="km">Kilometers</option>
								<option value="mi">Miles</option>
							</select>
						</div>
					</div>
				</form>
			</div>
			<div id='map_canvas' style='width:95%; height:650px; clear:both;'></div>
		</div>
	<?php
 	include_once($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>