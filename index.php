
<?php
include_once('config/symbini.php');
include_once('content/lang/index.'.$LANG_TAG.'.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
<head>
      	<title><?php echo $DEFAULT_TITLE; ?> Home</title>
        <?php
    $activateJQuery = true;
    if(file_exists($SERVER_ROOT.'/includes/head.php')){
      include_once($SERVER_ROOT.'/includes/head.php');
    }
    else{
      echo '<link href="'.$CLIENT_ROOT.'/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
      echo '<link href="'.$CLIENT_ROOT.'/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
      echo '<link href="'.$CLIENT_ROOT.'/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
    }
     	?>
	<link href="css/quicksearch.css" type="text/css" rel="Stylesheet" />
  <script src="js/jquery-3.2.1.min.js" type="text/javascript"></script>
        <script src="js/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
        <script src="js/symb/api.taxonomy.taxasuggest.js" type="text/javascript"></script>
        <script type="text/javascript">
                <?php include_once($SERVER_ROOT.'/includes/googleanalytics.php'); ?>
        </script>
        <style>
               	#slideshowcontainer{
                        border: 2px solid black;
                        border-radius:10px;
                        padding:10px;
                        margin-left: auto;
                        margin-right: auto;
                }
        </style>
</head>
<body>
      	<?php
	include($SERVER_ROOT.'/includes/header.php');
        ?>
	<!-- This is inner text! -->
        <div id="innertext">
                <h1></h1>
                <div id="quicksearchdiv" style="clear:both;margin-top:5px;margin-bottom:25px;border-width:2px;border-color:#ff7417;text-align:center;">
                        <!-- -------------------------QUICK SEARCH SETTINGS--------------------------------------- -->
                        <form name="quicksearch" id="quicksearch" action="<?php echo $CLIENT_ROOT; ?>/taxa/index.php" method="get" onsubmit="return verifyQuickSearch(this);">
                                <div id="quicksearchtext" ><?php echo (isset($LANG['QSEARCH_SEARCH'])?$LANG['QSEARCH_SEARCH']:'Search Taxon'); ?></div>
                                <input id="taxa" type="text" name="taxon" />
                                <button name="formsubmit"  id="quicksearchbutton" type="submit" value="Search Terms"><?php echo (isset($LANG['QSEARCH_SEARCH_BUTTON'])?$LANG['QSEARCH_SEARCH_BUTTON']:'Search'); ?></button>
                        </form>
                </div>
                <!-- <div style="width:100%;clear:both"> -->

			<div style="display: block;width: 46%;min-width:250px;float:left;padding: 7px;vertical-align:top;">

				<h1 style="text-align:center;"><?php echo (isset($LANG['LEFT_TITLE'])?$LANG['LEFT_TITLE']:'About ecdysis'); ?></h1>
				<font size="4">

				<?php echo (isset($LANG['LEFT_TEXT'])?$LANG['LEFT_TEXT']:'<p>Welcome to <b>ecdysis</b>, a portal for live-managing arthropod collections data. <b>ecdysis</b> is designed to serve the arthropod collections community as a robust and efficient environment for collections digitization and data management.</p><img src="https://serv.biokic.asu.edu/ecdysis/images/fulgorids.jpg" style="display:block;width:98%;float:center;"/><p>Hosted by the <a target="_blank" href="https://biokic.asu.edu/">Biodiversity Knowledge Integration Center</a> at Arizona State University, please contact Andrew Johnston (ajohnston@asu.edu) with any questions or to have a collection profile established.</p>'); ?> 
				</font>

			</div>
			<div style="display:block;width: 46%;min-width:250px;float:right;padding: px;vertical-align: top;">

				<h1 style="text-align:center;vertical-align:top;"><?php echo (isset($LANG['RIGHT_TITLE'])?$LANG['RIGHT_TITLE']:'Decentralized Portal Networks'); ?></h1>
				<font size="4">


				<?php echo (isset($LANG['RIGHT_TEXT'])?$LANG['RIGHT_TEXT']:'<p>This portal is designed to work as one node within the online <a target="_blank" href="https://riojournal.com/article/8767">Biodiversity Knowledge Graph</a>.  Not intended to be a single portal to integrate all biodiversity data, <b>ecdysis</b> is built to interact with and share biodiversity data between other such portals.  This concept is outlined in the figure below, and more information about decentralized biodiversity data portals can be <a href="https://github.com/nfranz/Presentations/blob/master/Franz_Biodiversity_Next_2019_Distributed_But_Global_in_Reach_De-Centralized_Biodiversity_Data.pdf" target="_blank">found here</a>. </p><img src="https://serv.biokic.asu.edu/ecdysis/images/BioCache.png" style="display:block;width:98%;float:center;"/><p>Built on the <a target="_blank" href="https://bdj.pensoft.net/articles.php?id=1114">Symbiota</a> software platform (available <a target="_blank" href="https://github.com/BioCache/Symbiota-light">here on GitHub</a>, <b>ecdysis</b> complements larger aggregators such as the Symbiota Collections of Arthropods Network portal (SCAN,  <a target="_blank" href="https://scan-bugs.org/portal/">scan-bugs.org</a>), providing active research collections a more streamlined option to manage data on-line.  We offer both data-linkage and publishing to collection profiles on <a target="_blank" href="https://scan-bugs.org/portal/">SCAN</a> and <a target="_blank" href="https://gbif.org/">GBIF</a>.</p>'); ?>

				</font>

			</div>
		<!-- </div> -->
		</div>
		<div style="text-align:center;width:100%;clear:both;padding:15px;">
			<img src="https://serv.biokic.asu.edu/ecdysis/images/biokic_logo.png" style="width:40%;float:center;max-width:500px;"/>
		</div>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
        ?>
</body>
</html>
