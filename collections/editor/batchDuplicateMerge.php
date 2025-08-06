<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT . '/components/breadcrumbs.php');
include_once($SERVER_ROOT . '/classes/utilities/QueryUtil.php');
include_once($SERVER_ROOT . '/classes/Database.php');
include_once($SERVER_ROOT . '/classes/OccurrenceCleaner.php');
include_once($SERVER_ROOT . '/classes/OccurrenceEditorManager.php');

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$start = array_key_exists('start',$_REQUEST)?$_REQUEST['start']:0;
$limit = array_key_exists('limit',$_REQUEST)?$_REQUEST['limit']:1000;

$collid = 2;

$fields = [
	'o.occid', 
	'o.catalognumber', 
	'dl.duplicateid', 
	'o.collid',
	'country',
	'stateProvince',
	'county',
	'c.institutionCode',
	'c.collectionCode',
];

$harvestFields = [
	'decimalLatitude',
	'decimalLongitude',
	'geodeticDatum',
	'footprintWKT',
	'coordinateUncertaintyInMeters',
	'georeferencedBy',
	'georeferenceRemarks',
	'georeferenceSources',
	'georeferenceProtocol',
	//'georeferenceDate',
	'georeferenceVerificationStatus',
];


$sql = 'SELECT ' . implode(',', $fields) . ',' . implode(',', $harvestFields) . ' from omoccurduplicatelink dl
	join omoccurrences o on o.occid = dl.occid
	join omcollections  c on c.collid = o.collid
	where dl.duplicateid in (
		SELECT duplicateid from omoccurduplicatelink dl 
		join omoccurrences o on o.occid = dl.occid 
		where collid = ?
	)';

$conn = Database::connect('readonly');
$rs = QueryUtil::executeQuery($conn, $sql, [$collid]);
$duplicates = $rs->fetch_all(MYSQLI_ASSOC);

$targets  = [];
$options = [];

foreach ($duplicates as $dupe) {
	if(!isset($options[$dupe['duplicateid']])) {
		$options[$dupe['duplicateid']] = [];
	}

	if($dupe['collid'] === $collid) {
		$targets[$dupe['duplicateid']] = $dupe;
	}

	$options[$dupe['duplicateid']][] = $dupe;
}

function getTableHeaders(array $arr) {
	$html = '<thead>';
	$html .= '<th></th>';

	foreach($arr as $key => $value) {
		$html .= '<th>' . $key . '</th>';
	}

	$html .= '</thead>';

	return $html;
}

function render_row($row, $checkboxName = false) {
	$html = '<tr>';
	$html .= '<td><div style="display:flex; align-items:center; justify-content: center;">' . 
		($checkboxName ? '<input type="checkbox" onclick="checkbox_one_only(this)" name="'. $checkboxName  .'" value="' . $row['occid'] . '" style="margin:0"/>': '') . 
		'</div></td>';
		
	foreach ($row as $key => $value) {
		if($key === 'occid') {
			$html .= '<td><a href="#">' . $value . '</a></td>';
		}  else  {
			$html .= '<td>' . $value . '</td>';
		}
	}

	return $html .=  '</tr>';
}

function copyOccurrenceInfo($targetOccId, $sourceOccId) {
	$sql = 'Update omoccurrences target 
		INNER JOIN omoccurrences source on target.occid = ? and source.occid = ? 
		SET target.country = COALESCE(source.country, target.country)';

	foreach ($harvestFields as $field) {
		$sql .= 'target.' . $field . '  =  COALESCE(source.'  . $field . ', target.'. $field . ')';
	}

	QueryUtil::executeQuery(
		Database::connect('write'), 
		$sql, 
		[$targetOccId, $sourceOccId]
	);
}
$target_occids = $_POST['targets'] ?? [];
$sources = $_POST['sources'] ?? [];

if(count($target_occids) && count($sources) && count($sources) === count($target_occids)) {
	for ($i = 0; $i  < count($sources); $i ++) { 
		# code...
	}
}

$ui_option = 1

?>

<!DOCTYPE html>
<html lang="en">
	<head>
	<?php include_once($SERVER_ROOT.'/includes/head.php') ;?>

	<style tyle="text/css">
		.table-scroll {
			display: block;
			white-space: nowrap;
			overflow-x: scroll;
			overflow-y: scroll;
			max-height: 80vh;
			padding-bottom: 0.5rem;
			margin-bottom: 1.2rem;
		}

		tbody tr:first-child td {
			background-color: #CCC
		}

		#record-viewer-innertext { 
			margin-left: 2em;
			width: calc(100vw - 4em);
		}
	</style>
		<script type="text/javascript">
		function checkbox_one_only(input) {
			//console.log(input.checked, e.target.checked)
			const checked_elements = document.querySelectorAll(`input[name='${input.name}']:checked`);
			for(let elem  of checked_elements) {
				if(elem.value !=  input.value) {
					elem.checked = false;
				}
			}
		}
		</script>
	</head>

	<body>
		<?php include_once($SERVER_ROOT.'/includes/header.php') ;?>

		<div role="main" id="record-viewer-innertext">

			<!-- TODO (Logan) import lang tags -->
			<?php /* breadcrumbs([
			'Home' => '../../index.php',
			'Collections Profile' => '../misc/collprofiles.php?emode=1&collid=' . $collid,
			'Duplicate Merger',
			]) */
			?>

			<h1>Duplicate Data Harvester</h1>

			<form>
				<div style="width:100%; height:150px; background-color: #ccc; margin-bottom: 1rem;">
					<div style="display:flex; justify-content:center; align-items: center;  height: 100%;">
						Place Holder Form
					</div>
				</div>
			</form>

			<form method="POST">
			<?php if($ui_option === 1): ?>

			<?php foreach ($targets as $duplicateId => $target): ?>
			
			<?php if(count($options[$duplicateId])): ?>
			<table class="styledtable table-scroll">
				<?php echo getTableHeaders($target) ?>
				<tbody>
					<?= render_row($target) ?>
					<?php foreach ($options[$duplicateId] as $dupe): ?>
						<?= $dupe['occid'] !== $target['occid']? render_row($dupe, $duplicateId): '' ?>
					<?php endforeach ?>
				</tbody>
			</table>
			<?php endif ?>
			<?php endforeach ?>

			<?php elseif($ui_option === 2): ?>

			<?php endif ?>

			<button class="button">Copy Duplicate Data</button>
			</form>
			<br/>
		</div>

		<?php include_once($SERVER_ROOT.'/includes/footer.php') ;?>
	</body>
</html>
