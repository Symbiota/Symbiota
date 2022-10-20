<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
include_once('content/lang/misc/aboutproject.'.$LANG_TAG.'.php');
?>
<html>
	<head>
		<title>About Project</title>
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
			<b><?php echo (isset($LANG['ABOUT_PROJECT'])?$LANG['ABOUT_PROJECT']:'About the Project'); ?></b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext" style="margin:10px 20px">
			<h1><?php echo (isset($LANG['ABOUT_PROJECT'])?$LANG['ABOUT_PROJECT']:'About the Project'); ?>:</h1>

			<p></p>

			<h1><?php echo (isset($LANG['FUNDING'])?$LANG['FUNDING']:'Funding'); ?>:</h1>

			<p>This portal has been supported by the following NSF Awards:</p>

			<p>
				Thematic Collections Network Award:
				<a href="https://www.nsf.gov/awardsearch/showAward?AWD_ID=------" target="_blank">-----</a>
			</p>

		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>