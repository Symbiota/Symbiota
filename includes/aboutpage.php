<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once ($SERVER_ROOT . '/classes/utilities/GeneralUtil.php');

Language::load('templates/aboutpage');

header("Content-Type: text/html; charset=" . $CHARSET);
$serverHost = GeneralUtil::getDomain();
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title><?php echo $DEFAULT_TITLE . " " . "About CCH"; ?></title>
	<?php
	include_once($SERVER_ROOT . '/includes/head.php');
	?>
    <style>

        h2 {
            text-align: center;
        }

        .image-container {
            text-align: center;
        }

        .images {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .images img {
            height: 400px;
        }
        .caption {
            text-align: center;
            margin-top: 10px;
            font-style: italic;
        }
    </style>
</head>

<body>
	<?php
	include($SERVER_ROOT . '/includes/header.php');
	?>
	<div class="navpath">
		<a href="<?php echo htmlspecialchars($CLIENT_ROOT, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>/index.php">Home</a> &gt;&gt;
		<b>About CCH</b>
	</div>
	<!-- This is inner text! -->
	<div role="main" id="innertext">
		<h1 class="page-heading">About the California Consortia of Herbaria</h1>
		<p>The Consortium of California Herbaria (CCH) is an association of herbarium collections at universities,
            research stations, natural history museums, and botanical gardens. The CCH currently comprises 84 accredited (i.e., listed on 
        <a href="https://sweetgum.nybg.org/science/ih/" target="_blank">Index Herbariorium</a>)
       herbaria, with more being added. CCH members are integral to the thriving collections community of California.
       Member institutions share knowledge and expertise, share resources, and support excellence in collections curation.
        </p>
        <p>
            All of these herbaria share and curate their data of vascular plant collections on the CCH2 Data Portal.
            Some of these herbaria maintain their algae, bryophytes, fungi, pteridophytes, and/or lichen collections on those respective portals.
            Most herbaria of the CCH are located in the state of California, but some are located outside the state,
            sharing either all of their collection data or only that for California plants with the CCH2 Data Portal.
        </p>
        <p>
            The CCH now shares 5,250,000 herbarium records in this portal. Most of the CCH herbaria also upload their records to 
            <a href="https://www.gbif.org/" target="_blank">GBIF</a>,
            the Global Biological Information Facility, which hosts worldwide records of biological collections and images (see an example 
            <a href="https://www.gbif.org/species/2925739" target="_blank">here</a>).
        </p>
        <p>
            CCH2 uses the software 
            <a href="https://symbiota.org/" target="_blank">Symbiota</a>,
            an open-source biodiversity data management software that has become the standard for many biological collections around the world.
            This database has numerous and diverse capabilities, only a few of which are exemplified here:
        </p>

        <h2>
            <?php echo "SEARCHES & MAPPING" ?>
        </h2>
        <p>
            CCH2 allows for 
            <a href="https://cch2.org/portal/collections/search/index.php" target="_blank">Searches</a>
            across multiple fields, such as taxonomic name(s), locality, latitude/longitude/elevation, collector/number/date,
            catalog (accession/barcode) number, numerous trait and association fields, and collection (specifying all or a subset of herbaria).
            One or more taxa are easily
            <a href="https://cch2.org/portal/collections/map/index.php" target="_blank">mapped</a>
            in CCH2 to illustrate their distribution, the points linked to current herbarium records and easily color-coded by taxon.
        </p>
        <div class="image-container">
            <div class="images">
                <a href="https://cch2.org/portal/collections/search/index.php">
                    <img src="<?php echo $CLIENT_ROOT . '/images/Picture1.png'; ?>" alt="Image 1">
                </a>
                <a href="https://cch2.org/portal/collections/map/index.php?taxa=Pectocarya%20linearis&usethes=1&taxontype=2&association-type=none&comingFrom=newsearch&gridSizeSetting=60&minClusterSetting=10&clusterSwitch=y&menuClosed">
                    <img src="<?php echo $CLIENT_ROOT . '/images/Picture2.png'; ?>" alt="Image 2">
                </a>
            </div>
            <div class="caption">
                Left: Search data fields of the Symbiota-based CCH2. Right: Map of CCH2 collection data
                of <u>Pectocarya linearis</u> (Boraginaceae). Click the image above to be taken to the dynamic map. On the dynamic map, 
				click on a point on the map to see the herbarium record.
            </div>
        </div>
        <h2>
            <?php echo "CHECKLISTS" ?>
        </h2>
        <p>
            CCH2 allows for creation of taxon
            <a href="https://cch2.org/portal/checklists/index.php" target="_blank">Checklists</a>
            which can be used to list and link all herbarium records of a given region. These can serve as definitive records of documented plants
            in that region, from which a printed checklist may easily be generated. In addition,
            <a href="https://www.inaturalist.org/" target="_blank">iNaturalist</a> and
            <a href="https://www.calflora.org/" target="_blank">Calflora</a>
            records of a taxon in that specified region may be linked and used as additional taxon documentation for the checklist.
        </p>

        <div class="image-container">
            <div class="images">
                <a href="https://cch2.org/portal/checklists/checklist.php?clid=82&emode=0">
                    <img src="<?php echo $CLIENT_ROOT . '/images/Picture3.png'; ?>" alt="Image 3">
                </a>
                <a href="https://cch2.org/portal/checklists/checklistmap.php?clid=82&thesfilter=0&taxonfilter=">
                    <img src="<?php echo $CLIENT_ROOT . '/images/Picture4.png'; ?>" alt="Image 4">
                </a>
            </div>
            <div class="caption">
                An example of the Checklist function of CCH2. Click the left image to see the checklist and the right image to see a map of linked voucher specimens in the checklist.
            </div>
        </div>

        <h2>
            <?php echo "MEDIA" ?>
        </h2>
        <p>
            CCH2 shares  
            <a href="https://cch2.org/portal/imagelib/search.php?taxontype=2&usethes=1&tagExistance=1" target="_blank">media</a>
            associated with taxa and specimens in the portal. Media generally consist of high-resolution
            photographs of herbarium sheet vouchers, an example seen 
            <a href="https://cch2.org/portal/taxa/index.php?tid=202621" target="_blank">here</a>.
            However, other images of a plant collection, taken from the field or lab, may be uploaded to a CCH2 record, as exemplified
            <a href="https://cch2.org/portal/collections/individual/index.php?occid=1298386&clid=0" target="_blank">here</a>.
            Observations on
            <a href="https://www.inaturalist.org/" target="_blank">iNaturalist</a>
            of a specimen collected may also be linked to a given herbarium record, as seen
            <a href="https://cch2.org/portal/collections/individual/index.php?occid=5803812" target="_blank">here</a>.
            A recent addition allows uploads of audio files.
        </p>

       <h2>
            <?php echo "PHENOLOGY" ?>
        </h2>
        <p>
            Herbarium records may now be used to assess the timing of phenological events (e.g., timing of flowering and fruiting) of taxa, exemplified
            <a href=https://cch2.org/portal/taxa/index.php?tid=202621" target="_blank">here</a>.
			Taxon pages in CCH2 include a tally of number of records scored for a given phenological status by month (see below). 
			These data allow for study of phenological changes over time and space.
        </p>

       <div class="image-container">
            <div class="images">
                <a href="https://cch2.org/portal/taxa/index.php?tid=211646" target="_blank">
                    <img src="<?php echo $CLIENT_ROOT . '/images/Picture5.png'; ?>" alt="Image 5">
                </a>
            </div>
            <div class="caption">
            Specimen image and phenological data of Calochortus tuburonensis. Click to see details. 
            </div>
        </div>

        <h2>
            <?php echo "ASSOCIATIONS: THE EXTENDED SPECIMEN" ?>
        </h2>
        <p>
            CCH2 allows linking of external data, such as genetic data derived from specimens. 
			For example, sequence data derived from tissue of a sample
            may now be linked to the <a href="https://www.ncbi.nlm.nih.gov/nucleotide/">NCBI</a> (National Center for Biotechnology Information) or 
            <a href="https://www.boldsystems.org/">Barcode of Life</a> record housing those data, as seen
            <a href="https://cch2.org/portal/collections/individual/index.php?occid=393367&clid=0" target="_blank">here</a>
            (click the Genetic tab). See <a href="https://docs.symbiota.org/Editor_Guide/linking_records/#linking-to-genetic-resources-and-sequences">this article</a> 
            for more information about linking records to genetic resources.
        </p>
        <p>
            In addition, CCH2 allows linkages between institutions for developing Extended Specimen Networks; see examples below.
        </p>
        <div class="image-container">
            <div class="images">
                <a href="https://cch2.org/portal/collections/list.php?db=all%2C70%2C99%2C85%2C84%2C67%2C65%2C68%2C114%2C94%2C63%2C115%2C62%2C58%2C2%2C14%2C110%2C98%2C48%2C1%2C52%2C50%2C44%2C61%2C109%2C42%2C47%2C39%2C107%2C77%2C111%2C38%2C73%2C9%2C95%2C5%2C59%2C45%2C55%2C37%2C54%2C53%2C100%2C101%2C10%2C125%2C34%2C97%2C19%2C86%2C36%2C108%2C71%2C96%2C74%2C12%2C81%2C49%2C117%2C56%2C104%2C76%2C4%2C89%2C17%2C118%2C105%2C51%2C16%2C102%2C7%2C119%2C43%2C112%2C106%2C6%2C40%2C41%2C69%2C18%2C123%2C82%2C15%2C87%2C75%2C46%2C64%2C3%2C35%2C13%2C124%2C80%2C91%2C57%2C103%2C116%2C60%2C92%2C72%2C79&includeothercatnum=1&includecult=1&usethes=1&taxontype=2&collnum=4342&collector=Carlquist&association-type=none&taxontype-association=2&usethes-associations=1&comingFrom=newsearch">
                    <img src="<?php echo $CLIENT_ROOT . '/images/Picture6.png'; ?>" alt="Image 6">
                </a>
                <a href="https://cch2.org/portal/collections/individual/index.php?occid=3792483&clid=0">
                    <img src="<?php echo $CLIENT_ROOT . '/images/Picture7.png'; ?>" alt="Image 7">
                </a>
            </div>
            <div class="caption">
                Left: Linked resources of a CCH2 (RSA) herbarium specimen to liquid-preserved material, wood samples, and prepared microscope slide images of wood sections.
                Right: Link of RSA specimen to BRIT (Botanical Research Institute of Texas) resources. 

            </div>
        </div>
        <p>
            Funding for the CCH2 was provided by a 2018 NSF award to create the
            <a href="https://www.capturingcaliforniasflowers.org/" target="_blank">California Phenology Thematic Collections Network</a>.
            This project generated over 1 million images (currently many more) of specimens that can now be viewed in this portal,
            and more are added every day. To advance research of the California flora and the impacts of climate change,
            this project also developed phenological data for the specimens, and those data can be downloaded from CCH2. 
            The portal is managed the Symbiota Support Hub. See the CCH2 homepage for more information, including the
            <a href="https://cch2.org/portal/includes/usagepolicy.php" target="_blank">Data Use</a>
            policy and contacts.
        </p>

        <p>
            If you maintain an herbarium of California plants and wish to join the CCH, please contact
            <a href="mailto:jyost@calpoly.edu" target="_blank">Jenn Yost</a>
            for guidelines and help in setting up a CCH2 collection.
        </p>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>
</html>
