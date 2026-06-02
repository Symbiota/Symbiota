<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoProjectManager.php');
header("Content-Type: text/html; charset=".$CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl='.$CLIENT_ROOT.'/ethno/manager/editcommunity.php?'.$_SERVER['QUERY_STRING']);

$comId = array_key_exists('comid',$_REQUEST)?$_REQUEST['comid']:0;
$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$action = array_key_exists('action',$_POST)?$_POST['action']:'';

$ethnoManager = new EthnoProjectManager();
$ethnoManager->setCollid($collid);
$ethnoManager->setComid($comId);

$closeWindow = false;
$statusStr = '';
$comArr = array();
$langArr = array();
if($action == 'Create Community'){
	$ethnoManager->createCommunity($_POST);
	$comId = $ethnoManager->getComid();
	$pArr = $_POST;
	$pArr['addCommID'] = $comId;
	$ethnoManager->createProjCommLink($pArr);
	$closeWindow = true;
}
elseif($action == 'Save Changes'){
	$ethnoManager->saveCommunityChanges($_POST);
	$closeWindow = true;
}
elseif($action == 'Add language'){
	$pArr = $_POST;
	if(!$_POST['comid']){
		$ethnoManager->createCommunity($pArr);
		$comId = $ethnoManager->getComid();
		$pArr['comid'] = $comId;
		$pArr['addCommID'] = $comId;
		$ethnoManager->createProjCommLink($pArr);
	}
	$ethnoManager->createCommLangLink($pArr);
}
elseif($action == 'Delete Selected'){
	$ethnoManager->deleteCommLangLink($_POST);
}
if($comId){
	$comArr = $ethnoManager->getCommunityInfoArr();
	$langArr = $ethnoManager->getCommunityLangArr($collid);
}
$roleArr = $ethnoManager->getPersonnelRoleArr();
$projLangList = $ethnoManager->getLangNameDropDownList($collid);
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> - Add/Edit Community</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<link href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui.js"></script>
	<script src="../../js/symb/shared.js" type="text/javascript"></script>
	<script type="text/javascript">
		$(function() {
			var dialogArr = new Array("comm","county","municipality","declat","declong","langsit");
			var dialogStr = "";
			for(i=0;i<dialogArr.length;i++){
				dialogStr = dialogArr[i]+"info";
				$( "#"+dialogStr+"dialog" ).dialog({
					autoOpen: false,
					modal: true,
					position: { my: "left top", at: "right bottom", of: "#"+dialogStr }
				});

				$( "#"+dialogStr ).click(function() {
					$( "#"+this.id+"dialog" ).dialog( "open" );
				});
			}

		});

		$(document).ready(function() {
			function split( val ) {
				return val.split( /,\s*/ );
			}

			$( "#country" )
				.bind( "keydown", function( event ) {
					if ( event.keyCode === $.ui.keyCode.TAB &&
						$( this ).data( "autocomplete" ).menu.active ) {
						event.preventDefault();
					}
				})
				.autocomplete({
					source: function( request, response ) {
						$.getJSON( "rpc/autofillcountryname.php", {
							name: request.term
						}, response );
					},
					search: function() {
						var term = this.value;
						if ( term.length < 4 ) {
							return false;
						}
					},
					focus: function() {
						return false;
					},
					select: function( event, ui ) {
						var terms = split( this.value );
						terms.pop();
						terms.push( ui.item.value );
						this.value = terms.join( ", " );
						return false;
					}
				},{});

			$( "#addLanguageName" )
				.bind( "keydown", function( event ) {
					if ( event.keyCode === $.ui.keyCode.TAB &&
						$( this ).data( "autocomplete" ).menu.active ) {
						event.preventDefault();
					}
				})
				.autocomplete({
					source: function( request, response ) {
						$.getJSON( "rpc/autofilllanguagename.php", {
							name: request.term,
							collid: <?php echo $collid; ?>
						}, response );
					},
					search: function() {
						var term = this.value;
						if ( term.length < 3 ) {
							return false;
						}
					},
					focus: function() {
						return false;
					},
					select: function( event, ui ) {
						var terms = split( this.value );
						terms.pop();
						terms.push( ui.item.value );
						document.getElementById('addLanguageID').value = ui.item.id;
						this.value = terms.join( ", " );
						return false;
					},
					change: function (event, ui) {
						if (!ui.item) {
							this.value = '';
							alert("Language must be added to the project and selected from the list.");
						}
					}
				},{});


		});

		function openPointRadiusMap(){
			mapWindow=open("mappoint.php","pointradius","resizable=0,width=750,height=750,left=20,top=20");
			if (mapWindow.opener == null) mapWindow.opener = self;
			mapWindow.focus();
		}

		function verifyAddEditForm(id,action){
			var communityName = document.getElementById("communityName").value;
			var countryVal = document.getElementById("country").value;
			var stateVal = document.getElementById("stateProvince").value;
			if(communityName && countryVal && stateVal){
				var http = new XMLHttpRequest();
				var url = "rpc/checkcommunityname.php";
				var params = '?name='+name+'&id='+id;
				//console.log(url+'?'+params);
				http.open("POST", url, true);
				http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				http.onreadystatechange = function() {
					if(http.readyState == 4 && http.status == 200) {
						if(Number(http.responseText) == 0){
							document.getElementById("addeditformaction").value = action;
							document.addeditcommunityform.submit();
						}
						else{
							alert('Community already exists in database.');
						}
					}
				};
				http.send(params);
			}
			else{
				alert('Please enter values for community name, country, and state/province.');
			}
		}

		function verifyAddLanguage(commid){
			var langID = document.getElementById("addLanguageID").value;
			var prevVal = document.getElementById("addLangPrevalence").value;
			var communityName = document.getElementById("communityName").value;
			var countryVal = document.getElementById("country").value;
			var stateVal = document.getElementById("stateProvince").value;
			if(!commid){
				if(communityName && countryVal && stateVal){
					var http = new XMLHttpRequest();
					var url = "rpc/checkcommunityname.php";
					var params = '?name='+name+'&id='+id;
					//console.log(url+'?'+params);
					http.open("POST", url, true);
					http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					http.onreadystatechange = function() {
						if(http.readyState == 4 && http.status == 200) {
							if(Number(http.responseText) == 0){
								if(langID && prevVal){
									document.getElementById("linklangformaction").value = 'Add language';
									document.addeditcommunityform.action = 'editcommunity.php';
									document.addeditcommunityform.submit();
								}
								else{
									alert('Please enter a language name and select the prevalence.');
									return false;
								}
							}
							else{
								alert('Community already exists in database.');
								return false;
							}
						}
					};
					http.send(params);
				}
				else{
					alert('Please enter values for community name, country, and state/province.');
					return false;
				}
			}
			if(langID && prevVal){
				document.getElementById("linklangformaction").value = 'Add language';
				document.linklanguageform.submit();
			}
			else{
				alert('Please enter a language name and select the prevalence.');
			}
		}

		function verifyDeleteLanguage(f){
			var valid = false;
			for(var i=0;i<f.length;i++){
				if((f.elements[i].name == "llid[]") && (f.elements[i].checked == true)){
					valid = true;
				}
			}
			if(valid){
				document.deletelangform.submit();
			}
			else{
				alert('Please select at least one language to delete.');
			}
		}
	</script>
</head>
<body>
<?php
$displayLeftMenu = false;
include($SERVER_ROOT.'/includes/header.php');

echo '<div class="navpath">';
echo '<a href="../../index.php">Home</a> &gt;&gt; ';
echo '<a href="../../collections/misc/collprofiles.php?collid='.$collid.'&emode=1">Collection Control Panel</a> &gt;&gt; ';
echo '<a href="index.php?collid='.$collid.'&tabindex=1">Manage Project</a> &gt;&gt; ';
echo '<b>Add/Edit Community</b>';
echo '</div>';
?>
<!-- This is inner text! -->
<div id="innertext">
	<?php
	if($statusStr){
		?>
		<div style="margin:15px;color:red;">
			<?php echo $statusStr; ?>
		</div>
		<?php
	}
	?>
	<div id="newcommdiv" style="margin-bottom:10px;">
		<fieldset>
			<legend><b>Add/Edit Community</b></legend>
			<div style="clear:both;">
				<fieldset style="padding:10px;margin-top:12px;width:470px;float:left;clear:left;">
					<form name="addeditcommunityform" action="editcommunity.php" method="post" onsubmit="">
						<legend><b>Community Information</b></legend>
						<table style="width:470px;float:left;clear:left;">
							<tr>
								<td style="font-size:14px;">
									Community:
									<a id="comminfo" href="#" onclick="return false" title="More information about Institution Code">
										<img src="../../images/qmark_big.png" style="width:15px;" />
									</a>
								</td>
								<td>
									<input type="text" id="communityName" name="communityName" size="43" value="<?php echo (isset($comArr['communityname'])?$comArr['communityname']:''); ?>" autocomplete="off" />
									<div id="comminfodialog">

									</div>
								</td>
							</tr>
							<tr>
								<td style="font-size:14px;">
									Country:
								</td>
								<td>
									<input type="text" id="country" name="country" size="43" value="<?php echo (isset($comArr['country'])?$comArr['country']:''); ?>" autocomplete="off" />
								</td>
							</tr>
							<tr>
								<td style="font-size:14px;">
									State/Province:
								</td>
								<td>
									<input type="text" id="stateProvince" name="stateProvince" size="43" value="<?php echo (isset($comArr['stateProvince'])?$comArr['stateProvince']:''); ?>" autocomplete="off" />
								</td>
							</tr>
							<tr>
								<td style="font-size:14px;">
									County:
									<a id="countyinfo" href="#" onclick="return false" title="More information about Institution Code">
										<img src="../../images/qmark_big.png" style="width:15px;" />
									</a>
								</td>
								<td>
									<input type="text" id="county" name="county" size="43" value="<?php echo (isset($comArr['county'])?$comArr['county']:''); ?>" autocomplete="off" />
									<div id="countyinfodialog">

									</div>
								</td>
							</tr>
							<tr>
								<td style="font-size:14px;">
									Municipality:
									<a id="municipalityinfo" href="#" onclick="return false" title="More information about Institution Code">
										<img src="../../images/qmark_big.png" style="width:15px;" />
									</a>
								</td>
								<td>
									<input type="text" id="municipality" name="municipality" size="43" value="<?php echo (isset($comArr['municipality'])?$comArr['municipality']:''); ?>" autocomplete="off" />
									<div id="municipalityinfodialog">

									</div>
								</td>
							</tr>
							<tr>
								<td style="font-size:14px;">
									Decimal latitude:
									<a id="declatinfo" href="#" onclick="return false" title="More information about Institution Code">
										<img src="../../images/qmark_big.png" style="width:15px;" />
									</a>
									<a id="fileurlinfo" href="#" onclick="openPointRadiusMap();">
										<img src="../../images/world.png" style="width:15px;" />
									</a>
								</td>
								<td>
									<input type="text" id="decimalLatitude" name="decimalLatitude" size="43" value="<?php echo (isset($comArr['decimalLatitude'])?$comArr['decimalLatitude']:''); ?>" autocomplete="off" />
									<div id="declatinfodialog">

									</div>
								</td>
							</tr>
							<tr>
								<td style="font-size:14px;">
									Decimal longitude:
									<a id="declonginfo" href="#" onclick="return false" title="More information about Institution Code">
										<img src="../../images/qmark_big.png" style="width:15px;" />
									</a>
								</td>
								<td>
									<input type="text" id="decimalLongitude" name="decimalLongitude" size="43" value="<?php echo (isset($comArr['decimalLongitude'])?$comArr['decimalLongitude']:''); ?>" autocomplete="off" />
									<div id="declonginfodialog">

									</div>
								</td>
							</tr>
							<tr>
								<td style="font-size:14px;">
									Elevation (meters):
								</td>
								<td>
									<input type="text" id="elevationInMeters" name="elevationInMeters" size="43" value="<?php echo (isset($comArr['elevationInMeters'])?$comArr['elevationInMeters']:''); ?>" autocomplete="off" />
								</td>
							</tr>
							<tr>
								<td colspan="2" style="width:100%;font-size:14px;">
									Language situation (describe in text):
									<a id="langsitinfo" href="#" onclick="return false" title="More information about Institution Code">
										<img src="../../images/qmark_big.png" style="width:15px;" />
									</a>
								</td>
							</tr>
							<tr>
								<td colspan="2" style="width:100%;font-size:14px;">
									<textarea name="languagecomments" rows="6" cols="70"><?php echo (isset($comArr['languagecomments'])?$comArr['languagecomments']:''); ?></textarea>
									<div id="langsitinfodialog">

									</div>
								</td>
							</tr>
						</table>
						<input id="addeditformaction" name="action" type="hidden" value="" />
						<!-- <input id="addLanguageID" name="addLanguageID" type="hidden" value="" /> -->
						<input name="comid" type="hidden" value="<?php echo $comId; ?>" />
						<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
						<?php
						if(!$comId){
							?>
							<div style="clear:both;padding:10px;float:right;">
								<button onclick="verifyAddEditForm('','Create Community');" type="button"><b>Add Community</b></button>
							</div>
							<?php
						}
						else{
							?>
							<div style="clear:both;padding:25px;float:right;">
								<button onclick="verifyAddEditForm(<?php echo $comId; ?>,'Save Changes');" type="button"><b>Save Changes</b></button>
							</div>
							<?php
						}
						?>
					</form>
				</fieldset>
				<fieldset style="padding:10px;margin-top:12px;width:350px;float:right;clear:right;">
					<legend><b>Language</b></legend>
					<?php
					if($langArr){
						?>
						<div style="float:right;">
							<a href="#" onclick="toggle('addLanguageDiv');" title="Link language to community"><img src="../../images/add.png" /></a>
						</div>
						<?php
					}
					?>
					<div id="addLanguageDiv" style="display:<?php echo ($langArr?'none':'block'); ?>;">
						<fieldset style="padding:10px;width:340px;float:left;">
							<form name="linklanguageform" action="editcommunity.php" method="post" onsubmit="">
								<legend><b>Link Language to Community</b></legend>
								<div style="width:100%;margin: 10px 0;clear:both;">
									Language name: <select name="addLanguageID" id="addLanguageID" style="width:180px;" >
										<option value="">----Select Language----</option>
										<?php
										foreach($projLangList as $k => $v){
											echo '<option value="'.$v['id'].'">'.$v['name'].'</option>';
										}
										?>
									</select>
								</div>
								<div style="width:100%;margin: 10px 0;clear:both;">
									Prevalence:
									<select id="addLangPrevalence" name="addLangPrevalence" onchange="">
										<option value='' >--------</option>
										<option value='Primary'>Primary</option>
										<option value='Secondary'>Secondary</option>
										<option value='Regional'>Regional</option>
										<option value='Immigrant'>Immigrant</option>
										<option value='Temporary visitor'>Temporary visitor</option>
									</select>
								</div>
								<div style="clear:both;float:right;">
									<input id="linklangformaction" name="action" type="hidden" value="" />
									<!-- <input id="addLanguageID" name="addLanguageID" type="hidden" value="" /> -->
									<input name="comid" type="hidden" value="<?php echo $comId; ?>" />
									<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
									<button type="button" onclick="verifyAddLanguage(<?php echo $comId; ?>);"><b>Link language to community</b></button>
								</div>
								<!-- <input id="addLanguageID" name="addLanguageID" type="hidden" value="" /> -->
							</form>
						</fieldset>
					</div>

					<?php
					if($langArr){
						?>
						<form name="deletelangform" action="editcommunity.php" method="post" onsubmit="">
							<div style="clear:both;margin-top:10px;">
								<table class="styledtable" style="font-family:Arial;font-size:12px;">
									<tr>
										<th style="width:20px;"></th>
										<th style="width:40px;">Name</th>
										<th style="width:40px;">Prevalence</th>
									</tr>
									<?php
									foreach($langArr as $llId => $lArr){
										echo '<tr>';
										echo '<td><input name="llid[]" type="checkbox" value="'.$llId.'" /></td>'."\n";
										echo '<td>'.$lArr['langname'].'</td>'."\n";
										echo '<td>'.$lArr['linktype'].'</td>'."\n";
										echo '</tr>';
									}
									?>
								</table>
							</div>
							<div style="margin:15px;">
								<button type="button" onclick='verifyDeleteLanguage(this.form);'><b>Delete Selected</b></button>
							</div>
							<input name="action" type="hidden" value="Delete Selected" />
							<!-- <input id="addLanguageID" name="addLanguageID" type="hidden" value="" /> -->
							<input name="comid" type="hidden" value="<?php echo $comId; ?>" />
							<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
						</form>
						<?php
					}
					?>
				</fieldset>
			</div>
		</fieldset>
	</div>
</div>
<?php
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>
