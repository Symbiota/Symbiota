<SCRIPT>
<!--
if (top.frames.length!=0)
  top.location=self.document.location;
// -->
</SCRIPT>
<table id="maintable" cellspacing="0">
	<tr>
		<td id="header" colspan="3">
			<div style="background-color:#504e4f;">
				<div style="float:left;">
					<img style="" src="<?php echo $CLIENT_ROOT; ?>/images/layout/LeftCorner2.jpg" />
				</div>
				<div style="float:left;font:bold Simplifica, Georgia, serif">
					<div style="margin:5px 0px 0px 20px;font-size:50px;">My Waterbears</div>
					<div style="margin:2px 0px 0px 20px;font-size:20px;">Tardigrade Reference Center</div>
				</div>
			</div>
			<div id="top_navbar">
				<div id="right_navbarlinks">
					<?php
					if($USER_DISPLAY_NAME){
					?>
						<span style="">
							Welcome <?php echo $USER_DISPLAY_NAME; ?>!
						</span>
						<span style="margin-left:5px;">
							<a href="<?php echo $CLIENT_ROOT; ?>/profile/viewprofile.php">My Profile</a>
						</span>
						<span style="margin-left:5px;">
							<a href="<?php echo $CLIENT_ROOT; ?>/profile/index.php?submit=logout">Logout</a>
						</span>
					<?php
					}
					else{
					?>
						<span style="">
							<a href="<?php echo $CLIENT_ROOT."/profile/index.php?refurl=".$_SERVER['SCRIPT_NAME'].'?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES); ?>">
								Log In
							</a>
						</span>
						<span style="margin-left:5px;">
							<a href="<?php echo $CLIENT_ROOT; ?>/profile/newprofile.php">
								New Account
							</a>
						</span>
					<?php
					}
					?>
					<span style="margin-left:5px;margin-right:5px;">
						<a href='<?php echo $CLIENT_ROOT; ?>/sitemap.php'>Sitemap</a>
					</span>
					
				</div>
				<ul id="hor_dropdown">
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/index.php" >Home</a>
					</li>
					<li>
						<a href="#" >Search</a>
						<ul>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/collections/index.php" >Search Collections</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/collections/map/mapinterface.php" target="_blank">Map Search</a>
							</li>
						</ul>
					</li>
					<li>
						<a href="#" >Images</a>
						<ul>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/imagelib/index.php" >Image Browser</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/imagelib/search.php" >Search Images</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/imagelib/contributors.php">Contributors</a>
							</li>
						</ul>
					</li>
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?pid=1" >Species Lists</a>
					</li>
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/taxa/taxonomy/taxonomydynamicdisplay.php?target=tardigrada&displayauthor=1">Taxonomy</a>
					</li>
					<li>
						<a href="#">Project Details</a>
						<ul>
							<li>
								<a href="https://researchoutreach.org/articles/capturing-images-and-data-before-the-slides-degrade-into-uselessness/" target="_blank">Specimen Digitization</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/misc/medialinks.php">Additional Media</a>
							</li>
						</ul>
					</li>
					<li>
						<a href="#" >Links</a>
						<ul>
							<li>
								<a href="http://www.tardigrada.net/register/index.htm" target="_blank">Tardigrada Register</a>
							</li>
							<li>
								<a href="https://en.wikipedia.org/wiki/Tardigrade" target="_blank">Wikipedia</a>
							</li>
							<li>
								<a href="http://www.tardigrada.net/newsletter/index.htm" target="_blank">Tardigrada Newsletter</a>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</td>
	</tr>
    <tr>
		<td id='middlecenter'  colspan="3">
