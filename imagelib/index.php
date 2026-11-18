<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once($SERVER_ROOT . '/classes/ImageLibraryBrowser.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load('imagelib/index');

header("Content-Type: text/html; charset=".$CHARSET);

$taxon = array_key_exists('taxon', $_REQUEST) ? $_REQUEST['taxon'] : '';
$target = array_key_exists('target', $_REQUEST) ? trim($_REQUEST['target']):'';

$imgManager = new ImageLibraryBrowser();
$imgManager->setSearchTerm($taxon);
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<title><?= $DEFAULT_TITLE . ' ' . $LANG['IMG_LIBRARY'] ?></title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<link href="<?= $CSS_BASE_PATH ?>/jquery-ui.min.css" type="text/css" rel="stylesheet">
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/symb/imagelib.search.js?v=1" type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function() {

			//Auto-complete for taxon search field
			const taxaInput = document.querySelector("#taxa");
			if(taxaInput){
				taxaInput.addEventListener("focus", (event) => {
					taxaSuggest.config.clientRoot = "<?= $CLIENT_ROOT ?>";
					taxaSuggest.config.multipleTermSupport = true;
					taxaSuggest.config.taxonSearchType = document.getElementById("taxontype").value;
					taxaSuggest.config.includeAuthor = <?= (empty($TAXON_AUTOCOMPLETE_INCLUDE_AUTHOR) ? 'false' : 'true') ?>;
					taxaSuggest.config.includeKingdom = <?= (empty($TAXON_AUTOCOMPLETE_INCLUDE_KINGDOM) ? 'false' : 'true') ?>;
					taxaSuggest.initiate("taxa");
				});
			}

		});
	</script>
	<style>
		.sciname-search {
			float: left;
			margin: 10px 0px 10px 30px;
		}
		.sciname-search-container {
			float: right;
			width: 30rem;
			margin-bottom: 1rem;
		}
	</style>
</head>
<body>
	<?php
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="<?= $CLIENT_ROOT ?>/index.php"><?= $LANG['HOME'] ?></a> &gt;&gt;
		<b><?= $LANG['IMG_LIBRARY'] ?></b>
	</div>
	<!-- This is inner text! -->
	<div role="main" id="innertext">
		<h1 class="page-heading"><?= $LANG['TAXA_W_IMGS'] ?></h1>
		<div style="margin:0px 0px 5px 20px;"><?= $LANG['TAXA_IMG_EXPLAIN'] ?>
		</div>
		<div class="sciname-search">
			<div>
				<a href='index.php?target=family'><?= $LANG['BROWSE_FAMILY'] ?></a>
			</div>
			<div style='margin-top:10px;'>
				<a href='index.php?target=genus'><?= $LANG['BROWSE_GENUS'] ?></a>
			</div>
			<div style='margin-top:10px;'>
				<?= $LANG['BROWSE_SPECIES'] ?>
			</div>
			<div style='margin:2px 0px 0px 10px;'>
				<div><a href='index.php?taxon=A'>A</a>|<a href='index.php?taxon=B'>B</a>|<a href='index.php?taxon=C'>C</a>|<a href='index.php?taxon=D'>D</a>|<a href='index.php?taxon=E'>E</a>|<a href='index.php?taxon=F'>F</a>|<a href='index.php?taxon=G'>G</a>|<a href='index.php?taxon=H'>H</a></div>
				<div><a href='index.php?taxon=I'>I</a>|<a href='index.php?taxon=J'>J</a>|<a href='index.php?taxon=K'>K</a>|<a href='index.php?taxon=L'>L</a>|<a href='index.php?taxon=M'>M</a>|<a href='index.php?taxon=N'>N</a>|<a href='index.php?taxon=O'>O</a>|<a href='index.php?taxon=P'>P</a>|<a href='index.php?taxon=Q'>Q</a></div>
				<div><a href='index.php?taxon=R'>R</a>|<a href='index.php?taxon=S'>S</a>|<a href='index.php?taxon=T'>T</a>|<a href='index.php?taxon=U'>U</a>|<a href='index.php?taxon=V'>V</a>|<a href='index.php?taxon=W'>W</a>|<a href='index.php?taxon=X'>X</a>|<a href='index.php?taxon=Y'>Y</a>|<a href='index.php?taxon=Z'>Z</a></div>
			</div>
		</div>
		<div class="sciname-search-container">
			<div style="margin:10px 0px 0px 0px;">
				<form name="searchform1" action="index.php" method="post">
					<fieldset class="fieldset-like-box">
						<legend style="font-weight:bold;"><?= $LANG['SCINAME_SEARCH'] ?></legend>
						<label for="taxon">Taxon: </label>
						<input type="text" id="taxon" name="taxon" value="<?= $imgManager->cleanOutStr($taxon) ?>" title="<?= $LANG['ENTER_TAXON_NAME'] ?>" placeholder="<?= $LANG['ENTER_TAXON_NAME'] ?>" >
						<button name="submit" value="Search" type="submit"><?= $LANG['SEARCH'] ?></button>
					</fieldset>
				</form>
			</div>
			<div style="font-weight:bold;margin:15px 10px 0px 20px;">
				<div>
					<a href="../includes/usagepolicy.php#images"><?= $LANG['IMG_CP_POLICY'] ?></a>
				</div>
				<div>
					<a href="contributors.php"><?= $LANG['IMG_CONTRIBUTORS'] ?></a>
				</div>
				<div>
					<a href="search.php"><?= $LANG['IMG_SEARCH'] ?></a>
				</div>
			</div>
		</div>
		<div style='clear:both;'><hr/></div>
		<?php
			$taxaList = Array();
			if($target == 'genus'){
				$taxaList = $imgManager->getGenusList();
				if($taxaList){
					echo '<h2>'.$LANG['SELECT_GENUS'].'</h2>';
					foreach($taxaList as $value){
						echo '<div style="margin-left:30px;"><a href="index.php?taxon=' . Sanitize::outString($value) . '">' . Sanitize::outString($value) . '</a></div>';
					}
				}
				else{
					echo '<h2>' . $LANG['NO_TAXA_RETURNED'] . '</h2>';
				}
			}
			elseif($target == 'species' || $taxon){
				$taxaList = $imgManager->getSpeciesList();
				if($taxaList){
					echo '<h2>' . $LANG['SELECT_SPECIES'] . '</h2>';
					foreach($taxaList as $tid => $taxonName){
						?>
						<div style="margin-left:30px;font-style:italic;">
							<a href="#" onclick="openTaxonPopup(<?= $tid ?>);return false;"><?= Sanitize::outString($taxonName) ?></a>
							<a href="search.php?taxa=<?= $tid ?>&usethes=1&taxontype=2&submitaction=search" target="_blank"><img src="../images/image.png" style="width:1.5em;" /></a>
						</div>
						<?php
					}
				}
				else{
					echo '<h2>'.$LANG['NO_TAXA_RETURNED'].'</h2>';
				}
			}
			else{ //Family display
				$taxaList = $imgManager->getFamilyList();
				if($taxaList){
					echo '<h2>'.$LANG['SELECT_FAMILY'].'.</h2>';
					foreach($taxaList as $value){
						echo '<div style="margin-left:30px;"><a href="index.php?target=genus&taxon=' . Sanitize::outString($value) . '">' . Sanitize::outString(strtoupper($value)) . '</a></div>';
					}
				}
				else{
					echo '<h2>' . $LANG['NO_TAXA_RETURNED'] . '</h2>';
				}
			}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
