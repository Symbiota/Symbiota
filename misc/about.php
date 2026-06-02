<?php
include_once('../config/symbini.php');
header('Content-Type: text/html; charset='.$CHARSET);
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Home</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
</head>
<body>
	<?php
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<!-- This is inner text! -->
	<div id="innertext">
		<h1></h1>
		<b>About</b><br><br>

		Under development, text forthcoming
	</div>

	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>