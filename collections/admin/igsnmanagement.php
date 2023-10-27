<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceSesar.php');
header("Content-Type: text/html; charset=".$CHARSET);
ini_set('max_execution_time', 3600);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/admin/igsnmanagement.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$username = array_key_exists('username',$_REQUEST)?$_REQUEST['username']:'';
$pwd = array_key_exists('pwd',$_REQUEST)?$_REQUEST['pwd']:'';
$namespace = array_key_exists('namespace',$_REQUEST)?$_REQUEST['namespace']:'';
$generationMethod = array_key_exists('generationMethod',$_REQUEST)?$_REQUEST['generationMethod']:'';
$action = array_key_exists('formsubmit',$_POST)?$_POST['formsubmit']:'';

//Variable sanitation
if(!is_numeric($collid)) $collid = 0;
if(preg_match('/[^A-Z]+/', $namespace)) $namespace = '';
if(!in_array($generationMethod,array('inhouse','sesar'))) $generationMethod = '';

$statusStr = '';
$isEditor = 0;
if($IS_ADMIN || (array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin']))){
	$isEditor = 1;
}
$guidManager = new OccurrenceSesar();
$guidManager->setCollid($collid);
$guidManager->setCollArr();
$guidManager->setSesarUser($username);
$guidManager->setSesarPwd($pwd);
$guidManager->setNamespace($namespace);
$guidManager->setGenerationMethod($generationMethod);

if($action){
	if($action == 'saveProfile'){
		$guidManager->saveProfile();
	}
	elseif($action == 'deleteProfile'){
		$guidManager->deleteProfile();
		$namespace = '';
		$generationMethod = '';
	}
}

$sesarProfile = $guidManager->getSesarProfile();
if(isset($sesarProfile['namespace'])) $namespace = $sesarProfile['namespace'];
if(isset($sesarProfile['generationMethod'])) $generationMethod = $sesarProfile['generationMethod'];
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title>IGSN GUID Management</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript">
		function validateCredentials(f){
			if(f.username.value == "" || f.pwd.value == ""){
				alert("Please enter valid SESAR username and password");
				return false;
			}
			else if(f.username.value.indexOf("@") == -1){
				alert("SESAR username must be an email address");
				return false;
			}
			$.ajax({
				method: "POST",
				data: { username: f.username.value, password: f.pwd.value },
				dataType: "xml",
				url: "https://app.geosamples.org/webservices/credentials_service_v2.php"
			})
			.done(function(xml) {
				var valid = $(xml).find('valid').text();
				if(valid == "yes"){
					$(xml).find('user_codes').each(function(){
	                    $(this).find("user_code").each(function(){
	                        var userCode = $(this).text();
	                        $('#nsSelect').append(new Option(userCode, userCode));
	                    });
	                });
                    $("#igsn-reg-div").show();
                    $("#validate-button").hide();
                    $("#valid-span").show();
                    $("#notvalid-span").hide();
				}
				else{
					alert($(xml).find('error').text());
	                $("#igsn-reg-div").hide();
	                $("#validate-button").show();
	                $("#valid-span").hide();
	                $("#notvalid-span").show();
				}
			})
			.fail(function() {
				alert("Validation call failed");
	            $("#igsn-reg-div").hide();
	            $("#validate-button").show();
	            $("#valid-span").hide();
	            $("#notvalid-span").show();
			});
		}

		function verifyProfileForm(f){
			if(f.namespace.value == ""){
				alert("Select a namespace");
				return false;
			}
			return true;
		}
	</script>
	<style type="text/css">
		fieldset{ margin:10px; padding:15px; }
		fieldset legend{ font-weight:bold; }
		.form-label{  }
		button{ margin:15px; }
	</style>
</head>
<body>
<?php
$displayLeftMenu = false;
include($SERVER_ROOT.'/includes/header.php');
?>
<div class='navpath'>
	<a href="../../index.php">Home</a> &gt;&gt;
	<a href="../misc/collprofiles.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>&emode=1">Collection Management</a> &gt;&gt;
	<a href="igsnmapper.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>">IGSN GUID Generator</a> &gt;&gt;
	<b>IGSN Management</b>
</div>
<div id="innertext">
	<?php
	if($isEditor && $collid){
		echo '<h3>IGSN Management: '.$guidManager->getCollectionName().'</h3>';
		if($statusStr){
			?>
			<fieldset>
				<legend>Error Panel</legend>
				<?php echo $statusStr; ?>
			</fieldset>
			<?php
		}
		if(!$guidManager->getProductionMode()){
			echo '<h2 style="color:orange">-- In Development Mode --</h2>';
		}
		if($namespace){
			$guidCnt = $guidManager->getGuidCount($collid);
			$guidMissingCnt = $guidManager->getMissingGuidCount();
			$guidAllCollCnt = $guidManager->getGuidCount();
			?>
			<fieldset>
				<legend>IGSN Profile Details & Statistics</legend>
				<p><span class="form-label">IGSN Namespace:</span> <?php echo $namespace; ?></p>
				<p><span class="form-label">IGSN generation method:</span> <?php echo $generationMethod; ?></p>
				<p><span class="form-label">GUIDs within collection:</span> <?php echo $guidCnt; ?></p>
				<p><span class="form-label">Occurrences without GUIDs:</span> <?php echo $guidMissingCnt; ?></p>
				<?php
				if($guidAllCollCnt > $guidCnt){
					?>
					<p><span class="form-label">GUIDs using above namespace across all collections:</span> <?php echo $guidAllCollCnt; ?></p>
					<?php
				}
				?>
				<div style="margin:10px;">
					<form name="deleteform" action="igsnmanagement.php" method="post">
						<input type="hidden" name="collid" value="<?php echo $collid; ?>" />
						<input type="hidden" name="namespace" value="<?php echo $namespace; ?>" />
						<span style="margin-left:10px;">
							<button name="formsubmit" type="submit" value="deleteProfile" onclick="return confirm('Are you sure you want to delete this profile?')">Delete Profile</button>
						</span>
						<span style="margin-left:10px;">
							<a href="igsnmapper.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>"><button type="button">Go to Mapper</button></a>
						</span>
					</form>
				</div>
				<div style="margin:10px;">
					<form name="verifyigsnform" action="igsnverification.php" method="post">
						<input type="hidden" name="collid" value="<?php echo $collid; ?>" />
						<input type="hidden" name="namespace" value="<?php echo $namespace; ?>" />
						<span style="margin-left:10px;">
							<button name="formsubmit" type="submit" value="verifysesar">Verify SESAR GUIDs</button>
						</span>
					</form>
				</div>
			</fieldset>
			<?php
		}
		else{
			?>
			<form name="profileform" action="igsnmanagement.php" method="post" onsubmit="return verifyProfileForm(this)">
				<fieldset>
					<legend>IGSN Registration Profile</legend>
					<p>
						<div>
							<span class="form-label">Username:</span> <input name="username" type="text" value="<?php echo $username; ?>" />
							<span id="valid-span" style="display:none;color:green">Credentials Valid!</span>
							<span id="notvalid-span" style="display:none;color:orange">Credentials Not Valid</span>
						</div>
						<div><span class="form-label">Password:</span> <input name="pwd" type="password" value="<?php echo $pwd; ?>" /></div>
						<button id="validate-button" type="button" onclick="validateCredentials(this.form)">Validate Credentials</button>
					</p>
					<div id="igsn-reg-div" style="margin-top:20px;display:none">
						<p>
							<span class="form-label">IGSN Namespace:</span>
							<select id="nsSelect" name="namespace">
								<option value="">-- Select an IGSN Namespace --</option>
								<option value="">------------------------------</option>
							</select>
						</p>
						<p>
							<span class="form-label">IGSN Generation Method:</span>
							<select name="generationMethod">
								<option value='sesar'>SESAR generates IGSN (recommended)</option>
								<option value='inhouse'>Generate IGSN in-house</option>
							</select>
						</p>
						<p>
							<button name="formsubmit" type="submit" value="saveProfile">Save Profile</button>
							<input type="hidden" name="collid" value="<?php echo $collid; ?>" />
						</p>
					</div>
				</fieldset>
			</form>
			<?php
		}
	}
	else echo '<h2>You are not authorized to access this page or collection identifier has not been set</h2>';
	?>
</div>
<?php
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>