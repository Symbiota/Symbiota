<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/TaxonomyDisplayManager.php');
include_once($SERVER_ROOT.'/content/lang/taxa/taxonomy/taxonomydisplay.'.$LANG_TAG.'.php'); //yes, the lang file is the same as taxonomydisplay
header('Content-Type: text/html; charset='.$CHARSET);
header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past

$target = array_key_exists('target',$_REQUEST)?$_REQUEST['target']:'';
$displayAuthor = array_key_exists('displayauthor',$_REQUEST)?$_REQUEST['displayauthor']:0;
$taxAuthId = array_key_exists('taxauthid',$_REQUEST)?$_REQUEST['taxauthid']:1;
$editorMode = array_key_exists('emode',$_POST)?$_POST['emode']:0;
$statusStr = array_key_exists('statusstr',$_REQUEST)?$_REQUEST['statusstr']:'';

//Sanitation
$target = filter_var($target, FILTER_SANITIZE_STRING);
$displayAuthor = (is_numeric($displayAuthor)?$displayAuthor:0);
$taxAuthId = (is_numeric($taxAuthId)?$taxAuthId:0);
$editorMode = (is_numeric($editorMode)?$editorMode:0);
$statusStr = filter_var($statusStr, FILTER_SANITIZE_STRING);

$taxonDisplayObj = new TaxonomyDisplayManager();
$taxonDisplayObj->setTargetStr($target);
$taxonDisplayObj->setTaxAuthId($taxAuthId);

$isEditor = false;
if($IS_ADMIN || array_key_exists('Taxonomy',$USER_RIGHTS)){
	$isEditor = true;
	$editorMode = 1;
	if(array_key_exists('target',$_POST) && !array_key_exists('emode',$_POST)) $editorMode = 0;
}

$treePath = $taxonDisplayObj->getDynamicTreePath();
$targetId = end($treePath);
reset($treePath);
//echo json_encode($treePath);
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE.' Taxonomy Explorer: '.$taxonDisplayObj->getTargetStr(); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>"/>
	<link href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/dojo/1.14.1/dijit/themes/claro/claro.css" media="screen">
	<style type="text/css">
		.dijitLeaf,
		.dijitIconLeaf,
		.dijitFolderClosed,
		.dijitIconFolderClosed,
		.dijitFolderOpened,
		.dijitIconFolderOpen {
			background-image: none;
			width: 0px;
			height: 0px;
		}
	</style>
	<script src="../../js/jquery.js" type="text/javascript" ></script>
	<script src="../../js/jquery-ui.js" type="text/javascript" ></script>
	<script src="//ajax.googleapis.com/ajax/libs/dojo/1.14.1/dojo/dojo.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#taxontarget").autocomplete({
				source: function( request, response ) {
					$.getJSON( "rpc/gettaxasuggest.php", { term: request.term, taid: document.tdform.taxauthid.value }, response );
				}
			},{ minLength: 3 }
			);
		});

		function displayTaxomonyMeta(){
			$("#taxDetailDiv").hide();
			$("#taxMetaDiv").show();
		}
	</script>
