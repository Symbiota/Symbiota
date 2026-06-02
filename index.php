<?php
include_once('config/symbini.php');
if($LANG_TAG == 'en' || !file_exists($SERVER_ROOT.'/content/lang/index.'.$LANG_TAG.'.php'))
	include_once($SERVER_ROOT.'/content/lang/index.en.php');
else include_once($SERVER_ROOT.'/content/lang/index.'.$LANG_TAG.'.php');
header('Content-Type: text/html; charset=' . $CHARSET);
?>
<html>
<head>
	<title><?= $DEFAULT_TITLE; ?> Home</title>
	<?php
	include_once($SERVER_ROOT . '/includes/head.php');
	include_once($SERVER_ROOT . '/includes/googleanalytics.php');
	?>
	<link href="<?= $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<link href="<?= $CSS_BASE_PATH; ?>/quicksearch.css?ver=1d" type="text/css" rel="Stylesheet" />
	<script src="js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="js/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		var clientRoot = "<?= $CLIENT_ROOT ?>";
	</script>
	<script src="js/symb/api.taxonomy.taxasuggest.js" type="text/javascript"></script>
	<style>
		p{ padding: 0px 10px; }
	</style>
</head>
<body>
	<?php
	include($SERVER_ROOT . '/includes/header.php');
	?>
	<div id="innertext">
		<div id="quicksearchdiv">
			<!-- -------------------------QUICK SEARCH SETTINGS--------------------------------------- -->
			<form name="quicksearch" id="quicksearch" action="<?= $CLIENT_ROOT; ?>/taxa/index.php" method="get" onsubmit="return verifyQuickSearch(this);">
				<div id="quicksearchtext" ><?= $LANG['QSEARCH_SEARCH'] ?></div>
				<input id="taxa" type="text" name="taxon" />
				<button name="formsubmit"  id="quicksearchbutton" type="submit" value="Search Terms"><?= $LANG['QSEARCH_SEARCH_BUTTON'] ?></button>
			</form>
		</div>
		<?php
		if($LANG_TAG == 'en'){
			?>
			<div>
				<p>
					<b>Documenting Ethnobiology in Mexico and Central America (DEMCA)</b> is a data portal in development for the
					presentation, exchange and discussion of traditional ecological knowledge, particularly the nomenclature,
					classification and symbolic and economic use of flora and fauna in Indigenous communities of this region.
					It will provide users registered as Project Managers or Community Liaisons with a mechanism to upload
					and share their own materials (textual, photographic, and audio) and to offer these materials for
					identification and commentary by the DEMCA community of users.
				</p>
				<p>
					Registered users will be able to search or browse DEMCA's resources through linguistic means (e.g,
					Indigenous nomenclature, the semantics of plant and animal names), project or community filters, functional
					uses (e.g, fencing, ointments), collection data (e.g., altitude), and Western nomenclature (e.g, by binomial
					name, browsing by genus). Eventually users will be able to tag entries, create and store records in
					personalized databases, and download these sets of linked entries to their computer. Audio recordings of
					narratives and discussions in Indigenous languages on local flora and fauna will be accessible through
					continuous or line-by-line playback and the contents will be displayed in transcription and translation
					(Spanish) formats. Eventually video will be incorporated.
				</p>
				<p>
					Development of the DEMCA data portal has been supported by the following programs:
					<a href="http://www.neh.gov/divisions/odh/grant-news/the-sug-program-no-more-please-welcome-digital-humanities-advancement-grants" target="_blank">
					National Endowment for the Humanities Digital Humanities Start-up grants</a>,
					<a href="https://www.nsf.gov/funding/pgm_summ.jsp?pims_id=12816" target="_blank">National Science Foundation, Documentation of
					Endangered Languages Program</a>, <a href="http://www.neh.gov/divisions/preservation/" target="_blank">National Endowment for the Humanities
					Division of Preservation and Access</a>, and the <a href="http://www.eldp.net/" target="_blank">Endangered Language Documentation Programme</a>.
					Software is based on <a href="http://symbiota.org" target="_blank">Symbiota</a>.
				</p>
				<p>
					How to cite: [Authors], [Collection name] accessed at DEMCA: Documenting the Ethnobiology of Mexico and Central America (Jonathan
					D. Amith, project director), accessed on [day month year].
				</p>
			</div>
			<?php
		}
		else{
			//Default Language (Spanish)
			?>
			<div>
				<p>
					<b>Documentando la Etnobiología en México y América Central (DEMCA)</b> es un portal de datos en desarrollo para la
					presentación, intercambio y discusión del conocimiento ecológico tradicional, particularmente la nomenclatura,
					clasificación y usos simbólicos y económicos de la flora y fauna en comunidades indígenas de esta región.
					Busca proveerles a los usuarios registrados como Gestores de Proyectos o Enlaces Comunitarios,
					un mecanismo para subir (poner en línea) y compartir sus propios materiales (textos, fotografías y audios),
					así como ofrecer estos materiales para identificación y comentarios por parte de la comunidad de usuarios de DEMCA.
				</p>
				<p>
					Usuarios registrados podrán buscar o navegar por los recursos de DEMCA a través de medios lingüísticos
					(p. ej., Nomenclatura indígena o Semántica de los nombres de plantas y animales), filtros de proyectos o
					comunidades, usos funcionales (p. ej., cercas, ungüentos), datos de recolección (p. ej., altitud) y
					nomenclatura occidental (p. ej., nombre binomial científico, navegación por género).
					Con el tiempo los usuarios registrados podrán etiquetar entradas, crear y almacenar registros de
					bases de datos personalizadas, y descargar a su ordenador estos conjuntos de entradas vinculadas.
					Podrán acceder a grabaciones de audio de narraciones y conversaciones en lenguas indígenas sobre la
					flora y fauna local mediante reproducción continua o línea por línea, y los contenidos se mostrarán en
					formatos de transcripción y traducción (español). Más adelante se planea incorporar video.
				</p>
				<p>
					El desarrollo del portal de datos DEMCA ha contado con el apoyo de los siguientes programas:
					<a href="http://www.neh.gov/divisions/odh/grant-news/the-sug-program-no-more-please-welcome-digital-humanities-advancement-grants" target="_blank">
					National Endowment for the Humanities Digital Humanities Start-up grants</a>,
					<a href="https://www.nsf.gov/funding/pgm_summ.jsp?pims_id=12816" target="_blank">National Science Foundation, Documentation of Endangered Languages Program</a>,
					<a href="http://www.neh.gov/divisions/preservation/" target="_blank">National Endowment for the Humanities Division of Preservation and Access</a>,
					y el <a href="http://www.eldp.net/" target="_blank">Endangered Language Documentation Programme</a>.
					El software está basado en <a href="http://symbiota.org" target="_blank">Symbiota</a>.
				</p>
				<p>
					Cómo citar: [Autores], [Nombre de la colección] consultado en DEMCA: Documenting the Ethnobiology of
					Mexico and Central America (Jonathan D. Amith, director del proyecto), consultado el [día, mes, año]
				</p>
			</div>
			<?php
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>
</html>
