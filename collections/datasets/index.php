<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/OccurrenceDataset.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load('collections/datasets/index');

header("Content-Type: text/html; charset=".$CHARSET);

$datasetid = array_key_exists('datasetid', $_REQUEST) ? Sanitize::int($_REQUEST['datasetid']) : 0;

$datasetManager = new OccurrenceDataset();
$datasetManager->setDatasetID($datasetid);
$dsMetaArr = $datasetManager->getDatasetMetadata();
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
	<head>
		<title><?= $LANG['PUB_DAT_LIST'] ?></title>
		<?php
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
	</head>
	<body>
		<?php
		include($SERVER_ROOT . '/includes/header.php');
		?>
		<div class="navpath">
			<a href="<?= $CLIENT_ROOT ?>/index.php"><?= $LANG['HOME'] ?></a> &gt;&gt;
			<a href="<?= $CLIENT_ROOT ?>/collections/datasets/index.php"><?= $LANG['PUB_DAT_LIST'] ?></a> &gt;&gt;
			<?php
			if($datasetid) echo '<b>' . $dsMetaArr['datasetName'] . '</b>';
			?>
		</div>
		<div role="main" id="innertext">
			<?php
			if($datasetid){
				$searchUrl = $CLIENT_ROOT . '/collections/list.php?datasetid=' . $datasetid;
				$tableUrl = $CLIENT_ROOT . '/collections/listtabledisplay.php?datasetid=' . $datasetid;
				$taxaUrl = $CLIENT_ROOT . '/collections/list.php?datasetid=' . $datasetid . '&tabindex=0';
				?>
				<h1 class="page-heading"><?= $dsMetaArr['datasetName'] ?></h1>
				<div><?= $dsMetaArr['description'] ;?></div>
				<p><?= $LANG['INCLUDES'] ?> <?= $datasetManager->getOccurrenceCount() ?> <?= $LANG['RECORDS'] ?></p>
				<p><a class="btn" href="<?= $searchUrl ?>" target="_blank"><?= $LANG['VIEW_AND_DOWNLOAD'] ?></a></p>
				<p><a class="btn" href="<?= $tableUrl ?>" target="_blank"><?= $LANG['VIEW_SAMPLE'] ?></a></p>
				<p><a class="btn" href="<?= $taxaUrl ?>" target="_blank"><?= $LANG['VIEW_LIST'] ?></a></p>
				<?php
			}
			else{
				echo '<h1 class="page-heading">' . $LANG['PUB_DAT_LIST'] . '</h1>';
				if($datasetArr = $datasetManager->getDatasetList()){
					echo '<ul>';
					foreach($datasetArr as $category => $dsArr){
						if($category == 'UNDEFINED_CATEGORY'){
							$category = '';
							if(count($datasetArr) > 1){
								$category = $LANG['UNDEFINED_CATEGORY'];
							}
						}
						echo '<h3>' . $category . '</h3>';
						foreach($dsArr as $dsID => $dsName){
							echo '<li><a href="index.php?datasetid=' . $dsID . '">' . Sanitize::outString($dsName) . '</a></li>';
						}
					}
				}
				else{
					echo $LANG['NO_DATASETS'];
				}
			}
			if($SYMB_UID){
				?>
				<div style="margin: 30px 0px">
					<hr>
				</div>
				<h3><a href="datasetuserlist.php"><?= $LANG['VIEW_USER_LIST'] ?></a></h3>
				<?php
			}
			?>
		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
