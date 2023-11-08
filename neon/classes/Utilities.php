<?php

  include_once($SERVER_ROOT.'/classes/Manager.php');

 /**
 * Controler class for /neon/classes/Utilities.php
 *
 */

 class Utilities extends Manager {

  public function __construct() {
    parent::__construct(null,'readonly');
    $this->verboseMode = 2;
    set_time_limit(2000);
  }

  public function __destruct() {
    parent::__destruct();
  }

  // Main functions


  // Formats array in tabular form (pass array name and headers array as arguments)
  public function htmlTable($data, $headerArr){
    foreach ($headerArr as $header){
      $headers[] = "<th>{$header}</th>";
    }
    $rows = array();
    foreach ($data as $row) {
        $cells = array();
        foreach ($row as $cell) {
            $cells[] = "<td>{$cell}</td>";
          }
          $rows[] = "<tr>" . implode('', $cells) . "</tr>";
        }
    $tableElement = '<table class="table-sortable"><thead>'. implode('', array_merge($headers)).'</thead>' . implode('', array_merge($rows)) . "</table>";
    return $tableElement;
  }
}
?>