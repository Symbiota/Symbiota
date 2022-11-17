<?php
//error_reporting(E_ALL);
 include_once('../config/symbini.php');
 header("Content-Type: text/html; charset=".$CHARSET);
 header("Location: ".$CLIENT_ROOT."/index.php");
 
?>
<html>
	<head>
		<title>Page</title>
		<link rel="stylesheet" href="<?php echo $CLIENT_ROOT; ?>/css/main.css<?php echo (isset($CSS_VERSION_LOCAL)?'?ver='.$CSS_VERSION_LOCAL:''); ?>" type="text/css" />
	</head>
	<body>
		<?php
		$displayLeftMenu = true;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<!-- This is inner text! -->
		<div id="innertext">
			<h1>Forbidden</h1>
			<div style="font-weight:bold;">
				You don't have permission to access this page.
			</div>
			<div style="font-weight:bold;margin:10px;">
				<a href="<?php echo $CLIENT_ROOT; ?>/index.php">Return to index page</a>
			</div>
		</div>
		<?php
			include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
