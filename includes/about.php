<?php
include_once('../config/symbini.php');
if($LANG_TAG == 'en' || !file_exists($SERVER_ROOT.'/content/lang/about.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/about.en.php');
else include_once($SERVER_ROOT.'/content/lang/about.'.$LANG_TAG.'.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
	<head>
	<title><?php echo $DEFAULT_TITLE; ?> <?php echo $LANG['ABOUT'] ?></title>
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
			<a href="<?php echo $CLIENT_ROOT; ?>/index.php"><?php echo $LANG['HOME'] ?></a> &gt;&gt;
			<b><?php echo $LANG['ABOUT_PROJ'] ?></b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext">
			<!-- <h1>About This Project</h1><br /> -->
			<?php
				if ($LANG_TAG=='fr'){
			?>
			<h2>Qu’est-ce que le Projet sur les Plantes d'Afrique Tropicales ?</h2>
			<div style="margin:20px">
				<p>
				Le Projet sur les plantes d'Afrique tropicales est un effort à grande échelle conçu pour établir 
				une nouvelle ressource importante de données sur la biodiversité. L'initiative a débuté par un 
				projet de « preuve de concept », soutenu par la Fondation JRS pour la biodiversité et dirigé par 
				le professeur Alex Asase (Université du Ghana), qui a abouti à la numérisation de plus de 250 000 
				enregistrements de données sur la biodiversité provenant d'herbiers européens et ouest-africains. 
				Le présent effort, soutenu par la National Science Foundation des États-Unis, mènera à la numérisation 
				de plus de 1,1 million de spécimens d'herbiers et d'enregistrements de données associés provenant 
				de toute l'Afrique tropicale et hébergés dans 21 herbiers américains. Les liens vers les demandes 
				de financement pour ces deux projets sont fournis ci-dessous.
				</p>
			</div>
			<div style="margin:20px">
				<p>JRS 2014: <a href="https://www.dropbox.com/scl/fi/ly4sbxq906117tca8f5ah/JRS_WAfrica_2014.pdf?rlkey=8n7rbdthzeprvbit5dh62trc2&dl=0" target="_blank">Projet sur les plantes d'Afrique de l'Ouest</a> (dirigé par le professeur Alex Asase)</p>
				<p>NSF 2022: <a href="https://www.dropbox.com/scl/fi/lh9di7oo0hek25s8lnf73/African-Plants-2022-Final-Project-Description-only.pdf?rlkey=za4d08n016glut5ls9wjkamhx&dl=0" target="_blank">Projet sur les plantes d’Afrique tropicales</a></p>
			</div>
			<div style="margin:20px">
				<p>
					Les spécimens des pays suivants sont ciblés dans ce projet de numérisation :
					Angola, Bénin, Burkina Faso, Burundi, Cameroun, République centrafricaine, Tchad, Congo, Côte d'Ivoire,
					République démocratique du Congo, Djibouti, Guinée équatoriale, Érythrée, Éthiopie, Gabon, Gambie, Ghana,
					Guinée, Guinée-Bissau, Kenya, Libéria, Malawi, Mali, Mozambique, Niger, Nigéria, Ouganda, Rwanda, Sao Tomé-et-Principe, Sénégal, Sierra Leone, Somalie, Soudan, Tanzanie, Togo et Zambie. Toutefois, des spécimens
					d'autres pays peuvent également être numérisés dans le cadre de flux de travail efficaces.
				</p>
			</div>
			<h2>Opportunités</h2>
			<div style="margin:20px">
				<p>
					Notre effort de collaboration offre de nombreuses opportunités aux individus et aux institutions 
					du monde entier de participer au projet sur les plantes d'Afrique tropicales.
				</p>
				<p>
					Pour les chercheurs et étudiants intéressés par l'utilisation des données, elles sont librement 
					accessibles via ce portail. Les personnes ayant des demandes plus spécialisées ou à plus grande 
					échelle sont encouragées à contacter les responsables du projet listés ci-dessous.
				</p>
				<p>
					Les institutions disposant d'herbiers et d'autres biocollections intéressées à contribuer des données 
					sont invitées à contacter le <a href="https://symbiota.org/" target="_blank">Symbiota Support Hub</a>, qui a créé 
					et gère le système de portails pour les données sur la biodiversité.
				</p>
			</div>
			<h2>Contacts</h2>
			<div style="margin:20px">
				<p><b>Lead PI:</b> <a href="town@ku.edu">A. Townsend Peterson</a></p>
				<p><b>Project Manager:</b> <a href="slowell@ku.edu">Samantha Lowell</a></p>
			</div>
			<?php
				} else if ($LANG_TAG=='pt'){
			?>
			<h2>O que é o Projeto Plantas Tropicais Africanas?</h2>
			<div style="margin:20px">
				<p>
					O Projeto Plantas Tropicais Africanas é um esforço em grande escala concebido para estabelecer 
					um novo e importante recurso de dados sobre a biodiversidade. A iniciativa começou com um projeto 
					de “prova de conceito”, apoiado pela JRS Fundação de Biodiversidade (JRS Biodiversity Foundation) 
					e liderado pelo Prof. Alex Asase (Universidade de Gana), que resultou na digitalização de mais de 
					250.000 registros de dados de biodiversidade de herbários europeus e da África Ocidental. O presente 
					esforço, apoiado pela Fundação Nacional de Ciências dos Estados Unidos, levará à digitalização de 
					mais de 1,1 milhões de espécimes de herbário e registros dos dados associados à África tropical, 
					depositados em 21 herbários dos Estados Unidos. Links para ambas as propostas são fornecidos abaixo.
				</p>
			</div>
			<div style="margin:20px">
				<p>JRS 2014: <a href="https://www.dropbox.com/scl/fi/ly4sbxq906117tca8f5ah/JRS_WAfrica_2014.pdf?rlkey=8n7rbdthzeprvbit5dh62trc2&dl=0" target="_blank">Projeto Plantas da África Ocidental</a> (liderado pelo Prof. Alex Asase)</p>
				<p>NSF 2022: <a href="https://www.dropbox.com/scl/fi/lh9di7oo0hek25s8lnf73/African-Plants-2022-Final-Project-Description-only.pdf?rlkey=za4d08n016glut5ls9wjkamhx&dl=0" target="_blank">Projeto Plantas Tropicais Africanas</a></p>
			</div>
			<div style="margin:20px">
				<p>
					Espécimes dos seguintes países estão sendo alvos deste projeto de digitalização:
					Angola, Benim, Burkina Faso, Burundi, Camarões, República Centro-Africana, Chade, Congo, Costa do Marfim,
					República Democrática do Congo, Djibuti, Guiné Equatorial, Eritreia, Etiópia, Gabão, Gâmbia, Gana,
					Guiné, Guiné-Bissau, Quênia, Libéria, Malawi, Mali, Moçambique, Níger, Nigéria, Ruanda, São Tomé e
					Príncipe, Senegal, Serra Leoa, Somália, Sudão, Tanzânia, Togo, Uganda e Zâmbia. No entanto, espécimes
					de outros países também podem ser digitalizados como parte de fluxos de trabalho eficientes.
				</p>
			</div>
			<h2>Oportunidades</h2>
			<div style="margin:20px">
				<p>
					O nosso esforço colaborativo oferece muitas oportunidades para indivíduos e instituições de todo 
					o mundo participarem no Projeto Plantas Tropicais Africanas.
				</p>
				<p>
					Para pesquisadores e estudantes interessados em utilizar os dados, esses estão disponíveis e 
					acessíveis através deste portal. Indivíduos com solicitações mais especializadas ou de maior 
					escala são incentivados a entrar em contato com a liderança do projeto abaixo.
				</p>
				<p>
					Instituições com herbários e outras coleções biológicas interessadas em contribuir com dados devem entrar em contato com o 
					<a href="https://symbiota.org/" target="_blank">Symbiota Support Hub</a>, que criou e mantém o sistema de portais de dados 
					de biodiversidade.
				</p>
			</div>
			<h2>Contatos</h2>
			<div style="margin:20px">
				<p><b>PI Principal:</b> <a href="town@ku.edu">A. Townsend Peterson</a></p>
				<p><b>Gestora de Projeto:</b> <a href="slowell@ku.edu">Samantha Lowell</a></p>
			</div>
			<?php
				} else {
			?>
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
				<p>NSF 2022: <a href="https://www.dropbox.com/scl/fi/lh9di7oo0hek25s8lnf73/African-Plants-2022-Final-Project-Description-only.pdf?rlkey=za4d08n016glut5ls9wjkamhx&dl=0" target="_blank">Tropical African Plants Project</a></p>
			</div>
			<div style="margin:20px">
				<p>
				Specimens from the following countries are being targeted in this digitization project:
				Angola, Benin, Burkina Faso, Burundi, Cameroon, Central African Republic, Chad, Congo, Cote D'Ivoire, 
				Democratic Republic of the Congo, Djibouti, Equatorial Guinea, Eritrea, Ethiopia, Gabon, Gambia, Ghana, 
				Guinea, Guinea-Bissau, Kenya, Liberia, Malawi, Mali, Mozambique, Niger, Nigeria, Rwanda, Sao Tome and 
				Principe, Senegal, Sierra Leone, Somalia, Sudan, Tanzania, Togo, Uganda, and Zambia. However, specimens 
				from other countries may also be digitized as part of efficient workflows.
				</p>
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
				<p><b><a href="https://www.idigbio.org/wiki/index.php/TCN:_Collaborative_Research:_Digitization_and_Enrichment_of_U.S._Herbarium_Data_from_Tropical_Africa_to_Enable_Urgent_Quantitative_Conservation_Assessments#Project_Collaborators" target="_blank">Project Collaborators</b></a></p>
			</div>
			<?php
				}
			?>
		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
