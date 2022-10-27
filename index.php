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
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<link href="css/quicksearch.css" type="text/css" rel="Stylesheet" />
	<script src="js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="js/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
	<link href="js/jquery-ui/jquery-ui.min.css" type="text/css" rel="Stylesheet" />
	<script type="text/javascript">
		var clientRoot = "<?php echo $CLIENT_ROOT; ?>";
	</script>
	<script src="js/symb/api.taxonomy.taxasuggest.js" type="text/javascript"></script>
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
		<h1></h1>
		<div id="quicksearchdiv">
			<!-- -------------------------QUICK SEARCH SETTINGS--------------------------------------- -->
			<form name="quicksearch" id="quicksearch" action="<?php echo $CLIENT_ROOT; ?>/taxa/index.php" method="get" onsubmit="return verifyQuickSearch(this);">
				<div id="quicksearchtext" ><?php echo (isset($LANG['QSEARCH_SEARCH'])?$LANG['QSEARCH_SEARCH']:'Taxon Search'); ?></div>
				<input id="taxa" type="text" name="taxon" />
				<button name="formsubmit"  id="quicksearchbutton" type="submit" value="Search Terms"><?php echo (isset($LANG['QSEARCH_SEARCH_BUTTON'])?$LANG['QSEARCH_SEARCH_BUTTON']:'Search'); ?></button>
			</form>
		</div>
		<div style="padding: 0px 10px;">
			<p>
				The <a href="https://www.dmns.org/science/earth-sciences/projects/madagascar-paleontology-projects/">Madagascar Paleontology Project (MPP)</a>, initiated in 1993, is designed to elucidate the biogeographic and plate tectonic history of the southern supercontinent of Gondwana in general and the 
				island of Madagascar in particular. Our paleontological discoveries in the Mahajanga Basin of northwestern Madagascar during the course of 13 highly successful expeditions as well as 
				recent expansion into the Ambilobe Basin in northernmost Madagascar and the Morondava Basin in western Madagascar have established the island as having some of the most complete and 
				scientifically significant specimens of Cretaceous vertebrate animals from all of Gondwana and, indeed, the world. The discoveries in the Mahajanga Basin, most of them from a small area in 
				the Maevarano Formation near the village of Berivotra, have more than quintupled the previously known diversity of Late Cretaceous vertebrates from Madagascar and now include specimens of 
				fishes, frogs, turtles, lizards, snakes, crocodyliforms, pterosaurs, non-avian dinosaurs, birds, and mammals. Many of the specimens recovered represent animals that are new to science––we have 
				named and described 20 new taxa––and include complete skulls and skeletons that are the most complete for entire clades.
			</p>
			<button>
			<img src="/madpaleo/portal/images/layout/madpaleofooterbanner.png" style="width:100%">
		</div>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
