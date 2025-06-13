<?php
include_once('../config/symbini.php');
ini_set('max_execution_time', 300);

$message = '';
$count = 0;
$totalProcessed = 0;
$batchSize = 1000;
$lastSeenId = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_copy'])) {
    $conn = MySQLiConnectionFactory::getCon("write");

    if (!$conn) {
        $message = "Failed to connect to the database.";
    } else {
        $modifiedUID = intval($GLOBALS['SYMB_UID']);

        while (true) {
            $sql = "SELECT occid, otherCatalogNumbers
                    FROM omoccurrences
                    WHERE TRIM(otherCatalogNumbers) != ''
                      AND occid > $lastSeenId
                    ORDER BY occid ASC
                    LIMIT $batchSize";

            $result = $conn->query($sql);

            if (!$result || $result->num_rows === 0) {
                break;
            }

            while ($row = $result->fetch_assoc()) {
                $occid = intval($row['occid']);
                $lastSeenId = $occid;
                $value = $conn->real_escape_string($row['otherCatalogNumbers']);

                $insertSql = "
                    INSERT IGNORE INTO omoccuridentifiers (occid, identifierValue, modifiedUID)
                    VALUES ($occid, '$value', $modifiedUID)
                ";

                if ($conn->query($insertSql) && $conn->affected_rows > 0) {
                    $count++;
                }
                $totalProcessed++;
            }
        }

        $message = "Processed $totalProcessed records. Inserted $count new row(s) into <code>omoccuridentifiers</code>.";
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Copy Other Catalog Numbers</title>
    <?php include_once($SERVER_ROOT.'/includes/head.php'); ?>
</head>
<body>
<?php include($SERVER_ROOT.'/includes/header.php'); ?>

<div class="container" id="innertext">
    <h2>Copy Other Catalog Numbers to Identifier Table</h2>
    <p>This tool copies all non-empty <code>otherCatalogNumbers</code> from the <code>omoccurrences</code> table to the <code>omoccuridentifiers</code> table. Each value is inserted as a new row using the current user ID as <code>modifiedUID</code>.</p>

    <?php if (!empty($message)): ?>
        <div class="successbox"><?= $message ?></div>
    <?php endif; ?>

    <form method="post">
        <button type="submit" name="run_copy" class="button">Run Copy Process</button>
    </form>
</div>

<?php include($SERVER_ROOT.'/includes/footer.php'); ?>
</body>
</html>

