<?php

include_once($SERVER_ROOT.'/classes/Manager.php');

class DynamicPropertiesRestorer extends Manager {

    private $stats = array(
        'totalRows' => 0,
        'updatedRows' => 0,
        'invalidJsonRows' => 0,
        'missingLabelFormatsRows' => 0
    );

    public function __construct(){
        parent::__construct(null,'write');
    }

    public function __destruct(){
        parent::__destruct();
    }

    public static function restoreLabelFormatsOnly($echoProgress = false){
        $manager = new self();

        $tableArr = array('omcollections');

        $manager->stats = array(
            'totalRows' => 0,
            'updatedRows' => 0,
            'invalidJsonRows' => 0,
            'missingLabelFormatsRows' => 0
        );

        foreach($tableArr as $tableName){
            if(!self::restoreForTable($manager, $tableName, $echoProgress)){
                return false;
            }
        }

        return true;
    }

    private static function restoreForTable($manager, $tableName, $echoProgress = false){
        $selectSql = 'SELECT collid, dynamicProperties FROM '.$tableName.' WHERE dynamicProperties IS NOT NULL AND dynamicProperties != ""';
        if(!$result = $manager->conn->query($selectSql)){
            $manager->errorMessage = 'Select failed for '.$tableName.': '.$manager->conn->error;
            return false;
        }

        $updateSql = 'UPDATE '.$tableName.' SET dynamicProperties = ? WHERE collid = ?';
        if(!$updateStmt = $manager->conn->prepare($updateSql)){
            $manager->errorMessage = 'Prepare failed for '.$tableName.': '.$manager->conn->error;
            $result->close();
            return false;
        }

        while($row = $result->fetch_assoc()){
            $manager->stats['totalRows']++;

            $collId = (int)$row['collid'];
            $decoded = self::decodeDynamicPropertiesJson($row['dynamicProperties']);

            $labelFormats = array();
            if(!is_array($decoded)){
                $manager->stats['invalidJsonRows']++;
                if($echoProgress) echo 'Rewriting '.$tableName.' collid '.$collId.': invalid JSON (defaulting to empty labelFormats)'.PHP_EOL;
            }
            else{
                if(isset($decoded['labelFormats']) && is_array($decoded['labelFormats'])){
                    $labelFormats = $decoded['labelFormats'];
                }
                else{
                    $manager->stats['missingLabelFormatsRows']++;
                    if($echoProgress) echo 'Rewriting '.$tableName.' collid '.$collId.': no labelFormats key (defaulting to empty array)'.PHP_EOL;
                }
            }

            $newJson = json_encode(array('labelFormats' => $labelFormats), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            if($newJson === false){
                $manager->warningArr[] = 'Failed encoding '.$tableName.' collid '.$collId.': '.json_last_error_msg();
                if($echoProgress) echo 'Failed encoding '.$tableName.' collid '.$collId.': '.json_last_error_msg().PHP_EOL;
                continue;
            }
            $updateStmt->bind_param('si', $newJson, $collId);
            if(!$updateStmt->execute()){
                $manager->warningArr[] = 'Failed updating '.$tableName.' collid '.$collId.': '.$updateStmt->error;
                if($echoProgress) echo 'Failed updating '.$tableName.' collid '.$collId.': '.$updateStmt->error.PHP_EOL;
                continue;
            }

            $manager->stats['updatedRows']++;
            if($echoProgress) echo 'Updated '.$tableName.' collid '.$collId.PHP_EOL;
        }

        $updateStmt->close();
        $result->close();

        return true;
    }

    private static function decodeDynamicPropertiesJson($jsonString){
        $decoded = json_decode($jsonString, true);
        if(json_last_error() !== JSON_ERROR_NONE){
            return null;
        }

        // Some legacy rows were saved as JSON-encoded strings (double encoded).
        if(is_string($decoded)){
            $decodedInner = json_decode($decoded, true);
            if(json_last_error() !== JSON_ERROR_NONE){
                return null;
            }
            return $decodedInner;
        }

        return $decoded;
    }

    public function getStats(){
        return $this->stats;
    }
}

?>