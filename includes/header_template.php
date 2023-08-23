<?php
if($LANG_TAG == 'en' || !file_exists($SERVER_ROOT.'/content/lang/header.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/header.en.php');
else include_once($SERVER_ROOT.'/content/lang/header.'.$LANG_TAG.'.php');

include_once($SERVER_ROOT.'/classes/ProfileManager.php');
$pHandler = new ProfileManager();
$isAccessiblePreferred = $pHandler->getAccessibilityPreference($SYMB_UID);
?>
<div class="header-wrapper">
	<header>
		<div class="top-wrapper">
			<a class="skip-link" href="#end-nav"><?php echo $LANG['SKIP_NAV'] ?></a>
			<nav class="top-login" aria-label="horizontal-nav">
				<?php
				if ($USER_DISPLAY_NAME) {
					?>
					<span>
						<?php echo (isset($LANG['H_WELCOME'])?$LANG['H_WELCOME']:'Welcome').' '.$USER_DISPLAY_NAME; ?>!
					</span>
					<span class="button button-tertiary">
						<a class="accessibility-button" onclick="toggleAccessibilityStyles('<?php echo $CLIENT_ROOT . '/includes' . '/' ?>', '<?php echo $CSS_BASE_PATH ?>', '<?php echo $LANG['TOGGLE_508_OFF'] ?>', '<?php echo $LANG['TOGGLE_508_ON'] ?>')" id="accessibility-button" data-accessibility="accessibility-button" ><?php echo (isset($LANG['TOGGLE_508_ON'])?$LANG['TOGGLE_508_ON']:'Accessibility Mode'); ?></a>
					</span>
					<span class="button button-tertiary">
						<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/profile/viewprofile.php"><?php echo htmlspecialchars((isset($LANG['H_MY_PROFILE'])?$LANG['H_MY_PROFILE']:'My Profile'), HTML_SPECIAL_CHARS_FLAGS)?></a>
					</span>
					<span class="button button-secondary">
						<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/profile/index.php?submit=logout"><?php echo htmlspecialchars((isset($LANG['H_LOGOUT'])?$LANG['H_LOGOUT']:'Sign Out'), HTML_SPECIAL_CHARS_FLAGS)?></a>
					</span>
					<?php
				} else {
					?>
					<span class="button button-tertiary">
						<a class="accessibility-button" onclick="toggleAccessibilityStyles('<?php echo $CLIENT_ROOT . '/includes' . '/' ?>', '<?php echo $CSS_BASE_PATH ?>', '<?php echo $LANG['TOGGLE_508_OFF'] ?>', '<?php echo $LANG['TOGGLE_508_ON'] ?>')" id="accessibility-button" data-accessibility="accessibility-button" ><?php echo (isset($LANG['TOGGLE_508_ON'])?$LANG['TOGGLE_508_ON']:'Accessibility Mode'); ?></a>
					</span>
					<span class="button button-tertiary">
						<a onclick="window.location.href='#'">
							<?php echo htmlspecialchars($LANG['CONTACT_US'], HTML_SPECIAL_CHARS_FLAGS); ?>
						</a>
					</span>
					<span class="button button-secondary">
						<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS) . "/profile/index.php?refurl=" . htmlspecialchars($_SERVER['SCRIPT_NAME'], HTML_SPECIAL_CHARS_FLAGS) . "?" . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES); ?>">
							<?php echo (isset($LANG['H_LOGIN'])?$LANG['H_LOGIN']:'Login')?>
						</a>
					</span>
					<?php
				}
				?>
			</nav>
			<div class="top-brand">
				<a href="https://symbiota.org">
					<div class="image-container">
						<img src="<?php echo $CLIENT_ROOT; ?>/images/layout/logo_symbiota.png" alt="Symbiota logo">
					</div>
				</a>
				<div class="brand-name">
					<h1>Symbiota Brand New Portal</h1>
					<h2>Redesigned by the Symbiota Support Hub</h2>
				</div>
			</div>
		</div>
		<div class="menu-wrapper">
			<!-- Hamburger icon -->
			<input class="side-menu" type="checkbox" id="side-menu" name="side-menu" />
			<label class="hamb hamb-line hamb-label" for="side-menu" tabindex="0">☰</label>
			<!-- Menu -->
			<nav class="top-menu" aria-label="hamburger-nav">
				<ul class="menu">
					<li>
						<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/index.php">
							<?php echo (isset($LANG['H_HOME'])?$LANG['H_HOME']:'Home'); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/collections/index.php">
							<?php echo (isset($LANG['H_COLLECTIONS'])?$LANG['H_COLLECTIONS']:'Collections'); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/collections/map/index.php" target="_blank" rel="noopener noreferrer">
							<?php echo (isset($LANG['H_MAP_SEARCH'])?$LANG['H_MAP_SEARCH']:'Map Search'); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/checklists/index.php">
							<?php echo (isset($LANG['H_INVENTORIES'])?$LANG['H_INVENTORIES']:'Checklists'); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/imagelib/search.php">
							<?php echo (isset($LANG['H_IMAGES'])?$LANG['H_IMAGES']:'Images'); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/includes/usagepolicy.php">
							<?php echo (isset($LANG['H_DATA_USAGE'])?$LANG['H_DATA_USAGE']:'Data Use'); ?>
						</a>
					</li>
					<li>
						<a href="https://symbiota.org/docs" target="_blank" rel="noopener noreferrer">
							<?php echo (isset($LANG['H_HELP'])?$LANG['H_HELP']:'Help'); ?>
						</a>
					</li>
					<li>
						<a href='<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/sitemap.php'>
							<?php echo (isset($LANG['H_SITEMAP'])?$LANG['H_SITEMAP']:'Sitemap'); ?>
						</a>
					</li>
					<li>
						<label for="language-selection"><?php echo $LANG['SELECT_LANGUAGE'] ?>: </label>
						<select oninput="setLanguage(this)" id="language-selection" name="language-selection">
							<option value="en">English</option>
							<option value="es" <?php echo ($LANG_TAG=='es'?'SELECTED':''); ?>>Espa&ntilde;ol</option>
							<option value="fr" <?php echo ($LANG_TAG=='fr'?'SELECTED':''); ?>>Français</option>
						</select>
					</li>
				</ul>
			</nav>
		</div>
		<div id="end-nav"></div>
	</header>
</div>