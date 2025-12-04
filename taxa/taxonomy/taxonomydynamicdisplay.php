<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/TaxonomyDisplayManager.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load('taxa/taxonomy/taxonomydisplay');

header('Content-Type: text/html; charset=' . $CHARSET);

$target = $_REQUEST['target'] ?? '';
$displayAuthor = !empty($_REQUEST['displayauthor']) ? 1: 0;
$limitToOccurrences = !empty($_REQUEST['limittooccurrences']) ? 1 : 0;
$taxAuthId = array_key_exists('taxauthid', $_REQUEST) ? filter_var($_REQUEST['taxauthid'], FILTER_SANITIZE_NUMBER_INT) : 1;
$editorMode = !empty($_REQUEST['emode']) ? 1 : 0;
$submitAction = array_key_exists('tdsubmit', $_POST) ? $_POST['tdsubmit'] : '';
$statusStr = $_REQUEST['statusstr'] ?? '';

$taxonDisplayObj = new TaxonomyDisplayManager();
$taxonDisplayObj->setTargetStr($target);
$taxonDisplayObj->setTaxAuthId($taxAuthId);

if($submitAction){
	if($submitAction == 'exportTaxonTree'){
		$taxonDisplayObj->setDisplayFullTree(1);
		$taxonDisplayObj->setLimitToOccurrences($limitToOccurrences);
		$taxonDisplayObj->exportCsv();
		exit;
	}
}

$isEditor = false;
if($IS_ADMIN || array_key_exists('Taxonomy', $USER_RIGHTS)){
	$isEditor = true;
	$editorMode = 1;
	if(array_key_exists('target', $_POST) && !array_key_exists('emode', $_POST)) $editorMode = 0;
}

