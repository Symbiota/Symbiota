<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/TaxonomyEditorManager.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load('taxa/taxonomy/taxoneditor');

header("Content-Type: text/html; charset=" . $CHARSET);

if(!$SYMB_UID) header('Location: ' . $CLIENT_ROOT . '/profile/index.php?refurl=../taxa/taxonomy/taxoneditor.php?' . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$tid = Sanitize::int($_REQUEST['tid']) ?? 0;
$taxAuthId = array_key_exists('taxauthid', $_REQUEST) ? Sanitize::int($_REQUEST['taxauthid']) : 1;
$tabIndex = array_key_exists('tabindex', $_REQUEST) ? Sanitize::int($_REQUEST['tabindex']) : 0;
$submitAction = array_key_exists('submitaction', $_REQUEST) ? $_REQUEST['submitaction'] : '';

$taxonEditorObj = new TaxonomyEditorManager();
$taxonEditorObj->setTid($tid);
$taxonEditorObj->setTaxAuthId($taxAuthId);

$isEditor = false;
if ($IS_ADMIN || array_key_exists("Taxonomy", $USER_RIGHTS)) $isEditor = true;

$statusStr = '';
if ($isEditor) {
	if (array_key_exists('taxonedits', $_POST)) {
		$statusStr = $taxonEditorObj->submitTaxonEdits($_POST);
	} elseif ($submitAction == 'updatetaxstatus') {
		$statusStr = $taxonEditorObj->submitTaxStatusEdits($_POST['parenttid'], $_POST['tidaccepted']);
	} elseif (array_key_exists("synonymedits", $_REQUEST)) {
		$statusStr = $taxonEditorObj->submitSynonymEdits($_POST['tidsyn'], $tid, $_POST['unacceptabilityreason'], $_POST['notes'], $_POST['sortsequence']);
	} elseif ($submitAction == 'linkToAccepted') {
		$deleteOther = array_key_exists("deleteother", $_REQUEST) ? true : false;
		$statusStr = $taxonEditorObj->submitAddAcceptedLink($_REQUEST["tidaccepted"], $deleteOther);
	} elseif (array_key_exists('deltidaccepted', $_REQUEST)) {
		$statusStr = $taxonEditorObj->removeAcceptedLink($_REQUEST['deltidaccepted']);
	} elseif (array_key_exists("changetoaccepted", $_REQUEST)) {
		$tidAccepted = $_REQUEST["tidaccepted"];
		$switchAcceptance = array_key_exists("switchacceptance", $_REQUEST) ? true : false;
		$statusStr = $taxonEditorObj->submitChangeToAccepted($tid, $tidAccepted, $switchAcceptance);
	} elseif ($submitAction == 'changeToNotAccepted') {
		$tidAccepted = $_REQUEST["tidaccepted"];
		$statusStr = $taxonEditorObj->submitChangeToNotAccepted($tid, $tidAccepted, $_POST['unacceptabilityreason'], $_POST['notes']);
	} elseif ($submitAction == 'updatehierarchy') {
		$statusStr = $taxonEditorObj->rebuildHierarchy($tid);
	} elseif ($submitAction == 'remapTaxon') {
		$remapStatus = $taxonEditorObj->transferResources($_REQUEST['remaptid']);
		if ($taxonEditorObj->getWarningArr()) $statusStr = $LANG['FOLLOWING_WARNINGS'] . ': ' . implode(';', $taxonEditorObj->getWarningArr());
		if ($remapStatus) {
			$statusStr = $LANG['SUCCESS_REMAPPING'] . ' ' . $statusStr;
			header('Location: taxonomydisplay.php?target=' . $_REQUEST["genusstr"] . '&statusstr=' . $statusStr);
		} else $statusStr = $taxonEditorObj->getErrorMessage();
	} elseif ($submitAction == 'deleteTaxon') {
		$delStatus = $taxonEditorObj->deleteTaxon();
		if ($taxonEditorObj->getWarningArr()) $statusStr = $LANG['FOLLOWING_WARNINGS'] . ': ' . implode(';', $taxonEditorObj->getWarningArr());
		if ($delStatus) {
			$statusStr = $LANG['SUCCESS_DELETING'] . ' ' . $statusStr;
			header('Location: taxonomydisplay.php?statusstr=' . $statusStr);
		} else $statusStr = $taxonEditorObj->getErrorMessage();
	}
	$taxonEditorObj->setTaxon();
}
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<title><?= $DEFAULT_TITLE . " " . $LANG['TAX_EDITOR'] . ": " . $tid ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET ?>" />
	<?php
	include_once($SERVER_ROOT . '/includes/head.php');
	?>
	<link href="<?= $CSS_BASE_PATH ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/symb/taxa.suggest.js?v=1" type="text/javascript"></script>
	<script>
		var tid = <?php echo $taxonEditorObj->getTid() ?>;
		var tabIndex = <?php echo $tabIndex ?>;

	    document.addEventListener('DOMContentLoaded', () => {

			const parentInput = document.querySelector('#parentstr');
			if(parentInput){
				parentInput.addEventListener('focus', (event) => {
					taxaSuggest.config.clientRoot = "<?= $CLIENT_ROOT ?>";
					taxaSuggest.config.taxAuthID = document.taxauthidform.taxauthid.value;
					taxaSuggest.config.rankMaximum = document.taxoneditform.rankid.value - 1;
					taxaSuggest.config.includeAuthor = <?= (empty($TAXON_AUTOCOMPLETE_INCLUDE_AUTHOR) ? 'false' : 'true') ?>;
					taxaSuggest.config.includeKingdom = <?= (empty($TAXON_AUTOCOMPLETE_INCLUDE_KINGDOM) ? 'false' : 'true') ?>;
					taxaSuggest.initiate("parentstr", function(result) {
						if (result.valid) {
							document.getElementById("parenttid").value = result.item.id;
						}
						else{
							document.getElementById("parenttid").value = "";
							if(this.value != ""){
								alert("<?= $LANG['SELECT_FROM_LIST'] ?>");
							}
						}
					});
				});
			}

			const aefAcceptedInput = document.querySelector('#aefacceptedstr');
			if(aefAcceptedInput){
				aefAcceptedInput.addEventListener('focus', (event) => {
					taxaSuggest.config.clientRoot = "<?= $CLIENT_ROOT ?>";
					taxaSuggest.config.taxAuthID = document.taxauthidform.taxauthid.value;
					taxaSuggest.config.rankMaximum = 0;
					taxaSuggest.config.limitToAccepted = true;
					taxaSuggest.initiate("aefacceptedstr", function(result) {
						if (result.valid) {
							document.getElementById("aeftidaccepted").value = result.item.id;
						}
						else{
							document.getElementById("aeftidaccepted").value = "";
							if(this.value != ""){
								alert("<?= $LANG['SELECT_FROM_LIST'] ?>");
							}
						}
					});
				});
			}

			const ctnafAcceptedInput = document.querySelector('#ctnafacceptedstr');
			if(ctnafAcceptedInput){
				ctnafAcceptedInput.addEventListener('focus', (event) => {
					taxaSuggest.config.clientRoot = "<?= $CLIENT_ROOT ?>";
					taxaSuggest.config.taxAuthID = document.taxauthidform.taxauthid.value;
					taxaSuggest.config.rankMaximum = 0;
					taxaSuggest.config.limitToAccepted = true;
					taxaSuggest.initiate("ctnafacceptedstr", function(result) {
						if (result.valid) {
							document.getElementById("ctnaftidaccepted").value = result.item.id;
						}
						else{
							document.getElementById("ctnaftidaccepted").value = "";
							if(this.value != ""){
								alert("<?= $LANG['SELECT_FROM_LIST'] ?>");
							}
						}
					});
				});
			}
		});

		function validateAcceptedChangeForm(f) {
			if (f.tidaccepted.value == "") {
				alert("<?= $LANG['SELECT_FROM_LIST'] ?>");
				return false;
			}
			return true;
		}

	</script>
	<script src="<?= $CLIENT_ROOT ?>/js/symb/taxa.sharedTaxonomyCRUD.js?ver=5"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/symb/taxa.taxonomyeditor.js?ver=4"></script>
	<style type="text/css">
		.search-bar-long { width: 35rem; }
		.editDiv { clear: both; }
		.editLabel { float: left; font-weight: bold; }
		.editfield { float: left; margin-left: 5px; }
		.tsedit { float: left; margin-left: 5px; }
		.headingDiv { font-size: 110%; font-weight: bold; padding-top: 10px; }
		.taxonDiv { font-size: 1.125rem; margin-top: 15px; margin-left: 10px; }
		.taxonDiv a { color: #990000; font-weight: bold; font-style: italic; }
		.taxonDiv img { border: 0px; margin: 0px; height: 15px; }
	</style>
</head>
<body>
	<?php
	$jsLangFile = $CLIENT_ROOT . '/js/symb/' . $LANG_TAG . '.js';
	if(!file_exists($jsLangFile)) $jsLangFile = $CLIENT_ROOT . '/js/symb/en.js';
	?>
	<script src="<?= $jsLangFile ?>" type="text/javascript"></script>
	<?php
	$displayLeftMenu = (isset($taxa_admin_taxonomyeditorMenu) ? $taxa_admin_taxonomyeditorMenu : "true");
	include($SERVER_ROOT . '/includes/header.php');
	?>
	<div class="navpath">
		<a href="../../index.php"><?= $LANG['HOME'] ?></a> &gt;&gt;
		<a href="taxonomydisplay.php"><?= $LANG['TAX_TREE_VIEW'] ?></a> &gt;&gt;
		<b><?= $LANG['TAXONOMY_EDITOR'] ?></b>
	</div>
	<div role="main" id="innertext">
		<h1 class="page-heading">
			<?php
			$splitSciname = $taxonEditorObj->splitSciname();
			$author = !empty($splitSciname['author']) ? ($splitSciname['author'] . ' ') : '';
			$cultivarEpithet = !empty($splitSciname['cultivarEpithet']) ? ($taxonEditorObj->standardizeCultivarEpithet($splitSciname['cultivarEpithet'])) . ' ' : '';
			$tradeName = !empty($splitSciname['tradeName']) ? ($taxonEditorObj->standardizeTradeName($splitSciname['tradeName']) . ' ') : '';
			$nonItalicizedScinameComponent = $author . $cultivarEpithet . $tradeName;
			echo $LANG['TAX_EDITOR'] . ': <i>' . Sanitize::outString($splitSciname['base']) . '</i> ' . Sanitize::outString($nonItalicizedScinameComponent) . ' [' . $taxonEditorObj->getTid() . ']';
			?>
		</h1>
		<?php
		if ($statusStr) {
			?>
			<hr />
			<div style="color:<?= (strpos($statusStr, $LANG['SUCCESS']) !== false ? 'green' : 'red') ?>;margin:15px;">
				<?= $statusStr ?>
			</div>
			<hr />
			<?php
		}
		if ($isEditor && $tid) {
			$hierarchyArr = $taxonEditorObj->getHierarchyArr();
			?>
			<div style="float:right;" title="<?= $LANG['GO_TAX_DISPLAY'] ?>">
				<a href="taxonomydisplay.php?target=<?= Sanitize::outString($taxonEditorObj->getUnitName1()) ?>&showsynonyms=1">
					<img style='border:0px;width:1.3em;' src='../../images/toparent.png' />
				</a>
			</div>
			<div style="float:right;" title="<?= $LANG['ADD_NEW_TAXON'] ?>">
				<a href="taxonomyloader.php">
					<img style='border:0px;width:1.3em;' src='../../images/add.png' />
				</a>
			</div>
			<h1>
				<?php
				echo "<div class='taxonDiv'><a href='../profile/tpeditor.php?tid=" . $taxonEditorObj->getTid() . "'>";
				echo "View Taxon Profile Editor";
				echo "</a></div>";
				?>
			</h1>
			<div id="tabs" class="taxondisplaydiv">
				<ul>
					<li><a href="#editorDiv"><?= $LANG['EDITOR'] ?></a></li>
					<li><a href="#taxonstatusdiv"><?= $LANG['TAX_STATUS'] ?></a></li>
					<li><a href="#hierarchydiv"><?= $LANG['HIERARCHY'] ?></a></li>
					<li><a href="taxonomychildren.php?tid=<?= $tid . '&taxauthid=' . $taxAuthId ?>"><?= $LANG['CHILDREN_TAXA'] ?></a></li>
					<li><a href="taxonomydelete.php?tid=<?= $tid ?>&genusstr=<?= Sanitize::outString($taxonEditorObj->getUnitName1()) ?>"><?= $LANG['DELETE'] ?></a></li>
				</ul>
				<div id="editorDiv" style="height:400px;">
					<div style="float:right;cursor:pointer;" onclick="toggleEditFields()" title="<?= $LANG['TOGGLE_TAXON_EDITING'] ?>">
						<img style='width:1.3em;border:0px;' src='../../images/edit.png' />
					</div>
					<form id="taxoneditform" name="taxoneditform" action="taxoneditor.php" method="post" onsubmit="return validateTaxonEditForm(this, originalForm)">
						<input type="hidden" id="sciname" name="sciname" class="search-bar-long" value="" />
						<div class="editDiv">
							<div class="editLabel"><?= $LANG['RANK_NAME'] ?>: </div>
							<div class="editfield">
								<?= ($taxonEditorObj->getRankName() ? Sanitize::outString($taxonEditorObj->getRankName()) : $LANG['NON_RANKED_NODE']) ?>
							</div>
							<div class="editfield" style="display:none;">
								<select id="rankid" name="rankid" style="margin-bottom: 0.5rem;">
									<option value="0"><?= $LANG['NON_RANKED_NODE'] ?></option>
									<option value="">---------------------------------</option>
									<?php
									$rankArr = $taxonEditorObj->getRankArr();
									foreach ($rankArr as $rankId => $nameArr) {
										foreach ($nameArr as $rName) {
											echo '<option value="' . $rankId . '" ' . ($taxonEditorObj->getRankId() == $rankId ? 'SELECTED' : '') . '>' . Sanitize::outString($rName) . '</option>';
										}
									}
									?>
								</select>
							</div>
						</div>
						<div class="editDiv" id="genus-div">
							<div class="editLabel">
								<!-- <?= $LANG['UNITNAME1'] ?>:  -->
								<label id="unitind1label" for="unitind1">
									<?= $LANG['GENUS_NAME'] ?>
								</label>
							</div>
							<div class="editfield">
								<?php
								$unitInd1 = $taxonEditorObj->getUnitInd1();
								echo Sanitize::outString(($unitInd1 ? $unitInd1 . ' ' : '') . $taxonEditorObj->getUnitName1());
								?>
							</div>
							<div class="editfield" style="display:none;">
								<span id="required-field" name="required-field" style="color: var(--danger-color);">*</span>
								<span>: </span>
								<select id="unitind1-select" name="unitind1">
									<option value=""></option>
									<option value="&#215;" <?= ($unitInd1 && (mb_ord($unitInd1) == 215 || strtolower($unitInd1) == 'x') ? 'selected' : '') ?>>&#215;</option>
									<?php
									if(!empty($GLOBALS['ACTIVATE_PALEO_DAGGER'])) {
										echo '<option value="&#8224;" ' . ($unitInd1 && mb_ord($unitInd1) == 8224 ? 'selected' : '') . '>&#8224;</option>';
									}
									else{
										if($unitInd1 && mb_ord($unitInd1) == 8224){
											echo '<option value="&#8224;" selected>&#8224;</option>';
										}
									}
									?>
								</select>
								<input type="text" id="unitname1" name="unitname1" style="width:300px;border-style:inset;" value="<?= Sanitize::outString($taxonEditorObj->getUnitName1()) ?>" />
							</div>
						</div>
						<div id="div2hide" style="display: <?= empty($taxonEditorObj->getUnitName2()) ? 'none' : 'block' ?>" class="editDiv">
							<div id="unit-2-name-label" class="editLabel"><?= $LANG['UNITNAME2'] ?>: </div>
							<div class="editfield">
								<?php
								$unitInd2 = $taxonEditorObj->getUnitInd2();
								echo Sanitize::outString(($unitInd2 ? $unitInd2 . ' ' : '') . $taxonEditorObj->getUnitName2());
								?>
							</div>
							<div class="editfield" style="display:none;">
								<select name="unitind2" id="unitind2-select">
									<option value=""></option>
									<option value="&#215;" <?= (ord($unitInd2 ?? '') == 195 || strtolower($unitInd2 ?? '') == 'x' ? 'selected' : '') ?>>&#215;</option>
								</select>
								<input type="text" id="unitname2" name="unitname2" style="width:300px;border-style:inset;" value="<?= Sanitize::outString($taxonEditorObj->getUnitName2()) ?>" />
							</div>
						</div>
						<div id="div3hide" style="display: <?= empty($taxonEditorObj->getUnitName3()) ? 'none' : 'block' ?>" class="editDiv">
							<div class="editLabel"><?= $LANG['UNITNAME3'] ?>: </div>
							<div class="editfield">
								<?= Sanitize::outString($taxonEditorObj->getUnitInd3() . ' ' . $taxonEditorObj->getUnitName3()) ?>
							</div>
							<div class="editfield" style="display:none;">
								<input type="text" id="unitind3" name="unitind3" style="width:50px;border-style:inset;" value="<?= Sanitize::outString($taxonEditorObj->getUnitInd3()) ?>" />
								<input type="text" id="unitname3" name="unitname3" style="width:300px;border-style:inset;" value="<?= Sanitize::outString($taxonEditorObj->getUnitName3()) ?>" />
							</div>
						</div>
						<div id="div4hide" class="editDiv">
							<div id="unit4Display" style="display: <?= (empty($taxonEditorObj->getCultivarEpithet()) && empty($taxonEditorObj->getTradeName()))  ? 'none' : 'block' ?>">
								<div class="editLabel"><?= $LANG['UNITNAME4'] ?>: </div>
								<div class="editfield">
									<?= Sanitize::outString($taxonEditorObj->getCultivarEpithet()) ?? '' ?>
								</div>
								<div class="editfield" style="display:none;">
									<input placeholder="e.g., cultivar epithet (no quotes)" aria-placeholder="Cultivar epithet. Do not include quotations." type="text" id="cultivarEpithet" name="cultivarEpithet" style="width:300px;border-style:inset;" value="<?= Sanitize::outString($taxonEditorObj->getCultivarEpithet()) ?? '' ?>" />
								</div>
							</div>
						</div>
						<div id="div5hide" class="editDiv">
							<div id="unit5Display" style="display: <?= (empty($taxonEditorObj->getTradeName()) && empty($taxonEditorObj->getCultivarEpithet())) ? 'none' : 'block' ?>">
								<div class="editLabel"><?= $LANG['UNITNAME5'] ?>: </div>
								<div class="editfield">
									<?= Sanitize::outString($taxonEditorObj->getTradeName()) ?? '' ?>
								</div>
								<div class="editfield" style="display:none;">
									<input placeholder="e.g., TRADENAME" aria-placeholder="Entry will be converted to uppercase letters per trade name convention" type="text" id="tradeName" name="tradeName" style="width:300px;border-style:inset;" value="<?= Sanitize::outString($taxonEditorObj->getTradeName()) ?? '' ?>" />
								</div>
							</div>
						</div>
						<div id="author-div" class="editDiv">
							<div class="editLabel"><?= $LANG['AUTHOR'] ?>: </div>
							<div class="editfield">
								<?= Sanitize::outString($taxonEditorObj->getAuthor()) ?>
							</div>
							<div class="editfield" style="display:none;">
								<input type="text" id="author" name="author" style="width:400px;border-style:inset;" value="<?= Sanitize::outString($taxonEditorObj->getAuthor()) ?>" />
							</div>
						</div>
						<div id="kingdomdiv" class="editDiv">
							<div class="editLabel"><?= $LANG['KINGDOM'] ?>: </div>
							<div class="editfield">
								<?= Sanitize::outString($taxonEditorObj->getKingdomName()) ?>
							</div>
						</div>
						<div class="editDiv">
							<div class="editLabel"><?= $LANG['NOTES'] ?>: </div>
							<div class="editfield">
								<?= $taxonEditorObj->getNotes() ?>
							</div>
							<div class="editfield" style="display:none;width:90%;">
								<input type="text" id="notes" name="notes" style="width:100%;" value="<?= Sanitize::outString($taxonEditorObj->getNotes()) ?>" />
							</div>
						</div>
						<div class="editDiv">
							<div class="editLabel"><?= $LANG['SOURCE'] ?>: </div>
							<div class="editfield">
								<?php
								$safeSource = $taxonEditorObj->getSource() ?? '';
								$safeSource = strip_tags($safeSource, '<a>');
								echo $safeSource;
								?>
							</div>
							<div class="editfield" style="display:none;width:90%;">
								<input type="text" id="source" name="source" style="width:100%;" value="<?= $safeSource ?>" />
							</div>
						</div>
						<div class="editDiv">
							<div class="editLabel"><?= $LANG['LOC_SECURITY'] ?>: </div>
							<div class="editfield">
								<?php
								switch ($taxonEditorObj->getSecurityStatus()) {
									case 0:
										echo $LANG['SHOW_ALL_LOC'];
										break;
									case 1:
										echo $LANG['HIDE_LOC'];
										break;
									default:
										echo $LANG['LOC_SEC_NOT_SET'];
										break;
								}
								?>
							</div>
							<div class="editfield" style="display:none;">
								<select id="securitystatus" name="securitystatus">
									<option value="0"><?= $LANG['SEL_LOC_SETTING'] ?></option>
									<option value="0">---------------------------------</option>
									<option value="0" <?php if ($taxonEditorObj->getSecurityStatus() == 0) echo "SELECTED" ?>><?= $LANG['SHOW_ALL_LOC'] ?></option>
									<option value="1" <?php if ($taxonEditorObj->getSecurityStatus() == 1) echo "SELECTED" ?>><?= $LANG['HIDE_LOC'] ?></option>
								</select>
								<input type='hidden' name='securitystatusstart' value='<?= $taxonEditorObj->getSecurityStatus() ?>' />
							</div>
						</div>
						<div class="editfield" style="display:none;clear:both;margin:15px 0px" class="gridlike-form">
							<input type="hidden" name="tid" value="<?= $taxonEditorObj->getTid() ?>" />
							<input type="hidden" name="taxauthid" value="<?= $taxAuthId ?>">
							<div class="gridlike-form-row">
								<button type="button" id="taxoneditsubmit" name="taxonedits" value="submitEdits"><?= $LANG['SUBMIT_EDITS'] ?></button>
								<span id="required-display" style="color: var(--danger-color)">Fields marked with * are required</span>
								<span id="error-display" style="color: var(--danger-color)"></span>
							</div>
						</div>
					</form>
				</div>
				<div id="taxonstatusdiv" style="min-height:400px;">
					<fieldset style="width:95%;">
						<legend><b><?= $LANG['TAX_PLACEMENT'] ?></b></legend>
						<div style="padding:3px 7px;margin:-12px -10px 5px 0px;float:right;">
							<form name="taxauthidform" action="taxoneditor.php" method="post">
								<select name="taxauthid" onchange="this.form.submit()">
									<option value="1"><?= $LANG['DEFAULT_TAX'] ?></option>
									<option value="1">----------------------------</option>
									<?php
									$ttIdArr = $taxonEditorObj->getTaxonomicThesaurusIds();
									foreach ($ttIdArr as $ttID => $ttName) {
										echo '<option value=' . $ttID . ' ' . ($taxAuthId == $ttID ? 'SELECTED' : '') . '>' . Sanitize::outString($ttName) . '</option>';
									}
									?>
								</select>
								<input type="hidden" name="tid" value="<?= $taxonEditorObj->getTid() ?>" />
								<input type="hidden" name="tabindex" value="1" />
							</form>
						</div>
						<div style="font-size:120%;font-weight:bold;"><?= $LANG['STATUS'] ?>:
							<span style='color:red;'>
								<?php
								switch ($taxonEditorObj->getIsAccepted()) {
									case -2:		//In conflict, needs to be resolved
										echo $LANG['IN_CONFLICT'];
										break;
									case -1:		//Taxonomic status not yet assigned
										echo $LANG['NOT_YET_DEFINED'];
										break;
									case 0:			//Not Accepted
										echo $LANG['NOT_ACCEPTED'];
										break;
									case 1:			//Accepted
										echo $LANG['ACCEPTED'];
										break;
								}
								?>
							</span>
						</div>
						<div style="clear:both;margin:10px;">
							<div style="float:right;">
								<a href="#" onclick="toggle('tsedit');return false;"><img style='width:1.3em;border:0px;' src='../../images/edit.png' /></a>
							</div>
							<div style="float:left">
								<form name="taxstatusform" action="taxoneditor.php" method="post">
									<?php
									if ($taxonEditorObj->getRankId() > 140 && $taxonEditorObj->getFamily()) {
										?>
										<div class="editDiv">
											<div class="editLabel"><?= $LANG['FAMILY'] ?>: </div>
											<div class="tsedit">
												<?= Sanitize::outString($taxonEditorObj->getFamily()) ?>
											</div>
										</div>
										<?php
									}
									?>
									<div class="editDiv">
										<div class="editLabel"><?= $LANG['PARENT_TAXON'] ?>: </div>
										<div class="tsedit">
											<?= '<a href="taxoneditor.php?tid=' . $taxonEditorObj->getParentTid() . '">' . $taxonEditorObj->getParentHtmlFull() . '</a>' ?>
										</div>
										<div class="tsedit" style="display:none;margin:3px;">
											<input id="parentstr" name="parentstr" type="text" value="<?= Sanitize::outString($taxonEditorObj->getParentName()) ?>" style="width:450px" required />
											<input id="parenttid" name="parenttid" type="text" value="<?= $taxonEditorObj->getParentTid() ?>" />
										</div>
									</div>
									<div class="tsedit" style="display:none;clear:both;">
										<input type="hidden" name="tid" value="<?= $taxonEditorObj->getTid() ?>" />
										<input type="hidden" name="taxauthid" value="<?= $taxAuthId ?>">
										<?php
										$aArr = $taxonEditorObj->getAcceptedArr();
										$aStr = key($aArr);
										?>
										<input type="hidden" name="tidaccepted" value="<?= ($taxonEditorObj->getIsAccepted() == 1 ? $taxonEditorObj->getTid() : $aStr) ?>" />
										<input type="hidden" name="tabindex" value="1" />
										<input type="hidden" name="submitaction" value="updatetaxstatus" />
										<button type="submit" name="taxstatuseditsubmit"><?= $LANG['SUBMIT_UPPER_EDITS'] ?></button>
									</div>
								</form>
							</div>
						</div>
						<div id="AcceptedDiv" style="margin-top:30px;clear:both;">
							<?php
							if ($taxonEditorObj->getIsAccepted() <> 1) {	//Is Not Accepted
								$acceptedArr = $taxonEditorObj->getAcceptedArr();
								?>
								<div class="headingDiv"><?= $LANG['ACCEPTED_TAXON'] ?></div>
								<div style="float:right;">
									<a href="#" onclick="toggle('acceptedits');return false;"><img style="border:0px;width:1.3em;" src="../../images/edit.png" /></a>
								</div>
								<?php
								if ($acceptedArr) {
									echo "<ul>\n";
									foreach ($acceptedArr as $tidAccepted => $linkedTaxonArr) {
										echo "<li id='acclink-" . $tidAccepted . "'>\n";
										echo "<a href='taxoneditor.php?tid=" . $tidAccepted . "&taxauthid=" . $taxAuthId . "'><i>" . Sanitize::outString($linkedTaxonArr['sciname']) . "</i></a> " . Sanitize::outString($linkedTaxonArr["author"]) . "\n";
										if (count($acceptedArr) > 1) {
											echo '<span class="acceptedits" style="display:none;"><a href="taxoneditor.php?tabindex=1&tid=' . $tid . '&deltidaccepted=' . $tidAccepted . '&taxauthid=' . $taxAuthId . '">';
											echo '<img style="border:0px;width:1.3em;" src="../../images/del.png" />';
											echo '</a></span>';
										}
										if ($linkedTaxonArr["usagenotes"]) {
											echo "<div style='margin-left:10px;'>";
											if ($linkedTaxonArr["usagenotes"]) echo "<u>Notes</u>: " . $linkedTaxonArr["usagenotes"];
											echo "</div>\n";
										}
										echo "</li>\n";
									}

									echo "</ul>\n";
								} else {
									echo "<div style='margin:20px;'>" . $LANG['ACCEPTED_NOT_DESIGNATED'] . "</div>\n";
								}
								?>
								<div class="acceptedits" style="display:none;">
									<form id="accepteditsform" name="accepteditsform" action="taxoneditor.php" method="post" onsubmit="return validateAcceptedChangeForm(this)">
										<fieldset style="width:80%;margin:20px;padding:15px">
											<legend><b><?= $LANG['LINK_TO_OTHER_NAME'] ?></b></legend>
											<div>
												<?= $LANG['ACCEPTED_TAXON'] ?>:
												<input id="aefacceptedstr" name="acceptedstr" type="text" style="width:450px;" required />
												<input id="aeftidaccepted" name="tidaccepted" type="hidden" />
											</div>
											<div>
												<input type="checkbox" name="deleteother" checked /> <?= $LANG['REMOVE_OTHER_LINKS'] ?>
											</div>
											<div>
												<input type="hidden" name="tid" value="<?= $taxonEditorObj->get ?>) ?>" />
												<input type="hidden" name="taxauthid" value="<?= $taxAuthId ?>" />
												<input type="hidden" name="tabindex" value="1" />
												<button name="submitaction" type="submit" value="linkToAccepted"><?= $LANG['ADD_LINK'] ?></button>
											</div>
										</fieldset>
									</form>
									<form id="changetoacceptedform" name="changetoacceptedform" action="taxoneditor.php" method="post">
										<fieldset style="width:80%;margin:20px;padding:15px;">
											<legend><b><?= $LANG['CHANGE_TO_ACCEPTED'] ?></b></legend>
											<?php
											$acceptedTid = key($acceptedArr);
											if ($acceptedArr && count($acceptedArr) == 1) {
												if (!array_key_exists($acceptedTid, $hierarchyArr)) {
													?>
													<div>
														<input type="checkbox" name="switchacceptance" value="1" checked /> <?= $LANG['SWITCH_ACCEPTANCE'] ?>
													</div>
													<?php
												}
											}
											?>
											<div>
												<input type="hidden" name="tid" value="<?= $taxonEditorObj->getTid() ?>" />
												<input type="hidden" name="taxauthid" value="<?= $taxAuthId ?>" />
												<input type="hidden" name="tidaccepted" value="<?= $aStr ?>" />
												<input type="hidden" name="tabindex" value="1" />
												<button type='submit' id='changetoacceptedsubmit' name='changetoaccepted' value='Change Status to Accepted'><?= $LANG['CHANGE_STATUS_ACCEPTED'] ?></button>
											</div>
										</fieldset>
									</form>
								</div>
								<?php
							}
							?>
						</div>
						<div id="SynonymDiv" style="clear:both;padding-top:15px;">
							<?php
							if ($taxonEditorObj->getIsAccepted() <> 0) {	//Is Accepted
								?>
								<div class="headingDiv"><?= $LANG['SYNONYMS'] ?></div>
								<div style="float:right;">
									<a href="#" onclick="toggle('tonotaccepted');return false;"><img style='border:0px;width:1.3em;' src='../../images/edit.png' /></a>
								</div>
								<?php
								$synonymArr = $taxonEditorObj->getSynonyms();
								if ($synonymArr) {
									echo '<ul>';
									foreach ($synonymArr as $tidSyn => $synArr) {
										echo '<li> ';
										echo '<a href="taxoneditor.php?tid=' . $tidSyn . '&taxauthid=' . $taxAuthId . '"><i>' . Sanitize::outString($synArr['sciname']) . '</i></a> ' . Sanitize::outString($synArr['author']) . ' ';
										echo '<a href="#" onclick="toggle(\'syn-' . $tidSyn . '\');">';
										echo '<img style="border:0px;width:1.3em;" src="../../images/edit.png" />';
										echo '</a>';
										if ($synArr["notes"] || $synArr["unacceptabilityreason"]) {
											if ($synArr["unacceptabilityreason"]) {
												echo "<div style='margin-left:10px;'>";
												echo "<u>" . $LANG['REASON'] . "</u>: " . Sanitize::outString($synArr["unacceptabilityreason"]);
												echo "</div>";
											}
											if ($synArr["notes"]) {
												echo "<div style='margin-left:10px;'>";
												echo "<u>" . $LANG['NOTES'] . "</u>: " . Sanitize::outString($synArr["notes"]);
												echo "</div>";
											}
										}
										echo '</li>';
										?>
										<fieldset id="syn-<?= $tidSyn ?>" style="display:none;">
											<legend><b><?= $LANG['SYN_LINK_EDITOR'] ?></b></legend>
											<form id="synform-<?= $tidSyn ?>" name="synform-<?= $tidSyn ?>" action="taxoneditor.php" method="post">
												<div style="clear:both;">
													<?= $LANG['UNACCEPT_REASON'] ?>:
													<input id='unacceptabilityreason' name='unacceptabilityreason' type='text' style="width:400px;" value='<?= Sanitize::outString($synArr['unacceptabilityreason']) ?? '' ?>' />
												</div>
												<div>
													<?= $LANG['NOTES'] ?>:
													<input id='notes' name='notes' type='text' style="width:400px;" value='<?= Sanitize::outString($synArr['notes']) ?? '' ?>' />
												</div>
												<div>
													<?= $LANG['SORT_SEQ'] ?>:
													<input id='sortsequence' name='sortsequence' type='text' style="width:60px;" value='<?= $synArr['sortsequence'] ?>' />
												</div>
												<div>
													<input type="hidden" name="tid" value="<?= $taxonEditorObj->getTid() ?>" />
													<input type="hidden" name="tidsyn" value="<?= $tidSyn ?>" />
													<input type="hidden" name="taxauthid" value="<?= $taxAuthId ?>">
													<input type="hidden" name="tabindex" value="1" />
													<button type="submit" id="syneditsubmit" name="synonymedits" value="submitChanges"><?= $LANG['SUBMIT_EDITS'] ?></button>
												</div>
											</form>
										</fieldset>
										<?php
									}
									echo '</ul>';
								} else echo "<div style='margin:20px;'>" . $LANG['NO_SYN_LINKED_TAXON'] . "</div>";
								$hasAcceptedChildren = $taxonEditorObj->hasAcceptedChildren();
								?>
								<div id="tonotaccepted" style="display:none;">
									<form name="changeToNotAcceptedForm" action="taxoneditor.php" method="post" onsubmit="return validateAcceptedChangeForm(this)">
										<fieldset style="width:90%px;">
											<legend><b><?= $LANG['CHANGE_NOT_ACCEPTED'] ?></b></legend>
											<div style="margin:5px;">
												<?= $LANG['ACCEPTED_NAME'] ?>:
												<input id="ctnafacceptedstr" name="acceptedstr" type="text" style="width:550px;" required />
												<input id="ctnaftidaccepted" name="tidaccepted" type="hidden" value="" />
											</div>
											<div style="margin:5px;">
												<?= $LANG['REASON'] ?>:
												<input name="unacceptabilityreason" type="text" style="width:90%;" />
											</div>
											<div style="margin:5px;">
												<?= $LANG['NOTES'] ?>:
												<input name="notes" type="text" style="width:90%;" />
											</div>
											<div style="margin:5px;">
												<input name="tid" type="hidden" value="<?= $taxonEditorObj->getTid() ?>" />
												<input name="taxauthid" type="hidden" value="<?= $taxAuthId ?>">
												<input name="tabindex" type="hidden" value="1" />
												<button name="submitaction" type="submit" value="changeToNotAccepted" <?= ($hasAcceptedChildren ? 'disabled' : '') ?>><?= $LANG['CHANGE_STAT_NOT_ACCEPT'] ?></button>
											</div>
											<?php
											if ($hasAcceptedChildren) echo '<div style="margin:5px;color:orange;font-weight:bold;">' . $LANG['TAX_CANNOT_BE_NOT_ACCEPTED'] . '</div>';
											?>
											<div style="margin:5px;">
												* <?= $LANG['SYNONYMS_TRANSFERRED'] ?>
											</div>
										</fieldset>
									</form>
								</div>
							<?php
							}
						?>
						</div>
					</fieldset>
				</div>
				<div id="hierarchydiv" style="height:400px;">
					<fieldset style="width:420px;padding:25px;">
						<legend><b><?= $LANG['QUERY_HIERARCHY'] ?></b></legend>
						<div style="float:right;" title="<?= $LANG['REBUILD_HIERARCHY'] ?>">
							<form name="updatehierarchyform" action="taxoneditor.php" method="post">
								<input type="hidden" name="tid" value="<?= $taxonEditorObj->getTid() ?>" />
								<input type="hidden" name="taxauthid" value="<?= $taxAuthId ?>">
								<input type="hidden" name="submitaction" value="updatehierarchy" />
								<input type="hidden" name="tabindex" value="2" />
								<input type="image" name="imagesubmit" src="../../images/undo.png" style="width:20px;" />
							</form>
						</div>
						<?php
						if ($hierarchyArr) {
							$indent = 0;
							foreach ($hierarchyArr as $hierTid => $hierSciname) {
								if($hierTid != $tid){
									echo '<div style="margin-left:' . $indent . 'px;">';
									echo '<a href="taxoneditor.php?tid=' . $hierTid . '">' . Sanitize::outString($hierSciname) . '</a>';
									echo "</div>\n";
									$indent += 10;
								}
							}
							echo '<div style="margin-left:' . $indent . 'px;">';
							echo '<a href="taxoneditor.php?tid=' . $taxonEditorObj->getTid() . '">' . Sanitize::outString($taxonEditorObj->getSciName()) . '</a>';
							echo "</div>\n";
						} else {
							echo '<div style="margin:10px;">' . $LANG['EMPTY'] . '</div>';
						}
						?>
					</fieldset>
				</div>
			</div>
			<?php
		} else {
			if (!$tid) {
				if ($statusStr != 'SUCCESS: taxon deleted!') {
					echo "<div>" . $LANG['TARGET_TAXON_MISSING'] . "</div>";
				}
			} else {
				?>
				<div style="margin:30px;font-weight:bold;font-size:120%;">
					<?= $LANG['NOT_AUTH'] ?>
				</div>
				<?php
			}
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>
</html>
