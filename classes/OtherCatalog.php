<?php
class OtherCatalog {
    private $conn;
    private $batchSize = 100000;
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
            }

            echo "<div>{$this->totalProcessed} total processed... last occid $lastId</div>";
            ob_flush();
            flush();
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