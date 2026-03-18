<?php
global $SERVER_ROOT, $LANG_TAG, $LANG, $CLIENT_ROOT;
include_once($SERVER_ROOT.'/classes/Database.php');
include_once($SERVER_ROOT.'/classes/utilities/QueryUtil.php');
include_once($SERVER_ROOT.'/classes/utilities/Language.php');
include_once($SERVER_ROOT.'/classes/CollectionFormManager.php');

Language::load('collections/sharedterms');

$catId = array_key_exists("catid",$_REQUEST)?$_REQUEST["catid"]:'';
$collectionFormManager = new CollectionFormManager();
$collectionsByCategory = $collectionFormManager->getCollectionsByCategory();

$checkedCollections = [];

if(array_key_exists('db', $_REQUEST)) {
	$collIds = is_array($_REQUEST['db'])? $_REQUEST['db']: [$_REQUEST['db']];
	foreach($collIds as $collId) {
		$checkedCollections[$collId] = true;
	}
}
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

function toggleCategory(categoryId, event=null) {
	const container = document.getElementById(categoryId + '_inputs');
	if(!container) return;

	const open_toggle = document.getElementById(categoryId + '_open_toggle');
	const close_toggle = document.getElementById(categoryId + '_close_toggle');

	if(!open_toggle || !close_toggle) return;

	if(container.style.display === 'none') {
		open_toggle.style.display = 'none';
		close_toggle.style.display = 'flex';
		if(event?.type === 'click'){
			container.classList.remove('explicitly-collapsed');
		}
		container.style.display = 'flex';
	} else {
		open_toggle.style.display = 'flex';
		close_toggle.style.display = 'none';
		if(event?.type === 'click'){
			container.classList.add('explicitly-collapsed');
		}
		container.style.display = 'none';
	}
}

</script>
<div id="all_collections_parent_container" data-config='<?= json_encode([
		'CATORD' => $requestSuppliedCatOrd ?? $CATORD ?? [],
		'CATEXPND' =>  $requestSuppliedCatExpnd ?? $CATEXPND ?? [],
		'CATCHK' => $requestSuppliedCatChk ?? $CATCHK ?? [],
		'CURRENT_URL' => $_SERVER['REQUEST_URI'],
	]) ?>'></div>
