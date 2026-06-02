<?php
include_once("config/symbini.php");
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Home</title>
	<link href="css/base.css?<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="css/main.css?<?php echo $CSS_VERSION_LOCAL; ?>" type="text/css" rel="stylesheet" />
	<script type="text/javascript">
		<?php include_once($SERVER_ROOT.'/config/googleanalytics.php'); ?>
	</script>
</head>
<body>
	<?php
	include($SERVER_ROOT.'/header.php');
	?> 
	<!-- This is inner text! -->
	<div  id="innertext">
        <div style="float:right;width:380px;">
            <div style="clear:both;float:right;width:320px;float:right;margin-top:8px;margin-right:8px;padding:5px;-moz-border-radius:5px;-webkit-border-radius:5px;border:1px solid black;" >
                <div style="float:left;width:350px;">
                    <?php
                    $searchText = 'Taxon Search';
                    $buttonText = 'Search';
                    include_once($serverRoot.'/classes/PluginsManager.php');
                    $pluginManager = new PluginsManager();
                    $quicksearch = $pluginManager->createQuickSearch($buttonText,$searchText);
                    echo $quicksearch;
                    ?>
                </div>
            </div>
        </div>
        <div style="padding: 0px 10px;">
			Documenting Ethnobiology in Mexico and Central America (DEMCA) is a data portal in development for the
            presentation, exchange and discussion of traditional ecological knowledge, particularly the nomenclature,
            classification and symbolic and economic use of flora and fauna in Indigenous communities of this region.
            It will provide users registered as Project Managers or Community Liaisons with a mechanism to upload
            and share their own materials (textual, photographic, and audio) and to offer these materials for
            identification and commentary by the DEMCA community of users.
		</div><br>
        <div style="padding: 0px 10px;">
            Registered users will be able to search or browse DEMCA's resources through linguistic means (e.g,
            Indigenous nomenclature, the semantics of plant and animal names), project or community filters, functional
            uses (e.g, fencing, ointments), collection data (e.g., altitude), and Western nomenclature (e.g, by binomial
            name, browsing by genus). Eventually users will be able to tag entries, create and store records in
            personalized databases, and download these sets of linked entries to their computer. Audio recordings of
            narratives and discussions in Indigenous languages on local flora and fauna will be accessible through
            continuous or line-by-line playback and the contents will be displayed in transcription and translation
            (Spanish) formats. Eventually video will be incorporated.
        </div><br>
        <div style="padding: 0px 10px;">
            Development of the DEMCA data portal has been supported by the following programs:
            <a href="http://www.neh.gov/divisions/odh/grant-news/the-sug-program-no-more-please-welcome-digital-humanities-advancement-grants">
            National Endowment for the Humanities Digital Humanities Start-up grants</a>,
            <a href="https://www.nsf.gov/funding/pgm_summ.jsp?pims_id=12816">National Science Foundation, Documentation of
            Endangered Languages Program</a>, <a href="http://www.neh.gov/divisions/preservation/">National Endowment for the Humanities
            Division of Preservation and Access</a>, and the <a href="http://www.eldp.net/">Endangered Language Documentation Programme</a>.
            Software is based on <a href="http://symbiota.org/docs/">Symbiota</a>, data and web design by
            <a href="http://www.civicactions.com/">Civic Actions</a> (for more details on specific awards,
            see <a href="acknowledgements.php">Acknowledgements</a>. Usage Policy. Copyright © 2016.
        </div><br>
        <div style="padding: 0px 10px;">
            How to cite: [Authors], [Collection name] accessed at DEMCA: Documenting the Ethnobiology of Mexico and Central America (Jonathan
            D. Amith, project director), accessed on [day month year].
        </div>
	</div>

	<?php
	include($SERVER_ROOT.'/footer.php');
	?> 
</body>
</html>
