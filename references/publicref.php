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

?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">
	<head>
		<title><?php echo $LANG['REF']; ?>: <?php echo $rArr['name'] ;?></title>
		<?php
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
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

      <!-- Occurrences -->
		<div><h3><b> <?php echo $LANG['OCCUR'] ?? 'Associated Occurrences' ?>:</b></h3></div>

      <p><?php echo $LANG['INCLUDES']; ?> <?php echo count($ocArr); ?> <?php echo $LANG['RECORDS']; ?></p>

      <a class="btn" href="<?php echo htmlspecialchars($searchUrl, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) ;?>"><?php echo $LANG['VIEW_AND_DOWNLOAD']; ?></a></br>
      <a class="btn" href="<?php echo htmlspecialchars($tableUrl, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) ;?>"><?php echo $LANG['VIEW_SAMPLE']; ?></a>
      <!-- <p><a href="#">Download this Dataset</a></p> -->

	<!-- Associated Datasets -->
		<?php 
		if ($datasetArr) {
			?>
			<div><h3><b><?php echo $LANG['DATASETS'] ?? 'Datasets' ?>:</b></h3></div>
		<?php

			foreach($datasetArr as $datasetid => $datasetName){
				echo '<div id="occur-ref" class="occur-ref">';
				echo '<a href="../collections/datasets/public.php?datasetid=' . htmlspecialchars($datasetid, ENT_QUOTES) . '">'
					. htmlspecialchars($datasetName, ENT_QUOTES) .
					'</a>';	
				echo '</div>';
			}
		}
		?>

	<!-- Associated Taxa -->
		<?php 
		if ($taxArr) {
			?>
		<div><h3><b> <?php echo $LANG['TAXA'] ?? 'Taxa' ?>:</b></h3></div>
			<?php
			foreach($taxArr as $tid => $taxon){
				echo '<div id="occur-ref" class="occur-ref">';
				echo '<a href="../taxa/index.php?taxon=' . htmlspecialchars($tid, ENT_QUOTES) . '">'
					. htmlspecialchars($taxon, ENT_QUOTES) .
					'</a>';	
				echo '</div>';
			}
		}
		?>

	<!-- Associated Checklists -->
		<?php 
		if ($checklistArr) {
			?>
		<div><h3><b> <?php echo $LANG['CHECKLISTS'] ?? 'Checklists' ?>:</b></h3></div>
			<?php
			foreach($checklistArr as $clid => $checklist){
				echo '<div id="occur-ref" class="occur-ref">';
				// NEON Customization
				// echo '<a href="../neon/checklists/checklist.php?clid=' . htmlspecialchars($clid, ENT_QUOTES) . '">'
				// 	. htmlspecialchars($checklist, ENT_QUOTES) .
				// 	'</a>';	
				// END NEON Customization
				echo '<a href="../checklists/checklist.php?clid=' . htmlspecialchars($clid, ENT_QUOTES) . '">'
					. htmlspecialchars($checklist, ENT_QUOTES) .
					'</a>';	
				echo '</div>';
			}
		}
		?>

	<!-- Associated Collections -->
		<?php 
		if ($collArr) {
			?>
		<div><h3><b> <?php echo $LANG['COLLECTIONs'] ?? 'Collections' ?>:</b></h3></div>
			<?php
			foreach($collArr as $collid => $collection){
				echo '<div id="occur-ref" class="occur-ref">';
				// NEON customization
				// echo '<a href="../collections/misc/neoncollprofiles.php?collid=' . htmlspecialchars($collid, ENT_QUOTES) . '">'
				// 	. htmlspecialchars($collection, ENT_QUOTES) .
				// 	'</a>';	
				// END NEON customization
				echo '<a href="../collections/misc/collprofiles.php?collid=' . htmlspecialchars($collid, ENT_QUOTES) . '">'
				 	. htmlspecialchars($collection, ENT_QUOTES) .
				 	'</a>';	
				echo '</div>';
			}
		}
		?>

    </ul>
		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
