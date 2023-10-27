<?php
//error_reporting(E_ALL);
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/TaxonomyCleaner.php');
header("Content-Type: text/html; charset=".$CHARSET);
include_once($SERVER_ROOT.'/content/lang/taxa/taxonomy/taxonomycleaner.'.$LANG_TAG.'.php');

$collId = $_REQUEST['collid'];
$displayIndex = array_key_exists('displayindex',$_REQUEST)?$_REQUEST['displayindex']:0;
$analyzeIndex = array_key_exists('analyzeindex',$_REQUEST)?$_REQUEST['analyzeindex']:0;
$taxAuthId = array_key_exists('taxauthid',$_REQUEST)?$_REQUEST['taxauthid']:1;
$action = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';

//Sanitation
if(!is_numeric($collId)) $collId = 0;
if(!is_numeric($displayIndex)) $displayIndex = 0;
if(!is_numeric($analyzeIndex)) $analyzeIndex = 0;
if(!is_numeric($taxAuthId)) $taxAuthId = 1;

$cleanManager = null;
$collName = '';

if($collId){
	$cleanManager = new TaxonomyCleaner();
	$cleanManager->setCollId($collId);
	$collName = $cleanManager->getCollectionName();
}
else{
	$cleanManager = new TaxonomyCleaner();
}
if($taxAuthId){
	$cleanManager->setTaxAuthId($taxAuthId);
}

$isEditor = false;
if($IS_ADMIN){
	$isEditor = true;
}
else{
	if($collId){
		if(array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collId,$USER_RIGHTS["CollAdmin"])){
			$isEditor = true;
		}
	}
	else{
		if(array_key_exists("Taxonomy",$USER_RIGHTS)) $isEditor = true;
	}
}

$status = "";

