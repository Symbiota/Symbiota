<!DOCTYPE html>

<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/TaxonomyDisplayManager.php');
header("Content-Type: text/html; charset=".$CHARSET);
include_once($SERVER_ROOT.'/content/lang/taxa/taxonomy/taxonomydisplay.'.$LANG_TAG.'.php');

$target = $_REQUEST['target'] ?? '';
$displayAuthor = array_key_exists('displayauthor', $_REQUEST) ? filter_var($_REQUEST['displayauthor'], FILTER_SANITIZE_NUMBER_INT): 0;
$matchOnWords = array_key_exists('matchonwords', $_POST) ? filter_var($_POST['matchonwords'], FILTER_SANITIZE_NUMBER_INT) : 0;
$displayFullTree = array_key_exists('displayfulltree', $_REQUEST) ? filter_var($_REQUEST['displayfulltree'], FILTER_SANITIZE_NUMBER_INT) : 0;
$displaySubGenera = array_key_exists('displaysubgenera', $_REQUEST) ? filter_var($_REQUEST['displaysubgenera'], FILTER_SANITIZE_NUMBER_INT) : 0;
$taxAuthId = array_key_exists('taxauthid', $_REQUEST) ? filter_var($_REQUEST['taxauthid'], FILTER_SANITIZE_NUMBER_INT) : 1;
$statusStr = array_key_exists('statusstr', $_REQUEST) ? $_REQUEST['statusstr'] : '';

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
<html lang="<?php echo $LANG_TAG ?>">
<head>
	<title><?php echo $DEFAULT_TITLE." ".(isset($LANG['TAX_DISPLAY'])?$LANG['TAX_DISPLAY']:'Taxonomy Display').": ".$taxonDisplayObj->getTargetStr(); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>"/>
	<link href="<?php echo htmlspecialchars($CSS_BASE_PATH, HTML_SPECIAL_CHARS_FLAGS); ?>/jquery-ui.css" type="text/css" rel="stylesheet">
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
	$displayLeftMenu = (isset($taxa_admin_taxonomydisplayMenu)?$taxa_admin_taxonomydisplayMenu:false);
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="../../index.php"><?php echo htmlspecialchars((isset($LANG['HOME'])?$LANG['HOME']:'Home'), HTML_SPECIAL_CHARS_FLAGS); ?></a> &gt;&gt;
		<a href="taxonomydisplay.php"><b><?php echo htmlspecialchars((isset($LANG['TAX_TREE_VIEWER'])?$LANG['TAX_TREE_VIEWER']:'Taxonomic Tree Viewer'), HTML_SPECIAL_CHARS_FLAGS); ?></b></a>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<?php
		if($statusStr){
			$statusStr = str_replace(';', '<br/>', htmlspecialchars($statusStr, HTML_SPECIAL_CHARS_FLAGS));
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
					<img style='border:0px;width:1.5em;' src='../../images/add.png' alt="Plus sign">
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
				if(isset($taxMetaArr['url'])) echo '<div style="margin:3px 0px"><b>URL:</b> <a href="' . htmlspecialchars($taxMetaArr['url'], HTML_SPECIAL_CHARS_FLAGS) . '" target="_blank">' . htmlspecialchars($taxMetaArr['url'], HTML_SPECIAL_CHARS_FLAGS) . '</a></div>';
				if(isset($taxMetaArr['notes'])) echo '<div style="margin:3px 0px"><b>'.(isset($LANG['NOTES'])?$LANG['NOTES']:'Notes').':</b> '.$taxMetaArr['notes'].'</div>';
				echo '</div>';
			}
			?>
		</div>
		<div style="clear:both;">
			<form id="tdform" name="tdform" action="taxonomydisplay.php" method='POST'>
				<fieldset style="padding:10px;max-width:850px;" class="flex-form">
					<legend><b><?php echo (isset($LANG['TAX_SEARCH'])?$LANG['TAX_SEARCH']:'Taxon Search'); ?></b></legend>
					<div>
						<label for="taxontarget"> <?php echo htmlspecialchars($LANG['TAXON'], HTML_SPECIAL_CHARS_FLAGS) ?>: </label>
						<input id="taxontarget" class="search-bar" name="target" type="text" value="<?php echo $taxonDisplayObj->getTargetStr(); ?>" />

						<div>
							<input id="displayauthor" name="displayauthor" type="checkbox" value="1" <?php echo ($displayAuthor?'checked':''); ?> />
							<label for="displayauthor" > <?php echo (isset($LANG['DISP_AUTHORS'])?$LANG['DISP_AUTHORS']:'Display authors'); ?> </label>
						</div>
						<div>
							<input id="matchonwords" name="matchonwords" type="checkbox" value="1" <?php echo ($matchOnWords?'checked':''); ?> />
							<label for="matchonwords" > <?php echo (isset($LANG['MATCH_WHOLE_WORDS'])?$LANG['MATCH_WHOLE_WORDS']:'Match on whole words'); ?> </label>
						</div>
						<div>
							<input id="displayfulltree" name="displayfulltree" type="checkbox" value="1" <?php echo ($displayFullTree?'checked':''); ?> />
							<label for="displayfulltree" > <?php echo (isset($LANG['DISP_FULL_TREE'])?$LANG['DISP_FULL_TREE']:'Display full tree below family'); ?> </label>
						</div>
						<div>
							<input id="displaysubgenera" name="displaysubgenera" type="checkbox" value="1" <?php echo ($displaySubGenera?'checked':''); ?> />
							<label for="displaysubgenera"> <?php echo (isset($LANG['DISP_SUBGENERA'])?$LANG['DISP_SUBGENERA']:'Display species with subgenera'); ?> </label>
						</div>

					</div>

					<div class="flex-form">
						<div>
							<button name="tdsubmit" type="submit" value="displayTaxonTree"><?php echo (isset($LANG['DISP_TAX_TREE'])?$LANG['DISP_TAX_TREE']:'Display Taxon Tree'); ?></button>
							<input name="taxauthid" type="hidden" value="<?php echo $taxAuthId; ?>" />
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
