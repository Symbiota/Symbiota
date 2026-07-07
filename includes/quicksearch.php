<script>
	if (typeof clientRoot === 'undefined') {
		var clientRoot = "<?= $CLIENT_ROOT ?>";
	}
</script>
<link href="<?= $CSS_BASE_PATH ?>/quicksearch.css" type="text/css" rel="stylesheet">
<link href="<?= $CSS_BASE_PATH ?>/jquery-ui.css" type="text/css" rel="stylesheet">

<script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
<script src="<?= $CLIENT_ROOT . '/js/jquery-ui.min.js' ?>" type="text/javascript"></script>
<script src="js/symb/api.taxonomy.taxasuggest.js" type="text/javascript"></script>

<div id="quicksearchdiv">
	<!-- -------------------------QUICK SEARCH SETTINGS--------------------------------------- -->
	<form name="quicksearch" id="quicksearch" action="<?= $CLIENT_ROOT; ?>/taxa/index.php" method="get" onsubmit="return verifyQuickSearch(this);">
		<div class="quicksearchcontainer">
			<div id="quicksearchtext"><?= $LANG['QSEARCH_SEARCH'] ?></div>
			<input id="taxa" type="text" name="taxon" />
			<button name="formsubmit" id="quicksearchbutton" type="submit" value="Search Terms"><?= $LANG['QSEARCH_SEARCH_BUTTON'] ?></button>
		</div>
	</form>
</div>