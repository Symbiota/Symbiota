<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT . '/components/breadcrumbs.php');
include_once($SERVER_ROOT . '/classes/utilities/QueryUtil.php');
include_once($SERVER_ROOT . '/classes/Database.php');
include_once($SERVER_ROOT . '/classes/OccurrenceCleaner.php');
include_once($SERVER_ROOT . '/classes/OccurrenceEditorManager.php');
include_once($SERVER_ROOT . '/classes/Sanitize.php');
include_once($SERVER_ROOT . '/classes/CustomQuery.php');

$collid = array_key_exists('collid',$_REQUEST) && is_numeric($_REQUEST['collid'])? intval($_REQUEST['collid']):0;
$start = array_key_exists('start',$_REQUEST)?$_REQUEST['start']:0;
$limit = array_key_exists('limit',$_REQUEST)?$_REQUEST['limit']:1000;

$updated = [];
$errors = [];

$fields = [
	'dl.occid',
	'o.catalognumber', 
	'"" as institutionCode',
	'"" as collectionCode',
	'dl.duplicateid', 
	'o.collid',
	'o.country',
	'o.stateProvince',
	'o.county',
];

$harvestFields = [
	'o.decimalLatitude',
	'o.decimalLongitude',
	'o.geodeticDatum',
	'o.footprintWKT',
	'o.coordinateUncertaintyInMeters',
	'o.georeferencedBy',
	'o.georeferenceRemarks',
	'o.georeferenceSources',
	'o.georeferenceProtocol',
	//'georeferenceDate',
	'o.georeferenceVerificationStatus',
];

// Don't show in ui table
$fieldIgnores = [
	'collid',
	'duplicateid',
];

foreach($_POST as $targetOccId => $sourceOccId) {
	if(is_numeric($targetOccId) && is_numeric($sourceOccId)) {
		try {
			copyOccurrenceInfo($targetOccId, $sourceOccId, $harvestFields);
			array_push($updated, $targetOccId);
		} catch(Exception $e)  {
			$errors[$targetOccId] = $e->getMessage();
		}
	}
}

$sql = 'SELECT DISTINCT ' . implode(',', $fields) . ',' . implode(',', $harvestFields) . ' from omoccurduplicatelink dlc
	join omoccurrences o2 on o2.occid = dlc.occid and o2.collid = ?
	join omoccurduplicatelink dl on dl.duplicateid = dlc.duplicateid
	join omoccurrences o on o.occid = dl.occid';

$conn = Database::connect('readonly');
$rs = QueryUtil::executeQuery($conn, $sql, [$collid]);
$duplicates = $rs->fetch_all(MYSQLI_ASSOC);

$rs = QueryUtil::executeQuery($conn, 'SELECT collid, collectionCode, institutionCode from omcollections', []);
$collections = [];
foreach($rs->fetch_all(MYSQLI_ASSOC) as $row) {
	$collections[$row['collid']] = $row;
}

$targets  = [];
$options = [];

foreach ($duplicates as $dupe) {
	if(!isset($options[$dupe['duplicateid']])) {
		$options[$dupe['duplicateid']] = [];
	}

	$collection = $collections[$dupe['collid']];
	$dupe['institutionCode'] = $collection['institutionCode'];
	$dupe['collectionCode'] = $collection['collectionCode'];

	if($dupe['collid'] === $collid) {
		$targets[$dupe['duplicateid']] = $dupe;
	}

	$options[$dupe['duplicateid']][] = $dupe;
}

function getTableHeaders(array $arr, array $ignore = []) {
	$html = '<thead>';
	$html .= '<th></th>';

	foreach($arr as $key => $value) {
		if(in_array($key, $ignore)) {
			continue;
		} else {
			$html .= '<th>' . $key . '</th>';
		}
	}

	$html .= '</thead>';

	return $html;
}

