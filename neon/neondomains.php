<?php
/*
 * Returns JSON containing NEON Domains information available for Biorepo database
 * 
 */
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/DatasetsMetadata.php');
header('Content-type: Application/JSON');

$datasetsMetadata = new DatasetsMetadata();
$taxonArr = $datasetsMetadata->getNeonDomains();

echo json_encode($taxonArr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>