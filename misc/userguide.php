<?php
include_once('../config/symbini.php');
header('Content-Type: text/html; charset='.$CHARSET);
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> - User Guide</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
</head>
<body>
	<?php
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<!-- This is inner text! -->
	<div id="innertext">
		<h1></h1>
		<b>User Guide</b><br><br>

		Download User Guide (PDF)
		<br>
		<br>The DEMCA data portal uses the Symbiota Virtual Biota software package to establish a biodiversity portal with a specific regional scope. This portal represents the community of collaborating researchers that manage the core scientific data for Ethnobotanical data in Mesoamerica. If you are interested in becoming a data contributor for our data portal, contact the portal administrator for more information on gaining access to the data editing tools. For more information on what is involved in being a data provider, read the Specimen Integration page.
		<br>			<br>
		<c>DEMCA Specimen Database User Manual</c><br>
		Collection Management Editor mode<br>
		(Guideline for entering, editing, and searching for data in the DEMCA Symbiota database)<br>
		<br><br>
		This guide and all other DEMCA “How-To” guides complement the Symbiota supporting information http://symbiota.org/docs/ that includes, but is not limited to specific “help” pages http://symbiota.org/docs/symbiota-introduction/symbiota-help-pages/.  Symbiota supporting information located on the Symbiota website is developed to help all Symbiota portals.  DEMCA User guides are developed specifically to help with ethnobiology databasing in general and the DEMCA Symbiota database in particular.
		<br><br>
		<b>Table of Contents</b><br>
		Create a New User Profile<br>
		Log in to data portal<br>
		Data Entry<br>
		Editing an Existing Record<br>
		Searching Records<br>
		Adding Descriptions to Taxon Profile Pages in DEMCA
		<br>-------------------------------------------------------------------------------------------------------------------------
		<br><br>
		<br><b>Create a New User Profile</b><br>
		Step 1: Select the New account menu item from menu bar. Enter all required fields.
		<br><br>
		<img src="<?= $CLIENT_ROOT ?>/images/userguide/userguide1.jpg" alt="gif" width="960" height="480" align="absmiddle" />
		<br><br>
		<br><b>Log into data portal</b><br>
		Step 2: Select the Log in menu item from menu bar and provide username and password. To save user name and password in browser select [ ] Remember me on this computer.
		<br><br>
		<img src="<?= $CLIENT_ROOT ?>/images/userguide/userguide2.jpg" alt="gif" width="960" height="480" align="absmiddle" />
		<br><br>
		<br><b>Begin Data management: Data entry or editing</b><br>
		Step 3: Select My Profile menu item from menu bar
		<br><br>
		<img src="<?= $CLIENT_ROOT ?>/images/userguide/userguide3.jpg" alt="gif" width="960" height="480" align="absmiddle" />
		<br><br><br>
		Step 4: Select Specimen Management
		<br><br>
		<img src="<?= $CLIENT_ROOT ?>/images/userguide/userguide4.jpg" alt="gif" width="960" height="480" align="absmiddle" />
		<br><br><br>
		Step 5: Select a collection to manage
		<br><br>
		<img src="<?= $CLIENT_ROOT ?>/images/userguide/userguide6.jpg" alt="gif" width="960" height="480" align="absmiddle" />
		<br><br><br>
		Step 6: Select add New Occurence Record to add a specimen label data record from Data Editor Control Panel
		<br><br>
		<img src="<?= $CLIENT_ROOT ?>/images/userguide/userguide7.jpg" alt="gif" width="960" height="480" align="absmiddle" />
		<br><br>
		<img src="<?= $CLIENT_ROOT ?>/images/userguide/userguide8.jpg" alt="gif" width="960" height="480" align="absmiddle" />
		<br><br><br>
		Step 7: Save new specimen data record with preferred data entry setting
		<br><br>
		<img src="<?= $CLIENT_ROOT ?>/images/userguide/userguide9.jpg" alt="gif" width="960" height="480" align="absmiddle" />
		<br><br><br>
		Step 8: Enter next specimen occurence record
		<br><br>
		<img src="<?= $CLIENT_ROOT ?>/images/userguide/userguide8.jpg" alt="gif" width="960" height="480" align="absmiddle" />
		<br><br>
		Step 9: To edit a specimen occurence record, select Edit Existing Occurrence Records from Data Editor Control Panel
		<br><br><br>
		<img src="<?= $CLIENT_ROOT ?>/images/userguide/userguide10.jpg" alt="gif" width="960" height="480" align="absmiddle" />
		<br><br><br>
		Step 10: To edit a specimen occurence record, select custom Field Name and respective data including display preference (Display as data record view or table view)
		<br><br>
		<img src="<?= $CLIENT_ROOT ?>/images/userguide/userguide11.jpg" alt="gif" width="960" height="480" align="absmiddle" />
		<br><br><br>
		Step 11: Table view of <i>Piper</i> (Field Name = Genus) search
		<br><br>
		<img src="<?= $CLIENT_ROOT ?>/images/userguide/userguide12.jpg" alt="gif" width="960" height="480" align="absmiddle" />
		<br><br>
		<br><br><br>
		<br><br><br><br>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>