<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/TaxonomyDisplayManager.php');
header("Content-Type: text/html; charset=".$CHARSET);
include_once($SERVER_ROOT.'/content/lang/taxa/taxonomy/taxonomydisplay.'.$LANG_TAG.'.php');

$target = array_key_exists('target', $_REQUEST) ? filter_var($_REQUEST['target'], FILTER_SANITIZE_STRING) : '';
$displayAuthor = array_key_exists('displayauthor', $_REQUEST) ? filter_var($_REQUEST['displayauthor'], FILTER_SANITIZE_NUMBER_INT): 0;
$matchOnWords = array_key_exists('matchonwords', $_POST) ? filter_var($_POST['matchonwords'], FILTER_SANITIZE_NUMBER_INT) : 0;
$displayFullTree = array_key_exists('displayfulltree', $_REQUEST) ? filter_var($_REQUEST['displayfulltree'], FILTER_SANITIZE_NUMBER_INT) : 0;
$displaySubGenera = array_key_exists('displaysubgenera', $_REQUEST) ? filter_var($_REQUEST['displaysubgenera'], FILTER_SANITIZE_NUMBER_INT) : 0;
$taxAuthId = array_key_exists('taxauthid', $_REQUEST) ? filter_var($_REQUEST['taxauthid'], FILTER_SANITIZE_NUMBER_INT) : 1;
$statusStr = array_key_exists('statusstr', $_REQUEST) ? filter_var($_REQUEST['statusstr'], FILTER_SANITIZE_STRING) : '';

if(!$target) $matchOnWords = 1;
$taxonDisplayObj = new TaxonomyDisplayManager();
$taxonDisplayObj->setTargetStr($target);
$taxonDisplayObj->setTaxAuthId($taxAuthId);
$taxonDisplayObj->setDisplayAuthor($displayAuthor);
$taxonDisplayObj->setMatchOnWholeWords($matchOnWords);
$taxonDisplayObj->setDisplayFullTree($displayFullTree);
$taxonDisplayObj->setDisplaySubGenera($displaySubGenera);

$isEditor = false;
if($IS_ADMIN || array_key_exists("Taxonomy",$USER_RIGHTS)){
	$isEditor = true;
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE." ".(isset($LANG['TAX_DISPLAY'])?$LANG['TAX_DISPLAY']:'Taxonomy Display').": ".$taxonDisplayObj->getTargetStr(); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>"/>
	<link href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<script src="../../js/jquery.js" type="text/javascript"></script>
	<script src="../../js/jquery-ui.js" type="text/javascript"></script>
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
<body>
	<?php
	$displayLeftMenu = (isset($taxa_admin_taxonomydisplayMenu)?$taxa_admin_taxonomydisplayMenu:'false');
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="../../index.php"><?php echo (isset($LANG['HOME'])?$LANG['HOME']:'Home'); ?></a> &gt;&gt;
		<a href="taxonomydisplay.php"><b><?php echo (isset($LANG['TAX_TREE_VIEWER'])?$LANG['TAX_TREE_VIEWER']:'Taxonomic Tree Viewer'); ?></b></a>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<?php
		if($statusStr){
			$statusStr = str_replace(';', '<br/>', $statusStr);
			?>
			<hr/>
			<div style="color:<?php echo (stripos($statusStr,'SUCCESS') !== false?'green':'red'); ?>;margin:15px;">
				<?php echo $statusStr; ?>
			</div>
			<hr/>
			<?php
		}
		if($isEditor){
			?>
			<div style="float:right;" title="<?php echo (isset($LANG['ADD_NEW_TAXON'])?$LANG['ADD_NEW_TAXON']:'Add a New Taxon'); ?>">
				<a href="taxonomyloader.php">
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
			<form id="tdform" name="tdform" action="taxonomydisplay.php" method='POST'>
				<fieldset style="padding:10px;max-width:850px;">
					<legend><b><?php echo (isset($LANG['TAX_SEARCH'])?$LANG['TAX_SEARCH']:'Taxon Search'); ?></b></legend>
					<div style="float:left;">
						<b><?php echo (isset($LANG['TAXON'])?$LANG['TAXON']:'Taxon'); ?>:</b>
						<input id="taxontarget" name="target" type="text" style="width:400px;" value="<?php echo $taxonDisplayObj->getTargetStr(); ?>" />
					</div>
					<div style="float:left;margin-left:15px;">
						<button name="tdsubmit" type="submit" value="displayTaxonTree"><?php echo (isset($LANG['DISP_TAX_TREE'])?$LANG['DISP_TAX_TREE']:'Display Taxon Tree'); ?></button>
						<input name="taxauthid" type="hidden" value="<?php echo $taxAuthId; ?>" />
					</div>
					<div style="clear:both;padding-top:15px; margin-left:60px;">
						<div style="margin:3px;">
							<input name="displayauthor" type="checkbox" value="1" <?php echo ($displayAuthor?'checked':''); ?> /> <?php echo (isset($LANG['DISP_AUTHORS'])?$LANG['DISP_AUTHORS']:'Display authors'); ?>
						</div>
						<div style="margin:3px;">
							<input name="matchonwords" type="checkbox" value="1" <?php echo ($matchOnWords?'checked':''); ?> /> <?php echo (isset($LANG['MATCH_WHOLE_WORDS'])?$LANG['MATCH_WHOLE_WORDS']:'Match on whole words'); ?>
						</div>
						<div style="margin:3px">
							<input name="displayfulltree" type="checkbox" value="1" <?php echo ($displayFullTree?'checked':''); ?> /> <?php echo (isset($LANG['DISP_FULL_TREE'])?$LANG['DISP_FULL_TREE']:'Display full tree below family'); ?>
						</div>
						<div style="margin:3px;">
							<input name="displaysubgenera" type="checkbox" value="1" <?php echo ($displaySubGenera?'checked':''); ?> /> <?php echo (isset($LANG['DISP_SUBGENERA'])?$LANG['DISP_SUBGENERA']:'Display species with subgenera'); ?>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
		<?php
		$taxonDisplayObj->displayTaxonomyHierarchy();
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
