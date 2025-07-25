<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SpecUploadBase.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/collections/admin/specupload.' . $LANG_TAG . '.php')) include_once($SERVER_ROOT . '/content/lang/collections/admin/specupload.' . $LANG_TAG . '.php');
else include_once($SERVER_ROOT.'/content/lang/collections/admin/specupload.en.php');

header('Content-Type: text/html; charset='.$CHARSET);
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/admin/specupload.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = filter_var($_REQUEST['collid'], FILTER_SANITIZE_NUMBER_INT);
$uploadType = array_key_exists('uploadtype', $_REQUEST) ? filter_var($_REQUEST['uploadtype'], FILTER_SANITIZE_NUMBER_INT) : 0;
$uspid = array_key_exists('uspid', $_REQUEST) ? filter_var($_REQUEST['uspid'], FILTER_SANITIZE_NUMBER_INT) : 0;

$DIRECTUPLOAD = 1; $SKELETAL = 7; $IPTUPLOAD = 8; $NFNUPLOAD = 9; $STOREDPROCEDURE = 4; $SCRIPTUPLOAD = 5; $SYMBIOTA = 13; $INATURALIST = 14;

$duManager = new SpecUploadBase();

$duManager->setCollId($collid);
$duManager->setUspid($uspid);
$duManager->readUploadParameters();
if($uploadType) $duManager->setUploadType($uploadType);
else $uploadType = $duManager->getUploadType();

