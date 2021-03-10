<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
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
			<a href="../index.php"><?php echo (isset($LANG['HOME'])?$LANG['HOME']:'Home'); ?></a> &gt;
			<b><?php echo (isset($LANG['ABOUT_PROJECT'])?$LANG['ABOUT_PROJECT']:'DenBG > About the Project'); ?></b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext" style="margin:10px 20px">
			<h1><?php echo (isset($LANG['ABOUT_PROJECT'])?$LANG['ABOUT_PROJECT']:'About the Project'); ?>:</h1>
			<p>The Denver EcoFlora Project is designed to connect and engage citizen scientists with biodiversity in the greater Denver metro area. The project has two main goals: 1) to meaningfully engage citizens in observing, protecting, and preserving the metro area's native plant species, and 2) to assemble novel observations and data on the metro area's flora to better inform policy decisions concerning land management and conservation strategies. Researchers and land managers can also use these observations to answer questions such as the influence of climate change on biodiversity, the role of rare species in protecting critical ecosystem functions, and the identification of priority areas for conservation.</p>
		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>