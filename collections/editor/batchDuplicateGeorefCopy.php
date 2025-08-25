<?php
include_once('../../config/symbini.php');
global $SERVER_ROOT, $IS_ADMIN, $USER_RIGHTS, $CLIENT_ROOT, $LANG;
include_once($SERVER_ROOT . '/components/breadcrumbs.php');
include_once($SERVER_ROOT . '/classes/utilities/QueryUtil.php');
include_once($SERVER_ROOT . '/classes/utilities/UserUtil.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/Database.php');
include_once($SERVER_ROOT . '/classes/OccurrenceCleaner.php');
include_once($SERVER_ROOT . '/classes/OccurrenceEditorManager.php');
include_once($SERVER_ROOT . '/classes/Sanitize.php');
include_once($SERVER_ROOT . '/classes/CustomQuery.php');

$collId = array_key_exists('collid',$_REQUEST) && is_numeric($_REQUEST['collid'])? intval($_REQUEST['collid']):0;

UserUtil::isCollectionAdminOrDenyAcess($collId);
Language::load([
	'collections/sharedterms',
	'collections/misc/sharedterms',
	'collections/editor/batchDuplicateGeorefCopy',
	'collections/list'
]);

$start = array_key_exists('start',$_REQUEST)?$_REQUEST['start']:0;
$db = array_key_exists('db',$_REQUEST)?$_REQUEST['db']:[];
$hideExactMatches = array_key_exists('hideExactMatches',$_REQUEST);
$missingLatLng = array_key_exists('missingLatLng',$_REQUEST);

$updated = [];
$errors = [];

// Other none copied fields selected out of occurrence table
$fields = [
	'occid',
	'collid',
	'catalognumber',
	'country',
	'stateProvince',
	'county',
];

// Fields that will get copied into occurrence
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
	'georeferenceVerificationStatus',
];

// Fields that are shown in the table
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

function mapField($field, $prefix) {
	$tableAlias = 'o';
	$fieldAlias = ($prefix? ' AS ' . $field . $prefix: '');

	if($field == 'duplicateid') {
		$tableAlias = 'dl';
	}

	return $tableAlias . $prefix . '.' . $field . ($prefix? ' AS ' . $field . $prefix: '');
};

