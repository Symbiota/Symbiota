<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/TaxonomyUpload.php');
include_once($SERVER_ROOT.'/classes/TaxonomyHarvester.php');
include_once($SERVER_ROOT.'/content/lang/taxa/taxonomy/batchloader.'.$LANG_TAG.'.php');

header('Content-Type: text/html; charset=' . $CHARSET);
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl='.$CLIENT_ROOT.'/taxa/taxonomy/batchloader.php');
ini_set('max_execution_time', 3600);

$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';
$taxAuthId = (array_key_exists('taxauthid', $_REQUEST) ? filter_var($_REQUEST['taxauthid'], FILTER_SANITIZE_NUMBER_INT) : 1);
$kingdomName = (array_key_exists('kingdomname', $_REQUEST) ? filter_var($_REQUEST['kingdomname'], FILTER_SANITIZE_STRING) : '');
$sciname = (array_key_exists('sciname', $_REQUEST) ? filter_var($_REQUEST['sciname'], FILTER_SANITIZE_STRING) : '');
$targetApi = (array_key_exists('targetapi',$_REQUEST) ? filter_var($_REQUEST['targetapi'], FILTER_SANITIZE_STRING) : '');
$rankLimit = (array_key_exists('ranklimit', $_REQUEST) ? filter_var($_REQUEST['ranklimit'], FILTER_SANITIZE_NUMBER_INT):'');

$isEditor = false;
if($IS_ADMIN || array_key_exists('Taxonomy', $USER_RIGHTS)){
	$isEditor = true;
}

$loaderManager = new TaxonomyUpload();
$loaderManager->setTaxaAuthId($taxAuthId);
$loaderManager->setKingdomName($kingdomName);

