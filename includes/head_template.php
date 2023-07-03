<?php
/*
** Symbiota Redesign
** The version is determined by the number of the release
** set in config/symbini.php ($CSS_VERSION_RELEASE).
** To customize the styles, add your own CSS files to the
** css folder and include them here.
*/
include_once($SERVER_ROOT.'/classes/ProfileManager.php');
$pHandler = new ProfileManager();
$isAccessiblePreferred = $pHandler->getAccessibilityPreference($SYMB_UID);
// $_SESSION['active_stylesheet'] = null; // use this if you want to troubleshoot the behavior of just the persisted preference
$localSession = isset($_SESSION['active_stylesheet']) ? $_SESSION['active_stylesheet'] : null;
?>
<!-- Responsive viewport -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Symbiota styles -->
<link href="<?php echo $CSS_BASE_PATH; ?>/symbiota/normalize.slim.css" type="text/css" rel="stylesheet">
<link href="<?php echo $CSS_BASE_PATH; ?>/symbiota/main.css" type="text/css" rel="stylesheet">
<script src="<?php echo $CLIENT_ROOT; ?>/js/symb/lang.js" type="text/javascript"></script>
<script src="<?php echo $CLIENT_ROOT; ?>/js/symb/accessibilityUtils.js" type="text/javascript"></script>
<?php 
    if($isAccessiblePreferred){
        if(isset($localSession) && strpos($localSession, "condensed.css")){
            ?>
            <link href="<?php echo $CSS_BASE_PATH; ?>/symbiota/accessibility-compliant.css?ver=6.css" type="text/css" rel="stylesheet" data-accessibility-link="accessibility-css-link" disabled >
            <link href="<?php echo $CSS_BASE_PATH; ?>/symbiota/condensed.css?ver=6.css" type="text/css" rel="stylesheet" data-accessibility-link="accessibility-css-link" >
            <?php
        }else{
            ?>
            <link href="<?php echo $CSS_BASE_PATH; ?>/symbiota/accessibility-compliant.css?ver=6.css" type="text/css" rel="stylesheet" data-accessibility-link="accessibility-css-link" >
            <link href="<?php echo $CSS_BASE_PATH; ?>/symbiota/condensed.css?ver=6.css" type="text/css" rel="stylesheet" data-accessibility-link="accessibility-css-link" disabled >
            <?php
        }
    } else{
        if(isset($localSession) && strpos($localSession, "accessibility-compliant.css")){
            ?>
            <link href="<?php echo $CSS_BASE_PATH; ?>/symbiota/accessibility-compliant.css?ver=6.css" type="text/css" rel="stylesheet" data-accessibility-link="accessibility-css-link" >
            <link href="<?php echo $CSS_BASE_PATH; ?>/symbiota/condensed.css?ver=6.css" type="text/css" rel="stylesheet" data-accessibility-link="accessibility-css-link" disabled >
            <?php
        } else{
            ?>
            <link href="<?php echo $CSS_BASE_PATH; ?>/symbiota/accessibility-compliant.css?ver=6.css" type="text/css" rel="stylesheet" data-accessibility-link="accessibility-css-link" disabled >
            <link href="<?php echo $CSS_BASE_PATH; ?>/symbiota/condensed.css?ver=6.css" type="text/css" rel="stylesheet" data-accessibility-link="accessibility-css-link" >
            <?php
        }
    }
?>