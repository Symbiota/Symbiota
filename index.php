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
		<div class="lang en">
			<h1>African Herbaria</h1>
			<h2>Collaborative Biodiversity Portal</h2>
			<p>This portal provides access to digitized herbarium specimen data of African plants mobilized by the <a href"https://www.idigbio.org/wiki/index.php/TCN:_Collaborative_Research:_Digitization_and_Enrichment_of_U.S._Herbarium_Data_from_Tropical_Africa_to_Enable_Urgent_Quantitative_Conservation_Assessments">Tropical Africa TCN</a> and other digitization efforts.</p>
		</div>
	</div>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
	<script type="text/javascript">
		setLanguageDiv();
	</script>
</body>
</html>
