<?php
class OtherCatalog {
    private $conn;
    private $batchSize = 10000;
    private $insertBatchSize = 1000;
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
        $totalSql = "SELECT COUNT(*) AS total
                     FROM omoccurrences o
                     LEFT JOIN omoccuridentifiers i ON o.occid = i.occid
                     WHERE otherCatalogNumbers IS NOT NULL
                     AND otherCatalogNumbers != ''
                     AND i.occid IS NULL";
        $totalResult = $this->conn->query($totalSql);
        $total = ($totalResult && $row = $totalResult->fetch_assoc()) ? intval($row['total']) : 0;

        echo "<div style='margin-bottom:10px;'>Total records to process: <b>{$total}</b></div>";
        echo str_repeat(' ', 1024); if (ob_get_level() > 0) ob_flush(); flush();

        if ($total === 0)
            return ['processed' => 0, 'inserted' => 0, 'time' => '0s'];

        while (true) {
            $sql = "SELECT o.occid, o.otherCatalogNumbers
                    FROM omoccurrences o
                    LEFT JOIN omoccuridentifiers i ON o.occid = i.occid
                    WHERE otherCatalogNumbers IS NOT NULL
                    AND otherCatalogNumbers != ''
                    AND o.occid > $lastId
                    AND i.occid IS NULL
                    ORDER BY o.occid ASC
                    LIMIT $this->batchSize";

            $result = $this->conn->query($sql);
            if (!$result || $result->num_rows === 0)
                break;
            $insertValues = [];

            while ($row = $result->fetch_assoc()) {
                $occid = intval($row['occid']);
                $lastId = $occid;
                $parts = preg_split('/[;,]+/', $row['otherCatalogNumbers']);

                foreach ($parts as $part) {
                    $part = trim($part);
                    if ($part === '')
                        continue;

                    if (strpos($part, ':') !== false)
                        [$identifierName, $identifierValue] = array_map('trim', explode(':', $part, 2));
                    else {
                        $identifierName = '';
                        $identifierValue = $part;
                    }

                    $identifierName = $this->conn->real_escape_string($identifierName);
                    $identifierValue = $this->conn->real_escape_string($identifierValue);

                    $insertValues[] = "($occid,'$identifierName','$identifierValue',{$this->modifiedUID})";

                    if (count($insertValues) >= $this->insertBatchSize) {
                        $this->insertBatch($insertValues);
                        $insertValues = [];
                    }
                }

                $this->totalProcessed++;

                if ($this->totalProcessed % $this->batchSize === 0 || $this->totalProcessed === $total) {
                    $percent = round(($this->totalProcessed / $total) * 100, 2);
                    echo "<div>{$this->totalProcessed} / {$total} processed ({$percent}%)</div>";
                    echo str_repeat(' ', 1024); if (ob_get_level() > 0) ob_flush(); flush();
                }
            }
            if (!empty($insertValues))
                $this->insertBatch($insertValues);
        }

        $timeTaken = round(microtime(true) - $startTime, 2) . "s";
        return [
            'processed' => $this->totalProcessed,
            'inserted' => $this->count,
            'time' => $timeTaken
        ];
    }

    private function insertBatch(array $values) {
        $sql = "INSERT IGNORE INTO omoccuridentifiers (occid, identifierName, identifierValue, modifiedUID) VALUES " . implode(',', $values);
        if ($this->conn->query($sql)) {
            $this->count += $this->conn->affected_rows;
        } else {
            error_log("Batch insert failed: " . $this->conn->error);
        }
    }
}
?>