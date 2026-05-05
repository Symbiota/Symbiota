<?php
include_once('../../../config/symbini.php');

header('Content-Type: application/json');

$term = trim($_GET['term'] ?? '');

if (strlen($term) < 2) {
    echo json_encode([]);
    exit;
}

$conn = MySQLiConnectionFactory::getCon('readonly');

$sql = "SELECT refID, bibliographicCitation
        FROM referenceObject
        WHERE bibliographicCitation LIKE ?
        ORDER BY bibliographicCitation
        LIMIT 20";

$stmt = $conn->prepare($sql);

$like = "%{$term}%";
$stmt->bind_param("s", $like);

$stmt->execute();
$res = $stmt->get_result();

$out = [];

while ($row = $res->fetch_assoc()) {
    $out[] = [
        "label" => $row['bibliographicCitation'],
        "value" => $row['bibliographicCitation'], 
        "refid" => $row['refID'] 
    ];
}

echo json_encode($out);