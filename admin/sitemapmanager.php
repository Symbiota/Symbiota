<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SitemapXMLManager.php');

$sitemapManager = new SitemapXMLManager();
$sitemapPath = $SERVER_ROOT . '/content/sitemaps/sitemap.xml';
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($sitemapManager->generateSitemap()) {
        $message = "Sitemap generated and saved!";
    } else {
        $message = "Error: " . $sitemapManager->getSitemapMessage();
    }
}

// check if sitemap.xml already exists and display the date last modified
if (file_exists($sitemapPath)) {
    $lastModified = date("Y-m-d", filemtime($sitemapPath));
    $sitemapExist = "There is an existing sitemap (Last generated: {$lastModified})";
}
?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <?php
    include_once($SERVER_ROOT.'/includes/head.php');
    ?>

    <style type="text/css">
        label { font-weight:bold; }
        .message {margin-bottom: 1rem;}
        .info {margin-bottom: 1rem;}
        button { margin: 15px; }
    </style>
</head>
<body>
    <?php
    include($SERVER_ROOT.'/includes/header.php');
    ?>
    <div class="container" id="innertext">
        <h1>Sitemap Generator</h1>

        <?php if (!empty($sitemapExist)): ?>
            <div class="info"><?php echo $sitemapExist; ?></div>
        <?php endif; ?>

        <form method="post">
            <button type="submit" class="button">Generate Sitemap</button>
        </form>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if (!empty($message) && strpos($message,'Sitemap generated') === 0): ?>
        <div class="info">
            <h3>Next steps:</h3>
            <ol>
                <li class="bottom-breathing-room-rel-sm">Check that the file was saved to <b>/content/sitemaps/sitemap.xml.</b></li>
                <li class="bottom-breathing-room-rel-sm">Make sure the sitemap link in robots.txt points to the path:</li>
                <b>Sitemap: <?= GeneralUtil::getDomain() . $CLIENT_ROOT ?>/content/sitemaps/sitemap.xml</b>
            </ol>
        </div>
    <?php endif; ?>
    </div>
    <?php
    include($SERVER_ROOT.'/includes/footer.php');
    ?>
</body>
</html>