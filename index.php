<?php
include_once('config/symbini.php');
if($LANG_TAG == 'en' || !file_exists($SERVER_ROOT.'/content/lang/templates/index.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/templates/index.en.php');
else include_once($SERVER_ROOT.'/content/lang/templates/index.'.$LANG_TAG.'.php');
header('Content-Type: text/html; charset=' . $CHARSET);
?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">
<head>
        <title><?php echo $DEFAULT_TITLE; ?> Home</title>
        <?php
        include_once($SERVER_ROOT . '/includes/head.php');
        include_once($SERVER_ROOT . '/includes/googleanalytics.php');
        ?>
        <script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
        <script src="<?= $CLIENT_ROOT ?>/js/jquery-ui.min.js" type="text/javascript"></script>
        <script type="text/javascript">
                var clientRoot = "<?= $CLIENT_ROOT ?>";
                $(document).ready(function() {
                        $("#qstaxa").autocomplete({
                                source: function( request, response ) {
                                        $.getJSON( "checklists/rpc/speciessuggest.php", { term: request.term }, response );
                                },
                                minLength: 3,
                                autoFocus: true,
                                select: function( event, ui ) {
                                        if(ui.item){
                                                $( "#qstaxa" ).val(ui.item.value);
                                                $( "#qstid" ).val(ui.item.id);
                                        }
                                },
                                change: function( event, ui ) {
                                        if(ui.item === null) {
                                                $( "#qstid" ).val("");
                                        }
                                }
                        });
                });
        </script>
        <script src="<?= $CLIENT_ROOT ?>/js/jquery.slides.js"></script>
        <meta name='keywords' content='lichens,natural history collections,flora,checklists,species lists' />
</head>
<body>
        <?php
        include($SERVER_ROOT . '/includes/header.php');
        ?>
        <div class="navpath"></div>
        <main id="innertext">
		<div style="float:right;margin-top:30px;margin-left:10px">
			<div id="quicksearchdiv" style="margin:0px 0px 30px;width:400px;height:50px">
				<form name="quicksearch" id="quicksearch" action="<?php echo $CLIENT_ROOT; ?>/taxa/index.php" method="get" onsubmit="return verifyQuickSearch(this);" style="margin-left:20px">
					<div id="quicksearchtext"><?php echo (isset($LANG['QSEARCH_SEARCH'])?$LANG['QSEARCH_SEARCH']:'Search Taxon'); ?></div>
					<input type="text" name="taxon" id="quicksearchtaxon" />
					<button name="formsubmit"  id="quicksearchbutton" type="submit" value="Search Terms" style="float:right;margin-right:80px"><?php echo (isset($LANG['QSEARCH_SEARCH_BUTTON'])?$LANG['QSEARCH_SEARCH_BUTTON']:'Search'); ?></button>
				</form>
			</div>
			<div style="margin-left:35px">
			<?php
                        	$ssId = 1;
                        	$numSlides = 10;
                        	$width = 350;
                        	$dayInterval = 7;
                        	$clId = 1;
				$imageType = "specimen";
                        	$numDays = 240;

                        	ini_set('max_execution_time', 120); //300 seconds = 5 minutes
                        	include_once('classes/PluginsManager.php');
                        	$pluginManager = new PluginsManager();
                        	echo $pluginManager->createSlideShow($ssId,$numSlides,$width,$numDays,$imageType,$clid,$dayInterval);
                        ?>
			</div>
		        <fieldset style="text-align:center; width:300px; -moz-border-radius:5px; -webkit-border-radius:5px; margin:10px; border: 1px solid black;margin-left:auto;margin-right:auto;">
                                <legend><b>Science Friday Podcast</b></legend>
                                <iframe title="Science Friday Podcast - UC Davis Tardigrades" src="https://www.3cmediasolutions.org/privid/56252?embed=&key=02835784303ed703988d042303e4e13c0e5a9675&width=400&height=75" width="400px" height="75px" scrolling="no" allowfullscreen frameborder="0"></iframe>
                                <div><a href="https://www.sciencefriday.com/segments/achieving-suspended-animation-with-help-from-the-water-bear/" target="_blank">Achieving Suspended Animation Podcast - 09/30/2016</a></div>
                        </fieldset>
		</div>
		<div>
			<h2 style="margin-top:-8px">Welcome to the Tardigrade Reference Center </h2>
			<p>
				The Tardigrade Reference Center is a collaborative effort to bring both the current and historical information about tardigrades to everybody. 
				The site is the work of Dr. Clark W. Beasley of McMurry University, Abilene, TX and Dr. William R. Miller of Baker University, 
				Baldwin City, KS and their students. The site is built like a scientific paper and provides reference to all source materials. 
			</p>
                        <fieldset style="float:left;width:275px;margin:5px 15px;padding:15px;text-align:center; -moz-border-radius:5px; -webkit-border-radius:5px; border: 1px solid black;">
                                <legend><b>Tardigrade Taxonomy</b></legend>
                                <a href="<?php echo $CLIENT_ROOT; ?>/taxa/taxonomy/taxonomydynamicdisplay.php?target=tardigrada&displayauthor=1">
                                        <img src="<?php echo $CLIENT_ROOT; ?>/images/layout/taxonomy_tn.jpg" style="width:200px;height:120px;margin:auto;" />
                                        <div>Explore tardigrade taxonomy</div>
                                </a>
                        </fieldset>
			<p style="padding:15px 20px">
				The site was designed for the student, educator, or citizen scientist who wishes to learn about tardigrades. It provides general tardigrade 
				information and both "what is" and "how to" information. In addition, there are examples of research projects that can be used at different levels of 
				teaching or student projects. Read more about the <b><i><a href="https://researchoutreach.org/articles/capturing-images-and-data-before-the-slides-degrade-into-uselessness/" target="_blank">specimen digitization project</a></i></b> that lead to the creation of this resource.
			</p>
<!--
                	<p>
				The Science side is based on the scientific literature and provides access or reference to most of the scientific data necessary for research. 
				This side starts with a list of more than 2,500 scientific papers and updated as new papers are published. The bibliography of published papers is 
				cross referenced and can be accessed by author, location, subject, or taxon. In addition, there is a current list of all described species, and a modern 
				data based description of each species with images. Finally, we have developed a modern, dynamic, interactive key to the identification of species of tardigrades. 
			</p>
-->
			<div>
				<fieldset style="text-align:center;width:500px;	-moz-border-radius: 5px;-webkit-border-radius: 5px; margin:5px 15px; border:1px solid black;">
					<legend><b>BBC-Horizons: Oceans of the Solar System</b></legend>
					<iframe title="BBC-Horizons: Oceans of the Solar System" src="https://www.3cmediasolutions.org/privid/45194?embed=&key=7e30500a1e073535795a0e6f5a9fac0d89be2b7f&width=500&height=300" width="500px" height="300px" scrolling="no" allowfullscreen frameborder="0"></iframe>
					<div><a href="http://www.bbc.co.uk/programmes/b076qqxh" target="_blank"><b>BBC documentary on the search for life on other planetary bodies in our solar system</b></a></div>
				</fieldset>

			</div>
		</div>
	</main>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?> 
</body>
</html>
