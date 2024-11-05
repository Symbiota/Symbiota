<?php
/*
 * Customize styling by adding or modifying CSS file links below
 * Default styling for individual page is defined within /css/symb/
 * Individual styling can be customized by:
 *     1) Uncommenting the $CUSTOM_CSS_PATH variable below
 *     2) Copying individual CCS file to the /css/symb/custom directory
 *     3) Modifying the sytle definiation within the file
 */

//$CUSTOM_CSS_PATH = '/css/symb/custom';
?>
<!-- Responsive viewport -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Symbiota styles -->
<link href="<?= $CSS_BASE_PATH ?>/symbiota/header.css?ver=<?= $CSS_VERSION ?>" type="text/css" rel="stylesheet">
<link href="<?= $CSS_BASE_PATH ?>/symbiota/footer.css?ver=<?= $CSS_VERSION ?>" type="text/css" rel="stylesheet">
<link href="<?= $CSS_BASE_PATH ?>/symbiota/main.css?ver=<?= $CSS_VERSION ?>" type="text/css" rel="stylesheet">
<link href="<?= $CSS_BASE_PATH ?>/symbiota/customizations.css?ver=<?= $CSS_VERSION ?>" type="text/css" rel="stylesheet">

<script src="<?= $CLIENT_ROOT ?>/js/symb/lang.js" type="text/javascript"></script>
