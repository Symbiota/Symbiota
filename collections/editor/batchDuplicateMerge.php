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
	'occid',
	'collid',
	'catalognumber',
	// 'duplicateid',
	'country',
	'stateProvince',
	'county',
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

$shownFields = [
	'occid',
	'catalognumber',
	'institutionCode',
	'collectionCode',
	'country',
	'stateProvince',
	'county',
	...$harvestFields,
];

// Don't show in ui table
$fieldIgnores = [
	'collid',
	'duplicateid',
];

function getSqlFields(array $fields, string $prefix = '') {	
	$sql = '';

	for($i = 0; $i < count($fields); $i++) {
		$sql .= mapField($fields[$i], $prefix) . ($i < (count($fields) - 1)? ', ': '') ;
	}

	return $sql;
}

function mapField($field, $prefix) {
	$tableAlias = 'o';
	$fieldAlias = ($prefix? ' AS ' . $field . $prefix: '');

	if($field == 'duplicateid') {
		$tableAlias = 'dl';
	}

	return $tableAlias . $prefix . '.' . $field . ($prefix? ' AS ' . $field . $prefix: '');
};

function getTargets(int $collId) {
	global $fields, $harvestFields;

	$sql = 'SELECT DISTINCT duplicateid from omoccurduplicatelink dl
	join omoccurrences o on o.occid = dl.occid and o.collid = ?
	ORDER BY duplicateid LIMIT 100';

	$conn = Database::connect('readonly');
	$rs = QueryUtil::executeQuery($conn, $sql, [$collId]);
	return array_map(fn($v) => $v['duplicateid'], $rs->fetch_all(MYSQLI_ASSOC));
}

function getTableHeaders(array $arr, array $ignore = []) {
	$html = '<thead>';
	$html .= '<th></th>';

	foreach($arr as $key) {
		if(in_array($key, $ignore)) {
			continue;
		} else {
			$html .= '<th>' . $key . '</th>';
		}
	}

	$html .= '</thead>';

	return $html;
}

