<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
	<head>
		<title>Checklists</title>
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
			<b>Checklists</b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext">
      <h1>Checklists</h1>
      <ul>
        <li><a href="https://biorepo.neonscience.org/portal/projects/index.php?pid=6">Research Sites - Invertebrates</a></li>
        <li><a href="https://biorepo.neonscience.org/portal/projects/index.php?pid=1">Research Sites - Plants</a></li>
        <li><a href="https://biorepo.neonscience.org/portal/projects/index.php?pid=7">Research Sites - Vertebrates</a></li>
      </ul>
		</div>
		<?php
			include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
