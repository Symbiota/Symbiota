<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load('templates/supporters');

header("Content-Type: text/html; charset=" . $CHARSET);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo $DEFAULT_TITLE . $LANG['DATA_USAGE_GUIDELINES']; ?></title>
    <?php
    include_once($SERVER_ROOT . '/includes/head.php');
    ?>
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
            <section name="support_tier_1" class="support-tier tier-1" aria-labelledby="tier-1-heading">
                <div class="support-item"><img src="<?= $CLIENT_ROOT?>/images/layout/logo_symbiota.png" alt=""><p class="supporter-name">Organization One</p></div>
                <div class="support-item"><img src="<?= $CLIENT_ROOT?>/images/layout/logo_symbiota.png" alt=""><p class="supporter-name">Organization Two</p></div>
                <div class="support-item"><img src="<?= $CLIENT_ROOT?>/images/layout/logo_symbiota.png" alt=""><p class="supporter-name">Organization Three</p></div>
            </section>
            <!-- Tier 2 -->
            <h2 id="tier-2-heading"><?= $LANG['SUPPORTERS_TIER_2'] ?></h2>
            <section name="support_tier_2" class="support-tier tier-2" aria-labelledby="tier-2-heading">
                <div class="support-item"><img src="<?= $CLIENT_ROOT?>/images/layout/logo_symbiota.png" alt=""><p class="supporter-name">Organization Four</p></div>
                <div class="support-item"><img src="<?= $CLIENT_ROOT?>/images/layout/logo_symbiota.png" alt=""><p class="supporter-name">Organization Five</p></div>
                <div class="support-item"><img src="<?= $CLIENT_ROOT?>/images/layout/logo_symbiota.png" alt=""><p class="supporter-name">Organization Six</p></div>
                <div class="support-item"><img src="<?= $CLIENT_ROOT?>/images/layout/logo_symbiota.png" alt=""><p class="supporter-name">Organization Seven</p></div>
            </section>
            <!-- Tier 3 -->
            <h2 id="tier-3-heading"><?= $LANG['SUPPORTERS_TIER_3'] ?></h2>
            <section name="support_tier_3" class="support-tier tier-3" aria-labelledby="tier-3-heading">
                <div class="support-item"><img src="<?= $CLIENT_ROOT?>/images/layout/logo_symbiota.png" alt=""><p class="supporter-name">Organization Eight</p></div>
                <div class="support-item"><img src="<?= $CLIENT_ROOT?>/images/layout/logo_symbiota.png" alt=""><p class="supporter-name">Organization Nine</p></div>
                <div class="support-item"><img src="<?= $CLIENT_ROOT?>/images/layout/logo_symbiota.png" alt=""><p class="supporter-name">Organization Ten</p></div>
                <div class="support-item"><img src="<?= $CLIENT_ROOT?>/images/layout/logo_symbiota.png" alt=""><p class="supporter-name">Organization Eleven</p></div>
                <div class="support-item"><img src="<?= $CLIENT_ROOT?>/images/layout/logo_symbiota.png" alt=""><p class="supporter-name">Organization Twelve</p></div>
            </section>
        </div>
        <p><?= $LANG['SUPPORTERS_THANKYOU'] ?></p>
    </div>
    <?php
    include($SERVER_ROOT . '/includes/footer.php');
    ?>
</body>

</html>