<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
	<head>
		<title>Conduct a Sample Search</title>
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
    <style>
      #innertext {
        /* max-width: 100% !important; */
      }
      #guide {
        display: grid; 
        grid-template-columns: minmax(150px, 25%) 1fr;
      }
      article {
        background-color: #ededed;
        padding: 10px;
        margin: 10px 0;
        border-radius: 6px;
      }
      figcaption {
        background-color: #002d74;
        color: #ffffff;
        padding: 0 1em;
      }
      figure {
        border: 1px solid #002d74;
        -webkit-box-shadow: 9px 10px 12px -10px rgba(0,0,0,0.5);
        -moz-box-shadow: 9px 10px 12px -10px rgba(0,0,0,0.5);
        box-shadow: 9px 10px 12px -10px rgba(0,0,0,0.5);
      }
    </style>
	</head>
	<body>
		<?php
		$displayLeftMenu = true;
		include($SERVER_ROOT.'/includes/header.php');
		?>
    <div class="navpath">
      <a href="<?php echo $CLIENT_ROOT; ?>/index.php">Home</a> &gt;&gt;
      <a href="<?php echo $CLIENT_ROOT; ?>/misc/tutorials.php">Tutorials</a> &gt;&gt;   
      <b>Conduct a Sample Search</b>
    </div>
		<!-- This is inner text! -->
		<div id="innertext">
      <h1 style="text-align: center;">Conduct a Sample Search</h1>
      <em>Updated to demonstrate the new search page.</em>
      <p>Use the Sample Search feature of the NEON Biorepository data portal to search for, download, and map available NEON samples based on collection, taxon, location, and more.</p>     
      <h2>Understand the new search interface</h2>
      <iframe src="../neon/tutorials/searchinterface.html" height="70%" width="100%" frameborder="0"></iframe>
      <p><a href="<?php echo $CLIENT_ROOT; ?>/neon/tutorials/NEON-Biorepository-Tutorial_Understanding-Search-Interface_v202105.pdf">Click to download this tutorial (PDF)</a>.</p>
      <h2>Use the results</h2>
      <iframe src="../neon/tutorials/searchresults.html" height="70%" width="100%" frameborder="0"></iframe>
      <p><a href="<?php echo $CLIENT_ROOT; ?>/neon/tutorials/NEON-Biorepository-Tutorial_Using-Search-Results_v202105.pdf">Click to download this tutorial (PDF)</a>.</p>
    </div>
		<?php
			include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
