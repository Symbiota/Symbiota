<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SitemapXMLManager.php');

$sitemapManager = new SitemapXMLManager();
$sitemapPath = $SERVER_ROOT . '/content/sitemaps/sitemap.xml';
$message = "";
$protocol = 'https';

// check for valid protocol value
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $protocolCheck = $_POST['protocol'] ?? '';
    if (in_array($protocolCheck, ['http', 'https'], true))
        $protocol = $protocolCheck;
}

$sitemapManager->setProtocol($protocol);

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
            <label for="protocol">Protocol:
                <select name="protocol" id="protocol">
                    <option value="https">https</option>
                    <option value="http">http</option>
                </select>
            </label>

            <button type="submit" class="button">Generate Sitemap</button>
        </form>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
    </div>
    <?php
    include($SERVER_ROOT.'/includes/footer.php');
    ?>
</body>
</html>