<?php
//include_once($SERVER_ROOT.'/config/dbconnection.php');
include_once('Manager.php');

class LanguageAdmin extends Manager {

	//private $conn;
	private $langArr = array('en','es','pt');

	function __construct() {
		//$this->conn = MySQLiConnectionFactory::getCon("readonly");
	}

	function __destruct(){
		//if(!($this->conn === false)) $this->conn->close();
	}

	public function getLanguageVariables($refUrl){
		$retArr = array();
		$filePath = parse_url($refUrl, PHP_URL_PATH);
		$filePath = $GLOBALS['SERVER_ROOT'].'/content/lang'.substr($filePath, strlen($GLOBALS['CLIENT_ROOT']));
		$filePath = substr($filePath, 0, strlen($filePath)-4);
		foreach($this->langArr as $langCode){
			$path = $filePath.'.'.$langCode.'.php';
			$handlerUrl = $this->getDomain().$GLOBALS['CLIENT_ROOT'].'/content/lang/admin/varhandler.php?path='.$path;
			if($jsonStr = file_get_contents($handlerUrl)){
				$retArr[$langCode] = json_decode($jsonStr,true);
			}
			else{
				$retArr[$langCode] = array();
			}
		}
		return $retArr;
	}
}
?>