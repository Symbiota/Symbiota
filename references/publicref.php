<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceDataset.php');
include_once($SERVER_ROOT.'/classes/ReferenceManager.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load('references/index');

header("Content-Type: text/html; charset=".$CHARSET);

// References
$refid = array_key_exists('refid',$_REQUEST)?$_REQUEST['refid']:0;

if(!is_numeric($refid)) $refid = 0;

$refManager = new ReferenceManager();
$rArr = $refManager->getReferenceMetadata($refid);
$searchUrl = '../../collections/list.php?refid='.$refid;
$tableUrl = '../../collections/listtabledisplay.php?refid='.$refid;
$ocArr = $refManager->getOccurrences($refid);
$datasetArr = $refManager -> getPublicRefDatasetArr($refid);
$taxArr = $refManager -> getRefTaxaArr($refid);
$collArr = $refManager -> getRefCollArr($refid);
$checklistArr = $refManager -> getRefChecklistArr($refid);

$isEditor = false;
if($IS_ADMIN) $isEditor = true;
elseif(array_key_exists('SuperAdmin',$USER_RIGHTS) || array_key_exists('SuperAdmin',$USER_RIGHTS)) $isEditor = true;


?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">
	<head>
		<title><?php echo $LANG['REF']; ?>: <?php echo $rArr['name'] ;?></title>
		<?php
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<link rel="stylesheet" href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.css">
		<link href="<?= $CSS_BASE_PATH ?>/symbiota/checklists/checklist.css" type="text/css" rel="stylesheet" />
		<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-3.7.1.min.js"></script>
		<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.min.js"></script>
	</head>
	<body>
		<?php
		$displayLeftMenu = true;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
			<a href="<?php echo htmlspecialchars($CLIENT_ROOT, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>/index.php"><?php echo $LANG['HOME']; ?></a> &gt;&gt;
			<b><?php echo $rArr['title'] ;?></b>
		</div>
		<!-- This is inner text! -->
		<div role="main" id="innertext">
    	<h1 class="page-heading"><?php echo $rArr['title'] ;?></h1>
		<?php
		if ($isEditor){
			echo '<b><a href="../references/refdetails.php?refid=' . htmlspecialchars($refid, ENT_QUOTES) . '">'
				. htmlspecialchars('Edit Reference', ENT_QUOTES) .
				'</a></b>';	
		}
		?>
    <ul>
      <!-- Metadata -->
      <div><?php echo $rArr['description'] ;?></div>

	 <!-- Citation Summary -->
		
		<?php
		if ($rArr['bibliographicCitation']) {
			?>
			<div>
				<h2><b><?php echo $LANG['CITATION'] ?? 'Citation' ?>:</b></h2>

				<a href="<?php echo htmlspecialchars($rArr['url'], ENT_QUOTES); ?>">
					<?php echo htmlspecialchars($rArr['bibliographicCitation'], ENT_QUOTES); ?>
				</a>
			</div>
			<?php
		}
		?>
	<div><h2><b><?php echo $LANG['ASSOC_REC_RES'] ?? 'Associated Records and Resources'?>:</b><h2></div>	

	<div class="accordions">

		<!-- Occurrences -->
		<input type="checkbox" id="acc-occurrences" class="accordion-selector" />
		<label for="acc-occurrences" class="accordion-header">
			<?php echo $LANG['OCCUR'] ?? 'Occurrences'; ?>
		</label>
		<div class="content">
			<p>
				<?php echo $LANG['INCLUDES']; ?>
				<?php echo count($ocArr); ?>
				<?php echo $LANG['RECORDS']; ?>
			</p>

			<a class="btn" href="<?php echo htmlspecialchars($searchUrl, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>">
				<?php echo $LANG['VIEW_AND_DOWNLOAD']; ?>
			</a>
			<br>
			<a class="btn" href="<?php echo htmlspecialchars($tableUrl, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>">
				<?php echo $LANG['VIEW_SAMPLE']; ?>
			</a>
		</div>

		<!-- Datasets -->
		<?php if ($datasetArr) { ?>
			<input type="checkbox" id="acc-datasets" class="accordion-selector" />
			<label for="acc-datasets" class="accordion-header">
				<?php echo $LANG['DATASETS'] ?? 'Datasets'; ?>
			</label>
			<div class="content">
				<?php foreach ($datasetArr as $datasetid => $datasetName) { ?>
					<div>
						<a href="../collections/datasets/public.php?datasetid=<?php echo $datasetid; ?>">
							<?php echo htmlspecialchars($datasetName, ENT_QUOTES); ?>
						</a>
					</div>
				<?php } ?>
			</div>
		<?php } ?>

		<!-- Taxa -->
		<?php if ($taxArr) { ?>
			<input type="checkbox" id="acc-taxa" class="accordion-selector" />
			<label for="acc-taxa" class="accordion-header">
				<?php echo $LANG['TAXA'] ?? 'Taxa'; ?>
			</label>
			<div class="content">
				<?php foreach ($taxArr as $tid => $taxon) { ?>
					<div>
						<a href="../taxa/index.php?taxon=<?php echo $tid; ?>">
							<?php echo htmlspecialchars($taxon, ENT_QUOTES); ?>
						</a>
					</div>
				<?php } ?>
			</div>
		<?php } ?>

		<!-- Checklists -->
		<?php if ($checklistArr) { ?>
			<input type="checkbox" id="acc-checklists" class="accordion-selector" />
			<label for="acc-checklists" class="accordion-header">
				<?php echo $LANG['CHECKLISTS'] ?? 'Checklists'; ?>
			</label>
			<div class="content">
				<?php foreach ($checklistArr as $clid => $checklist) { ?>
					<div>
						<a href="../checklists/checklist.php?clid=<?php echo $clid; ?>">
							<?php echo htmlspecialchars($checklist, ENT_QUOTES); ?>
						</a>
					</div>
				<?php } ?>
			</div>
		<?php } ?>

		<!-- Collections -->
		<?php if ($collArr) { ?>
			<input type="checkbox" id="acc-collections" class="accordion-selector" />
			<label for="acc-collections" class="accordion-header">
				<?php echo $LANG['COLLECTIONS'] ?? 'Collections'; ?>
			</label>
			<div class="content">
				<?php foreach ($collArr as $collid => $collection) { ?>
					<div>
						<a href="../collections/misc/collprofiles.php?collid=<?php echo $collid; ?>">
							<?php echo htmlspecialchars($collection, ENT_QUOTES); ?>
						</a>
					</div>
				<?php } ?>
			</div>
		<?php } ?>

	</div>

    </ul>
		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
