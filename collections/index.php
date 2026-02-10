<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceManager.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/CollectionFormManager.php');

Language::load([
	'collections/sharedterms',
	'collections/index', 
	'collections/search/index',
]);

header("Content-Type: text/html; charset=".$CHARSET);


$collManager = new OccurrenceManager();
$collManager->reset();
$currentPage = $_SERVER['REQUEST_URI'];

$otherCatArr = $collManager->getOccurVoucherProjects();

$collectionFormManager = new CollectionFormManager();
$requestSuppliedCatOrd = (array_key_exists('catOrd', $_REQUEST) && $collectionFormManager->areCollectionIdsValid($_REQUEST['catOrd'])) ? explode(',', $_REQUEST['catOrd']) : null;
$requestSuppliedCatExpnd = (array_key_exists('catExpnd', $_REQUEST) && $collectionFormManager->areCollectionIdsValid($_REQUEST['catExpnd'])) ? explode(',', $_REQUEST['catExpnd']) : null;
$requestSuppliedCatChk = (array_key_exists('catChk', $_REQUEST) && $collectionFormManager->areCollectionIdsValid($_REQUEST['catChk'])) ? explode(',', $_REQUEST['catChk']) : null;

?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>">
		<title><?php echo $DEFAULT_TITLE.' '.$LANG['PAGE_TITLE']; ?></title>
		<?php
		include_once($SERVER_ROOT.'/includes/head.php');
		include_once($SERVER_ROOT.'/includes/googleanalytics.php');
		?>
		<link href="<?= $CSS_BASE_PATH; ?>/symbiota/collections/listdisplay.css" type="text/css" rel="stylesheet" />
		<link href="<?= $CSS_BASE_PATH ?>/symbiota/collections/sharedCollectionStyling.css" type="text/css" rel="stylesheet" />
		<link href="<?= $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
		<link href="<?= $CSS_BASE_PATH ?>/searchStyles.css?ver=1" type="text/css" rel="stylesheet">
		<link href="<?= $CSS_BASE_PATH ?>/searchStylesInner.css" type="text/css" rel="stylesheet">
		<script src="<?= $CLIENT_ROOT; ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
		<script src="<?= $CLIENT_ROOT; ?>/js/jquery-ui.min.js" type="text/javascript"></script>
		<script src="<?= $CLIENT_ROOT ?>/js/alerts.js?v=202107" type="text/javascript"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$('#tabs').tabs({
					select: function(event, ui) {
						return true;
					},
					beforeLoad: function( event, ui ) {
						$(ui.panel).html("<p>Loading...</p>");
					}
				});
			});
		</script>
		<script type="text/javascript">
			$(document).ready(function() {
				setSessionQueryStr();
				setSearchForm(document.getElementById("params-form"));
				toggleAccordionsFromSessionStorage(sessionStorage.getItem("querystr" + getCurrentPage() + "/" + "accordionIds") ?.split(",") || []);
				document.getElementById("params-form").addEventListener("submit", function(event) {
					event.preventDefault();
					simpleSearch();
				});
				document.getElementById("reset-btn").addEventListener("click", function (event) {
					document.getElementById("params-form").reset();
					clearPageSpecificSessionStorageItems();
					checkTheCollectionsThatShouldBeCheckedBasedOnConfig();
					closeAllCategories();
					expandCategoriesBasedOnConfig();
					updateChip(event, isInitialConfig=true);
				});
			});
		</script>
	</head>
	<body>
	<?php
	$displayLeftMenu = (isset($collections_indexMenu)?$collections_indexMenu:false);
	include($SERVER_ROOT.'/includes/header.php');
	if(isset($collections_indexCrumbs)){
		if($collections_indexCrumbs){
			echo '<div class="navpath">';
			echo $collections_indexCrumbs;
			echo ' <b>' . $LANG['NAV_COLLECTIONS'] . '</b>';
			echo '</div>';
		}
	}
	else{
		echo '<div class="navpath">';
			echo '<a href="../index.php">' . htmlspecialchars((isset($LANG['NAV_HOME'])?$LANG['NAV_HOME']:'Home'), ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</a>';
			echo '&gt;&gt; ';
			echo '<b>' . (isset($LANG['NAV_COLLECTIONS']) ? $LANG['NAV_COLLECTIONS'] : 'Collections') . '</b>';
		echo "</div>";
	}
	?>
	<!-- This is inner text! -->
	<div role="main" id="innertext" class="inntertext-tab pin-things-here inner-search">
		<h1 class="page-heading screen-reader-only"><?php echo $LANG['COLLECTION_LIST']; ?></h1>
		<div id="error-msgs" class="errors"></div>
		<!-- <form  class="content" id="params-form" action="harvestparams.php" method="post" onsubmit="preventDefault(); return validateForm();"> -->
		<form  class="content" id="params-form" method="post" action="harvestparams.php" style="grid-template-columns: none;">
			<div style="display: flex; justify-content: flex-end; position: sticky; top: 1rem; z-index: 100;">
				<button style="width: 75px; margin-right: 0.5rem;" id="search-btn" type="submit" name="action"><?php echo isset($LANG['SEARCH'])?$LANG['SEARCH']:'Search &gt'; ?></button>
				<button style="margin-right: 0.5rem; background-color: var(--medium-color); width: 75px;" id="reset-btn" type="button"><?php echo $LANG['RESET'] ?></button>
			</div>
			<fieldset style="margin-top:1rem;">
				<div id="search-form-colls">
					<?php
						include($SERVER_ROOT . '/collections/collectionForm.php');
					?>
				</div>
			</fieldset>
		</form>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
	</body>
	<script src="<?= $CLIENT_ROOT ?>/js/symb/collections.list.js?ver=20251002>" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/symb/searchform.js?ver=2" type="text/javascript"></script>
	<script src="../js/symb/collections.index.js?ver=20171215" type="text/javascript"></script>
	<script type="text/javascript">
	$(document).ready(function() {
		const searchBtn = document.getElementById("search-btn");
		searchBtn.addEventListener("click", function(event) {
			const form = document.getElementById("params-form");
			event.preventDefault();
			simpleSearch();
		});
		
	});
</script>
</html>
