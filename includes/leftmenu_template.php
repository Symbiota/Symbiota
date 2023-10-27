<div class="menu">
	<div class="menuheader">
		<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/index.php">
			<?php echo $DEFAULT_TITLE; ?> Homepage
		</a>
	</div>
	<div class="menuitem">
		<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/collections/index.php">
			Search Collections
		</a>
	</div>
	<div class="menuitem">
		<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/collections/map/index.php" target="_blank">
			Map Search
		</a>
	</div>
	<div class="menuitem">
		<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/projects/index.php">
			Flora Projects
		</a>
	</div>
	<div class="menuitem">
		<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/projects/index.php?pid=1">
			Blabla Flora
		</a>
	</div>
	<div class="menuitem">
    	<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/agents/index.php">
    		Agents
    	</a>
    </div>
	<div class="menuitem">
		<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/checklists/dynamicmap.php?interface=checklist">
			Dynamic Checklist
		</a>
	</div>
	<div class="menuitem">
		<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/checklists/dynamicmap.php?interface=key">
			Dynamic Key
		</a>
	</div>
	<div class="menuitem">
		<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/imagelib/index.php">
			Image Library
		</a>
	</div>
	<div class="menuitem">
		<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/imagelib/search.php">
			Search Images
		</a>
	</div>
	<div>
		<hr/>
	</div>
	<?php
	if($USER_DISPLAY_NAME){
		?>
		<div class='menuitem'>
			Welcome <?php echo $USER_DISPLAY_NAME; ?>!
		</div>
		<div class="menuitem">
			<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/profile/viewprofile.php">My Profile</a>
		</div>
		<div class="menuitem">
			<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/profile/index.php?submit=logout">Logout</a>
		</div>
		<?php
	}
	else{
		?>
		<div class="menuitem">
			<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS) . "/profile/index.php?refurl=" . htmlspecialchars($_SERVER['SCRIPT_NAME'], HTML_SPECIAL_CHARS_FLAGS) . "?" . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES); ?>">
				Log In
			</a>
		</div>
		<div class="menuitem">
			<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/profile/newprofile.php">
				New Account
			</a>
		</div>
		<?php
	}
	?>
	<div class='menuitem'>
		<a href='<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/sitemap.php'>Sitemap</a>
	</div>
</div>
