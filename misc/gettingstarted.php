<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=" . $CHARSET);
?>
<html>

<head>
	<title>Getting Started & FAQ</title>
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
		<a href="<?php echo $CLIENT_ROOT; ?>/index.php">Home</a> >>;
		<b>Getting Started</b>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<h1 style="text-align: center;">Getting Started and Frequently Asked Questions</h1>
		<p>For tutorials, click <a href="<?php echo $CLIENT_ROOT; ?>/misc/tutorials.php" target="_blank" rel="noopener noreferrer">here</a>.</p>
		<!-- Table of Contents -->
		<h2 class="anchor" id="getting-started-toc">Table of Contents</h2>

		<ul>
			<h3>General Information</h3>
			<li><a href="#h.92fs9knk10ri">What is the NEON Biorepository?</a></li>
			<li><a href="#h.dupjcs7lsdqj">Where is the NEON Biorepository located</a>?</li>
			<li><a href="#h.svmnyswsw36">What is this portal (NEON Biorepository Data Portal)?</a></li>
		</ul>
		<ul>
			<h3>Collections and Samples</h3>
			<li><a href="#h.umiu0nb3f0np">How do I search for samples?</a></li>
			<li><a href="#h.o92dard0pqfu">How can I get more information about a collection that interests me?</a></li>
			<li><a href="#h.o8zdx5f5mqid">Why are there "External Collections" in your Data Portal? </a></li>
			<li><a href="#h.ec4rhmcfzhf">Some samples have a scientific name that contains a "/" (e.g. "<span style="font-style: italic">Peromyscus leucopus/maniculatus</span>). What do these names mean?</a>
			</li>
			<li><a href="#h.98ri20ckc2zf">I'd like to search for samples from ecological or functional groups that correspond to different NEON sampling protocols (e.g. "Invertebrates"). Is there a way to filter search results by these criteria?</a>
			</li>
			<li><a href="#h.g9ohhrjf8kao">I'd like to use your samples for research. How can I make a Sample Use Request?</a>
			</li>
		</ul>
		<ul>
			<h3>Issues and More</h3>
			<li><a href="#h.tuzvhhy4kbdq">Something is wrong with my search or with the portal. Where can I report bugs or ask for help?</a></li>
			<li><a href="#h.bnoo09ulc932">Where can I read about your Sample Use Policy?</a></li>
			<li><a href="#h.xh3whkfjnooh">Where can I read about your Data Usage Policy?</a></li>
			<li><a href="#h.1pdug3wj2wfu">How should I cite your data and acknowledge the Biorepository in my published work?</a></li>
			<li><a href="#h.1pdug3wj2waa">How can I publish my own sample-related data in the NEON Biorepository portal?</a></li>
			<li><a href="#h.9zbc8feqbj6m">Where can I find more information?</a></li>
		</ul>
		<hr>
		<!-- End of Table of Contents -->

		<article>
			<h3 class="anchor" id="h.92fs9knk10ri">What is the NEON Biorepository?</h3>
			<p>The NEON Biorepository at the <a href="https://biokic.asu.edu/collections" target="_blank" rel="noopener noreferrer">Arizona State University Biocollections</a> receives, curates, and makes nearly all of the samples collected by the <a href="https://www.neonscience.org/" target="_blank" rel="noopener noreferrer">National Ecological Observatory Network</a> (NEON) available to researchers. NEON aims to provide open data that will enable researchers, educators and the public to better understand how aquatic and terrestrial ecosystems are changing over time. Funded by the National Science Foundation and operated by Battelle Memorial Institute, the network publishes over <a href="https://data.neonscience.org/home" target="_blank" rel="noopener noreferrer">180 standardized ecological and environmental datasets</a> collected from 81 field sites across the United States, including Puerto Rico, Alaska, and Hawaii, and is intended to run for 30 years. NEON also collects over 100,000 biological and environmental samples and specimens each year from those sites, which are then analyzed and sent to the NEON Biorepository for future use.</p>
			<button><a href="#getting-started-toc">Go back to TOC</a></button>
		</article>

		<article>
			<h3 class="anchor" id="h.dupjcs7lsdqj">Where is the NEON Biorepository located?</h3>
			<p>The NEON Biorepository is part of the <a href="https://biokic.asu.edu/" target="_blank" rel="noopener noreferrer">Arizona State University Biodiversity Knowledge Integration Center</a> and located at the ASU Biocollections at <a href="https://www.asu.edu/map/interactive/?id=120%26mrkIid=66206" target="_blank" rel="noopener noreferrer">734 W Alameda Drive, Tempe, AZ 85282</a>.</p><button><a href="#getting-started-toc">Go back to TOC</a></button>
		</article>

		<article>
			<h3 class="anchor" id="h.svmnyswsw36">What is this portal (NEON Biorepository Data Portal)?</h3>
			<p>The <a href="https://biorepo.neonscience.org/" target="_blank" rel="noopener noreferrer">NEON Biorepository data portal</a> is a <a href="https://symbiota.org/" target="_blank" rel="noopener noreferrer">Symbiota-based collections portal</a> that allows you to:
			</p>
			<ol start="1">
				<li>Learn more about NEON samples and their suitability for your research interests</li>
				<li>Explore sample availability</li>
				<li>Initiate sample loan requests</li>
				<li>Find NEON sample use policies</li>
				<li>Contribute and publish your own value-added sample data</li>
			</ol>
			<p>While the main NEON data portal will often include observational data from data products relevant to a sample, the NEON Biorepository data portal collates all occurence information for those samples that are available for research.</p><button><a href="#getting-started-toc">Go back to TOC</a></button>
		</article>

		<article>
			<h3 class="anchor" id="h.umiu0nb3f0np">How do I search for samples?</h3>
			<p>There are several different ways that you can search for samples. View our tutorials <a href="<?php echo $CLIENT_ROOT; ?>/misc/tutorials.php" target="_blank" rel="noopener noreferrer">here</a> that provide step-by-step instructions for searching via the most common methods.</p><button><a href="#getting-started-toc">Go back to TOC</a></button>
		</article>

		<article>
			<h3 class="anchor" id="h.tuzvhhy4kbdq">Something is wrong with my search or with the portal. Where can I report bugs or ask for help?</h3>
			<p>You can also always contact us at <a href="mailto:biorepo@asu.edu">biorepo@asu.edu</a> with any questions or concerns. We also have a public GitHub repository for the NEON Biorepository <a href="https://github.com/BioKIC/NEON-Biorepository" target="_blank" rel="noopener noreferrer">Portal Development</a> where you can submit issues and feature requests online.</p>
			<button><a href="#getting-started-toc">Go back to TOC</a></button>
		</article>

		<article>
			<h3 class="anchor" id="h.o92dard0pqfu">How can I get more information about a collection that interests me?</h3>
			<p>You can read more about any collection by clicking the collection name or "more info..." link in the "Sample Search". <a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=26" target="_blank" rel="noopener noreferrer">This page for the fecal sample collection </a>provides an example. On these pages you will also find collection contacts, relevant NEON data products, ways to cite the collection, and summary statistics for the collection.</p>
			<button><a href="#getting-started-toc">Go back to TOC</a></button>
		</article>

		<article>
			<h3 class="anchor" id="h.o8zdx5f5mqid">Why are there "External Collections" in your Data Portal?</h3>
			<p>External Collections are of two types:</p>
			<ol start="1">
				<li><b>All Additional NEON Collections:</b> These are collections of NEON samples not held at the NEON Biorepository (e.g. carabid specimens at the Essig Museum of Entomology and legacy small mammal samples at the Museum of Southwestern Biology). These are generally legacy NEON samples collected before the initiation of the NEON Biorepository in late-2018 or reference collections held by contracted taxonomic experts. Include these samples to see results for all samples collected as part of NEON protocols.</li>
				<li><b>All Other Collections from NEON sites:</b> These are collections of non-NEON samples that were collected at what are now NEON sites (e.g. SCAN Portal Network Arthropod Specimens and SEINet Portal Network Botanical Specimens). These samples are not part of the NEON Biorepository and are generally not held at Arizona State University. Search these collections to better understand background measures of diversity at NEON sites.</li>
			</ol>
			<p>Do not select these collections in the Sample Search or Map Search collections pages if you only wish to explore NEON Biorepository samples. If samples of interest are not housed at the NEON Biorepository, access requires that researchers reach out to the relevant institution.</p>
			<button><a href="#getting-started-toc">Go back to TOC</a></button>
		</article>

		<article>
			<h3 class="anchor" id="h.ec4rhmcfzhf">Some samples have a scientific name that contains a "/" (e.g. "<span style="font-style: italic">Peromyscus leucopus/maniculatus"</span>). What do these names mean?</h3>
			<p>These samples are from a "species complex" meaning that the identification of the organism could not be resolved by the collectors or identifiers. Therefore, the "/" can be interpreted as "or." These identifications are rare and result when it is not possible to identify the individuals with certainty due to their life stage or the state of the organism. This most often occurs with tissue samples collected from live individuals of small mammal species that require examination of skull morphology to distinguish.</p>
			<p>When you search the portal for either of the species in the species complex with "Include Synonyms" checked, your results will include samples identified as the species for which you searched and those identified as the complex. When you search for the species complex with "Include Synonyms" checked, you will get samples from individuals identified as either of the species and as the complex. For any search with "Include Synonyms" unchecked, you will only receive verbatim results that match the name you provide in the taxonomic criteria with the name on the record.</p><button><a href="#getting-started-toc">Go back to TOC</a></button>
		</article>

		<article>
			<h3 class="anchor" id="h.98ri20ckc2zf">I'd like to search for samples from ecological or functional groups that correspond to different NEON sampling protocols (e.g. "Invertebrates", "Macroalgae"). Is there a way to filter search results by these criteria?</h3>
			<p>While this is atypical of Symbiota based data portals, you can search for a number of ecological or functional groups. Wherever you see "Taxonomic Criteria" in a search form, try inputting the group in which you are interested. If we have
				loaded the name of the group into our Taxonomic Tree, your search should result in all relevant samples. To check whether the name for which you are searching is loaded into our tree, search the <a href="https://biorepo.neonscience.org/portal/taxa/taxonomy/taxonomydynamicdisplay.php" target="_blank" rel="noopener noreferrer">Taxonomy Explorer</a>.</p><button><a href="#getting-started-toc">Go back to TOC</a></button>
		</article>

		<article>
			<h3 class="anchor" id="h.g9ohhrjf8kao">I'd like to use your samples for research. How can I make a Sample Use Request?</h3>
			<p>To request samples, navigate to the <a href="https://biorepo.neonscience.org/portal/misc/samplerequest.php" target="_blank" rel="noopener noreferrer">"Sample Request" </a>page found under "Sample Use" in the main menu. Make sure you read our Sample Use Policy.</p>
			<p>Always feel free to email us at <a href="mailto:biorepo@asu.edu">biorepo@asu.edu</a> with questions about samples and potential sample use. It is generally not possible or best to develop your own sample list unless you are well familiarized with NEON data and protocols. We are more than willing to help you develop those lists based on your research project goals.</p>
			<button><a href="#getting-started-toc">Go back to TOC</a></button>
		</article>

		<article>
			<h3 class="anchor" id="h.bnoo09ulc932">Where can I read about your Sample Use Policy?</h3>
			<p>To find information about appropropriate sample uses and about how decisions to loan samples are made, navigate to the "<a href="https://biorepo.neonscience.org/portal/misc/samplepolicy.php" target="_blank" rel="noopener noreferrer">Sample Use Policy</a>" page found under "Sample Use" in the main menu. On this page you will find a brief summary of our sample use policy and a link to the full <a href="https://biorepo.neonscience.org/portal/misc/NEON_Sample_Use_Policy_vC_2022.pdf" target="_blank" rel="noopener noreferrer">NEON Sample Use Policy.</a> These policies have been enacted to fairly balance current and potential future use and ensure that the data potential of all NEON samples is maximized. We encourage consumptive and destructive uses when the spatial, temporal and taxonomic breadth of remaining samples is maintained. Additionally, resulting data and remaining sample-related material should be made available to other researchers through the NEON Biorepository.</p>
			<button><a href="#getting-started-toc">Go back to TOC</a></button>
		</article>


		<article>
			<h3 class="anchor" id="h.xh3whkfjnooh">Where can I read about your Data Usage Policy?</h3>
			<p>To find guidelines for acceptable NEON Biorepository data use, navigate to the "<a href="https://biorepo.neonscience.org/portal/includes/cite.php" target="_blank" rel="noopener noreferrer">How to Cite</a>" page in the main menu. On this page you will find statements on appropriate use of sample data and images and recommended formats for citing NEON Biorepository data in your work.</p>
			<button><a href="#getting-started-toc">Go back to TOC</a></button>
		</article>

		<article>
			<h3 class="anchor" id="h.1pdug3wj2wfu">How should I cite your data and acknowledge the Biorepository in my published work?</h3>
			<p>Guidelines for when and how to acknowledge the NEON Biorepository resources and services that have contributed to published research outputs are outlined on the <a href="https://biorepo.neonscience.org/portal/misc/cite.php" target="_blank" rel="noopener noreferrer">"How to Cite"</a> page of this portal. Guidelines differ depending on whether general services, specific collections, or special datasets were used.</p>
			<button><a href="#getting-started-toc">Go back to TOC</a></button>
		</article>

		<article>
			<h3 class="anchor" id="h.1pdug3wj2waa">How can I publish my own sample-related data in the NEON Biorepository portal?</h3>
			<p>Publishing sample-related data and citations to the NEON Biorepository portal after sample use is a way to increase the visibility of your work, can fulfill funder and journal requirements that data be made openly available, and is often a requirement in our sample use agreements. General guidelines can be found on the <a href="https://biorepo.neonscience.org/portal/misc/datasetpublishing.php" target="_blank" rel="noopener noreferrer">"Dataset Publishing"</a> page, but the specifics of any particular sample use case will be determined in collaboration with the NEON Biorepository team.</p>
			<button><a href="#getting-started-toc">Go back to TOC</a></button>
		</article>

		<article>
			<h3 class="anchor" id="h.9zbc8feqbj6m">Where can I find more information?</h3>
			<p>Under "Additional Information" in the NEON Biorepository data portal main menu, you will find links to overviews of the <a href="https://www.neonscience.org/" target="_blank" rel="noopener noreferrer">NEON project</a>, main <a href="https://www.neonscience.org/data/about-data" target="_blank" rel="noopener noreferrer">NEON data portal</a>,
				<a href="https://symbiota.org/" target="_blank" rel="noopener noreferrer">Symbiota</a>, and the <a href="https://biokic.asu.edu/" target="_blank" rel="noopener noreferrer">Arizona State University Biocollections</a>. For any other information, please contact us
				at <a href="mailto:biorepo@asu.edu">biorepo@asu.edu</a>.
			</p>
			<button><a href="#getting-started-toc">Go back to TOC</a></button>
		</article>

	</div>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>

</html>