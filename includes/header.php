<?php
if($LANG_TAG == 'en' || !file_exists($SERVER_ROOT.'/content/lang/header.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/header.en.php');
else include_once($SERVER_ROOT.'/content/lang/header.'.$LANG_TAG.'.php');
include_once($SERVER_ROOT.'/classes/ProfileManager.php');
$pHandler = new ProfileManager();
$isAccessiblePreferred = $pHandler->getAccessibilityPreference($SYMB_UID);

$SHOULD_USE_HARVESTPARAMS = $SHOULD_USE_HARVESTPARAMS ?? true;
$actionPage = $SHOULD_USE_HARVESTPARAMS ? "harvestparams.php" : "./search/index.php";
?>
<div class="footer-wrapper dialog-off-canvas-main-canvas" data-off-canvas-main-canvas>
  <div class="official-website-banner">
    <div class="container">    
      <div class="official-website-banner__message">
        <img src="<?php echo $CLIENT_ROOT ?>/assets/uswds/img/us_flag_small.png" alt="" aria-hidden="true">An official website of the United States government.
        <button class="official-website-banner__trigger">Here&apos;s how you know.</button>
      </div>
      <div class="official-website-banner__content">
        <div class="content-region content-region__first col-sm-6">
          <svg class="banner-svg" id="dot_gov_icon" data-name="dot gov icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 54 54">
            <defs>  
              <style>
                  .icon-dot-gov-1 {
                      fill: #007faa;
                  }

                  .icon-dot-gov-2 {
                      fill: none;
                      stroke: #046b99 !important;
                      stroke-miterlimit: 10;
                  }
              </style>
            </defs>
            <title>dot gov icon</title>
            <path class="icon-dot-gov-1"
              d="M36.5,20.91v1.36H35.15a0.71,0.71,0,0,1-.73.68H18.23a0.71,0.71,0,0,1-.73-0.68H16.14V20.91l10.18-4.07Zm0,13.57v1.36H16.14V34.48a0.71,0.71,0,0,1,.73-0.68h18.9A0.71,0.71,0,0,1,36.5,34.48ZM21.57,23.62v8.14h1.36V23.62h2.71v8.14H27V23.62h2.71v8.14h1.36V23.62h2.71v8.14h0.63a0.71,0.71,0,0,1,.73.68v0.68H17.5V32.45a0.71,0.71,0,0,1,.73-0.68h0.63V23.62h2.71Z" />
            <circle class="icon-dot-gov-2" cx="27" cy="27.12" r="26" />
          </svg>
          <p>
          <strong>Official websites use .gov</strong>
          <br>
          A <strong>.gov</strong> website belongs to an official government organization in the United States.    </p>
        </div>
        <div class="content-region content-region__second col-sm-6">
          <svg class="banner-svg" id="https_icon" data-name="https icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 54 54">
            <defs>
              <style>
                  .icon-https-1 {
                      fill: #549500;
                  }

                  .icon-https-2 {
                      fill: none;
                      stroke: #458600;
                      stroke-miterlimit: 10;
                  }
              </style>
            </defs>
            <title>https icon</title>
            <path class="icon-https-1"
              d="M34.72,34.84a1.29,1.29,0,0,1-1.29,1.29H20.57a1.29,1.29,0,0,1-1.29-1.29V27.12a1.29,1.29,0,0,1,1.29-1.29H21V23.26a6,6,0,0,1,12,0v2.57h0.43a1.29,1.29,0,0,1,1.29,1.29v7.72Zm-4.29-9V23.26a3.43,3.43,0,0,0-6.86,0v2.57h6.86Z" />
            <circle class="icon-https-2" cx="27" cy="27.12" r="26" />
          </svg>
          <p>
          <strong>Secure .gov websites use HTTPS</strong>
          <br>
          A <strong>lock</strong> ( <span class="icon-lock"><svg xmlns="http://www.w3.org/2000/svg" width="52" height="64" viewBox="0 0 52 64" class="usa-banner__lock-image" role="img" aria-hidden="true"><path fill="#000000" fill-rule="evenodd" d="M26 0c10.493 0 19 8.507 19 19v9h3a4 4 0 0 1 4 4v28a4 4 0 0 1-4 4H4a4 4 0 0 1-4-4V32a4 4 0 0 1 4-4h3v-9C7 8.507 15.507 0 26 0zm0 8c-5.979 0-10.843 4.77-10.996 10.712L15 19v9h22v-9c0-6.075-4.925-11-11-11z"/></svg></span> ) or <strong>https://</strong> means you&apos;ve safely connected to the .gov website. Share sensitive information only on official, secure websites.    </p>
        </div>
      </div>
    </div>
  </div>

  <header>
    <div class="container">
      <div class="row">                  
        <div class="col-md-6 site-branding clearfix">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 147.04 100.58" class="site-branding__usda-logo">
            <defs>
              <style>.usda-logo-1{fill:#004785;}.usda-logo-2{fill:#005941;}</style>
            </defs>
            <g id="Layer_2" data-name="Layer 2">
              <g id="Layer_1-2" data-name="Layer 1">
                <path class="usda-logo-1" d="M33.36,4.09l-.19,25.36c0,10.54-6.26,14.63-14.69,14.63C11.22,44.08,2,40.86,2,29.88V4.09A3.16,3.16,0,0,0,0,1.3H11.9A3.11,3.11,0,0,0,10,4.09v26c0,4.34,1.49,10.48,9.55,10.48,7.38,0,10-4.84,10-11L29.45,4.09A3,3,0,0,0,27.78,1.3H35A3,3,0,0,0,33.36,4.09Z"/>
                <path class="usda-logo-1" d="M50.15,44.08a26.37,26.37,0,0,1-11.9-3l-.31-10.48c1.18,4.4,4.9,10.23,11.35,10.23,6.13,0,8-4.4,8-7.44,0-6-5.39-6.76-11-9.8s-8.19-6.51-8.19-11.22C38.06,4,45.94.62,51.58.62a19.92,19.92,0,0,1,9.36,2.29l.31,9.18c-.87-3.29-4.4-8.25-10.35-8.25-4.72,0-6.58,3.29-6.58,6,0,3.78,2.67,5.2,8.5,7.87S64.6,22.44,64.6,31.06C64.6,38.81,57.78,44.08,50.15,44.08Z"/>
                <path class="usda-logo-1" d="M84.62,43.46H68.69a3.08,3.08,0,0,0,1.61-2.79V4.09A3,3,0,0,0,68.69,1.3H85.18c20.4,0,24.49,14.45,24.49,20.46C109.67,32.55,101.36,43.46,84.62,43.46ZM83.88,4.4c-1.49,0-4.78,0-5.71.13V40.24h5c14,0,17.92-9.43,17.92-18.48C101.05,14.32,96.71,4.4,83.88,4.4Z"/>
                <path class="usda-logo-1" d="M133.77,43.46c1.62-.87,1.93-1.73,1.49-3-.18-.62-1.8-4.4-3.53-8.61H116.54a89.58,89.58,0,0,0-3.47,8.55c-.56,1.49-.25,2.36,1.42,3.1h-8.55a7.38,7.38,0,0,0,2.91-3C110,38.07,126.33,0,126.33,0s16.5,38.07,17.61,40.42a5.45,5.45,0,0,0,3.1,3ZM124,13.39s-4.22,10.54-6.2,15.19H130.3C127.39,21.7,124,13.39,124,13.39Z"/>
                <path class="usda-logo-2" d="M145.18,49.42S55,47.18,1.52,75.65c0,0,56.63-22.39,143.66-20.15Z"/>
                <path class="usda-logo-2" d="M1.52,49.42s30.4.32,49.27,7.36c0,0-38.71-3.2-49.27-1.6Z"/>
                <path class="usda-logo-2" d="M1.52,59.34s21.76-1,34.88.32c0,0-31.68,3.84-34.88,5.44Z"/>
                <path class="usda-logo-2" d="M130.23,60.32h-.47C103.53,60.83,48.51,64,1.54,80.59v20l143.66,0V60.32S139.62,60.15,130.23,60.32Z"/>
              </g>
            </g>
          </svg>
          <div class="site-branding__text">
            <div class="site-branding__site-name"><a href="/" title="Home" rel="home">Agricultural Research Service</a></div>
              <div class="site-branding__usda"><a href="https://www.usda.gov">U.S. Department of Agriculture</a></div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="region region-header">
                <nav aria-labelledby="block-secondarylinks-menu" id="block-secondarylinks">
                  <h2 class="visually-hidden" id="block-secondarylinks-menu">Secondary Links</h2>
                  <ul class="menu menu--secondary-links nav">
                    <?php
                      if($USER_DISPLAY_NAME){
                    ?>
                    <li style="font-size: 1.1rem; color: rgb(91, 97, 107)">
                      <b>
                        <?php echo (isset($LANG['H_WELCOME'])?$LANG['H_WELCOME']:'Welcome').' '.$USER_DISPLAY_NAME; ?>
                      </b>
                    </li>
                    <?php
                      }
                    ?>
                    <li class="first">
                      <a title="My Profile" href="<?php echo $CLIENT_ROOT ?>/profile/viewprofile.php"><b>My Profile</b></a>
                    </li>
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
                </nav>
              </div>
            </div>
          </div>
        </div>
        <div class="navbar navbar-default" id="navbar">
          <div class="container">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
                menu
              </button>
            </div>
            <div id="navbar-collapse" class="navbar-collapse collapse">
              <div class="region region-navigation-collapsible">
                <nav aria-labelledby="block-bootstrap-core-main-menu-menu" id="block-bootstrap-core-main-menu">
                  <h2 class="sr-only" id="block-bootstrap-core-main-menu-menu">Main menu</h2>
                  <ul class="menu menu--main nav navbar-nav">
                    <li class="first">
                      <button id="btnMenu-0" onclick="navigateHome();">
                        <span>
                          Home
                        </span>
                      </button>
                    </li>
                    <li class="expanded dropdown">
                      <button class="navbar-text dropdown-toggle" data-toggle="dropdown">
                        Search Collections <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu">
                        <li class="first">
                          <a href="<?php echo $CLIENT_ROOT?>/collections/<?php echo $actionPage ?>">Search All Collections</a>
                        </li>
                        <li>
                          <a href="<?php echo $CLIENT_ROOT?>/collections/<?php echo $actionPage ?>?db=<?php echo  $NA_COLLID?>">Search National Arboretum Herbarium</a>
                        </li>
                        <li>
                          <a href="<?php echo $CLIENT_ROOT?>/collections/<?php echo $actionPage ?>?db=<?php echo  $BARC_COLLID?>">Search National Seed Herbarium</a>
                        </li>
                        <li>
                          <a href="<?php echo $CLIENT_ROOT?>/collections/<?php echo $actionPage ?>?db=<?php echo  $BPI_SNAPSHOT_COLLID?>">Search National Fungus Collections</a>
                        </li>
                      </ul>
                    </li>
                    <li class="expanded dropdown">
                      <button title="Map Search" class="navbar-text dropdown-toggle" data-toggle="dropdown">
                        Map Search
                        <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu">
                        <li class="first">
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
                    <li class="expanded dropdown">
                      <button class="navbar-text dropdown-toggle" data-toggle="dropdown">
                        About Collections <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu">
                        <li class="first">
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
                    <li class="expanded dropdown">
                      <button class="navbar-text" onclick="navigateToDataUse();">
                        Data Use
                      </button>
                    </li>
                    <li class="expanded dropdown">
                      <button class="navbar-text" onclick="navigateToHelp();">
                        Help
                      </button>
                    </li>
                    <li class="expanded dropdown last">
                      <button class="navbar-text" onclick="navigateToSiteMap();">
                        Site Map
                      </button>
                    </li>
                  </ul>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>

  <div class="main-container container js-quickedit-main-content">
    <div class="row">
      <section class="col-sm-12">
        <div class="highlighted">
          <div class="region region-highlighted">
            <section id="block-sitewidealert" class="block block-sitewide-alert-block clearfix">
              <div data-sitewide-alert></div>
            </section>
            <div data-drupal-messages-fallback class="hidden"></div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>
<script src="<?php echo $CLIENT_ROOT ?>/css/uswds/symbiota/nal.js"></script>
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