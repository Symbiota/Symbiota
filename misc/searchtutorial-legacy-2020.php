<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=" . $CHARSET);
?>
<html>

<head>
	<title>Conduct a Sample Search</title>
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
			background-color: #ededed;
			padding: 10px;
			margin: 10px 0;
			border-radius: 6px;
		}

		figcaption {
			background-color: #002d74;
			color: #ffffff;
			padding: 0 1em;
		}

		figure {
			border: 1px solid #002d74;
			-webkit-box-shadow: 9px 10px 12px -10px rgba(0, 0, 0, 0.5);
			-moz-box-shadow: 9px 10px 12px -10px rgba(0, 0, 0, 0.5);
			box-shadow: 9px 10px 12px -10px rgba(0, 0, 0, 0.5);
		}
	</style>
</head>

<body>
	<?php
	$displayLeftMenu = true;
	include($SERVER_ROOT . '/includes/header.php');
	?>
	<div class="navpath">
		<a href="<?php echo $CLIENT_ROOT; ?>/index.php">Home</a> &gt;&gt;
		<a href="<?php echo $CLIENT_ROOT; ?>/misc/tutorials.php">Tutorials</a> &gt;&gt;
		<b>Conduct a Sample Search</b>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<h1 style="text-align: center;">Conduct a Sample Search</h1>
		<p>Use the Sample Search feature of the NEON Biorepository data portal to search for, download, and map available NEON samples based on collection, taxon, location, and more.</p>
		<p>To do so, follow the following steps (or download the tutorial <a href="<?php echo $CLIENT_ROOT; ?>/misc/20200427_portalTutorial.pdf">here</a>):</p>

		<article>
			<p>Navigate to <a href="https://biorepo.neonscience.org/portal/collections/index.php">Sample Search</a> under "Search" in the main menu.</p>
			<figure>
				<img src="../misc/images/tutorial_11.jpg" alt="">
				<figcaption>Navigate to <a href="https://biorepo.neonscience.org/portal/collections/index.php">Sample Search</a> under "Search" in the main menu.</figcaption>
			</figure>
		</article>

		<article>
			<p>Note the disclaimer at the top of the search form:</p>
			<figure>
				<img src="../misc/images/tutorial_25.jpg" alt="Note the disclaimer at the top of the search form">
				<figcaption>Note the disclaimer at the top of the search form</figcaption>
			</figure>
		</article>

		<article>
			<p>External Collections are of two types:</p>
			<ol>
				<li>Collections of NEON samples not held at the NEON Biorepository (e.g. Essig and the Museum of Southwestern Biology). These samples are generally legacy samples collected before the initiation of the NEON Biorepository in late-2018.</li>
				<li>Collections of non-NEON samples that were collected at what are now NEON sites. These samples are not part of the NEON Biorepository and are generally not held at Arizona State University. Search these collections to better understand background measures of diversity at NEON sites.</li>
			</ol>
			<p>Make sure to deselect these collections at the bottom of the page if you only wish to explore NEON Biorepository samples.</p>
			<figure>
				<img src="../misc/images/tutorial_35.jpg" alt="Note the disclaimer at the top of the search form">
				<figcaption>Note the disclaimer at the top of the search form</figcaption>
			</figure>
		</article>

		<article>
			<p>Also, note that not all sample types are available for research use at this time.</p>
			<figure>
				<img src="../misc/images/tutorial_5.jpg" alt="Note that not all sample types are available for research use at this time.">
				<figcaption>Note that not all sample types are available for research use at this time.</figcaption>
			</figure>
		</article>

		<article>
			<p>To be notified of when collections of interest are updated, fill out the linked <a href="https://docs.google.com/forms/d/e/1FAIpQLSeE0NCJfObUji6r9tRDuH4sSGyKHFDw2IoqVYHm9Vtg7cnKrg/viewform">Google Form</a>.</p>
			<ol>
				<li>To begin a sample search, select the collections that are of interest. Collections are broken down into five categories: Algae, Environmental, Invertebrate, Plant, Vertebrate, and External. For illustration, we will search for samples relevant to deer mouse physiology in the western continental United States. Therefore, we will begin by finding the small mammal fecal and hair samples under Vertebrates.</li>
				<li>Note that you can read more about any collection by clicking the "more info..." link at the end of the collection name. To see an example navigate to <a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=26">this page for the fecal sample collection.</a></li>
			</ol>
			<figure>
				<img src="../misc/images/tutorial_32.jpg" alt="Click on more info to open collection profiles">
				<figcaption>Click on more info to open collection profiles</figcaption>
			</figure>
		</article>

		<article>
			<p>Here, we can read a description and see the metadata for that collection.</p>
			<figure>
				<img src="../misc/images/tutorial_39.jpg" alt="Collection metadata">
				<figcaption>Collection metadata</figcaption>
			</figure>
			<p>Note that this information includes a link to a related NEON data product for small mammal captures using the <a href="https://data.neonscience.org/data-products/DP1.10072.001">Related Link</a></p>
		</article>

		<article>
			<p>At the bottom of the page, we can see some summary statistics for the collection. We can click on "Show Geographic Distribution" and "Show Family Distribution" to explore the number of samples at different geographic and taxonomic scales.</p>
			<figure>
				<img src="../misc/images/tutorial_14.jpg" alt="Collection profile">
				<figcaption>Collection profile</figcaption>
			</figure>
		</article>

		<article>
			<p>Returning to the "Sample Search" page, we select the mammal fecal and hair collections.</p>
			<figure>
				<img src="../misc/images/tutorial_20.jpg" alt="Example collections">
				<figcaption>Example collections</figcaption>
			</figure>
		</article>

		<article>
			<p>Then, we will scroll back to the top of the page and click "Search".</p>
			<figure>
				<img src="../misc/images/tutorial_15.jpg" alt="Click search">
				<figcaption>Click search</figcaption>
			</figure>
		</article>

		<article>
			<p>The next page brings us to a search form with several optional criteria. Only one criterion must be applied to conduct your search, but many criteria can be used to produce a narrower set of results.</p>
			<figure>
				<img src="../misc/images/tutorial_8.jpg" alt="Search criteria">
				<figcaption>Search criteria</figcaption>
			</figure>
		</article>

		<article>
			<p>Some commonly used criteria are explained below.</p>
			<ol>
				<li>
					<p>Taxonomic Criteria can be applied. When searching based on a taxon loaded into the portal’s taxonomic tree, suggested criteria will help you populate the text. For illustration, we will search for deer mice (Peromyscus) specimens. With "include Synonyms" checked, we will see all specimens identified as being from within that genus. Otherwise we would see only those specimens identified as Peromyscus but not to species.</p>
					<figure>
						<img src="../misc/images/tutorial_26.jpg" alt="Taxonomic criteria">
						<figcaption>Taxonomic criteria</figcaption>
					</figure>
				</li>
				<li>
					<p>We can also search by Locality criteria. For this example, we will leave this blank, but we could narrow our search by state, county, or elevational range.</p>
					<figure>
						<img src="../misc/images/tutorial_17.jpg" alt="Locality criteria">
						<figcaption>Locality criteria</figcaption>
					</figure>
				</li>
				<li>
					<p>Latitude and Longitude criteria can also be applied using a bounding box, spatial polygon, or point-radius area. Here, we will limit our search to within a bounding box drawn in the Google Earth pop-up window to correspond roughly with the western half of the continental United States.</p>
					<figure>
						<img src="../misc/images/tutorial_7.jpg" alt="Spatial criteria">
						<figcaption>Spatial criteria</figcaption>
					</figure>
				</li>
				<li>
					<p>Other Collector and Specimen Criteria can be used to limit the search. Of interest for a select number of NEON collections is the ability to "Limit to Specimens with Genetic Data." This search will limit results to those linked to sequences available in the <a class="c5" href="http://www.boldsystems.org/">Barcode of Life Database (BOLD)</a>.</p>
					<figure>
						<img src="../misc/images/tutorial_31.jpg" alt="Other criteria">
						<figcaption>Other criteria</figcaption>
					</figure>
				</li>
				<li>
					<p>When all search criteria of interest have been applied, we will click "List Display" either at the top or the bottom of the search form.</p>
					<figure>
						<img src="../misc/images/tutorial_6.jpg" alt="List display option">
						<figcaption>List display option</figcaption>
					</figure>
				</li>
			</ol>
		</article>

		<article>
			<p>This brings us to the "Occurrence Records" tab of the <a href="https://biorepo.neonscience.org/portal/collections/list.php?db=26%2C27&llbound=49.15165%3B23.42108%3B-126.20996%3B-93.64648&taxa=Peromyscus&usethes=1&taxontype=2">search results</a>. At the top of that page, you see the search criteria used to generate the results. We can see that as of April 23, 2020, 3379 samples from the NEON-MAMC-FE (fecal) and NEON-MAMC-HA (hair) collections met our criteria.</p>
			<figure>
				<img src="../misc/images/tutorial_44.jpg" alt="Example search results">
				<figcaption>Example search results</figcaption>
			</figure>
		</article>

		<article>
			<p>Note that you can click the link button on the upper right corner of the page to copy a URL for these search results to your clipboard.</p>
			<figure>
				<img src="../misc/images/tutorial_46.jpg" alt="Copy search results URL for future reference">
				<figcaption>Copy search results URL for future reference</figcaption>
			</figure>
		</article>

		<article>
			<p>We can also click the download button on the upper right corner of the page to download the results.</p>
			<figure>
				<img src="../misc/images/tutorial_29.jpg" alt="Download search results">
				<figcaption>Download search results</figcaption>
			</figure>
		</article>

		<article>
			<p>This will bring up a pop-up window where we can select whether we would like to download our results as a <a href="https://symbiota.org/docs/">Symbiota</a> Native or <a href="https://www.tdwg.org/standards/dwc/">Darwin Core Archive</a> file. These formats are very similar, but Symbiota Native files supports more fields. Click the icon to the right of these names for brief descriptions of these file structures.</p>
			<figure>
				<img src="../misc/images/tutorial_36.jpg" alt="Download options">
				<figcaption>Download options</figcaption>
			</figure>
			<p>We can also choose the data extensions (determination history and/or images) that we would like to include in our download, the file format, and whether we would like the results as a zip file. We select "Download Data" when we have identified our preferences.</p>
		</article>

		<article>
			<p>In a default download, we will see a folder like below in which the "occurences.csv" file is the primary results file containing a table of all available sample-associated data.</p>
			<figure>
				<img src="../misc/images/tutorial_18.jpg" alt="DwCA contents">
				<figcaption>DwCA contents</figcaption>
			</figure>
		</article>

		<article>
			<p>Returning to the portal results, we will navigate to the "Species List" tab to see a list of all taxa represented in the results.</p>
			<figure>
				<img src="../misc/images/tutorial_33.jpg" alt="Species list tab in results page">
				<figcaption>Species list tab in results page</figcaption>
			</figure>
		</article>

		<article>
			<p>Note that you click on any of the taxon names to read more about that taxon. For some taxa, this page will include photos and/or detailed descriptions of the tax.</p>
			<figure>
				<img src="../misc/images/tutorial_30.jpg" alt="Taxon profile">
				<figcaption>Taxon profile</figcaption>
			</figure>
		</article>

		<article>
			<p>From there, you can click "Open Interactive Map" underneath the main text box on the Taxon Page to view the collection locations for samples from that taxon.</p>
			<figure>
				<img src="../misc/images/tutorial_43.jpg" alt="Interactive Map window">
				<figcaption>Interactive Map window</figcaption>
			</figure>
		</article>

		<article>
			<p>Back to the main Occurrence Records Tab, we can scroll through to explore the resulting records.</p>
			<figure>
				<img src="../misc/images/tutorial_12.jpg" alt="Search results records">
				<figcaption>Search results records</figcaption>
			</figure>
		</article>

		<article>
			<p>Clicking on the species name to go to the Taxon Page and learn more about the identified taxon, as we could from the Species List tab.</p>
			<figure>
				<img src="../misc/images/tutorial_13.jpg" alt="Taxon profile link">
				<figcaption>Taxon profile link</figcaption>
			</figure>
		</article>

		<article>
			<p>Clicking on "Full Record Details" opens a pop-up window that allows us to read more about an individual sample.</p>
			<figure>
				<img src="../misc/images/tutorial_47.jpg" alt="Figure Caption">
				<figcaption>Full record details link</figcaption>
			</figure>
		</article>

		<article>
			<p>In that pop-up window, we will see much of the available data relevant to that individual samples.</p>
			<figure>
				<img src="../misc/images/tutorial_4.jpg" alt="Full record details pop-up window">
				<figcaption>Full record details pop-up window</figcaption>
			</figure>
			<p>Some samples will have other information available, such as links to publications and online datasets using the sample.</p>
		</article>

		<article>
			<p>Back to the main search results page, we can navigate to the "Maps" tab to map of search results.</p>
			<figure>
				<img src="../misc/images/tutorial_41.jpg" alt="Maps tab of search results">
				<figcaption>Maps tab of search results</figcaption>
			</figure>
		</article>

		<article>
			<p>We can click "Display Coordinates in Google Map" to visualize the collection locations of the samples in a pop-up Google Maps window.</p>
			<figure>
				<img src="../misc/images/tutorial_10.jpg" alt="Google Map with coordinates">
				<figcaption>Google Map with coordinates</figcaption>
			</figure>
		</article>

		<article>
			<p>We can zoom in and click on individual markers to see the "Full Record Details" pop-up window for the corresponding sample like that we saw above from the "Occurrence Records" tab.</p>
			<figure>
				<img src="../misc/images/tutorial_34.jpg" alt="Occurrence markers">
				<figcaption>Occurrence markers</figcaption>
			</figure>
		</article>

		<article>
			<p>Back on the "Maps" tab in the search results, we can download a KML file of occurrences suitable for mapping in Google Earth.</p>
			<figure>
				<img src="../misc/images/tutorial_40.jpg" alt="Maps tab in search results">
				<figcaption>Maps tab in search results</figcaption>
			</figure>
		</article>

		<article>
			<p>Note that you can click "Add Extra Fields" to select additional Symbiota fields to include in the KML download.</p>
			<figure>
				<img src="../misc/images/tutorial_1.jpg" alt="Google Earth KML option">
				<figcaption>Google Earth KML option</figcaption>
			</figure>
		</article>
	</div>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>

</html>