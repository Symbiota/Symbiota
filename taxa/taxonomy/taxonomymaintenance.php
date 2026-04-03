<?php
use phpseclib3\Math\BigInteger\Engines\PHP;

include_once('../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/TaxonomyMaintenance.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/PortalProperties.php');

Language::load('taxa/taxonomy/taxonomymaintenance');

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../taxa/taxonomy/taxonomymaintenance.php?' . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$node = array_key_exists('node', $_REQUEST) ? htmlspecialchars($_REQUEST['node']) : '';
$taxAuthID = array_key_exists('taxauthid', $_REQUEST) ? filter_var($_REQUEST['taxauthid'], FILTER_SANITIZE_NUMBER_INT) : 1;
$taxonomicAuthority = array_key_exists('taxonomicauthority', $_POST) ? $_POST['taxonomicauthority'] : 0;
$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';

$taxonomyManager = new TaxonomyMaintenance();
$taxonomyManager->setNode($node);
$taxonomyManager->setTaxAuthID($taxAuthID);
//$taxonomyManager->setTaxonomicAuthority();

$isEditor = false;
if($IS_ADMIN || array_key_exists('Taxonomy', $USER_RIGHTS)) $isEditor = true;

$statusStr = '';
if($isEditor && $action){
	if($action == 'autoSyncFamilies'){
		if($cnt = $taxonomyManager->synchronizeFamilyQuickLookup()){
			$statusStr = 'Success batch synchronized ' . $cnt . ' records';
		}
	}
	elseif($action == 'pruneIllegalParentNodes'){
		if($cnt = $taxonomyManager->pruneIllegalParentNodes()){
			$statusStr = 'Successfully pruned bad nodes';
		}
	}
}
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<title><?= $DEFAULT_TITLE . ' ' . $LANG['TAX_MAINT'] ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET ?>"/>
	<link href="<?= $CSS_BASE_PATH ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/symb/shared.js?ver=1a" type="text/javascript"></script>
	<script type="text/javascript">
		function toggleDetailSection(target){
			const targetList = document.querySelectorAll(".subsection-div");
			for (let i = 0; i < targetList.length; i++) {
				let targetDisplay = window.getComputedStyle(targetList[i]).getPropertyValue("display");
				targetList[i].style.display = "none";
			}
			toggleElement(target, 'block');
		}
	</script>
	<style>
		 .form-section{ margin: 5px 15px; }
		 .icon{ width: 15px; }
		 .subsection-div{ margin: 5px 10px }
		 .desc-div{ margin: 10px }
		 .listSection-div{ margin-bottom: 5px }
		 button{ margin: 5px 20px }
		 fieldset{ padding: 15px; }
		 legend{ font-weight: bold }
	</style>