$treePath = $taxonDisplayObj->getDynamicTreePath();
$targetId = end($treePath);
reset($treePath);
//echo json_encode($treePath);
?>
<!Doctype html>
<html lang="<?php echo $LANG_TAG ?>">
<head>
	<title><?php echo $DEFAULT_TITLE . ' ' . $LANG['TAX_EXPLORE'] . ': ' . $taxonDisplayObj->getTargetStr(); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>"/>
	<link href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<style>
		/* Tree styles */
		.tree-container {
			font-family: Arial, sans-serif;
			font-size: 14px;
		}
		.tree-node {
			margin-left: 20px;
			list-style: none;
		}
		.tree-node-content {
			padding: 2px 0;
			cursor: pointer;
			user-select: none;
		}
		.tree-node-content:hover {
			background-color: #f0f0f0;
		}
		.tree-expand-icon {
			display: inline-block;
			width: 16px;
			height: 16px;
			margin-right: 4px;
			cursor: pointer;
			font-weight: bold;
			text-align: center;
			line-height: 16px;
			font-size: 12px;
		}
		.tree-children {
			padding: 0;
			margin: 0;
		}
		.tree-children.hidden {
			display: none;
		}
		.tree-leaf .tree-expand-icon {
			visibility: hidden;
		}
		.fieldset-size {
			padding: 10px;
			max-width: 600px;
		}
		.icon-image{ border: 0px; width: 15px; }
		.tax-meta-arr {
			float: left;
			margin: 1rem 0rem 2.5rem 0rem;
			font-weight: bold;
			font-size: 120%;
		}
		.tax-detail-div {
			margin-top: 1.35rem;
			margin-left: 0.7rem;
			float: left;
			font-size: 80%;
		}
		.tax-meta-div {
			margin: 1rem 1.35rem 3rem 1.35rem;
			display: none;
			clear: both;
		}
	</style>
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#taxontarget").autocomplete({
				source: function( request, response ) {
					$.getJSON( "rpc/gettaxasuggest.php", { term: request.term, taid: document.tdform.taxauthid.value }, response );
				},
				autoFocus: true,
				minLength: 3 }
			);

			// Initialize the tree after DOM is ready
			initializeTaxonTree();
		});

		function displayTaxomonyMeta(){
			$("#taxDetailDiv").hide();
			$("#taxMetaDiv").show();
		}

		// Vanilla JavaScript Tree Implementation
		let treeData = {};
		let treePath = <?php echo json_encode($treePath); ?>;
		const displayAuthor = <?= $displayAuthor ?>;
		const limitToOccurrences = <?= $limitToOccurrences ?>;
		const targetId = <?= $targetId ?>;
		const editorMode = <?= $editorMode ?>;

		function initializeTaxonTree() {
			fetchTreeData('root').then(data => {
				if (data && data.children) {
					renderTree(data.children, document.getElementById('tree'), true);
					// Expand to the target path
					expandToPath(treePath);
				}
			}).catch(error => {
				console.error('Error loading tree:', error);
			});
		}

		async function fetchTreeData(id) {
			if (treeData[id]) {
				return treeData[id];
			}

			try {
				const params = new URLSearchParams({
					id: id,
					authors: displayAuthor,
					limittooccurrences: limitToOccurrences,
					targetid: targetId,
					emode: editorMode
				});

				const response = await fetch(`rpc/getdynamicchildren.php?${params}`);
				const data = await response.json();
				treeData[id] = data;
				return data;
			} catch (error) {
				console.error('Error fetching tree data:', error);
				return null;
			}
		}

		function renderTree(nodes, container, isRoot = false) {
			if (!isRoot) {
				container.innerHTML = '';
			}

			const ul = document.createElement('ul');
			ul.className = isRoot ? 'tree-container' : 'tree-children';

			nodes.forEach(node => {
				const li = document.createElement('li');
				li.className = 'tree-node';
				li.dataset.id = node.id;

				const nodeContent = document.createElement('div');
				nodeContent.className = 'tree-node-content';

				const hasChildren = node.children && node.children === true;
				
				if (hasChildren) {
					const expandIcon = document.createElement('span');
					expandIcon.className = 'tree-expand-icon';
					expandIcon.textContent = '+';
					expandIcon.onclick = (e) => {
						e.stopPropagation();
						toggleNode(li, node.id);
					};
					nodeContent.appendChild(expandIcon);
				} else {
					li.classList.add('tree-leaf');
					const expandIcon = document.createElement('span');
					expandIcon.className = 'tree-expand-icon';
					nodeContent.appendChild(expandIcon);
				}

				const label = document.createElement('span');
				label.innerHTML = node.label || node.name || '';
				label.onclick = () => {
					if (node.url) {
						window.open(node.url, '_blank');
					}
				};
				nodeContent.appendChild(label);

				li.appendChild(nodeContent);
				ul.appendChild(li);
			});

			container.appendChild(ul);
		}

		async function toggleNode(nodeElement, nodeId) {
			const expandIcon = nodeElement.querySelector('.tree-expand-icon');
			let childrenContainer = nodeElement.querySelector('.tree-children');

			if (childrenContainer) {
				// Toggle existing children
				if (childrenContainer.classList.contains('hidden')) {
					childrenContainer.classList.remove('hidden');
					expandIcon.textContent = '-';
				} else {
					childrenContainer.classList.add('hidden');
					expandIcon.textContent = '+';
				}
			} else {
				// Load and display children
				const data = await fetchTreeData(nodeId);
				if (data && data.children && Array.isArray(data.children)) {
					renderTree(data.children, nodeElement);
					expandIcon.textContent = '-';
				}
			}
		}

		async function expandToPath(pathArray) {
			for (let i = 0; i < pathArray.length; i++) {
				const nodeId = pathArray[i];
				const nodeElement = document.querySelector(`[data-id="${nodeId}"]`);
				
				if (nodeElement) {
					// Expand this node if it has children
					const expandIcon = nodeElement.querySelector('.tree-expand-icon');
					if (expandIcon && expandIcon.textContent === '+') {
						await toggleNode(nodeElement, nodeId);
					}
					
					// If this is the last item in the path, scroll to it
					if (i === pathArray.length - 1) {
						nodeElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
						nodeElement.style.backgroundColor = '#e6f3ff';
						setTimeout(() => {
							nodeElement.style.backgroundColor = '';
						}, 2000);
					}
				}
				
				// Small delay to allow DOM updates
				await new Promise(resolve => setTimeout(resolve, 100));
			}
		}
	</script>
