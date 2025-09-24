<?php
include_once('../../config/symbini.php');
global $SERVER_ROOT, $IS_ADMIN, $USER_RIGHTS, $CLIENT_ROOT, $LANG;
include_once($SERVER_ROOT . '/components/breadcrumbs.php');
include_once($SERVER_ROOT . '/classes/utilities/QueryUtil.php');
include_once($SERVER_ROOT . '/classes/utilities/UserUtil.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/OccurrenceUtil.php');
include_once($SERVER_ROOT . '/classes/Database.php');
include_once($SERVER_ROOT . '/classes/OccurrenceCleaner.php');
include_once($SERVER_ROOT . '/classes/OccurrenceEditorManager.php');
include_once($SERVER_ROOT . '/classes/Sanitize.php');
include_once($SERVER_ROOT . '/classes/CustomQuery.php');

// Other none copied fields selected out of occurrence table
$fields = [
	'occid',
	'collid',
	'catalognumber',
	'recordedBy',
	'recordNumber',
	'country',
	'stateProvince',
	'county',
	'locality',
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
	'recordedBy',
	'recordNumber',
	...$harvestFields,
	'country',
	'stateProvince',
	'county',
	'locality',
];

$fieldDisplayAlias = [
	'recordedBy' => 'collector',
	'recordNumber' => 'collector number',
];

// Don't show in ui table
$fieldIgnores = [
	'collid',
	'duplicateid',
];

$updated = [];
$errors = [];
$hiddenFields = [];

foreach($shownFields as $field) {
	if(array_key_exists('hide_' . $field, $_REQUEST)) {
		array_push($hiddenFields, $field);
	}
}