$isEditor = 0;
$isAdmin = ($IS_ADMIN || (array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin']))) ? 1 : 0;
if($isAdmin) $isEditor = 1;
// Allow for Personal Observation Management
elseif($duManager->getCollInfo('colltype') == 'General Observations' && array_key_exists('CollEditor',$USER_RIGHTS) && in_array($collid, $USER_RIGHTS['CollEditor'])) $isEditor = 1;
if($uploadType == $IPTUPLOAD || $uploadType == $SYMBIOTA){
	if($duManager->getPath()) header('Location: specuploadmap.php?uploadtype='.$uploadType.'&uspid='.$uspid.'&collid='.$collid);
}
elseif($uploadType == $DIRECTUPLOAD || $uploadType == $STOREDPROCEDURE || $uploadType == $SCRIPTUPLOAD){
	header('Location: specuploadprocessor.php?uploadtype='.$uploadType.'&uspid='.$uspid.'&collid='.$collid);
}
?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title><?php echo $DEFAULT_TITLE . ' ' . $LANG['SPEC_UPLOAD']; ?></title>
	<link href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script src="../../js/symb/shared.js" type="text/javascript"></script>
	<script src="https://cdn.jsdelivr.net/gh/mickley/iNatJS@main/inatjs.js" type="text/javascript"></script>
	<script src="../../js/symb/collections.inat.js" type="text/javascript"></script>
	<?php
	if(!empty($GOOGLE_MAP_KEY)) {
	?>
	<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $GOOGLE_MAP_KEY ?>&loading=async&libraries=&v=weekly"defer></script>
	<?php
	}
	?>
	<link href="<?php echo $CSS_BASE_PATH; ?>/symbiota/collections/inatupload.css" type="text/css" rel="stylesheet">
	<script>
		function verifyFileUploadForm(f){
			var fileName = "";
			if(f.uploadfile || f.ulfnoverride){
				if(f.uploadfile && f.uploadfile.value){
					 fileName = f.uploadfile.value;
				}
				else{
					fileName = f.ulfnoverride.value;
				}
				if(fileName == ""){
					alert("<?php echo $LANG['PATH_EMPTY']; ?>");
					return false;
				}
				else{
					var ext = fileName.split('.').pop();
					if(ext == 'csv' || ext == 'CSV') return true;
					else if(ext == 'zip' || ext == 'ZIP') return true;
					else if(ext == 'txt' || ext == 'TXT') return true;
					else if(ext == 'tab' || ext == 'tab') return true;
					else if(fileName.substring(0,4) == 'http') return true;
					else{
						alert("<?php echo $LANG['MUST_CSV']; ?>");
						return false;
					}
				}
			}
			return true;
		}

		function verifyFileSize(inputObj){
			inputObj.form.ulfnoverride.value = ''
			if (!window.FileReader) {
				//alert("The file API isn't supported on this browser yet.");
				return;
			}
			<?php
			$maxUpload = ini_get('upload_max_filesize');
			$maxUpload = str_replace("M", "000000", $maxUpload);
			if($maxUpload > 100000000) $maxUpload = 100000000;
			echo 'var maxUpload = '.$maxUpload.";\n";
			?>
			var file = inputObj.files[0];
			if(file.size > maxUpload){
				var msg = "<?php echo $LANG['IMPORT_FILE']; ?>"+file.name+" ("+Math.round(file.size/100000)/10+"<?php echo $LANG['IS_BIGGER'] . ' '; ?>"+(maxUpload/1000000)+"MB).";
				if(file.name.slice(-3) != "zip") msg = msg + "<?php echo $LANG['MAYBE_ZIP']; ?>";
				alert(msg);
			}
		}

		// Language variables to pass to the javascript iNat functions in collections.inat.js
		const lang = {
			AUTH_SUCCESS: "<?php echo $LANG['AUTH_SUCCESS'];?>",
			AUTH_FAIL: "<?php echo $LANG['AUTH_FAIL'];?>",
			AUTHORIZE: "<?php echo $LANG['AUTHORIZE'];?>",
			NO_SEARCH: "<?php echo $LANG['NO_SEARCH'];?>",
			LOADING: "<?php echo $LANG['LOADING'];?>",
			SEARCH_ERROR: "<?php echo $LANG['SEARCH_ERROR'];?>",
			NO_OBS: "<?php echo $LANG['NO_OBS'];?>",
			SKIPPED: "<?php echo $LANG['SKIPPED'];?>",
			OBSCURED_COORDS: "<?php echo $LANG['OBSCURED_COORDS'];?>",
			LICENSING_RESTRICT: "<?php echo $LANG['LICENSING_RESTRICT'];?>",
			AUTHORIZE: "<?php echo $LANG['AUTHORIZE'];?>",
			TOP_BUTTON: "<?php echo $LANG['TOP_BUTTON'];?>",
			HEADER_PHOTO: "<?php echo $LANG['HEADER_PHOTO'];?>",
			HEADER_SCINAME: "<?php echo $LANG['HEADER_SCINAME'];?>",
			HEADER_OBSERVED: "<?php echo $LANG['HEADER_OBSERVED'];?>",
			HEADER_UPLOADED: "<?php echo $LANG['HEADER_UPLOADED'];?>",
			HEADER_LOCATION: "<?php echo $LANG['HEADER_LOCATION'];?>",
			HEADER_OBSERVER: "<?php echo $LANG['HEADER_OBSERVER'];?>"
		};
	</script>
</head>
<body>
	<?php
$displayLeftMenu = false;
include($SERVER_ROOT.'/includes/header.php');
?>
<div class="navpath">
	<a href="../../index.php"><?php echo htmlspecialchars($LANG['HOME'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?></a> &gt;&gt;
	<?php
	if($duManager->getCollInfo('colltype') == 'General Observations' && !$isAdmin){
	?>
	<a href="../../profile/viewprofile.php?tabindex=1"><?php echo htmlspecialchars((isset($LANG['PERS_MANAGEMENT'])?$LANG['PERS_MANAGEMENT']:'Personal Management'), ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?></a> &gt;&gt;
	<?php
	} else {
	?>
	<a href="../misc/collprofiles.php?collid=<?php echo htmlspecialchars($collid, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>&emode=1"><?php echo htmlspecialchars($LANG['COL_MGMNT'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?></a> &gt;&gt;
	<a href="specuploadmanagement.php?collid=<?php echo htmlspecialchars($collid, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>"><?php echo htmlspecialchars($LANG['LIST_UPLOAD'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?></a> &gt;&gt;
	<?php
	}
	?>
	<b><?php echo $LANG['SPEC_UPLOAD']; ?></b>
</div>
<div role="main" id="innertext">
	<h1 class="page-heading"><?= $LANG['UP_MODULE']; ?></h1>
	<?php
	if($isEditor && $collid){
		//Grab collection name and last upload date and display for all
		echo '<div style="font-weight:bold;font-size:130%;">'.$duManager->getCollInfo('name').'</div>';
		echo '<div style="margin:0px 0px 15px 15px;"><b>' . $LANG['LAST_UPLOAD_DATE'] . ':</b> ' . ($duManager->getCollInfo('uploaddate')?$duManager->getCollInfo('uploaddate'):$LANG['NOT_REC']) . '</div>';

		// Normal file upload form
		if($uploadType != $INATURALIST){
		?>
		<form name="fileuploadform" action="specuploadmap.php" method="post" enctype="multipart/form-data" onsubmit="return verifyFileUploadForm(this)">
			<fieldset style="width:95%;">
				<legend style="font-weight:bold;font-size:120%;<?php if($uploadType == $SKELETAL) echo 'background-color:lightgreen'; ?>"><?php echo $duManager->getTitle() . ': ' . $LANG['ID_SOURCE']; ?></legend>
				<div>
					<div style="margin:10px">
						<?php
						$pathLabel = $LANG['IPT_URL'];
						if($uploadType != $IPTUPLOAD){
							$pathLabel = $LANG['RESOURCE_URL'];
							?>
							<div>
								<input name="uploadfile" type="file" onchange="verifyFileSize(this)" aria-label="<?php echo $LANG['UPLOAD'] ?>" />
							</div>
							<?php
						}
						?>
						<div class="ulfnoptions" style="display:<?php echo ($uploadType!=$IPTUPLOAD?'none':''); ?>;margin:15px 0px">
							<b><?php echo $pathLabel; ?>:</b>
							<input name="ulfnoverride" type="text" size="70" /><br/>
							<?php
							if($uploadType != $IPTUPLOAD) echo '* ' . $LANG['WORKAROUND'];
							?>
						</div>
						<?php
						if($uploadType != $IPTUPLOAD){
							?>
							<div class="ulfnoptions">
								<a href="#" onclick="toggle('ulfnoptions');return false;"><?php echo htmlspecialchars($LANG['DISPLAY_OPS'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?></a>
							</div>
							<?php
						}
						?>
					</div>
					<div style="margin:10px;">
						<?php
						if(!$uspid && $uploadType != $NFNUPLOAD)
							echo '<input id="automap" name="automap" type="checkbox" value="1" CHECKED /> <label for="automap"><b>' . $LANG['AUTOMAP'] . '</b></label><br/>';
						?>
					</div>
					<div style="margin:10px;">
						<button name="action" type="submit" value="Analyze File"><?php echo $LANG['ANALYZE_FILE']; ?></button>
						<input name="uspid" type="hidden" value="<?php echo $uspid; ?>" />
						<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
						<input name="uploadtype" type="hidden" value="<?php echo $uploadType; ?>" />
						<input name="MAX_FILE_SIZE" type="hidden" value="100000000" />
					</div>
				</div>
			</fieldset>
		</form>
		<?php

		// iNaturalist Import
		} else {
		?>
		<div class="container">
			<fieldset id="auth">
				<legend><b><?php echo $LANG['STATUS_FIELDSET'];?></b></legend>
				<div>
					<b><?php echo $LANG['INAT_AUTH'];?>: </b>
					<span id="inat-status" style="width:150px;color:red;"><?php echo $LANG['AUTH_NONE'];?></span> <br/>
					<a href="https://www.inaturalist.org/users/api_token" target="_blank" style="text-decoration: underline;"><?php echo $LANG['GET_TOKEN'];?></a> <?php echo $LANG['TOKEN_EXPIRE'];?>:
					<input id="apitoken" type="text" size=53 placeholder="Paste API Token Here" onchange="verifyAuthentication()" />
					<div style="margin-top: 10px;"><?php echo $LANG['AUTH_INSTRUCTIONS'];?></div>
				</div>
			</fieldset>
			<fieldset id="options">
				<legend><b><?php echo $LANG['OPTIONS_FIELDSET'];?></b></legend>
				<div>
					<div style="width: 50%; float: left;">
						<input id="automap" type="checkbox" value="1" checked />
						<label><?php echo $LANG['AUTOMAP_INAT'];?></label><br/>
						<input id="fullimport" type="checkbox" value="1" checked />
						<label><?php echo $LANG['UPDATE_RECORDS'];?></label><br/>
						<input id="savesettings" type="checkbox" value="1" checked />
						<label><?php echo $LANG['SAVE_OPTIONS'];?></label><br/>
						<input id="obsfields" type="checkbox" value="1" checked />
						<label><?php echo $LANG['OBS_FIELDS'];?></label>

					</div>
					<div style="width: 50%; float: right;">
						<input id="addelev" type="checkbox" value="1" checked />
						<label><?php echo $LANG['ELEVATION'];?></label><br/>
						<input id="addlink" type="checkbox" value="1" />
						<label><?php echo $LANG['SYMBIOTA_URL'];?></label><br/>
						<input id="assoctaxa" type="checkbox" value="1" onclick="$('#taxa-radius').toggle();"/> <label><?php echo $LANG['ASSOC_TAXA_SEARCH'];?></label><br/>
						<div id="taxa-radius" style="display: none; margin-left: 25px;">
							<label><?php echo $LANG['ASSOC_TAXA_RADIUS'];?>: </label>
							<input id="assoctaxaradius" type="text" value="50" size="4">
						</div>

					</div>
				</div>
			</fieldset>
			<fieldset id="search">
				<legend><b><?php echo $LANG['SEARCH_FIELDSET'] ;?></b></legend>
				<section id="fetch">
					<label style="display: inline-block; margin-bottom: 5px;"><?php echo $LANG['SEARCH_URL'];?>: </label>
					<input id="url" type="text" placeholder="<?php echo $LANG['URL_PLACEHOLDER'];?>" /><br/>
					<div id="iNatSearch">
						<div>
							<label><?php echo $LANG['OBSERVER'];?>: </label>
							<input id="user_login" type="text" placeholder="<?php echo $LANG['OBSERVER_PLACEHOLDER'];?>" onchange="buildSearchURL()">
							<label><?php echo $LANG['PROJECT'];?>: </label>
							<input id="project_id" size="60" type="text" placeholder="<?php echo $LANG['PROJECT_PLACEHOLDER'];?>" onchange="buildSearchURL()">
						</div>
						<div>
							<label><?php echo $LANG['IDENTIFIER'];?>: </label>
							<input id="ident_user_id" type="text" placeholder="<?php echo $LANG['IDENTIFIER_PLACEHOLDER'];?>" onchange="buildSearchURL()">
							<label><?php echo $LANG['PLACE'];?>: </label>
							<input id="place" size="60" type="text" placeholder="<?php echo $LANG['PLACE_PLACEHOLDER'];?>" onchange="buildSearchURL()">
							<input type="hidden" id="place_id">
						</div>
						<div>
							<label><?php echo $LANG['TAXON'];?>: </label>
							<input id="taxon" size="35" type="text" placeholder="<?php echo $LANG['TAXON_PLACEHOLDER'];?>" onchange="buildSearchURL()">
							<input type="hidden" id="taxon_id">
							<label><?php echo $LANG['OBS_AFTER'];?>: </label>
							<input id="d1" type="date" max="<?php echo date("Y-m-j"); ?>" size=10 onchange="buildSearchURL()">
							<label><?php echo $LANG['OBS_BEFORE'];?>: </label>
							<input id="d2" type="date" max="<?php echo date("Y-m-j"); ?>" size=10 onchange="buildSearchURL()">
						</div>
						<div>
							<label><?php echo $LANG['QUALITY'];?>: </label>
							<select id="quality_grade" onchange="buildSearchURL()">
								<option value="" selected><?php echo $LANG['ANY'];?></option>
								<option value="research"><?php echo $LANG['QUALITY_RESEARCH'];?></option>
								<option value="needs_id"><?php echo $LANG['QUALITY_NEEDSID'];?></option>
								<option value="casual"><?php echo $LANG['QUALITY_CASUAL'];?></option>
							</select>
							<label><?php echo $LANG['CULTIVATED'];?>: </label>
							<select id="captive" onchange="buildSearchURL()">
								<option value="" selected><?php echo $LANG['ANY'];?></option>
								<option value="true"><?php echo $LANG['CULT_YES'];?></option>
								<option value="false"><?php echo $LANG['CULT_NO'];?></option>
							</select>
							<label><?php echo $LANG['ID_AGREE'];?>: </label>
							<select id="identifications" onchange="buildSearchURL()">
								<option value="" selected><?php echo $LANG['ANY'];?></option>
								<option value="most_agree"><?php echo $LANG['AGREE_MOST'];?></option>
								<option value="some_agree"><?php echo $LANG['AGREE_SOME'];?></option>
								<option value="most_disagree"><?php echo $LANG['AGREE_DISAGREE'];?></option>
							</select>
						</div>
						<div>
							<label><?php echo $LANG['COORD_UNCERTAINTY'];?>: </label>
							<input id="acc_below" size="6" type="text" value="" onchange="buildSearchURL()">
							<label><?php echo $LANG['ORDER_BY'];?>: </label>
							<select id="order_by" onchange="buildSearchURL()">
								<option value="observed_on" selected><?php echo $LANG['ORDER_OBSERVED'];?></option>
								<option value="created_at"><?php echo $LANG['ORDER_UPLOADED'];?></option>
								<option value="updated_at"><?php echo $LANG['ORDER_UPDATED'];?></option>
							</select>
								<select id="order" onchange="buildSearchURL()">
								<option value="desc" selected><?php echo $LANG['ORDER_DESC'];?></option>
								<option value="asc"><?php echo $LANG['ORDER_ASC'];?></option>
							</select>
						</div>
						<div style="margin-top: 10px;"><?php echo $LANG['SEARCH_INSTRUCTIONS'];?></div>
					</div>
					<div style="margin-top: 10px;">
						<button id="search" onclick="getiNat()"><?php echo $LANG['FIND_BUTTON'] ;?></button>
						<button id="reset" onclick="resetSearch()"><?php echo $LANG['RESET_BUTTON'];?></button>
					</div>
				</section>
			</fieldset>
			<form id="inatimportform" name="fullform" action="specuploadmap.php" method="post">

				<fieldset id="resultsbox" style="display:none;">
					<legend><b><?php echo $LANG['RESULTS_FIELDSET'] ;?></b></legend>
					<button name="action" type="button" value="Map Fields" onClick="parseSelected()"><?php echo $LANG['IMPORT_BUTTON'];?></button>
					<button id="site" type="button" value="Map Fields" onClick="$('#site-data').toggle();"><?php echo $LANG['SITE_DATA_BUTTON'];?></button>
					<span style="margin-left: 10px;"><?php echo $LANG['SCROLL'];?></span>
					<div id="waiting"></div>
					<div id="site-data" style="display: none;">
						<div id="localityDiv">
							<label>
								<?php echo $LANG['LOCALITY'];?><?php //echo $LANG['ASSOCIATED_COLLECTORS']; ?>
								<a href="#" onclick="return dwcDoc('locality')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a>
							</label>
							<textarea id="locality" name="locality"></textarea>
						</div>
						<div id="habitatDiv">
							<label>
								<?php echo $LANG['HABITAT'];?><?php //echo $LANG['ASSOCIATED_COLLECTORS']; ?>
								<a href="#" onclick="return dwcDoc('habitat')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a>
							</label>
							<textarea id="habitat" name="habitat"/></textarea>
						</div>
						<div id="associatedTaxaDiv">
							<label>
								<?php echo $LANG['ASSOC_TAXA'];?><?php //echo $LANG['ASSOCIATED_TAXA']; ?>
								<a href="#" onclick="return dwcDoc('associatedTaxa')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a>
							</label>
							<textarea id="associatedtaxa" name="associatedtaxa"></textarea>
							<?php
							if(!isset($ACTIVATEASSOCTAXAAID) || $ACTIVATEASSOCTAXAAID){
								echo '<a href="#" onclick="openAssocSppAid();return false;"><img class="editimg" src="../../images/list.png" style="margin-bottom: 7px;" /></a>';
							}
							?>
						</div>
						<div id="associatedCollectorsDiv">
							<label>
								<?php echo $LANG['ASSOC_COLL'];?><?php //echo $LANG['ASSOCIATED_COLLECTORS']; ?>
								<a href="#" onclick="return dwcDoc('associatedCollectors')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a>
							</label>
							<textarea id="associatedcollectors" name="associatedcollectors"/></textarea>
							<div><?php echo $LANG['SITE_INSTRUCTIONS'];?></div>
						</div>
					</div>
					<div id="results">
						<div id="status"></div>
					</div>
					<input type="hidden" id="form-apitoken" name="apitoken" />
					<input type="hidden" id="uspid" name="uspid" value="<?php echo $uspid; ?>" />
					<input type="hidden" id="collid" name="collid" value="<?php echo $collid;?>" />
					<input type="hidden" id="uploadtype" name="uploadtype" value="14" />
					<input type="hidden" name="automap" value="1" />
					<input type="hidden" name="addlink" value="1" />
					<input type="hidden" name="fullimport" value="1" />
				</fieldset>
			</form>
		</div>
		<?php
		}
	}
	else{
		if(!$isEditor || !$collid) echo '<div style="font-weight:bold;font-size:120%;">' . $LANG['NOT_AUTH'] . '</div>';
		else{
			echo '<div style="font-weight:bold;font-size:120%;">';
			echo $LANG['PAGE_ERROR'] . ' = ';
			echo ini_get("upload_max_filesize").'; post_max_size = '.ini_get('post_max_size');
			echo $LANG['USE_BACK'];
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