</head>
<body>
	<?php
	$displayLeftMenu = (isset($taxa_admin_taxonomydisplayMenu)?$taxa_admin_taxonomydisplayMenu:false);
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="../../index.php"><?php echo htmlspecialchars($LANG['HOME'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?></a> &gt;&gt;
		<a href="taxonomydynamicdisplay.php"><b><?php echo htmlspecialchars($LANG['TAX_EXPLORE'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?></b></a>
	</div>
	<!-- This is inner text! -->
	<div role="main" id="innertext">
		<?php $taxMetaArr = $taxonDisplayObj->getTaxonomyMeta(); ?>
		<h1 class="page-heading"><?php echo $LANG['TAX_EXPLORE'] . ': ' . (array_key_exists('name', $taxMetaArr) ? $taxMetaArr['name'] : $LANG['CENTRAL_THESAURUS']); ?></h1>
		<?php
		if($statusStr){
			?>
			<hr/>
			<div style="color:<?php echo (strpos($statusStr,'SUCCESS') !== false?'green':'red'); ?>;margin:15px;">
				<?= $taxonDisplayObj->cleanOutStr($statusStr); ?>
			</div>
			<hr/>
			<?php
		}
		if($isEditor){
			?>
			<div style="float:right;">
				<a href="taxonomyloader.php" target="_blank"><img class="icon-image" src="../../images/add.png" title="<?= $LANG['ADD_NEW_TAXON'] ?>" alt="<?= $LANG['PLUS_SIGN_DESC'] ?>"></a>
			</div>
			<?php
		}
		?>
		<div>
			<?php
			
			if(count($taxMetaArr) > 1){
				//echo '<div id="taxDetailDiv" class="tax-detail-div"><a href="#" onclick="displayTaxomonyMeta()">(more details)</a></div>';
				echo '<div id="taxMetaDiv" class="tax-meta-div">';
				if(isset($taxMetaArr['description'])) echo '<div style="margin:3px 0px"><b>' . $LANG['DESCRIPTION'] . ':</b> ' . $taxMetaArr['description'] . '</div>';
				if(isset($taxMetaArr['editors'])) echo '<div style="margin:3px 0px"><b>' . $LANG['EDITORS'] . ':</b> ' . $taxMetaArr['editors'] . '</div>';
				if(isset($taxMetaArr['contact'])) echo '<div style="margin:3px 0px"><b>' . $LANG['CONTACT'] . ':</b> ' . $taxMetaArr['contact'] . '</div>';
				if(isset($taxMetaArr['email'])) echo '<div style="margin:3px 0px"><b>' . $LANG['EMAIL'] . ':</b> ' . $taxMetaArr['email'] . '</div>';
				if(isset($taxMetaArr['url'])) echo '<div style="margin:3px 0px"><b>URL:</b> <a href="' . $taxMetaArr['url'] . '" target="_blank">' . $taxMetaArr['url'] . '</a></div>';
				if(isset($taxMetaArr['notes'])) echo '<div style="margin:3px 0px"><b>' . $LANG['NOTES'] . ':</b> ' . $taxMetaArr['notes'] . '</div>';
				echo '</div>';
			}
			?>
		</div>
		<div style="clear:both;">
			<form id="tdform" name="tdform" action="taxonomydynamicdisplay.php" method='POST'>
				<fieldset class="fieldset-size">
					<legend><b><?php echo $LANG['TAX_SEARCH']; ?></b></legend>
                    <div>
						<label for="taxontarget"> <?= $LANG['TAXON'] ?>: </label>
						<input id="taxontarget" name="target" type="text" class="search-bar" value="<?= $taxonDisplayObj->getTargetStr() ?>" />
					</div>
					<div style="margin:15px 15px 0px 60px;">
						<div>
							<input id="displayauthor" name="displayauthor" type="checkbox" value="1" <?php echo ($displayAuthor ? 'checked' : ''); ?> />
							<label for="displayauthor"> <?= $LANG['DISP_AUTHORS'] ?> </label>
						</div>
						<div>
							<input id="limittooccurrences" name="limittooccurrences" type="checkbox" value="1" <?= ($limitToOccurrences ? 'checked' : ''); ?> />
							<label for="limittooccurrences"> <?= $LANG['LIMIT_TO_OCCURRENCES'] ?> </label>
						</div>
						<?php
						if($isEditor){
							?>
							<div>
								<input name="emode" id="emode" type="checkbox" value="1	" <?= ($editorMode ? 'checked' : '')?> />
								<label for="emode"><?= $LANG['EDITOR_MODE'] ?></label>
							</div>
							<?php
						}
						?>
					</div>
					<div class="flex-form" style="margin: 10px">
						<div>
							<button name="tdsubmit" type="submit" value="displayTaxonTree"><?= $LANG['DISP_TAX_TREE'] ?></button>
							<input name="taxauthid" type="hidden" value="<?= $taxAuthId; ?>" />
						</div>
						<div style="float: right">
							<button name="tdsubmit" type="submit" value="exportTaxonTree"><?= $LANG['EXPORT_TREE'] ?></button>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
		<div id="tree"></div>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