<div>
	<?php
	$checkboxConfigs = [
		[
			'id' => 'all_collections',
			// 'name' => 'all_collections',
			'name' => 'db[]',
			'value'=> 'all',
			'target' => 'collections_container',
			'data_chip' => $LANG['ALL_COLLECTIONS'],
			'margin' => '0',
			'label_content' => $LANG['SELECT_DESELECT'] . '<a href="' . $CLIENT_ROOT . '/collections/misc/collprofiles.php">' . ' ' . $LANG['ALL_COLLECTIONS'] . '</a>',
		],
		[
			'id' => 'all_specimen_collections',
			'name' => 'all_specimen_collections',
			'value'=> 'all_specimens_collections',
			'target' => 'specimens_collections',
			'data_chip' => $LANG['ALL_SPECIMEN_COLLECTIONS'],
			'margin' => '0 15px',
			'label_content' => $LANG['SELECT_DESELECT_ALL_SPECIMENS'],
		],
		[
			'id' => 'all_observation_collections',
			'name' => 'all_observation_collections',
			'value'=> 'all_observations_collections',
			'target' => 'observations_collections',
			'data_chip' => $LANG['ALL_OBSERVATION_COLLECTIONS'],
			'margin' => '0 15px',
			'label_content' => $LANG['SELECT_DESELECT_ALL_OBSERVATIONS'],
		]
	];
	?>
	<div id="type-level-checkboxes" style="display:flex; gap: 15px;">
		<?php foreach($checkboxConfigs as $checkboxConfig): ?>
		<div id="<?= $checkboxConfig['id'] ?>_checkbox_container" style="display: inline-block; margin: <?= $checkboxConfig['margin'] ?>;">
			<input
				style="margin:0;"
				onclick="toggleAllCheckboxes(document.getElementById('<?= $checkboxConfig['target'] ?>'), this.checked)"
				data-chip="<?= $checkboxConfig['data_chip'] ?>"
				type="checkbox"
				id="<?= $checkboxConfig['id'] ?>"
				name="<?= $checkboxConfig['name'] ?>"
				value="<?= $checkboxConfig['value'] ?>"
				<?= array_key_exists($checkboxConfig['id'], $_REQUEST)? 'checked': '' ?>
			>
			<label for="<?= $checkboxConfig['id'] ?>">
				<?= $checkboxConfig['label_content']?>
			</label>
		</div>
		<?php endforeach; ?>
	</div>
	<div id="collections_container">
		<?php
			$sortedCollectionsByCategory = $collectionFormManager->reorderPortalCategories(
				$collectionsByCategory,
				$requestSuppliedCatOrd ?? $CATORD ?? [],
			);
			$allOrphans = $collectionFormManager->areAllCollectionsCategoryless($sortedCollectionsByCategory);
		 ?>
		<?php foreach($sortedCollectionsByCategory as $collectionType => $categories):
				$revisedUncategorizedCategories = $collectionFormManager->reviseUncategorizedCollections($categories);
			 ?>
		<div style="margin: 0;" id="<?= strtolower($collectionType) . '_collections' ?>">
			<h2>
				<?php if(!$allOrphans){
					echo ($collectionType === 'Specimens'? $LANG['SPECIMEN_COLLECTIONS']: $LANG['OBSERVATION_COLLECTIONS']);
				}
				?>
			</h2>
			<?php foreach($revisedUncategorizedCategories as $category): ?>
			<?php
				$categoryIdentifer = $collectionType . '_' . $category['id'];
			?>

			<fieldset id="<?=  $categoryIdentifer . '_container' ?>" style="margin-bottom: 1rem;">
				<legend>
					<div style="display:flex; align-items: center; gap:0.5rem;">
						<input
							data-category
							onchange="updateParent(document.querySelectorAll('input[data-category]'),'#all_collections')"
							onclick="toggleAllCheckboxes(document.getElementById(`<?= $categoryIdentifer . '_container' ?>`), this.checked)"
							style="margin:0;"
							type="checkbox"
							id="<?= $categoryIdentifer ?>"
							name="<?= $categoryIdentifer ?>"
							value="1"
							<?= array_key_exists($categoryIdentifer, $_REQUEST)? 'checked': '' ?>
						>
						<label for="<?= $categoryIdentifer ?>">
							<?= $category['name'] ?>
						</label>

						<a onclick="toggleCategory(`<?=  $categoryIdentifer ?>`, event)" style="cursor: pointer;">
							<span id="<?=  $categoryIdentifer . '_open_toggle' ?>"
								style="display: none; align-items: center; gap:0.5rem;">
								<img
									src="<?= $CLIENT_ROOT ?>/images/plus.png"
									style="width: 1em; height: 1em; cursor: pointer;"
								/>
								<?= $LANG['EXPAND'] ?>
							</span>

							<span id="<?=  $categoryIdentifer . '_close_toggle' ?>"
								style="display: flex; align-items: center; gap:0.5rem;">
								<img
									src="<?= $CLIENT_ROOT ?>/images/minus.png"
									style="width: 1em; height: 1em; cursor: pointer;"
								/>
								<?= $LANG['CONDENSE'] ?>
							</span>
						</a>
					</div>
				</legend>

				<div id="<?=  $categoryIdentifer . '_inputs' ?>"
					style="display:flex; flex-direction:column; gap:0.5rem;"
					onchange="updateParent(this.querySelectorAll(`input[type=checkbox]`), '#<?= $categoryIdentifer ?>')"
				>
					<?php foreach($category['collections'] as $collection): ?>
					<?php
						$collid = array_key_exists('collid', $collection) ? $collection['collid'] : null;
						$codeStr = $collectionFormManager->generateCodeStr($collection);
					?>
					<div style="display:flex; align-items: center; gap: 0.5rem;">
						<img width="30px" height="30px" src="<?= $collection['icon'] ?>">
						<input
							data-chip="Collection: <?= $codeStr ?>" aria-label="select collection <?= $collid ?>" data-role="none"
							data-codeStr="<?= $codeStr ?>"
							style="margin:0;"
							id="<?= $category['name'] . '_' . $collection['collid'] ?>"
							<?= array_key_exists($collection['collid'] ,$checkedCollections)? 'checked': '' ?>
							type="checkbox"
							name="db[]"
							value="<?= $collection['collid'] ?>"
						>
						<div>
							<div>
								<label for="<?= $category['name'] . '_' . $collection['collid'] ?>">
									<?= $collection['collectionname'] . ' (' . $collection['institutioncode'] . ($collection['collectioncode'] ? '-' . $collection['collectioncode'] : '') . ')' ?>
								</label>
							</div>
							<div>
								<a target="_blank" href="<?= $CLIENT_ROOT ?>/collections/misc/collprofiles.php?collid=<?= $collection['collid']?>">More Info</a>
							</div>
						</div>
					</div>
					<?php endforeach ?>
				</div>
			</fieldset>
			<?php endforeach ?>
		</div>
		<?php endforeach ?>
	</div>
</div>
