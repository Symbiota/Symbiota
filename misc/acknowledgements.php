<?php
include_once('../config/symbini.php');
header('Content-Type: text/html; charset='.$CHARSET);
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Home</title>
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
		<div style="padding: 0px 10px;">
			<b>Development of the DEMCA data portal supported by:</b>
			<UL>
				<LI>National Endowment for the Humanities Digital Humanities Start-up Grant, Level 1 (HD-228866-15, Comparative Ethnobiology in Mesoamerica:
					A Digital Portal for Collaborative Research and Public Dissemination), Jonathan D. Amith (PI)
			</UL><br><br>
			<b>Ethnobiological research to Jonathan D. Amith supported by</b>:<br>
			<UL>
				<LI>A Biological Approach to Documenting Traditional Ecological Knowledge in Synchronic and Diachronic Perspectives. National Endowment for the Humanities, PD-50031;
				National Science Foundation, Documenting Endangered Languages, BCS-1401178 (W. John Kress, co-PI)<br><br>
				<LI>Corpus and lexicon development: Endangered genres of discourse and domains of cultural knowledge in Tu’un ísaví (Mixtec) of Yoloxóchitl, Guerrero.
				National Science Foundation, Documenting Endangered Languages, BCS-0966462; Endangered Language Documentation Programme, MDP0201<br><br>
				<LI>Floristics, Biodiversity, and Traditional Ecological Knowledge in the Sierra Nororiental of Puebla, Mexico.
				Comisión Nacional para el Conocimiento y Uso de la Biodiversidad (CONABIO), Mexico. (Gerardo Salazar, PI; Jonathan D. Amith, co-PI)<br><br>
				<LI>Documentation of Nahuat Knowledge of Natural History, Material Culture, and Ecology in the Municipality of Cuetzalan, Puebla.
				Endangered Language Documentation Programme, School of Oriental and African Studies, MDP0272<br><br>
				<LI>U.S. Fulbright Scholar award, Institute of International Education, Council for International Exchange of Scholars (Jan.–Sept. 2013.
				Host institutions: Jardín Etnobotánico, Oaxaca de Juárez, Oaxaca; Seminario de Lenguas Indígenas, Universidad Nacional Autónoma de México, Mexico City<br><br>
				<LI>Nahuatl Cultural Encyclopedia: Botany and Zoology, Foundation for the Advancement of Mesoamerican Studies, FAMSI #03049<br>
			</UL>
		</div>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>