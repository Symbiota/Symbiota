<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
	<head>
		<title>View the Homepage</title>
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
			<b>View the Homepage</b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext">
      <h1 style="text-align: center;">View the Homepage</h1>
      <p>On the NEON Biorepository data portal homepage, you can view periodically updated summary statistics for our collections and find links to more information about NEON and the NEON Biorepository.</p>
      <figure>
        <img src="../misc/images/tutorial_19.jpg" alt="NEON Biorepository Data Portal homepage screenshot">
        <figcaption>NEON Biorepository Data Portal homepage screenshot of top view</figcaption>
      </figure>

      <p>On our homepage, you will also find contact information for the NEON Biorepository. Always feel free to email us at </span><span class="c12"><a class="c5" href="mailto:biorepo@asu.edu">biorepo@asu.edu</a></span><span class="c1">&nbsp;with any inquiries.</span></p>
      <figure>
        <img alt="NEON Biorepository Data Portal homepage screenshot" src="../misc/images/tutorial_24.jpg">
        <figcaption>NEON Biorepository Data Portal homepage screenshot of middle view</figcaption>
      </figure>
		</div>
		<?php
			include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
