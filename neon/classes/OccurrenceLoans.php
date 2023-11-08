<?php
include_once($SERVER_ROOT.'/classes/Manager.php');

class OccurrenceLoans extends Manager{

	function __construct() {
		parent::__construct(null,'write');
	}

	function __destruct(){
		parent::__destruct();
	}

  // Gets all loans for all collections, with links for loan and collection
  public function getLoanOutAll(){
  	$dataArr = array();
    $sql = 'SELECT
l.loanid, l.collidown, c.collectioncode, c.collectionname, i.institutioncode AS borrower, l.forwhom, l.datesent, l.datedue, l.dateclosed, COUNT(o.occid) AS numspecimens, l.createdbyown AS enteredby FROM omoccurloans AS l LEFT JOIN institutions AS i ON l.iidborrower = i.iid LEFT JOIN omcollections AS c  ON l.collidown = c.collid JOIN omoccurloanslink AS o ON l.loanid = o.loanid GROUP BY loanid;';
    if($result = $this->conn->query($sql)){
      while($row = $result->fetch_assoc()){
        $dataArr[] = array(
          'loanid' => '<a href="../collections/loans/outgoing.php?collid='.$row['collidown'].'&loanid='.$row['loanid'].'">'.$row['loanid'].'</a>',
          'collection' => '<a href="../collections/misc/collprofiles.php?collid='.$row['collidown'].'">['.$row['collectioncode'].'] '.$row['collectionname'].'</a>',
          'borrower' => is_null($row['borrower'])?'<span style="color:lightgray;">NULL</span>':$row['borrower'],
          'forwhom' => is_null($row['forwhom'])?'<span style="color:lightgray;">NULL</span>':$row['forwhom'],
          'datesent' => is_null($row['datesent'])?'<span style="color:lightgray;">NULL</span>':$row['datesent'],
          'datedue' => is_null($row['datedue'])?'<span style="color:lightgray;">NULL</span>':$row['datedue'],
          'dateclosed' => is_null($row['dateclosed'])?'<span style="color:lightgray;">NULL</span>':$row['dateclosed'],
          'numspecimens' => is_null($row['numspecimens'])?'<span style="color:lightgray;">NULL</span>':$row['numspecimens'],
          'enteredby' => is_null($row['enteredby'])?'<span style="color:lightgray;">NULL</span>':$row['enteredby'],
        );
      }
      $result->free();
    }
    else {
      $this->errorMessage = 'Loan out query was not successfull';
      $dataArr = false;
    }
    return $dataArr;
  }

  // Gets count of all samples in open loans in all collections
  public function getOutSamplesCnt(){
    $retArr = array();
    $sql = 'SELECT COUNT(occid) AS totalOut FROM omoccurloanslink WHERE returndate IS NULL;';
    if($result = $this->conn->query($sql)){
      while($row = $result->fetch_assoc()){
        $totalLoaned = $row['totalOut'];
      }
      $result->free();
    }
    else {
      $this->errorMessage = 'Loan out query was not successfull';
      $totalLoaned = false;
    }
    return $totalLoaned;
  }
}
?>