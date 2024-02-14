<?php
include_once('config/symbini.php');
if($LANG_TAG == 'en' || !file_exists($SERVER_ROOT.'/content/lang/index.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/index.en.php');
else include_once($SERVER_ROOT.'/content/lang/index.'.$LANG_TAG.'.php');
header('Content-Type: text/html; charset=' . $CHARSET);
?>
<html>
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
	<div id="innertext">
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
				esses dados, e (3) partilhá-los abertamente com as comunidades mundiais científicas e de conservação. A descrição 
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
				science and conservation communities. You can read the <a href="https://www.dropbox.com/scl/fi/lh9di7oo0hek25s8lnf73/African-Plants-2022-Final-Project-Description-only.pdf?rlkey=za4d08n016glut5ls9wjkamhx&dl=0" target="_blank">full project description here</a>.
			</p>

			<p>The African Plants Portal is broader in scope and is open to participation by any institutions wishing 
				to use this resource and/or contribute data. The ultimate goal of the portal is to enable an ever-expanding 
				global storehouse of plant biodiversity data regarding Africa, to facilitate a new generation of analyses 
				that will inform and illuminate the understanding and conservation of the continent's rich flora.</p>
		</div>
		<?php
			}
		?>
	</div>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
	<script type="text/javascript">
		setLanguageDiv();
	</script>
</body>
</html>
