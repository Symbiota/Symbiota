<!DOCTYPE html>

<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/TaxonomyEditorManager.php');
include_once($SERVER_ROOT.'/content/lang/taxa/taxonomy/taxonomyloader.'.$LANG_TAG.'.php');
header('Content-Type: text/html; charset='.$CHARSET);

if(!$SYMB_UID) header('Location: '.$CLIENT_ROOT.'/profile/index.php?refurl=../taxa/taxonomy/taxonomyloader.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$tid = array_key_exists('tid',$_REQUEST)?$_REQUEST['tid']:'';
$status = '';

//Sanitation
if(!is_numeric($tid)) $tid = 0;

$loaderObj = new TaxonomyEditorManager();

$isEditor = false;
if($IS_ADMIN || array_key_exists('Taxonomy',$USER_RIGHTS)){
	$isEditor = true;
}

if($isEditor){
	if(array_key_exists('sciname',$_POST)){
		$status = $loaderObj->loadNewName($_POST);
		if(is_int($status)){
		 	header('Location: taxoneditor.php?tid='.$status);
		}
	}
}
?>
<html lang="<?php echo $LANG_TAG ?>">
<head>
	<title><?php echo $DEFAULT_TITLE.' '.(isset($LANG['TAXON_LOADER'])?$LANG['TAXON_LOADER']:'Taxon Loader'); ?>: </title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>"/>
	<link href="<?php echo htmlspecialchars($CSS_BASE_PATH, HTML_SPECIAL_CHARS_FLAGS); ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui.js"></script>
	<script src="../../js/symb/taxa.taxonomyloader.js?ver=19"></script>
</head>
<body>
<?php
	$displayLeftMenu = false;
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="../../index.php"><?php echo htmlspecialchars((isset($LANG['HOME'])?$LANG['HOME']:'Home'), HTML_SPECIAL_CHARS_FLAGS); ?></a> &gt;&gt;
		<a href="taxonomydisplay.php"><?php echo htmlspecialchars((isset($LANG['TAX_TREE_VIEW'])?$LANG['TAX_TREE_VIEW']:'Taxonomy Tree Viewer'), HTML_SPECIAL_CHARS_FLAGS); ?></a> &gt;&gt;
		<b><?php echo (isset($LANG['TAXONOMY_LOADER'])?$LANG['TAXONOMY_LOADER']:'Taxonomy Loader'); ?></b>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<?php
		if($status){
			echo '<div style="color:red;font-size:120%;">'.$status.'</div>';
		}
		if($isEditor){
			?>
			<form id="loaderform" name="loaderform" action="taxonomyloader.php" method="post" onsubmit="return verifyLoadForm(this)">
				<fieldset>
					<legend><b><?php echo (isset($LANG['ADD_NEW_TAXON'])?$LANG['ADD_NEW_TAXON']:'Add a New Taxon'); ?></b></legend>
					<div>
						<div class="left-column"><label for="sciname"> <?php echo (isset($LANG['TAXON_NAME'])?$LANG['TAXON_NAME']:'Taxon Name'); ?>: </label></div>
						<input type="text" id="sciname" name="sciname" class="search-bar-long" value="" onchange="parseName(this.form)"/>
					</div>
					<div>
						<div class="left-column"> <label for="author"> <?php echo (isset($LANG['AUTHOR'])?$LANG['AUTHOR']:'Author'); ?>: </label></div>
						<input type='text' id='author' name='author' class='search-bar-long' />
					</div>
					<div style="clear:both;">
						<div class="left-column"> <label for="rankid"> <?php echo (isset($LANG['TAXON_RANK'])?$LANG['TAXON_RANK']:'Taxon Rank'); ?>: </label></div>
						<select id="rankid" name="rankid" title="Rank ID" class='search-bar-short'>
							<option value=""><?php echo (isset($LANG['SEL_TAX_RANK'])?$LANG['SEL_TAX_RANK']:'Select Taxon Rank'); ?></option>
							<option value="0"><?php echo (isset($LANG['NON_RANKED_NODE'])?$LANG['NON_RANKED_NODE']:'Non-Ranked Node'); ?></option>
							<option value="">--------------------------------</option>
							<?php
							$tRankArr = $loaderObj->getRankArr();
							foreach($tRankArr as $rankId => $nameArr){
								foreach($nameArr as $rName){
									echo '<option value="'.$rankId.'" '.($rankId==220?' SELECTED':'').'>'.$rName.'</option>';
								}
							}
							?>
						</select>
					</div>
					<div style="clear:both;">
						<div class="left-column"> <label for="unitind1"> <?php echo (isset($LANG['UNITNAME1'])?$LANG['UNITNAME1']:'UnitName1'); ?>: </label></div>
						<select id="unitind1" name="unitind1" onchange="updateFullname(this.form)">
							<option value=""></option>
							<option value="&#215;">&#215;</option>
							<option value="&#8224;">&#8224;</option>
						</select>
						<input type='text' id='unitname1' name='unitname1' onchange="updateFullname(this.form)" class='search-bar' title='Genus or Base Name'/>
					</div>
					<div style="clear:both;">
						<div class="left-column"> <label for="unitind2"> <?php echo (isset($LANG['UNITNAME2'])?$LANG['UNITNAME2']:'UnitName2'); ?>: </label></div>
						<select id="unitind2" name="unitind2" onchange="updateFullname(this.form)">
							<option value=""></option>
							<option value="&#215;">&#215;</option>
						</select>
						<input type='text' id='unitname2' name='unitname2' onchange="updateFullname(this.form)" class='search-bar' title='epithet'/>
					</div>
					<div style="clear:both;">
						<div class="left-column"> <label for="unitind3"> <?php echo (isset($LANG['UNITNAME3'])?$LANG['UNITNAME3']:'UnitName3'); ?>: </label></div>
						<input type='text' id='unitind3' name='unitind3' onchange="updateFullname(this.form)" class='search-bar-extraShort' title='Rank: e.g. subsp., var., f.'/>
						<input type='text' id='unitname3' name='unitname3' onchange="updateFullname(this.form)" class='search-bar' title='infrasp. epithet'/>
					</div>
					<div style="clear:both;">
						<div class="left-column"> <label for="parentname"> <?php echo (isset($LANG['PARENT_TAXON'])?$LANG['PARENT_TAXON']:'Parent Taxon'); ?>: </label></div>
						<input type="text" id="parentname" name="parentname" class='search-bar' />
						<span id="addparentspan" style="display:none;">
							<a id="addparentanchor" href="taxonomyloader.php?target=" target="_blank"><?php echo htmlspecialchars((isset($LANG['ADD_PARENT'])?$LANG['ADD_PARENT']:'Add Parent'), HTML_SPECIAL_CHARS_FLAGS); ?></a>
						</span>
						<input id="parenttid" name="parenttid" type="hidden" value="" />
					</div>
					<div style="clear:both;">
						<div class="left-column"> <label for="notes"> <?php echo (isset($LANG['NOTES'])?$LANG['NOTES']:'Notes'); ?>: </label></div>
						<input type='text' id='notes' name='notes' class='search-bar-long'/>
					</div>
					<div style="clear:both;">
						<div class="left-column"> <label for="source"> <?php echo (isset($LANG['SOURCE'])?$LANG['SOURCE']:'Source'); ?>: </label></div>
						<input type='text' id='source' name='source' class='search-bar-long'/>
					</div>
					<div style="clear:both;">
						<div class="left-column"> <label for="securitystatus"> <?php echo (isset($LANG['LOC_SECURITY'])?$LANG['LOC_SECURITY']:'Locality Security'); ?>: </label></div>
						<select id="securitystatus" name="securitystatus" class="search-bar-short">
							<option value="0"><?php echo (isset($LANG['NO_SECURITY'])?$LANG['NO_SECURITY']:'No Security'); ?></option>
							<option value="1"><?php echo (isset($LANG['HIDE_LOC_DETAILS'])?$LANG['HIDE_LOC_DETAILS']:'Hide Locality Details'); ?></option>
						</select>
					</div>
					<div style="clear:both;">
						<fieldset>
							<legend><b><?php echo (isset($LANG['ACCEPT_STATUS'])?$LANG['ACCEPT_STATUS']:'Acceptance Status'); ?></b></legend>
							<div>
								<input type="radio" id="isaccepted" name="acceptstatus" value="1" onchange="acceptanceChanged(this.form)" checked> <label for="isaccepted">  <?php echo (isset($LANG['ACCEPTED'])?$LANG['ACCEPTED']:'Accepted'); ?> </label>
								<input type="radio" id="isnotaccepted" name="acceptstatus" value="0" onchange="acceptanceChanged(this.form)"> <label for="isnotaccepted">  <?php echo (isset($LANG['NOT_ACCEPTED'])?$LANG['NOT_ACCEPTED']:'Not Accepted'); ?> </label>
							</div>
							<div id="accdiv" style="display:none;margin-top:3px;">
								<div>
									<div class="left-column"> <label for="acceptedstr"> <?php echo (isset($LANG['ACCEPTED_TAXON'])?$LANG['ACCEPTED_TAXON']:'Accepted Taxon'); ?>: </label></div>
									<input id="acceptedstr" name="acceptedstr" type="text" class="search-bar-long" />
									<input id="tidaccepted" name="tidaccepted" type="hidden" />
								</div>
								<div>
									<div class="left-column"> <label for="unacceptabilityreason"> <?php echo (isset($LANG['UNACCEPT_REASON'])?$LANG['UNACCEPT_REASON']:'Unacceptability Reason'); ?>: </label></div>
									<input type='text' id='unacceptabilityreason' name='unacceptabilityreason' class='search-bar-long' />
								</div>
							</div>
						</fieldset>
					</div>
					<div style="clear:both;">
						<button type="submit" name="submitaction" value="submitNewName" ><?php echo (isset($LANG['SUBMIT_NEW_NAME'])?$LANG['SUBMIT_NEW_NAME']:'Submit New Name'); ?></button>
					</div>
				</fieldset>
			</form>
			<?php
		}
		else{
			?>
			<div style="margin:30px;font-weight:bold;font-size:120%;">
				<?php echo (isset($LANG['NOT_AUTH'])?$LANG['NOT_AUTH']:'You are not authorized to access this page'); ?>
			</div>
			<?php
		}
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</div>
</body>
</html>