?>
<html>
	<head>
		<title><?php echo $DEFAULT_TITLE.' '.(isset($LANG['TAX_NAME_CLEANER'])?$LANG['TAX_NAME_CLEANER']:'Taxonomic Name Cleaner'); ?></title>
		<?php

		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<script language="javascript">
			function toggle(divName){
				divObj = document.getElementById(divName);
				if(divObj != null){
					if(divObj.style.display == "block"){
						divObj.style.display = "none";
					}
					else{
						divObj.style.display = "block";
					}
				}
				else{
					divObjs = document.getElementsByTagName("div");
					divObjLen = divObjs.length;
					for(i = 0; i < divObjLen; i++) {
						var obj = divObjs[i];
						if(obj.getAttribute("class") == target || obj.getAttribute("className") == target){
							if(obj.style.display=="none"){
								obj.style.display="inline";
							}
							else {
								obj.style.display="none";
							}
						}
					}
				}
			}

		</script>
	</head>
	<body>
		<?php
		$displayLeftMenu = (isset($taxa_admin_taxonomycleanerMenu)?$taxa_admin_taxonomycleanerMenu:'true');
		include($SERVER_ROOT.'/includes/header.php');
		if(isset($taxa_admin_taxonomycleanerCrumbs)){
			?>
			<div class='navpath'>
				<?php echo $taxa_admin_taxonomycleanerCrumbs; ?>
				<b><?php echo (isset($LANG['TAX_NAME_CLEANER'])?$LANG['TAX_NAME_CLEANER']:'Taxonomic Name Cleaner'); ?></b>
			</div>
			<?php
		}
		?>
		<!-- inner text block -->
		<div id="innertext">
			<?php
			if($SYMB_UID){
				if($status){
					?>
					<div style='float:left;margin:20px 0px 20px 0px;'>
						<hr/>
						<?php echo $status; ?>
						<hr/>
					</div>
					<?php
				}
				if($isEditor){
					if($collId){
						?>
						<h1><?php echo $collName; ?></h1>
						<div>
							<?php echo (isset($LANG['TAX_CLEANER_EXPLAIN'])?$LANG['TAX_CLEANER_EXPLAIN']:'This module is designed to aid in cleaning scientific names that are not mapping to the taxonomic thesaurus. Unmapped names are likely due to misspelllings, illegidimate names, or simply because they just have not yet been added to the thesaurus.'); ?>
						</div>
						<div>
							<?php echo (isset($LANG['NUMBER_MISMAPPED'])?$LANG['NUMBER_MISMAPPED']:'Number of mismapped names').": ".$cleanManager->getTaxaCount(); ?>
						</div>
						<?php
						if(!$action){
							?>
							<form name="occurmainmenu" action="taxonomycleaner.php" method="post">
								<fieldset>
									<legend><b><?php echo (isset($LANG['MAIN_MENU'])?$LANG['MAIN_MENU']:'Main Menu'); ?></b></legend>
									<div>
										<input type="radio" name="submitaction" value="displaynames" />
										<?php echo (isset($LANG['DISPLAY_UNVERIFIED'])?$LANG['DISPLAY_UNVERIFIED']:'Display unverified names'); ?>
										<div style="margin-left:15px;"><?php echo (isset($LANG['START_INDEX'])?$LANG['START_INDEX']:'Start index'); ?>:
											<input name="displayindex" type="text" value="0" style="width:25px;" />
											<?php echo (isset($LANG['500_NAMES'])?$LANG['500_NAMES']:'(500 names at a time)'); ?>
										</div>
									</div>
									<div>
										<input type="radio" name="submitaction" value="analyzenames" />
										<?php echo (isset($LANG['ANALYZE_NAMES'])?$LANG['ANALYZE_NAMES']:'analyze names'); ?>
										<div style="margin-left:15px;"><?php echo (isset($LANG['START_INDEX'])?$LANG['START_INDEX']:'Start index'); ?>:
											<input name="analyzeindex" type="text" value="0" style="width:25px;" />
											<?php echo (isset($LANG['10_NAMES'])?$LANG['10_NAMES']:'(10 names at a time)'); ?>
										</div>
									</div>
									<div>
										<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
										<input type="submit" name="submitbut" value="Perform Action" />
									</div>
								</fieldset>
							</form>
							<?php
						}
						elseif($action == 'displaynames'){
							$nameArr = $cleanManager->getTaxaList($displayIndex);
							echo '<ul>';
							foreach($nameArr as $k => $sciName){
								echo '<li>';
								echo '<a href="spectaxcleaner.php?submitaction=analyzenames&analyzeindex=' . htmlspecialchars($k, HTML_SPECIAL_CHARS_FLAGS) . '">';
								echo '<b><i>'.$sciName.'</i></b>';
								echo '</a>';
								echo '</li>';
							}
							echo '</ul>';
						}
						elseif($action == 'analyzenames'){
							$nameArr = $cleanManager->analyzeTaxa($analyzeIndex);
							echo '<ul>';
							foreach($nameArr as $sn => $snArr){
								echo '<li>'.$sn.'</li>';
								if(array_key_exists('col',$snArr)){

								}
								else{
									echo '<div style="margin-left:15px;font-weight:bold;">';
									echo '<form name="taxaremapform" method="get" action="" >';
									echo (isset($LANG['REMAP_TO'])?$LANG['REMAP_TO']:'Remap to').': ';
									echo '<input type="input" name="remaptaxon" value="'.$sn.'" />';
									echo '<input type="submit" name="submitaction" value="Remap" />';
									echo '</form>';
									echo '</div>';
									if(array_key_exists('soundex',$snArr)){
										foreach($snArr['soundex'] as $t => $s){
											echo '<div style="margin-left:15px;font-weight:bold;">';
											echo $s;
											echo ' <a href="" title="' . htmlspecialchars((isset($LANG['REMAP_TO_NAME'])?$LANG['REMAP_TO_NAME']:'Remap to this name'), HTML_SPECIAL_CHARS_FLAGS) . '...">==>></a>';
											echo '</div>';
										}
									}
								}
							}
							echo '</ul>';
						}
					}
					else{
						?>
						<h1><?php echo (isset($LANG['TAX_THES_VALIDATOR'])?$LANG['TAX_THES_VALIDATOR']:'Taxonomic Thesaurus Validator'); ?></h1>
						<div style="margin:15px;">
							<?php echo (isset($LANG['VALIDATOR_EXPLAIN'])?$LANG['VALIDATOR_EXPLAIN']:'This module is designed to aid in validating scientific names within the taxonomic thesauri'); ?>.
						</div>
						<?php
						$taxonomyAction = array_key_exists('taxonomysubmit',$_POST)?$_POST['taxonomysubmit']:'';
						if($taxonomyAction == 'Validate Names'){
							?>
							<div style="margin:15px;">
								<b><?php echo (isset($LANG['VAL_STATUS'])?$LANG['VAL_STATUS']:'Validation Status'); ?>:</b>
								<ul>
									<?php //$cleanManager->verifyTaxa($_POST['versource']); ?>
								</ul>
							</div>
							<?php
						}
						?>
						<div style="margin:15px;">
							<fieldset>
								<legend><b><?php echo (isset($LANG['VER_STATUS'])?$LANG['VER_STATUS']:'Verification Status'); ?></b></legend>
								<?php
								$vetArr = $cleanManager->getVerificationCounts();
								?>
								<?php echo (isset($LANG['FULL_VER'])?$LANG['FULL_VER']:'Full Verification').': '.$vetArr[1]; ?><br/>
								<?php echo (isset($LANG['SUSPECT_STATUS'])?$LANG['SUSPECT_STATUS']:'Suspect Status').': '.$vetArr[2]; ?><br/>
								<?php echo (isset($LANG['VALIDATE_ONLY'])?$LANG['VALIDATE_ONLY']:'Name Validated Only').': '.$vetArr[3]; ?><br/>
								<?php echo (isset($LANG['UNTESTED'])?$LANG['UNTESTED']:'Untested').': '.$vetArr[0]; ?>
							</fieldset>
						</div>
						<div style="margin:15px;">
							<form name="taxonomymainmenu" action="taxonomycleaner.php" method="post">
								<fieldset>
									<legend><b><?php echo (isset($LANG['MAIN_MENU'])?$LANG['MAIN_MENU']:'Main Menu'); ?></b></legend>
									<div>
										<b><?php echo (isset($LANG['TESTING_RESOURCE'])?$LANG['TESTING_RESOURCE']:'Testing Resource'); ?>:</b><br/>
										<input type="radio" name="versource" value="col" CHECKED />
										<?php echo (isset($LANG['CAT_OF_LIFE'])?$LANG['CAT_OF_LIFE']:'Catalogue of Life'); ?><br/>
									</div>
									<div>
										<input type="hidden" name="taxauthid" value="<?php echo $taxAuthId; ?>" />
										<button type="submit" name="taxonomysubmit" value="Validate Names" ><?php echo (isset($LANG['VALIDATE_NAMES'])?$LANG['VALIDATE_NAMES']:'Validate Names'); ?></button>
									</div>
								</fieldset>
							</form>
						</div>
						<?php
					}
				}
				else{
					?>
					<div style="margin:20px;font-weight:bold;font-size:120%;">
						<?php echo (isset($LANG['ERROR_NOPERM'])?$LANG['ERROR_NOPERM']:'ERROR: You don\'t have the necessary permissions to access this data cleaning module'); ?>.
					</div>
					<?php
				}
			}
			else{
				?>
				<div style="font-weight:bold;">
					<?php echo (isset($LANG['PLEASE'])?$LANG['PLEASE']:'Please')."<a href='../../profile/index.php?refurl=" . htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS) . "/taxa/taxonomy/taxonomycleaner.php?collid=" . htmlspecialchars($collId, HTML_SPECIAL_CHARS_FLAGS) . ">" . htmlspecialchars((isset($LANG['LOGIN'])?$LANG['LOGIN']:'log in'), HTML_SPECIAL_CHARS_FLAGS) . "</a>!" ?>
				</div>
				<?php
			}
			?>
		</div>
		<?php include($SERVER_ROOT.'/includes/footer.php');?>
	</body>
</html>
