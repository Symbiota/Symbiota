<?php
global $SERVER_ROOT, $LANG_TAG, $CLIENT_ROOT;
include_once($SERVER_ROOT.'/classes/Database.php');
include_once($SERVER_ROOT.'/classes/utilities/QueryUtil.php');
include_once($SERVER_ROOT.'/content/lang/collections/sharedterms.' . $LANG_TAG . '.php');

$catId = array_key_exists("catid",$_REQUEST)?$_REQUEST["catid"]:'';

$sql = 'SELECT c.collid, c.institutioncode, c.collectioncode, c.collectionname, c.icon, c.colltype, ccl.ccpk,
	cat.category, cat.icon AS caticon, cat.acronym
	FROM omcollections c INNER JOIN omcollectionstats s ON c.collid = s.collid
	LEFT JOIN omcollcatlink ccl ON c.collid = ccl.collid
	LEFT JOIN omcollcategories cat ON ccl.ccpk = cat.ccpk
	WHERE s.recordcnt > 0 AND (cat.inclusive IS NULL OR cat.inclusive = 1 OR cat.ccpk = 1) 
	order by cat.category, collectionname';


$collectionsByCategory = [
	'Specimens' => [],
	'Observations' => [],
];
try {
$rs = QueryUtil::executeQuery(Database::connect('readonly'), $sql);

	foreach($rs->fetch_all(MYSQLI_ASSOC) as $collection) {
		$type = $collection['colltype'] === 'Preserved Specimens'?
		'Specimens':
		'Observations';

		if(!isset($collectionsByCategory[$type][$collection['category']])) {
			$collectionsByCategory[$type][$collection['category']] = [
				'name' => $collection['category'],
				'icon' => $collection['caticon'],
				'acronym' => $collection['acronym'],
				'id' => $collection['ccpk'],
				'collections' => [],
			];
		}

		$collectionsByCategory[$type][$collection['category']]['collections'][] = $collection;
	}

} catch(Throwable $th) {
	echo $th->getMessage();
}
//Icon Name instcode coll code
?>

<script type="text/javascript">
function toggleAllCheckboxes(scope, checked) {
	for(let input of scope.querySelectorAll('input[type="checkbox"]')) {
		input.checked = checked ;
	}
}

function updateParent(inputs, parentSelector) {	
	const parent = document.querySelector(parentSelector);

	let consensus = null;
	for(let input of inputs) {
		if(consensus === null) {
			consensus = input.checked;
		} else if(consensus != input.checked) {
			consensus = false;
			break;
		}
	}

	parent.checked = consensus;
	if(parent.onchange) {
		parent.onchange();
	}
}

function toggleCategory(categoryId) {
	const container = document.getElementById(categoryId + '_inputs')

	const open_toggle = document.getElementById(categoryId + '_open_toggle');
	const close_toggle = document.getElementById(categoryId + '_close_toggle');

	if(container.style.display === 'none') {
		open_toggle.style.display = 'none';
		close_toggle.style.display = 'flex';
		container.style.display = 'flex';
	} else {
		open_toggle.style.display = 'flex';
		close_toggle.style.display = 'none';
		container.style.display = 'none';
	}
}

</script>
<div>
	<input style="margin:0;" onclick="toggleAllCheckboxes(this.parentElement, this.checked)" type="checkbox" id="all_collections" name="all_collections" value="1">
	<label for="all_collections">
		Select/Deselect <a href="<?= $CLIENT_ROOT? '/' . $CLIENT_ROOT: ''?>/collections/misc/collprofiles.php">All Collections</a>
	</label>
	<?php foreach($collectionsByCategory as $collectionType => $categories): ?>
	<h2><?= $collectionType === 'Specimens'? 'Specimen Collections': 'Observation Collections' ?></h2>
	<?php foreach($categories as $category): ?>
	<?php $categoryIdentifer = $collectionType . '_' . $category['id'] ?>

	<fieldset id="<?=  $categoryIdentifer . '_container' ?>" style="margin-bottom: 1rem;">
		<legend>
			<div style="display:flex; align-items: center; gap:0.5rem;">
				<input data-category onchange="updateParent(document.querySelectorAll('input[data-category]'),'#all_collections')"  onclick="toggleAllCheckboxes(document.getElementById(`<?= $categoryIdentifer . '_container' ?>`), this.checked)"
					style="margin:0;" type="checkbox" id="<?= $categoryIdentifer ?>" name="<?= $categoryIdentifer ?>" value="1">
				<label for="<?= $categoryIdentifer ?>">
					<?= $category['name'] ?>
				</label>

				<a onclick="toggleCategory(`<?=  $categoryIdentifer ?>`)" style="cursor: pointer;">
					<span id="<?=  $categoryIdentifer . '_open_toggle' ?>" style="display: none; align-items: center; gap:0.5rem;">
						<img  src="<?= ($CLIENT_ROOT? '/'. $CLIENT_ROOT: '' ); ?>/images/plus.png" style="width: 1em; height: 1em; cursor: pointer;"/>
						Expand
					</span>

					<span id="<?=  $categoryIdentifer . '_close_toggle' ?>" style="display: flex; align-items: center; gap:0.5rem;">
						<img src="<?= ($CLIENT_ROOT? '/'. $CLIENT_ROOT: '' ); ?>/images/minus.png" style="width: 1em; height: 1em; cursor: pointer;"/>
						Condense
					</span>
				</a>
			</div>
		</legend>

		<div id="<?=  $categoryIdentifer . '_inputs' ?>"  style="display:flex; flex-direction:column; gap:0.5rem;" onchange="updateParent(this.querySelectorAll(`input[type=checkbox]`), '#<?= $categoryIdentifer ?>')">
			<?php foreach($category['collections'] as $collection): ?>
			<div style="display:flex; align-items: center; gap: 0.5rem;">
				<img width="30px" height="30px" src="<?= $collection['icon'] ?>">
				<input style="margin:0;" id="<?= $category['name'] . '_' . $collection['collid'] ?>" type="checkbox" name="db[]" value="<?= $collection['collid'] ?>">
				<label>
					<?= $collection['collectionname'] . ' (' . $collection['institutioncode'] . ($collection['collectioncode'] ? '-' . $collection['collectioncode'] : '') . ')' ?>
				</label>
			</div>
			<?php endforeach ?>
		</div>
	</fieldset>
	<?php endforeach ?>
	<?php endforeach ?>
</div>
