<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
	<head>
		<title>Tutorials and Help</title>
    <?php
      $activateJQuery = false;
      if(file_exists($SERVER_ROOT.'/includes/head.php')){
        include_once($SERVER_ROOT.'/includes/head.php');
      }
      else{
        echo '<link href="'.$CLIENT_ROOT.'/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
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
		<div class="navpath">
			<a href="<?php echo $CLIENT_ROOT; ?>/index.php">Home</a> &gt;&gt;
			<b>Tutorials and Help</b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext">
      <h1 style="text-align: center;">Tutorials and Help</h1>
      <p>Find more information on how to use the NEON Biorepository Data Portal by clicking on these links:</p>
      <ul>
        <li><a href="<?php echo $CLIENT_ROOT; ?>/misc/hometutorial.php">View the Homepage</a></li>
        <li><a href="<?php echo $CLIENT_ROOT; ?>/misc/searchtutorial.php">Conduct a Sample Search</a></li>
        <li><a href="<?php echo $CLIENT_ROOT; ?>/misc/mapsearchtutorial.php">Conduct a Map Search</a></li>
        <li><a href="<?php echo $CLIENT_ROOT; ?>/misc/gettingstarted.php">Getting Started and Frequently Asked Questions</a></li>
      </ul>



		</div>
		<?php
			include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
