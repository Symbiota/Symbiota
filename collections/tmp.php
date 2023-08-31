<?php
    include_once('../config/symbini.php');
    if($LANG_TAG == 'en' || !file_exists($SERVER_ROOT.'/content/lang/index.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/index.en.php');
    else include_once($SERVER_ROOT.'/content/lang/index.'.$LANG_TAG.'.php');
    header('Content-Type: text/html; charset=' . $CHARSET);
?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">
<head>
    <link href="<?php echo htmlspecialchars($CSS_BASE_PATH, HTML_SPECIAL_CHARS_FLAGS); ?>/symbiota/main.css" type="text/css" rel="stylesheet">
</head>
<body>
    <div class="usa-overlay"></div>
    <header class="usa-header usa-header--basic usa-header--megamenu">
    <div class="usa-nav-container">
        <div class="usa-navbar">
        <div class="usa-logo">
            <!-- <div style="display: flex"> -->
            <img src="<?php echo $CLIENT_ROOT ?>/assets/uswds/img/ars-color-lockup.png"/>
            <em class="usa-logo__text">
            <a href="/" title="<Project title>">
                USDA Biocollections Portal
                </a>
            </em>
            <!-- </div> -->
        </div>
        <button type="button" class="usa-menu-btn">Menu</button>
        </div>
        <nav aria-label="Primary navigation" class="usa-nav">
        <button type="button" class="usa-nav__close">
            <img src="/assets/img/usa-icons/close.svg" role="img" alt="Close" />
        </button>
        <ul class="usa-nav__primary usa-accordion usa-current">
            <li class="usa-nav__primary-item">
            <a href="javascript:void(0);" class="usa-nav-link"
                >Home</a
            >
            </li>
            <li class="usa-nav__primary-item">
            <button
                type="button"
                class="usa-accordion__button usa-nav__link"
                aria-expanded="false"
                aria-controls="basic-mega-nav-section-one"
            >
                <span>Search Collections</span>
            </button>
            <div
                id="basic-mega-nav-section-one"
                class="usa-nav__submenu usa-megamenu"
            >
                <div class="grid-row grid-gap-4">
                <div class="usa-col">
                    <ul class="usa-nav__submenu-list">
                    <li class="usa-nav__submenu-item">
                        <a href="javascript:void(0);">Search All Collections</a>
                    </li>
                    <li class="usa-nav__submenu-item">
                        <a href="javascript:void(0);">Search National Arboretum Herbarium</a>
                    </li>
                    <li class="usa-nav__submenu-item">
                        <a href="javascript:void(0);">Search National Seed Herbarium</a>
                    </li>
                    <li class="usa-nav__submenu-item">
                        <a href="javascript:void(0);">Search National Fungus Collections</a>
                    </li>
                    </ul>
                </div>
                </div>
            </div>
            </li>
            <li class="usa-nav__primary-item">
            <button
                type="button"
                class="usa-accordion__button usa-nav__link usa-current"
                aria-expanded="false"
                aria-controls="basic-mega-nav-section-one"
            >
                <span>Map Search</span>
            </button>
            <div
                id="basic-mega-nav-section-one"
                class="usa-nav__submenu usa-megamenu"
            >
                <div class="grid-row grid-gap-4">
                <div class="usa-col">
                    <ul class="usa-nav__submenu-list">
                    <li class="usa-nav__submenu-item">
                        <a href="javascript:void(0);">Search All Collections</a>
                    </li>
                    <li class="usa-nav__submenu-item">
                        <a href="javascript:void(0);">Search National Arboretum Herbarium</a>
                    </li>
                    <li class="usa-nav__submenu-item">
                        <a href="javascript:void(0);">Search National Seed Herbarium</a>
                    </li>
                    <li class="usa-nav__submenu-item">
                        <a href="javascript:void(0);">Search National Fungus Collections</a>
                    </li>
                    </ul>
                </div>
                </div>
            </div>
            </li>
            <li class="usa-nav__primary-item">
            <button
                type="button"
                class="usa-accordion__button usa-nav__link"
                aria-expanded="false"
                aria-controls="basic-mega-nav-section-one"
            >
                <span>About Collections</span>
            </button>
            <div
                id="basic-mega-nav-section-one"
                class="usa-nav__submenu usa-megamenu"
            >
                <div class="grid-row grid-gap-4">
                <div class="usa-col">
                    <ul class="usa-nav__submenu-list">
                    <li class="usa-nav__submenu-item">
                        <a href="javascript:void(0);">About All USDA Biocollections</a>
                    </li>
                    <li class="usa-nav__submenu-item">
                        <a href="javascript:void(0);">About National Arboretum Herbarium</a>
                    </li>
                    <li class="usa-nav__submenu-item">
                        <a href="javascript:void(0);">About National Seed Herbarium</a>
                    </li>
                    <li class="usa-nav__submenu-item">
                        <a href="javascript:void(0);">About National Fungus Collections</a>
                    </li>
                    </ul>
                </div>
                </div>
            </div>
            </li>
            <li class="usa-nav__primary-item">
            <a href="javascript:void(0);" class="usa-nav-link"
                >Data Use</a
            >
            </li>
            <li class="usa-nav__primary-item">
            <a href="javascript:void(0);" class="usa-nav-link"
                >Help</a
            >
            </li>
            <li class="usa-nav__primary-item">
            <a href='<?php echo $CLIENT_ROOT; ?>/sitemap.php' class="usa-nav-link"
                >Sitemap</a
            >
            </li>
        </ul>
        </nav>
    </div>
    </header>
    <div class="navpath">
    </div>
</body>

<?php
?>