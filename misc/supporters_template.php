<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load(['templates/index','templates/supporters']);

header("Content-Type: text/html; charset=" . $CHARSET);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= $DEFAULT_TITLE . ' | ' . $LANG['SUPPORTERS_TITLE']?></title>
    <?php
    include_once($SERVER_ROOT . '/includes/head.php');
    ?>
    <link href="<?= $CSS_BASE_PATH ?>/symbiota/supporters.css?ver=<?= $CSS_VERSION ?>" type="text/css" rel="stylesheet">
</head>

<body>
    <?php
    $displayLeftMenu = true;
    include($SERVER_ROOT . '/includes/header.php');
    ?>
    <div class="navpath">
        <a href="<?php echo htmlspecialchars($CLIENT_ROOT, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>/index.php"><?php echo $LANG['HOME']; ?></a> &gt;&gt;
        <b><?php echo $LANG['SUPPORTERS_TITLE']; ?></b>
    </div>
    <!-- This is inner text! -->
    <div role="main" id="innertext">
        <h1 class="page-heading"><?= $LANG['SUPPORTERS_TITLE'] ?></h1>
        <p><?= $LANG['SUPPORTERS_MESSAGE'] ?></p>
        <div class="support-tiers">
            <!-- Tier 1 -->
            <h2 id="tier-1-heading"><?= $LANG['SUPPORTERS_TIER_1'] ?></h2>
            <ul name="support_tier_1" class="tier-1 table-view" aria-labelledby="tier-1-heading">
                <li><img src="<?= $CLIENT_ROOT ?>/images/layout/logo_symbiota.png" alt="">
                    <p>Allison W. Cusick Botanical Research Fund at Carnegie Museum of Natural History</p>
                </li>
                <li><img src="<?= $CLIENT_ROOT ?>/images/layout/logo_symbiota.png" alt="">
                    <p>Organization Two</p>
                </li>
                <li><a href="https://symbiota.org"><img src="<?= $CLIENT_ROOT ?>/images/layout/logo_symbiota.png" alt="">
                    <p>Organization Three</p></a>
                </li>
                <li><img src="<?= $CLIENT_ROOT ?>/images/layout/logo_symbiota.png" alt="">
                    <p>Organization One</p>
                </li>
                <li class="nologo">
                    <p>Organization Two</p>
                </li>
                <li><img src="<?= $CLIENT_ROOT ?>/images/layout/logo_symbiota.png" alt="">
                    <p>Organization Three</p>
                </li>
                
            </ul>
            <hr />
            <!-- Tier 2 -->
            <h2 id="tier-2-heading"><?= $LANG['SUPPORTERS_TIER_2'] ?></h2>
            <ul name="support_tier_2" class="tier-2 table-view" aria-labelledby="tier-2-heading">
                <li><img src="<?= $CLIENT_ROOT ?>/images/layout/logo_symbiota.png" alt="">
                    <p>Organization Four</p>
                </li>
                <li class="nologo">
                    <p>Allison W. Cusick Botanical Research Fund at Carnegie Museum of Natural History</p>
                </li>
                <li><img src="<?= $CLIENT_ROOT ?>/images/layout/logo_symbiota.png" alt="">
                    <p>Organization Six</p>
                </li>
                <li><img src="<?= $CLIENT_ROOT ?>/images/layout/logo_symbiota.png" alt="">
                    <p>Organization Seven</p>
                </li>
                <li><img src="<?= $CLIENT_ROOT ?>/images/layout/logo_symbiota.png" alt="">
                    <p>Organization Four</p>
                </li>
                <li><img src="<?= $CLIENT_ROOT ?>/images/layout/logo_symbiota.png" alt="">
                    <p>Organization Five</p>
                </li>
                <li><img src="<?= $CLIENT_ROOT ?>/images/layout/logo_symbiota.png" alt="">
                    <p>Organization Six</p>
                </li>
                <li><img src="<?= $CLIENT_ROOT ?>/images/layout/logo_symbiota.png" alt="">
                    <p>Organization Seven</p>
                </li>
            </ul>
            <hr />
            <!-- Tier 3 -->
            <h2 id="tier-3-heading"><?= $LANG['SUPPORTERS_TIER_3'] ?></h2>
            <ul id="support_tier_3" name="support_tier_3" class="tier-3 bullet-view" aria-labelledby="tier-3-heading">
                <li>
                    <p>Cheadle Center for Biodiversity and Ecological Restoration</p>
                </li>
                <li>
                    <p>Herbario del Jardín Botánico BUAP, Puebla, Mexico</p>
                </li>
                <li>
                    <p>Organization Ten</p>
                </li>
                <li>
                    <p>Organization Eleven</p>
                </li>
                <li>
                    <p>Organization Twelve</p>
                </li>
                <li>
                    <p>Organization Eight</p>
                </li>
                <li>
                    <p>Allison W. Cusick Botanical Research Fund at Carnegie Museum of Natural History</p>
                </li>
                <li>
                    <p>Organization Ten</p>
                </li>
                <li>
                    <p>Organization Eleven</p>
                </li>
                <li>
                    <p>Organization Twelve</p>
                </li>
                <li>
                    <p>Organization Eight</p>
                </li>
                <li>
                    <p>Organization Nine</p>
                </li>
                <li>
                    <p>Organization Ten</p>
                </li>
                <li>
                    <p>Organization Eleven</p>
                </li>
                <li>
                    <p>Organization Twelve</p>
                </li>
            </ul>
            <hr />
        </div>
    </div>
    <?php
    include($SERVER_ROOT . '/includes/footer.php');
    ?>
</body>

</html>