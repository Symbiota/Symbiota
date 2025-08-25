<?php
if($LANG_TAG == 'en' || !file_exists($SERVER_ROOT.'/content/lang/header.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/header.en.php');
else include_once($SERVER_ROOT.'/content/lang/header.'.$LANG_TAG.'.php');
include_once($SERVER_ROOT.'/classes/ProfileManager.php');
$pHandler = new ProfileManager();
$isAccessiblePreferred = $pHandler->getAccessibilityPreference($SYMB_UID);
?>
<div class="header-wrapper">
  <header class="usa-header usa-header--basic usa-header--megamenu">
    <div class="top-wrapper usa-nav-container">
      <nav class="top-login usda-nav">
        <span>
          <a href="#"> <?php echo htmlspecialchars($LANG['CONTACT_US'], HTML_SPECIAL_CHARS_FLAGS) ?> </a>
        </span>
        <span class="button button-secondary">
          <a
            href="/usda/portal/profile/index.php?refurl=/usda/portal/index.php?"
          >
            <?php echo htmlspecialchars($LANG['LOGIN'], HTML_SPECIAL_CHARS_FLAGS) ?>
          </a>
        </span>
      </nav>
      <div class="top-brand">
        <a href="https://symbiota.org">
          <img
            src="/usda/portal/images/layout/usda-symbol.svg"
            alt="USDA logo"
          />
        </a>
        <div class="brand-name">
          <h1>USDA Collections Portal</h1>
          <h2>Construction in Progress</h2>
        </div>
      </div>
    </div>
    <div class="menu-wrapper">
      <!-- Hamburger icon -->
      <input class="side-menu" type="checkbox" id="side-menu" />
      <label class="hamb" for="side-menu"
        ><span class="hamb-line"></span
      ></label>
      <!-- Menu -->
      <nav class="top-menu usda-nav">
        <ul class="menu">
          <li><a href="/usda/portal/index.php"><?php echo htmlspecialchars($LANG['HOME'], HTML_SPECIAL_CHARS_FLAGS) ?></a></li>
          <li>
            <a href="/usda/portal/collections/index.php"><?php echo htmlspecialchars($LANG['SEARCH_COLLECTIONS'], HTML_SPECIAL_CHARS_FLAGS) ?></a>
          </li>
          <li>
            <a
              href="/usda/portal/collections/map/index.php"
              target="_blank"
              rel="noopener noreferrer"
              ><?php echo htmlspecialchars($LANG['H_MAP_SEARCH'], HTML_SPECIAL_CHARS_FLAGS) ?></a
            >
          </li>
          <li><a href="/usda/portal/checklists/index.php"><?php echo htmlspecialchars($LANG['CHECKLISTS'], HTML_SPECIAL_CHARS_FLAGS) ?></a></li>
          <li><a href="/usda/portal/imagelib/search.php"><?php echo htmlspecialchars($LANG['H_IMAGE_SEARCH'], HTML_SPECIAL_CHARS_FLAGS) ?></a></li>
          <li><a href="/usda/portal/includes/usagepolicy.php"><?php echo htmlspecialchars($LANG['DATA_USE'], HTML_SPECIAL_CHARS_FLAGS) ?></a></li>
          <li>
            <a
              href="https://symbiota.org/"
              target="_blank"
              rel="noopener noreferrer"
              ><?php echo htmlspecialchars($LANG['ABOUT_SYMBIOTA'], HTML_SPECIAL_CHARS_FLAGS) ?></a
            >
          </li>
          <li>
            <a
              href="https://symbiota.org/docs"
              target="_blank"
              rel="noopener noreferrer"
              ><?php echo htmlspecialchars($LANG['HELP'], HTML_SPECIAL_CHARS_FLAGS) ?></a
            >
          </li>
          <li><a href="/usda/portal/sitemap.php"><?php echo htmlspecialchars($LANG['H_SITEMAP'], HTML_SPECIAL_CHARS_FLAGS) ?></a></li>
        </ul>
      </nav>
    </div>
  </header>
</div>
<div class="navpath">
</div>