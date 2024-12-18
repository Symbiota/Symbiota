<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
include_once("config/symbini.php");
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
<head>
	<meta http-equiv="X-Frame-Options" content="deny">
	<title><?php echo $DEFAULT_TITLE; ?> Home</title>
	<?php
	$activateJQuery = true;
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php'); 
	?>
	<link href="css/quicksearch.css" type="text/css" rel="Stylesheet" />
	<script src="js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="js/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
	<script src="js/symb/api.taxonomy.taxasuggest.js" type="text/javascript"></script>
</head>
<body>
	<?php
	include($SERVER_ROOT.'/includes/header.php');
	?> 
	<!-- This is inner text! -->
        <div class="navpath"></div>
 	<main id="innertext">
	<div id="quicksearchdiv" style="float:right;margin-right:20px">
		<!-- -------------------------QUICK SEARCH SETTINGS--------------------------------------- -->
		<form name="quicksearch" id="quicksearch" action="<?php echo $CLIENT_ROOT; ?>/taxa/index.php" method="get" onsubmit="return verifyQuickSearch(this);">
			<div id="quicksearchtext" ><?php echo (isset($LANG['QSEARCH_SEARCH'])?$LANG['QSEARCH_SEARCH']:'Search Taxon'); ?></div>
			<input id="taxa" type="text" name="taxon" />
			<button name="formsubmit"  id="quicksearchbutton" type="submit" value="Search Terms"><?php echo (isset($LANG['QSEARCH_SEARCH_BUTTON'])?$LANG['QSEARCH_SEARCH_BUTTON']:'Search'); ?></button>
		</form>
	</div>
		<h1 class="page-heading">Consortium of Vertebrate Collections</h1>
		<div style="padding: 0px 10px;">
			<div style="float:right;margin:10px;">
				<?php
				//---------------------------GAME SETTINGS---------------------------------------
				//If more than one game will be active, assign unique numerical ids for each game.
				//If only one game will be active, leave set to 1. 
				$oodID = 1; 
				//Enter checklist id (clid) of the checklist you wish to use, if you would like to use more than one checklist,
				//separate their ids with a comma ex. "1,2,3,4"
				$ootdGameChecklist = "315";

				//Change to modify title
				$ootdGameTitle = "Reptile of the Day "; 

				//Replace "plant" with the type of organism, eg: plant, animal, insect, fungi, etc.
				//This setting will appear in "Name that ______"
				$ootdGameType = "reptile"; 
				//---------------------------DO NOT CHANGE BELOW HERE-----------------------------

				include_once($SERVER_ROOT.'/classes/GamesManager.php');
				$gameManager = new GamesManager();
				$gameInfo = $gameManager->setOOTD($oodID,$ootdGameChecklist);
				?>
				<div style="float:right;margin-right:10px;width:375px;text-align:center;">
					<div style="font-size:130%;font-weight:bold;">
						<?php echo $ootdGameTitle; ?>
					</div>
					<a href="<?php echo $CLIENT_ROOT; ?>/games/ootd/index.php?oodid=<?php echo $oodID.'&cl='.$ootdGameChecklist.'&title='.$ootdGameTitle.'&type='.$ootdGameType; ?>">
					<img src="<?php echo $gameInfo['images'][0]; ?>" style="width:325px;border:0px;" />
					</a><br/>
					<b>What is this <?php echo $ootdGameType; ?>?</b><br/>
					<a href="<?php echo $CLIENT_ROOT; ?>/games/ootd/index.php?oodid=<?php echo $oodID.'&cl='.$ootdGameChecklist.'&title='.$ootdGameTitle.'&type='.$ootdGameType; ?>">
						Click here to test your knowledge
					</a>
				</div>
			</div>
                        <div style="float:right;margin:10px;">
                                <?php
                                $oodID = 2;
                                //Enter checklist id (clid) of the checklist you wish to use, if you would like to use more than one checklist,
                                //separate their ids with a comma ex. "1,2,3,4"
                                $ootdGameChecklist = "316";

                                //Change to modify title
                                $ootdGameTitle = "Mammal of the Day ";

                                //Replace "plant" with the type of organism, eg: plant, animal, insect, fungi, etc.
                                //This setting will appear in "Name that ______"
                                $ootdGameType = "mammal";
                                //---------------------------DO NOT CHANGE BELOW HERE-----------------------------

                                include_once($SERVER_ROOT.'/classes/GamesManager.php');
                                $gameManager = new GamesManager();
                                $gameInfo = $gameManager->setOOTD($oodID,$ootdGameChecklist);
                                ?>
                                <div style="float:right;margin-right:10px;width:375px;text-align:center;">
                                        <div style="font-size:130%;font-weight:bold;">
                                                <?php echo $ootdGameTitle; ?>
                                        </div>
                                        <a href="<?php echo $CLIENT_ROOT; ?>/games/ootd/index.php?oodid=<?php echo $oodID.'&cl='.$ootdGameChecklist.'&title='.$ootdGameTitle.'&type='.$ootdGameType; ?>">
                                        <img src="<?php echo $gameInfo['images'][0]; ?>" style="width:325px;border:0px;" />
                                        </a><br/>
                                        <b>What is this <?php echo $ootdGameType; ?>?</b><br/>
                                        <a href="<?php echo $CLIENT_ROOT; ?>/games/ootd/index.php?oodid=<?php echo $oodID.'&cl='.$ootdGameChecklist.'&title='.$ootdGameTitle.'&type='.$ootdGameType; ?>">
                                                Click here to test your knowledge
                                        </a>

                                </div>
                        </div>
			<div> 
				<p>The Consortium of Vertebrate Collections is a growing network of research, academic, and museum institutions focused on the study of birds, mammals,
				fish, amphibians, and reptiles. It is a collaborative venture of iDigBio and Arizona State University and provides a mechanism for network 
				members to manage and share biodiversity data to scientists and the general public. Participating institutions are afforded sophisticated online data 
				management tools without the need for onsite IT support.</p>
				<p>Do you have specimen data to contribute, problems accessing or managing data, 
				or simply wish to provide feedback? Please send questions, comments, and requests to <a href="mailto:CSVCollAdmin@asu.edu">CSVCollAdmin@asu.edu</a></p>
			</div>
		</div>
	</main>

	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?> 
</body>
</html>
