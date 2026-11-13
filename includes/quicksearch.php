<!-- Remove the following jQuery imports/links if they are already being imported into page -->
<link href="<?= $CSS_BASE_PATH ?>/jquery-ui.css" type="text/css" rel="stylesheet">
<script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
<script src="<?= $CLIENT_ROOT . '/js/jquery-ui.min.js' ?>" type="text/javascript"></script>

<link href="<?= $CSS_BASE_PATH ?>/quicksearch.css" type="text/css" rel="stylesheet">
<script src="<?= $CLIENT_ROOT ?>/js/symb/taxa.suggest.js?v=1" type="text/javascript"></script>
<script>
	$(document).ready(function() {
		const taxaInput = document.querySelector("#taxon");
		if(taxaInput){
			taxaInput.addEventListener("focus", (event) => {
				taxaSuggest.config.clientRoot = "<?= $CLIENT_ROOT ?>";
				taxaSuggest.config.includeAuthor = <?= (empty($TAXON_AUTOCOMPLETE_INCLUDE_AUTHOR) ? 'false' : 'true') ?>;
				taxaSuggest.config.includeKingdom = <?= (empty($TAXON_AUTOCOMPLETE_INCLUDE_KINGDOM) ? 'false' : 'true') ?>;
				taxaSuggest.initiate("taxon", function(result){
					if(result.valid) {
						document.getElementById("tid").value = result.item.id
					}
				});
			});
		}
	});
</script>
<div id="quicksearchdiv">
	<!-- -------------------------QUICK SEARCH SETTINGS--------------------------------------- -->
	<form name="quicksearch" id="quicksearch" action="<?= $CLIENT_ROOT; ?>/taxa/index.php" method="get" onsubmit="return verifyQuickSearch(this);">
		<div class="quicksearchcontainer">
			<div id="quicksearchtext"><?= $LANG['QSEARCH_SEARCH'] ?></div>
			<input id="taxon" type="text" name="taxon" >
			<input id="tid" type="hidden" name="tid" >
			<button name="formsubmit" id="quicksearchbutton" type="submit" value="Search Terms"><?= $LANG['QSEARCH_SEARCH_BUTTON'] ?></button>
		</div>
	</form>
</div>