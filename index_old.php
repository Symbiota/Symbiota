<?php
//error_reporting(E_ALL);
include_once("config/symbini.php");
header("Content-Type: text/html; charset=".$CHARSET);
//header('X-Frame-Options: SAMEORIGIN');
header('X-Frame-Options: deny');
?>
<html>
<head>
	<title><?php echo $defaultTitle?> Home</title>
	<meta http-equiv="X-Frame-Options" content="deny">
	<?php
	$activateJQuery = true;
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once('includes/googleanalytics.php');
	?>
	<meta name='keywords' content='' />
</head>
<body>
	<?php
	include($SERVER_ROOT.'/includes/header.php');
	?>
    <div  id="innertext">
        <div style="padding:10px;">
            The Consortium of Pacific Herbaria (CPH) data portal is an access point for specimen data from herbaria in Hawai'i and the Pacific basin</b></a>.
            <br><br> </div>
    </div>

    ﻿
    <?php
	include($SERVER_ROOT.'/includes/footer.php');
	?> 

</body>
</html>
