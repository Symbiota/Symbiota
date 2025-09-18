<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT . '/classes/utilities/GeneralUtil.php');
include_once($SERVER_ROOT . '/classes/ProfileManager.php');
if(!empty($THIRD_PARTY_OID_AUTH_ENABLED)){
	include_once($SERVER_ROOT . '/config/auth_config.php');
	require_once($SERVER_ROOT . '/vendor/autoload.php');
}
use Jumbojett\OpenIDConnectClient;
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/profile/index.' . $LANG_TAG . '.php'))
	include_once($SERVER_ROOT.'/content/lang/profile/index.' . $LANG_TAG . '.php');
else include_once($SERVER_ROOT . '/content/lang/profile/index.en.php');
header('Content-Type: text/html; charset=' . $CHARSET);

$login = isset($_REQUEST['login']) ? $_REQUEST['login'] : '';
$remMe = isset($_POST['remember']) ? 1 : 0;
$emailAddr = isset($_POST['email']) ? $_POST['email'] : '';
$resetPwd = (isset($_REQUEST['resetpwd']) && $_REQUEST['resetpwd'] == 1) ? 1 : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';
if(!$action && array_key_exists('submit', $_REQUEST)) $action = $_REQUEST['submit'];

$pHandler = new ProfileManager();

$statusStr = '';
//Sanitation
if($login){
	if(!$pHandler->setUserName($login)){
		$login = '';
		$statusStr = $LANG['INVALID_LOGIN'] . '<ERR/>';
	}
}
if($emailAddr){
	if(!$pHandler->validateEmailAddress($emailAddr)){
		$emailAddr = '';
		$statusStr = $LANG['INVALID_EMAIL'] . '<ERR/>';
	}
}