function render_row($row, $checkboxName = false, $shownFields = []) {
	$html = '<tr>';
	$html .= '<td><div style="display:flex; align-items:center; justify-content: center;">' . 
		($checkboxName ? '<input type="checkbox" onclick="checkbox_one_only(this)" name="'. $checkboxName  .'" value="' . $row['occid'] . '" style="margin:0"/>': '') . 
		'</div></td>';

	$base_url = ($GLOBALS['CLIENT_ROOT']? $GLOBALS['CLIENT_ROOT'] . '/': '') . 
	'/collections/individual/index.php?occid=';
		

	foreach($shownFields as $key) {
		$value = $row[$key] ?? null;
		if($key === 'occid') {
			$html .= '<td><a href="'. $base_url . $value . '">' . $value . '</a></td>';
		}  else  {
			$html .= '<td>' . $value . '</td>';
		}
	}

	// foreach ($row as $key => $value) {
	// 	if(in_array($key, $fieldIgnores)) {
	// 		continue;
	// 	} else if($key === 'occid') {
	// 		$html .= '<td><a href="'. $base_url . $value . '">' . $value . '</a></td>';
	// 	}  else  {
	// 		$html .= '<td>' . $value . '</td>';
	// 	}
	// }

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

function getShownDuplicate(array $occurrenceDuplicate, string $postfix = '') {
	global $shownFields;

	$shownDuplicate = [];
	foreach($shownFields as $field) {
		$shownDuplicate[$field] = $occurrenceDuplicate[$field . $postfix] ?? null;
	}

	return $shownDuplicate;
}

function getOccurrences(array $occIds, mysqli $conn) {
	global $fields, $harvestFields; 

	$parameters = str_repeat('?,', count($occIds) - 1) . '?';

	$sql = 'SELECT ' . getSqlFields($fields) . ',' .getSqlFields($harvestFields) . 
	' from omoccurrences o where occid in (' . $parameters . ')';

	$rs = QueryUtil::executeQuery($conn, $sql, $occIds);

	return $rs->fetch_all(MYSQLI_ASSOC);
}

function searchDuplicateOptions(int $targetCollId, mysqli $conn) {
	global $harvestFields;

	$sql = 'SELECT dl.duplicateid, o2.occid as targetOccid, o.occid from omoccurduplicatelink dl2
	join omoccurrences o2 on o2.occid = dl2.occid and o2.collid = ?
	join omoccurduplicatelink dl on dl.duplicateid = dl2.duplicateid
	join omoccurrences o on o.occid = dl.occid where o.occid != o2.occid';

	$oneHarvestableField = '';

	for($i = 0; $i < count($harvestFields); $i++) {
		$field = $harvestFields[$i];
		$oneHarvestableField .= 'o2.' . $field . ' != ' . 'o.' . $field;

		if($i < count($harvestFields) - 1) {
			$oneHarvestableField .= ' OR ';
		}
	}

	$sql .= ' AND (' . $oneHarvestableField . ')';

	$parameters = [$targetCollId];

	$customWhere = CustomQuery::buildCustomWhere($_REQUEST, 'o');
	if($customWhere['sql']) {
		$sql .= ' AND (' . $customWhere['sql'] . ')';

		$parameters = array_merge($parameters, $customWhere['bindings']);
	}

	$sql .= ' LIMIT 100';
	$rs = QueryUtil::executeQuery($conn, $sql, $parameters);

	return $rs->fetch_all(MYSQLI_ASSOC);
}

function getCollections(mysqli $conn) {
	$rs = QueryUtil::executeQuery($conn, 'SELECT collid, collectionCode, institutionCode from omcollections', []);
	$collections = [];
	foreach($rs->fetch_all(MYSQLI_ASSOC) as $row) {
		$collections[$row['collid']] = $row;
	}	

	return $collections;
}

function copyInfo() {
	global $errors, $harvestFields;
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
}

function hasDiff(array $duplicateA, array $duplicateB) {
	global $harvestFields;
	foreach($harvestFields as $key) {
		$valueB = $duplicateB[$key] ?? null;
		$valueA = $duplicateA[$key] ?? null;

		if($valueA != $valueB) {
			return true;
		}
	}

	return false;
}

$conn = Database::connect('readonly');

$duplicates = searchDuplicateOptions($collid, $conn);
$collections = getCollections($conn);

$targets  = [];
$options = [];

$targetOccids = [];
$optionOccids = [];

foreach ($duplicates as $dupe) {
	$occid = $dupe['occid'];
	$targetOccid= $dupe['targetOccid'];
	$duplicateId = $dupe['duplicateid'];

	if(!isset($targets[$targetOccid])) {
		$targets[$targetOccid] = $duplicateId;
		$targetOccids[] = $targetOccid;
	}

	if(!isset($options[$duplicateId])) {
		$options[$duplicateId] = [];
	}

	if(!isset($options[$duplicateId][$occid])) {
		$options[$duplicateId][$occid] = [];
		$optionOccids[$occid] = $duplicateId;
	}
}

foreach (getOccurrences($targetOccids, $conn) as $target) {
	$target['duplicateid'] = $targets[$target['occid']]; 
	$target['institutionCode'] = $collections[$target['collid']]['institutionCode'];
	$target['collectionCode'] = $collections[$target['collid']]['collectionCode'];
	$targets[$target['occid']] =  $target;
}

foreach (getOccurrences(array_keys($optionOccids), $conn) as $option) {
	$occid = $option['occid'];
	$duplicateId = $optionOccids[$occid];
	$option['duplicateid'] = $duplicateId;
	$option['institutionCode'] = $collections[$option['collid']]['institutionCode'];
	$option['collectionCode'] = $collections[$option['collid']]['collectionCode'];
	$options[$duplicateId][$occid] = $option;
}

//var_dump($targetsOccurrneces, $optionsOccurrences);


// foreach ($duplicates as $dupe) {
// 	if(!isset($options[$dupe['duplicateid']])) {
// 		$options[$dupe['duplicateid']] = [];
// 	}
//
// 	if($dupe['collid2'] === $collid) {
// 		$targets[$dupe['occid2']] = getShownDuplicate($dupe, '2');
// 		$targets[$dupe['occid2']]['institutionCode'] = $collections[$dupe['collid2']]['institutionCode'];
// 		$targets[$dupe['occid2']]['collectionCode'] = $collections[$dupe['collid2']]['collectionCode'];
// 		$targets[$dupe['occid2']]['duplicateid'] = $dupe['duplicateid2'];
// 	}
//
// 	$shownDupe = getShownDuplicate($dupe);
// 	$shownDupe['duplicateid'] = $dupe['duplicateid'];
// 	$shownDupe['institutionCode'] = $collections[$dupe['collid']]['institutionCode'];
// 	$shownDupe['collectionCode'] = $collections[$dupe['collid']]['collectionCode'];
// 	$options[$dupe['duplicateid']][$dupe['occid']] = $shownDupe;
// }

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
				<?= getTableHeaders($shownFields, $fieldIgnores)?>
				<tbody>
					<?= render_row($target, false, $shownFields) ?>
					<?php foreach ($options[$duplicateId] as $dupe): ?>
						<?= $dupe['occid'] !== $target['occid']? render_row($dupe, $target['occid'], $shownFields): '' ?>
					<?php endforeach ?>
				</tbody>
			</table>
			<?php endif ?>
			<?php endforeach ?>

			<?php elseif($ui_option === 2): ?>

			<table class="styledtable table-scroll">
			<?= getTableHeaders($shownFields, $fieldIgnores) ?>
			<?php foreach ($targets as $targetOccid => $target): ?>
			<?php if(count($options[$target['duplicateid']])): ?>
				<tbody>
					<?= render_row($target, false, $shownFields) ?>
					<?php foreach ($options[$target['duplicateid']] as $dupeOccid => $dupe): ?>
						<?= $dupeOccid !== $targetOccid? render_row($dupe, $targetOccid, $shownFields): '' ?>
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
