<?php 

  include_once($SERVER_ROOT.'/classes/Manager.php');

 /**
 * Controler class for /neon/classes/PortalStatistics.php
 * 
 */

 class PortalStatistics extends Manager {
   
  public function __construct() {
    parent::__construct(null,'readonly');
    $this->verboseMode = 2;
    set_time_limit(2000);
  }

  public function __destruct() {
    parent::__destruct();
  }

  // Main functions

  // Gets total NEON samples
  public function getTotalNeonSamples(){
    $totalSamples = '';

    $sql = 'SELECT ROUND(SUM(recordcnt),-3) AS totalSamples FROM omcollectionstats AS s JOIN omcollections AS c ON s.collid = c.collid WHERE c.institutioncode = "NEON" AND recordcnt > 1;';

    $result = $this->conn->query($sql);

    while ($row = $result->fetch_row()){
      $totalSamples = $row;
    }
    $result->free(); 
    return $totalSamples[0];
  }

  // Gets NEON samples by taxonomic category
  public function getNeonSamplesByTax(){
    $dataArr = array();

    $sql = 'SELECT name, samples, db, lastUpdated FROM NeonStatsOcc WHERE lastUpdated = (SELECT MAX(lastUpdated) FROM NeonStatsOcc);';

    $result = $this->conn->query($sql);

    while ($row = $result->fetch_array()){
      $dataArr[] = $row;
    }
    $result->free(); 
    return $dataArr;
  }

  // Gets total NEON taxa
  public function getTotalNeonTaxa(){
    $totalTaxa = '';

    $sql = 'SELECT ROUND(COUNT(DISTINCT o.tidinterpreted),-2) AS roundedTotalTaxa FROM NeonSample AS n INNER JOIN omoccurrences AS o ON o.occid = n.occid WHERE o.tidinterpreted IS NOT NULL;';

    $result = $this->conn->query($sql);

    while ($row = $result->fetch_row()){
      $totalTaxa = $row;
    }
    $result->free(); 
    return $totalTaxa[0];
  }

    // Gets NEON samples by taxonomic category
  public function getNeonTaxa(){
    $dataArr = array();

    $sql = 'SELECT name, taxa, db, lastUpdated FROM NeonStatsTax WHERE lastUpdated = (SELECT MAX(lastUpdated) FROM NeonStatsTax);';

    $result = $this->conn->query($sql);

    while ($row = $result->fetch_array()){
      $dataArr[] = $row;
    }
    $result->free(); 
    return $dataArr;
  }

 };

;?>