<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SitemapXMLManager.php');

$sitemapManager = new SitemapXMLManager();
$sitemapPath = $SERVER_ROOT . '/content/sitemaps/sitemap.xml';
$message = "";
$protocol = 'https';

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

if (file_exists($sitemapPath)) {
    $lastModified = date("Y-m-d", filemtime($sitemapPath));
    $sitemapExist = "There is an existing sitemap (Last generated: {$lastModified})";
}
?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <div class="container">
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
</body>
</html>