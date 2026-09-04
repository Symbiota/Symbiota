<?php
use PHPUnit\Exception;

include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/TPEditorManager.php');
include_once($SERVER_ROOT.'/classes/TPDescEditorManager.php');
include_once($SERVER_ROOT.'/classes/TPImageEditorManager.php');
include_once($SERVER_ROOT.'/classes/Media.php');
include_once($SERVER_ROOT.'/classes/Paginator.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load('taxa/profile/tpeditor');

header('Content-Type: text/html; charset='.$CHARSET);

$tid = array_key_exists('tid', $_REQUEST) ? Sanitize::int($_REQUEST['tid']) : 0;
$taxon = array_key_exists('taxon',$_REQUEST)?$_REQUEST['taxon']:'';
$tabIndex = array_key_exists('tabindex', $_REQUEST) ? Sanitize::int($_REQUEST['tabindex']) : 0;
$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';
$mediaPage = Paginator::getPageRequestVar('mediaPage');
$mediaSortPage = Paginator::getPageRequestVar('mediaSortPage');

if(!is_numeric($tabIndex)) $tabIndex = 0;

$tEditor = null;
if($tabIndex == 1 || $tabIndex == 2){
	$tEditor = new TPImageEditorManager();
}
elseif($tabIndex == 4){
	$tEditor = new TPDescEditorManager();
}
else{
	$tEditor = new TPEditorManager();
}

$taxaArr = array();
if(!$tid && $taxon){
	if(is_numeric($taxon)) $tid = $taxon;
	else{
		$taxaArr = $tEditor->getTidFromStr($taxon);
		if($taxaArr){
			if(count($taxaArr) == 1) $tid = key($taxaArr);
		}
	}
}
$tEditor->setTid($tid);
$tid = $tEditor->getTid();

$statusStr = '';
$isEditor = false;
if($IS_ADMIN || array_key_exists("TaxonProfile",$USER_RIGHTS)) $isEditor = true;

if($isEditor && $action){
	/*
	 * Pending deprecation of allowing Taxon Profile Editors to adjust display order of synonyms
	if($action == 'editSynonymSort'){
		$synSortArr = Array();
		foreach($_REQUEST as $sortKey => $sortValue){
			if($sortValue && (substr($sortKey,0,4) == "syn-")){
				$synSortArr[substr($sortKey,4)] = $sortValue;
			}
		}
		$statusStr = $tEditor->editSynonymSort($synSortArr);
	}
	*/
	if($action == "Submit Common Name Edits"){
		if(!$tEditor->editVernacular($_POST)) $statusStr = $tEditor->getErrorMessage();
	}
	elseif($action == "Add Common Name"){
		if(!$tEditor->addVernacular($_POST)) $statusStr = $tEditor->getErrorMessage();
	}
	elseif($action == "Delete Common Name"){
		if(!$tEditor->deleteVernacular($_REQUEST["delvern"])) $statusStr = $tEditor->getErrorMessage();
	}
	elseif($action == 'Add Description Block'){
		if(!$tEditor->insertDescriptionBlock($_POST)){
			$statusStr = 'ERROR inserting description block: ' . $tEditor->getErrorMessage();
		}
	}
	elseif($action == 'saveDescriptionBlock'){
		if(!$tEditor->updateDescriptionBlock($_POST)){
			$statusStr = 'ERROR editing description block: '.$tEditor->getErrorMessage();
		}
	}
	elseif($action == 'Delete Description Block'){
		if(!$tEditor->deleteDescriptionBlock($_POST['tdbid'])){
			$statusStr = 'ERROR deleting description block: ' . $tEditor->getErrorMessage();
		}
	}
	elseif($action == 'remap'){
		if(!$tEditor->remapDescriptionBlock($_GET['tdbid'])){
			$statusStr = 'ERROR remapping description block: ' . $tEditor->getErrorMessage();
		}
	}
	elseif($action == 'Add Statement'){
		if(!$tEditor->addStatement($_POST)){
			$statusStr = $tEditor->getErrorMessage();
		}
	}
	elseif($action == 'saveStatementEdit'){
		if(!$tEditor->editStatement($_POST)){
			$statusStr = $tEditor->getErrorMessage();
		}
	}
	elseif($action == 'Delete Statement'){
		if(!$tEditor->deleteStatement($_POST['tdsid'])){
			$statusStr = $tEditor->getErrorMessage();
		}
	}
	elseif($action == 'Submit Image Sort Edits'){
		$imgSortArr = Array();
		foreach($_REQUEST as $sortKey => $sortValue){
			if($sortValue && substr($sortKey,0,6) == 'imgid-'){
				$imgSortArr[substr($sortKey,6)]  = $sortValue;
			}
		}
		$statusStr = $tEditor->editImageSort($imgSortArr);
	}
	elseif($action == 'Upload Image'){
		$family = $tEditor->getFamily();
		$path = ($family? $family . '/': '') . date('Ym') . '/';
		try {
			Media::uploadAndInsert(
				$_POST,
				$_FILES['imgfile'] ?? null,
				StorageFactory::make($path)
			);
		} catch(Exception $e) {
			$statusStr .= '<br/>' . $e->getMessage();
		}
	}
}
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<title><?= $DEFAULT_TITLE . ' ' . $LANG['TAXON_EDITOR'] .': ' . $tEditor->getSciName(); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET;?>" />
	<link href="<?= $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/javascript_lang_tags.php');
	?>
	<script type="text/javascript" src="../../js/symb/shared.js"></script>
	<script src="<?= $CLIENT_ROOT; ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT; ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT; ?>/js/symb/taxa.tpimageeditor.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT; ?>/js/symb/taxa.suggest.js?v=1a" type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#tabs').tabs({
				active: <?= $tabIndex; ?>
			});

			const taxonInput = document.querySelector("#taxon");
			if(taxonInput){
				taxonInput.addEventListener("focus", (event) => {
					taxaSuggest.config.clientRoot = "<?= $CLIENT_ROOT ?>";
					taxaSuggest.config.includeAuthor = <?= (empty($TAXON_AUTOCOMPLETE_INCLUDE_AUTHOR) ? 'false' : 'true') ?>;
					taxaSuggest.config.includeKingdom = <?= (empty($TAXON_AUTOCOMPLETE_INCLUDE_KINGDOM) ? 'false' : 'true') ?>;
					taxaSuggest.initiate("taxon", function(result){
						if(result.item){
							$("#tid").val(result.item.id);
						}
						else{
							$("#tid").val("");
							if(this.value != ""){
								alert("<?= $LANG['SELECT_FROM_LIST'] ?>");
							}
						}
					});
				});
			}

		});

		function submitAddImageForm(f){
			var fileBox = document.getElementById("imgfile");
			var file = fileBox.files[0];
			if(file.size>4000000){
				alert("<?= $LANG['IMG_TOO_LARGE']; ?>");
				return false;
			}
		}

		function validateGetTaxonForm(f){
			if(f.taxon.value != "" && f.tid.value == ""){
				alert("<?= $LANG['SELECT_FROM_LIST'] ?>");
				return false;
			}
			return true;
		}
	</script>
	<style>
		.sectionDiv{ clear:both; }
		.sectionDiv div{ float:left }
		.labelDiv{ margin-right: 5px }
		#redirectedfrom{ font-size:1rem; margin-top:5px; margin-left:10px; font-weight:bold; }
		#taxonDiv{ font-size:1.125rem; margin-top:15px; margin-left:10px; }
		#taxonDiv a{ color:#990000; font-weight: bold; font-style: italic; }
		#familyDiv{ margin-left:20px; margin-top:0.25em; }
		.tox-dialog{ min-height: 400px }
		input{ margin:3px; border:inset; }
		hr{ margin:30px 0px; }
		.icon-img{ border: 0px; height: 1.2em; }
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = (isset($taxa_admin_tpeditorMenu)?$taxa_admin_tpeditorMenu:false);
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="../../index.php"><?= $LANG['HOME'] ?></a> &gt;&gt;
		<?php
		if($tid) echo '<a href="../index.php?tid=' . $tid . '">' . $LANG['TAX_PROF_PUBLIC_DISP'] . '</a> &gt;&gt; ';
		echo '<b>'.$LANG['TAX_PROF_EDITOR'].'</b>';
		?>
	</div>
	<div role="main" id="innertext">
		<h1 class="page-heading"><?php
		 $splitSciname = $tEditor->splitSciname();
		 $author = !empty($splitSciname['author']) ? ($splitSciname['author'] . ' ') : '';
		 $cultivarEpithet = !empty($splitSciname['cultivarEpithet']) ? ($tEditor->standardizeCultivarEpithet($splitSciname['cultivarEpithet'])) . ' ' : '';
		 $tradeName = !empty($splitSciname['tradeName']) ? ($tEditor->standardizeTradeName($splitSciname['tradeName']) . ' ') : '';
		 $nonItalicizedScinameComponent = $author . $cultivarEpithet . $tradeName;

		 echo $LANG['TAX_PROF_EDITOR'] . ': <i>' . $splitSciname['base'] . '</i> ' . $nonItalicizedScinameComponent;
		 ?></h1>
		<?php
		if($tEditor->getTid()){
			if($isEditor){
				if($tEditor->isForwarded()) echo '<div id="redirectedfrom">' . $LANG['REDIRECTED_FROM'] . ': <i>' . $tEditor->getSubmittedValue('sciname') . '</i></div>';
				echo '<div id="taxonDiv"><a href="../index.php?taxon=' . $tEditor->getTid() . '">' . $LANG['VIEW_PUBLIC_TAXON'] . '</a> ';
				if($tEditor->getRankId() > 140) echo "&nbsp;<a href='tpeditor.php?tid=" . $tEditor->getParentTid() . "'><img class='icon-img' src='../../images/toparent.png' title='" . $LANG['GO_TO_PARENT'] . "' /></a>";
				echo "</div>\n";
				if($tEditor->getFamily()) echo '<div id="familyDiv"><b>' . $LANG['FAMILY'] . ':</b> ' . $tEditor->getFamily() . '</div>' . "\n";
				if($statusStr) echo '<div style="margin:15px;font-weight:bold;font-size:120%;color:' . (stripos($statusStr,'error') !== false?'red':'green') .';">' . $statusStr . '</div>';
				?>
				<div id="tabs" style="margin:10px;">
					<ul>
						<li><a href="#commontab"><span><?= $LANG['VERNAC_COMMON'] ?></span></a></li>
					<li><a href="tpimageeditor.php?tid=<?= $tEditor->getTid() . '&mediaPage=' . $mediaPage . '&mediaSortPage=' . $mediaSortPage ?>"><span><?= $LANG['IMAGES'] ?></span></a></li>
						<li><a href="tpimageeditor.php?tid=<?= $tEditor->getTid() . '&cat=imagequicksort&mediaSortPage=' . $mediaSortPage . '&mediaPage=' . $mediaPage ?> "><span><?= $LANG['IMAGE_SORT'] ?></span></a></li>
						<li><a href="tpimageeditor.php?tid=<?= $tEditor->getTid() . '&cat=imageadd' . '&mediaSortPage=' . $mediaSortPage . '&mediaPage=' . $mediaPage ?>"><span><?= $LANG['ADD_IMAGE'] ?></span></a></li>
						<li><a href="tpdesceditor.php?tid=<?= $tEditor->getTid() . '&action=' . Sanitize::outString($action) . '&mediaSortPage=' . $mediaSortPage . '&mediaPage=' . $mediaPage ?>"><span><?= $LANG['DESCRIPTIONS'] ?></span></a></li>
					</ul>
					<div id="commontab">
						<?php
						//Display Common Names (vernaculars)
						$vernacularList = $tEditor->getVernaculars();
						$langArr = $tEditor->getLangArr();
						?>
						<div>
							<div style="margin:10px 0px" title="<?= $LANG['ADD_COMMON_NAME']; ?>">
								<b><?= ($vernacularList ? $LANG['COMMON_NAMES'] : $LANG['NO_COMMON_NAMES']); ?></b>
								<a href="#" onclick="toggle('addvern');return false;">
									<img class="icon-img" src="../../images/add.png"/>
								</a>
							</div>
							<div id="addvern" class="addvern" style="display:<?= ($vernacularList?'none':'block'); ?>;">
								<form name="addvernform" action="tpeditor.php" method="post" >
									<fieldset style="width:650px;margin:5px 0px 0px 20px;">
										<legend><b><?= $LANG['NEW_COMMON_NAME']; ?></b></legend>
										<div>
											<?= $LANG['COMMON_NAME']; ?>:
											<input name="vernname" type="text" style="width:250px" />
										</div>
										<div>
											<?= $LANG['LANGUAGE']; ?>:
											<select name="langid">
												<option value=""><?= $LANG['SEL_LANGUAGE']; ?></option>
												<?php
												foreach($langArr as $langID => $langName){
													echo '<option value="' . $langID . '" ' . (strpos($langName,'(' . $DEFAULT_LANG . ')') ? 'SELECTED' : '') . '>' . $langName . '</option>';
												}
												?>
											</select>
										</div>
										<div>
											<?= $LANG['NOTES']; ?>:
											<input name="notes" type="text" style="width:500px" />
										</div>
										<div>
											<?= $LANG['SOURCE']; ?>:
											<input name="source" type="text" style="width:500px" />
										</div>
										<div>
											<?= $LANG['SORT_SEQUENCE']; ?>:
											<input name="sortsequence" style="width:40px" type="text" />
										</div>
										<div>
											<input type="hidden" name="tid" value="<?= $tEditor->getTid(); ?>" />
											<button id="vernsadd" name="action" style="margin-top:5px;" type="submit" value="Add Common Name" ><?= $LANG['ADD_COMMON_NAME']; ?></button>
										</div>
									</fieldset>
								</form>
							</div>
							<?php
							foreach($vernacularList as $lang => $vernsList){
								?>
								<div style="width:650px;margin:5px 0px 0px 15px;">
									<fieldset style="width:650px;margin:5px 0px 0px 15px;">
										<legend><b><?= $lang; ?></b></legend>
										<?php
										foreach($vernsList as $vid => $vernArr){
											?>
											<div style="margin-left:10px;" title="<?= $LANG['EDIT_COMMON_NAME']; ?>">
												<b><?= $vernArr['vernname']; ?></b>
												<a href="#" onclick="toggle('vid-<?= $vid; ?>');return false;">
													<img class="icon-img" src="../../images/edit.png" />
												</a>
											</div>
											<form name="updatevern" action="tpeditor.php" method="post" style="margin:15px;clear:both">
												<div class="sectionDiv">
													<div class='vid-<?= $vid; ?>' style='display:none;'>
														<input id="vernname" name="vernname" type="text" value="<?= $vernArr["vernname"]; ?>" style="width:250px" />
													</div>
												</div>
												<div class="sectionDiv">
													<div class="labelDiv"><?= $LANG['LANGUAGE']; ?>:</div>
													<div class='vid-<?= $vid; ?>'><?= $langArr[$vernArr['langid']]; ?></div>
													<div class='vid-<?= $vid; ?>' style='display:none;'>
														<select name="langid">
															<option value=""><?= $LANG['SEL_LANGUAGE']; ?></option>
															<?php
															foreach($langArr as $langID => $langName){
																echo '<option value="' . $langID . '" ' . ($vernArr['langid']==$langID ? 'SELECTED' : '') . '>' . $langName . '</option>';
															}
															?>
														</select>
													</div>
												</div>
												<div class="sectionDiv">
													<div class="labelDiv"><?= $LANG['NOTES']; ?>:</div>
													<div class="vid-<?= $vid; ?>"><?= $vernArr['notes']; ?></div>
													<div class="vid-<?= $vid; ?>" style="display:none;">
														<input id='notes' name='notes' type='text' value='<?= $vernArr['notes'];?>' style="width:500px" />
													</div>
												</div>
												<div class="sectionDiv">
													<div class="labelDiv"><?= $LANG['SOURCE']; ?>:</div>
													<div class="vid-<?= $vid; ?>"> <?= $vernArr['source']; ?></div>
													<div class="vid-<?= $vid; ?>" style='display:none;'>
														<input id='source' name='source' type='text' value='<?= $vernArr['source']; ?>' style="width:500px" />
													</div>
												</div>
												<div class="sectionDiv">
													<div class="labelDiv"><?= $LANG['SORT_SEQUENCE']; ?>:</div>
													<div class='vid-<?= $vid; ?>'><?= $vernArr['sort'];?></div>
													<div class='vid-<?= $vid; ?>' style='display:none;'>
														<input id='sortsequence' name='sortsequence' style='width:40px;' type='text' value='<?= $vernArr['sort']; ?>' />
													</div>
												</div>
												<div class="sectionDiv">
													<input type='hidden' name='vid' value='<?= $vid; ?>' />
													<input type='hidden' name='tid' value='<?= $tEditor->getTid();?>' />
													<div class='vid-<?= $vid;?>' style='display:none;'>
														<button name='action' type='submit' value='Submit Common Name Edits' ><?= $LANG['SUBMIT_COMMON_EDITS']; ?></button>
													</div>
												</div>
											</form>
											<div class="vid-<?= $vid; ?>" style="display:none;padding-top:15px;padding-left:15px;clear:both">
												<form id="delvern" name="delvern" action="tpeditor.php" method="post" onsubmit="return window.confirm('<?= $LANG['SURE_DELETE_COMMON']; ?>')">
													<input type="hidden" name="delvern" value="<?= $vid; ?>" />
													<input type="hidden" name="tid" value="<?= $tEditor->getTid(); ?>" />
													<button class="button-danger" name="action" type="submit" value="Delete Common Name"><?= $LANG['DELETE_COMMON']; ?></button>
												</form>
											</div>
											<div style="clear:both;margin:10px 0px"><hr/></div>
											<?php
										}
										?>
									</fieldset>
								</div>
								<?php
							}
							?>
						</div>
						<!-- Deprecation of Taxon Profile Editors ability to adjust display order of synonyms pending user input
						<hr/>
						<fieldset style="width:650px;margin:5px 0px 0px 15px;">
							<legend><b><?php //echo $LANG['SYNONYMS']; ?></b></legend>
							<?php
							//Display Synonyms
							//if($synonymArr = $tEditor->getSynonym()){
								?>
								<div style="float:right;" title="<?php //echo $LANG['EDIT_SYN_ORDER']; ?>">
									<a href="#"  onclick="toggle('synsort');return false;"><img class="icon-img" src="../../images/edit.png"/></a>
								</div>
								<div style="font-weight:bold;margin-left:15px;">
									<ul>
										<?php
										// foreach($synonymArr as $tidKey => $valueArr){
										// 	 //echo '<li>' . $valueArr["sciname"] . '</li>';
										// }
										?>
									</ul>
								</div>
								<div class="synsort" style="display:none;">
									<form name="synsortform" action="tpeditor.php" method="post">
										<input type="hidden" name="tid" value="<?= $tEditor->getTid(); ?>" />
										<fieldset style='margin:5px 0px 5px 5px;margin-left:20px;width:350px;'>
										<legend><b><?php //echo $LANG['SYN_SORT_ORDER']; ?></b></legend>
										<?php
										// foreach($synonymArr as $tidKey => $valueArr){
											?>
												<div>
													<b><?php //echo $valueArr["sortsequence"]; ?></b> -
													<?php //echo $valueArr["sciname"]; ?>
												</div>
												<div style="margin:0px 0px 5px 10px;">
													new sort value:
													<input type="text" name="syn-<?php //echo $tidKey; ?>" style="width:35px;border:inset;" />
												</div>
												<?php
											//}
											?>
											<div>
												<button type="submit" name="action" value="editSynonymSort"><?php //echo $LANG['EDIT_SYN_ORDER']; ?></button>
											</div>
										</fieldset>
									</form>
								</div>
								<?php
							// }
							// else{
								//echo '<div style="margin:20px 0px"><b>' . $LANG['NO_SYN_LINK'] . '</b></div>';
							// }
							?>
							<div style="margin:10px;">
								*<?php //echo $LANG['MOST_SYN_IN_TAX_THES'] . ' <a href="../../sitemap.php">' . $LANG['SITEMAP'] . '</a>).'; ?>
							</div>
						</fieldset>
						-->
					</div>
				</div>
				<?php
			}
			else{
				?>
				<div style="margin:30px;">
					<h2><?= $LANG['NOT_AUTH']; ?></h2>
				</div>
				<?php
			}
		}
		else{
			?>
			<div style="margin:20px;">
				<form name="gettidform" action="tpeditor.php" method="post" onsubmit="return validateGetTaxonForm(this)">
					<b><label for="taxon"> <?= $LANG['SCINAME'] ?>: </label></b>
					<input id="taxon" name="taxon" value="<?= $taxon ?>" size="60" required />
					<input id="tid" name="tid" value="<?= $tid ?>" type="hidden" >
					<input type="hidden" name="tabindex" value="<?= $tabIndex ?>" />
					<div style="margin: 20px">
						<button type="submit" name="action" value="editTaxon" ><?= $LANG['EDIT_TAXON_PROFILE'] ?></button>
					</div>
				</form>
			</div>
			<?php
			if(count($taxaArr) > 1){
				echo '<div style="margin:15px">'.$LANG['MORE_THAN_ONE_TAXON'].': </div>';
				echo '<div style="margin:10px">';
				foreach($taxaArr as $tidKey => $sciArr){
					$outStr = '<b>' . Sanitize::outString($sciArr['sciname']);
					if($sciArr['rankid'] > 179) $outStr = '<i>' . $outStr . '</i> ';
					$outStr .= Sanitize::outString($sciArr['author']) . '</b> ';
					if(isset($sciArr['rankname'])) $outStr .= '- ' . Sanitize::outString($sciArr['rankname']) . ' rank ';
					if(isset($sciArr['kingdom'])) $outStr .= ' (' . Sanitize::outString($sciArr['kingdom']) . ')';
					echo '<div><a href="tpeditor.php?tid=' . $tidKey . '">' . $outStr . '</a></div>';
				}
				echo '</div>';
			}
			else{
				echo '<div style="margin:15px">';
				if($taxon) echo "<i>" . ucfirst($taxon) . "</i> " . $LANG['NOT_IN_SYSTEM'] . ".";
				echo '</div>';
			}
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
