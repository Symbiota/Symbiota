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
		<?php
			if ($LANG_TAG=='pt'){
		?>
		<div class="lang pt">
			<h1>Bem-vindo!</h1>
			<!-- <h2>Collaborative Biodiversity Portal</h2> -->
			<p>O Portal das Plantas Africanas fornece um ponto de acesso único a milhões de registros primários 
				da biodiversidade das plantas de países africanos. Esta iniciativa surge de uma <a href="https://www.nsf.gov/awardsearch/showAward?AWD_ID=2223875" target="_blank">subvenção</a> 
				do programa Avanço na Digitalização de Coleções Biológicas (Advancing the Digitization of Biological Collections) 
				da Fundação Nacional de Ciências (National Science Foundation) que visa (1) digitalizar espécimes e dados 
				associados de coleções africanas depositadas em herbários dos Estados Unidos, (2) georreferenciar e melhorar 
				esses dados, e (3) partilhá-los abertamente com as comunidades mundiais científicas e de conservação. O escopo 
				da digitalização deste projeto se concentra em espécimes da <a href=<?php echo $CLIENT_ROOT . '/includes/about.php' ?>>África tropical</a>. Embora espécimes digitalizados de 
				outros países africanos também estejam incluídos neste portal, por não serem o foco da digitalização para este 
				projeto, eles podem não refletir com precisão os acervos completos dos herbários parceiros. A descrição 
				<a href="https://www.dropbox.com/scl/fi/lh9di7oo0hek25s8lnf73/African-Plants-2022-Final-Project-Description-only.pdf?rlkey=za4d08n016glut5ls9wjkamhx&dl=0" target="_blank">completa do projeto é fornecida aqui</a>.
			</p>
				
			<p>O Portal das Plantas Africanas tem um âmbito amplo e está aberto à participação de quaisquer instituições 
				interessadas em utilizar este recurso e/ou contribuir com dados. O objetivo final do portal é fornecer 
				um depósito global em constante expansão dos dados de biodiversidade vegetal relativos à África, para 
				facilitar uma nova geração de análises que irão informar e esclarecer o conhecimento e conservação da rica 
				flora do continente.
			</p>
		</div>
		<?php
			} else if ($LANG_TAG=='fr'){
		?>
		<div class="lang fr">
			<h1>Bem-vindo!</h1>
			<!-- <h2>Collaborative Biodiversity Portal</h2> -->
			<p>Le Portail des Plantes Africaines fournit un point d'accès unique à des millions d'enregistrements de plantes 
				des pays africains. Cette initiative est financée par d'une <a href="https://www.nsf.gov/awardsearch/showAward?AWD_ID=2223875" target="_blank">subvention</a> 
				du programme Advancing the Digitization of Biological Collections de la National Science Foundation (USA) qui vise à 
				(1) numériser les spécimens et les données associées des collections africaines déposées dans les herbiers aux 
				États-Unis, (2) géoréférencer et améliorer ces données, et (3) les partager librement avec les communautés 
				mondiales de la science et de la conservation. La numérisation de ce projet porte sur les spécimens <a href=<?php echo $CLIENT_ROOT . '/includes/about.php' ?>>d'Afrique tropicale</a>. 
				Bien que des spécimens numérisés d'autres pays africains soient également inclus dans ce portail, comme ils ne sont pas 
				l'objet de la numérisation de ce projet, ils peuvent ne pas refléter avec précision l'intégralité des fonds des herbiers 
				partenaires. Vous pouvez lire la 
				<a href="https://www.dropbox.com/scl/fi/lh9di7oo0hek25s8lnf73/African-Plants-2022-Final-Project-Description-only.pdf?rlkey=za4d08n016glut5ls9wjkamhx&dl=0" target="_blank">description complète du projet ici</a>.
			</p>
				
			<p>Le Portail des plantes africaines a une portée large et est ouvert à la participation de toute institution 
				souhaitant utiliser cette ressource et/ou fournir des images et des données. L'objectif ultime du Portail 
				est d'établir une ressource mondiale en constante expansion contenant des données sur la biodiversité végétale 
				concernant l'Afrique, afin de faciliter une nouvelle génération d'analyses qui informeront et éclaireront la 
				compréhension et la conservation de la riche flore du continent.
			</p>
		</div>
		<?php
			} else {
		?>
		<div class="lang en">
			<h1>Welcome!</h1>
			<!-- <h2>Collaborative Biodiversity Portal</h2> -->
			<p>The African Plants Portal provides a unique access point to millions of primary biodiversity records 
				of plants from African countries. This initiative springs from 
				<a href="https://www.nsf.gov/awardsearch/showAward?AWD_ID=2223875" target="_blank">a grant by the National Science Foundation</a>'s  
				program <i>Advancing the Digitization of Biological Collections</i> 
				that aims to (1) digitize specimens and associated data of African collections deposited in herbaria in 
				the United States, (2) georeference and improve those data, and (3) share them openly with the global 
				science and conservation communities. The scope of digitization of this project focuses on specimens from <a href=<?php echo $CLIENT_ROOT . '/includes/about.php' ?>>tropical Africa</a>. 
				While digitized specimens from other African countries are also included in this portal, because they are not the focus 
				of digitization for this project, they may not accurately reflect partner herbaria's complete holdings. 
				You can read the <a href="https://www.dropbox.com/scl/fi/lh9di7oo0hek25s8lnf73/African-Plants-2022-Final-Project-Description-only.pdf?rlkey=za4d08n016glut5ls9wjkamhx&dl=0" target="_blank">full project description here</a>.
			</p>

			<p>The African Plants Portal is broader in scope and is open to participation by any institutions wishing 
				to use this resource and/or contribute data. The ultimate goal of the portal is to enable an ever-expanding 
				global storehouse of plant biodiversity data regarding Africa, to facilitate a new generation of analyses 
				that will inform and illuminate the understanding and conservation of the continent's rich flora.</p>
		</div>
		<?php
			}
		?>
		<div style="max-width:100%;text-align:center;margin:3rem;height:auto">
			<img src="<?php echo $CLIENT_ROOT . '/images/layout/Map_withimagery.jpg' ?>" alt="Map of Africa" style="max-width:100%"></img>
		</div>
	</main>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>
</html>