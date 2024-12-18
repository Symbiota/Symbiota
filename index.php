<?php
include_once('config/symbini.php');
if($LANG_TAG == 'en' || !file_exists($SERVER_ROOT.'/content/lang/templates/index.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/templates/index.en.php');
else include_once($SERVER_ROOT.'/content/lang/templates/index.'.$LANG_TAG.'.php');
header('Content-Type: text/html; charset=' . $CHARSET);
?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">
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
	<div class="navpath"></div>
	<main id="innertext">
		<h1 class="page-heading screen-reader-only"><?php echo $DEFAULT_TITLE; ?> Home</h1>
			<div>
				<h1>Welcome!</h1>
				<p>
					The Consortium of Pacific Herbaria (CPH) data portal is an access point for specimen data from herbaria
					in Hawai'i and the Pacific basin.
				</p>
			</div>
		<div style="align-items:center;">
			<img src="images/layout/HAW15533.jpg" style="width:200px;margin-left:40px;margin-right:20px;">
                        <img src="images/layout/HAW42984.jpg" style="width:200px;margin-right:20px;">
                        <img src="images/layout/HAW09638.jpg" style="width:200px;margin-right:20px;">
                	<img src="images/layout/HAW44773.jpg" style="width:200px;">
		</div>
	</main>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>
</html>
