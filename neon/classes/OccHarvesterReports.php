<?php

  include_once($SERVER_ROOT.'/classes/Manager.php');

 /**
 * Controler class for /neon/classes/OccHarvesterReports.php
 *
 */

 class OccHarvesterReports extends Manager {

  public function __construct() {
    parent::__construct(null,'readonly');
    $this->verboseMode = 2;
    set_time_limit(2000);
  }

  public function __destruct() {
    parent::__destruct();
  }

  // Main functions

  // Formats shipment urls (dependes heavily on specific query)
  private function getShipmentUrl($pkCol, $idCol){
    $pksArr = explode(';', $pkCol);
    $idsArr = explode(';', $idCol);

    $urlList = '';
    foreach ($pksArr as $pk){
      $urlList = $urlList.'<a href="manifestviewer.php?shipmentPK='.$pk.'&sampleFilter=harvestingError">'.$idsArr[(array_search($pk, $pksArr))].'</a></br>';
    }
    return $urlList;
  }

  // Gets data about harvesting errors grouped by sampleClass
  public function getHarvestReport(){
    $dataArr = array();

    $sql = 'SELECT sampleClass, errorMessage, COUNT(samplepk) AS cnt, GROUP_CONCAT(DISTINCT s.shipmentPK SEPARATOR ";") AS shipmentPK, GROUP_CONCAT(DISTINCT sh.shipmentID SEPARATOR ";") AS shipmentID FROM NeonSample s LEFT JOIN omoccurrences o ON o.occid = s.occid AND o.collid != "81" JOIN NeonShipment sh  ON s.shipmentPK = sh.shipmentPK WHERE errorMessage IS NOT NULL AND acceptedforanalysis = 1 AND s.checkinTimestamp IS NOT NULL GROUP BY sampleClass, errorMessage;';

    $result = $this->conn->query($sql);

    if ($result) {
      //output data of each row
      while ($row = $result->fetch_assoc()){
        // originally
        // $dataArr[] = $row;
        $dataArr[] = array(
          $row['sampleClass'],
          $row['errorMessage'],
          $row['cnt'],
          $this->getShipmentUrl($row['shipmentPK'], $row['shipmentID']),
        );
      }
      $result->free();
    }
    else {
      $this->errorMessage = 'Harvest report query was not successfull';
      $dataArr = false;
    }
    return $dataArr;
  }

  // Gets total sum of samples with error messages
  public function getTotalSamples(){
      $totalSamples = '';

      $sql = 'SELECT count(s.samplePK) AS totalSamples FROM NeonSample s LEFT JOIN omoccurrences o ON s.occid = o.occid AND o.collid != "81"
WHERE errorMessage IS NOT NULL AND s.checkinTimestamp IS NOT NULL AND acceptedforanalysis = 1;';

    $result = $this->conn->query($sql);

    if ($result) {
      //output data of each row
      while ($row = $result->fetch_assoc()){
        $totalSamples = $row['totalSamples'];
      }
      $result->free();
    }
    else {
      $this->errorMessage = 'Harvest report query was not successfull';
      $totalSamples = false;
    }
    return $totalSamples;
  }

   // Formats array in tabular form (pass array name and headers array as arguments)
  public function htmlTable($data, $headerArr){
    foreach ($headerArr as $header){
      $headers[] = "<th>{$header}</th>";
    }
    $rows = array();
    foreach ($data as $row) {
        $cells = array();
        foreach ($row as $cell) {
          //  original
            $cells[] = "<td>{$cell}</td>";
        }
        $rows[] = "<tr>" . implode('', $cells) . "</tr>";
    }
    return '<table class="table-sortable"><thead>'. implode('', array_merge($headers)).'</thead>' . implode('', array_merge($rows)) . "</table>";
  }
}
?>