function getSqlFields(array $fields, string $prefix = '') {	
	$sql = '';

	for($i = 0; $i < count($fields); $i++) {
		$sql .= mapField($fields[$i], $prefix) . ($i < (count($fields) - 1)? ', ': '') ;
	}

	return $sql;
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

	$base_url = $GLOBALS['CLIENT_ROOT'] . '/collections/individual/index.php?occid=';
		
	foreach($shownFields as $key) {
		$value = $row[$key] ?? null;
		if($key === 'occid') {
			$html .= '<td><a target="_blank" href="'. $base_url . $value . '">' . $value . '</a></td>';
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

function getOccurrences(array $occIds, mysqli $conn) {
	global $fields, $harvestFields; 
	if(count($occIds) <= 0) return [];

	$parameters = str_repeat('?,', count($occIds) - 1) . '?';

	$sql = 'SELECT ' . getSqlFields($fields) . ',' .getSqlFields($harvestFields) . 
	' from omoccurrences o where occid in (' . $parameters . ')';

	$rs = QueryUtil::executeQuery($conn, $sql, $occIds);

	return $rs->fetch_all(MYSQLI_ASSOC);
}

function searchDuplicateOptions(int $targetCollId, int $page, mysqli $conn) {
	global $harvestFields, $hideExactMatches, $missingLatLng, $db;

	$sql = 'SELECT dl.duplicateid, o2.occid as targetOccid, o.occid from omoccurduplicatelink dl2
	join omoccurrences o2 on o2.occid = dl2.occid and o2.collid = ?
	join omoccurduplicatelink dl on dl.duplicateid = dl2.duplicateid
	join omoccurrences o on o.occid = dl.occid where o.occid != o2.occid';

	$parameters = [$targetCollId];

	if($hideExactMatches) {
		$oneHarvestableField = '';
		for($i = 0; $i < count($harvestFields); $i++) {
			$field = $harvestFields[$i];
			$oneHarvestableField .= 'o2.' . $field . ' != ' . 'o.' . $field;

			if($i < count($harvestFields) - 1) {
				$oneHarvestableField .= ' OR ';
			}
		}

		$sql .= ' AND (' . $oneHarvestableField . ')';
	}

	if($missingLatLng) {
		$sql .=	' AND (o2.decimalLongitude IS NULL OR o2.decimalLatitude IS NULL)';
	}

	if(count($db) > 0) {
		$sql .= '  AND o.collid in (' . str_repeat('?, ', count($db) -1 ) . '? ' . ')';
		$parameters = array_merge($parameters, $db);
	}

	$customWhere = CustomQuery::buildCustomWhere($_REQUEST, 'o');
	if($customWhere['sql']) {
		$sql .= ' AND (' . $customWhere['sql'] . ')';

		$parameters = array_merge($parameters, $customWhere['bindings']);
	}

	$sql .= ' LIMIT 100 OFFSET '. ($page ?? 0) * 100;

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
	global $errors, $harvestFields, $updated;
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

if(count($_POST)) copyInfo();

$conn = Database::connect('readonly');

$duplicates = searchDuplicateOptions($collId, $start, $conn);
$collections = getCollections($conn);

$paginateNext = count($duplicates) == 100;

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

			<?php breadcrumbs([
			$LANG['HOME'] => '../../index.php',
			$LANG['COL_MGMNT'] => '../misc/collprofiles.php?emode=1&collid=' . $collId,
			$LANG['BATCH_DUPLICATE_HARVESTER'],
			])
			?>

			<h1><?= $LANG['BATCH_DUPLICATE_HARVESTER'] ?></h1>

			<h2 style="margin-bottom: 0.5rem"><?= $LANG['DUPLICATE_SEARCH_CRITERIA'] ?></h2>
			<form method="POST" style="margin-bottom: 1rem;">
				<div style="margin-bottom: 1rem;">
					<?php CustomQuery::renderCustomInputs() ?>
				</div>
				<div>
					<input id="missingLatLng" type="checkbox" name="missingLatLng" value="1" <?= $missingLatLng? 'checked': ''?>>
					<label for="missingLatLng"><?= $LANG['MISSING_LAT_LNG'] ?></label>
				</div>

				<div style="margin-bottom: 1rem;">
					<input id="hideExactMatches" type="checkbox" name="hideExactMatches" value="1" <?= $hideExactMatches? 'checked': ''?>>
					<label for="hideExactMatches"><?= $LANG['HIDE_EXACT_MATCHES'] ?></label>
				</div>

				<dialog id="collections_dialog" style="min-width: 900px;">
					<div style="display:flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
						<h1 style="margin:0;"><?= $LANG['NAV_COLLECTIONS'] ?></h1>
						<div style="flex-grow: 1;">
						<button class="button" style="margin-left: auto;" type="button" onclick="document.getElementById('collections_dialog').close()"><?= $LANG['CLOSE'] ?></button>
						</div>
					</div>
					<?php include(__DIR__ . '/includes/collectionForm.php') ?>
				</dialog>

				<button style="margin-bottom:1rem" class="button" type="button" onclick="document.getElementById('collections_dialog').showModal()"><?= $LANG['FILTER_COLLECTIONS'] ?></button>
				<button class="button"><?= $LANG['SEARCH'] ?></button>

			</form>


			<?php foreach($errors as $duplicateId => $error): ?>
			<div style="margin-bottom:0.5rem">
<?= 'ERROR: ' . $error ?>
			</div>
			<?php endforeach ?>

			<?php foreach($updated as $occId): ?>
			<div style="margin-bottom:0.5rem">
			<?= 'Updated Record ' ?>
			<a href="<?= $CLIENT_ROOT . '/collections/individual/index.php?occid=' . $occId ?>" >
				<?= '#' . $occId ?>
			</a>
			</div>
			<?php endforeach ?>

			<div style="margin-bottom: 1rem; display: flex; gap: 1rem;">
				<?php if($start != 0): ?>
					<a href="?collid=<?= $collId?>&start=<?= $start - 1 ?>"><?= $LANG['PAGINATION_NEXT'] ?></a>
				<?php endif ?>

				<?php if($paginateNext): ?>
					<a href="?collid=<?= $collId?>&start=<?= $start + 1 ?>"><?= $LANG['PAGINATION_PREVIOUS'] ?></a>
				<?php endif ?>

				<!-- <div style="flex-grow: 1; display: flex; justify-content: end;"> -->
				<!-- 	<?= (($start * 100) + 1) . '-' . (($start * 100) + count($duplicates)) . ' duplicates'?> -->
				<!-- </div> -->
			</div>

			<form method="POST">
			<?php if(count($targets)): ?>
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

			<?php else: ?>
				<h4 style="margin-bottom: 1rem; padding:1rem 0;">
					<?= $LANG['NO_DUPLICATES'] ?>
				</h4>
			<?php endif ?>

			<button class="button"><?= $LANG['COPY_DUPLICATE_DATA'] ?></button>
			<p>
				<?= $LANG['COPY_DUPLICATE_DATA_EXPLANATION'] ?>
			</p>
			</form>
			<br/>
		</div>

		<?php include_once($SERVER_ROOT.'/includes/footer.php') ;?>
	</body>
</html>
