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

        while (true) {
            $sql = "SELECT occid, otherCatalogNumbers
                    FROM omoccurrences
                    WHERE TRIM(otherCatalogNumbers) != ''
                      AND occid > $lastId
                    ORDER BY occid ASC
                    LIMIT $this->batchSize";

            $result = $this->conn->query($sql);
            if (!$result || $result->num_rows === 0) {
                break;
            }

            while ($row = $result->fetch_assoc()) {
                $occid = intval($row['occid']);
                $lastId = $occid;
                $value = $this->conn->real_escape_string($row['otherCatalogNumbers']);

                $insertSql = "
                    INSERT IGNORE INTO omoccuridentifiers (occid, identifierValue, modifiedUID)
                    VALUES ($occid, '$value', $this->modifiedUID)
                ";

                if ($this->conn->query($insertSql) && $this->conn->affected_rows > 0) {
                    $this->count++;
                }

                if (!$this->conn->query($insertSql)) {
                    error_log("Insert failed for occid $occid: " . $this->conn->error);
                }

                $this->totalProcessed++;
            }

            echo "<div>$this->batchSize records processed with batch ending with occid $lastId</div>";
            ob_flush();
            flush();
        }

        return [
            'processed' => $this->totalProcessed,
            'inserted' => $this->count
        ];
    }
}