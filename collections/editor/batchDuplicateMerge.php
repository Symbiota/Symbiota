<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT . '/components/breadcrumbs.php');
include_once($SERVER_ROOT . '/classes/utilities/QueryUtil.php');
include_once($SERVER_ROOT . '/classes/OccurrenceCleaner.php');
include_once($SERVER_ROOT . '/classes/OccurrenceEditorManager.php');

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$start = array_key_exists('start',$_REQUEST)?$_REQUEST['start']:0;
$limit = array_key_exists('limit',$_REQUEST)?$_REQUEST['limit']:1000;

$collid = 2;

$occurrences = [
	0 => [],
	1 => [],
	2 => [],
	3 => [],
	4 => [],
	5 => [],
];
$sql = 'SELECT dl.duplicateid, o.* from omoccurduplicatelink dl
	join omoccurrences o on o.occid = dl.occid
	where dl.duplicateid in (
		SELECT duplicateid from omoccurduplicatelink dl 
		join omoccurrences o on o.occid = dl.occid 
		where collid = ?
	)';
$conn = Database::connect('read');

$rs = QueryUtil::executeQuery($conn, $sql, [$collid]);
$duplicates = $rs->fetch_all(MYSQLI_ASSOC);

$clusters = [];

foreach ($duplicates as $dupe) {
	if(!isset($clusters[$dupe['duplicateid']])) {
		$clusters[$dupe['duplicateid']] = [];
	}

	if($dupe['collid'] === $collId)
	$clusters[$dupe['duplicateid']] = $dupe['collid'];
}

/*
$cleanManager = new OccurrenceCleaner();

if($collid) $cleanManager->setCollId($collid);
$collMap = current($cleanManager->getCollMap());

$dupArr =  $cleanManager->getDuplicateCatalogNumber('cat', $start, $limit);


foreach ($dupArr as $dupcluster) {
	foreach ($dupcluster as $occid => $value) {
		var_dump($occid, $value);
		echo '<br/><br/>';
	}
}*/

// -> queue processing search a thying then you go through each occid and process it

?>

<!DOCTYPE html>
<html lang="en">
	<?php include_once($SERVER_ROOT.'/includes/head.php') ;?>
	<body>
		<?php include_once($SERVER_ROOT.'/includes/header.php') ;?>

		<div role="main" id="innertext">

			<!-- TODO (Logan) import lang tags -->
			<?php breadcrumbs([
			'Home' => '../../index.php',
			'Collections Profile' => '../misc/collprofiles.php?emode=1&collid=' . $collid,
			'Duplicate Merger',
			])
			?>

			<h1>Place Holder Title</h1>

			<!-- <?php foreach ($occurrences as $key => $value): ?> -->
			<!-- <div> -->
			<!-- 	<?php echo $key ?> -->
			<!-- </div> -->
			<!-- <?php endforeach ?> -->

			<?php foreach ($duplicates as $dupCluster): ?>

			<div style="padding: 1rem; border: solid black 1px">
			<?php foreach ($dupcluster as $occid => $arr): ?>
			<div>
				<div style="display:flex; gap:0.5rem; align-items: center">
				<span> <input style="margin: 0" type="checkbox" name="" value=""> </span>
				<?php foreach ($arr as $key => $value): ?>

				<?php if($value): ?>
					<span>
					<?= $key . ' ' . $value ?>
					</span>
				<?php endif ?>
				<?php endforeach ?>
				</div>
			</div>
			<?php endforeach ?>
			</div>

			<?php endforeach ?>

			<br/>
		</div>

		<?php include_once($SERVER_ROOT.'/includes/footer.php') ;?>
	</body>
</html>
