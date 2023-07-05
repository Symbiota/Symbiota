<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceSesar.php');
header("Content-Type: text/html; charset=".$CHARSET);
ini_set('max_execution_time', 3600);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/admin/igsnmapper.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$username = array_key_exists('username',$_REQUEST)?$_REQUEST['username']:'';
$pwd = array_key_exists('pwd',$_REQUEST)?$_REQUEST['pwd']:'';
$registrationMethod = array_key_exists('registrationMethod',$_REQUEST)?$_REQUEST['registrationMethod']:'';
$igsnSeed = array_key_exists('igsnSeed',$_REQUEST)?$_REQUEST['igsnSeed']:'';
$processingCount = array_key_exists('processingCount',$_REQUEST)?$_REQUEST['processingCount']:10;
$action = array_key_exists('formsubmit',$_POST)?$_POST['formsubmit']:'';

//Variable sanitation
if(!is_numeric($collid)) $collid = 0;
if(!in_array($registrationMethod,array('api','csv','xml'))) $registrationMethod = '';
if(preg_match('/[^A-Z0-9]+/', $igsnSeed)) $igsnSeed = '';
if($processingCount && !is_numeric($processingCount)) $processingCount = 10;

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
$guidManager->setRegistrationMethod($registrationMethod);

$sesarProfile = $guidManager->getSesarProfile();
$namespace = '';
if(isset($sesarProfile['namespace'])) $namespace = $sesarProfile['namespace'];
$generationMethod = '';
if(isset($sesarProfile['generationMethod'])) $generationMethod = $sesarProfile['generationMethod'];

if($igsnSeed) $guidManager->setIgsnSeed($igsnSeed);
elseif($generationMethod == 'inhouse') $igsnSeed = $guidManager->getIgsnSeed();

if($action == 'populateGUIDs'){
	if($registrationMethod == 'xml'){
		$guidManager->setVerboseMode(0);
		if($guidManager->batchProcessIdentifiers($processingCount)){
			exit;
		}
		else{
			$statusStr = '<div><span style="color:red">Error Message:</span> '.$guidManager->getErrorMessage().'</div>';
			if($warningArr = $guidManager->getWarningArr()){
				foreach($warningArr as $errMsg){
					$statusStr .= '<div style="margin-left:10px">'.$errMsg.'</div>';
				}
			}
		}
	}
}
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title>IGSN GUID Mapper</title>
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
					//$(xml).find('user_codes').each(function(){
						//$(this).find("user_code").each(function(){
							//var userCode = $(this).text();
						//});
					//});
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

		function generationMethodChanged(elem){
			if(elem.value == "sesar"){
				$("#igsnseed-div").hide();
			}
			else{
				generateIgsnSeed();
			}
		}

		function generateIgsnSeed(){
			var f = document.guidform;
			$("#igsnseed-div").show();
			$.ajax({
				method: "POST",
				data: { collid: f.collid.value },
				dataType: "text",
				url: "rpc/getigsnseed.php"
			})
			.done(function(responseStr) {
				f.igsnSeed.value = responseStr;
			});
		}

		function verifyGuidForm(f){
			if(f.registrationMethod.value == ""){
				alert("Select a registration method");
				return false;
			}
			<?php
			if($generationMethod == 'inhouse'){
				?>
				else if(f.igsnSeed.value == ""){
					alert("In-house IGSN Generation selected but IGSN seed not generated (contact administrator)");
					return false;
				}
				<?php
			}
			?>
			setTimeout(function(){
				//f.igsnSeed.value = "";
			}, 100);
			return true;
		}
	</script>
	<style type="text/css">
		fieldset{ margin:10px; padding:15px; }
		fieldset legend{ font-weight:bold; }
		.form-label{ font-weight: bold; }
		button{ margin:15px; }
	</style>
</head>
<body>
<?php
$displayLeftMenu = 'false';
include($SERVER_ROOT.'/includes/header.php');
?>
<div class='navpath'>
	<a href="../../index.php">Home</a> &gt;&gt;
	<a href="../misc/collprofiles.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>&emode=1">Collection Management</a> &gt;&gt;
	<a href="igsnmanagement.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>">IGSN GUID Management</a> &gt;&gt;
	<b>IGSN Mapper</b>
