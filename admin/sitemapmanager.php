<?php

include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SitemapXMLManager.php');


$sitemapManager = new SitemapXMLManager();

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($sitemapManager->generateSitemap())
        $message = "Sitemap generated and saved!";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>

    <div class="container">
        <h1>Sitemap Generator</h1>

        <form method="post">
            <button type="submit" class="button">Generate Sitemap</button>
        </form>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
    </div>

</body>
</html>