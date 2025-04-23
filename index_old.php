<?php
include_once("config/symbini.php");
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Home</title>
	<?php
	$activateJQuery = true;
	include_once($SERVER_ROOT.'/includes/head.php');
        include_once('/includes/googleanalytics.php');
	?>
	<link href="css/quicksearch.css" type="text/css" rel="Stylesheet" />
	<script src="js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="js/jquery-ui-1.12.1/jquery-ui.js" type="text/javascript"></script>
	<script src="js/symb/plugin.quicksearch.js" type="text/javascript"></script>
	<script src="js/jquery.slides.js"></script>
</head>
<body>
	<?php
	include($SERVER_ROOT.'/includes/header.php');
	?> 
	<!-- This is inner text! -->
	<div id="innertext">
		<div style="float:right;margin-top:50px;">
			<div id="quicksearchdiv" style="margin:10px 30px;width:400px;height:50px">
				<form name="quicksearch" id="quicksearch" action="<?php echo $CLIENT_ROOT; ?>/taxa/index.php" method="get" onsubmit="return verifyQuickSearch(this);">
					<div id="quicksearchtext" ><?php echo (isset($LANG['QSEARCH_SEARCH'])?$LANG['QSEARCH_SEARCH']:'Search Taxon'); ?></div>
					<input type="text" name="taxon" id="quicksearchtaxon" />
					<button name="formsubmit"  id="quicksearchbutton" type="submit" value="Search Terms"><?php echo (isset($LANG['QSEARCH_SEARCH_BUTTON'])?$LANG['QSEARCH_SEARCH_BUTTON']:'Search'); ?></button>
				</form>
			</div>
			<div style="margin:15px;width:395px;margin-left:auto;margin-right:auto;">
				<?php
				//---------------------------SLIDESHOW SETTINGS---------------------------------------
				//If more than one slideshow will be active, assign unique numerical ids for each slideshow.
				//If only one slideshow will be active, leave set to 1. 
				$ssId = 1; 

				//Enter number of images to be included in slideshow (minimum 5, maximum 10) 
				$numSlides = 10;

				//Enter width of slideshow window (in pixels, minimum 275, maximum 800)
				$width = 375;

				//Enter amount of days between image refreshes of images
				$dayInterval = 7;

				//Enter amount of time (in milliseconds) between rotation of images
				$interval = 7000;

				//Enter checklist id, if you wish for images to be pulled from a checklist,
				//leave as 0 if you do not wish for images to come from a checklist
				//if you would like to use more than one checklist, separate their ids with a comma ex. "1,2,3,4"
				$clId = '1';

				//Enter field, specimen, or both to specify whether to use only field or specimen images, or both
				$imageType = "specimen";

				//Enter number of days of most recent images that should be included 
				$numDays = 30;

				//---------------------------DO NOT CHANGE BELOW HERE-----------------------------

				ini_set('max_execution_time', 120);
				include_once($SERVER_ROOT.'/classes/PluginsManager.php');
				$pluginManager = new PluginsManager();
				echo $pluginManager->createSlideShow($ssId,$numSlides,$width,$numDays,$imageType,$clId,$dayInterval,$interval);
				?>
			</div>
                        <fieldset style="text-align:center; width:325px; -moz-border-radius:5px; -webkit-border-radius:5px; margin:10px; border: 1px solid black;margin-left:auto;margin-right:auto;">
                                <legend><b>Science Friday Podcast</b></legend>
                                <iframe title="Science Friday Podcast - UC Davis Tardigrades" src="https://www.3cmediasolutions.org/privid/56252?embed=&key=02835784303ed703988d042303e4e13c0e5a9675&width=400&height=75" width="400px" height="75px" scrolling="no" allowfullscreen frameborder="0"></iframe>
                                <div><a href="https://www.sciencefriday.com/segments/achieving-suspended-animation-with-help-from-the-water-bear/" target="_blank">Achieving Suspended Animation Podcast - 09/30/2016</a></div>
                        </fieldset>
		</div>
		<div style="font-size:140%;">
			<h2 style="margin-top:-8px">Welcome to the Tardigrade Reference Center </h2>
			<p>
				The Tardigrade Reference Center is a collaborative effort to bring both the current and historical information about tardigrades to everybody. 
				The site is the work of Dr. Clark W. Beasley of McMurry University, Abilene, TX and Dr. William R. Miller of Baker University, 
				Baldwin City, KS and their students. The site is built like a scientific paper and provides reference to all source materials. 
			</p>
                        <fieldset style="float:left;width:275px;margin:5px 15px;padding:15px;text-align:center; -moz-border-radius:5px; -webkit-border-radius:5px; border: 1px solid black;">
                                <legend><b>Tardigrade Taxonomy</b></legend>
                                <a href="<?php echo $CLIENT_ROOT; ?>/taxa/taxonomy/taxonomydynamicdisplay.php?target=tardigrada&displayauthor=1">
                                        <img src="<?php echo $CLIENT_ROOT; ?>/images/layout/taxonomy_tn.jpg" style="width:200px;height:120px;margin:auto;" />
                                        <div>Explore tardigrade taxonomy</div>
                                </a>
                        </fieldset>
			<p style="padding:15px 20px">
				The site was designed for the student, educator, or citizen scientist who wishes to learn about tardigrades. It provides general tardigrade 
				information and both "what is" and "how to" information. In addition, there are examples of research projects that can be used at different levels of 
				teaching or student projects. Read more about the <b><i><a href="https://researchoutreach.org/articles/capturing-images-and-data-before-the-slides-degrade-into-uselessness/" target="_blank">specimen digitization project</a></i></b> that lead to the creation of this resource.
			</p>
<!--
                	<p>
				The Science side is based on the scientific literature and provides access or reference to most of the scientific data necessary for research. 
				This side starts with a list of more than 2,500 scientific papers and updated as new papers are published. The bibliography of published papers is 
				cross referenced and can be accessed by author, location, subject, or taxon. In addition, there is a current list of all described species, and a modern 
				data based description of each species with images. Finally, we have developed a modern, dynamic, interactive key to the identification of species of tardigrades. 
			</p>
-->
			<div>
				<fieldset style="text-align:center;width:700px;	-moz-border-radius: 5px;-webkit-border-radius: 5px; margin:5px 15px; border:1px solid black;">
					<legend><b>BBC-Horizons: Oceans of the Solar System</b></legend>
					<iframe title="BBC-Horizons: Oceans of the Solar System" src="https://www.3cmediasolutions.org/privid/45194?embed=&key=7e30500a1e073535795a0e6f5a9fac0d89be2b7f&width=680&height=380" width="680px" height="380px" scrolling="no" allowfullscreen frameborder="0"></iframe>
					<div><a href="http://www.bbc.co.uk/programmes/b076qqxh" target="_blank"><b>BBC documentary on the search for life on other planetary bodies in our solar system</b></a></div>
				</fieldset>

			</div>
		</div>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?> 
</body>
</html>
