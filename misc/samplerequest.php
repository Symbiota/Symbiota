<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
	<head>
		<title>Sample Use Request</title>
		<?php
		$activateJQuery = false;
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
	</head>
	<body>
		<?php
		$displayLeftMenu = true;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
			<a href="<?php echo $CLIENT_ROOT; ?>/index.php">Home</a> &gt;&gt;
			<b>Sample Use Request</b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext" style="text-align: center;">
			<h1>Sample Use Request</h1>
			<iframe src="https://docs.google.com/forms/d/1FzkQsm45do1SgSWUnLMR0pZ0QGXoME1O9_lJfi0AkUs/viewform?embedded=true" width="790" height="1000px" frameborder="0" marginheight="0" marginwidth="0" style="margin-top: 2rem">Loading…</iframe></iframe>
		</div>
		<?php
			include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
