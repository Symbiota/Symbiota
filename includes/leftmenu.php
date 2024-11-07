<div class="menu">
	<div class='menuheader'>
		<a href='<?php echo $CLIENT_ROOT; ?>/index.php'>
			<?php echo $DEFAULT_TITLE; ?> Homepage
		</a>
	</div>
	<div class='menuitem'>
		<a href='<?php echo $CLIENT_ROOT; ?>/collections/index.php?taxa=Myrtaceae'>
			Specimen Search
		</a>
	</div>
	<div class="menuitem">
		<a href="<?php echo $CLIENT_ROOT; ?>/collections/map/mapinterface.php?taxa=Myrtaceae">
			Map Search
		</a>
	</div>
	<div class='menuitem'>
		<a href='<?php echo $CLIENT_ROOT; ?>/projects/index.php?proj=9'>
			Species Lists
		</a>
	</div>
	<div class='menuitem'>
		<a href='<?php echo $CLIENT_ROOT; ?>/imagelib/index.php?taxon=Myrtaceae'>
			Image Library
		</a>
	</div>
	<div class="menuitem">
		<a href="<?php echo $CLIENT_ROOT; ?>/checklists/dynamicmap.php?interface=checklist&tid=37127">
			Dynamic Checklist
		</a>
	</div>
	<div class="menuitem">
		<a href="<?php echo $CLIENT_ROOT; ?>/checklists/dynamicmap.php?interface=key&tid=37127">
			Dynamic Key
		</a>
	</div>
	<div>
		<hr/>
	</div>
	<?php
	if($USER_DISPLAY_NAME){
		?>
		<div class='menuitem'>
			<a href='<?php echo $CLIENT_ROOT; ?>/profile/viewprofile.php'>My Profile</a>
		</div>
		<div class='menuitem'>
			<a href='<?php echo $CLIENT_ROOT; ?>/profile/index.php?submit=logout'>Logout</a>
		</div>
		<?php
	}
	else{
		?>
		<div class='menuitem'>
			<a href='<?php echo $CLIENT_ROOT."/profile/index.php?refurl=".$_SERVER['SCRIPT_NAME']."?".htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES); ?>'>
				Log In
			</a>
		</div>
		<div class='menuitem'>
			<a href='<?php echo $CLIENT_ROOT; ?>/profile/newprofile.php'>
				New Account
			</a>
		</div>
		<?php
	}
	?>
	<div class='menuitem'>
		<a href='<?php echo $CLIENT_ROOT; ?>/sitemap.php'>Sitemap</a>
	</div>
</div>
