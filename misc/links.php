<?php
//error_reporting(E_ALL);
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$charset);
?>
<html>
<head>
    <title><?php echo $defaultTitle; ?> Links</title>
    <link rel="stylesheet" href="../css/main.css" type="text/css" />
</head>
<body>
	<?php
	$displayLeftMenu = (isset($misc_linksMenu)?$misc_linksMenu:"true");
	include($serverRoot.'/header.php');
	if(isset($misc_linksCrumbs)){
		echo "<div class='navpath'>";
		echo "<a href='../index.php'>Home</a> &gt; ";
		echo $misc_linksCrumbs;
		echo " <b>Links</b>"; 
		echo "</div>";
	}
	?> 
	<!-- This is inner text! --> 
	<div id="innertext">
		<h1>Links</h1>
		<div style="margin-left:10px;">
			<div>
				<div style="font-weight:bold;">Argentina</div>
				<ul>
					<li>
						<a href="http://www.floraargentina.edu.ar" target="_blank">
							Flora de la Rep&uacute;blica Argentina
						</a>
					</li>
					<li>
						<a href="http://www2.darwin.edu.ar/Proyectos/FloraArgentina/FA.asp" target="_blank">
							Instituto de Bot&aacute;nica Darwinion
						</a>
					</li>
				</ul>
			</div>
			<div>
				<div style="font-weight:bold;">Chile</div>
				<ul>
					<li><a href="http://www.mobot.org/mobot/research/chile/welcome.shtml" target="_blank">
						Flora of Chile Project - Missouri Botanical Garden
					</a></li>
					<li><a href="http://www.chilebosque.cl" target="_blank">
						Chile Bosque
					</a></li>
					<li><a href="http://www.trekkingchile.com/flora/index.html" target="_blank">
						Flora Chilena
					</a></li>
					<li><a href="http://www.chileflora.com/Shome.htm" target="_blank">
						Chile Flora
					</a></li>
					<li><a href="http://www.florachilena.cl" target="_blank">
						Enciclopedia de la Flora Chilena - Darian Stark
					</a></li>
					<li><a href="http://www.chlorischile.cl" target="_blank">
						Chloris Chilensis Index
					</a></li>
				</ul>
			</div>
			<div>
				<div style="font-weight:bold;">Recursos Generales</div>
				<ul>
					<li>
						<a href="http://swbiodiversity.org/seinet/index.php" target="_blank">
							Southwest Environmental Information network (SEINet)
						</a>
					</li>
					<li>
						<a href="http://symbiota.org/cotram/index.php" target="_blank">
							Cooperative Taxonomic Resource for American Myrtaceae (CoTRAM)
						</a>
					</li>
					<li>
						<a href='http://symbiota.org' target='_blank'>
							Symbiota Virtual Flora Software Project
						</a>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<?php
	include($serverRoot.'/footer.php');
	?> 

</body>
</html>

