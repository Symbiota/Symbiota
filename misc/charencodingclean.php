<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT . '/config/dbconnection.php');
include_once($SERVER_ROOT . '/classes/utilities/Encoding.php');

header("Content-Type: text/html; charset=".$CHARSET);
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>"/>
	</head>

	<body>
    <h1 class="page-heading">Character Encoding Cleaner</h1>
		<div>
			<b>READ ME:</b> This page is for cleaning central database tabels that may contain mixed latin and UTF-8 character sets.
			This module will convert mixed character sets to UTF-8. If you want to convert from UTF-8 to another character set,
			you will need to modify the code.
		</div>
		<?php

		$cleanManager = new characterEnclodeCleaner();
		//$cleanManager->cleanOccurrences(1,1,0,100000);
		//$cleanManager->cleanDeterminations(1,1,224,2000);
		//$cleanManager->cleanTaxa(1,1,486080,100000);

		?>
	</body>
</html>
<?php

class characterEnclodeCleaner{

	private $conn;

	function __construct() {
		$this->conn = MySQLiConnectionFactory::getCon('write');
		set_time_limit(1800);
	}

	function __destruct(){
 		if(!($this->conn === false)) $this->conn->close();
	}

	public function cleanOccurrences($preview = 1, $fix = 0, $startOccid = 0, $limit = 100000){
		$sql = 'SELECT * '.
			'FROM omoccurrences ';
		if($startOccid) $sql .= 'WHERE occid > '.$startOccid.' ';
		$sql .= 'ORDER BY occid ';
		if($limit) $sql .= 'LIMIT '.$limit;
		//echo $sql;
		$excludeFields = array('occid','dbpk','collid','tidinterpreted','eventdate','day','month','year','startdayofyear','enddayofyear',
			'modified','datelastmodified','decimallatitude','decimallongitude','coordinateuncertaintyinmeters','footprintwkt',
			'coordinateprecision','minimumelevationinmeters','maximumelevationinmeters','observeruid','processingstatus','duplicatequantity',
			'dateentered');
		$rs = $this->conn->query($sql);
		echo '<ol>';
		while($r = $rs->fetch_assoc()){
			$rActive = array_change_key_case($r);
			$occid = $rActive['occid'];
			echo '<li><b>occid: </b>'.$occid.': ';
			$setArr = array();
			$problem = false;

			foreach($rActive as $k => $v){
				if($v && !in_array($k, $excludeFields) && !is_numeric($v)){
					$vCleaned = $this->cleanInStr($v);
					$toUtf8 = Encoding::toUTF8($vCleaned);
					$fixUtf8 = Encoding::fixUTF8($vCleaned);
					if($fix && $fixUtf8 != $vCleaned){
						$problem = true;
					}
					if(!$fix && $toUtf8 != $vCleaned){
						$problem = true;
					}
					if($preview){
						echo '<div style="margin-left:10px;">';
						echo '<b>'.$k.':</b>: '.$v.'; ';
						echo '<b>toUTF8 method:</b> '.$toUtf8.'; ';
						echo '<b>fixUTF8 method:</b> '.$fixUtf8;
						echo '</div>';
					}
					if($fix){
						$setArr[] = $k.' = "'.$fixUtf8.'"';
					}
					else{
						$setArr[] = $k.' = "'.$toUtf8.'"';
					}
				}
			}
			if($setArr){
				$sqlFix = 'UPDATE omoccurrences SET '.implode(',',$setArr).' WHERE occid = '.$occid;
				if($problem){
					echo '<div style="margin-left:10px;"><b>PROBLEM:</b>'.$sqlFix.'</div>';
					if(!$preview){
						if(!$this->conn->query($sqlFix)){
							echo '<div style="margin-left:10px;">ERROR: '.$this->conn->error.'; SQL: '.$sqlFix.'</div>';
						}
					}
				}
			}
			echo '</li>';
		}
		$rs->free();
		echo '</ol>';
	}

