<?php
if($LANG_TAG == 'en' || !file_exists($SERVER_ROOT.'/content/lang/header.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/header.en.php');
else include_once($SERVER_ROOT.'/content/lang/header.'.$LANG_TAG.'.php');
include_once($SERVER_ROOT.'/classes/ProfileManager.php');
$pHandler = new ProfileManager();
$isAccessiblePreferred = $pHandler->getAccessibilityPreference($SYMB_UID);
?>
<a class="usa-skipnav" href="#main-content">Skip to main content</a>
<header class="usa-header usa-header-basic footer-header-wrapper" role="banner">
  <!-- Gov banner BEGIN -->
  <div class="usa-banner">
    <div class="usa-accordion">
      <header class="usa-banner-header">
        <div class="usa-grid usa-banner-inner">
          <img
            src="<?php echo $CLIENT_ROOT ?>/assets/uswds/img/us_flag_small.png"
            alt="U.S. flag"
          />
          <p>An official website of the United States government</p>
          <button
            class="usa-accordion__button usa-banner-button"
            aria-expanded="false"
            aria-controls="gov-banner"
            style="background-image: url('../css/uswds/img/usa-icons/angle-arrow-down-hover.svg');"
          >
            <span class="usa-banner-button-text">Here's how you know</span>
          </button>
        </div>
      </header>
      <div
        class="usa-banner-content usa-grid usa-accordion-content"
        id="gov-banner"
      >
        <div class="usa-banner-guidance-gov usa-width-one-half">
          <img
            class="usa-banner-icon usa-media_block-img"
            src="<?php echo $CLIENT_ROOT ?>/assets/uswds/img/icon-dot-gov.svg"
            alt="Dot gov"
          />
          <div class="usa-media_block-body">
            <p>
              <strong>Official websites use .gov</strong>
              <br />
              A <strong>.gov</strong> website belongs to an official government
              organization in the United States.
            </p>
          </div>
        </div>
        <div class="usa-banner-guidance-ssl usa-width-one-half">
          <img
            class="usa-banner-icon usa-media_block-img"
            src="<?php echo $CLIENT_ROOT ?>/assets/uswds/img/icon-https.svg"
            alt="SSL"
          />
          <div class="usa-media_block-body">
            <p>
              <strong>Secure .gov websites use HTTPS</strong>
              <br />
              A <strong>lock</strong> (
              <span class="icon-lock">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="52"
                  height="64"
                  viewBox="0 0 52 64"
                  class="usa-banner__lock-image"
                  role="img"
                  aria-labelledby="banner-lock-title banner-lock-description"
                >
                  <title id="banner-lock-title">Lock</title>
                  <desc id="banner-lock-description">A locked padlock</desc>
                  <path
                    fill="#000000"
                    fill-rule="evenodd"
                    d="M26 0c10.493 0 19 8.507 19 19v9h3a4 4 0 0 1 4 4v28a4 4 0 0 1-4 4H4a4 4 0 0 1-4-4V32a4 4 0 0 1 4-4h3v-9C7 8.507 15.507 0 26 0zm0 8c-5.979 0-10.843 4.77-10.996 10.712L15 19v9h22v-9c0-6.075-4.925-11-11-11z"
                  >
                  </path>
                </svg>
              </span>

              ) or <strong>https://</strong> means you’ve safely connected to
              the .gov website. Share sensitive information only on official,
              secure websites.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Gov banner END -->
  <!-- Logo  and Log-Text Begin -->
  <div class="usa-nav-container" style="max-width:144rem; margin-left:0; margin-right: 0">
    <div class="usa-navbar" style="margin-left:20rem;">
      <button class="usa-menu-btn">Menu</button>
      <div class="usa-logo" id="logo">
        <span
          href="https://www.usda.gov"
          title="Home"
          rel="home"
          class="usda-logo"
        >
          <img
            src="<?php echo $CLIENT_ROOT ?>/assets/uswds/img/usda-symbol.svg"
            alt="USDA Logo"
            class="usda-logo-img"
          />
        </span>

        <em class="usa-logo-text">
          <a
            href="https://www.ars.usda.gov/"
            title="Agricultural Research Service Home"
            aria-label="The Agricultural Research Service Home Page"
            rel="home"
            id="anch_1"
            >Agricultural Research Service
          </a>

          <h6>
            <a
              href="https://www.usda.gov/"
              title="U.S. DEPARTMENT OF AGRICULTURE Home"
              aria-label="The U.S. Department of Agriculture Home Page"
              rel="home"
              id="anch_1"
              >U.S. DEPARTMENT OF AGRICULTURE
            </a>
          </h6>
        </em>
      </div>
    </div>

    <!-- Logo  and Log-Text End -->
    <nav role="navigation" class="usa-nav usa-color-primary-darkest">
      <button class="usa-menu-btn usa-nav-close">
        <img src="<?php echo $CLIENT_ROOT ?>/assets/uswds/img/close.svg" alt="close" />
      </button>
      
      <ul class="usa-nav-primary usa-accordion">
        <li>
          <button
            id="btnMenu-0"
            onclick="navigateHome();"
          >
            <span>
              Home
            </span>
          </button>
        </li>
        <li>
          <button
            class="usa-accordion__button usa-nav-link"
            aria-expanded="false"
            aria-controls="side-nav-1"
            id="btnMenu-1"
          >
            <span id="menuName">Search Collections</span>
          </button>
          <ul
            id="side-nav-1"
            class="usa-nav-submenu usa-color-white"
            aria-hidden="true"
          >
            <li>
              <a href="<?php echo $CLIENT_ROOT?>/collections/harvestparams.php">Search All Collections</a>
            </li>
            <li>
              <a href="<?php echo $CLIENT_ROOT?>/collections/harvestparams.php?db=<?php echo  $NA_COLLID?>">Search National Arboretum Herbarium</a>
            </li>
            <li>
              <a href="<?php echo $CLIENT_ROOT?>/collections/harvestparams.php?db=<?php echo  $BARC_COLLID?>">Search National Seed Herbarium</a>
            </li>
            <li>
              <a href="<?php echo $CLIENT_ROOT?>/collections/harvestparams.php?db=<?php echo  $BPI_SNAPSHOT_COLLID?>">Search National Fungus Collections</a>
            </li>
          </ul>
        </li>
        <li>
          <button
            class="usa-accordion__button usa-nav-link"
            aria-expanded="false"
            aria-controls="side-nav-2"
            id="btnMenu-2"
          >
            <span id="menuName">Map Search</span>
          </button>
          <ul
            id="side-nav-2"
            class="usa-nav-submenu usa-color-white"
            aria-hidden="true"
          >
            <li>
              <a href="<?php echo $CLIENT_ROOT ?>/collections/map/index.php">Map Search All Collections</a>
            </li>
            <li>
              <a href="<?php echo $CLIENT_ROOT?>/collections/map/index.php?db=<?php echo  $NA_COLLID?>">Map Search National Arboretum Herbarium</a>
            </li>
            <li>
              <a href="<?php echo $CLIENT_ROOT?>/collections/map/index.php?db=<?php echo  $BARC_COLLID?>">Map Search National Seed Herbarium</a>
            </li>
            <li>
              <a href="<?php echo $CLIENT_ROOT?>/collections/map/index.php?db=<?php echo  $BPI_SNAPSHOT_COLLID?>">Map Search National Fungus Collections</a>
            </li>
          </ul>
        </li>
        <li>
          <button
            class="usa-accordion__button usa-nav-link"
            aria-expanded="false"
            aria-controls="side-nav-3"
            id="btnMenu-3"
          >
            <span id="menuName">About Collections</span>
          </button>
          <ul
            id="side-nav-3"
            class="usa-nav-submenu usa-color-white"
            aria-hidden="true"
          >
            <li>
              <a href="<?php echo $CLIENT_ROOT?>/collections/misc/collprofiles.php?collid=<?php echo  $NA_COLLID?>">About National Arboretum Herbarium</a>
            </li>
            <li>
              <a href="<?php echo $CLIENT_ROOT?>/collections/misc/collprofiles.php?collid=<?php echo  $BARC_COLLID?>">About National Seed Herbarium</a>
            </li>
            <li>
              <a href="<?php echo $CLIENT_ROOT?>/collections/misc/collprofiles.php?collid=<?php echo  $BPI_SNAPSHOT_COLLID?>">About National Fungus Collections</a>
            </li>
          </ul>
        </li>
        <li>
          <button
            id="btnMenu-0"
            onclick="navigateToDataUse();"
          >
            <span>
              Data Use
            </span>
          </button>
        </li>
        <li>
          <button
            id="btnMenu-0"
            onclick="navigateToHelp();"
          >
            <span>
              Help
            </span>
          </button>
        </li>
        <li>
          <button
            id="btnMenu-0"
            onclick="navigateToSiteMap();"
          >
            <span>
              Site Map
            </span>
          </button>
        </li>
      </ul>
      <!-- Secondary Naivagation Start -->
      <div class="usa-nav-secondary" style="margin-right:16rem;">
        <ul class="usa-unstyled-list usa-nav-secondary-links">
          <?php
            if($USER_DISPLAY_NAME){
          ?>
          <li style="font-size: 1.5rem; color: rgb(91, 97, 107)">
            <b>
              <?php echo (isset($LANG['H_WELCOME'])?$LANG['H_WELCOME']:'Welcome').' '.$USER_DISPLAY_NAME; ?>
            </b>
          </li>
          <?php
            }
          ?>
          <li>
            <a title="ARS Home" href="https://www.ars.usda.gov/"><b>ARS Home</b></a>
          </li>
          <li>
            <a class="accessibility-button" onclick="toggleAccessibilityStyles('<?php echo $CLIENT_ROOT . '/includes' . '/' ?>', '<?php echo $CSS_BASE_PATH ?>', '<?php echo $LANG['TOGGLE_508_OFF'] ?>', '<?php echo $LANG['TOGGLE_508_ON'] ?>')" id="accessibility-button" data-accessibility="accessibility-button" >
              <b>
                <?php echo (isset($LANG['TOGGLE_508_ON'])?$LANG['TOGGLE_508_ON']:'Accessibility Mode'); ?>
              </b>
            </a>
          </li>
          <li>
            <a title="About ARS" href="https://www.ars.usda.gov/about-ars/"><b>About ARS</b></a>
          </li>
          <?php
            if($USER_DISPLAY_NAME){
          ?>
          <li>
            <a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/profile/index.php?submit=logout">
              <b>
                <?php echo htmlspecialchars((isset($LANG['H_LOGOUT'])?$LANG['H_LOGOUT']:'Sign Out'), HTML_SPECIAL_CHARS_FLAGS)?>
              </b>
            </a>
          </li>
          <?php
            } else{
          ?>
          <li>
            <a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS) . "/profile/index.php?refurl=" . htmlspecialchars($_SERVER['SCRIPT_NAME'], HTML_SPECIAL_CHARS_FLAGS) . "?" . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES); ?>">
              <b>
                <?php echo (isset($LANG['H_LOGIN'])?$LANG['H_LOGIN']:'Login')?>
              </b>
            </a>
          </li>
          <?php
            }
          ?>
        </ul>
      </div>
      <!-- Secondary Naivagation End -->
    </nav>
  </div>
</header>
<script type="text/javascript">
  const navigateHome = () => {
    window.location.href = '<?php echo $CLIENT_ROOT; ?>';
  };
  const navigateToDataUse = () => {
    window.location.href = '<?php echo $CLIENT_ROOT; ?>/includes/usagepolicy.php';
  };
  const navigateToHelp = () => {
    window.location.href = 'https://symbiota.org/docs';
  };
  const navigateToSiteMap = () => {
    window.location.href = '<?php echo $CLIENT_ROOT; ?>/sitemap.php';
  };
</script>
