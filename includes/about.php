<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
	<?php
		if ($LANG_TAG=='en'){
	?>
	<head>

	<title><?php echo $DEFAULT_TITLE; ?> About</title>
		<?php
		$activateJQuery = false;
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
	</head>
	<body>
		<?php
		$displayLeftMenu = true;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
			<a href="<?php echo $CLIENT_ROOT; ?>/index.php">Home</a> &gt;&gt;
			<b>About Project</b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext">
			<!-- <h1>About This Project</h1><br /> -->
			<h2>What is the Tropical African Plants Project?</h2>
			<div style="margin:20px">
				<p>
					The Tropical African Plants Project is a large-scale effort designed to establish an important new 
					biodiversity data resource. The initiative began with a “proof of concept” project, supported by the 
					JRS Biodiversity Foundation, and led by Prof. Alex Asase (University of Ghana), which resulted in the 
					digitization of more than 250,000 biodiversity data records from European and West African herbaria. 
					The present effort, supported by the U.S. National Science Foundation, will lead to the digitization 
					of more than 1.1M herbarium specimens and associated data records from across tropical Africa housed 
					in 21 U.S. herbaria. Links to both proposals are provided below.
				</p>
			</div>
			<div style="margin:20px">
				<p>JRS 2014: <a href="https://www.dropbox.com/scl/fi/ly4sbxq906117tca8f5ah/JRS_WAfrica_2014.pdf?rlkey=8n7rbdthzeprvbit5dh62trc2&dl=0" target="_blank">West African Plants Project</a> (led by Prof. Alex Asase)</p>
				<p>NSF 2022: <a href="https://www.dropbox.com/s/soqy53xv6ve6rxm/African%20Plants%202022%20Final%20Reduced.pdf?dl=0" target="_blank">Tropical African Plants Project</a></p>
			</div>
			<h2>Opportunities</h2>
			<div style="margin:20px">
				<p>
					Our collaborative endeavor offers many opportunities for individuals and institutions around the world 
					to participate in the Tropical African Plants Project. 
				</p>
				<p>
					For researchers and students interested in using the data, they are openly available and accessible via 
					this portal. Individuals with more specialized or larger-scale requests are encouraged to contact the 
					project leadership listed below.
				</p>
				<p>
					Institutions with herbaria and other biocollections interested in contributing data should contact 
					the <a href="https://symbiota.org/" target="_blank">Symbiota Support Hub</a>, which has created 
					and is maintaining the system of portals for biodiversity data.
				</p>
			</div>
			<h2>Contacts</h2>
			<div style="margin:20px">
				<p><b>Lead PI:</b> <a href="town@ku.edu">A. Townsend Peterson</a></p>
				<p><b>Project Manager:</b> <a href="slowell@ku.edu">Samantha Lowell</a></p>
			</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
	<?php
		}
	?>
	<?php
		if ($LANG_TAG=='pt'){
	?>
	<head>

	<title><?php echo $DEFAULT_TITLE; ?> Sobre</title>
		<?php
		$activateJQuery = false;
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
	</head>
	<body>
		<?php
		$displayLeftMenu = true;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
			<a href="<?php echo $CLIENT_ROOT; ?>/index.php">Início</a> &gt;&gt;
			<b>Sobre o Projeto</b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext">
			<!-- <h1>About This Project</h1><br /> -->
			<h2>O que é o Projeto Plantas Tropicais Africanas?</h2>
			<div style="margin:20px">
				<p>
					O Projecto de Plantas Tropicais Africanas é um esforço em grande escala concebido para estabelecer um novo e importante
					recurso de dados de biodiversidade. A iniciativa começou com um projeto de “prova de conceito”, apoiado pela
					JRS Biodiversity Foundation, e liderada pelo Prof. Alex Asase (Universidade de Gana), que resultou no
					digitalização de mais de 250.000 registos de dados de biodiversidade de herbários europeus e da África Ocidental.
					O presente esforço, apoiado pela Fundação Nacional de Ciência dos EUA, levará à digitalização
					de mais de 1,1 milhão de espécimes de herbário e registros de dados associados de toda a África tropical alojados
					em 21 herbários dos EUA. Links para ambas as propostas são fornecidos abaixo.
				</p>
			</div>
			<div style="margin:20px">
				<p>JRS 2014: <a href="https://www.dropbox.com/scl/fi/ly4sbxq906117tca8f5ah/JRS_WAfrica_2014.pdf?rlkey=8n7rbdthzeprvbit5dh62trc2&dl=0" target="_blank">Projeto de Plantas da África Ocidental</a> (liderado pelo Prof. Alex Asase)</p>
				<p>NSF 2022: <a href="https://www.dropbox.com/s/soqy53xv6ve6rxm/African%20Plants%202022%20Final%20Reduced.pdf?dl=0" target="_blank">Projeto de Plantas Tropicais Africanas</a></p>
			</div>
			<h2>Oportunidades</h2>
			<div style="margin:20px">
				<p>
					Nosso esforço colaborativo oferece muitas oportunidades para indivíduos e instituições em todo o mundo
					participar do Projeto Plantas Tropicais Africanas.
				</p>
				<p>
					Para pesquisadores e estudantes interessados em utilizar os dados, eles estão disponíveis abertamente e acessíveis via
					este portal. Indivíduos com solicitações mais especializadas ou de maior escala são incentivados a entrar em contato com o
					liderança do projeto listado abaixo.
				</p>
				<p>
					Instituições com herbários e outras biocoleções interessadas em contribuir com dados deverão entrar em contato
					o <a href="https://symbiota.org/" target="_blank">Symbiota Support Hub</a>, que criou
					e mantém o sistema de portais de dados de biodiversidade.
				</p>
			</div>
			<h2>Contatos</h2>
			<div style="margin:20px">
				<p><b>PI Líder:</b> <a href="town@ku.edu">A. Townsend Peterson</a></p>
				<p><b>Gestor de Projeto:</b> <a href="slowell@ku.edu">Samantha Lowell</a></p>
			</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
	<?php
		}
	?>
		<?php
		if ($LANG_TAG=='fr'){
	?>
	<head>

	<title><?php echo $DEFAULT_TITLE; ?> À propos</title>
		<?php
		$activateJQuery = false;
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
	</head>
	<body>
		<?php
		$displayLeftMenu = true;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
			<a href="<?php echo $CLIENT_ROOT; ?>/index.php">Accueil</a> &gt;&gt;
			<b>À propos du Projet</b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext">
			<!-- <h1>About This Project</h1><br /> -->
			<h2>Qu'est-ce que le Projet Plantes Tropicales d'Afrique ??</h2>
			<div style="margin:20px">
				<p>
				Le Projet sur les plantes tropicales d'Afrique est un effort à grande échelle conçu pour établir un nouveau
				ressource de données sur la biodiversité. L'initiative a débuté par un projet de "preuve de concept", soutenu par le
				JRS Biodiversity Foundation, et dirigé par le professeur Alex Asase (Université du Ghana), qui a abouti à la
				numérisation de plus de 250 000 enregistrements de données sur la biodiversité provenant d’herbiers européens et ouest-africains.
				Le présent effort, soutenu par la National Science Foundation des États-Unis, mènera à la numérisation
				de plus de 1,1 million de spécimens d’herbier et d’enregistrements de données associés provenant de toute l’Afrique tropicale hébergés
				dans 21 herbiers américains. Les liens vers les deux propositions sont fournis ci-dessous.
				</p>
			</div>
			<div style="margin:20px">
				<p>JRS 2014: <a href="https://www.dropbox.com/scl/fi/ly4sbxq906117tca8f5ah/JRS_WAfrica_2014.pdf?rlkey=8n7rbdthzeprvbit5dh62trc2&dl=0" target="_blank">Projet de plantes d'Afrique de l'Ouest</a> (dirigé par le professeur Alex Asase)</p>
				<p>NSF 2022: <a href="https://www.dropbox.com/s/soqy53xv6ve6rxm/African%20Plants%202022%20Final%20Reduced.pdf?dl=0" target="_blank">Projet de Plantes Tropicales Africaines</a></p>
			</div>
			<h2>Opportunités</h2>
			<div style="margin:20px">
				<p>
					Notre effort de collaboration offre de nombreuses opportunités aux individus et aux institutions du monde entier.
					pour participer au Projet sur les plantes tropicales d'Afrique.
				</p>
				<p>
					Pour les chercheurs et étudiants intéressés par l’utilisation des données, celles-ci sont librement disponibles et accessibles via
					ce portail. Les personnes ayant des demandes plus spécialisées ou à plus grande échelle sont encouragées à contacter le
					direction de projet énumérée ci-dessous.
				</p>
				<p>
					Les institutions disposant d'herbiers et d'autres biocollections intéressées par la contribution de données doivent contacter
					le <a href="https://symbiota.org/" target="_blank">Symbiota Support Hub</a>, qui a créé
					et entretient le système de portails pour les données sur la biodiversité.
				</p>
			</div>
			<h2>Contacts</h2>
			<div style="margin:20px">
				<p><b>PI líder:</b> <a href="town@ku.edu">A. Townsend Peterson</a></p>
				<p><b>Chef de Projet:</b> <a href="slowell@ku.edu">Samantha Lowell</a></p>
			</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
	<?php
		}
	?>
</html>