	public function cleanDeterminations($preview = 1, $fix = 0, $startDetid = 0, $limit = 100000){
		$sql = 'SELECT detid, identifiedby, dateidentified, sciname, scientificnameauthorship, identificationreferences, identificationremarks, taxonremarks '.
			'FROM omoccurdeterminations ';
		if($startDetid) $sql .= 'WHERE detid > '.$startDetid.' ';
		$sql .= 'ORDER BY detid ';
		if($limit) $sql .= 'LIMIT '.$limit;
		//echo $sql;
		$rs = $this->conn->query($sql);
		echo '<ol>';
		while($r = $rs->fetch_assoc()){
			$detid = $r['detid'];
			echo '<li><b>detid:</b> '.$detid.': ';
			$setArr = array();
			$problem = false;

			foreach($r as $k => $v){
				if($v && $k != 'detid'){
					$vCleaned = $this->cleanInStr($v);
					$toUtf8 = Encoding::toUTF8($vCleaned);
					$fixUtf8 = Encoding::fixUTF8($vCleaned);
					if($fix && $fixUtf8 != $vCleaned){
						$problem = true;
					}
					if(!$fix && $toUtf8 != $vCleaned){
						$problem = true;
					}
					if($preview){
						echo '<div style="margin-left:10px;">';
						echo '<b>'.$k.':</b>: '.$v.'; ';
						echo '<b>toUTF8 method:</b> '.$toUtf8.'; ';
						echo '<b>fixUTF8 method:</b> '.$fixUtf8;
						echo '</div>';
					}
					if($fix){
						$setArr[] = $k.' = "'.$fixUtf8.'"';
					}
					else{
						$setArr[] = $k.' = "'.$toUtf8.'"';
					}
				}
			}
			if($setArr){
				$sqlFix = 'UPDATE omoccurdeterminations SET '.implode(',',$setArr).' WHERE detid = '.$detid;
				if($problem){
					echo '<div style="margin-left:10px;"><b>PROBLEM:</b>'.$sqlFix.'</div>';
					if(!$preview){
						if(!$this->conn->query($sqlFix)){
							echo '<div style="margin-left:10px;">ERROR: '.$this->conn->error.'; SQL: '.$sqlFix.'</div>';
						}
					}
				}
			}
			echo '</li>';
		}
		$rs->free();
		echo '</ol>';
	}

	public function cleanTaxa($preview = 1, $fix = 0, $startTid = 0, $limit = 100000){
		$sql = 'SELECT tid, author '.
			'FROM taxa '.
			'WHERE author IS NOT NULL ';
		if($startTid) $sql .= 'AND tid > '.$startTid.' ';
		$sql .= 'ORDER BY tid ';
		if($limit) $sql .= 'LIMIT '.$limit;
		//echo $sql;

		$rs = $this->conn->query($sql);
		echo '<ol>';
		while($r = $rs->fetch_assoc()){
			$tid = $r['tid'];
			echo '<li><b>tid: </b>'.$tid.': ';
			$setArr = array();
			$problem = false;
			foreach($r as $k => $v){
				if($v && $k != 'tid'){
					$vCleaned = $this->cleanInStr($v);
					$toUtf8 = Encoding::toUTF8($vCleaned);
					$fixUtf8 = Encoding::fixUTF8($vCleaned);
					if($fix && $fixUtf8 != $vCleaned){
						$problem = true;
					}
					if(!$fix && $toUtf8 != $vCleaned){
						$problem = true;
					}
					if($preview || $problem){
						echo '<div style="margin-left:10px;">';
						echo '<b>'.$k.':</b>: '.$v.'; ';
						echo '<b>toUTF8 method:</b> '.$toUtf8.'; ';
						echo '<b>fixUTF8 method:</b> '.$fixUtf8;
						echo '</div>';
					}
					if($fix){
						$setArr[] = $k.' = "'.$fixUtf8.'"';
					}
					else{
						$setArr[] = $k.' = "'.$toUtf8.'"';
					}
				}
			}
			if($setArr){
				$sqlFix = 'UPDATE taxa SET '.implode(',',$setArr).' WHERE tid = '.$tid;
				if($problem){
					echo '<div style="margin-left:10px;"><b>PROBLEM:</b> '.$sqlFix.'</div>';
					if(!$preview){
						if(!$this->conn->query($sqlFix)){
							echo '<div style="margin-left:10px;">ERROR: '.$this->conn->error.'; SQL: '.$sqlFix.'</div>';
						}
					}
				}
			}
			echo '</li>';
		}
		$rs->free();
		echo '</ol>';
	}

	private function cleanInStr($str){
		$badwordchars=array("\xe2\x80\x98", // left single quote
							"\xe2\x80\x99", // right single quote
							"\xe2\x80\x9c", // left double quote
							"\xe2\x80\x9d", // right double quote
							"\xe2\x80\x94", // em dash
							"\xe2\x80\xa6" // elipses
		);
		$fixedwordchars=array("'", "'", '"', '"', '-', '...');
		$str = str_replace($badwordchars, $fixedwordchars, $str);
		return $this->conn->real_escape_string(trim($str));
	}

}

?>
