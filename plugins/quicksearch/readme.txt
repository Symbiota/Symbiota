Copy code below into the site's home page (index.php) or any other page of interest
Tool is dependent on the following css and js files being included within head tag
  
	<link href="css/quicksearch.css" type="text/css" rel="Stylesheet" />
	<link href="js/jquery-ui/jquery-ui.min.css" type="text/css" rel="Stylesheet" />
	<script src="js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="js/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
	<script src="js/symb/api.taxonomy.taxasuggest.js" type="text/javascript"></script>



Place following div where you want the query field to occur  
Quick search style tags within custom.css can be modified file to customize quick search style

<div id="quicksearchdiv">
	<!-- -------------------------QUICK SEARCH SETTINGS--------------------------------------- -->
	<form name="quicksearch" id="quicksearch" action="<?php echo $CLIENT_ROOT; ?>/taxa/index.php" method="get" onsubmit="return verifyQuickSearch(this);">
		<div id="quicksearchtext" ><?php echo (isset($LANG['QSEARCH_SEARCH'])?$LANG['QSEARCH_SEARCH']:'Taxon Search'); ?></div>
		<input id="taxa" type="text" name="taxon" />
		<button name="formsubmit"  id="quicksearchbutton" type="submit" value="Search Terms"><?php echo (isset($LANG['QSEARCH_SEARCH_BUTTON'])?$LANG['QSEARCH_SEARCH_BUTTON']:'Search'); ?></button>
	</form>
</div>