<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceCollectionProfile.php');
include_once($SERVER_ROOT.'/content/lang/collections/misc/collmetadata.'.$LANG_TAG.'.php');
header("Content-Type: text/html; charset=".$CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/misc/collmetadata.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$tabIndex = array_key_exists('tabindex',$_REQUEST)?$_REQUEST['tabindex']:0;
$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';

if(!is_numeric($collid)) $collid = 0;
if(!is_numeric($tabIndex)) $tabIndex = 0;
if(preg_match('/[^a-zA-Z\s]+/', $action)) $action = '';

$isEditor = 0;
if($IS_ADMIN) $isEditor = 1;
elseif($collid){
	if(array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin'])){
		$isEditor = 1;
	}
}

$collManager = new OccurrenceCollectionProfile($isEditor&&($action||!$collid)?'write':'readonly');
$collManager->setCollid($collid);

$statusStr = '';
if($isEditor){
	if($action == 'saveEdits'){
		$statusStr = $collManager->collectionUpdate($_POST);
		if($statusStr === true) header('Location: collprofiles.php?collid='.$collid);
	}
	elseif($action == 'newCollection'){
		if($IS_ADMIN){
			$newCollid = $collManager->collectionInsert($_POST);
			if($newCollid){
				$statusStr = '<span style="color:green">'.(isset($LANG['ADD_SUCCESS'])?$LANG['ADD_SUCCESS']:'New collection added successfully').'!</span><br/>'.
				(isset($LANG['ADD_STUFF'])?$LANG['ADD_STUFF']:'Add contacts, resource links, or institution address below').'.';
				$collid = $newCollid;
				$tabIndex = 1;
			}
			else $statusStr = $collManager->getErrorMessage();
		}
	}
	elseif($action == 'saveResourceLink'){
		if(!$collManager->saveResourceLink($_POST)) $statusStr = $collManager->getErrorMessage();
		$tabIndex = 1;
	}
	elseif($action == 'saveContact'){
		if(!$collManager->saveContact($_POST)) $statusStr = $collManager->getErrorMessage();
		$tabIndex = 1;
	}
	elseif($action == 'deleteContact'){
		if(!$collManager->deleteContact($_POST['contactIndex'])) $statusStr = $collManager->getErrorMessage();
		$tabIndex = 1;
	}
	elseif($action == 'Link Address'){
		if(!$collManager->linkAddress($_POST['iid'])) $statusStr = $collManager->getErrorMessage();
	}
	elseif(array_key_exists('removeiid',$_GET)){
		if(!$collManager->removeAddress($_GET['removeiid'])) $statusStr = $collManager->getErrorMessage();
	}
}
$collData = current($collManager->getCollectionMetadata());
$collManager->cleanOutArr($collData);
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE.' '.($collid?$collData['collectionname']:'').' '.(isset($LANG['COLL_PROFS'])?$LANG['COLL_PROFS']:'Collection Profiles'); ?></title>
	<?php
	$activateJQuery = true;
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="../../js/jquery.js" type="text/javascript"></script>
	<script src="../../js/jquery-ui.js" type="text/javascript"></script>
	<script src="../../js/symb/common.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../js/tinymce/tinymce.min.js"></script>
	<script>
		// Adds WYSIWYG editor to description field
		tinymce.init({
			selector: '#full-description',
			plugins: 'link lists image',
			menubar: '',
			toolbar: ['undo redo | bold italic underline | link | alignleft aligncenter alignright | formatselect | bullist numlist | indent outdent | blockquote | image | charmap'],
			branding: false,
			default_link_target: "_blank",
			paste_as_text: true
		});

		$(function() {
			var dialogArr = new Array("instcode","collcode","pedits","pubagg","rights","rightsholder","accessrights","guid","colltype","management","icon","collectionid","sourceurl","sort");
			var dialogStr = "";
			for(i=0;i<dialogArr.length;i++){
				dialogStr = dialogArr[i]+"info";
				$( "#"+dialogStr+"dialog" ).dialog({
					autoOpen: false,
					modal: true,
					position: { my: "left", at: "center", of: "#"+dialogStr }
				});
				$( "#"+dialogStr ).click(function() {
					$( "#"+this.id+"dialog" ).dialog( "open" );
				});
			}

			$('#tabs').tabs({
				select: function(event, ui) {
					return true;
				},
				active: <?php echo $tabIndex; ?>,
				beforeLoad: function( event, ui ) {
					$(ui.panel).html("<?php echo (isset($LANG['LOADING'])?$LANG['LOADING']:'<p>Loading...</p>'); ?>");
				}
			});
		});

		function verifyCollEditForm(f){
			if(f.institutioncode.value == ''){
				alert("<?php echo (isset($LANG['NEED_INST_CODE'])?$LANG['NEED_INST_CODE']:'Institution Code must have a value'); ?>");
				return false;
			}
			if(f.collectionname.value == ''){
				alert("<?php echo (isset($LANG['NEED_COLL_VALUE'])?$LANG['NEED_COLL_VALUE']:'Collection Name must have a value'); ?>");
				return false;
			}
			if(f.managementtype.value == "Snapshot"){
				if(f.guidtarget.value == "symbiotaUUID"){
					alert("<?php echo (isset($LANG['CANNOT_GUID'])?$LANG['CANNOT_GUID']:'The Symbiota Generated GUID option cannot be selected for a collection that is managed locally outside of the data portal (e.g. Snapshot management type). In this case, the GUID must be generated within the source collection database and delivered to the data portal as part of the upload process.'); ?>");
					return false;
				}
			}
			if(!isNumeric(f.latitudedecimal.value) || !isNumeric(f.longitudedecimal.value)){
				alert("<?php echo (isset($LANG['NEED_DECIMAL'])?$LANG['NEED_DECIMAL']:'Latitude and longitude values must be in the decimal format (numeric only)'); ?>");
				return false;
			}
			if(f.rights.value == ""){
				alert("<?php echo (isset($LANG['NEED_RIGHTS'])?$LANG['NEED_RIGHTS']:'Rights field (e.g. Creative Commons license) must have a selection'); ?>");
				return false;
			}
			try{
				if(!isNumeric(f.sortseq.value)){
					alert("<?php echo (isset($LANG['SORT_NUMERIC'])?$LANG['SORT_NUMERIC']:'Sort sequence must be numeric only'); ?>");
					return false;
				}
			}
			catch(ex){}
			return true;
		}

		function managementTypeChanged(selElem){
			if(selElem.value == "Live Data") $(".sourceurl-div").hide();
			else $(".sourceurl-div").show();
			checkManagementTypeGuidSource(selElem.form);
		}

		function checkManagementTypeGuidSource(f){
			if(f.managementtype.value == "Snapshot" && f.guidtarget.value == "symbiotaUUID"){
				alert("<?php echo (isset($LANG['CANNOT_GUID'])?$LANG['CANNOT_GUID']:'The Symbiota Generated GUID option cannot be selected for a collection that is managed locally outside of the data portal (e.g. Snapshot management type). In this case, the GUID must be generated within the source collection database and delivered to the data portal as part of the upload process.'); ?>");
				f.guidtarget.value = '';
			}
			else if(f.managementtype.value == "Aggregate" && f.guidtarget.value != "" && f.guidtarget.value != "occurrenceId"){
				alert("<?php echo (isset($LANG['AGG_GUID'])?$LANG['AGG_GUID']:'An Aggregate dataset (e.g. specimens coming from multiple collections) can only have occurrenceID selected for the GUID source'); ?>");
				f.guidtarget.value = 'occurrenceId';
			}
			if(!f.guidtarget.value) f.publishToGbif.checked = false;
		}

		function checkGUIDSource(f){
			if(f.publishToGbif.checked == true){
				if(!f.guidtarget.value){
					alert("<?php echo (isset($LANG['NEED_GUID'])?$LANG['NEED_GUID']:'You must select a GUID source in order to publish to data aggregators.'); ?>");
					f.publishToGbif.checked = false;
				}
			}
		}

		function verifyAddAddressForm(f){
			if(f.iid.value == ""){
				alert("<?php echo (isset($LANG['SEL_INST'])?$LANG['SEL_INST']:'Select an institution to be linked'); ?>");
				return false;
			}
			return true;
		}

		function verifyIconImage(f){
			var iconImageFile = document.getElementById("iconfile").value;
			if(iconImageFile){
				var iconExt = iconImageFile.substr(iconImageFile.length-4);
				iconExt = iconExt.toLowerCase();
				if((iconExt != '.jpg') && (iconExt != 'jpeg') && (iconExt != '.png') && (iconExt != '.gif')){
					document.getElementById("iconfile").value = '';
					alert("<?php echo (isset($LANG['NOT_SUPP'])?$LANG['NOT_SUPP']:'The file you have uploaded is not a supported image file. Please upload a jpg, png, or gif file.'); ?>");
				}
				else{
					var fr = new FileReader;
					fr.onload = function(){
						var img = new Image;
						img.onload = function(){
							if((img.width>500) || (img.height>500)){
								document.getElementById("iconfile").value = '';
								img = '';
								alert("<?php echo (isset($LANG['MUST_SMALL'])?$LANG['MUST_SMALL']:'The image file must be less than 500 pixels in both width and height.'); ?>");
							}
						};
						img.src = fr.result;
					};
					fr.readAsDataURL(document.getElementById("iconfile").files[0]);
				}
			}
		}

		function verifyIconURL(f){
			var iconImageFile = document.getElementById("iconurl").value;
			if(iconImageFile && (iconImageFile.substr(iconImageFile.length-4) != '.jpg') && (iconImageFile.substr(iconImageFile.length-4) != '.png') && (iconImageFile.substr(iconImageFile.length-4) != '.gif')){
				alert("<?php echo (isset($LANG['NOT_SUPP_URL'])?$LANG['NOT_SUPP_URL']:'The url you have entered is not for a supported image file. Please enter a url for a jpg, png, or gif file.'); ?>");
			}
		}
	</script>
	<style type="text/css">
		fieldset { background-color: #f9f9f9; padding:15px }
		legend { font-weight: bold; }
		.field-block { margin: 5px 0px; }
		.field-label {  }
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = (isset($collections_misc_collmetadataMenu)?$collections_misc_collmetadataMenu:true);
	include($SERVER_ROOT.'/includes/header.php');
	echo '<div class="navpath">';
	echo '<a href="../../index.php">'.(isset($LANG['HOME'])?$LANG['HOME']:'Home').'</a> &gt;&gt; ';
	if($collid){
		echo '<a href="collprofiles.php?collid='.$collid.'&emode=1">'.(isset($LANG['COL_MGMNT'])?$LANG['COL_MGMNT']:'Collection Management').'</a> &gt;&gt; ';
		echo '<b>'.$collData['collectionname'].' '.(isset($LANG['META_EDIT'])?$LANG['META_EDIT']:'Metadata Editor').'</b>';
	}
	else echo '<b>'.(isset($LANG['CREATE_COLL'])?$LANG['CREATE_COLL']:'Create New Collection Profile').'</b>';
	echo '</div>';
	?>
	<div id="innertext">
		<?php
		if($statusStr){
			?>
			<hr />
			<div style="margin:20px;">
				<?php echo $statusStr; ?>
			</div>
			<hr />
			<?php
		}
		?>
        <div id="tabs" style="margin:0px;">
			<?php
			if($isEditor){
				if($collid) echo '<h1>'.$collData['collectionname'].(array_key_exists('institutioncode',$collData)?' ('.$collData['institutioncode'].')':'').'</h1>';
				?>
				<ul>
					<li><a href="#colleditor"><?php echo (isset($LANG['COL_META_EDIT'])?$LANG['COL_META_EDIT']:'Collection Metadata Editor'); ?></a></li>
					<?php
					if($collid) echo '<li><a href="collmetaresources.php?collid='.$collid.'">'.(isset($LANG['CONT_RES'])?$LANG['CONT_RES']:'Contacts & Resources').'</a></li>';
					?>
				</ul>
				<div id="colleditor">
					<fieldset>
						<legend><?php echo ($collid?'Edit':'Add New').' '.(isset($LANG['COL_INFO'])?$LANG['COL_INFO']:'Collection Information'); ?></legend>
						<form id="colleditform" name="colleditform" action="collmetadata.php" method="post" enctype="multipart/form-data" onsubmit="return verifyCollEditForm(this)">
							<div class="field-block">
								<span class="field-label"><?php echo (isset($LANG['INST_CODE'])?$LANG['INST_CODE']:'Institution Code'); ?>:</span>
								<span class="field-elem">
									<input type="text" name="institutionCode" value="<?php echo ($collid?$collData['institutioncode']:'');?>" required />
									<a id="instcodeinfo" href="#" onclick="return false" title="<?php echo (isset($LANG['MORE_INST_CODE'])?$LANG['MORE_INST_CODE']:'More information about Institution Code'); ?>" tabindex="-1">
										<img src="../../images/info.png" style="width:15px;" />
									</a>
									<span id="instcodeinfodialog">
										<?php
										echo (isset($LANG['NAME_ONE'])?$LANG['NAME_ONE']:'The name (or acronym) in use by the institution having custody
										of the occurrence records. This field is required. For more details, see').' '.'<a href="http://rs.tdwg.org/dwc/terms/index.htm#institutionCode"
										target="_blank">'.(isset($LANG['DWC_DEF'])?$LANG['DWC_DEF']:'Darwin Core definition').'</a>.'
										?>
									</span>
								</span>
							</div>
							<div class="field-block">
								<span class="field-label"><?php echo (isset($LANG['COLL_CODE'])?$LANG['COLL_CODE']:'Collection Code'); ?>:</span>
								<span class="field-elem">
									<input type="text" name="collectionCode" value="<?php echo ($collid?$collData["collectioncode"]:'');?>" />
									<a id="collcodeinfo" href="#" onclick="return false" title="<?php echo (isset($LANG['MORE_COLL_CODE'])?$LANG['MORE_COLL_CODE']:'More information about Collection Code'); ?>" tabindex="-1">
										<img src="../../images/info.png" style="width:15px;" />
									</a>
									<span id="collcodeinfodialog">
										<?php
										echo (isset($LANG['NAME_ACRO'])?$LANG['NAME_ACRO']:'The name, acronym, or code identifying the collection or data set
										from which the record was derived. This field is optional. For more details, see').' '.
										'<a href="http://rs.tdwg.org/dwc/terms/index.htm#institutionCode" target="_blank">'.
										(isset($LANG['DWC_DEF'])?$LANG['DWC_DEF']:'Darwin Core definition').'</a>.'
										?>
									</span>
								</span>
							</div>
							<div class="field-block">
								<span class="field-label"><?php echo (isset($LANG['COLL_NAME'])?$LANG['COLL_NAME']:'Collection Name'); ?>:</span>
								<span class="field-elem">
									<input type="text" name="collectionName" value="<?php echo ($collid?$collData["collectionname"]:'');?>" style="width:600px;" required />
								</span>
							</div>
							<div class="field-block">
								<span class="field-label"><?php echo (isset($LANG['DESC'])?$LANG['DESC']:'Description (2000 character max)'); ?>:</span>
								<div class="field-elem">
									<textarea id="full-description" name="fullDescription" style="width:95%;height:90px;"><?php echo ($collid?$collData["fulldescription"]:'');?></textarea>
								</div>
							</div>
							<div class="field-block">
								<span class="field-label"><?php echo (isset($LANG['LAT'])?$LANG['LAT']:'Latitude'); ?>:</span>
								<span class="field-elem">
									<input id="decimallatitude" name="latitudeDecimal" type="text" value="<?php echo ($collid?$collData["latitudedecimal"]:'');?>" />
									<a href="#" onclick="openPopup('../tools/mappointaid.php?errmode=0');return false;" tabindex="-1"><img src="../../images/world.png" style="width:12px;" /></a>
								</span>
							</div>
							<div class="field-block">
								<span class="field-label"><?php echo (isset($LANG['LONG'])?$LANG['LONG']:'Longitude'); ?>:</span>
								<span class="field-elem">
									<input id="decimallongitude" name="longitudeDecimal" type="text" value="<?php echo ($collid?$collData["longitudedecimal"]:'');?>" />
								</span>
							</div>
							<?php
							$fullCatArr = $collManager->getCategoryArr();
							if($fullCatArr){
								?>
								<div class="field-block">
									<span class="field-label"><?php echo (isset($LANG['CATEGORY'])?$LANG['CATEGORY']:'Category'); ?>:</span>
									<span class="field-elem">
										<select name="ccpk">
											<option value=""><?php echo (isset($LANG['NO_CATEGORY'])?$LANG['NO_CATEGORY']:'No Category'); ?></option>
											<option value="">-------------------------------------------</option>
											<?php
											$catArr = $collManager->getCollectionCategories();
											foreach($fullCatArr as $ccpk => $category){
												echo '<option value="'.$ccpk.'" '.($collid && array_key_exists($ccpk, $catArr)?'SELECTED':'').'>'.$category.'</option>';
											}
											?>
										</select>
									</span>
								</div>
								<?php
							}
							?>
							<div class="field-block">
								<span class="field-label"><?php echo (isset($LANG['ALLOW_PUBLIC_EDITS'])?$LANG['ALLOW_PUBLIC_EDITS']:'Allow Public Edits'); ?>:</span>
								<span class="field-elem">
									<input type="checkbox" name="publicEdits" value="1" <?php echo ($collData && $collData['publicedits']?'CHECKED':''); ?> />
									<a id="peditsinfo" href="#" onclick="return false" title="<?php echo (isset($LANG['MORE_PUB_EDITS'])?$LANG['MORE_PUB_EDITS']:'More information about Public Edits'); ?>" tabindex="-1">
										<img src="../../images/info.png" style="width:15px;" />
									</a>
									<span id="peditsinfodialog">
										<?php echo (isset($LANG['EXPLAIN_PUBLIC'])?$LANG['EXPLAIN_PUBLIC']:'Checking public edits will allow any user logged into the system to modify specimen records
										and resolve errors found within the collection. However, if the user does not have explicit
										authorization for the given collection, edits will not be applied until they are
										reviewed and approved by collection administrator.');
										?>
									</span>
								</span>
							</div>
							<div class="field-block">
								<span class="field-label"><?php echo (isset($LANG['LICENSE'])?$LANG['LICENSE']:'License'); ?>:</span>
								<span class="field-elem">
									<?php
									if(isset($RIGHTS_TERMS)){
										?>
										<select name="rights">
											<?php
											$hasOrphanTerm = true;
											if(!$collid) $hasOrphanTerm = false;
											foreach($RIGHTS_TERMS as $k => $v){
												$selectedTerm = '';
												if($collid && strtolower($collData["rights"])==strtolower($v)){
													$selectedTerm = 'SELECTED';
													$hasOrphanTerm = false;
												}
												echo '<option value="'.$v.'" '.$selectedTerm.'>'.$k.'</option>'."\n";
											}
											if($hasOrphanTerm && array_key_exists("rights",$collData)){
												echo '<option value="'.$collData["rights"].'" SELECTED>'.$collData["rights"].' ['.(isset($LANG['ORPHANED'])?$LANG['ORPHANED']:'orphaned term').']</option>'."\n";
											}
											?>
										</select>
										<?php
									}
									else{
										?>
										<input type="text" name="rights" value="<?php echo ($collid?$collData["rights"]:'');?>" style="width:90%;" />
										<?php
									}
									?>
									<a id="rightsinfo" href="#" onclick="return false" title="<?php echo (isset($LANG['MORE_INFO_RIGHTS'])?$LANG['MORE_INFO_RIGHTS']:'More information about Rights'); ?>" tabindex="-1">
										<img src="../../images/info.png" style="width:15px;" />
									</a>
									<span id="rightsinfodialog">
										<?php echo (isset($LANG['LEGAL_DOC'])?$LANG['LEGAL_DOC']:'A legal document giving official permission to do something with the resource.
										This field can be limited to a set of values by modifying the portal\'s central configuration file.
										For more details, see').' '.'<a href="http://rs.tdwg.org/dwc/terms/index.htm#dcterms:license" target="_blank">'.(isset($LANG['DWC_DEF'])?$LANG['DWC_DEF']:'Darwin Core definition').'</a>.'
										?>
									</span>
								</span>
							</div>
							<div class="field-block">
								<span class="field-label"><?php echo (isset($LANG['RIGHTS_HOLDER'])?$LANG['RIGHTS_HOLDER']:'Rights Holder'); ?>:</span>
								<span class="field-elem">
									<input type="text" name="rightsHolder" value="<?php echo ($collid?$collData["rightsholder"]:'');?>" style="width:600px" />
									<a id="rightsholderinfo" href="#" onclick="return false" title="<?php echo (isset($LANG['MORE_INFO_RIGHTS_H'])?$LANG['MORE_INFO_RIGHTS_H']:'More information about Rights Holder'); ?>" tabindex="-1">
										<img src="../../images/info.png" style="width:15px;" />
									</a>
									<span id="rightsholderinfodialog">
										<?php echo (isset($LANG['HOLDER_DEF'])?$LANG['HOLDER_DEF']:'The organization or person managing or owning the rights of the resource.
										For more details, see').' '.'<a href="http://rs.tdwg.org/dwc/terms/index.htm#dcterms:rightsHolder" target="_blank">'.(isset($LANG['DWC_DEF'])?$LANG['DWC_DEF']:'Darwin Core definition').'</a>.'
										?>
									</span>
								</span>
							</div>
							<div class="field-block">
								<span class="field-label"><?php echo (isset($LANG['ACCESS_RIGHTS'])?$LANG['ACCESS_RIGHTS']:'Access Rights'); ?>:</span>
								<span class="field-elem">
									<input type="text" name="accessRights" value="<?php echo ($collid?$collData["accessrights"]:'');?>" style="width:600px" />
									<a id="accessrightsinfo" href="#" onclick="return false" title="<?php echo (isset($LANG['MORE_INFO_ACCESS_RIGHTS'])?$LANG['MORE_INFO_ACCESS_RIGHTS']:'More information about Access Rights'); ?>" tabindex="-1">
										<img src="../../images/info.png" style="width:15px;" />
									</a>
									<span id="accessrightsinfodialog">
										<?php echo (isset($LANG['ACCESS_DEF'])?$LANG['ACCESS_DEF']:'Information or a URL link to page with details explaining
										how one can use the data. See').' '.'<a href="http://rs.tdwg.org/dwc/terms/index.htm#dcterms:accessRights" target="_blank">'.(isset($LANG['DWC_DEF'])?$LANG['DWC_DEF']:'Darwin Core definition').'</a>.'
										?>
									</span>
								</span>
							</div>
							<?php
							if($IS_ADMIN){
								?>
								<div class="field-block">
									<span class="field-label"><?php echo (isset($LANG['DATASET_TYPE'])?$LANG['DATASET_TYPE']:'Dataset Type'); ?>:</span>
									<span class="field-elem">
										<select name="collType">
											<option value="Preserved Specimens"><?php echo (isset($LANG['PRES_SPECS'])?$LANG['PRES_SPECS']:'Preserved Specimens'); ?></option>
											<option <?php echo ($collid && $collData["colltype"]=='Observations'?'SELECTED':''); ?> value="Observations"><?php echo (isset($LANG['OBSERVATIONS'])?$LANG['OBSERVATIONS']:'Observations'); ?></option>
											<option <?php echo ($collid && $collData["colltype"]=='General Observations'?'SELECTED':''); ?> value="General Observations"><?php echo (isset($LANG['PERS_OBS_MAN'])?$LANG['PERS_OBS_MAN']:'Personal Observation Management'); ?></option>
										</select>
										<a id="colltypeinfo" href="#" onclick="return false" title="<?php echo (isset($LANG['MORE_COL_TYPE'])?$LANG['MORE_COL_TYPE']:'More information about Collection Type'); ?>" tabindex="-1">
											<img src="../../images/info.png" style="width:15px;" />
										</a>
										<span id="colltypeinfodialog">
											<?php echo (isset($LANG['COL_TYPE_DEF'])?$LANG['COL_TYPE_DEF']:'Preserved Specimens signify a collection type that contains physical samples that are
											available for inspection by researchers and taxonomic experts. Use Observations when the record is not based on a physical specimen.
											Personal Observation Management is a dataset where registered users
											can independently manage their own subset of records. Records entered into this dataset are explicitly linked to the user&apos;s profile
											and can only be edited by them. This type of collection
											is typically used by field researchers to manage their collection data and print labels
											prior to depositing the physical material within a collection. Even though personal collections
											are represented by a physical sample, they are classified as &quot;observations&quot; until the
											physical material is publicly available within a collection.');
											?>
										</span>
									</span>
								</div>
								<div class="field-block">
									<span class="field-label"><?php echo (isset($LANG['MANAGEMENT'])?$LANG['MANAGEMENT']:'Management'); ?>:</span>
									<span class="field-elem">
										<select name="managementType" onchange="managementTypeChanged(this)">
											<option><?php echo (isset($LANG['SNAPSHOT'])?$LANG['SNAPSHOT']:'Snapshot'); ?></option>
											<option <?php echo ($collid && $collData["managementtype"]=='Live Data'?'SELECTED':''); ?>><?php echo (isset($LANG['LIVE_DATA'])?$LANG['LIVE_DATA']:'Live Data'); ?></option>
											<option <?php echo ($collid && $collData["managementtype"]=='Aggregate'?'SELECTED':''); ?>><?php echo (isset($LANG['AGGREGATE'])?$LANG['AGGREGATE']:'Aggregate'); ?></option>
										</select>
										<a id="managementinfo" href="#" onclick="return false" title="<?php echo (isset($LANG['MORE_INFO_TYPE'])?$LANG['MORE_INFO_TYPE']:'More information about Management Type'); ?>" tabindex="-1">
											<img src="../../images/info.png" style="width:15px;" />
										</a>
										<span id="managementinfodialog">
											<?php echo (isset($LANG['SNAPSHOT_DEF'])?$LANG['SNAPSHOT_DEF']:'Use Snapshot when there is a separate in-house database maintained in the collection and the dataset
											within the Symbiota portal is only a periodically updated snapshot of the central database.
											A Live dataset is when the data is managed directly within the portal and the central database is the portal data.');
											?>
										</span>
									</span>
								</div>
								<?php
							}
							?>
							<div class="field-block">
								<span class="field-label" title="Source of Global Unique Identifier"><?php echo (isset($LANG['GUID_SOURCE'])?$LANG['GUID_SOURCE']:'GUID source'); ?>:</span>
								<span class="field-elem">
									<select name="guidTarget" onchange="checkManagementTypeGuidSource(this.form)">
										<option value=""><?php echo (isset($LANG['NOT_DEFINED'])?$LANG['NOT_DEFINED']:'Not defined'); ?></option>
										<option value="">-------------------</option>
										<option value="occurrenceId" <?php echo ($collid && $collData["guidtarget"]=='occurrenceId'?'SELECTED':''); ?>><?php echo (isset($LANG['OCCURRENCE_ID'])?$LANG['OCCURRENCE_ID']:'occurrenceID GUID'); ?></option>
										<option value="catalogNumber" <?php echo ($collid && $collData["guidtarget"]=='catalogNumber'?'SELECTED':''); ?>><?php echo (isset($LANG['CAT_NUM'])?$LANG['CAT_NUM']:'Catalog Number'); ?></option>
										<option value="symbiotaUUID" <?php echo ($collid && $collData["guidtarget"]=='symbiotaUUID'?'SELECTED':''); ?>><?php echo (isset($LANG['SYMB_GUID'])?$LANG['SYMB_GUID']:'Symbiota Generated GUID (UUID)'); ?></option>
									</select>
									<a id="guidinfo" href="#" onclick="return false" title="<?php echo (isset($LANG['MORE_INFO_GUID'])?$LANG['MORE_INFO_GUID']:'More information about Global Unique Identifier'); ?>" tabindex="-1">
										<img src="../../images/info.png" style="width:15px;" />
									</a>
									<span id="guidinfodialog">
										<?php echo (isset($LANG['OCCID_DEF_1'])?$LANG['OCCID_DEF_1']:'Occurrence Id is generally used for
										Snapshot datasets when a Global Unique Identifier (GUID) field
										is supplied by the source database (e.g. Specify database) and the GUID is mapped to the').
										' <a href="http://rs.tdwg.org/dwc/terms/index.htm#occurrenceID" target="_blank">'.(isset($LANG['OCCURRENCEID'])?$LANG['OCCURRENCEID']:'occurrenceId').'</a>'.
										(isset($LANG['OCCID_DEF_2'])?$LANG['OCCID_DEF_2']:'field. The use of the Occurrence Id as the GUID is not recommended for live datasets.
										Catalog Number can be used when the value within the catalog number field is globally unique.
										The Symbiota Generated GUID (UUID) option will trigger the Symbiota data portal to automatically
										generate UUID GUIDs for each record. This option is recommended for many for Live Datasets
										but not allowed for Snapshot collections that are managed in local management system.');
										?>
									</span>
								</span>
							</div>
							<?php
							if(isset($GBIF_USERNAME) && isset($GBIF_PASSWORD) && isset($GBIF_ORG_KEY) && $GBIF_ORG_KEY) {
								?>
								<div class="field-block">
									<span class="field-label"><?php echo (isset($LANG['PUBLISH_TO_AGGS'])?$LANG['PUBLISH_TO_AGGS']:'Publish to Aggregators'); ?>:</span>
									<span class="field-elem">
										GBIF <input type="checkbox" name="publishToGbif" value="1" onchange="checkGUIDSource(this.form);" <?php echo($collData['publishtogbif'] ? 'CHECKED' : ''); ?> />
										<a id="pubagginfo" href="#" onclick="return false" title="More information about Publishing to Aggregators" tabindex="-1">
											<img src="../../images/info.png" style="width:15px;"/>
										</a>
										<!--
										<span>
											iDigBio <input type="checkbox" name="publishToIdigbio" value="1" onchange="checkGUIDSource(this.form);" <?php echo($collData['publishtoidigbio']?'CHECKED':''); ?> />
										</span>
										 -->
										<span id="pubagginfodialog">
											<?php echo (isset($LANG['ACTIVATE_GBIF'])?$LANG['ACTIVATE_GBIF']:'Activates GBIF publishing tools available within Darwin Core Archive Publishing menu option'); ?>.
										</span>
									</span>
								</div>
								<?php
							}
							?>
							<div class="field-block">
								<div class="sourceurl-div" style="display:<?php echo ($collData["managementtype"]=='Live Data'?'none':'');?>">
									<span class="field-label"><?php echo (isset($LANG['SOURCE_REC_URL'])?$LANG['SOURCE_REC_URL']:'Source Record URL'); ?>:</span>
									<span class="field-elem">
										<input type="text" name="individualUrl" style="width:700px" value="<?php echo ($collid?$collData["individualurl"]:'');?>" title="<?php echo (isset($LANG['DYNAMIC_LINK_REC'])?$LANG['DYNAMIC_LINK_REC']:'Dynamic link to source database individual record page'); ?>" />
										<a id="sourceurlinfo" href="#" onclick="return false" title="<?php echo (isset($LANG['MORE_INFO_SOURCE'])?$LANG['MORE_INFO_SOURCE']:'More information about Source Records URL'); ?>" tabindex="-1">
											<img src="../../images/info.png" style="width:15px;" />
										</a>
										<span id="sourceurlinfodialog">
											<?php echo (isset($LANG['ADVANCE_SETTING'])?$LANG['ADVANCE_SETTING']:'Advance setting: Adding a
											URL template here will insert a link to the source record within the specimen details page.
											A optional URL title can be include with a colon delimiting the title and URL.
											For example, &quot;SEINet source record').':http://swbiodiversity.org/seinet/collections/individual/index.php?occid=--DBPK--&quot; '.
											(isset($LANG['ADVANCE_SETTING_2'])?$LANG['ADVANCE_SETTING_2']:'will display the ID with the url pointing to the original
											record managed within SEINet. Or').' &quot;http://www.inaturalist.org/observations/--DBPK--&quot; '.(isset($LANG['ADVANCE_SETTING_3'])
											?$LANG['ADVANCE_SETTING_3']:'can be used for an	iNaturalist import if you mapped their ID field as the source
											Identifier (e.g. dbpk) during import. Template patterns --CATALOGNUMBER--, --OTHERCATALOGNUMBERS--, and --OCCURRENCEID-- are additional options.');
											?>
										</span>
									</span>
								</div>
							</div>
							<div class="field-block">
								<span class="field-label"><?php echo (isset($LANG['ICON_URL'])?$LANG['ICON_URL']:'Icon URL'); ?>:</span>
								<span class="field-elem">
									<span class="icon-elem" style="display:<?php echo (($collid&&$collData["icon"])?'none;':''); ?>">
										<input type='hidden' name='MAX_FILE_SIZE' value='20000000' />
										<input name='iconFile' id='iconfile' type='file' onchange="verifyIconImage(this.form);" />
									</span>
									<span class="icon-elem" style="display:<?php echo (($collid&&$collData["icon"])?'':'none'); ?>">
										<input style="width:600px;" type='text' name='iconUrl' id='iconurl' value="<?php echo ($collid?$collData["icon"]:'');?>" onchange="verifyIconURL(this.form);" />
									</span>
									<a id="iconinfo" href="#" onclick="return false" title="<?php echo (isset($LANG['WHAT_ICON'])?$LANG['WHAT_ICON']:'What is an Icon?'); ?>" tabindex="-1"><img src="../../images/info.png" style="width:15px;" /></a>
									<span id="iconinfodialog">
										<?php echo (isset($LANG['UPLOAD_ICON'])?$LANG['UPLOAD_ICON']:'');
										?>
									</span>
								</span>
								<span class="icon-elem" style="display:<?php echo (($collid&&$collData["icon"])?'none;':''); ?>">
									<a href="#" onclick="toggle('icon-elem');return false;"><?php echo (isset($LANG['ENTER_URL'])?$LANG['ENTER_URL']:'Enter URL'); ?></a>
								</span>
								<span class="icon-elem" style="display:<?php echo (($collid&&$collData["icon"])?'':'none;'); ?>">
									<a href="#" onclick="toggle('icon-elem');return false;">
										<?php echo (isset($LANG['UPLOAD_LOCAL'])?$LANG['UPLOAD_LOCAL']:'Upload Local Image'); ?>
									</a>
								</span>
							</div>
							<?php
							if($IS_ADMIN){
								?>
								<div class="field-block" style="clear:both">
									<span class="field-label"><?php echo (isset($LANG['SORT_SEQUENCE'])?$LANG['SORT_SEQUENCE']:'Sort Sequence'); ?>:</span>
									<span class="field-elem">
										<input type="text" name="sortSeq" value="<?php echo ($collid?$collData["sortseq"]:'');?>" />
										<a id="sortinfo" href="#" onclick="return false" title="<?php echo (isset($LANG['MORE_SORTING'])?$LANG['MORE_SORTING']:'More information about Sorting'); ?>" tabindex="-1">
											<img src="../../images/info.png" style="width:15px;" />
										</a>
										<span id="sortinfodialog">
											<?php echo (isset($LANG['LEAVE_IF_ALPHABET'])?$LANG['LEAVE_IF_ALPHABET']:'Leave this field empty if you want the collections to sort alphabetically (default)'); ?>
										</span>
									</span>
								</div>
								<?php
							}
							?>
							<div class="field-block">
								<span class="field-label"><?php echo (isset($LANG['COLLECTION_ID'])?$LANG['COLLECTION_ID']:'Collection ID (GUID)'); ?>:</span>
								<span class="field-elem">
									<input type="text" name="collectionID" value="<?php echo ($collid?$collData["collectionid"]:'');?>" style="width:400px" />
									<a id="collectionidinfo" href="#" onclick="return false" title="<?php echo (isset($LANG['MORE_INFO'])?$LANG['MORE_INFO']:'More information'); ?>" tabindex="-1">
										<img src="../../images/info.png" style="width:15px;" />
									</a>
									<span id="collectionidinfodialog">
										<?php echo (isset($LANG['EXPLAIN_COLLID'])?$LANG['EXPLAIN_COLLID']:'Global Unique Identifier for this collection (see').
										' <a href="https://dwc.tdwg.org/terms/#dwc:collectionID" target="_blank">'.(isset($LANG['DWC_COLLID'])?$LANG['DWC_COLLID']:'dwc:collectionID').
										'</a>): '.(isset($LANG['EXPLAIN_COLLID_2'])?$LANG['EXPLAIN_COLLID_2']:'If your collection already has a previously assigned GUID, that identifier should be represented here.
										For physical specimens, the recommended best practice is to use an identifier from a collections registry such as the
										Global Registry of Biodiversity Repositories').' (<a href="http://grbio.org" target="_blank">http://grbio.org</a>).';
										?>
									</span>
								</span>
							</div>
							<?php
							if($collid){
								?>
								<div class="field-block">
									<span class="field-label"><?php echo (isset($LANG['SECURITY_KEY'])?$LANG['SECURITY_KEY']:'Security Key'); ?>:</span>
									<span class="field-elem">
										<?php echo $collData['securitykey']; ?>
									</span>
								</div>
								<div class="field-block">
									<span class="field-label"><?php echo (isset($LANG['RECORDID'])?$LANG['RECORDID']:'recordID'); ?>:</span>
									<span class="field-elem">
										<?php echo $collData['recordid']; ?>
									</span>
								</div>
								<?php
							}
							?>
							<div class="field-block">
								<div style="margin:20px;">
									<?php
									if($collid){
										?>
										<input type="hidden" name="securityKey" value="<?php echo $collData['securitykey']; ?>" />
										<input type="hidden" name="recordID" value="<?php echo $collData['recordid']; ?>" />
										<input type="hidden" name="collid" value="<?php echo $collid;?>" />
										<button type="submit" name="action" value="saveEdits"><?php echo (isset($LANG['SAVE_EDITS'])?$LANG['SAVE_EDITS']:'Save Edits'); ?></button>
										<?php
									}
									else{
										?>
										<button type="submit" name="action" value="newCollection"><?php echo (isset($LANG['CREATE_COLL_2'])?$LANG['CREATE_COLL_2']:'Create New Collection'); ?></button>
										<?php
									}
									?>
								</div>
							</div>
						</form>
					</fieldset>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
