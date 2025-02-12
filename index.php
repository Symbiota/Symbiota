<?php
include_once('config/symbini.php');
if($LANG_TAG == 'en' || !file_exists($SERVER_ROOT.'/content/lang/templates/index.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT . '/content/lang/templates/index.en.php');
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
		<div id="quicksearchdiv">
		<!-- QUICK SEARCH SETTINGS -->
			<form name="quicksearch" id="quicksearch" action="<?php echo $CLIENT_ROOT; ?>/taxa/index.php" method="get" onsubmit="return verifyQuickSearch(this);">
				<div id="quicksearchtext" ><?php echo (isset($LANG['QSEARCH_SEARCH'])?$LANG['QSEARCH_SEARCH']:'Quick Search by Taxon'); ?></div>
				<input id="taxa" type="text" name="taxon" />
				<button name="formsubmit"  id="quicksearchbutton" type="submit" value="Search Terms"><?php echo (isset($LANG['QSEARCH_SEARCH_BUTTON'])?$LANG['QSEARCH_SEARCH_BUTTON']:'Search'); ?></button>
			</form>
		</div>
		<?php
		if($LANG_TAG == 'es'){
			?>
			<div>
				<h1 class="page-heading">Bienvenidos al Weevil Portal</h1>
				<p>Este portal está diseñado para funcionar como un recurso colaborativo para la integración de datos de la superfamilia
				Curculionoidea, agregando registros digitalizados desde otros <a href="https://symbiota.org">portales Symbiota</a> y desde
				la <a href="https://gbif.org.">Instalación Global de Información de Biodiversidad (GBIF)</a>. El portal  permite la
				generación de mapas, listados de especies y otros proyectos interactivos de datos de biodiversidad. Los usuarios
				especializados en taxonomía de gorgojos están invitados a unirse para contribuir con la curación del Tesauro Taxonómico.
				Los datos añadidos en el portal son de libre acceso, pero se insta a citar adecuadamente el origen de los mismos.
				</p>
				</p>
				El portal está alojado en los servidores del Centro de <a href="https://biokic.asu.edu/">Integración del Conocimiento de
				la Biodiversidad (BIOKIC)</a> de la Universidad Estatal de Arizona (ASU), en Estados Unidos. Para más información o para
				integrar datos por favor comunicarse con Samanta Orellana (<a href="mailto:sorellana@asu.edu">sorellana@asu.edu</a>) o
				Jennifer Girón (<a href="mailto:entiminae@gmail.com">entiminae@gmail.com</a>).
				</p>
			</div>
			<?php
		}
		else{
			//Default Language
			?>
			<div>
				<h1 class="page-heading">Welcome to the Weevil Portal</h1>
				<p>This portal aims to serve as a collaborative resource to integrate biodiversity data about the superfamily Curculionoidea,
				harvesting digitized records from other <a href="https://symbiota.org">Symbiota portals</a> and the
				<a href="https://gbif.org.">Global Biodiversity Information Facility (GBIF)</a>. The portal allows the generation of maps,
				checklists and other interactive biodiversity data projects. Users specialized in weevil taxonomy are invited to join and
				contribute to the curation of the Taxonomic Thesaurus. The data within the portal are freely available for use, but proper
				citation is encouraged.
				</p>
				<p>
				The Weevil Portal is hosted by the <a href="https://biokic.asu.edu/">Biodiversity Knowledge Integration Center (BIOKIC)</a>
				at Arizona State University, USA. For further information or to have a collection data ingested, please contact Samanta 
				Orellana (<a href="mailto:sorellana@asu.edu">sorellana@asu.edu</a>) or Jennifer Girón (<a href="mailto:entiminae@gmail.com">entiminae@gmail.com</a>).
				</p>
			</div>
			<?php
		}
		?>
	
	</main>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>
</html>
