<?php
global $SERVER_ROOT, $CSS_BASE_PATH, $CSS_VERSION, $ACCESSIBILITY_ACTIVE, $USER_RIGHTS, $CLIENT_ROOT, $LANG;

include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load('prohibit');

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title><?= $LANG['ACCESS_DENIED'] ?></title>
		<?php include($SERVER_ROOT.'/includes/head.php') ?>
	</head>
	<body>
		<div id="innertext" style="display:flex; align-items: center; justify-content: center; height:100vh;">
			<div style="text-align:center;">
				<h1><?= $LANG['ACCESS_DENIED'] ?></h1>
				<p><?= $LANG['NO_PERMISSION'] ?></p>
				<a href="<?= $CLIENT_ROOT . '/index.php' ?>"><?= $LANG['RETURN'] ?></a>
			</div>
		</div>
	</body>
</html>
