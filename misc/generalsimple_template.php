<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Page Title</title>
		<?php

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
			<b>New Page</b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext">



		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
