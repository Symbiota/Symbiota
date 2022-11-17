<?php
include_once('../../../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
header("Location: ".$CLIENT_ROOT."/index.php");
?>
<html>
	<head>
		<title>Forbidden</title>
		<?php
		$activateJQuery = false;
		if(file_exists($SERVER_ROOT.'/includes/head.php')){
			include_once($SERVER_ROOT.'/includes/head.php');
		}
		else{
			echo '<link href="'.$CLIENT_ROOT.'/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
			echo '<link href="'.$CLIENT_ROOT.'/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
		}
		?>
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