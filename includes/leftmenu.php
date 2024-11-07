<div class="menu">
	<div class="menuheader">
		<a href="<?php echo $CLIENT_ROOT; ?>/index.php">
			Herbario Virtual<br/>Austral Americano
		</a>
	</div>
	<div class="menuitem">
		<a href="<?php echo $CLIENT_ROOT; ?>/collections/index.php">
			Search Collections
		</a>
	</div>
	<div class="menuitem">
		<a href="<?php echo $CLIENT_ROOT; ?>/imagelib/index.php">
			Image Library
		</a>
	</div>
	<div class='menuitem'>
    		<a href='<?php echo $CLIENT_ROOT; ?>/games/index.php'>
			Games
		</a>
	</div>
	<div class="menuitem">
		<a href="<?php echo $CLIENT_ROOT; ?>/misc/links.php">
			Useful Links
		</a>
	</div>
	<div class="menusubheader">
		<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php">
			Floristic Projects
		</a>
	</div>
	<div class="menuitem">
		<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?proj=2">
			Flora de Chile
		</a>
	</div>
	<div class="menusubheader">
		Dynamic Floras
	</div>
	<div class="menuitem">
		<a href="<?php echo $CLIENT_ROOT; ?>/checklists/dynamicmap.php?interface=checklist">
			Dynamic Checklist
		</a>
	</div>
	<div style="margin:20px 0px 20px 0px;">
		<hr/>
	</div>
	<?php
	if($USER_DISPLAY_NAME){
		?>
		<div class='menuitem'>
			Welcome <?php echo $USER_DISPLAY_NAME; ?>!
		</div>
		<div class="menuitem">
			<a href="<?php echo $CLIENT_ROOT; ?>/profile/viewprofile.php">My Profile</a>
		</div>
		<div class="menuitem">
			<a href="<?php echo $CLIENT_ROOT; ?>/profile/index.php?submit=logout">Logout</a>
		</div>
		<?php
	}
	else{
		?>
		<div class="menuitem">
			<a href="<?php echo $CLIENT_ROOT."/profile/index.php?refurl=".$_SERVER['SCRIPT_NAME']."?".htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES); ?>">
				Log In
			</a>
		</div>
		<div class="menuitem">
			<a href="<?php echo $CLIENT_ROOT; ?>/profile/newprofile.php">
				New Account
			</a>
		</div>
		<?php
	}
	?>
	<hr/>
	<div class='menuitem'>
		<a href='<?php echo $CLIENT_ROOT; ?>/sitemap.php'>Sitemap</a>
	</div>
</div>
