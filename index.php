<?php
include_once('config/symbini.php');
include_once('content/lang/index.' . $LANG_TAG . '.php');
header("Content-Type: text/html; charset=" . $CHARSET);
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
	<div class="navpath">
        </div>
	<div id="innertext">

		<h1>Welcome to the Symbiota Sandbox</h1>
		<div style="padding:10px;font-size:18px;margin:10px;">
		<p>The central purpose of this data portal is to provide a playground where general users can explore and experiment within the management tools available within 
		<a href="http://symbiota.org" target="_blank">Symbiota</a>. This portal has been primed with copies of several dataset. While the data is based on real specimen records, 
		it is not considered to be production quality and is meant to be modified by any user, right or wrongly. If you would like access to play within this sandbox, 
		first create a login by clicking on &quot;New Account&quot; link located to the upper right of page, and then contact us at the email below with details of 
		which tools you would like to explore. We also recommend that you first explore the 
		<a href="http://symbiota.org/docs/" target="_blank">help pages, tutorials, and training videos available on Symbiota Docs</a>.</p>

		<p>This portal is also meant to serve as an instruction platform to help in teaching students on how to enter specimen records, make labels, 
		and manage inventory species checklists within a Symbiota portal. If you are an instructor interested in incorporating this portal into your lesson plans, contact us and we will set you up with a custom dataset for your classroom and the necessary administrative permissions to add new students.</p>

		<p><u>Limits of this portal:</u> While this portal will accept records from any taxonomic domain, the taxonomic thesaurus and support data has been specifically tuned to 
		handle botanical specimens. Furthermore, the OCR tools work best when similar specimens have already been processed within the portal. For instance, these 
		tools would not perform as well with South American specimens with Spanish labels until a similar preprocessed dataset is loaded and the OCR stats tables were recalibrated. </p>

		<p><b>Warning:</b> The data in this portal is not guaranteed and regular backups are not maintained. Database could be reverted to a previous version at any time. 
		It is not recommended to manage production data within this portal. If you enter records that you want to maintain, make sure to periodically download your own backups of your dataset.</p>

		<p>If you would like access, contact us: <a href="mailto:help@symbiota.org?subject=Access to ASU Symbiota Sandbox">help@symbiota.org</a>
		</div>

	</div>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>

</html>

