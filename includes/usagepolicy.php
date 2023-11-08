<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=" . $CHARSET);
?>
<html>

<head>
	<title><?php echo $DEFAULT_TITLE; ?> Data Usage Policy</title>
	<?php
	$activateJQuery = true;
	include_once($SERVER_ROOT . '/includes/head.php');
	?>
</head>

<body>
	<?php
	$displayLeftMenu = true;
	include($SERVER_ROOT . '/includes/header.php');
	?>
	<div class="navpath">
		<a href="<?php echo $CLIENT_ROOT; ?>/index.php">Home</a> &gt;&gt;
		<b>Data Usage Policy</b>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<p>This page has been archived. To check data usage guidelines and citations, please visit the <a href="<?php echo $CLIENT_ROOT; ?>/misc/cite.php">How to Cite: Ways to Acknowledge and Cite the Use of the NEON Biorepository</a>.</p>
	</div>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>

</html>