function copyOccurrenceInfo($targetOccId, $sourceOccId, $harvestFields) {
	$sql = 'Update omoccurrences target
		INNER JOIN omoccurrences source on target.occid = ? and source.occid = ?
		INNER JOIN omcollections c on c.collid = source.collid
		SET ';

	$count = 0;
	$maxCount = count($harvestFields);
	foreach ($harvestFields as $field) {
		if($field === 'georeferenceRemarks') {
			$sql .= 'target.' . $field . ' = TRIM(CONCAT("Copied from duplicate ", c.institutionCode, " ", COALESCE(source.catalogNumber, source.otherCatalogNumbers, source.occid), " ", COALESCE(source.'  . $field .', "")))';
		} else {

			$sql .= 'target.' . $field . ' = source.'  . $field;
		}

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

if(array_key_exists('copyInfo', $_POST)) {
	copyInfo();
	 $_REQUEST = $_SESSION['batchDuplicateGeorefCopyRequest'];
} else {
	//Assume Search
	$_SESSION['batchDuplicateGeorefCopyRequest'] = $_REQUEST;
}

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
$missingLatLng = array_key_exists('missingLatLng',$_REQUEST);
$hideExactMatches = array_key_exists('hideExactMatches',$_REQUEST);

$autoCheckSingleOptions = false;
if(array_key_exists('autoCheckSingleOptions',$_REQUEST)) {
	$autoCheckSingleOptions = true;
} else if(array_key_exists('searchDuplicates',$_REQUEST)) {
	$autoCheckSingleOptions = false;
}

$missingLatLng = true;
if(array_key_exists('missingLatLng',$_REQUEST)) {
	$missingLatLng = true;
} else if(array_key_exists('searchDuplicates',$_REQUEST)) {
	$missingLatLng = false;
}

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

function getTableHeaders(array $arr, array $ignore = [], array $alias = []) {
	$html = '<thead>';
	$html .= '<th></th>';

	foreach($arr as $key) {
		if(in_array($key, $ignore)) {
			continue;
		} else {
			$hide = array_key_exists('hide_' . $key, $_REQUEST);
			$html .= '<th data-key="'. $key .'" ' . ($hide? 'style="display:none"':'') . '>' . ($alias[$key] ?? $key) . '</th>';
		}
	}

	$html .= '</thead>';

	return $html;
}

function render_row($row, $checkboxName = false, $shownFields = [], $onlyOption = false) {
	$html = '<tr><td><div style="display:flex; align-items:center; justify-content: center;">';
	if($checkboxName) {
		$html .= '<input type="checkbox" onclick="checkbox_one_only(this)" name="' . $checkboxName . '" value="' . $row['occid'] . '" style="margin:0" ' . ($onlyOption? 'checked': '') . '/>';
	}

	$html .= '</div></td>';

	$base_url = $GLOBALS['CLIENT_ROOT'] . '/collections/individual/index.php?occid=';
		
	foreach($shownFields as $key) {
		$value = $row[$key] ?? null;
		$hide = array_key_exists('hide_' . $key, $_REQUEST);

		if($key === 'occid') {
			$html .= '<td data-key="'. $key .'" ' . ($hide? 'style="display:none"':'') . '><a target="_blank" href="'. $base_url . $value . '">' . $value . '</a></td>';
		}  else  {
			$html .= '<td data-key="'. $key .'" ' . ($hide? 'style="display:none"':'') . '>' . $value . '</td>';
		}
	}

	return $html .=  '</tr>';
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
	join omoccurrences o on o.occid = dl.occid where o.occid != o2.occid ' .
	OccurrenceUtil::appendFullProtectionSQL();

	// Don't process records with hidden locality information
	$sql .= ' AND o.recordSecurity != 1 ';

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

$conn = Database::connect('readonly');

$duplicates = array_key_exists('searchDuplicates', $_REQUEST)? searchDuplicateOptions($collId, $start, $conn): [];
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

function getUniqueOptionCount($options, $targetOccid) {
	$count = 0;
	foreach($options as $dupeOccid => $dup) {
		if($dupeOccid !== $targetOccid) {
			$count++;
		}
	}

	return $count;
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
			<div style="max-width:50rem">
			<p>
				<?= $LANG['MUST_BATCH_LINK_DUPLICATES'] ?>
			</p>
			</div>
			<h2 style="margin-bottom: 0.5rem"><?= $LANG['DUPLICATE_SEARCH_CRITERIA'] ?></h2>
			<form method="POST" style="margin-bottom: 1rem;">
				<div style="margin-bottom: 1rem;">
					<?php CustomQuery::renderCustomInputs() ?>
				</div>
				<div>
					<input id="missingLatLng" type="checkbox" name="missingLatLng" value="1" <?= $missingLatLng? 'checked': ''?>>
					<label for="missingLatLng"><?= $LANG['MISSING_LAT_LNG'] ?></label>
				</div>

				<div>
					<input id="hideExactMatches" type="checkbox" name="hideExactMatches" value="1" <?= $hideExactMatches? 'checked': ''?>>
					<label for="hideExactMatches"><?= $LANG['HIDE_EXACT_MATCHES'] ?></label>
				</div>

				<div style="margin-bottom: 1rem;">
					<input id="autoCheckSingleOptions" type="checkbox" name="autoCheckSingleOptions" value="1" <?= $autoCheckSingleOptions? 'checked': ''?>>
					<label for="autoCheckSingleOptions"><?= $LANG['ENABLE_AUTO_CHECK'] ?></label>
				</div>

				<div style="display:flex; gap:1rem;margin-bottom:1rem;">
					<dialog id="table_toggle_dialog" style="min-width: 900px;">
						<div style="display:flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
							<h1 style="margin:0;"><?= 'Hide Displayed Fields' ?></h1>
							<div style="flex-grow: 1;">
							<button class="button" style="margin-left: auto;" type="button" onclick="document.getElementById('table_toggle_dialog').close()"><?= $LANG['CLOSE'] ?></button>
							</div>
						</div>

						<?php foreach ($shownFields as $field):?>
						<div>
							<input id="hide_<?= $field ?>" type="checkbox" name="hide_<?= $field ?>" <?= array_key_exists('hide_' . $field, $_REQUEST)? 'checked': ''?> onclick="document.querySelectorAll('[data-key=<?= $field ?>]').forEach(el => el.style.display = this.checked? 'none': '')">
							<label for="hide_<?= $field ?>" ><?= ($fieldDisplayAlias[$field] ?? $field) ?></label>
						</div>
						<?php endforeach ?>
					</dialog>
					<input type="hidden" name="searchDuplicates" value="1"/>
					<button class="button" type="button" onclick="document.getElementById('table_toggle_dialog').showModal()"><?= 'Hide Fields' ?></button>

					<dialog id="collections_dialog" style="min-width: 900px;">
						<div style="display:flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
							<h1 style="margin:0;"><?= $LANG['NAV_COLLECTIONS'] ?></h1>
							<div style="flex-grow: 1;">
							<button class="button" style="margin-left: auto;" type="button" onclick="document.getElementById('collections_dialog').close()"><?= $LANG['CLOSE'] ?></button>
							</div>
						</div>
						<?php include(__DIR__ . '/includes/collectionForm.php') ?>
					</dialog>
					<button class="button" type="button" onclick="document.getElementById('collections_dialog').showModal()"><?= $LANG['FILTER_COLLECTIONS'] ?></button>
					<button class="button"><?= $LANG['SEARCH'] ?></button>
				</div>

				<input type="hidden" name="searchDuplicates" value="1"/>
			</form>


			<?php foreach($errors as $duplicateId => $error): ?>
			<div style="margin-bottom:0.5rem">
<?= 'ERROR: ' . $error ?>
			</div>
			<?php endforeach ?>

			<?php foreach($updated as $occId): ?>
			<div style="margin-bottom:0.5rem">
			<?= 'Updated Record ' ?>
			<a href="<?= $CLIENT_ROOT . '/collections/individual/index.php?occid=' . $occId ?>" target="_blank">
				<?= '#' . $occId ?>
			</a>
			</div>
			<?php endforeach ?>

			<div style="margin-bottom: 1rem; display: flex; gap: 1rem;">
				<?php if($start != 0): ?>
					<a href="?collid=<?= $collId?>&start=<?= $start - 1 ?>"><?= $LANG['PAGINATION_PREVIOUS'] ?></a>
				<?php endif ?>

				<?php if($paginateNext): ?>
					<a href="?collid=<?= $collId?>&start=<?= $start + 1 ?>"><?= $LANG['PAGINATION_NEXT'] ?></a>
				<?php endif ?>

				<!-- <div style="flex-grow: 1; display: flex; justify-content: end;"> -->
				<!-- 	<?= (($start * 100) + 1) . '-' . (($start * 100) + count($duplicates)) . ' duplicates'?> -->
				<!-- </div> -->
			</div>

			<form method="POST">
			<?php if(count($targets)): ?>
			<table class="styledtable table-scroll">
			<?= getTableHeaders($shownFields, $fieldIgnores, $fieldDisplayAlias) ?>

			<?php foreach ($targets as $targetOccid => $target): ?>
			<?php if($optionCount = getUniqueOptionCount($options[$target['duplicateid']], $targetOccid)): ?>
				<tbody>
					<?= render_row($target, false, $shownFields) ?>
					<?php foreach ($options[$target['duplicateid']] as $dupeOccid => $dupe): ?>
						<?php if($dupeOccid !== $targetOccid): ?>
							<?= render_row($dupe, $targetOccid, $shownFields, ($optionCount === 1 && $dupe['collid'] != $target['collid'] && $autoCheckSingleOptions)) ?>
						<?php endif ?>
					<?php endforeach ?>
					<tr>
						<td colspan="18" style="height: 1rem"></td>
					</tr>
				</tbody>
			<?php endif ?>
			<?php endforeach ?>
			</table>

			<?php else: ?>
				<?php if(array_key_exists('searchDuplicates', $_REQUEST)): ?>
					<h4 style="margin-bottom: 1rem; padding:1rem 0;">
						<?= $LANG['NO_DUPLICATES'] ?>
					</h4>
				<?php else: ?>
					<h4 style="margin-bottom: 1rem; padding:1rem 0;">
						<?= $LANG['SEARCH_TO_SEE_DUPLICATES'] ?>
					</h4>
				<?php endif ?>
			<?php endif ?>

			<input type="hidden" name="copyInfo" value="1" />
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
