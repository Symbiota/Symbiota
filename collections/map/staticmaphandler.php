<?php
include_once('../../config/symbini.php');
//include_once($SERVER_ROOT.'/classes/MapSupport.php');
header('Content-Type: text/html; charset=' . $CHARSET);

$mapManager = new MapSupport();

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
?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">
<head>
	<title><?php echo $DEFAULT_TITLE; ?> - Static distribution map generator</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/leafletmap.php');
	?>
	<script type="text/javascript">
		<?php
		if($IS_ADMIN){
			//Only display JS if user is logged with SuperAdmin permissions
			?>
			Form actions will trigger JS to do following via AJAX:
			1) Grab accepted taxa list (currently set to return 1000 records at a time)
			2) For each taxon:
				a) grab coordinates to be mapped
				b) Create map image using LeafLet
				c) Post image to server and save URL to database

			<?php
		}
		?>
	</script>
</head>
<body>
	<div id="innertext">
		<form name="thumbnailBuilder" method="post" action="">
			Form only triggers JS functions, which processes data via AJAX functions wtihin the above list


			Form options to be added now:
				- map type (radio button): heat map, dot map
				- bounding box (set of text boxes): fields filled with above default bounding box values, but provides user ability to adjust. Maybe add the bounding box assist tool to help user define a new box?
				- replace (radio button): all maps, maps of set type (heat or dot), none
				- Target a specific taxon (text box with autocomplete that displays only accepted taxa of rankid 220 or greater)
			Form options to add later:
				- replace maps older than a certain date (date text box)


			<button type="button" onclick="buildMaps()"><?= $LANG['BUILDMAPS'] ?></button>
		</form>
	</div>
</body>
</html>

