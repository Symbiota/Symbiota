<?php
//error_reporting(E_ALL);
include_once('config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
?>
<html>
<head>
	<title>CoTRAM: Cooperative Taxonomic Resource For American Myrtaceae</title>
	<link rel="stylesheet" href="<?= $CLIENT_ROOT ?>/css/main.css" type="text/css" />
	<meta name='keywords' content='Arizona,New Mexico,Sonora,Sonoran,Desert,plants,lichens,natural history collections,flora, fauna, checklists,species lists' />
	<?php include_once($SERVER_ROOT.'/includes/googleanalytics.php'); ?>
</head>
<body>
	<?php
	$displayLeftMenu = true;
	include($SERVER_ROOT.'/includes/header.php');
	?> 
        <!-- This is inner text! -->
        <div id="innertext">
		<h2>Welcome to CoTRAM</h2>
		<div style="margin:20px;">The Cooperative Taxonomic Resource for American Myrtaceae was created for researchers of American Myrtaceae 
			and others who might want to identify unknowns.  The Myrtaceae, a family with perhaps over 5000 species may be the ninth largest family of flowering 
			plants, but its taxonomy is yet poorly understood, so these can only be tentative estimates.  It has two great centers of diversity: 
			Australia, southern Asia and islands of that general region; and the American tropics.  Perhaps half of all Myrtaceae occur in the Americas. 
			The Atlantic Coastal Forest of Brazil is especially rich, the family sometimes being dominant there.  Myrtaceae are also important in temperate 
			Chile and Argentina, the Andes from Bolivia to Venezuela, the Guayana Highlands, Mesoamerica and the Caribbean.
		</div>
		<div style="margin:20px;">Identification of American Myrtaceae can sometimes be a daunting task as some of the taxonomically important 
			characters are cryptic (embryo structure) and because there is relatively little variation in vegetative characters. 
			For example, it is often impossible for even a specialist to recognize an unknown sterile specimen to genus.
		</div>
		<div style="float:right;width:180px;padding:10px;-moz-border-radius:5px;-webkit-border-radius:5px;margin:0px 5px 10px 10px;border:#990000 solid 1px;">
			Development of CoTRAM, <a href="https://symbiota.org">Symbiota</a>, and several of the specimen databases have been supported by 
			National Science Foundation Grants 
			(<a href="http://nsf.gov/awardsearch/showAward.do?AwardNumber=9983132" target="_blank">DBI 9983132</a>, 
			<a href="http://nsf.gov/awardsearch/showAward.do?AwardNumber=0237418" target="_blank">BRC 0237418</a>, 
			<a href="http://www.nsf.gov/awardsearch/showAward.do?AwardNumber=0743827" target="_blank">DBI 0743827</a>,
			<a href="http://www.nsf.gov/awardsearch/showAward.do?AwardNumber=0847966" target="_blank">DBI 0847966</a>)
		</div>	
		<div style="margin:20px;">
			The objective of this website is to bring together databases of authority-identified specimens and images with software 
			tools that allow one to make geographically restricted checklists and maps.  An interactive key is being populated with character information.  
			Generic and species descriptions will be added in time.  Instructions on using this website will be posted in the near future.
		</div>
		<div style="margin:30px 20px 20px 20px;">
			Join CoTRAM as a regular visitor and please send your feedback to 
			<a class="bodylink" href="mailto:les.landrum@asu.edu?subject=Feedback">Leslie Landrum</a>
		</div>
        </div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?> 
</body>
</html>
