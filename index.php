<?php
include_once('config/symbini.php');
if($LANG_TAG == 'en' || !file_exists($SERVER_ROOT.'/content/lang/templates/index.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/templates/index.en.php');
else include_once($SERVER_ROOT.'/content/lang/templates/index.'.$LANG_TAG.'.php');
header('Content-Type: text/html; charset=' . $CHARSET);
?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Home</title>
	<?php
	include_once($SERVER_ROOT . '/includes/head.php');
	include_once($SERVER_ROOT . '/includes/googleanalytics.php');
	?>
</head>
<body>
	<?php
	include($SERVER_ROOT . '/includes/header.php');
	?>
	<div class="navpath"></div>
	<main id="innertext">
		<div style="float:right;margin-left:15px">
			<!--
			<div>
				//<?php
				//---------------------------SLIDESHOW SETTINGS---------------------------------------
				//If more than one slideshow will be active, assign unique numerical ids for each slideshow.
				//If only one slideshow will be active, leave set to 1.
				$ssId = 1;
				//Enter number of images to be included in slideshow (minimum 5, maximum 10)
				$numSlides = 10;
				//Enter width of slideshow window (in pixels, minimum 275, maximum 800)
				$width = 350;
				//Enter amount of days between image refreshes of images
				$dayInterval = 7;
				//Enter amount of time (in milliseconds) between rotation of images
				$interval = 7000;
				//Enter checklist id, if you wish for images to be pulled from a checklist,
				//leave as 0 if you do not wish for images to come from a checklist
				//if you would like to use more than one checklist, separate their ids with a comma ex. "1,2,3,4"
				//$clid = '1279';
				$clid = '80';
				//Enter field, specimen, or both to specify whether to use only field or specimen images, or both
				$imageType = 'specimen';
				//Enter number of days of most recent images that should be included
				$numDays = 30;

				//---------------------------DO NOT CHANGE BELOW HERE-----------------------------
				//ini_set('max_execution_time', 120);
				//include_once($SERVER_ROOT.'/classes/PluginsManager.php');
				//$pluginManager = new PluginsManager();
				//echo $pluginManager->createSlideShow($ssId,$numSlides,$width,$numDays,$imageType,$clid,$dayInterval,$interval);
				//?>
				//---------------------------END SLIDESHOW SETTINGS---------------------------------------
			</div>
			-->
		</div>
		<div style="padding: 0px 10px">
			<h1>Welcome to the CCH2 data portal</h1>
			<p>
				California is home to over 6,000 plant species. The botanical diversity of the state is documented in herbarium 
				collections housed at Universities, Field Stations, Museums, and Gardens. Plants are documented by field biologists 
				and others who work with the Consortium of California Herbaria, whose members preserve and curate those plant specimens. 
				The data are then leveraged by researchers to answer fundamental questions about plants in California.
			</p>
			<p>
				This Symbiota portal is the primary database for California's herbaria to manage and share data about the plant 
				specimens they curate. Currently over 60 collections share over 4 million plant records in this portal. Data 
				managed here flows into other botanical resources like the Jepson eFlora, Calfora, iDigBio, and GBIF.
			</p>
			<div style="float:right"><img src="images/layout/SHTC00976.jpg" style="width:200px;margin:0px 60px" /></div>
			<p>
				Funding for this portal was made possible in 2018 via NSF awards to create the California Phenology Thematic 
				Collections Network (CAP-TCN; <a href="https://www.capturingcaliforniasflowers.org" target="_blank">https://www.capturingcaliforniasflowers.org</a>). 
				This project created over 1 million images of specimens that can now be viewed in this portal, 
				and more are added every day. To advance research of the California flora and the impacts of climate change, 
				this project also developed phenological data for the specimens, and those data can be downloaded here.
			</p>
			<p>
				This portal is managed by Cal Poly, San Luis Obispo and the Symbiota Support Hub. For questions, 
				please email <a href="mailto:help@symbiota.org">help@symbiota.org</a>.
			</p>
			<div>
				<h3>Using the data:</h3>
			</div>
			<p>
			Please refer to our <a href="http://cch2.org/portal/includes/usagepolicy.php">Data Use Policy</a>. The Consortium of California Herbaria asks that users not redistribute data obtained from this site.
			However, links or references to this site may be freely posted. If you have any questions about this policy,
			please contact Jenn Yost (<a href="mailto:jyost@calpoly.edu">jyost@calpoly.edu</a>) and Katie Pearson (<a href="mailto:help@symbiota.org">help@symbiota.org</a>).
			</p>
			<p>
			<b>More California specimen data may be found at the following portals:</b>
			<ul>
				<li style="margin-bottom:7px; ">Algae: <a href="http://macroalgae.org" targert="_blank">Algae Herbarium Portal</a></li>
				<li style="margin-bottom:7px; ">Bryophytes: <a href="https://bryophyteportal.org/portal/" target="_blank">Consortium of Bryophyte Herbaria</a></li>
				<li style="margin-bottom:7px; ">Fungi: <a href="http://mycoportal.org" target="_blank">Mycology Collections Portal (MyCoPortal)</a></li>
				<li style="margin-bottom:7px; ">Lichens: <a href="https://lichenportal.org/portal/" target="_blank">Consortium of Lichen Herbaria</a></li>
				<li style="margin-bottom:7px; ">Pteridophytes: <a href="http://www.pteridoportal.org/portal/" target="_blank">Pteridophyte Collections Consortium</a></li>
			</ul>
			</p>
		</div>
 	</main>
	<?php if($GLOBALS['DONATE_LINK'] && file_exists($SERVER_ROOT . '/includes/donationButton.php')): ?>
		<?php include($SERVER_ROOT . '/includes/donationButton.php') ?>
	<?php endif ?>
        <?php
        include($SERVER_ROOT . '/includes/footer.php');
        ?>
</body>
</html>
