<?php
/*
** Symbiota Redesign
** The version is determined by the number of the release
** set in config/symbini.php ($CSS_VERSION_RELEASE).
** To customize the styles, add your own CSS files to the
** css folder and include them here.
*/
$CSS_PATH = $CLIENT_ROOT . '/css/v' . ($CSS_VERSION_RELEASE ? $CSS_VERSION_RELEASE : 'legacy');
?>
<!-- Responsive viewport -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Symbiota styles -->
<link href="<?php echo $CSS_PATH; ?>/symbiota/normalize.slim.css" type="text/css" rel="stylesheet">
<link href="<?php echo $CSS_PATH; ?>/symbiota/main.css?ver=1" type="text/css" rel="stylesheet">
