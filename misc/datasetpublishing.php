<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=" . $CHARSET);
?>
<html>

<head>
	<title>Dataset Publishing</title>
	<?php
	$activateJQuery = false;
	if (file_exists($SERVER_ROOT . '/includes/head.php')) {
		include_once($SERVER_ROOT . '/includes/head.php');
	} else {
		echo '<link href="' . $CLIENT_ROOT . '/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
		echo '<link href="' . $CLIENT_ROOT . '/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
		echo '<link href="' . $CLIENT_ROOT . '/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
	}
	?>
	<style>
		article {
			margin: 2rem 0;
		}

		button {
			width: fit-content;
		}

		.anchor {
			padding-top: 50px;
		}
	</style>
</head>

<body>
	<?php
	$displayLeftMenu = true;
	include($SERVER_ROOT . '/includes/header.php');
	?>
	<div class="navpath">
		<a href="<?php echo $CLIENT_ROOT; ?>/index.php">Home</a> >>
		<b>Dataset Publishing</b>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<h1 style="text-align: center;">Dataset Publishing</h1>
		<h2 style="text-align: center;">Publishing Research Output in the NEON Biorepository Data Portal</h2>
		<p>For all of the following, please consult with us at <a href="mailto:biorepo@asu.edu">biorepo@asu.edu</a> to identify the best joint solution for highlighting your sample-associated research publications and publishing your sample-associated data.</p>
		<!-- Table of Contents -->
		<h2 class="anchor" id="dataset-publishing-toc">Table of Contents</h2>

		<ol>
			<li><a href="#h.92fs9knk10ri">Link your publications as Associated References in NEON Biorepository occurrence records</a></li>
			<li><a href="#h.dupjcs7lsdqj">Create a NEON Biorepository Published Research Dataset</a></li>
			<li><a href="#h.svmnyswsw36">Publish value-added data to NEON Biorepository occurrence records</a></li>
		</ol>

		<!-- End of Table of Contents -->

		<article>
			<h3 class="anchor" id="h.92fs9knk10ri">1. Link your publications as Associated References in NEON Biorepository occurrence records</h3>
			<p>Researchers working with NEON Biorepository samples or sample data should provide NEON Biorepository personnel with a list of all samples used in their publications. We will then link citations to your work as Associated References in each sample occurrence record, highlighting your research contributions. For example, see the Associated References section of one such <a href="https://biorepo.neonscience.org/portal/collections/individual/index.php?occid=818122&clid=0" target="_blank" rel="noopener noreferrer">occurrence record</a>.</p>
			<button><a href="#dataset-publishing-toc">Go back to TOC</a></button>
		</article>

		<article>
			<h3 class="anchor" id="h.dupjcs7lsdqj">2. Create a NEON Biorepository Published Research Dataset</h3>
			<p>Lists of samples used in published work can be highlighted and promoted as <a href="https://biorepo.neonscience.org/portal/collections/datasets/publiclist.php" target="_blank" rel="noopener noreferrer">Published Research Dataset</a> within the NEON Biorepository data portal. These datasets and occurrence records therein can be annotated or expanded at any time, and static copies can be downloaded as Darwin Core Archives. These sample lists will be published to the Environmental Data Initiative portal, increasing the visibility of sample use in researhc.These datasets and their associated publications can then also be cited by future researchers, as outlined in relevant dataset pages, <a href="https://biorepo.neonscience.org/portal/collections/datasets/public.php?datasetid=157" target="_blank" rel="noopener noreferrer">such as this one</a>.</p>
			<button><a href="#dataset-publishing-toc">Go back to TOC</a></button>
		</article>

		<article>
			<h3 class="anchor" id="h.svmnyswsw36">3. Publish value-added data to NEON Biorepository occurrence records</h3>
			<p>The NEON Biorepository data portal is capable of hosting or linking to many forms of sample-associated data, including images, species determinations, genetic sequences, and trait data. Researchers using NEON Biorepository samples should work with NEON Biorepository personnel in order to disseminate their sample-associated data to the public. Doing so significantly increases the visibility of both the NEON Biorepository and your research and can often meet journal or funder requirements to provide open access to scientific data. Once your research results are linked to a sample occurrence record in the NEON Biorepository data portal, these data are then published to biodiversity data aggregators, such as the <a href="https://www.gbif.org/publisher/e794e60e-e558-4549-99f8-cfb241cdce24" target="_blank" rel="noopener noreferrer">Global Biodiversity Information Facility (GBIF)</a> portal further increasing their visibility to the broader research community.</p>
			<button><a href="#dataset-publishing-toc">Go back to TOC</a></button>
		</article>

	</div>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>

</html>