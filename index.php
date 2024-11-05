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
		<h1>Welcome to the Algae Portal!</h1>
		<div style="padding: 0px 10px;">
			<div style="margin:20px;font-size:12pt;">
				<p>
					Algae are the foundation of many marine, estuarine and freshwater benthic ecosystems and provide food, substrata and protection for a myriad of other aquatic organisms. Many species are sensitive to environmental change. In addition, a number of species, including kelp, nori, and others, are grown via extensive aquaculture or harvested from the wild for human food and for extraction of colloids used in cosmetics, food products, and pharmaceuticals. This portal serves as: 1) a resource to advance algal research; 2) a data management and publishing platform for vouchered and observational algal specimen data, and 3) to provide opportunities for the public to learn about the economic and ecological importance of algae.
				</p>
			</div>
			<div style="float:right">
				<img src="images/layout/Harvard_example_specimen.jpg" style="width:250px;margin:0px 60px 20px" />
			</div>
			<div style="margin:20px;font-size:12pt;">
				<!--draft text in case PSA supports us
				The Algae Herbarium Portal is supported by the <a href="https://www.psaalgae.org/" target="_blank">Phycological Society of America</a>
				and the <a href="https://ansp.org/research/systematics-evolution/botany/" target="_blank">Academy of Natural Sciences of Drexel University</a>.
				-->
				<p>
				The Algae Herbarium Portal was initially launched by the NSF-funded Macroalgal Digitization Project (NSF Award 1304924) and managed by the <a href="https://colsa.unh.edu/unh-collections/albion-r-hodgdon-herbarium" target="_blank">University of New Hampshire</a>. The goal of the original project was to image, database and georeference the macroalgal specimens in 49 herbaria from New England to Florida, to Hawaii and Guam. Since this project, the scope of the portal has expanded to include all algae specimens from any collection that wishes to contribute.
				</p>
			</div>
			<div style="margin:20px;font-size:12pt;">
				Additional Algae Research & Collections Resources:
				<ul>
					<li><a href="https://www.algaebase.org/" target="_blank">AlgaeBase</a></li>
					<li><a href="https://www.psaalgae.org/algal-web-links/" target="_blank">Phycological Society of America Resources</a></li>
				</ul>
			</div>
			<div style="margin:20px;font-size:12pt;">
				<p>
				For comments, questions, or to join the Algae Herbarium Portal, contact the Symbiota Support Hub (<a href="mailto:help@symbiota.org?subject=Macroalgae Portal Feedback">help@symbiota.org</a>).
				</p>
			</div>
			<div style="margin:50px;font-size:8pt;">
				Portal <a href="https://flickr.com/photos/40322276@N04/12801115735" target="_blank">header image</a> from NOAA's National Ocean Service licensed under <a href="http://creativecommons.org/licenses/by/2.0/" target="_blank">CC BY 2.0</a>.
			</div>
		</div>
	</main>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>
</html>