</div>
<!-- This is inner text! -->
<div id="innertext">
	<?php
	if($isEditor && $collid){
		echo '<h3>'.$guidManager->getCollectionName().'</h3>';
		if($statusStr){
			?>
			<fieldset style="margin:10px;">
				<legend>Error Panel</legend>
				<?php echo $statusStr; ?>
			</fieldset>
			<?php
		}
		if(!$guidManager->getProductionMode()){
			echo '<h2 style="color:orange">-- In Development Mode --</h2>';
		}
		if($namespace && $generationMethod){
			if($action == 'populateGUIDs'){
				if($registrationMethod == 'api'){
					echo '<fieldset>';
					echo '<legend>Action Panel</legend>';
					echo '<ul>';
					$guidManager->batchProcessIdentifiers($processingCount);
					echo '<ul>';
					echo '</fieldset>';
				}
			}
			?>
			<form id="guidform" name="guidform" action="igsnmapper.php" method="post" onsubmit="return verifyGuidForm(this)">
				<input type="hidden" name="collid" value="<?php echo $collid; ?>" />
				<fieldset>
					<legend>IGSN Registration Control Panel</legend>
					<p>Register IGSNs via a manual submission of data file or automaticly using <a href="http://www.geosamples.org/interop" target="_blank">SESAR API Web Services</a></p>
					<p>
						<div>
							<span class="form-label">Username:</span> <input name="username" type="text" value="<?php echo $username; ?>" />
							<span id="valid-span" style="display:none;color:green">Credentials Valid!</span>
							<span id="notvalid-span" style="display:none;color:orange">Credentials Not Valid</span>
						</div>
						<div><span class="form-label">Password:</span> <input name="pwd" type="password" value="<?php echo $pwd; ?>" /></div>
						<button id="validate-button" type="button" onclick="validateCredentials(this.form)">Validate Credentials</button>
					</p>
					<div style="margin:10px 0px"><hr/></div>
					<div style="margin:10px 0px">
						<p><b>Occurrences without GUIDs:</b> <?php echo $guidManager->getMissingGuidCount(); ?></p>
					</div>
					<div id="igsn-reg-div" style="margin-top:20px;display:none;">
						<p>
							<span class="form-label">IGSN Namespace:</span>
							<?php echo $namespace; ?>
						</p>
						<p>
							<span class="form-label">IGSN generation method:</span>
							<?php echo $generationMethod; ?>
						</p>
						<div id="igsnseed-div" style="display:<?php echo ($generationMethod=='inhouse'?'':'none'); ?>">
							<p>
								<span class="form-label">IGSN seed:</span>
								<input name="igsnSeed" type="text" value="<?php echo $igsnSeed; ?>" />
								<span style=""><a href="#" onclick="generateIgsnSeed();return false;"><img src="../../images/refresh.png" style="width:14px;vertical-align: middle;" /></a></span>
							</p>
						</div>
						<p>
							<span class="form-label">Registration method:</span>
							<select name="registrationMethod">
								<option value=''>-- Select Method --</option>
								<option value=''>----------------------------</option>
								<option value='api' <?php echo ($registrationMethod=='api'?'SELECTED':''); ?>>SESAR API</option>
								<!--  <option value='csv' <?php echo ($registrationMethod=='csv'?'SELECTED':''); ?>>Export CSV</option>  -->
								<option value='xml' <?php echo ($registrationMethod=='xml'?'SELECTED':''); ?>>Export XML</option>
							</select>
						</p>
						<p>
							<span class="form-label">Number of identifiers to generate: </span>
							<input name="processingCount" type="text" value="10" /> (leave blank to process all specimens)
						</p>
						<p>
							<button name="formsubmit" type="submit" value="populateGUIDs" <?php echo ($namespace && $generationMethod?'':'disabled'); ?>>Populate Collection GUIDs</button>
						</p>
					</div>
				</fieldset>
			</form>
			<?php
		}
		else{
			echo '<h2><span style="color:red">FATAL ERROR:<span> namespace and generationMethod is not set</h2>';
		}
	}
	else{
		echo '<h2>You are not authorized to access this page or collection identifier has not been set</h2>';
	}
	?>
</div>
<?php
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>