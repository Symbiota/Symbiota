<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
	<head>
		<title>Conduct a Map Search</title>
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
			<b>Conduct a Map Search</b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext">
      <h1 style="text-align: center;">Conduct a Map Search</h1>
      <p>We can use the "Map Search" feature of the NEON Biorepository data portal to visualize and download available NEON samples based on collection, taxon, location, and more. To do so we will:</p>

        <article>
          <p>We navigate to <a href="https://biorepo.neonscience.org/portal/collections/map/index.php">Map Search</a> under "Search" in the main menu. This opens a new Google Maps tab.</p>
          <figure>
            <img src="../misc/images/tutorial_45.jpg" alt="Map Search option in menu">
            <figcaption>Map Search option in menu</figcaption>
          </figure>
        </article>

        <article>
          <p>We can click "Open Search Panel" in the upper left hand corner to expand a panel to input search terms.</p>
          <figure>
            <img src="../misc/images/tutorial_23.jpg" alt="Map search panel button">
            <figcaption>Map search panel button</figcaption>
          </figure>
        </article>

        <article>
          <p>In the now visible search panel, entering search criteria is done in the same way as in the "Sample Search" tutorial. We will search for <span style="font-style: italic;">Peromyscus</span>.</p>
          <figure>
            <img src="../misc/images/tutorial_37.jpg" alt="Map search panel">
            <figcaption>Map search panel</figcaption>
          </figure>
        </article>

        <article>
          <p>Then, we then click the "Collections" tab to select the collections of interest.</p>
          <figure>
            <img src="../misc/images/tutorial_22.jpg" alt="Collections tab in map search panel">
            <figcaption>Collections tab in map search panel</figcaption>
          </figure>
        </article>

        <article>
          <p>We will focus only on the mammal fecal and hair samples.</p>
          <figure>
            <img src="../misc/images/tutorial_21.jpg" alt="Example mammal fecal and hair samples collections">
            <figcaption>Example mammal fecal and hair samples collections</figcaption>
          </figure>
        </article>

        <article>
          <p>We can use the map area selection tools at the center top area of the screen. We will focus on the western half of the continental US.</p>
          <figure>
            <img src="../misc/images/tutorial_3.jpg" alt="Map area selection tool">
            <figcaption>Map area selection tool</figcaption>
          </figure>
        </article>

        <article>
          <p>We click "Search" in the "Criteria" tab of the search panel to see the collection locations for the samples.</p>
          <figure>
            <img src="../misc/images/tutorial_2.jpg" alt="Search button">
            <figcaption>Search button</figcaption>
          </figure>
        </article>

        <article>
          <p>When the results appear, we will open the search panel again to see the records and taxa. To color the points by taxa, we switch to the "Taxa List" tab.</p>
          <figure>
            <img src="../misc/images/tutorial_28.jpg" alt="Taxa list tab">
            <figcaption>Taxa list tab</figcaption>
          </figure>
        </article>

        <article>
          <p>We select "Auto Color" to color the points by taxon. There we also see a list of taxa. Each name links to the Taxon Page like those described in the above section on the "Sample Search" feature.</p>
          <figure>
            <img src="../misc/images/tutorial_38.jpg" alt="Auto color button">
            <figcaption>Auto color button</figcaption>
          </figure>
        </article>

        <article>
          <p>As when the results of the "Sample Search" feature are mapped, we can zoom and select individual record. Clicking on "See Details" will bring us to the "Full Record Details" page.</p>
          <figure>
            <img src="../misc/images/tutorial_42.jpg" alt="Record details link">
            <figcaption>Record details link</figcaption>
          </figure>
        </article>

        <article>
          <p>We can return to the "Records and Taxa" tab to download the Symbiota or Darwin Core records resulting from the search (download button), download the KML file (KML download button), and copy a link to the search results to the clipboard (link button).</p>
          <figure>
            <img src="../misc/images/tutorial_27.jpg" alt="Records and taxa tab">
            <figcaption>Records and taxa tab</figcaption>
          </figure>
        </article>

		</div>
		<?php
			include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
