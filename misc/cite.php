<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=" . $CHARSET);
?>
<html>

<head>
	<title>How to Cite</title>
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
		<b>How to Cite</b>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<h1 style="text-align: center;">How to Cite</h1>
		<h2 style="text-align: center;">Ways to Acknowledge and Cite the Use of the NEON Biorepository</h2>
		<p>The following provides guidelines for acknowledging and citing the use of NEON Biorepository resources, including samples and data, in your research publications.</p>
		<!-- Table of Contents -->
		<h2 class="anchor" id="dataset-publishing-toc">Table of Contents</h2>

		<ol>
			<li>
				<a href="#h.1">Acknowledging the NEON Biorepository as a used resource in scientific publications</a>
				<ol type="A">
					<li><a href="#h.1.a">Generic <i>acknowledgment</i> of the NEON Biorepository as a resource</a></li>
					<li><a href="#h.1.b">Generic <i>citation</i> of the NEON Biorepository as a resource</a></li>
				</ol>
			</li>
			<li>
				<a href="#h.2">Citing the use of the NEON Biorepository data portal</a>
				<ol type="A">
					<li><a href="#h.2.a">Citing the NEON Biorepository portal generally</a></li>
					<li><a href="#h.2.b">Citing particular NEON Biorepository <i>collections</i> as sources for occurrence data</a></li>
					<li><a href="#h.2.c">Citing a NEON Biorepository <i>published research</i> or <i>special collections dataset</i></a></li>
				</ol>
			</li>
			<li><a href="#h.3">Acknowledging and citing NEON data generally</a></li>
			<li><a href="#h.4">Occurrence Record Use Policy</a></li>
			<li><a href="#h.5">Images</a></li>
		</ol>
		<hr>
		<!-- End of Table of Contents -->
		<article>
			<h3 class="anchor" id="h.1">1. Acknowledging the NEON Biorepository as a used resource in scientific publications</h3>
			<h4 class="anchor" id="h.1.a">1A. Generic <i>acknowledgment</i> of the NEON Biorepository as a resource</h4>
			<p>You can promote use of NEON Biorepository resources with the following statement in the acknowledgement section of your relevant publications:</p>
			<blockquote>"The National Ecological Observatory Network Biorepository at Arizona State University provided samples and data collected as part of the NEON Program."</blockquote>
			<h4 class="anchor" id="h.1.b">1B. Generic <i>citation</i> of the NEON Biorepository as a resource</h4>
			<p>If the sampling scheme and design of the NEON Biorepository has been integral to facilitating your research, we encourage you to also cite the following publication that outlines its conceptualization and implementation:</p>
			<blockquote>Kelsey M Yule, Edward E Gilbert, Azhar P Husain, M Andrew Johnston, Laura Rocha Prado, Laura Steger, & Nico M Franz. (2020). Designing Biorepositories to Monitor Ecological and Evolutionary Responses to Change (Version 1). Zenodo. <a href="https://doi.org/10.5281/zenodo.3880411" target="_blank" rel="noopener noreferrer">https://doi.org/10.5281/zenodo.3880411</a></blockquote>
			<button><a href="#dataset-publishing-toc">Go back to TOC</a></button>
		</article>
		<article>
			<h3 class="anchor" id="h.2">2. Citing the use of the NEON Biorepository <i>data</i> portal</h3>
			<h4 class="anchor" id="h.2.a">2A. Citing the NEON Biorepository portal generally</h4>
			<p> When your work relies on occurrence data published by the NEON Biorepository, cite the following:
			<blockquote>
				<?php
				$citationFile = $SERVER_ROOT . '/includes/citationportal.php';
				if (file_exists($citationFile)) {
					include($citationFile);
				} else {
					echo 'Biodiversity occurrence data published by: NEON (National Ecological Observatory Network) Biorepository, Arizona State University Biodiversity Knowledge Integration Center (Accessed through the NEON Biorepository Data Portal, <a href="http//:biorepo.neonscience.org/" target="_blank" rel="noopener noreferrer">http//:biorepo.neonscience.org/</a>, ' . date('Y-m-d') . ')';
				}
				?>
			</blockquote>
			</p>
			<h4 class="anchor" id="h.2.b">2B. Citing particular NEON Biorepository <i>collections</i> as sources for occurrence data</h4>
			<p>When your work relies on occurrence data from particular NEON Biorepository collections, use the preferred citation format published on the relevant collection details page under "Cite This Collection".</p>
			<h4 class="anchor" id="h.2.c">2C. Citing a NEON Biorepository <i>published research</i> or <i>special collections dataset</i></h4>
			<p>To cite the use of occurrence records from an <a href="https://biorepo.neonscience.org/portal/collections/datasets/publiclist.php" target="_blank" rel="noopener noreferrer">existing published research or special collections dataset</a>, include the citations available from the relevant dataset page under "Cite This Dataset".</p>
			<p>In many cases, you should also cite the original publication associated with the dataset, which is available at the top of the dataset description page.</p>
			<button><a href="#dataset-publishing-toc">Go back to TOC</a></button>
		</article>
		<article>
			<h3 class="anchor" id="h.3">3. Acknowledging and citing NEON data generally</h3>
			<p>Research outputs using other NEON data and samples should also follow NEON <a href="https://www.neonscience.org/data-samples/guidelines-policies/citing" target="_blank" rel="noopener noreferrer">citation policies</a> and <a href="https://www.neonscience.org/data-samples/guidelines-policies/publishing-research-outputs" target="_blank" rel="noopener noreferrer">guidelines for publishing research output</a>.</p>
			<button><a href="#dataset-publishing-toc">Go back to TOC</a>
			</button>
		</article>
		<article>
			<h3 class="anchor" id="h.4">4. Occurrence Record Use Policy</h3>
			<ul>
				<li>While the NEON Biorepository Data Portal will make every effort possible to control and document the quality of the data it publishes, the data are made available "as is". Any report of errors in the data should be directed to the appropriate curators and/or collections managers.</li>
				<li>The NEON Biorepository Data Portal cannot assume responsibility for damages resulting from mis-use or mis-interpretation of datasets or from errors or omissions that may exist in the data.</li>
				<li>It is considered a matter of professional ethics to cite and acknowledge the work of other scientists that has resulted in data used in subsequent research. We encourage users to contact the original investigator responsible for the data that they are accessing.</li>
				<li>The NEON Biorepository Data Portal asks that users not redistribute data obtained from this site without permission for data owners. However, links or references to this site may be freely posted.</li>
			</ul>
			<button><a href="#dataset-publishing-toc">Go back to TOC</a>
		</article>
		<article>
			<h3 class="anchor" id="h.5">5. Images</h3>
			<p>Images within this website have been generously contributed by their owners to promote education and research. These contributors retain the full copyright for their images. Unless stated otherwise, images are made available under the Creative Commons Attribution-ShareAlike (<a href="https://creativecommons.org/licenses/by-sa/3.0/" target="_blank" rel="noopener noreferrer">CCBY-SA</a>). Users are allowed to copy, transmit, reuse, and/or adapt content, as long as attribution regarding the source of the content is made. If the content is altered, transformed, or enhanced, it may be re-distributed only under the same or similar license by which it was acquired.</p>
			<button><a href="#dataset-publishing-toc">Go back to TOC</a>
		</article>
	</div>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>

</html>