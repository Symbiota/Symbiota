	<link href="<?php echo htmlspecialchars($CSS_BASE_PATH, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>/quicksearch.css" type="text/css" rel="stylesheet">
	<link href="<?= $CSS_BASE_PATH ?>/jquery-ui.css" type="text/css" rel="stylesheet">

	<script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?php echo $CLIENT_ROOT . '/js/jquery-ui.min.js'; ?>" type="text/javascript"></script>
	<script src="js/symb/api.taxonomy.taxasuggest.js" type="text/javascript"></script>

	<div id="quicksearchdiv">
		<!-- -------------------------QUICK SEARCH SETTINGS--------------------------------------- -->
		<form name="quicksearch" id="quicksearch" action="<?php echo $CLIENT_ROOT; ?>/taxa/index.php" method="get" onsubmit="return verifyQuickSearch(this);">
			<div class="quicksearchcontainer">
				<div id="quicksearchtext"><?php echo (isset($LANG['QSEARCH_SEARCH']) ? $LANG['QSEARCH_SEARCH'] : 'Taxon Search'); ?></div>
				<input id="taxa" type="text" name="taxon" />
				<button name="formsubmit" id="quicksearchbutton" type="submit" value="Search Terms"><?php echo (isset($LANG['QSEARCH_SEARCH_BUTTON']) ? $LANG['QSEARCH_SEARCH_BUTTON'] : 'Search'); ?></button>
			</div>
		</form>
	</div>