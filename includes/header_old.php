<table id="maintable" cellspacing="0">
	<tr>
		<td class="header" colspan="3">
			<div style="clear:both;">
				<div style="clear:both;">
					<img style="" src="<?php echo $CLIENT_ROOT; ?>/images/layout/pacherbheader.jpg" />
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
						<a href="<?php echo $CLIENT_ROOT; ?>/collections/index.php" >Search Collections</a>
					</li>
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/collections/map/mapinterface.php" target="_blank">Map Search</a>
					</li>
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/imagelib/imgsearch.php" >Image Search</a>
					</li>
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/imagelib/index.php" >Browse Images</a>
					</li>
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?" >Inventories</a>
						<ul>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?pid=8" >Flora of Palau</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?pid=1" >Flora of the Hawaiian Islands</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?pid=4" >Hawaii Seed Bank</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?pid=5" >Plant Extinction Prevention Program Species</a>
							</li>
				                            <li>
                                				<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?pid=12" >Traditional Plants Uses in Micronesia</a>
                            				</li>
						</ul>
					</li>
					<li>
						<a href="#" >Interactive Tools</a>
						<ul>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/checklists/dynamicmap.php?interface=checklist" >Dynamic Checklist</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/checklists/dynamicmap.php?interface=key" >Dynamic Key</a>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</td>
	</tr>
    <tr>
		<td class='middlecenter'  colspan="3">

