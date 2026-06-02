<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoProjectManager.php');
header("Content-Type: text/html; charset=".$CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl='.$CLIENT_ROOT.'/ethno/manager/editpersonnel.php?'.$_SERVER['QUERY_STRING']);

$cplId = array_key_exists('cplid',$_REQUEST)?$_REQUEST['cplid']:0;
$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$perId = array_key_exists('perid',$_REQUEST)?$_REQUEST['perid']:0;
$action = array_key_exists('action',$_POST)?$_POST['action']:'';

$ethnoManager = new EthnoProjectManager();
$ethnoManager->setCollid($collid);
$ethnoManager->setCplid($cplId);
$ethnoManager->setPerid($perId);

$closeWindow = false;
$statusStr = '';
$perArr = array();
$linkArr = array();
$commArr = array();
$langArr = array();
$projLangList = array();
$projCommList = array();
if($action === 'Create Personnel'){
	$ethnoManager->createPersonnel($_POST);
	$perId = $ethnoManager->getPerid();
	$closeWindow = true;
}
elseif($action === 'Save Changes'){
	$ethnoManager->savePersonnelChanges($_POST);
	$ethnoManager->savePersonnelLinkChanges($_POST);
	$closeWindow = true;
}
elseif($action === 'Link community'){
	$ethnoManager->createPerCommLink($_POST);
}
elseif($action === 'Remove Community'){
	$ethnoManager->deletePerCommLink($_POST);
}
elseif($action === 'Link language'){
	$ethnoManager->createPerLangLink($_POST);
}
elseif($action === 'Remove Language'){
	$ethnoManager->deletePerLangLink($_POST);
}
if($collid && $perId && !$cplId){
	$ethnoManager->createPersonnelLink($_POST);
	$cplId = $ethnoManager->getCplid();
	$action = 'Create Personnel';
}
if($perId){
	$perArr = $ethnoManager->getPersonnelInfoArr();
	if($cplId){
		$linkArr = $ethnoManager->getPersonnelProjInfoArr();
		//$commArr = $ethnoManager->getPersonnelCommArr();
		//$langArr = $ethnoManager->getPersonnelLangArr();
	}
}
$projLangList = $ethnoManager->getLangNameDropDownList($collid);
$projCommList = $ethnoManager->getCommNameDropDownList($collid);
$roleArr = $ethnoManager->getPersonnelRoleArr();
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> - Add/Edit Personnel</title>
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

			<?php
			if($closeWindow){
				if($action === 'Create Personnel' || $action === 'Save Changes'){
					?>
					if(opener.document.getElementById("perlistform")){
						window.opener.perlistform.submit();
					}
					<?php
				}
				echo 'self.close();';
			}
			?>

			$( "#firstName" )
				.bind( "keydown", function( event ) {
					if ( event.keyCode === $.ui.keyCode.TAB &&
						$( this ).data( "autocomplete" ).menu.active ) {
						event.preventDefault();
					}
				})
				.autocomplete({
					source: function( request, response ) {
						var ln = document.getElementById("lastName").value;
						$.getJSON( "rpc/autofillpersonnelname.php", {
							perfn: request.term,
							perln: ln
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
						self.location.href = self.location.href+'?cplid=<?php echo $cplId; ?>&collid=<?php echo $collid; ?>&perid='+ui.item.id;
						return false;
					}
				},{});

			$( "#lastName" )
				.bind( "keydown", function( event ) {
					if ( event.keyCode === $.ui.keyCode.TAB &&
						$( this ).data( "autocomplete" ).menu.active ) {
						event.preventDefault();
					}
				})
				.autocomplete({
					source: function( request, response ) {
						var fn = document.getElementById("firstName").value;
						$.getJSON( "rpc/autofillpersonnelname.php", {
							perfn: fn,
							perln: request.term
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
						self.location.href = self.location.href+'?cplid=<?php echo $cplId; ?>&collid=<?php echo $collid; ?>&perid='+ui.item.id;
						return false;
					}
				},{});

		});

		function checkPersonnelSex(){
			var sexSelection = document.getElementById("personnelSex").value;
			if(sexSelection == 'other'){
				document.getElementById("personnelSexComments").style.display = "inline";
			}
			else{
				document.getElementById("personnelSexComments").style.display = "none";
			}
		}

		function openCommunityEditor(comid){
			var urlStr = 'editcommunity.php?comid='+comid;
			newWindow = window.open(urlStr,'addnewpopup','toolbar=1,status=1,scrollbars=1,width=1350,height=900,left=20,top=20');
			if (newWindow.opener == null) newWindow.opener = self;
		}

		function verifyAddEditForm(id,action){
			var perfirstname = document.getElementById("firstName").value;
			if(perfirstname){
				if(isNaN(verifyPersonnelName(id))){
					document.getElementById("formaction").value = action;
					document.addeditpersonnelform.submit();
				}
				else{
					alert('Person by that name already exists in database.');
				}
			}
			else{
				alert('Please enter the first, or only, name for the person in the First Name field.');
			}
		}

		function verifyPersonnelName(id){
			var perTitle = document.getElementById("personnelTitle").value;
			var perFirstname = document.getElementById("firstName").value;
			var perLastname = document.getElementById("lastName").value;
			var http = new XMLHttpRequest();
			var url = "rpc/checkcommunityname.php";
			var params = '?title='+perTitle+'&fn='+perFirstname+'&ln='+perLastname+'&id='+id;
			//console.log(url+'?'+params);
			http.open("POST", url, true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.onreadystatechange = function() {
				if(http.readyState == 4 && http.status == 200) {
					return http.responseText;
				}
			};
			http.send(params);
		}

		function verifyLinkCommunity(){
			var comID = document.getElementById("addCommID").value;
			var resStatus = document.getElementById("addCommunityRes").value;
			if(comID && resStatus){
				document.getElementById("formaction").value = 'Link community';
				document.addeditpersonnelform.submit();
			}
			else{
				alert("Please enter a community name and select person's resident status.");
			}
		}

		function verifyRemoveCommunity(f){
			var valid = false;
			for(var i=0;i<f.length;i++){
				if((f.elements[i].name == "clid[]") && (f.elements[i].checked == true)){
					valid = true;
				}
			}
			if(valid){
				document.getElementById("formaction").value = 'Remove Community';
				document.addeditpersonnelform.submit();
			}
			else{
				alert('Please select at least one community to remove.');
			}
		}

		function verifyLinkLanguage(){
			var langID = document.getElementById("addLanguageID").value;
			if(langID){
				document.getElementById("formaction").value = 'Link language';
				document.addeditpersonnelform.submit();
			}
			else{
				alert("Please enter a language name.");
			}
		}

		function verifyRemoveLanguage(f){
			var valid = false;
			for(var i=0;i<f.length;i++){
				if((f.elements[i].name == "llid[]") && (f.elements[i].checked == true)){
					valid = true;
				}
			}
			if(valid){
				document.getElementById("formaction").value = 'Remove Language';
				document.addeditpersonnelform.submit();
			}
			else{
				alert('Please select at least one language to remove.');
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
echo '<a href="index.php?collid='.$collid.'&tabindex=2">Manage Project</a> &gt;&gt; ';
echo '<b>Add/Edit Personnel</b>';
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
	<div id="newtermdiv" style="margin-bottom:10px;">
		<form name="addeditpersonnelform" id="addeditpersonnelform" action="index.php" method="post" onsubmit="">
			<fieldset>
				<legend><b>Add/Edit Personnel</b></legend>
				<div style="clear:both;">
					<fieldset style="width:525px;float:left;clear:left;border: 0;">
						<fieldset style="padding:10px;margin-top:12px;">
							<legend><b>Personal Information</b></legend>
							<table style="width:460px;float:left;clear:left;">
								<tr>
									<td style="font-size:14px;">
										Title:
										<a id="comminfo" href="#" onclick="return false" title="More information about Institution Code">
											<img src="../../images/qmark_big.png" style="width:15px;" />
										</a>
									</td>
									<td>
										<input type="text" id="personnelTitle" name="personnelTitle" size="43" value="<?php echo (isset($perArr['title'])?$perArr['title']:''); ?>" title="" />
										<div id="comminfodialog">

										</div>
									</td>
								</tr>
								<tr>
									<td style="font-size:14px;">
										First Name:
									</td>
									<td>
										<input type="text" id="firstName" name="personnelFirstName" size="43" value="<?php echo (isset($perArr['firstname'])?$perArr['firstname']:''); ?>" title="" />
									</td>
								</tr>
								<tr>
									<td style="font-size:14px;">
										Last Name:
										<a id="comminfo" href="#" onclick="return false" title="More information about Institution Code">
											<img src="../../images/qmark_big.png" style="width:15px;" />
										</a>
									</td>
									<td>
										<input type="text" id="lastName" name="personnelLastName" size="43" value="<?php echo (isset($perArr['lastname'])?$perArr['lastname']:''); ?>" title="" />
										<div id="comminfodialog">

										</div>
									</td>
								</tr>
								<tr>
									<td style="font-size:14px;">
										Birth Year:
									</td>
									<td>
										<input type="text" id="personnelBirthYear" name="personnelBirthYear" size="43" value="<?php echo (isset($perArr['birthyear'])?$perArr['birthyear']:''); ?>" title="" />
									</td>
								</tr>
								<tr>
									<td style="font-size:14px;">
										Birth Estimate:
									</td>
									<td>
										<select id="birthYearEst" name="birthYearEst" onchange="">
											<option value='' >--------------------------</option>
											<option value='5years' <?php echo ((isset($perArr['birthyearestimation']) && $perArr['birthyearestimation'] === '5years')?'selected':''); ?> >Estimated (within 5 years)</option>
											<option value='10years' <?php echo ((isset($perArr['birthyearestimation']) && $perArr['birthyearestimation'] === '10years')?'selected':''); ?> >Estimated (within 10 years)</option>
											<option value='notindicated' <?php echo ((isset($perArr['birthyearestimation']) && $perArr['birthyearestimation'] === 'notindicated')?'selected':''); ?> >Not indicated</option>
										</select>
									</td>
								</tr>
								<tr>
									<td style="font-size:14px;">
										Sex:
										<a id="comminfo" href="#" onclick="return false" title="More information about Institution Code">
											<img src="../../images/qmark_big.png" style="width:15px;" />
										</a>
									</td>
									<td>
										<select id="personnelSex" name="personnelSex" onchange="checkPersonnelSex();">
											<option value='' >------</option>
											<option value='male' <?php echo ((isset($perArr['sex']) && $perArr['sex'] === 'male')?'selected':''); ?> >Male</option>
											<option value='female' <?php echo ((isset($perArr['sex']) && $perArr['sex'] === 'female')?'selected':''); ?> >Female</option>
											<option value='other' <?php echo ((isset($perArr['sex']) && $perArr['sex'] === 'other')?'selected':''); ?> >Other</option>
										</select>
										<div id="comminfodialog">

										</div>
									</td>
								</tr>
							</table>
							<table id="personnelSexComments" style="width:460px;float:left;clear:left;<?php echo ((isset($perArr['sex']) && $perArr['sex'] == 'other')?'':'display:none;'); ?>">
								<tr>
									<td style="font-size:14px;">
										Comments:
									</td>
									<td>
										<input type="text" name="personnelSexComments" size="60" value="<?php echo (isset($perArr['sexcomments'])?$perArr['sexcomments']:''); ?>" title="" />
									</td>
								</tr>
							</table>
						</fieldset>
						<fieldset style="padding:10px;margin-top:12px;">
							<legend><b>Community Information</b></legend>
							<table style="table-layout:fixed;width:460px;">
								<tr>
									<td style="width:250px;font-size:14px;">
										Community of residence:
										<a id="comminfo" href="#" onclick="return false" title="More information about Institution Code">
											<img src="../../images/qmark_big.png" style="width:15px;" />
										</a>
									</td>
									<td style="font-size:14px;">
										<select name="residenceCommunity">
											<option value="">----Select Community----</option>
											<?php
											foreach($projCommList as $k => $v){
												echo '<option value="'.$v['id'].'" '.($linkArr['residenceCommunity']==$v['id']?'selected':'').'>'.$v['name'].'</option>';
											}
											?>
										</select>
										<div id="comminfodialog">

										</div>
									</td>
								</tr>
								<tr>
									<td style="width:250px;font-size:14px;">
										Status of residency:
									</td>
									<td style="font-size:14px;">
										<select name="residenceStatus">
											<option value="">----Select Status----</option>
											<option value="native" <?php echo (($linkArr&&$linkArr['residenceStatus']==='native')?'selected':''); ?>>Native</option>
											<option value="permmigrant" <?php echo (($linkArr&&$linkArr['residenceStatus']==='permmigrant')?'selected':''); ?>>Permanent migrant</option>
											<option value="tempmigrant" <?php echo (($linkArr&&$linkArr['residenceStatus']==='tempmigrant')?'selected':''); ?>>Temporary migrant/Commuter</option>
											<option value="other" <?php echo (($linkArr&&$linkArr['residenceStatus']==='other')?'selected':''); ?>>Other</option>
										</select>
									</td>
								</tr>
								<tr>
									<td style="width:250px;font-size:14px;">
										Community where raised/born:
										<a id="comminfo" href="#" onclick="return false" title="More information about Institution Code">
											<img src="../../images/qmark_big.png" style="width:15px;" />
										</a>
									</td>
									<td style="font-size:14px;">
										<select name="birthCommunity">
											<option value="">----Select Community----</option>
											<?php
											foreach($projCommList as $k => $v){
												echo '<option value="'.$v['id'].'" '.(($linkArr&&$linkArr['birthCommunity']==$v['id'])?'selected':'').'>'.$v['name'].'</option>';
											}
											?>
										</select>
										<div id="comminfodialog">

										</div>
									</td>
								</tr>
								<tr>
									<td colspan="2" style="font-size:14px;">
										Comments on communities:
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<input type="text" name="commcomments" size="60" value="<?php echo (isset($linkArr['commcomments'])?$linkArr['commcomments']:''); ?>" title="" />
									</td>
								</tr>
							</table>
							<div style="clear:both;float:right;">
								<button type="button" onclick="openCommunityEditor('');" value="Add Community"><b>Add Community</b></button>
							</div>
						</fieldset>
						<fieldset style="padding:10px;margin-top:12px;">
							<legend><b>Relevant Language Information</b></legend>
							<table style="table-layout:fixed;width:460px;">
								<tr>
									<td style="width:175px;font-size:14px;">
										Target language:
										<a id="comminfo" href="#" onclick="return false" title="More information about Institution Code">
											<img src="../../images/qmark_big.png" style="width:15px;" />
										</a>
									</td>
									<td style="font-size:14px;">
										<select name="targetLanguage">
											<option value="">----Select Language----</option>
											<?php
											foreach($projLangList as $k => $v){
												echo '<option value="'.$v['id'].'" '.(($linkArr&&$linkArr['targetLanguage']==$v['id'])?'selected':'').'>'.$v['name'].'</option>';
											}
											?>
										</select>
										<div id="comminfodialog">

										</div>
									</td>
								</tr>
								<tr>
									<td style="width:175px;font-size:14px;">
										Qualification:
										<a id="comminfo" href="#" onclick="return false" title="More information about Institution Code">
											<img src="../../images/qmark_big.png" style="width:15px;" />
										</a>
									</td>
									<td style="font-size:14px;">
										<select name="targetLangQual">
											<option value="">----Select One----</option>
											<option value="native" <?php echo (($linkArr&&$linkArr['targetLangQual']==='native')?'selected':''); ?>>Native language</option>
											<option value="second" <?php echo (($linkArr&&$linkArr['targetLangQual']==='second')?'selected':''); ?>>Second language</option>
											<option value="regional" <?php echo (($linkArr&&$linkArr['targetLangQual']==='regional')?'selected':''); ?>>Regional language</option>
										</select>
										<div id="comminfodialog">

										</div>
									</td>
								</tr>
								<tr>
									<td style="width:175px;font-size:14px;">
										Additional language:
										<a id="comminfo" href="#" onclick="return false" title="More information about Institution Code">
											<img src="../../images/qmark_big.png" style="width:15px;" />
										</a>
									</td>
									<td style="font-size:14px;">
										<select name="secondLanguage">
											<option value="">----Select Language----</option>
											<?php
											foreach($projLangList as $k => $v){
												echo '<option value="'.$v['id'].'" '.(($linkArr&&$linkArr['secondLanguage']==$v['id'])?'selected':'').'>'.$v['name'].'</option>';
											}
											?>
										</select>
										<div id="comminfodialog">

										</div>
									</td>
								</tr>
								<tr>
									<td style="width:175px;font-size:14px;">
										Qualification:
									</td>
									<td style="font-size:14px;">
										<select name="secondLangQual">
											<option value="">----Select One----</option>
											<option value="native" <?php echo (($linkArr&&$linkArr['secondLangQual']==='native')?'selected':''); ?>>Native language</option>
											<option value="second" <?php echo (($linkArr&&$linkArr['secondLangQual']==='second')?'selected':''); ?>>Second language</option>
											<option value="regional" <?php echo (($linkArr&&$linkArr['secondLangQual']==='regional')?'selected':''); ?>>Regional language</option>
										</select>
									</td>
								</tr>
								<tr>
									<td style="width:175px;font-size:14px;">
										Additional language:
									</td>
									<td style="font-size:14px;">
										<select name="thirdLanguage">
											<option value="">----Select Language----</option>
											<?php
											foreach($projLangList as $k => $v){
												echo '<option value="'.$v['id'].'" '.(($linkArr&&$linkArr['thirdLanguage']==$v['id'])?'selected':'').'>'.$v['name'].'</option>';
											}
											?>
										</select>
									</td>
								</tr>
								<tr>
									<td style="width:175px;font-size:14px;">
										Qualification:
									</td>
									<td style="font-size:14px;">
										<select name="thirdLangQual">
											<option value="">----Select One----</option>
											<option value="native" <?php echo (($linkArr&&$linkArr['thirdLangQual']==='native')?'selected':''); ?>>Native language</option>
											<option value="second" <?php echo (($linkArr&&$linkArr['thirdLangQual']==='second')?'selected':''); ?>>Second language</option>
											<option value="regional" <?php echo (($linkArr&&$linkArr['thirdLangQual']==='regional')?'selected':''); ?>>Regional language</option>
										</select>
									</td>
								</tr>
							</table>
						</fieldset>
					</fieldset>
					<fieldset style="width:400px;float:right;clear:right;border: 0;">
						<fieldset style="padding:10px;margin-top:12px;width:400px;margin-left:auto;margin-right:auto;">
							<legend><b>Roles</b></legend>
							<?php
							$roles = array();
							if(array_key_exists('rolearr',$linkArr)) $roles = $linkArr['rolearr'];
							foreach($roleArr as $id => $role){
								echo '<input name="roleid[]" type="checkbox" value="'.$id.'" '.((is_array($roles)&&in_array($id,$roles))?'checked':'').' /> '.$role.'<br />'."\n";
							}
							?>
							<div style="width:100%;margin: 10px 0;clear:both;">
								Comments: <input type="text" name="personnelRoleComments" size="40" value="<?php echo (isset($linkArr['rolecomments'])?$linkArr['rolecomments']:''); ?>" title="" />
							</div>
						</fieldset>
						<fieldset style="padding:10px;margin-top:12px;width:400px;margin-left:auto;margin-right:auto;">
							<legend><b>Project Code</b></legend>
							<table style="table-layout:fixed;width:400px;">
								<tr>
									<td style="width:175px;font-size:14px;">
										Project code:
									</td>
									<td style="font-size:14px;">
										<input type="text" id="projectCode" name="projectCode" size="25" value="<?php echo (isset($linkArr['projectCode'])?$linkArr['projectCode']:''); ?>" title="" />
									</td>
								</tr>
								<tr>
									<td style="width:175px;font-size:14px;">
										Default display:
									</td>
									<td style="font-size:14px;">
										<select id="defaultDisplay" name="defaultDisplay" onchange="">
											<option value='' >------------</option>
											<option value='name' <?php echo ((isset($linkArr['defaultdisplay']) && $linkArr['defaultdisplay'] === 'name')?'selected':''); ?> >Name</option>
											<option value='project' <?php echo ((isset($linkArr['defaultdisplay']) && $linkArr['defaultdisplay'] === 'project')?'selected':''); ?> >Project code</option>
											<option value='random' <?php echo ((isset($linkArr['defaultdisplay']) && $linkArr['defaultdisplay'] === 'random')?'selected':''); ?> >Random Code</option>
										</select>
									</td>
								</tr>
							</table>
						</fieldset>
					</fieldset>
				</div>
				<?php
				if(!$perId){
					?>
					<div style="clear:both;padding:25px;float:right;">
						<button onclick="verifyAddEditForm('','Create Personnel');" type="button"><b>Add Personnel</b></button>
					</div>
					<?php
				}
				else{
					?>
					<div style="clear:both;padding:25px;float:right;">
						<button onclick="verifyAddEditForm(<?php echo $perId; ?>,'Save Personnel Changes');" type="button"><b>Save Changes</b></button>
					</div>
					<?php
				}
				?>
			</fieldset>
			<input id="formaction" name="action" type="hidden" value="" />
			<input id="birthCommID" name="birthCommID" type="hidden" value="" />
			<input id="priLanguageID" name="priLanguageID" type="hidden" value="" />
			<input id="addLanguageID" name="addLanguageID" type="hidden" value="" />
			<input id="addCommID" name="addCommID" type="hidden" value="" />
			<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
			<input name="cplid" type="hidden" value="<?php echo $cplId; ?>" />
			<input name="perid" type="hidden" value="<?php echo $perId; ?>" />
		</form>
	</div>
</div>
<?php
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>
