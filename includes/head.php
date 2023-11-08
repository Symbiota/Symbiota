<?php
/*
 * Customize styling by adding or modifying CSS file links below
 * Default styling for individual page is defined within /css/symb/
 * Individual styling can be customized by:
 *     1) Uncommenting the $CUSTOM_CSS_PATH variable below
 *     2) Copying individual CCS file to the /css/symb/custom directory
 *     3) Modifying the sytle definiation within the file
 */

$CUSTOM_CSS_PATH = '/css/symb/custom';
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="<?php echo $CLIENT_ROOT; ?>/css/v202209/jquery-ui.css" type="text/css" rel="stylesheet">
<!-- UNIVERSAL CSS –––––––––––––––––––––––––––––––––––––––––––––––––– -->
<link rel="stylesheet" href="<?php echo $CLIENT_ROOT; ?>/css/normalize.css">
<link rel="stylesheet" href="<?php echo $CLIENT_ROOT; ?>/css/skeleton.css">
<link rel="stylesheet" href="<?php echo $CLIENT_ROOT; ?>/css/base.css?ver=1" type="text/css">
<link rel="stylesheet" href="<?php echo $CLIENT_ROOT; ?>/css/neon.css?ver=1">
<link rel="stylesheet" href="<?php echo $CLIENT_ROOT; ?>/css/main.css?ver=2" type="text/css">

<script type="text/javascript" src="<?php echo $CLIENT_ROOT; ?>/js/symb/lang.js"></script>
<script type="text/javascript">
	//Uncomment following line to support toggling of database content containing DIVs with lang classes in form of: <div class="lang en">Content in English</div><div class="lang es">Content in Spanish</div>
	//setLanguageDiv();
</script>