</head>
<body class="claro">
	<?php
	$displayLeftMenu = (isset($taxa_admin_taxonomydisplayMenu)?$taxa_admin_taxonomydisplayMenu:'false');
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="../../index.php"><?php echo (isset($LANG['HOME'])?$LANG['HOME']:'Home'); ?></a> &gt;&gt;
		<a href="taxonomydynamicdisplay.php"><b><?php echo (isset($LANG['TAX_EXPLORE'])?$LANG['TAX_EXPLORE']:'Taxonomy Explorer'); ?></b></a>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<?php
		if($statusStr){
			?>
			<hr/>
			<div style="color:<?php echo (strpos($statusStr,'SUCCESS') !== false?'green':'red'); ?>;margin:15px;">
				<?php echo $statusStr; ?>
			</div>
			<hr/>
			<?php
		}
		if($isEditor){
			?>
			<div style="float:right;" title="<?php echo (isset($LANG['ADD_NEW_TAXON'])?$LANG['ADD_NEW_TAXON']:'Add a New Taxon'); ?>">
				<a href="taxonomyloader.php" target="_blank">
					<img style='border:0px;width:15px;' src='../../images/add.png'/>
				</a>
			</div>
			<?php
		}
		?>
		<div>
			<?php
			$taxMetaArr = $taxonDisplayObj->getTaxonomyMeta();
			echo '<div style="float:left;margin:10px 0px 25px 0px;font-weight:bold;font-size:120%;">'.$taxMetaArr['name'].'</div>';
			if(count($taxMetaArr) > 1){
				echo '<div id="taxDetailDiv" style="margin-top:15px;margin-left:5px;float:left;font-size:80%"><a href="#" onclick="displayTaxomonyMeta()">(more details)</a></div>';
				echo '<div id="taxMetaDiv" style="margin:10px 15px 35px 15px;display:none;clear:both;">';
				if(isset($taxMetaArr['description'])) echo '<div style="margin:3px 0px"><b>'.(isset($LANG['DESCRIPTION'])?$LANG['DESCRIPTION']:'Description').':</b> '.$taxMetaArr['description'].'</div>';
				if(isset($taxMetaArr['editors'])) echo '<div style="margin:3px 0px"><b>'.(isset($LANG['EDITORS'])?$LANG['EDITORS']:'Editors').':</b> '.$taxMetaArr['editors'].'</div>';
				if(isset($taxMetaArr['contact'])) echo '<div style="margin:3px 0px"><b>'.(isset($LANG['CONTACT'])?$LANG['CONTACT']:'Contact').':</b> '.$taxMetaArr['contact'].'</div>';
				if(isset($taxMetaArr['email'])) echo '<div style="margin:3px 0px"><b>'.(isset($LANG['EMAIL'])?$LANG['EMAIL']:'Email').':</b> '.$taxMetaArr['email'].'</div>';
				if(isset($taxMetaArr['url'])) echo '<div style="margin:3px 0px"><b>URL:</b> <a href="'.$taxMetaArr['url'].'" target="_blank">'.$taxMetaArr['url'].'</a></div>';
				if(isset($taxMetaArr['notes'])) echo '<div style="margin:3px 0px"><b>'.(isset($LANG['NOTES'])?$LANG['NOTES']:'Notes').':</b> '.$taxMetaArr['notes'].'</div>';
				echo '</div>';
			}
			?>
		</div>
		<div style="clear:both;">
			<form id="tdform" name="tdform" action="taxonomydynamicdisplay.php" method='POST'>
				<fieldset style="padding:10px;width:500px;">
					<legend><b><?php echo (isset($LANG['TAX_SEARCH'])?$LANG['TAX_SEARCH']:'Taxon Search'); ?></b></legend>
                    <div>
						<b><?php echo (isset($LANG['TAXON'])?$LANG['TAXON']:'Taxon'); ?>:</b>
						<input id="taxontarget" name="target" type="text" style="width:400px;" value="<?php echo $taxonDisplayObj->getTargetStr(); ?>" />
					</div>
					<div style="float:right;margin:15px 80px 15px 15px;">
						<button name="tdsubmit" type="submit" value="displayTaxonTree"><?php echo (isset($LANG['DISP_TAX_TREE'])?$LANG['DISP_TAX_TREE']:'Display Taxon Tree'); ?></button>
						<input name="taxauthid" type="hidden" value="<?php echo $taxAuthId; ?>" />
					</div>
					<div style="margin:15px 15px 0px 60px;">
						<input name="displayauthor" type="checkbox" value="1" <?php echo ($displayAuthor?'checked':''); ?> /> <?php echo (isset($LANG['DISP_AUTHORS'])?$LANG['DISP_AUTHORS']:'Display authors'); ?>
						<?php
						if($isEditor) echo '<br/><input name="emode" type="checkbox" value="1" '.($editorMode?'checked':'').' /> '.(isset($LANG['EDITOR_MODE'])?$LANG['EDITOR_MODE']:'Editor mode');
						?>
					</div>
				</fieldset>
			</form>
		</div>
		<div id="tree"></div>
		<script type="text/javascript">
			require([
				"dojo/window",
				"dojo/_base/declare",
				"dojo/dom",
				"dojo/on",
				"dijit/Tree",
				"dijit/tree/ObjectStoreModel",
				"dijit/tree/dndSource",
				"dojo/store/JsonRest",
				"dojo/domReady!"
			], function(win, declare, dom, on, Tree, ObjectStoreModel, dndSource, JsonRest){
				// set up the store to get the tree data
				var taxonTreeStore = new JsonRest({
					target: "rpc/getdynamicchildren.php",
					labelAttribute: "label",
					getChildren: function(object){
						return this.query({id:object.id,authors:<?php echo $displayAuthor; ?>,targetid:<?php echo $targetId; ?>, emode:<?php echo $editorMode; ?>}).then(function(fullObject){
							return fullObject.children;
						});
					},
					mayHaveChildren: function(object){
						return "children" in object;
					}
				});

				/*aspect.around(taxonTreeStore, "put", function(originalPut){
					return function(obj, options){
						if(options && options.parent){
							obj.parent = options.parent.id;
						}
						return originalPut.call(taxonTreeStore, obj, options);
					}
				});

				taxonTreeStore = new Observable(taxonTreeStore);*/

				// set up the model, assigning taxonTreeStore, and assigning method to identify leaf nodes of tree
				var taxonTreeModel = new ObjectStoreModel({
					store: taxonTreeStore,
					deferItemLoadingUntilExpand: true,
					getRoot: function(onItem){
						this.store.query({id:"root",authors:<?php echo $displayAuthor; ?>,targetid:<?php echo $targetId; ?>}).then(onItem);
					},
					mayHaveChildren: function(object){
						return "children" in object;
					}
				});

				var TaxonTreeNode = declare(Tree._TreeNode, {
					_setLabelAttr: {node: "labelNode", type: "innerHTML"}
				});

				// set up the tree, assigning taxonTreeModel;
				var taxonTree = new Tree({
					model: taxonTreeModel,
					showRoot: false,
					label: "Taxa Tree",
					//dndController: dndSource,
					persist: false,
					_createTreeNode: function(args){
					   return new TaxonTreeNode(args);
					},
					onClick: function(item){
						// Get the URL from the item, and navigate to it
						//location.href = item.url;
						window.open(item.url,'_blank');
					}
				}, "tree");

				taxonTree.set("path", <?php echo json_encode($treePath); ?>).then(
					function(path){
						if(taxonTree.selectedNode){
							taxonTree._expandNode(taxonTree.selectedNode);
							document.getElementById(taxonTree.selectedNode.id).scrollIntoView();
							//win.scrollIntoView(taxonTree.selectedNode.id);
						}
					}
				);
				taxonTree.startup();

				/*taxonTree.onLoadDeferred.then(function(){
					var parentnode = taxonTree.getNodesByItem("<?php echo $targetId; ?>");
					var lastnodes = parentnode[0].getChildren();
					for (i in lastnodes) {
						if(lastnodes[i].isExpanded){
							 taxonTree._collapseNode(lastnodes[i]);
						}
						lastnodes[i].makeExpandable();
					}
				});*/
			});

		</script>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>