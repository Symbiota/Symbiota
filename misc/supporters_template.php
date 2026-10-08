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
                <li>
                    <img src="<?= $CLIENT_ROOT ?>/images/layout/logo_symbiota.png" alt="">
                    <p>Super Cali Fragilistick Expi Alidocous</p>
                </li>
                <li>
                    <a href="https://symbiota.org">
                    <p>Supporter Two</p></a>
                </li>
                <li>
                    <a href="https://symbiota.org">
                    
                    <img src="<?= $CLIENT_ROOT ?>/images/layout/logo_symbiota.png" alt="">
                    <p>Supporter Three</p></a>
                </li>
                <li>
                    <p>Supporter Four</p>
                </li>
                <li>
                    <img src="<?= $CLIENT_ROOT ?>/images/layout/logo_symbiota.png" alt="">
                    <p>Super Cali Fragilistick Expi Alidocous</p>
                </li>
                <li>
                    <p>Supporter Six Super Cali Fragilistick Expi Alidocous</p>
                </li>
                 <li>
                    <a href="https://symbiota.org">
                    <img src="<?= $CLIENT_ROOT ?>/images/layout/logo_symbiota.png" alt="">
                    <p>Supporter Seven</p></a>
                </li>
            </ul>
            <!-- Tier 2 -->
            <h2 id="tier-2-heading"><?= $LANG['SUPPORTERS_TIER_2'] ?></h2>
            <ul name="support_tier_2" class="tier-2 table-view" aria-labelledby="tier-2-heading">
                <li>
                    <a href="https://symbiota.org">
                    <img src="<?= $CLIENT_ROOT ?>/images/layout/logo_symbiota.png" alt="">
                    <p>Supporter Eight</p></a>
                </li>
                <li>
                    <p>Supporter Nine Super Cali Fragilistick Expi Alidocous</p>
                </li>
                <li>
                    <img src="<?= $CLIENT_ROOT ?>/images/layout/logo_symbiota.png" alt="">
                    <p>Supporter Ten</p>
                </li>
                <li>
                    <a href="https://symbiota.org">
                    <p>Supporter Eleven</p></a>
                </li>
                <li>
                    <img src="<?= $CLIENT_ROOT ?>/images/layout/logo_symbiota.png" alt="">
                    <p>Supporter Twelve</p>
                </li>
                <li>
                    <p>Supporter Thirteen</p>
                </li>
                <li>
                    <img src="<?= $CLIENT_ROOT ?>/images/layout/logo_symbiota.png" alt="">
                    <p>Supporter Fourteen</p>
                </li>
                <li>
                    <p>Supporter Fifteen</p>
                </li>
            </ul>
            <!-- Tier 3 -->
            <h2 id="tier-3-heading"><?= $LANG['SUPPORTERS_TIER_3'] ?></h2>
            <ul id="support_tier_3" name="support_tier_3" class="tier-3 bullet-view" aria-labelledby="tier-3-heading"><!-- change bullet-view to table-view if desired -->
                <li>
                    <p>Super Cali Fragilistick Expi Alidocous</p>
                </li>
                <li>
                    <a href="https://symbiota.org">
                    <img src="<?= $CLIENT_ROOT ?>/images/layout/logo_symbiota.png" alt="">
                    <p>Super Cali Fragilistick Expi Alidocous</p></a>
                </li>
                <li>
                    <p>Supporter Eighteen</p>
                </li>
                <li>
                    <p>Supporter Nineteen</p>
                </li>
                <li>
                    <a href="https://symbiota.org">
                    <p>Supporter Twenty  Super Cali Fragilistick Expi Alidocous</p></a>
                </li>
                <li>
                    <p>Supporter Twenty-one</p>
                </li>
                <li>
                    <img src="<?= $CLIENT_ROOT ?>/images/layout/logo_symbiota.png" alt="">
                    <p>Supporter Twenty-two Super Cali Fragilistick Expi Alidocous</p>
                </li>
                <li>
                    <p>Supporter Twenty-three</p>
                </li>
                <li>
                    <p>Supporter Twenty-four</p>
                </li>
                <li>
                    <p>Supporter Twenty-five</p>
                </li>
                <li>
                    <p>Supporter Twenty-six</p>
                </li>
                <li>
                    <img src="<?= $CLIENT_ROOT ?>/images/layout/logo_symbiota.png" alt="">
                    <p>Supporter Twenty-seven</p>
                </li>
                <li>
                    <p>Supporter Twenty-eight  Super Cali Fragilistick Expi Alidocous</p>
                </li>
                <li>
                    <p>Supporter Twenty-nine  Super Cali Fragilistick Expi Alidocous</p>
                </li>
                <li>
                    <a href="https://symbiota.org">
                    <p>Supporter Thirty</p></a>
                </li>
            </ul>
        </div>
    </div>
    <?php
    include($SERVER_ROOT . '/includes/footer.php');
    ?>
</body>

</html>