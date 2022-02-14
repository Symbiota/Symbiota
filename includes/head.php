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
<meta name="viewport" content="initial-scale=1.0, user-scalable=yes" />
<?php
if($activateJQuery){
	//For an alternate jQuery UI styling, point link below to another css file
	echo '<link href="'.$CLIENT_ROOT.'/css/jquery-ui.css" type="text/css" rel="stylesheet">';
}
?>
<link href="<?php echo $CLIENT_ROOT; ?>/css/base.css?ver=1" type="text/css" rel="stylesheet">
<link href="<?php echo $CLIENT_ROOT; ?>/css/main.css?ver=1" type="text/css" rel="stylesheet">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $CLIENT_ROOT; ?>/images/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo $CLIENT_ROOT; ?>/images/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo $CLIENT_ROOT; ?>/images/favicon-16x16.png">
<link rel="manifest" href="<?php echo $CLIENT_ROOT; ?>/images/site.webmanifest">
<link rel="mask-icon" href="<?php echo $CLIENT_ROOT; ?>/images/safari-pinned-tab.svg" color="#5bbad5">
