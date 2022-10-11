<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
include_once('content/lang/misc/aboutproject.'.$LANG_TAG.'.php');
?>
<html>
	<head>
		<title><?php echo (isset($LANG['CONTACTS'])?$LANG['CONTACTS']:'Contacts'); ?></title>
		<?php
		$activateJQuery = false;
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
	</head>
	<body>
		<?php
		$displayLeftMenu = false;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
			<a href="../index.php"><?php echo (isset($LANG['HOME'])?$LANG['HOME']:'Home'); ?></a> &gt;&gt;
			<b><?php echo (isset($LANG['CONTACTS'])?$LANG['CONTACTS']:'Contacts'); ?></b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext" style="margin:10px 20px">
			<h1><?php echo (isset($LANG['CONTACTS'])?$LANG['CONTACTS']:'Contacts'); ?>:</h1>

			<p></p>

		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>