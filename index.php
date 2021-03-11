<?php
include_once('config/symbini.php');
include_once('content/lang/index.'.$LANG_TAG.'.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Home</title>
	<?php
	$activateJQuery = true;
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<link href="css/quicksearch.css" type="text/css" rel="Stylesheet" />
	<script src="js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="js/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
	<script src="js/symb/api.taxonomy.taxasuggest.js" type="text/javascript"></script>
	<script type="text/javascript">
		<?php include_once($SERVER_ROOT.'/includes/googleanalytics.php'); ?>
	</script>
	<style>
		#slideshowcontainer{
			border: 2px solid black;
			border-radius:10px;
			padding:10px;
			margin-left: auto;
			margin-right: auto;
		}
	</style>
</head>
<body>
	<?php
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<!-- This is inner text! -->
	<div id="innertext">
		<div style="text-align:center">
					<img src="<?php echo $CLIENT_ROOT; ?>/images/layout/BIGMAP.png" style="margin:0px 0px;width:800px" />
				</div>
			<div style="text-align:center">
			<p> This is the homepage for the North American EcoFloras project. EcoFloras, prototyped by the New York Botanical Garden and supported by a National Leadership grant from the Institute of Museum and Library Services, are innovative models for connecting people to plant collections, nature, and urban biodiversity.</p>
			<p> The projects combine existing knowledge from herbaria and libraries with real-time observations of plants and their ecological partners.</p>
			<p> EcoFloras' goals are:</p>
			<p> 1) to better understand urban ecosystems and urbanization, 2) democratize biodiversity data, and 3) increase the understanding and appreciation of plant life.</p>
			<p>Participants are encouraged to explore their communities and record observations using iNaturalist or Budburst. Exploration of urban biodiversity supports increased environmental literacy and fosters public appreciation of the natural world, while engaging urban residents in local conservation advocacy</p>
			<p>Currently there are 5 participating gardens. Visit the "Project Information" pages to learn more about their programs and information. </p>
		</div>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>