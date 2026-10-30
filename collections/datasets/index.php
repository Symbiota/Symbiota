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

$isEditor = false;
if($datasetid && $SYMB_UID){
	if ($SYMB_UID == $dsMetaArr['uid']) {
		$isEditor = true;
	}
	elseif($roles = $datasetManager->getUserRoles($datasetid, $SYMB_UID)) {
		if (isset($roles[$datasetid][$SYMB_UID]['roles']['DatasetAdmin'])) {
			$isEditor = true;
		} elseif (isset($roles[$datasetid][$SYMB_UID]['roles']['DatasetEditor'])) {
			$isEditor = true;
		}
	}
}
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
	<head>
		<title><?= $LANG['PUB_DAT_LIST'] ?></title>
		<?php
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<script>
			function targetDownloadPopup(f) {
				window.open('', 'downloadpopup', 'left=100,top=50,width=900,height=700');
				f.target = 'downloadpopup';
			}
		</script>
		<style>
			.img-icon{ width: 1.2em }
			.split-div{
				display: flex;
				justify-content: space-between;
				width: 100%;
				align-items: center;
			}
		</style>
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
		<div role="main" id="innertext" style="margin-bottom: 20px">
			<?php
			if($datasetid){
				if($dsMetaArr['isPublic'] || $isEditor){
					?>
					<div class="split-div">
						<div><h1 class="page-heading"><?= $dsMetaArr['datasetName'] ?></h1></div>
						<div>
							<?php
							if($SYMB_UID){
								?>
								<span><a href="datasetuserlist.php"><img class="img-icon" src="../../images/list.png" title="<?= $LANG['VIEW_USER_LIST'] ?>" ></a></span>
								<?php
							}
							if($isEditor){
								?>
								<span><a href="datasetmanager.php?datasetid=<?= $datasetid ?>"><img class="img-icon" src="../../images/edit.png" title="<?= $LANG['MANAGE_DATASET'] ?>" ></a></span>
								<?php
							}
							?>
						</div>
					</div>
					<div>
						<?php
						if($dsMetaArr['datasetIdentifier']) echo '<p><b>' . $LANG['DATASET_IDENTIFIER'] . ':</b> ' . $dsMetaArr['datasetIdentifier'] . '</p>';
						?>
						<p><?= $dsMetaArr['description'] ?></p>
						<p><?= $dsMetaArr['bibliographicCitation'] ?></p>
					</div>
					<div style="">
						<?php
						include('datasetoccurlist.php');
						?>
					</div>
					<div style="margin:15px">
						<form name="sample-list-form" action="../list.php" method="post" target="_blank">
							<input name="datasetid" type="hidden" value="<?= $datasetid ?>">
							<button type="submit"><?= $LANG['OCCURRENCES_LIST'] ?></button>
						</form>
					</div>
					<?php
				}
				else{
					?>
					<hr><?= $LANG['PRIVATE_DATASET'] ?></hr>
					<?php
				}
			}
			else{
				?>
				<div class="split-div">
					<div><h1 class="page-heading"><?= $LANG['PUB_DAT_LIST'] ?></h1></div>
					<div>
						<?php
						if($SYMB_UID){
							?>
							<span><a href="datasetuserlist.php"><img class="img-icon" src="../../images/list.png" title="<?= $LANG['VIEW_USER_LIST'] ?>" ></a></span>
							<?php
						}
						?>
					</div>
				</div>
				<?php
				if($datasetArr = $datasetManager->getDatasetsGroupedByCategory()){
					foreach($datasetArr as $category => $dsArr){
						echo '<div style="margin-bottom: 50px">';
						if($category == 'Z-UNDEFINED_CATEGORY'){
							$category = '';
							if(count($datasetArr) > 1){
								$category = $LANG['UNDEFINED_CATEGORY'];
							}
						}
						echo '<h3>' . $category . '</h3>';
						echo '<ul>';
						foreach($dsArr as $dsID => $unitArr){
							$dsName = $unitArr['datasetName'];
							$role = '';
							if(!$unitArr['isPublic']){
								$role = ' (' . $LANG['PRIVATE'];
								if(isset($unitArr['role'])) $role .= ' - ' . $unitArr['role'];
								$role .= ')';
							}
							echo '<li><a href="index.php?datasetid=' . $dsID . '">' . Sanitize::outString($dsName) . '</a>' . strtolower($role) . '</li>';
						}
						echo '</ul>';
						echo '</div>';
					}
				}
				else{
					echo $LANG['NO_DATASETS'];
				}
			}
			?>
		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
