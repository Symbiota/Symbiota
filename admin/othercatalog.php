<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT . '/classes/OtherCatalog.php');
ini_set('max_execution_time', 300);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_copy'])) {
    $conn = MySQLiConnectionFactory::getCon("write");

    if (!$conn) {
        $message = "Failed to connect to the database.";
    } else {
        $catalogCopier = new OtherCatalog($conn, $GLOBALS['SYMB_UID']);
        $result = $catalogCopier->copyOtherCatalogNumbers();

        $message = "Processed {$result['processed']} records. Inserted {$result['inserted']} new row(s) into omoccuridentifiers.<br>{$result['time']}";
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
    <p>This tool copies all non-empty otherCatalogNumbers from the omoccurrences table to the omoccuridentifiers table. Each value is inserted as a new row using the current user ID as modifiedUID.</p>

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