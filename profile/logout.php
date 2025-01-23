<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OpenIdProfileManager.php');

$sid = array_key_exists('sid', $_REQUEST) ? htmlspecialchars($_REQUEST['action'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) : '';
$mysession = session_id();

error_log("Remote Session:" . $sid . "Local Session: " . $mysession);



//header('Content-Type: text/html; charset=' . $CHARSET);

/*



$profManager = new OpenIdProfileManager();

$localSessionID = $profManager->lookupLocalSessionIDWithThirdPartySid($sid);
echo $localSessionID;
//$profManager->forceLogout($localSessionID);
$profManager->forceLogout(session_id());

?>


<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">
<head>
	<title><?php echo $DEFAULT_TITLE ?></title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>

</head>
<body>
	<?php
	$displayLeftMenu = (isset($profile_viewprofileMenu)?$profile_viewprofileMenu:"true");
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href='../index.php'><?php echo htmlspecialchars((isset($LANG['HOME'])?$LANG['HOME']:'Home'), ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?></a> &gt;&gt;
		<a href="../profile/viewprofile.php"><?php echo htmlspecialchars((isset($LANG['MY_PROFILE'])?$LANG['MY_PROFILE']:'My Profile'), ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?></a>
	</div>
	<div role="main" id="innertext">
		<h1 class="page-heading">Logged Out</h1>
		
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
*/

?>