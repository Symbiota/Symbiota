<?php
class OtherCatalog {
    private $conn;
    private $batchSize = 10000;
    private $modifiedUID;
    private $count = 0;
    private $totalProcessed = 0;

    public function __construct($conn, $modifiedUID) {
        $this->conn = $conn;
        $this->modifiedUID = intval($modifiedUID);
    }

    public function copyOtherCatalogNumbers() {
        $lastId = 0;
        $startTime = microtime(true);
        //calculate total count of records to be processed
        $totalSql = "SELECT COUNT(*) AS total
                     FROM omoccurrences
                     WHERE TRIM(otherCatalogNumbers) != ''
                       AND occid NOT IN (SELECT occid FROM omoccuridentifiers)";
        $totalResult = $this->conn->query($totalSql);
        $total = ($totalResult && $row = $totalResult->fetch_assoc()) ? intval($row['total']) : 0;

        echo "<div style='margin-bottom:10px;'>Total records to process: <b>{$total}</b></div>";
        if (ob_get_level() > 0) ob_flush();
        flush();

        if ($total === 0)
            return ['processed' => 0, 'inserted' => 0, 'time' => '0s'];

        while (true) {
            $sql = "SELECT occid, otherCatalogNumbers
                    FROM omoccurrences
                    WHERE TRIM(otherCatalogNumbers) != ''
                      AND occid > $lastId
                      AND occid NOT IN (SELECT occid FROM omoccuridentifiers)
                    ORDER BY occid ASC
                    LIMIT $this->batchSize";

            $result = $this->conn->query($sql);
            if (!$result || $result->num_rows === 0)
                break;

            while ($row = $result->fetch_assoc()) {
                $occid = intval($row['occid']);
                $lastId = $occid;
                $this->processCatalogNumber($occid, $row['otherCatalogNumbers']);
                $this->totalProcessed++;

                //show progress
                if ($this->totalProcessed % $this->batchSize === 0 || $this->totalProcessed === $total) {
                    $percent = round(($this->totalProcessed / $total) * 100, 2);
                    echo "<div>{$this->totalProcessed} / {$total} processed ({$percent}%)</div>";
                    if (ob_get_level() > 0) ob_flush();
                    flush();
                }
            }
        }

        $timeTaken = round(microtime(true) - $startTime, 2) . "s";
        return [
            'processed' => $this->totalProcessed,
            'inserted' => $this->count,
            'time' => $timeTaken
        ];
    }

    private function processCatalogNumber($occid, $otherCatalogNumbers) {
        $parts = preg_split('/[;,]+/', $otherCatalogNumbers);

        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') continue;

            //check for colon for IdentifierName
            if (strpos($part, ':') !== false) {
                [$identifierName, $identifierValue] = array_map('trim', explode(':', $part, 2));
                $this->insertIdentifier($occid, $identifierName ?: '', $identifierValue ?: '');
            } else
                $this->insertIdentifier($occid, '', $part);
        }
    }

    private function insertIdentifier($occid, $identifierName, $identifierValue) {
        if ($identifierName === '' && $identifierValue === '') return;

        $identifierName = $this->conn->real_escape_string($identifierName);
        $identifierValue = $this->conn->real_escape_string($identifierValue);

        $sql = "INSERT IGNORE INTO omoccuridentifiers (occid, identifierName, identifierValue, modifiedUID)
                VALUES ($occid, '$identifierName', '$identifierValue', $this->modifiedUID)";

        if ($this->conn->query($sql)) {
            if ($this->conn->affected_rows > 0) $this->count++;
        } else
            error_log("Insert failed for occid $occid: " . $this->conn->error);
    }
}
?>