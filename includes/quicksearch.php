<script src="<?= $CLIENT_ROOT ?>/js/symb/api.taxonomy.taxasuggest.js" type="text/javascript"></script>
<div id="quicksearchdiv">
	<!-- -------------------------QUICK SEARCH SETTINGS--------------------------------------- -->
	<form name="quicksearch" id="quicksearch" action="<?= $CLIENT_ROOT ?>/taxa/index.php" method="get" onsubmit="return verifyQuickSearch(this);">
		<div id="quicksearchtext"><?= $LANG['QSEARCH_SEARCH'] ?></div>
		<input id="taxa" type="text" name="taxon" />
		<button name="formsubmit" id="quicksearchbutton" type="submit" value="Search Terms"><?php echo (isset($LANG['QSEARCH_SEARCH_BUTTON']) ? $LANG['QSEARCH_SEARCH_BUTTON'] : 'Search'); ?></button>
	</form>
</div>