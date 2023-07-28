<?php
include_once('../config/symbini.php');
include_once('../content/lang/index.' . $LANG_TAG . '.php');
header("Content-Type: text/html; charset=" . $CHARSET);
?>
<html>

<head>
	<title><?php echo $DEFAULT_TITLE; ?> Style Guide</title>
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
		<h1>Style Guide</h1>
		<hr>
		<h1>Heading 1</h1>
		<h2>Heading 2</h2>
		<h3>Heading 3</h3>
		<h4>Heading 4</h4>
		<p>Paragraph</p>
		<p><a href="#">Link</a></p>
		<p><button>Button</button></p>
		<p class="grid-3"><span class="button button-primary"><a href="#">Primary Button (Link)</a></span><span class="button button-secondary"><a href="#">Secondary Button (Link)</a></span><span class="button button-tertiary"><a href="#">Tertiary Button (Link)</a></span></p>
	</main>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>

</html>