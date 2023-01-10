<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/TaxonomyEditorManager.php');
header("Content-Type: text/html; charset=".$CHARSET);
include_once($SERVER_ROOT.'/content/lang/taxa/taxonomy/taxoneditor.'.$LANG_TAG.'.php');

if(!$SYMB_UID) header('Location: '.$CLIENT_ROOT.'/profile/index.php?refurl=../taxa/taxonomy/taxoneditor.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$tid = $_REQUEST["tid"];
$taxAuthId = array_key_exists('taxauthid', $_REQUEST)?$_REQUEST['taxauthid']:1;
$tabIndex = array_key_exists('tabindex',$_REQUEST)?$_REQUEST['tabindex']:0;
$submitAction = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';

//Sanitation
if(!is_numeric($tid)) $tid = 0;
if(!is_numeric($taxAuthId)) $taxAuthId = 1;
if(!is_numeric($tabIndex)) $tabIndex = 0;

$taxonEditorObj = new TaxonomyEditorManager();
$taxonEditorObj->setTid($tid);
$taxonEditorObj->setTaxAuthId($taxAuthId);

$isEditor = false;
if($IS_ADMIN || array_key_exists("Taxonomy",$USER_RIGHTS)) $isEditor = true;

$statusStr = '';
if($isEditor){
	if(array_key_exists('taxonedits',$_POST)){
		$statusStr = $taxonEditorObj->submitTaxonEdits($_POST);
	}
	elseif($submitAction == 'updatetaxstatus'){
		$statusStr = $taxonEditorObj->submitTaxStatusEdits($_POST['parenttid'],$_POST['tidaccepted']);
	}
	elseif(array_key_exists("synonymedits",$_REQUEST)){
		$statusStr = $taxonEditorObj->submitSynonymEdits($_POST['tidsyn'], $tid, $_POST['unacceptabilityreason'], $_POST['notes'], $_POST['sortsequence']);
	}
	elseif($submitAction == 'linkToAccepted'){
		$deleteOther = array_key_exists("deleteother",$_REQUEST)?true:false;
		$statusStr = $taxonEditorObj->submitAddAcceptedLink($_REQUEST["tidaccepted"],$deleteOther);
	}
	elseif(array_key_exists('deltidaccepted',$_REQUEST)){
		$statusStr = $taxonEditorObj->removeAcceptedLink($_REQUEST['deltidaccepted']);
	}
	elseif(array_key_exists("changetoaccepted",$_REQUEST)){
		$tidAccepted = $_REQUEST["tidaccepted"];
		$switchAcceptance = array_key_exists("switchacceptance",$_REQUEST)?true:false;
		$statusStr = $taxonEditorObj->submitChangeToAccepted($tid,$tidAccepted,$switchAcceptance);
	}
	elseif($submitAction == 'changeToNotAccepted'){
		$tidAccepted = $_REQUEST["tidaccepted"];
		$statusStr = $taxonEditorObj->submitChangeToNotAccepted($tid,$tidAccepted,$_POST['unacceptabilityreason'],$_POST['notes']);
	}
	elseif($submitAction == 'updatehierarchy'){
		$statusStr = $taxonEditorObj->rebuildHierarchy($tid);
	}
	elseif($submitAction == 'remapTaxon'){
		$remapStatus = $taxonEditorObj->transferResources($_REQUEST['remaptid']);
		if($taxonEditorObj->getWarningArr()) $statusStr = (isset($LANG['FOLLOWING_WARNINGS'])?$LANG['FOLLOWING_WARNINGS']:'The following warnings occurred').': '.implode(';',$taxonEditorObj->getWarningArr());
		if($remapStatus){
			$statusStr = (isset($LANG['SUCCESS_REMAPPING'])?$LANG['SUCCESS_REMAPPING']:'Success remapping taxon!').' '.$statusStr;
			header('Location: taxonomydisplay.php?target='.$_REQUEST["genusstr"].'&statusstr='.$statusStr);
		}
		else $statusStr = $taxonEditorObj->getErrorMessage();
	}
	elseif($submitAction == 'deleteTaxon'){
		$delStatus = $taxonEditorObj->deleteTaxon();
		if($taxonEditorObj->getWarningArr()) $statusStr = (isset($LANG['FOLLOWING_WARNINGS'])?$LANG['FOLLOWING_WARNINGS']:'The following warnings occurred').': '.implode(';',$taxonEditorObj->getWarningArr());
		if($delStatus){
			$statusStr = (isset($LANG['SUCCESS_DELETING'])?$LANG['SUCCESS_DELETING']:'Success deleting taxon!').' '.$statusStr;
			header('Location: taxonomydisplay.php?statusstr='.$statusStr);
		}
		else $statusStr = $taxonEditorObj->getErrorMessage();
	}
	$taxonEditorObj->setTaxon();
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE." ".(isset($LANG['TAX_EDITOR'])?$LANG['TAX_EDITOR']:'Taxon Editor').": ".$tid; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>"/>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="../../js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="../../js/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
	<link href="../../js/jquery-ui/jquery-ui.min.css" type="text/css" rel="Stylesheet" />
	<script>
		var tid = <?php echo $taxonEditorObj->getTid(); ?>;
		var tabIndex = <?php echo $tabIndex; ?>;
	</script>
	<script src="../../js/symb/taxa.taxonomyeditor.js?ver=2"></script>
	<style type="text/css">
		.editDiv{ clear:both; }
		.editLabel{ float:left; font-weight:bold; }
		.editfield{ float:left; margin-left:5px; }
		.tsedit{ float:left; margin-left:5px; }
		.headingDiv{ font-size:110%;font-weight:bold;font-style:italic }
	</style>
</head>
<body>
<?php
	$displayLeftMenu = (isset($taxa_admin_taxonomyeditorMenu)?$taxa_admin_taxonomyeditorMenu:"true");
	include($SERVER_ROOT.'/includes/header.php');
	if(isset($taxa_admin_taxonomyeditorCrumbs)){
		if($taxa_admin_taxonomyeditorCrumbs){
			echo "<div class='navpath'>";
			echo $taxa_admin_taxonomyeditorCrumbs;
			echo " <b>Taxonomy Editor</b>";
			echo "</div>";
		}
	}
	else{
		?>
		<div class="navpath">
			<a href="../../index.php"><?php echo (isset($LANG['HOME'])?$LANG['HOME']:'Home'); ?></a> &gt;&gt;
			<a href="taxonomydisplay.php"><?php echo (isset($LANG['TAX_TREE_VIEW'])?$LANG['TAX_TREE_VIEW']:'Taxonomy Tree Viewer'); ?></a> &gt;&gt;
			<b><?php echo (isset($LANG['TAXONOMY_EDITOR'])?$LANG['TAXONOMY_EDITOR']:'Taxonomy Editor'); ?></b>
		</div>
		<?php
	}
	?>
	<!-- This is inner text! -->
	<div id="innertext">
		<?php
		if($statusStr){
			?>
			<hr/>
			<div style="color:<?php echo (strpos($statusStr,(isset($LANG['SUCCESS'])?$LANG['SUCCESS']:'SUCCESS')) !== false?'green':'red'); ?>;margin:15px;">
				<?php echo $statusStr; ?>
			</div>
			<hr/>
			<?php
		}
		if($isEditor && $tid){
			$hierarchyArr = $taxonEditorObj->getHierarchyArr()
			?>
			<div style="float:right;" title="<?php echo (isset($LANG['GO_TAX_DISPLAY'])?$LANG['GO_TAX_DISPLAY']:'Go to taxonomy display'); ?>">
				<a href="taxonomydisplay.php?target=<?php echo $taxonEditorObj->getUnitName1();?>&showsynonyms=1">
					<img style='border:0px;width:15px;' src='../../images/toparent.png'/>
				</a>
			</div>
			<div style="float:right;" title="<?php echo (isset($LANG['ADD_NEW_TAXON'])?$LANG['ADD_NEW_TAXON']:'Add a New Taxon'); ?>">
				<a href="taxonomyloader.php">
					<img style='border:0px;width:15px;' src='../../images/add.png'/>
				</a>
			</div>
			<h1>
				<?php
				echo "<a href='../profile/tpeditor.php?tid=".$taxonEditorObj->getTid()."' style='color:inherit;text-decoration:none;'>";
				echo "<i>".$taxonEditorObj->getSciName()."</i> ".$taxonEditorObj->getAuthor()." [".$taxonEditorObj->getTid()."]";
				echo "</a>"
				?>
			</h1>
			<div id="tabs" class="taxondisplaydiv">
				<ul>
					<li><a href="#editorDiv"><?php echo (isset($LANG['EDITOR'])?$LANG['EDITOR']:'Editor'); ?></a></li>
					<li><a href="#taxonstatusdiv"><?php echo (isset($LANG['TAX_STATUS'])?$LANG['TAX_STATUS']:'Taxonomic Status'); ?></a></li>
					<li><a href="#hierarchydiv"><?php echo (isset($LANG['HIERARCHY'])?$LANG['HIERARCHY']:'Hierarchy'); ?></a></li>
					<li><a href="taxonomychildren.php?tid=<?php echo $tid.'&taxauthid='.$taxAuthId; ?>"><?php echo (isset($LANG['CHILDREN_TAXA'])?$LANG['CHILDREN_TAXA']:'Children Taxa'); ?></a></li>
					<li><a href="taxonomydelete.php?tid=<?php echo $tid; ?>&genusstr=<?php echo $taxonEditorObj->getUnitName1(); ?>"><?php echo (isset($LANG['DELETE'])?$LANG['DELETE']:'Delete'); ?></a></li>
				</ul>
				<div id="editorDiv" style="height:400px;">
					<div style="float:right;cursor:pointer;" onclick="toggleEditFields()" title="Toggle Taxon Editing Functions">
						<img style='border:0px;' src='../../images/edit.png'/>
					</div>
					<form id="taxoneditform" name="taxoneditform" action="taxoneditor.php" method="post" onsubmit="return validateTaxonEditForm(this)">
						<div class="editDiv">
							<div class="editLabel"><?php echo (isset($LANG['UNITNAME1'])?$LANG['UNITNAME1']:'UnitName1'); ?>: </div>
							<div class="editfield">
								<?php
								$unitInd1 = $taxonEditorObj->getUnitInd1();
								echo ($unitInd1?$unitInd1.' ':'').$taxonEditorObj->getUnitName1();
								?>
							</div>
							<div class="editfield" style="display:none;">
								<select name="unitind1">
									<option value=""></option>
									<option value="&#215;" <?php echo ($unitInd1 && (mb_ord($unitInd1)==215 || strtolower($unitInd1) == 'x')?'selected':''); ?>>&#215;</option>
									<option value="&#8224;" <?php echo ($unitInd1 && mb_ord($unitInd1)==8224?'selected':''); ?>>&#8224;</option>
								</select>
								<input type="text" id="unitname1" name="unitname1" style="width:300px;border-style:inset;" value="<?php echo $taxonEditorObj->getUnitName1(); ?>" />
							</div>
						</div>
						<div class="editDiv">
							<div class="editLabel"><?php echo (isset($LANG['UNITNAME2'])?$LANG['UNITNAME2']:'UnitName2'); ?>: </div>
							<div class="editfield">
								<?php
								$unitInd2 = $taxonEditorObj->getUnitInd2();
								echo ($unitInd2?$unitInd2.' ':'').$taxonEditorObj->getUnitName2();
								?>
							</div>
							<div class="editfield" style="display:none;">
								<select name="unitind2">
									<option value=""></option>
									<option value="&#215;" <?php echo (ord($unitInd2)==195 || strtolower($unitInd2) == 'x'?'selected':''); ?>>&#215;</option>
								</select>
								<input type="text" id="unitname2" name="unitname2" style="width:300px;border-style:inset;" value="<?php echo $taxonEditorObj->getUnitName2(); ?>" />
							</div>
						</div>
						<div class="editDiv">
							<div class="editLabel"><?php echo (isset($LANG['UNITNAME3'])?$LANG['UNITNAME3']:'UnitName3'); ?>: </div>
							<div class="editfield">
								<?php echo $taxonEditorObj->getUnitInd3()." ".$taxonEditorObj->getUnitName3();?>
							</div>
							<div class="editfield" style="display:none;">
								<input type="text" id="unitind3" name="unitind3" style="width:50px;border-style:inset;" value="<?php echo $taxonEditorObj->getUnitInd3(); ?>" />
								<input type="text" id="unitname3" name="unitname3" style="width:300px;border-style:inset;" value="<?php echo $taxonEditorObj->getUnitName3(); ?>" />
							</div>
						</div>
						<div class="editDiv">
							<div class="editLabel"><?php echo (isset($LANG['AUTHOR'])?$LANG['AUTHOR']:'Author'); ?>: </div>
							<div class="editfield">
								<?php echo htmlspecialchars($taxonEditorObj->getAuthor());?>
							</div>
							<div class="editfield" style="display:none;">
								<input type="text" id="author" name="author" style="width:400px;border-style:inset;" value="<?php echo htmlspecialchars($taxonEditorObj->getAuthor()); ?>" />
							</div>
						</div>
						<div id="kingdomdiv" class="editDiv">
							<div  class="editLabel"><?php echo (isset($LANG['KINGDOM'])?$LANG['KINGDOM']:'Kingdom'); ?>: </div>
							<div class="editfield">
								<?php
								echo $taxonEditorObj->getKingdomName();
								?>
							</div>
						</div>
						<div class="editDiv">
							<div class="editLabel"><?php echo (isset($LANG['RANK_NAME'])?$LANG['RANK_NAME']:'Rank Name'); ?>: </div>
							<div class="editfield">
								<?php echo ($taxonEditorObj->getRankName()?$taxonEditorObj->getRankName():'Non-Ranked Node'); ?>
							</div>
							<div class="editfield" style="display:none;">
								<select id="rankid" name="rankid">
									<option value="0"><?php echo (isset($LANG['NON_RANKED_NODE'])?$LANG['NON_RANKED_NODE']:'Non-Ranked Node'); ?></option>
									<option value="">---------------------------------</option>
									<?php
									$rankArr = $taxonEditorObj->getRankArr();
									foreach($rankArr as $rankId => $nameArr){
										foreach($nameArr as $rName){
											echo '<option value="'.$rankId.'" '.($taxonEditorObj->getRankId()==$rankId?'SELECTED':'').'>'.$rName.'</option>';
										}
									}
									?>
								</select>
							</div>
						</div>
						<div class="editDiv">
							<div class="editLabel"><?php echo (isset($LANG['NOTES'])?$LANG['NOTES']:'Notes'); ?>: </div>
							<div class="editfield">
								<?php echo htmlspecialchars($taxonEditorObj->getNotes());?>
							</div>
							<div class="editfield" style="display:none;width:90%;">
								<input type="text" id="notes" name="notes" style="width:100%;" value="<?php echo htmlspecialchars($taxonEditorObj->getNotes()); ?>" />
							</div>
						</div>
						<div class="editDiv">
							<div class="editLabel"><?php echo (isset($LANG['SOURCE'])?$LANG['SOURCE']:'Source'); ?>: </div>
							<div class="editfield">
								<?php
								$source = $taxonEditorObj->getSource();
								if(!stripos($source, '<a ')){
									$source = htmlspecialchars($source);
								}
								echo $source;
								?>
							</div>
							<div class="editfield" style="display:none;width:90%;">
								<input type="text" id="source" name="source" style="width:100%;" value="<?php echo htmlspecialchars($taxonEditorObj->getSource()); ?>" />
							</div>
						</div>
						<div class="editDiv">
							<div class="editLabel"><?php echo (isset($LANG['LOC_SECURITY'])?$LANG['LOC_SECURITY']:'Locality Security'); ?>: </div>
							<div class="editfield">
								<?php
									switch($taxonEditorObj->getSecurityStatus()){
										case 0:
											echo "show all locality data";
											break;
										case 1:
											echo "hide locality data";
											break;
										default:
											echo "not set or set to an unknown setting";
											break;
									}
								?>
							</div>
							<div class="editfield" style="display:none;">
								<select id="securitystatus" name="securitystatus">
									<option value="0"><?php echo (isset($LANG['SEL_LOC_SETTING'])?$LANG['SEL_LOC_SETTING']:'select a locality setting'); ?></option>
									<option value="0">---------------------------------</option>
									<option value="0" <?php if($taxonEditorObj->getSecurityStatus()==0) echo "SELECTED"; ?>><?php echo (isset($LANG['SHOW_ALL_LOC'])?$LANG['SHOW_ALL_LOC']:'show all locality data'); ?></option>
									<option value="1" <?php if($taxonEditorObj->getSecurityStatus()==1) echo "SELECTED"; ?>><?php echo (isset($LANG['HIDE_LOC'])?$LANG['HIDE_LOC']:'hide locality data'); ?></option>
								</select>
								<input type='hidden' name='securitystatusstart' value='<?php echo $taxonEditorObj->getSecurityStatus(); ?>' />
							</div>
						</div>
						<div class="editfield" style="display:none;clear:both;margin:15px 0px">
							<input type="hidden" name="tid" value="<?php echo $taxonEditorObj->getTid(); ?>" />
							<input type="hidden" name="taxauthid" value="<?php echo $taxAuthId;?>">
							<button type="submit" id="taxoneditsubmit" name="taxonedits" value="submitEdits" ><?php echo (isset($LANG['SUBMIT_EDITS'])?$LANG['SUBMIT_EDITS']:'Submit Edits'); ?></button>
						</div>
					</form>
				</div>
				<div id="taxonstatusdiv" style="min-height:400px;">
					<fieldset style="width:95%;">
						<legend><b><?php echo (isset($LANG['TAX_PLACEMENT'])?$LANG['TAX_PLACEMENT']:'Taxonomic Placement'); ?></b></legend>
						<div style="padding:3px 7px;margin:-12px -10px 5px 0px;float:right;">
							<form name="taxauthidform" action="taxoneditor.php" method="post">
								<select name="taxauthid" onchange="this.form.submit()">
									<option value="1"><?php echo (isset($LANG['DEFAULT_TAX'])?$LANG['DEFAULT_TAX']:'Default Taxonomy'); ?></option>
									<option value="1">----------------------------</option>
									<?php
										$ttIdArr = $taxonEditorObj->getTaxonomicThesaurusIds();
										foreach($ttIdArr as $ttID => $ttName){
											echo '<option value='.$ttID.' '.($taxAuthId==$ttID?'SELECTED':'').'>'.$ttName.'</option>';
										}
									?>
								</select>
								<input type="hidden" name="tid" value="<?php echo $taxonEditorObj->getTid(); ?>" />
								<input type="hidden" name="tabindex" value="1" />
							</form>
						</div>
						<div style="font-size:120%;font-weight:bold;"><?php echo (isset($LANG['STATUS'])?$LANG['STATUS']:'Status'); ?>:
							<span style='color:red;'>
								<?php
								switch($taxonEditorObj->getIsAccepted()){
									case -2:		//In conflict, needs to be resolved
										echo (isset($LANG['IN_CONFLICT'])?$LANG['IN_CONFLICT']:'In Conflict, needs to be resolved!');
										break;
									case -1:		//Taxonomic status not yet assigned
										echo (isset($LANG['NOT_YET_DEFINED'])?$LANG['NOT_YET_DEFINED']:'Taxonomy not yet defined for this taxon.');
										break;
									case 0:			//Not Accepted
										echo (isset($LANG['NOT_ACCEPTED'])?$LANG['NOT_ACCEPTED']:'Not Accepted');
										break;
									case 1:			//Accepted
										echo (isset($LANG['ACCEPTED'])?$LANG['ACCEPTED']:'Accepted');
										break;
								}
								?>
							</span>
						</div>
						<div style="clear:both;margin:10px;overflow:auto">
							<div style="float:right;">
								<a href="#" onclick="toggle('tsedit');return false;"><img style='border:0px;' src='../../images/edit.png'/></a>
							</div>
							<div style="float:left">
								<form name="taxstatusform" action="taxoneditor.php" method="post">
									<?php
									if($taxonEditorObj->getRankId() > 140 && $taxonEditorObj->getFamily()){
										?>
										<div class="editDiv">
											<div class="editLabel"><?php echo (isset($LANG['FAMILY'])?$LANG['FAMILY']:'Family'); ?>: </div>
											<div class="editField">
												<?php echo $taxonEditorObj->getFamily();?>
											</div>
										</div>
										<?php
									}
									?>
									<div class="editDiv">
										<div class="editLabel"><?php echo (isset($LANG['PARENT_TAXON'])?$LANG['PARENT_TAXON']:'Parent Taxon'); ?>: </div>
										<div class="tsedit">
											<?php echo '<a href="taxoneditor.php?tid='.$taxonEditorObj->getParentTid().'">'.$taxonEditorObj->getParentNameFull().'</a>';?>
										</div>
										<div class="tsedit" style="display:none;margin:3px;">
											<input id="parentstr" name="parentstr" type="text" value="<?php echo $taxonEditorObj->getParentName(); ?>" style="width:450px" />
											<input name="parenttid" type="hidden" value="<?php echo $taxonEditorObj->getParentTid(); ?>" />
										</div>
									</div>
									<div class="tsedit" style="display:none;clear:both;">
										<input type="hidden" name="tid" value="<?php echo $taxonEditorObj->getTid(); ?>" />
										<input type="hidden" name="taxauthid" value="<?php echo $taxAuthId;?>">
										<?php
										$aArr = $taxonEditorObj->getAcceptedArr();
										$aStr = key($aArr);
										?>
										<input type="hidden" name="tidaccepted" value="<?php echo ($taxonEditorObj->getIsAccepted()==1?$taxonEditorObj->getTid():$aStr); ?>" />
										<input type="hidden" name="tabindex" value="1" />
										<input type="hidden" name="submitaction" value="updatetaxstatus" />
										<input type='button' name='taxstatuseditsubmit' value='<?php echo (isset($LANG['SUBMIT_UPPER_EDITS'])?$LANG['SUBMIT_UPPER_EDITS']:'Submit Upper Taxonomy Edits'); ?>' onclick="submitTaxStatusForm(this.form)" />
									</div>
								</form>
							</div>
						</div>
						<div id="AcceptedDiv" style="margin-top:30px;clear:both;">
							<?php
							if($taxonEditorObj->getIsAccepted() <> 1){	//Is Not Accepted
								$acceptedArr = $taxonEditorObj->getAcceptedArr();
								?>
								<div class="headingDiv"><?php echo (isset($LANG['ACCEPTED_TAXON'])?$LANG['ACCEPTED_TAXON']:'Accepted Taxon'); ?></div>
								<div style="float:right;">
									<a href="#" onclick="toggle('acceptedits');return false;"><img style="border:0px;width:15px;" src="../../images/edit.png" /></a>
								</div>
								<?php
								if($acceptedArr){
									echo "<ul>\n";
									foreach($acceptedArr as $tidAccepted => $linkedTaxonArr){
										echo "<li id='acclink-".$tidAccepted."'>\n";
										echo "<a href='taxoneditor.php?tid=".$tidAccepted."&taxauthid=".$taxAuthId."'><i>".$linkedTaxonArr["sciname"]."</i></a> ".$linkedTaxonArr["author"]."\n";
										if(count($acceptedArr)>1){
											echo '<span class="acceptedits" style="display:none;"><a href="taxoneditor.php?tabindex=1&tid='.$tid.'&deltidaccepted='.$tidAccepted.'&taxauthid='.$taxAuthId.'">';
											echo '<img style="border:0px;width:12px;" src="../../images/del.png" />';
											echo '</a></span>';
										}
										if($linkedTaxonArr["usagenotes"]){
											echo "<div style='margin-left:10px;'>";
											if($linkedTaxonArr["usagenotes"]) echo "<u>Notes</u>: ".$linkedTaxonArr["usagenotes"];
											echo "</div>\n";
										}
										echo "</li>\n";
									}

									echo "</ul>\n";
								}
								else{
									echo "<div style='margin:20px;'>".(isset($LANG['ACCEPTED_NOT_DESIGNATED'])?$LANG['ACCEPTED_NOT_DESIGNATED']:'Accepted name not yet designated for this taxon')."</div>\n";
								}
								?>
								<div class="acceptedits" style="display:none;">
									<form id="accepteditsform" name="accepteditsform" action="taxoneditor.php" method="post" onsubmit="return verifyLinkToAcceptedForm(this);" >
										<fieldset style="width:80%;margin:20px;padding:15px">
											<legend><b><?php echo (isset($LANG['LINK_TO_OTHER_NAME'])?$LANG['LINK_TO_OTHER_NAME']:'Link to Another Accepted Name'); ?></b></legend>
											<div>
												<?php echo (isset($LANG['ACCEPTED_TAXON'])?$LANG['ACCEPTED_TAXON']:'Accepted Taxon'); ?>:
												<input id="aefacceptedstr" name="acceptedstr" type="text" style="width:450px;" />
												<input name="tidaccepted" type="hidden" />
											</div>
											<div>
												<input type="checkbox" name="deleteother" checked /> <?php echo (isset($LANG['REMOVE_OTHER_LINKS'])?$LANG['REMOVE_OTHER_LINKS']:'Remove Other Accepted Links'); ?>
											</div>
											<div>
												<input type="hidden" name="tid" value="<?php echo $taxonEditorObj->getTid();?>" />
												<input type="hidden" name="taxauthid" value="<?php echo $taxAuthId;?>" />
												<input type="hidden" name="tabindex" value="1" />
												<button name="submitaction" type="submit" value="linkToAccepted"><?php echo (isset($LANG['ADD_LINK'])?$LANG['ADD_LINK']:'Add Link'); ?></button>
											</div>
										</fieldset>
									</form>
									<form id="changetoacceptedform" name="changetoacceptedform" action="taxoneditor.php" method="post">
										<fieldset style="width:80%;margin:20px;padding:15px;">
											<legend><b><?php echo (isset($LANG['CHANGE_TO_ACCEPTED'])?$LANG['CHANGE_TO_ACCEPTED']:'Change to Accepted'); ?></b></legend>
											<?php
											$acceptedTid = key($acceptedArr);
											if($acceptedArr && count($acceptedArr)==1){
												if(!array_key_exists($acceptedTid, $hierarchyArr)){
													?>
													<div>
														<input type="checkbox" name="switchacceptance" value="1" checked /> <?php echo (isset($LANG['SWITCH_ACCEPTANCE'])?$LANG['SWITCH_ACCEPTANCE']:'Switch Acceptance with Currently Accepted Name'); ?>
													</div>
													<?php
												}
											}
											?>
											<div>
												<input type="hidden" name="tid" value="<?php echo $taxonEditorObj->getTid();?>" />
												<input type="hidden" name="taxauthid" value="<?php echo $taxAuthId;?>" />
												<input type="hidden" name="tidaccepted" value="<?php echo $aStr; ?>" />
												<input type="hidden" name="tabindex" value="1" />
												<button type='submit' id='changetoacceptedsubmit' name='changetoaccepted' value='Change Status to Accepted'><?php echo (isset($LANG['CHANGE_STATUS_ACCEPTED'])?$LANG['CHANGE_STATUS_ACCEPTED']:'Change Status to Accepted'); ?></button>
											</div>
										</fieldset>
									</form>
								</div>
							<?php
							}
							?>
						</div>
						<div id="SynonymDiv" style="clear:both;padding-top:15px;">
							<?php
							if($taxonEditorObj->getIsAccepted() <> 0){	//Is Accepted
								?>
								<div class="headingDiv"><?php echo (isset($LANG['SYNONYMS'])?$LANG['SYNONYMS']:'Synonyms'); ?></div>
								<div style="float:right;">
									<a href="#"  onclick="toggle('tonotaccepted');return false;"><img style='border:0px;width:15px;' src='../../images/edit.png'/></a>
								</div>
								<ul>
								<?php
								$synonymArr = $taxonEditorObj->getSynonyms();
								if($synonymArr){
									foreach($synonymArr as $tidSyn => $synArr){
										echo '<li> ';
										echo '<a href="taxoneditor.php?tid='.$tidSyn.'&taxauthid='.$taxAuthId.'"><i>'.$synArr['sciname'].'</i></a> '.$synArr['author'].' ';
										echo '<a href="#" onclick="toggle(\'syn-'.$tidSyn.'\');">';
										echo '<img style="border:0px;width:10px;" src="../../images/edit.png" />';
										echo '</a>';
										if($synArr["notes"] || $synArr["unacceptabilityreason"]){
											if($synArr["unacceptabilityreason"]){
												echo "<div style='margin-left:10px;'>";
												echo "<u>".(isset($LANG['REASON'])?$LANG['REASON']:'Reason')."</u>: ".htmlspecialchars($synArr["unacceptabilityreason"]);
												echo "</div>";
											}
											if($synArr["notes"]){
												echo "<div style='margin-left:10px;'>";
												echo "<u>".(isset($LANG['NOTES'])?$LANG['NOTES']:'Notes')."</u>: ".htmlspecialchars($synArr["notes"]);
												echo "</div>";
											}
										}
										echo '</li>';
										?>
										<fieldset id="syn-<?php echo $tidSyn;?>" style="display:none;">
											<legend><b><?php echo (isset($LANG['SYN_LINK_EDITOR'])?$LANG['SYN_LINK_EDITOR']:'Synonym Link Editor'); ?></b></legend>
											<form id="synform-<?php echo $tidSyn;?>" name="synform-<?php echo $tidSyn;?>" action="taxoneditor.php" method="post">
												<div style="clear:both;">
													<?php echo (isset($LANG['UNACCEPT_REASON'])?$LANG['UNACCEPT_REASON']:'Unacceptability Reason'); ?>:
													<input id='unacceptabilityreason' name='unacceptabilityreason' type='text' style="width:400px;" value='<?php echo htmlspecialchars($synArr["unacceptabilityreason"]); ?>' />
												</div>
												<div>
													<?php echo (isset($LANG['NOTES'])?$LANG['NOTES']:'Notes'); ?>:
													<input id='notes' name='notes' type='text' style="width:400px;" value='<?php echo htmlspecialchars($synArr["notes"]); ?>' />
												</div>
												<div>
													<?php echo (isset($LANG['SORT_SEQ'])?$LANG['SORT_SEQ']:'Sort Sequence'); ?>:
													<input id='sortsequence' name='sortsequence' type='text' style="width:60px;" value='<?php echo $synArr["sortsequence"]; ?>' />
												</div>
												<div>
													<input type="hidden" name="tid" value="<?php echo $taxonEditorObj->getTid(); ?>" />
													<input type="hidden" name="tidsyn" value="<?php echo $tidSyn; ?>" />
													<input type="hidden" name="taxauthid" value="<?php echo $taxAuthId;?>">
													<input type="hidden" name="tabindex" value="1" />
													<button type="submit" id="syneditsubmit" name="synonymedits" value="submitChanges"><?php echo (isset($LANG['SUBMIT_EDITS'])?$LANG['SUBMIT_EDITS']:'Submit Edits'); ?></button>
												</div>
											</form>
										</fieldset>
										<?php
									}
									?>
								</ul>
								<?php
								}
								else echo "<div style='margin:20px;'>No Synonyms Linked to this Taxon</div>";
								$hasAcceptedChildren = $taxonEditorObj->hasAcceptedChildren();
								?>
								<div id="tonotaccepted" style="display:none;">
									<form name="changeToNotAcceptedForm" action="taxoneditor.php" method="post" onsubmit="return verifyChangeToNotAcceptedForm(this);">
										<fieldset style="width:90%px;">
											<legend><b><?php echo (isset($LANG['CHANGE_NOT_ACCEPTED'])?$LANG['CHANGE_NOT_ACCEPTED']:'Change to Not Accepted'); ?></b></legend>
											<div style="margin:5px;">
												<?php echo (isset($LANG['ACCEPTED_NAME'])?$LANG['ACCEPTED_NAME']:'Accepted Name'); ?>:
												<input id="ctnafacceptedstr" name="acceptedstr" type="text" style="width:550px;" />
												<input name="tidaccepted" type="hidden" value="" />
											</div>
											<div style="margin:5px;">
												<?php echo (isset($LANG['REASON'])?$LANG['REASON']:'Reason'); ?>:
												<input name="unacceptabilityreason" type="text" style="width:90%;" />
											</div>
											<div style="margin:5px;">
												<?php echo (isset($LANG['NOTES'])?$LANG['NOTES']:'Notes'); ?>:
												<input name="notes" type="text" style="width:90%;" />
											</div>
											<div style="margin:5px;">
												<input name="tid" type="hidden" value="<?php echo $taxonEditorObj->getTid();?>" />
												<input name="taxauthid" type="hidden" value="<?php echo $taxAuthId;?>">
												<input name="tabindex" type="hidden" value="1" />
												<button name="submitaction" type="submit" value="changeToNotAccepted" <?php echo ($hasAcceptedChildren?'disabled':'')?>><?php echo (isset($LANG['CHANGE_STAT_NOT_ACCEPT'])?$LANG['CHANGE_STAT_NOT_ACCEPT']:'Change Status to Not Accepted'); ?></button>
											</div>
											<?php
											if($hasAcceptedChildren) echo '<div style="margin:5px;color:orange;font-weight:bold;">Taxon cannot be changed to Not Accepted until accepted child taxa are resolved</div>';
											?>
											<div style="margin:5px;">
												* <?php echo (isset($LANG['SYNONYMS_TRANSFERRED'])?$LANG['SYNONYMS_TRANSFERRED']:'Synonyms will be transferred to Accepted Taxon'); ?>
											</div>
										</fieldset>
									</form>
								</div>
								<?php
							}
							?>
						</div>
					</fieldset>
				</div>
				<div id="hierarchydiv" style="height:400px;">
					<fieldset style="width:420px;padding:25px;">
						<legend><b><?php echo (isset($LANG['QUERY_HIERARCHY'])?$LANG['QUERY_HIERARCHY']:'Quick Query Taxonomic Hierarchy'); ?></b></legend>
						<div style="float:right;" title="Rebuild Hierarchy">
							<form name="updatehierarchyform" action="taxoneditor.php" method="post">
								<input type="hidden" name="tid" value="<?php echo $taxonEditorObj->getTid(); ?>"/>
								<input type="hidden" name="taxauthid" value="<?php echo $taxAuthId;?>">
								<input type="hidden" name="submitaction" value="updatehierarchy" />
								<input type="hidden" name="tabindex" value="2" />
								<input type="image" name="imagesubmit" src="../../images/undo.png" style="width:20px;"/>
							</form>
						</div>
						<?php
						if($hierarchyArr){
							$indent = 0;
							foreach($hierarchyArr as $hierTid => $hierSciname){
								echo '<div style="margin-left:'.$indent.'px;">';
								echo '<a href="taxoneditor.php?tid='.$hierTid.'">'.$hierSciname.'</a>';
								echo "</div>\n";
								$indent += 10;
							}
							echo '<div style="margin-left:'.$indent.'px;">';
							echo '<a href="taxoneditor.php?tid='.$taxonEditorObj->getTid().'">'.$taxonEditorObj->getSciName().'</a>';
							echo "</div>\n";
						}
						else{
							echo "<div style='margin:10px;'>".(isset($LANG['EMPTY'])?$LANG['EMPTY']:'Empty')."</div>";
						}
						?>
					</fieldset>
				</div>
			</div>
		<?php
		}
		else{
			if(!$tid){
				if($statusStr != 'SUCCESS: taxon deleted!'){
					echo "<div>Target Taxon missing</div>";
				}
			}
			else{
				?>
				<div style="margin:30px;font-weight:bold;font-size:120%;">
					<?php echo (isset($LANG['NOT_AUTH'])?$LANG['NOT_AUTH']:'You are not authorized to access this page'); ?>
				</div>
				<?php
			}
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>