$refUrl = '';
if(array_key_exists('refurl',$_REQUEST)){
	//Code rebuilds a sanitized redirect URL, which will get parsed into numerous import variables that need to be reappended to redirect url
	$refGetStr = '';
	foreach($_GET as $k => $v){
		$k = htmlspecialchars($k, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
		if($k != 'refurl'){
			if($k == 'attr' && is_array($v)){
				foreach($v as $v2){
					$v2 = htmlspecialchars($v2, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
					$refGetStr .= '&attr[]='.$v2;
				}
			}
			else{
				$v = htmlspecialchars($v, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
				$refGetStr .= '&'.$k.'='.$v;
			}
		}
	}
	$refUrl = str_replace('&amp;','&',htmlspecialchars($_REQUEST['refurl'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE));
	if(substr($refUrl,-4) == '.php') $refUrl .= '?'.substr($refGetStr,1);
	else $refUrl .= $refGetStr;
}

if($remMe) $pHandler->setRememberMe(true);
if($action == 'logout'){
	$redirect = GeneralUtil::getDomain() . $CLIENT_ROOT . '/index.php';
	//check if using third party auth
	if(array_key_exists('AUTH_PROVIDER', $_SESSION)){
		$oidc = new OpenIDConnectClient($PROVIDER_URLS[$_SESSION['AUTH_PROVIDER']], $CLIENT_IDS[$_SESSION['AUTH_PROVIDER']], $CLIENT_SECRETS[$_SESSION['AUTH_PROVIDER']], $PROVIDER_URLS[$_SESSION['AUTH_PROVIDER']]);
		$pHandler->reset();
		$oidc->signOut($_SESSION['AUTH_CLIENT_ID'], $redirect);
	}
	else{
		$pHandler->reset();
		header('Location: ' . $redirect);
		exit;
	}
}
elseif($action == 'login'){
	if($pHandler->authenticate($_POST['password'])){
		if(!$refUrl || (strtolower(substr($refUrl,0,4)) == 'http') || strpos($refUrl,'newprofile.php')){
			header('Location: ../index.php');
		}
		else{
			//Only relative paths are redirected to target
			header('Location: '.$refUrl);
		}
		exit;
	}
	else{
		if($pHandler->getErrorMessage()){
			$statusStr = $pHandler->getErrorMessage();
		}
		else{
			$statusStr = $LANG['INCORRECT'] . '<ERR/>';
			error_log('Authorization of user <F-USER>' . $login . '</F-USER> to access ' . $_SERVER['PHP_SELF']. ' failed', 0);
		}
	}
}
elseif($action == 'retrieveLogin'){
	if($emailAddr){
		if($pHandler->lookupUserName($emailAddr)){
			$statusStr = $LANG['LOGIN_EMAILED'] . ': ' . $emailAddr;
		}
		else{
			$statusStr = $LANG['EMAIL_ERROR'] . ' (' . $pHandler->getErrorMessage() . ')<ERR/>';
		}
	}
}
elseif($resetPwd){
	if($email = $pHandler->resetPassword($login)){
		$statusStr = $LANG['PWD_EMAILED'] . ': ' . $email . '<ERR/>';
	}
	else{
		$statusStr = $LANG['RESET_FAILED'] . '<ERR/>';
		if($pHandler->getErrorMessage()) $statusStr .= ' ('.$pHandler->getErrorMessage().')';
	}
}
else{
	$statusStr = $pHandler->getErrorMessage();
}

if($SYMB_UID){
	//Redirect logged in users somewhere else, with user's profile page the default target
	if(!empty($_REQUEST['refurl']) && substr($_REQUEST['refurl'], 0, 1) == '/'){
		header('Location:' . GeneralUtil::getDomain() . $CLIENT_ROOT . $_REQUEST['refurl']);
		exit;
	}
	elseif(!empty($_SESSION['refurl']) && substr($_SESSION['refurl'], 0, 1) == '/'){
		header('Location:' . GeneralUtil::getDomain() . $CLIENT_ROOT . $_SESSION['refurl']);
		unset($_SESSION['refurl']);
		exit;
	}
	else{
		header('Location:' . GeneralUtil::getDomain() . $CLIENT_ROOT . '/profile/viewprofile.php');
		exit;
	}
}

if (array_key_exists('last_message', $_SESSION)){
	$statusStr .= $_SESSION['last_message'];
	unset($_SESSION['last_message']);
}
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<title><?= $DEFAULT_TITLE . ' ' . $LANG['LOGIN_NAME'] ?></title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		if(!navigator.cookieEnabled){
			alert("<?= $LANG['COOKIES'] ?>");
		}

		function resetPassword(){
			if(document.getElementById("portal-login").value == ""){
				alert("<?= $LANG['ENTER_LOGIN_NO_PWD'] ?>");
				return false;
			}
			document.getElementById("resetpwd").value = "1";
			document.forms["loginform"].submit();
		}

		function checkCreds(f){
			if(f.password.value == ""){
				alert("<?= $LANG['ENTER_LOGIN'] ?>");
				return false;
			}
			return true;
		}
	</script>
	<script src="../js/symb/shared.js" type="text/javascript"></script>
	<style>
		.profile-fieldset {
			padding: 20px;
			background-color: #f9f9f9;
			border: 2px outset #808080;
		}
		.profile-legend {
			font-weight: bold;
		}
		.justify-center-full-screen {
			display: flex;
			justify-content: center;
		}
		.flex-item-login {
			width: 100%;
			max-width: 30rem;
			margin-left: auto;
			margin-right: auto;
		}
	</style>
</head>
<body>
<?php
$displayLeftMenu = (isset($profile_indexMenu)?$profile_indexMenu:'true');
include($SERVER_ROOT.'/includes/header.php');
?>
<div class="navpath"></div>
<!-- inner text -->
<div role="main" id="innertext">
	<h1 class="page-heading screen-reader-only"><?= $LANG['LOGIN'] ?></h1>
	<?php
	if($statusStr){
		$color = 'green';
		if(strpos($statusStr, '<ERR/>')) $color = 'red';
		?>
		<div style='color:<?= $color ?>;margin: 1em 1em 0em 1em;'>
			<?php
			echo $statusStr;
			?>
		</div>
		<?php
	}
	$SYMBIOTA_LOGIN_ENABLED = $SYMBIOTA_LOGIN_ENABLED ?? true;
	?>
	<div class="gridlike-form justify-center-full-screen">
		<div class="flex-item-login bottom-breathing-room-rel">
			<form id="loginform" name="loginform" action="index.php" onsubmit="return checkCreds(this);" method="post">
				<?php
				if($SYMBIOTA_LOGIN_ENABLED){
					?>
					<fieldset class="profile-fieldset">
						<legend class="profile-legend"><?= $LANG['PORTAL_LOGIN'] ?></legend>
						<div>
							<label for="portal-login"><?= $LANG['LOGIN_NAME'] ?></label>:
							<input id="portal-login" name="login" value="<?= $login; ?>" style="border-style:inset;" required />
						</div>
						<div>
							<label for="password"><?= $LANG['PASSWORD'] ?></label>:
							<input type="password" id="password" name="password"  style="border-style:inset;" autocomplete="off" />
						</div>
						<div>
							<input type="checkbox" value='1' name="remember" id="remember" checked >
							<label for="remember">
								<?= $LANG['REMEMBER'] ?>
							</label>
						</div>
						<div>
							<input type="hidden" name="refurl" value="<?= $refUrl; ?>" />
							<input type="hidden" id="resetpwd" name="resetpwd" value="">
							<button name="action" type="submit" value="login"><?= $LANG['SIGNIN'] ?></button>
						</div>
					</fieldset>
					<?php
				}
				?>
			</form>
		</div>
		<?php
		if(!empty($THIRD_PARTY_OID_AUTH_ENABLED)){
			if($refUrl) $_SESSION['refurl'] = $refUrl;
			if(empty($LOGIN_ACTION_PAGE)) $LOGIN_ACTION_PAGE = $CLIENT_ROOT . '/profile/openIdAuth.php';
			?>
			<div class="flex-item-login bottom-breathing-room-rel">
				<form action='<?= $LOGIN_ACTION_PAGE ?>' onsubmit="">
					<fieldset  class="profile-fieldset">
						<legend class="profile-legend"><?= $LANG['THIRD_PARTY_LOGIN'] ?></legend>
						<div class="justify-center">
							<button type="submit" value="login"><?= $LANG['OID_LOGIN'] ?></button>
						</div>
					</fieldset>
				</form>
			</div>
			<?php
		}
		?>
		<div class="flex-item-login" style="text-align:center">
			<?php
			$shouldBeAbleToCreatePublicUser = $SHOULD_BE_ABLE_TO_CREATE_PUBLIC_USER ?? true;
			if($shouldBeAbleToCreatePublicUser){
				?>
				<div style="font-weight:bold;">
					<?= $LANG['NO_ACCOUNT'] ?>
				</div>
				<div>
					<a href="newprofile.php?refurl=<?= $refUrl ?>"><?= $LANG['CREATE_ACCOUNT'] ?></a>
				</div>
				<?php
	 		}
			if($SYMBIOTA_LOGIN_ENABLED){ ?>
				<div style="font-weight:bold;margin-top:5px">
					<?= $LANG['REMEMBER_PWD'] ?>
				</div>
				<a href="#" onclick="resetPassword();"><?= $LANG['REST_PWD'] ?></a>
				<div style="font-weight:bold;margin-top:5px">
					<?= $LANG['REMEMBER_LOGIN'] ?>
				</div>
				<div>
					<div><a href="#" onclick="toggle('emaildiv');"><?= $LANG['RETRIEVE'] ?></a></div>
					<div id="emaildiv" style="display:none;">
						<fieldset class="profile-fieldset">
							<form id="retrieveloginform" name="retrieveloginform" action="index.php" method="post">
								<div style="text-align:left">
									<label for="email"><?= $LANG['YOUR_EMAIL'] ?></label>:
									<input id="email" name="email" type="text" required />
								</div>
								<div>
									<button name="action" type="submit" value="retrieveLogin"><?= $LANG['RETRIEVE'] ?></button>
								</div>
							</form>
						</fieldset>
					</div>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>
<?php include($SERVER_ROOT.'/includes/footer.php'); ?>
</body>
</html>