</head>
<body>
	<?php
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="../index.php"><?= $LANG['HOME'] ?></a> &gt;&gt;
		<b><a href="taxonomymaintenance.php"><?= $LANG['TAX_MAINT'] ?></a></b>
	</div>
	<div role="main" id="innertext">
		<h1 class="page-heading"><?= $LANG['TAX_MAINT']; ?></h1>
		<?php
		if($statusStr){
			?>
			<hr/>
			<div style="color:<?= (strpos($statusStr,'SUCCESS') !== false ? 'green' : 'red'); ?>;margin:15px;">
				<?= $statusStr; ?>
			</div>
			<hr/>
			<?php
		}
		if($isEditor){
			if($action){
				$reportArr = $taxonomyManager->getTaxonomyReport();
				?>
				<div style="margin-bottom: 15px">
					<div class="listSection-div">
						<label>Orphaned Taxa (across all nodes): </label> <?= $reportArr['orphanedTaxa'] ?> <a href="#" onclick="toggleDetailSection('#orphanedTaxa-div')"><img class="icon" src="../../images/triangledown.png"></a>
						<div id="orphanedTaxa-div" class="subsection-div" style="display: none<?= ($action == 'listOrphanedTaxa' ? 'block' : 'none') ?>">
							<fieldset>
								<legend>Details</legend>
								<div class="desc-div">
									Taxa that have a base record within taxa table, but hierarchy and acceptance is not defined (e.g. missing taxstatus record). Repair options include:
									<ul>
										<li><b>List taxa for manual cleaning:</b> Opening record will attempt to integrate taxon into heirachy as a n accpeted taxon, which will need to be properly adjusted.</li>
										<li><b>Resolve using Taxonomic Authority: </b></li>
										<li><b>Auto-resolve Taxa (not recommended): </b></li>
									</ul>
								</div>
								<div class="form-section">
									<form name="listOrphanedTaxaForm" method="post" action="taxonomymaintenance.php">
										<input name="action" type="radio" value="listOrphanedTaxa" required> <label>List Orphaned Taxa</label><br>
										<input name="action" type="radio" value="authorityResolveOrphanedTaxa" onclick="toggle('orphaned-taxauth-div');" required> <label>Resolve using Taxonomic Authority</label><br>
										<div id="orphaned-taxauth-div" style="display:none;margin-left: 15px">
											<fieldset>
												<legend>Taxonomic Authorities</legend>
												<legend><?= $LANG['TAXONOMY_AUTHORITIES'] ?></legend>
													<?php
													$taxResourceList = PortalProperties::getTaxonomicAuthorities();
													foreach($taxResourceList as $taKey => $taValue){
														echo '<input name="taxonomicauthority" id="taxresource" type="radio" value="' . $taKey . '" ' . ($taKey == $taxonomicAuthority ? 'checked' : '') . ' > ';
														echo '<label for="taxonomicauthority">' . $taValue . ' </label><br/>';
													}
													?>
											</fieldset>
										</div>
										<input name="action" type="radio" value="autoResolveOrphanedTaxa" required> <label>Auto-resolve Taxa</label>
										<input name="node" type="hidden" value="<?= $node ?>" >
										<input name="taxauthid" type="hidden" value="<?= $taxAuthID ?>" >
										<button name="action-button" type="submit">Perform Action</button>
									</form>
								</div>
							</fieldset>
						</div>
					</div>
					<div class="listSection-div">
						<label>Mismatched families: </label> <?= $reportArr['mismatchedFamilies'] ?> <a href="#" onclick="toggleDetailSection('#mismatchFamilies-div')"><img class="icon" src="../../images/triangledown.png"></a>
						<div id="mismatchFamilies-div" class="subsection-div" style="display: none">
							<fieldset>
								<legend>Details</legend>
								<div class="desc-div">
									Quick-lookup family field is out of sync with defined hierarchy. The control options below provides ability to list of auto-synchronize families.
									If all taxon records fail to update, there is likely an issue with the hierarchy (e.g. taxa linked to multiple families).
								</div>
								<div class="form-section">
									<form name="mismatchFamilyForm" method="post" action="taxonomymaintenance.php">
										<input name="action" type="radio" value="autoSyncFamilies"> <label>Auto-Synchronize Families</label>
										<input name="action" type="radio" value="listMismatchedFamilies"> <label>List Mismatched Families</label>
										<input name="node" type="hidden" value="<?= $node ?>" >
										<input name="taxauthid" type="hidden" value="<?= $taxAuthID ?>" >
										<button name="action-button" type="submit">Perform Action</button>
									</form>
								</div>
							</fieldset>
						</div>
					</div>
					<div class="listSection-div">
						<label>Illegal parents: </label> <?= $reportArr['illegalParentRankid'] ?> <a href="#" onclick="toggleDetailSection('#illegalParentRankid-div')"><img class="icon" src="../../images/triangledown.png"></a>
						<div id="illegalParentRankid-div" class="subsection-div" style="display: none">
							<fieldset>
								<legend>Details</legend>
								<div class="desc-div">
									Taxa linked to parents that have a greater rankID is not allowed. Repair options include:
									<ul>
										<li>List taxa for manual cleaning</li>
										<li>Automatically prune out bad nodes</li>
									</ul>
								</div>
								<div class="form-section">
									<form name="listIllegalParentRankidForm" method="post" action="taxonomymaintenance.php">
										<button name="action" type="submit" value="listIllegalParentRankid">List Bad Parents</button>
										<input name="node" type="hidden" value="<?= $node ?>" >
										<input name="taxauthid" type="hidden" value="<?= $taxAuthID ?>" >
									</form>
									<form name="autoPruneParentNodesForm" method="post" action="taxonomymaintenance.php">
										<button name="action" type="submit" value="pruneIllegalParentNodes">Automatically Prune Bad Nodes</button>
										<input name="node" type="hidden" value="<?= $node ?>" >
										<input name="taxauthid" type="hidden" value="<?= $taxAuthID ?>" >
									</form>
								</div>
							</fieldset>
						</div>
					</div>
					<div class="listSection-div">
						<label>Accepted with non-accepted parents: </label> <?= $reportArr['acceptedNonAcceptedParent'] ?> <a href="#" onclick="toggleDetailSection('#acceptedNonAcceptedParent-div')"><img class="icon" src="../../images/triangledown.png"></a>
						<div id="acceptedNonAcceptedParent-div" class="subsection-div" style="display: none">
							<fieldset>
								<legend>Details</legend>
								<div class="desc-div">
									Accepted taxa linked to non-accepted parents. List taxa to display cleaning options.
								</div>
								<div class="form-section">
									<form name="acceptedNonAcceptedParentForm" method="post" action="taxonomymaintenance.php">
										<button name="action" type="submit" value="listAcceptedNonAcceptedParent">List Taxa</button>
										<input name="node" type="hidden" value="<?= $node ?>" >
										<input name="taxauthid" type="hidden" value="<?= $taxAuthID ?>" >
									</form>
								</div>
							</fieldset>
						</div>
					</div>
					<div class="listSection-div">
						<label>Non-accepted taxa accepted to non-accepted taxon: </label> <?= $reportArr['nonAcceptedLinkedToNonAccepted'] ?> <a href="#" onclick="toggleDetailSection('#nonAcceptedLinkedToNonAccepted-div')"><img class="icon" src="../../images/triangledown.png"></a>
						<div id="nonAcceptedLinkedToNonAccepted-div" class="subsection-div" style="display: none">
							<fieldset>
								<legend>Details</legend>
								<div class="desc-div">
									Non-accepted taxa linked to non-accepted taxa. List taxa to display cleaning options.
								</div>
								<div class="form-section">
									<form name="nonAcceptedLinkedToNonAcceptedForm" method="post" action="taxonomymaintenance.php">
										<button name="action" type="submit" value="listNonAcceptedLinkedToNonAccepted">List Taxa</button>
										<input name="node" type="hidden" value="<?= $node ?>" >
										<input name="taxauthid" type="hidden" value="<?= $taxAuthID ?>" >
									</form>
								</div>
							</fieldset>
						</div>
					</div>
					<div class="listSection-div">
						<label>Mislinked infraspecific taxa: </label> <?= $reportArr['infraspIssues'] ?> <a href="#" onclick="toggleDetailSection('#infraspIssues-div')"><img class="icon" src="../../images/triangledown.png"></a>
						<div id="infraspIssues-div" class="subsection-div" style="display: none">
							<fieldset>
								<legend>Details</legend>
								<div class="desc-div">
									Infraspecific taxa linked to non-species ranked taxon. List taxa to display cleaning options.
								</div>
								<div class="form-section">
									<form name="infraspIssuesForm" method="post" action="taxonomymaintenance.php">
										<button name="action" type="submit" value="infraspIssues">List Taxa</button>
										<input name="node" type="hidden" value="<?= $node ?>" >
										<input name="taxauthid" type="hidden" value="<?= $taxAuthID ?>" >
									</form>
								</div>
							</fieldset>
						</div>
					</div>
					<div class="listSection-div">
						<label>Mislinked species ranked taxa: </label> <?= $reportArr['speciesIssues'] ?> <a href="#" onclick="toggleDetailSection('#speciesIssues-div')"><img class="icon" src="../../images/triangledown.png"></a>
						<div id="speciesIssues-div" class="subsection-div" style="display: none">
							<fieldset>
								<legend>Details</legend>
								<div class="desc-div">
									Species ranked taxa linked to taxon rank less than genus rank. List taxa to display cleaning options.
								</div>
								<div class="form-section">
									<form name="speciesIssuesForm" method="post" action="taxonomymaintenance.php">
										<button name="action" type="submit" value="speciesIssues">List Taxa</button>
										<input name="node" type="hidden" value="<?= $node ?>" >
										<input name="taxauthid" type="hidden" value="<?= $taxAuthID ?>" >
									</form>
								</div>
							</fieldset>
						</div>
					</div>
					<div class="listSection-div">
						<label>Mislinked genera: </label> <?= $reportArr['generaIssues'] ?> <a href="#" onclick="toggleDetailSection('#generaIssues-div')"><img class="icon" src="../../images/triangledown.png"></a>
						<div id="generaIssues-div" class="subsection-div" style="display: none">
							<fieldset>
								<legend>Details</legend>
								<div class="desc-div">
									Genera linked to taxon rank less than family. List taxa to display cleaning options.
								</div>
								<div class="form-section">
									<form name="generaIssuesForm" method="post" action="taxonomymaintenance.php">
										<button name="action" type="submit" value="generaIssues">List Taxa</button>
										<input name="node" type="hidden" value="<?= $node ?>" >
										<input name="taxauthid" type="hidden" value="<?= $taxAuthID ?>" >
									</form>
								</div>
							</fieldset>
						</div>
					</div>
				</div>
				<hr>
				<?php
				//List problematic taxa
				$problemTitle = '';
				$problemDescription = '';
				$problemCount = 0;
				$taxaList = null;
				if($action == 'listOrphanedTaxa'){
					$taxaList = $taxonomyManager->getOrphanedTaxa();
					$problemTitle = 'Orphaned Taxa';
					$problemDescription = 'Clicking on a taxon will open Taxonomic Editor with the taxon set as "accepted".
						An attempt will also be made to deduce and link parent taxon.
						Manual adjustments will be necessary if taxon is not accepted or parent linkages fail.';
					$problemCount = $taxonomyManager->getOrphanedTaxaCount();
				}
				elseif($action == 'listMismatchedFamilies'){
					$taxaList = $taxonomyManager->getMismatchedFamilyTaxa();
					$problemTitle = '';
					$problemDescription = '';
					$problemCount = $taxonomyManager->getMismatchedFamilyCount();
				}
				elseif($action == 'listIllegalParentRankid'){
					$taxaList = $taxonomyManager->getIllegalParentRankidTaxa();
					$problemTitle = '';
					$problemDescription = '';
					$problemCount = $taxonomyManager->getIllegalParentRankidCount();
				}
				elseif($action == 'listAcceptedNonAcceptedParent'){
					$taxaList = $taxonomyManager->getAcceptedNonAcceptedParentTaxa();
					$problemTitle = '';
					$problemDescription = '';
					$problemCount = $taxonomyManager->getAcceptedNonAcceptedParentCount();
				}
				elseif($action == 'listNonAcceptedLinkedToNonAccepted'){
					$taxaList = $taxonomyManager->getNonAcceptedLinkedToNonAcceptedTaxa();
					$problemTitle = '';
					$problemDescription = '';
					$problemCount = $taxonomyManager->getNonAcceptedLinkedToNonAcceptedCount();
				}
				if($taxaList){
					?>
					<h3><?= $problemTitle . ': ' . $problemCount ?> records</h3>
					<?php
					if($problemDescription){
						?>
						<div><?= $problemDescription ?></div>
						<?php
					}
					?>
					<ul>
						<?php
						foreach($taxaList as $tid => $taxaArr){
							?>
							<li>
								<?php
								$sciname = $taxaArr['sciname'];
								if($taxaArr['rankid'] > 180) $sciname = '<i>' . $sciname . '</i>';
								echo '<a href="' . $CLIENT_ROOT . '/taxa/taxonomy/taxoneditor.php?tid=' . $tid . '" target="_blank">' . $sciname . '</a> ' . $taxaArr['author'];
								?>
							</li>
							<?php
						}
						?>
					</ul>
					<?php
				}
			}
			else{
				?>
				<form name="generateReportForm" method="post" action="taxonomymaintenance.php">
					<fieldset>
						<legend>Taxonomic Thesaurus Cleaning Tool</legend>
						<div class="desc-div" style="margin-bottom: 10px">
							Select taxonomic node to be evaluated and click Generate Report button
						</div>
						<div class="form-section">
							<label>Taxon Node: </label>
							<select name="node" required>
								<option value="">Select Taxon Node</option>
								<option value="">-----------------------</option>
								<?php
								$nodeArr = $taxonomyManager->getNodeArr();
								foreach($nodeArr as $nodeTid => $nodeName){
									echo '<option value="' . $nodeTid . '-' . $nodeName . '">' . $nodeName . '</option>';
								}
								?>
							</select>
						</div>
						<div class="form-section">
							<input name="taxauthid" type="hidden" value="<?= $taxAuthID ?>">
							<button name="action" type="submit" value="generateReport" style="margin: 15px">Generate Node Report</button>
						</div>
					</fieldset>
				</form>
				<?php
			}
		}
		else{
			?>
			<div style="margin:30px;font-weight:bold;font-size:120%;">
				<?= $LANG['NOT_AUTH']; ?>
			</div>
			<?php
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
