<?php
include_once('config/symbini.php');
include_once('content/lang/index.' . $LANG_TAG . '.php');
header("Content-Type: text/html; charset=" . $CHARSET);
?>
<html>

<head>
	<title><?php echo $DEFAULT_TITLE; ?> Home</title>
	<?php
	include_once($SERVER_ROOT . '/includes/head.php');
	include_once($SERVER_ROOT . '/includes/googleanalytics.php');
	?>
</head>

<body>
	<?php
	include($SERVER_ROOT . '/includes/header.php');
	?>
	<main id="innertext">
	</main>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>

</html>