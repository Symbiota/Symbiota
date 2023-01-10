<?php
include_once($SERVER_ROOT.'/classes/SpecUploadBase.php');

class SpecUploadDirect extends SpecUploadBase {

 	public function __construct(){
		parent::__construct();
 	}

	public function __destruct(){
 		parent::__destruct();
	}

 	public function analyzeUpload(){
		if($sourceConn = $this->getSourceConnection()){
			$sql = trim($this->queryStr);
			if(substr($sql,-1) == ";") $sql = substr($sql,0,strlen($sql)-1);
			if(strlen($sql) > 20 && stripos(substr($sql,-20)," limit ") === false) $sql .= " LIMIT 10";
			$rs = $sourceConn->query($sql);
			if($rs){
				$sourceArr = Array();
				if($row = $rs->fetch_assoc()){
					foreach($row as $k => $v){
						$sourceArr[] = strtolower($k);
					}
				}
				else{
					echo '<div style="font-weight:bold;color:red;margin:25px;font-size:120%;">Query did not return any records</div>';
					return false;
				}
				$rs->close();
				$this->occurSourceArr = $sourceArr;
				//$this->echoFieldMapTable($sourceArr);
			}
			else{
				echo '<div style="font-weight:bold;margin:15px;">ERROR: '.$sourceConn->error.'</div>';
				return false;
			}
			$sourceConn->close();
		}
		return false;
	}

 	public function uploadData($finalTransfer){
 		global $CHARSET;

		$sourceConn = $this->getSourceConnection();
		if($sourceConn){
			//Delete all records in uploadspectemp table
			$this->prepUploadData();

			echo "<li style='font-weight:bold;'>Connected to Source Database</li>";
			set_time_limit(800);
			$sourceConn->query("SET NAMES ".str_replace('-','',strtolower($CHARSET)).";");
			//echo "<div>".$this->queryStr."</div><br/>";
			if($result = $sourceConn->query($this->queryStr)){
				echo "<li style='font-weight:bold;'>Results obtained from Source Connection, now reading Resultset... </li>";
				$this->transferCount = 0;
				while($row = $result->fetch_assoc()){
					$recMap = Array();
					$row = array_change_key_case($row);
					foreach($this->occurFieldMap as $symbField => $sMap){
						$valueStr = $row[$sMap['field']];
						$recMap[$symbField] = $valueStr;
					}
					$this->loadRecord($recMap);
					unset($recMap);
				}
				$result->close();

				$this->cleanUpload();

				if($finalTransfer){
					$this->finalTransfer();
				}
			}
			else{
				echo "<hr /><div style='color:red;'>Unable to create a Resultset with the Source Connection. Check connection parameters, source sql statement, and firewall restriction</div>";
				echo "<div style='color:red;'>ERROR: ".$sourceConn->error."</div><hr />";
				//echo "<div>SQL: $this->sourceSql</div>";
			}
			$sourceConn->close();
		}
	}

	private function getSourceConnection() {
		if(!$this->server || !$this->username || !$this->password || !$this->schemaName){
			echo "<div style='color:red;'>One of the required connection variables are null. Please resolve.</div>";
			return false;
		}
		$connection = new mysqli($this->server, $this->username, $this->password, $this->schemaName);
		if($connection->connect_error){
			echo "<div style='color:red;'>Could not connect to Source database!</div>";
			echo "<div style='color:red;'>ERROR: ".mysqli_connect_error()."</div>";
			return false;
		}
		return $connection;
    }

	public function getDbpkOptions(){
		$sFields = $this->occurSourceArr;
		sort($sFields);
		return $sFields;
	}
}
?>