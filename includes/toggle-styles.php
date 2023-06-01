<?php
$data = json_decode(file_get_contents('php://input'), true);
$CSS_BASE_PATH = $data['data'];
session_start();
$accessiblePath = $CSS_BASE_PATH . "/symbiota/condensed.css?ver=6.css";
$condensedPath = $CSS_BASE_PATH . "/symbiota/accessibility-compliant.css?ver=6.css";
if(isset($_SESSION['active_stylesheet']) && $_SESSION['active_stylesheet'] === $condensedPath ){
    $_SESSION['active_stylesheet'] = $accessiblePath;
    echo $accessiblePath;
} else {
    $_SESSION['active_stylesheet'] = $condensedPath;
    echo $condensedPath;
}
?>