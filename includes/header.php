<?php
if($LANG_TAG == 'en' || !file_exists($SERVER_ROOT.'/content/lang/templates/header.' . $LANG_TAG . '.php'))
	include_once($SERVER_ROOT . '/content/lang/templates/header.en.php');
else include_once($SERVER_ROOT . '/content/lang/templates/header.' . $LANG_TAG . '.php');
$SHOULD_USE_HARVESTPARAMS = $SHOULD_USE_HARVESTPARAMS ?? false;
$collectionSearchPage = $SHOULD_USE_HARVESTPARAMS ? '/collections/index.php' : '/collections/search/index.php';
?>
<div class="header-wrapper">
	<header>
		<div class="top-wrapper">
			<a class="screen-reader-only" href="#end-nav"><?= $LANG['H_SKIP_NAV'] ?></a>
			<nav class="top-login" aria-label="horizontal-nav">
				<?php
				if ($USER_DISPLAY_NAME) {
					?>
					<div class="welcome-text bottom-breathing-room-rel">
					<?= $LANG['H_WELCOME'] . ' ' . $USER_DISPLAY_NAME ?>!
					</div>
					<span id="profile">
						<form name="profileForm" method="post" action="<?= $CLIENT_ROOT . '/profile/viewprofile.php' ?>">
							<button class="button button-tertiary bottom-breathing-room-rel left-breathing-room-rel" name="profileButton" type="submit"><?= $LANG['H_MY_PROFILE'] ?></button>
						</form>
					</span>
					<span id="logout">
						<form name="logoutForm" method="post" action="<?= $CLIENT_ROOT ?>/profile/index.php?submit=logout">
							<button class="button button-secondary bottom-breathing-room-rel left-breathing-room-rel" name="logoutButton" type="submit"><?= $LANG['H_LOGOUT'] ?></button>
						</form>
					</span>
					<?php
				} else {
					?>
					<span id="login">
						<form name="loginForm" method="post" action="<?= $CLIENT_ROOT . "/profile/index.php" ?>">
							<input name="refurl" type="hidden" value="<?= htmlspecialchars($_SERVER['SCRIPT_NAME'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . "?" . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES) ?>">
							<button class="button button-secondary bottom-breathing-room-rel left-breathing-room-rel" name="loginButton" type="submit"><?= $LANG['H_LOGIN'] ?></button>
						</form>
					</span>
					<?php
				}
				?>
			</nav>
			<div class="top-brand">
				<a href="https://cch2.org">
					<div class="image-container">
						<img src="<?= $CLIENT_ROOT ?>/images/layout/CCH.png" alt="CCH2 logo">
					</div>
				</a>
				<div class="brand-name">
					<h1>CCH2</h1>
					<h2>Specimen data from the Consortium of California Herbaria</h2>
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
						<a href="<?= $CLIENT_ROOT ?>/index.php">
							<?= $LANG['H_HOME'] ?>
						</a>
					</li>	
					<li>
						<a href="<?= $CLIENT_ROOT ?>/collections/misc/collprofiles.php">
							Collections
						</a>
					</li>				
					<li>
						<a href="<?= $CLIENT_ROOT . $collectionSearchPage ?>">
							<?= $LANG['H_SEARCH'] ?>
						</a>
					</li>
					<li>
						<a href="<?= $CLIENT_ROOT ?>/collections/map/index.php" rel="noopener noreferrer">
							<?= $LANG['H_MAP_SEARCH'] ?>
						</a>
					</li>
					<li>
						<a href="<?= $CLIENT_ROOT ?>/checklists/index.php">
							Checklists
						</a>
					</li>
					<li>
						<a href="<?= $CLIENT_ROOT ?>/imagelib/search.php">
							<?= $LANG['H_IMAGES'] ?>
						</a>
					</li>
					<li>
						<a href="<?= $CLIENT_ROOT ?>/includes/usagepolicy.php">
							<?= $LANG['H_DATA_USAGE'] ?>
						</a>
					</li>
					<li>
						<a href="<?= $CLIENT_ROOT ?>/includes/aboutpage.php">About CCH</a>
					</li>
					<li>
						<a href="https://www.capturingcaliforniasflowers.org/symbiota.html" target="_blank">Help & Resources</a>
					</li>
					<li>
						<a href='<?= $CLIENT_ROOT ?>/sitemap.php'>
							<?= $LANG['H_SITEMAP'] ?>
						</a>
					</li>
				</ul>
			</nav>
		</div>
		<div id="end-nav"></div>
	</header>
</div>
