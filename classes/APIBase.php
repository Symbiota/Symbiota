<?php
require_once($SERVER_ROOT.'/classes/Manager.php');

class APIBase extends Manager{

	function __construct($id=null,$conType=null) {
		parent::__construct($id,$conType);
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

	protected function logOrEcho($str, $indexLevel=0, $tag = 'li'){
		if(!$this->logFH) $this->setLogFH('../content/logs/occurImport/occurrenceWriter_'.date('Y-m-d').'.log');
		parent::logOrEcho($str, $indexLevel, $tag);
	}
}
?>