$fieldMap = Array();
if($isEditor){
	$ulFileName = array_key_exists('ulfilename',$_REQUEST)?$_REQUEST['ulfilename']:'';
	$ulOverride = array_key_exists('uloverride',$_REQUEST)?$_REQUEST['uloverride']:'';
	if($ulFileName){
		$loaderManager->setFileName($ulFileName);
	}
	else{
		$loaderManager->setUploadFile($ulOverride);
	}

	if(array_key_exists("sf",$_REQUEST)){
		//Grab field mapping, if mapping form was submitted
 		$targetFields = $_REQUEST["tf"];
 		$sourceFields = $_REQUEST["sf"];
		for($x = 0;$x<count($targetFields);$x++){
			if($targetFields[$x] && $sourceFields[$x]) $fieldMap[$sourceFields[$x]] = $targetFields[$x];
		}
	}

	if($action == 'downloadcsv'){
		$loaderManager->exportUploadTaxa();
		exit;
	}
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE.' '.(isset($LANG['TAXA_LOADER'])?$LANG['TAXA_LOADER']:'Taxa Loader'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>" />
	<?php
	$activateJQuery = true;
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="../../js/jquery-3.2.1.min.js?ver=3" type="text/javascript"></script>
	<script src="../../js/jquery-ui/jquery-ui.min.js?ver=3" type="text/javascript"></script>
	<link href="../../js/jquery-ui/jquery-ui.min.css" type="text/css" rel="Stylesheet" />
	<script type="text/javascript">
		var clientRoot = "<?php echo $CLIENT_ROOT; ?>";

		function toggle(target){
			var tDiv = document.getElementById(target);
			if(tDiv != null){
				if(tDiv.style.display=="none"){
					tDiv.style.display="block";
				}
			 	else {
			 		tDiv.style.display="none";
			 	}
			}
			else{
			  	var divs = document.getElementsByTagName("div");
			  	for (var i = 0; i < divs.length; i++) {
			  	var divObj = divs[i];
					if(divObj.className == target){
						if(divObj.style.display=="none"){
							divObj.style.display="block";
						}
					 	else {
					 		divObj.style.display="none";
					 	}
					}
				}
			}
		}

		function verifyItisUploadForm(f){
			if(f.uploadfile.value == "" && f.uloverride.value == ""){
				alert("<?php echo (isset($LANG['ENTER_PATH'])?$LANG['ENTER_PATH']:'Please enter a path value of the file you wish to upload'); ?>");
				return false;
			}
			return true;
		}

		function verifyUploadForm(f){
			var inputValue = f.uploadfile.value;
			if(inputValue == "") inputValue = f.uloverride.value;
			if(inputValue == ""){
				alert("<?php echo (isset($LANG['ENTER_PATH'])?$LANG['ENTER_PATH']:'Please enter a path value of the file you wish to upload'); ?>");
				return false;
			}
			else{
				if(inputValue.indexOf(".csv") == -1 && inputValue.indexOf(".CSV") == -1 && inputValue.indexOf(".zip") == -1){
					alert("<?php echo (isset($LANG['UPLOAD_ZIP'])?$LANG['UPLOAD_ZIP']:'Upload file must be a CSV or ZIP file'); ?>");
					return false;
				}
			}
			if(f.kingdomname.value == ""){
				alert("<?php echo (isset($LANG['SEL_KINGDOM'])?$LANG['SEL_KINGDOM']:'Select a Target Kingdom'); ?>");
				return false;
			}
			return true;
		}

		function verifyMapForm(f){
			var sfArr = [];
			var tfArr = [];
			for(var i=0;i<f.length;i++){
				var obj = f.elements[i];
				if(obj.name == "sf[]"){
					if(sfArr.indexOf(obj.value) > -1){
						alert("<?php echo (isset($LANG['ERROR_SOURCE_DUP'])?$LANG['ERROR_SOURCE_DUP']:'ERROR: Source field names must be unique (duplicate field:'); ?>"+" "+obj.value+")");
						return false;
					}
					sfArr[sfArr.length] = obj.value;
				}
				else if(obj.value != "" && obj.value != "unmapped"){
					if(obj.name == "tf[]"){
						if(tfArr.indexOf(obj.value) > -1){
							alert("<?php echo (isset($LANG['ERROR_TARGET'])?$LANG['ERROR_TARGET']:'ERROR: Can\'t map to the same target field more than once'); ?>"+" ("+obj.value+")");
							return false;
						}
						tfArr[tfArr.length] = obj.value;
					}
				}
			}
			return true;
		}

		function checkTransferForm(f){
			return true;
		}

		function validateNodeLoaderForm(f){
			if(f.sciname.value == ""){
				alert("<?php echo (isset($LANG['ENTER_TAX_CODE'])?$LANG['ENTER_TAX_NODE']:'Please enter a valid taxonomic node'); ?>");
				return false;
			}
			if(f.taxauthid.value == ""){
				alert("<?php echo (isset($LANG['SEL_THESAURUS'])?$LANG['SEL_THESAURUS']:'Please select the target taxonomic thesaurus'); ?>");
				return false;
			}
			if(f.kingdomname.value == ""){
				alert("<?php echo (isset($LANG['PLS_SEL_KINGDOM'])?$LANG['PLS_SEL_KINGDOM']:'Please select the target kingdom'); ?>");
				return false;
			}
			if($('input[name=targetapi]:checked').length == 0){
				alert("<?php echo (isset($LANG['SEL_AUTHORITY'])?$LANG['SEL_AUTHORITY']:'Please select a taxonomic authority that will be used to harvest'); ?>");
				return false;
			}
			return true;
		}
	</script>
	<script src="../../js/symb/api.taxonomy.taxasuggest.js?ver=4" type="text/javascript"></script>
	<style type="text/css">
		fieldset { width:90%; padding:10px 15px }
		legend { font-weight:bold; }
	</style>
</head>
<body>
<?php
$displayLeftMenu = (isset($taxa_admin_taxaloaderMenu)?$taxa_admin_taxaloaderMenu:false);
include($SERVER_ROOT.'/includes/header.php');
?>
<div class="navpath">
	<a href="../../index.php"><?php echo (isset($LANG['HOME'])?$LANG['HOME']:'Home'); ?></a> &gt;&gt;
	<a href="taxonomydisplay.php"><?php echo (isset($LANG['BASIC_TREE_VIEWER'])?$LANG['BASIC_TREE_VIEWER']:'Basic Tree Viewer'); ?></a> &gt;&gt;
	<a href="taxonomydynamicdisplay.php"><?php echo (isset($LANG['DYN_TREE_VIEWER'])?$LANG['DYN_TREE_VIEWER']:'Dynamic Tree Viewer'); ?></a> &gt;&gt;
	<a href="batchloader.php"><b><?php echo (isset($LANG['TAX_BATCH_LOADER'])?$LANG['TAX_BATCH_LOADER']:'Taxa Batch Loader'); ?></b></a>
</div>
<?php
if($isEditor){
	$rankArr = $loaderManager->getTaxonRankArr();
	?>
	<div id="innertext">
		<h1><?php echo (isset($LANG['TAX_NAME_BATCH_LOADER'])?$LANG['TAX_NAME_BATCH_LOADER']:'Taxonomic Name Batch Loader'); ?></h1>
		<div style="margin:30px;">
			<div style="margin-bottom:30px;">
				<?php echo (isset($LANG['TAX_UPLOAD_EXPLAIN1'])?$LANG['TAX_UPLOAD_EXPLAIN1']:'This page allows a Taxonomic Administrator to batch upload taxonomic data files. See').' '; ?><a href="https://biokic.github.io/symbiota-docs/portal_manager/taxonomy/batch_load/"><?php echo (isset($LANG['SYMB_DOC'])?$LANG['SYMB_DOC']:'Symbiota Documentation'); ?></a><?php echo ' '.(isset($LANG['TAX_UPLOAD_EXPLAIN2'])?$LANG['TAX_UPLOAD_EXPLAIN2']:'pages for more details on the Taxonomic Thesaurus layout.'); ?>
			</div>
			<?php
			if($action == 'mapInputFile' || $action == 'verifyMapping'){
				?>
				<form name="mapform" action="batchloader.php" method="post" onsubmit="return verifyMapForm(this)">
					<fieldset>
						<legend><?php echo (isset($LANG['TAX_UPLOAD'])?$LANG['TAX_UPLOAD']:'Taxa Upload'); ?></legend>
						<div style="margin:10px;">
						</div>
						<table class="styledtable" style="width:450px">
							<tr>
								<th>
									<?php echo (isset($LANG['SOURCE_FIELD'])?$LANG['SOURCE_FIELD']:'Source Field'); ?>
								</th>
								<th>
									<?php echo (isset($LANG['TARGET_FIELD'])?$LANG['TARGET_FIELD']:'Target Field'); ?>
								</th>
							</tr>
							<?php
							$translationMap = array('phylum'=>'division', 'division'=>'phylum', 'subphylum'=>'subdivision', 'subdivision'=>'subphylum', 'sciname'=>'scinameinput',
								'scientificname'=>'scinameinput', 'scientificnameauthorship'=>'author', 'acceptedname'=>'acceptedstr', 'vernacularname'=>'vernacular');
							$sArr = $loaderManager->getSourceArr();
							$tArr = $loaderManager->getTargetArr();
							asort($tArr);
							foreach($sArr as $sField){
								?>
								<tr>
									<td style='padding:2px;'>
										<?php echo $sField; ?>
										<input type="hidden" name="sf[]" value="<?php echo $sField; ?>" />
									</td>
									<td>
										<select name="tf[]" style="background:<?php echo (array_key_exists($sField,$fieldMap)?"":"yellow");?>">
											<option value=""><?php echo (isset($LANG['FIELD_UNMAPPED'])?$LANG['FIELD_UNMAPPED']:'Field Unmapped'); ?></option>
											<option value="">-------------------------</option>
											<?php
											$selStr = '';
											$mappedTarget = (array_key_exists($sField,$fieldMap)?$fieldMap[$sField]:"");
											if($mappedTarget=='unmapped') $selStr = 'SELECTED';
											echo '<option value="unmapped" '.$selStr.'>'.(isset($LANG['LEAVE_UNMAPPED'])?$LANG['LEAVE_UNMAPPED']:'Leave Field Unmapped').'</option>';
											if($selStr) $selStr = 0;
											foreach($tArr as $k => $tField){
												if($selStr !== 0){
													$sTestField = str_replace(array(' ','_'), '', $sField);
													if($mappedTarget && $mappedTarget == $k){
														$selStr = 'SELECTED';
													}
													elseif($tField==$sTestField && $tField != 'sciname'){
														$selStr = 'SELECTED';
													}
													elseif(isset($translationMap[strtolower($sTestField)]) && $translationMap[strtolower($sTestField)] == $tField){
														$selStr = 'SELECTED';
													}
												}
												echo '<option value="'.$k.'" '.($selStr?$selStr:'').'>'.$tField."</option>\n";
												if($selStr){
													$selStr = 0;
												}
											}
											?>
										</select>
									</td>
								</tr>
								<?php
							}
							?>
						</table>
						<div>
							* <?php echo (isset($LANG['YELLOW_FIELDS'])?$LANG['YELLOW_FIELDS']:'Fields in yellow have not yet been verified'); ?>
						</div>
						<div style="margin-top:10px">
							<?php echo '<b>'.(isset($LANG['TARGET_KINGDOM'])?$LANG['TARGET_KINGDOM']:'Target Kingdom').':</b> '.$kingdomName.'<br/>'; ?>
							<?php echo '<b>'.(isset($LANG['TARGET_THESAURUS'])?$LANG['TARGET_THESAURUS']:'Target Thesaurus').':</b> '.$loaderManager->getTaxAuthorityName(); ?>
						</div>
						<div style="margin:10px;">
							<button type="submit" name="action" value="verifyMapping"><?php echo (isset($LANG['VERIFY_MAPPING'])?$LANG['VERIFY_MAPPING']:'Verify Mapping'); ?></button>
							<button type="submit" name="action" value="uploadTaxa"><?php echo (isset($LANG['UPLOAD_TAXA'])?$LANG['UPLOAD_TAXA']:'Upload Taxa'); ?></button>
							<input type="hidden" name="taxauthid" value="<?php echo $taxAuthId;?>" />
							<input type="hidden" name="ulfilename" value="<?php echo $loaderManager->getFileName();?>" />
							<input type="hidden" name="kingdomname" value="<?php echo $kingdomName; ?>" />
						</div>
					</fieldset>
				</form>
				<?php
			}
			elseif($action == 'uploadTaxa' || $action == 'Upload ITIS File' || $action == 'Analyze Taxa'){
				echo '<ul>';
				if($action == 'uploadTaxa'){
					$loaderManager->loadFile($fieldMap);
					$loaderManager->cleanUpload();
				}
				elseif($action == 'Upload ITIS File'){
					$loaderManager->loadItisFile($fieldMap);
					$loaderManager->cleanUpload();
				}
				elseif($action == 'Analyze Taxa'){
					$loaderManager->cleanUpload();
				}
				$reportArr = $loaderManager->analysisUpload();
				echo '</ul>';
				?>
				<form name="transferform" action="batchloader.php" method="post" onsubmit="return checkTransferForm(this)">
					<fieldset style="width:450px;">
						<legend><?php echo (isset($LANG['TRANSFER_TO_CENTRAL'])?$LANG['TRANSFER_TO_CENTRAL']:'Transfer Taxa To Central Table'); ?></legend>
						<div style="margin:10px;">
							<?php echo (isset($LANG['REVIEW_BEFORE_ACTIVATE'])?$LANG['REVIEW_BEFORE_ACTIVATE']:'Review upload statistics below before activating. Use the download option to review and/or adjust for reload if necessary.'); ?>
						</div>
						<div style="margin:10px">
							<?php echo (isset($LANG['TARGET_KINGDOM'])?$LANG['TARGET_KINGDOM']:'Target Kingdom').': <b>'.$kingdomName.'</b><br/>'; ?>
							<?php echo (isset($LANG['TARGET_THESAURUS'])?$LANG['TARGET_THESAURUS']:'Target Thesaurus').': <b>'.$loaderManager->getTaxAuthorityName().'</b>'; ?>
						</div>
						<div style="margin:10px;">
							<?php
							$statArr = $loaderManager->getStatArr();
							if($statArr){
								if(isset($statArr['upload'])) echo (isset($LANG['TAXA_UPLOADED'])?$LANG['TAXA_UPLOADED']:'Taxa uploaded').': <b>'.$statArr['upload'].'</b><br/>';
								echo (isset($LANG['TOTAL_TAXA'])?$LANG['TOTAL_TAXA']:'Total taxa').': <b>'.$statArr['total'].'</b> ('.(isset($LANG['INCLUDES_PARENTS'])?$LANG['INCLUDES_PARENTS']:'includes new parent taxa').')<br/>';
								echo (isset($LANG['TAXA_IN_THES'])?$LANG['TAXA_IN_THES']:'Taxa already in thesaurus').': <b>'.(isset($statArr['exist'])?$statArr['exist']:0).'</b><br/>';
								echo (isset($LANG['NEW_TAXA'])?$LANG['NEW_TAXA']:'New taxa').': <b>'.(isset($statArr['new'])?$statArr['new']:0).'</b><br/>';
								echo (isset($LANG['ACCEPTED_TAXA'])?$LANG['ACCEPTED_TAXA']:'Accepted taxa').': <b>'.(isset($statArr['accepted'])?$statArr['accepted']:0).'</b><br/>';
								echo (isset($LANG['NON_ACCEPTED_TAXA'])?$LANG['NON_ACCEPTED_TAXA']:'Non-accepted taxa').': <b>'.(isset($statArr['nonaccepted'])?$statArr['nonaccepted']:0).'</b><br/>';
								if(isset($statArr['bad'])){
									?>
									<fieldset style="margin:15px;padding:15px;">
										<legend><b><?php echo (isset($LANG['PROBLEM_TAXA'])?$LANG['PROBLEM_TAXA']:'Problematic taxa'); ?></b></legend>
										<div style="margin-bottom:10px">
											<?php echo (isset($LANG['TAXA_FAILED'])?$LANG['TAXA_FAILED']:'These taxa are marked as FAILED and will not load until problems have been resolved. You may want to download the data (link below), fix the bad relationships, and then reload.'); ?>
										</div>
										<?php
										foreach($statArr['bad'] as $msg => $cnt){
											echo '<div style="margin-left:10px">'.$msg.': <b>'.$cnt.'</b></div>';
										}
										?>
									</fieldset>
									<?php
								}
							}
							else{
								echo (isset($LANG['STATS_NOT_AVAIL'])?$LANG['STATS_NOT_AVAIL']:'Upload statistics are unavailable');
							}
							?>
						</div>
						<!--
						<div style="margin:10px;">
							<label>Target Thesaurus:</label>
							<select name="taxauthid">
								<?php
								$taxonAuthArr = $loaderManager->getTaxAuthorityArr();
								foreach($taxonAuthArr as $k => $v){
									echo '<option value="'.$k.'" '.($k==$taxAuthId?'SELECTED':'').'>'.$v.'</option>'."\n";
								}
								?>
							</select>
						</div>
						-->
						<div style="margin:10px;">
							<input type="hidden" name="taxauthid" value="<?php echo $taxAuthId;?>" />
							<input name="kingdomname" type="hidden" value="<?php echo $kingdomName; ?>" />
							<button type="submit" name="action" value="activateTaxa"><?php echo (isset($LANG['ACTIVATE_TAXA'])?$LANG['ACTIVATE_TAXA']:'Activate Taxa'); ?></button>
						</div>
						<div style="float:right;margin:10px;">
							<a href="batchloader.php?action=downloadcsv" target="_blank"><?php echo (isset($LANG['DOWNLOAD_CSV'])?$LANG['DOWNLOAD_CSV']:'Download CSV Taxa File'); ?></a>
						</div>
					</fieldset>
				</form>
				<?php
			}
			elseif($action == 'activateTaxa'){
				echo '<ul>';
				$loaderManager->transferUpload($taxAuthId);
				echo "<li>".(isset($LANG['TAX_UPLOAD_SUCCESS'])?$LANG['TAX_UPLOAD_SUCCESS']:'Taxa upload appears to have been successful').".</li>";
				echo "<li>".(isset($LANG['GO_TO'])?$LANG['GO_TO']:'Go to')." <a href='taxonomydisplay.php'>".(isset($LANG['TAX_TREE_SEARCH'])?$LANG['TAX_TREE_SEARCH']:'Taxonomic Tree Search').'</a> '.(isset($LANG['TO_QUERY'])?$LANG['TO_QUERY']:'page to query for a loaded name').".</li>";
				echo '</ul>';
			}
			elseif($action == 'loadApiNode'){
				if($targetApi == 'col'){
					$harvester = new TaxonomyHarvester();
					$harvester->setVerboseMode(2);
					$harvester->setTaxAuthId($taxAuthId);
					$harvester->setKingdomName($kingdomName);
					if(isset($_REQUEST['dskey'])){
						echo '<fieldset>';
						echo '<legend>'.(isset($LANG['ACTION_PANEL'])?$LANG['ACTION_PANEL']:'Action Panel').'</legend>';
						$id = $_REQUEST['id'];
						if(!preg_match('/^[A-Za-z\d]+$/',$id)) $id = 0;
						$datasetKey = (is_numeric($_REQUEST['dskey'])?$_REQUEST['dskey']:0);
						$harvester->addColNode($id, $datasetKey, $sciname, $rankLimit);
						echo '</fieldset>';
					}
					else{
						$targetArr = $harvester->fetchColNode($sciname);
						echo '<fieldset>';
						echo '<legend>'.(isset($LANG['RESULT_TARGETS'])?$LANG['RESULT_TARGETS']:'Result Targets').'</legend>';
						if($targetArr){
							$numResults = $targetArr['number_results'];
							unset($targetArr['number_results']);
							echo '<div><b>'.(isset($LANG['TARGET_TAXON'])?$LANG['TARGET_TAXON']:'Target taxon').':</b> '.$sciname.'</div>';
							echo '<div><b>'.(isset($LANG['KINGDOM'])?$LANG['KINGDOM']:'Kingdom').':</b> '.substr($kingdomName,strpos($kingdomName,':')+1).'</div>';
							echo '<div><b>'.(isset($LANG['LOWEST_RANK'])?$LANG['LOWEST_RANK']:'Lowest rank limit').':</b> '.$rankArr[$rankLimit].'</div>';
							echo '<div><b>'.(isset($LANG['SOURCE_LINK'])?$LANG['SOURCE_LINK']:'Source link').':</b> <a href="https://www.catalogueoflife.org" target="_blank">https://www.catalogueoflife.org</a></div>';
							echo '<div><b>'.(isset($LANG['TOTAL_RESULTS'])?$LANG['TOTAL_RESULTS']:'Total results').':</b> '.$numResults.'</div>';
							echo '<div><hr/></div>';
							foreach($targetArr as $colID => $colArr){
								echo '<div style="margin-top:10px">';
								echo '<div><b>'.(isset($LANG['ID'])?$LANG['ID']:'ID').':</b> '.$colID.'</div>';
								if(isset($colArr['error'])){
									echo '<div>'.(isset($LANG['ERROR'])?$LANG['ERROR']:'ERROR').': '.$colArr['error'].'</div>';
								}
								else{
									echo '<div>'.(isset($LANG['NAME'])?$LANG['NAME']:'Name').': '.$colArr['label'].'</div>';
									echo '<div>'.(isset($LANG['DATSET_KEY'])?$LANG['DATSET_KEY']:'Dataset key').': <a href="https://api.catalogueoflife.org/dataset/'.$colArr['datasetKey'].'" target="_blank">'.$colArr['datasetKey'].'</a></div>';
									echo '<div>'.(isset($LANG['STATUS'])?$LANG['STATUS']:'Status').': '.$colArr['status'].'</div>';
									if(isset($colArr['accordingTo'])) echo '<div>'.(isset($LANG['ACC_TO'])?$LANG['ACC_TO']:'According to').': '.$colArr['accordingTo'].'</div>';
									if(isset($colArr['link'])) echo '<div>'.(isset($LANG['SOURCE_LINK'])?$LANG['SOURCE_LINK']:'Source link').': <a href="'.$colArr['link'].'" target="_blank">'.$colArr['link'].'</a></div>';
									if(isset($colArr['scrutinizer'])) echo '<div>'.(isset($LANG['SCRUTINIZER'])?$LANG['SCRUTINIZER']:'Scrutinizer').': '.$colArr['scrutinizer'].'</div>';
									$targetStatus = '<span style="color:orange">'.(isset($LANG['NOT_PREF'])?$LANG['NOT_PREF']:'not preferred').'</span>';
									if($colArr['isPreferred']) $targetStatus = '<span style="color:green">'.(isset($LANG['PREF_TARGET'])?$LANG['PREF_TARGET']:'preferred target').'</span>';
									echo '<div>'.(isset($LANG['TARGET_STATUS'])?$LANG['TARGET_STATUS']:'Target status').': '.$targetStatus.'</div>';
									if(isset($colArr['webServiceUrl'])) echo '<div>'.(isset($LANG['WEB_SERVICE_URL'])?$LANG['WEB_SERVICE_URL']:'Web Service URL').': <a href="'.$colArr['webServiceUrl'].'" target="_blank">'.$colArr['webServiceUrl'].'</a></div>';
									if(isset($colArr['apiUrl'])) echo '<div>'.(isset($LANG['API_URL'])?$LANG['API_URL']:'API URL').': <a href="'.$colArr['apiUrl'].'" target="_blank">'.$colArr['apiUrl'].'</a></div>';
									echo '<div>'.(isset($LANG['COL_URL'])?$LANG['COL_URL']:'CoL URL').': <a href="'.$colArr['colUrl'].'" target="_blank">'.$colArr['colUrl'].'</a></div>';
									$harvestLink = 'batchloader.php?id='.$colID.'&dskey='.$colArr['datasetKey'].'&targetapi=col&taxauthid='.$_POST['taxauthid'].
										'&kingdomname='.$_POST['kingdomname'].'&ranklimit='.$_POST['ranklimit'].'&sciname='.$sciname.'&action=loadApiNode';
									if($colArr['datasetKey']) echo '<div><b><a href="'.$harvestLink.'">'.(isset($LANG['TARGET_THIS_NODE'])?$LANG['TARGET_THIS_NODE']:'Target this node to harvest children').'</a></b></div>';
								}
								echo '</div>';
							}
						}
						else{
							echo (isset($LANG['NO_VALID_COL'])?$LANG['NO_VALID_COL']:'ERROR: no valid CoL targets returned');
							return false;
						}
						echo '</fieldset>';
					}
				}
				elseif($targetApi == 'worms'){
					$harvester = new TaxonomyHarvester();
					$harvester->setVerboseMode(2);
					$harvester->setTaxAuthId($taxAuthId);
					$harvester->setKingdomName($kingdomName);
					echo '<ul>';
					if($harvester->addWormsNode($_POST)){
						echo '<li>'.$harvester->getTransactionCount().' '.(isset($LANG['TAXA_LOADED_SUCCESS'])?$LANG['TAXA_LOADED_SUCCESS']:'taxa within the target node have been loaded successfully').'</li>';
						echo '<li>'.(isset($LANG['GO_TO'])?$LANG['GO_TO']:'Go to').' <a href="taxonomydisplay.php">'.(isset($LANG['TAX_TREE_SEARCH'])?$LANG['TAX_TREE_SEARCH']:'Taxonomic Tree Search').'</a> '.(isset($LANG['TO_QUERY'])?$LANG['TO_QUERY']:'page to query for a loaded name').'</li>';
					}
					echo '</ul>';
				}
			}
			?>
			<div>
				<form name="uploadform" action="batchloader.php" method="post" enctype="multipart/form-data" onsubmit="return verifyUploadForm(this)">
					<fieldset>
						<legend><?php echo (isset($LANG['TAX_UPLOAD'])?$LANG['TAX_UPLOAD']:'Taxa Upload'); ?></legend>
						<div style="margin:10px;">
							<?php echo (isset($LANG['TAX_UPLOAD_INSTRUCTIONS'])?$LANG['TAX_UPLOAD_INSTRUCTIONS']:'
							Flat structured, CSV (comma delimited) text files can be uploaded here.
							Scientific name is the only required field below genus rank.
							However, family, author, and rankid (as defined in taxonunits table) are always advised.
							For upper level taxa, parents and rankids need to be included in order to build the taxonomic hierarchy.
							Large data files can be compressed as a ZIP file before import.
							If the file upload step fails without displaying an error message, it is possible that the
							file size exceeds the file upload limits set within your PHP installation (see your php configuration file).
							'); ?>
						</div>
						<input type='hidden' name='MAX_FILE_SIZE' value='100000000' />
						<div>
							<div class="overrideopt">
								<div style="margin:10px;">
									<input id="genuploadfile" name="uploadfile" type="file" size="40" />
								</div>
							</div>
							<div class="overrideopt" style="display:none;">
								<label><?php echo (isset($LANG['FULL_FILE_PATH'])?$LANG['FULL_FILE_PATH']:'Full File Path'); ?>:</label>
								<div style="margin:10px;">
									<input name="uloverride" type="text" size="50" /><br/>
									* <?php echo (isset($LANG['FULL_FILE_EXPLAIN'])?$LANG['FULL_FILE_EXPLAIN']:'This option is for manual upload of a data file. Enter full path to data file located on working server.'); ?>
								</div>
							</div>
							<div style="margin:10px;">
								<label><?php echo (isset($LANG['TARGET_THESAURUS'])?$LANG['TARGET_THESAURUS']:'Target Thesaurus'); ?>:</label>
								<select name="taxauthid">
									<?php
									$taxonAuthArr = $loaderManager->getTaxAuthorityArr();
									foreach($taxonAuthArr as $k => $v){
										echo '<option value="'.$k.'" '.($k==$taxAuthId?'SELECTED':'').'>'.$v.'</option>'."\n";
									}
									?>
								</select>
							</div>
							<div style="margin:10px;">
								<label><?php echo (isset($LANG['TARGET_KINGDOM'])?$LANG['TARGET_KINGDOM']:'Target Kingdom'); ?>:</label>
								<?php
								$kingdomArr = $loaderManager->getKingdomArr();
								echo '<select name="kingdomname">';
								echo '<option value="">'.(isset($LANG['SEL_KINGDOM'])?$LANG['SEL_KINGDOM']:'Select Kingdom').'</option>';
								echo '<option value="">----------------------</option>';
								foreach($kingdomArr as $k => $kingName){
									echo '<option>'.$kingName.'</option>';
								}
								echo '</select>';
								?>
							</div>
							<div style="margin:10px;">
								<button type="submit" name="action" value="mapInputFile"><?php echo (isset($LANG['MAP_INPUT_FILE'])?$LANG['MAP_INPUT_FILE']:'Map Input File'); ?></button>
							</div>
							<div style="float:right;" >
								<a href="#" onclick="toggle('overrideopt');return false;"><?php echo (isset($LANG['TOGGLE_MANUAL'])?$LANG['TOGGLE_MANUAL']:'Toggle Manual Upload Option'); ?></a>
							</div>
						</div>
					</fieldset>
				</form>
			</div>
			<!--
			<div>
				<form name="itisuploadform" action="batchloader.php" method="post" enctype="multipart/form-data" onsubmit="return verifyItisUploadForm(this)">
					<fieldset>
						<legend>ITIS Upload File</legend>
						<div style="margin:10px;">
							ITIS data extract from the <a href="http://www.itis.gov/access.html" target="_blank">ITIS Download Page</a> can be uploaded
							using this function. Note that the file needs to be in their single file pipe-delimited format
							(example: <a href="CyprinidaeItisExample.bin">CyprinidaeItisExample.bin</a>).
							File might have .csv extension, even though it is NOT comma delimited.
							This upload option is not guaranteed to work if the ITIS download format change often.
							Large data files can be compressed as a ZIP file before import.
							If the file upload step fails without displaying an error message, it is possible that the
							file size exceeds the file upload limits set within your PHP installation (see your php configuration file).
							If synonyms and vernaculars are included, these data will also be incorporated into the upload process.
						</div>
						<input type='hidden' name='MAX_FILE_SIZE' value='100000000' />
						<div class="itisoverrideopt">
							<b>Upload File:</b>
							<div style="margin:10px;">
								<input id="itisuploadfile" name="uploadfile" type="file" size="40" />
							</div>
						</div>
						<div class="itisoverrideopt" style="display:none;">
							<b>Full File Path:</b>
							<div style="margin:10px;">
								<input name="uloverride" type="text" size="50" /><br/>
								* This option is for manual upload of a data file.
								Enter full path to data file located on working server.
							</div>
						</div>
						<div style="margin:10px;">
							<input type="submit" name="action" value="Upload ITIS File" />
						</div>
						<div style="float:right;">
							<a href="#" onclick="toggle('itisoverrideopt');return false;">Toggle Manual Upload Option</a>
						</div>
					</fieldset>
				</form>
			</div>
			-->
			<div>
				<form name="analyzeform" action="batchloader.php" method="post">
					<fieldset>
						<legend><?php echo (isset($LANG['CLEAN_ANALYZE'])?$LANG['CLEAN_ANALYZE']:'Clean and Analyze'); ?></legend>
						<div style="margin:10px;">
							<?php echo (isset($LANG['CLEAN_ANALYZE_EXPLAIN'])?$LANG['CLEAN_ANALYZE_EXPLAIN']:'If taxa information was loaded into the UploadTaxa table using other means, one can use this form to clean and analyze taxa names in preparation to loading into the taxonomic tables (taxa, taxstatus).'); ?>
						</div>
						<div style="margin:10px;">
							<label><?php echo (isset($LANG['TARGET_THESAURUS'])?$LANG['TARGET_THESAURUS']:'Target Thesaurus'); ?>:</label>
							<select name="taxauthid">
								<?php
								$taxonAuthArr = $loaderManager->getTaxAuthorityArr();
								foreach($taxonAuthArr as $k => $v){
									echo '<option value="'.$k.'" '.($k==$taxAuthId?'SELECTED':'').'>'.$v.'</option>'."\n";
								}
								?>
							</select>
						</div>
						<div style="margin:10px;">
							<label><?php echo (isset($LANG['TARGET_KINGDOM'])?$LANG['TARGET_KINGDOM']:'Target Kingdom'); ?>:</label>
							<?php
							echo '<select name="kingdomname">';
							foreach($kingdomArr as $k => $kingName){
								echo '<option>'.$kingName.'</option>';
							}
							echo '</select>';
							?>
						</div>
						<div style="margin:10px;">
							<button type="submit" name="action" value="Analyze Taxa"><?php echo (isset($LANG['ANALYZE_TAXA'])?$LANG['ANALYZE_TAXA']:'Analyze Taxa'); ?></button>
						</div>
					</fieldset>
				</form>
			</div>
			<div>
				<fieldset>
					<legend><?php echo (isset($LANG['API_NODE_LOADER'])?$LANG['API_NODE_LOADER']:'API Node Loader'); ?></legend>
					<form name="apinodeloaderform" action="batchloader.php" method="post" onsubmit="return validateNodeLoaderForm(this)">
						<div style="margin:10px;">
							<?php echo (isset($LANG['API_NODE_LOADER_EXPLAIN'])?$LANG['API_NODE_LOADER_EXPLAIN']:'This form will batch load a taxonomic node from a selected Taxonomic Authority via their API resources.<br/>
							This function currently only works for Catalog of Life and WoRMS.'); ?>
						</div>
						<div style="margin:10px;">
							<fieldset style="padding:15px;margin:10px 0px">
								<legend><b><?php echo (isset($LANG['TAX_RESOURCE'])?$LANG['TAX_RESOURCE']:'Taxonomic Resource'); ?></b></legend>
								<?php
								$taxApiList = $loaderManager->getTaxonomicResourceList();
								foreach($taxApiList as $taKey => $taValue){
									echo '<input name="targetapi" type="radio" value="'.$taKey.'" '.($targetApi == $taKey?'checked':'').' /> '.$taValue.'<br/>';
								}
								?>
							</fieldset>
						</div>
						<div style="margin:10px;">
							<label><?php echo (isset($LANG['TARGET_NODE'])?$LANG['TARGET_NODE']:'Target node'); ?>:</label>
							<input id="taxa" name="sciname" type="text" value="<?php echo $sciname; ?>" />
						</div>
						<div style="margin:10px;">
							<label><?php echo (isset($LANG['TAX_THESAURUS'])?$LANG['TAX_THESAURUS']:'Taxonomic Thesaurus'); ?>:</label>
							<select name="taxauthid">
								<?php
								$taxonAuthArr = $loaderManager->getTaxAuthorityArr();
								foreach($taxonAuthArr as $k => $v){
									echo '<option value="'.$k.'" '.($k==$taxAuthId?'SELECTED':'').'>'.$v.'</option>'."\n";
								}
								?>
							</select>
						</div>
						<div style="margin:10px;">
							<label><?php echo (isset($LANG['KINGDOM'])?$LANG['KINGDOM']:'Kingdom'); ?>:</label>
							<select name="kingdomname">
								<?php
								if($kingdomArr > 1){
									echo '<option value="">'.(isset($LANG['SEL_KINGDOM'])?$LANG['SEL_KINGDOM']:'Select Target Kingdom').'</option>';
									echo '<option value="">-----------------------</option>';
								}
								foreach($kingdomArr as $k => $kName){
									$kKey = $k.':'.$kName;
									echo '<option value="'.$kKey.'" '.($kingdomName==$kKey?'selected':'').'>'.$kName.'</option>';
								}
								?>
							</select>
						</div>
						<div style="margin:10px;">
							<label><?php echo (isset($LANG['LOWEST_RANK'])?$LANG['LOWEST_RANK']:'Lowest Rank Limit'); ?></label>
							<select name="ranklimit">
								<option value="0"><?php echo (isset($LANG['ALL_RANKS'])?$LANG['ALL_RANKS']:'All Taxon Ranks'); ?></option>
								<option>---------------------</option>
								<?php
								foreach($rankArr as $rankid => $rankName){
									echo '<option value="'.$rankid.'" '.($rankid==$rankLimit?'SELECTED':'').'>'.$rankName.'</option>';
								}
								?>
							</select>
						</div>
						<div style="margin:10px;">
							<button id="submitButton" type="submit" name="action" value="loadApiNode"><?php echo (isset($LANG['LOAD_NODE'])?$LANG['LOAD_NODE']:'Load node'); ?></button>
						</div>
					</form>
				</fieldset>
			</div>
		</div>
	</div>
	<?php
}
else{
	?>
	<div style='font-weight:bold;margin:30px;'>
		<?php echo (isset($LANG['NO_PERMISSIONS'])?$LANG['NO_PERMISSIONS']:'You do not have permissions to batch upload taxonomic data'); ?>
	</div>
	<?php
}
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>