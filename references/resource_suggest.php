<?php
include_once('../config/symbini.php');

header('Content-Type: application/json');

$type = $_GET['type'] ?? '';
$term = trim($_GET['term'] ?? '');

if(strlen($term) < 2){
    echo json_encode([]);
    exit;
}

switch($type){
    case 'checklist':
        $table = 'fmchecklists';
        $idCol = 'clid';
        $nameCol = 'name';
        break;

    case 'dataset':
        $table = 'omoccurdatasets';
        $idCol = 'datasetID';
        $nameCol = 'name';
        break;

    case 'collection':
        $table = 'omcollections';
        $idCol = 'collID';
        $nameCol = 'collectionName';
        break;

    default:
        echo json_encode([]);
        exit;
}

$conn = MySQLiConnectionFactory::getCon('readonly');

$sql = "SELECT $idCol, $nameCol 
        FROM $table 
        WHERE $nameCol LIKE ? 
        ORDER BY $nameCol 
        LIMIT 20";

$stmt = $conn->prepare($sql);

$like = "%{$term}%";
$stmt->bind_param("s", $like);

$stmt->execute();
$res = $stmt->get_result();

$out = [];

while($row = $res->fetch_assoc()){
    $out[] = [
        "label" => $row[$nameCol],
        "value" => $row[$idCol]
    ];
}

echo json_encode($out);

?>