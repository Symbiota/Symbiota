<?php
require_once($SERVER_ROOT.'/classes/Manager.php');

class APIBase extends Manager{

	function __construct($id=null,$conType=null) {
		parent::__construct($id,$conType);
		//Need to change this so that the log only creates the log file is an error is thrown
		//$this->setLogFH('../content/logs/occurrenceWriter/occurrenceWriter_'.date('Y-m-d').'.log');
	}

	function __destruct(){
		parent::__destruct();
	}

	public function validateSecurityKey($k){
		if(isset($GLOBALS['SECURITY_KEY'])){
			if($k == $GLOBALS['SECURITY_KEY']){
				return true;
			}
			else{
				$this->errorMessage = 'Security Key authentication failed';
				return false;
			}
		}
		else{
			return true;
		}
		return true;
	}
}
?>