function render_row($row, $checkboxName = false, $fieldIgnores = []) {
	$html = '<tr>';
	$html .= '<td><div style="display:flex; align-items:center; justify-content: center;">' . 
		($checkboxName ? '<input type="checkbox" onclick="checkbox_one_only(this)" name="'. $checkboxName  .'" value="' . $row['occid'] . '" style="margin:0"/>': '') . 
		'</div></td>';

	$base_url = ($GLOBALS['CLIENT_ROOT']? $GLOBALS['CLIENT_ROOT'] . '/': '') . 
	'/collections/individual/index.php?occid=';
		
	foreach ($row as $key => $value) {
		if(in_array($key, $fieldIgnores)) {
			continue;
		} else if($key === 'occid') {
			$html .= '<td><a href="'. $base_url . $value . '">' . $value . '</a></td>';
		}  else  {
			$html .= '<td>' . $value . '</td>';
		}
	}

	return $html .=  '</tr>';
}

function copyOccurrenceInfo($targetOccId, $sourceOccId, $harvestFields) {
	$sql = 'Update omoccurrences target 
		INNER JOIN omoccurrences source on target.occid = ? and source.occid = ? 
		SET ';

	$count = 0;
	$maxCount = count($harvestFields);
	foreach ($harvestFields as $field) {
		$sql .= 'target.' . $field . '  =  source.'  . $field;
		if(++$count < $maxCount) {
			$sql .= ', ';
		}
	}

	QueryUtil::executeQuery(
		Database::connect('write'),
		$sql,
		[$targetOccId, $sourceOccId]
	);
}

function isCopyable($target, $source) {
	global $harvestFields;
	foreach ($harvestFields as $field) {
		if(($target[$field] ?? false) != ($source[$field] ?? false)) {
			return true;
		}
	}
}

$ui_option = 2;

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

			<form method="POST" style="margin-bottom: 1rem;">

				<div style="margin-bottom: 1rem;">
					<?php CustomQuery::renderCustomInputs() ?>
				</div>
				<button class="button">Submit</button>
			</form>


			<?php foreach($errors as $duplicateId => $error): ?>
			<div style="margin-bottom:0.5rem">
<?= 'ERROR: ' . $error ?>
			</div>
			<?php endforeach ?>

			<?php foreach($updated as $occId): ?>
			<div style="margin-bottom:0.5rem">
			<?= 'Updated Record ' ?>
			<a href="<?= ($CLIENT_ROOT? '/' . $CLIENT_ROOT: '' ) . '/collections/individual/index.php?occid=' . $occId ?>" >
				<?= '#' . $occId ?>
			</a>
			</div>
			<?php endforeach ?>

			<form method="POST">
			<?php if($ui_option === 1): ?>

			<?php foreach ($targets as $duplicateId => $target): ?>
			
			<?php if(count($options[$duplicateId])): ?>
			<table class="styledtable table-scroll">
				<?php echo getTableHeaders($target, $fieldIgnores) ?>
				<tbody>
					<?= render_row($target, false, $fieldIgnores) ?>
					<?php foreach ($options[$duplicateId] as $dupe): ?>
						<?= $dupe['occid'] !== $target['occid']? render_row($dupe, $target['occid'], $fieldIgnores): '' ?>
					<?php endforeach ?>
				</tbody>
			</table>
			<?php endif ?>
			<?php endforeach ?>

			<?php elseif($ui_option === 2): ?>

			<table class="styledtable table-scroll">
			<?php echo getTableHeaders(current($targets), $fieldIgnores) ?>
			<?php foreach ($targets as $duplicateId => $target): ?>
			<?php if(count($options[$duplicateId])): ?>
				<tbody>
					<?= render_row($target, false, $fieldIgnores) ?>
					<?php foreach ($options[$duplicateId] as $dupe): ?>
						<?= $dupe['occid'] !== $target['occid']? render_row($dupe, $target['occid'], $fieldIgnores): '' ?>
					<?php endforeach ?>
					<tr>
						<td colspan="18" style="height: 1rem"></td>
					</tr>
				</tbody>
			<?php endif ?>
			<?php endforeach ?>
			</table>

			<?php endif ?>

			<button class="button">Copy Duplicate Data</button>
			</form>
			<br/>
		</div>

		<?php include_once($SERVER_ROOT.'/includes/footer.php') ;?>
	</body>
</html>
