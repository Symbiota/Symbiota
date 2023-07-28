	<link href="<?php echo $CSS_PATH; ?>/symbiota/quicksearch.css" type="text/css" rel="stylesheet">

	<script type="text/javascript">
		var clientRoot = "<?php echo $CLIENT_ROOT; ?>";
	</script>
	<script src="js/symb/api.taxonomy.taxasuggest.js" type="text/javascript"></script>

	<div id="quicksearchdiv">
		<!-- -------------------------QUICK SEARCH SETTINGS--------------------------------------- -->
		<form name="quicksearch" id="quicksearch" action="<?php echo $CLIENT_ROOT; ?>/taxa/index.php" method="get" onsubmit="return verifyQuickSearch(this);">
			<div id="quicksearchtext"><?php echo (isset($LANG['QSEARCH_SEARCH']) ? $LANG['QSEARCH_SEARCH'] : 'Taxon Search'); ?></div>
			<input id="taxa" type="text" name="taxon" />
			<button name="formsubmit" id="quicksearchbutton" type="submit" value="Search Terms"><?php echo (isset($LANG['QSEARCH_SEARCH_BUTTON']) ? $LANG['QSEARCH_SEARCH_BUTTON'] : 'Search'); ?></button>
		</form>
	</div>