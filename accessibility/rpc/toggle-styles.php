<?php
include_once('../../config/symbini.php');
$currentlyEnabledStylesheet = $_REQUEST['currentEnabledStylesheet'];
//session_start();
$accessiblePath = $CSS_BASE_PATH . '/symbiota/condensed.css?ver=14';
$condensedPath = $CSS_BASE_PATH . '/symbiota/accessibility-compliant.css?ver=14';
if($currentlyEnabledStylesheet === $condensedPath){
    $_SESSION['active_stylesheet'] = $accessiblePath;
} else{
    $_SESSION['active_stylesheet'] = $condensedPath;
}
echo $_SESSION['active_stylesheet'];
?>