<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/content/lang/prohibit.'.$LANG_TAG.'.php')
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
	<head>
		<title><?php echo $LANG['NO_ACCESS']; ?></title>
	<?php

	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	</head>
	<body>
		<?php
		$displayLeftMenu = false;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<!-- This is inner text! -->
		<div id="innertext">
			<h1><?php echo $LANG['FORBIDDEN']; ?></h1>
			<div style="font-weight:bold;">
				<?php echo $LANG['NO_PERMISSION']; ?>.
			</div>
			<div style="font-weight:bold;margin:10px;">
				<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/index.php"><?php echo htmlspecialchars($LANG['RETURN'], HTML_SPECIAL_CHARS_FLAGS); ?></a>
			</div>
		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>