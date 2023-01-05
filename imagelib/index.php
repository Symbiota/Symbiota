<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ImageLibraryBrowser.php');
header("Content-Type: text/html; charset=".$CHARSET);

$taxon = array_key_exists('taxon',$_REQUEST)?htmlspecialchars(strip_tags($_REQUEST['taxon'])):'';
$target = array_key_exists('target',$_REQUEST)?trim($_REQUEST['target']):'';

$imgManager = new ImageLibraryBrowser();
$imgManager->setSearchTerm($taxon);
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Image Library</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<script src="../js/symb/imagelib.search.js?ver=201902" type="text/javascript"></script>
</head>
<body>
	<?php
	$displayLeftMenu = (isset($imagelib_indexMenu)?$imagelib_indexMenu:false);
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="<?php echo $CLIENT_ROOT; ?>/index.php">Home</a> &gt;&gt;
		<b>Image Library</b>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<h1>Species with Images</h1>
		<div style="margin:0px 0px 5px 20px;">This page provides a complete list to taxa that have images.
		Use the controls below to browse and search for images by family, genus, or species.
		</div>
		<div style="float:left;margin:10px 0px 10px 30px;">
			<div style=''>
				<a href='index.php?target=family'>Browse by Family</a>
			</div>
			<div style='margin-top:10px;'>
				<a href='index.php?target=genus'>Browse by Genus</a>
			</div>
			<div style='margin-top:10px;'>
				Browse by Species
			</div>
			<div style='margin:2px 0px 0px 10px;'>
				<div><a href='index.php?taxon=A'>A</a>|<a href='index.php?taxon=B'>B</a>|<a href='index.php?taxon=C'>C</a>|<a href='index.php?taxon=D'>D</a>|<a href='index.php?taxon=E'>E</a>|<a href='index.php?taxon=F'>F</a>|<a href='index.php?taxon=G'>G</a>|<a href='index.php?taxon=H'>H</a></div>
				<div><a href='index.php?taxon=I'>I</a>|<a href='index.php?taxon=J'>J</a>|<a href='index.php?taxon=K'>K</a>|<a href='index.php?taxon=L'>L</a>|<a href='index.php?taxon=M'>M</a>|<a href='index.php?taxon=N'>N</a>|<a href='index.php?taxon=O'>O</a>|<a href='index.php?taxon=P'>P</a>|<a href='index.php?taxon=Q'>Q</a></div>
				<div><a href='index.php?taxon=R'>R</a>|<a href='index.php?taxon=S'>S</a>|<a href='index.php?taxon=T'>T</a>|<a href='index.php?taxon=U'>U</a>|<a href='index.php?taxon=V'>V</a>|<a href='index.php?taxon=W'>W</a>|<a href='index.php?taxon=X'>X</a>|<a href='index.php?taxon=Y'>Y</a>|<a href='index.php?taxon=Z'>Z</a></div>
			</div>
		</div>
		<div style="float:right;width:250px;">
			<div style="margin:10px 0px 0px 0px;">
				<form name="searchform1" action="index.php" method="post">
					<fieldset style="background-color:#FFFFCC;padding:10px;">
						<legend style="font-weight:bold;">Scientific Name Search</legend>
						<input type="text" name="taxon" value="<?php echo $taxon; ?>" title="Enter family, genus, or scientific name" />
						<input name="submit" value="Search" type="submit">
					</fieldset>
				</form>
			</div>
			<div style="font-weight:bold;margin:15px 10px 0px 20px;">
				<div>
					<a href="../includes/usagepolicy.php#images">Image Copyright Policy</a>
				</div>
				<div>
					<a href="contributors.php">Image Contributors</a>
				</div>
				<div>
					<a href="search.php">Image Search</a>
				</div>
			</div>
		</div>
		<div style='clear:both;'><hr/></div>
		<?php
			$taxaList = Array();
			if($target == 'genus'){
				$taxaList = $imgManager->getGenusList($taxon);
				if($taxaList){
					echo '<h2>Select a Genus to see species list.</h2>';
					foreach($taxaList as $value){
						echo "<div style='margin-left:30px;'><a href='index.php?taxon=".$value."'>".$value."</a></div>";
					}
				}
				else{
					echo '<h2>No taxa returned matching search results</h2>';
				}
			}
			elseif($target == 'species' || $taxon){
				$taxaList = $imgManager->getSpeciesList($taxon);
				if($taxaList){
					echo '<h2>Select a species to access available images</h2>';
					foreach($taxaList as $key => $value){
						echo '<div style="margin-left:30px;font-style:italic;">';
						echo '<a href="#" onclick="openTaxonPopup('.$key.');return false;">'.$value.'</a> ';
						echo '<a href="search.php?taxa='.$key.'&usethes=1&taxontype=2&submitaction=search" target="_blank"> <img src="../images/image.png" style="width:10px;" /></a> ';
						echo '</div>';
					}
				}
				else{
					echo '<h2>No taxa returned matching search results</h2>';
				}
			}
			else{ //Family display
				$taxaList = $imgManager->getFamilyList();
				if($taxaList){
					echo '<h2>Select a family to see species list.</h2>';
					foreach($taxaList as $value){
						echo '<div style="margin-left:30px;"><a href="index.php?target=genus&taxon='.$value.'">'.strtoupper($value).'</a></div>';
					}
				}
				else{
					echo '<h2>No taxa returned matching search results</h2>';
				}
			}
	?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>