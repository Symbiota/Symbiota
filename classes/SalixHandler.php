<?php
include_once($SERVER_ROOT.'/classes/SpecProcNlpSalix.php');

class SalixHandler{

	private $returnFormat = 'json';
	private $charset = 'utf-8';

	private $instanceTS;
	private $timeout = 30;
	private $logHandler;
	private $verbose = false;
	private $errorStr;

	function __construct() {
		$this->instanceTS = time();
	}

	function __destruct(){
		$this->logMsg('Instance closed ('.date('Y-m-d H:i:s').')');
		if($this->logHandler) fclose($this->logHandler);
	}

	public function parse($ocrInput){
		$ocrInput = $this->cleanOcrInput($ocrInput);
		if(!$ocrInput){
			$this->errorStr = 'FATAL ERROR: Input string is null';
			$this->logMsg($this->errorStr);
			return false;
		}
		$this->logMsg("Parsing OCR:\n".$ocrInput);

		//Parse
		$salixManager = new SpecProcNlpSalix();
		$dwcArr = $salixManager->parse($ocrInput);
		if(!$dwcArr){
			$this->errorStr = 'NOTICE: Parser failed to return any data';
			$this->logMsg($this->errorStr);
			return false;
		}
		$dwcArr = $this->cleanDwcArr($dwcArr);
		if(!$dwcArr){
			$this->errorStr = 'NOTICE: Parsed data empty after cleaning';
			$this->logMsg($this->errorStr);
			return false;
		}

		//Format return
		$retStr = '';
		if($this->returnFormat == 'json'){
			$retStr = json_encode($dwcArr);
		}
		elseif($this->returnFormat == 'xml'){
			$root = '<?xml version="1.0" encoding="'.strtoupper($this->charset).'"?><DwcRecordSet xmlns="http://rs.tdwg.org/dwc/xsd/simpledarwincore/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://rs.tdwg.org/dwc/xsd/simpledarwincore/ http://rs.tdwg.org/dwc/xsd/tdwg_dwc_simple.xsd"></DwcRecordSet>';
			$xml = new SimpleXMLElement($root);
			$xmlRec = $xml->addChild('SimpleDarwinRecord','');
			foreach($dwcArr as $k => $v){
				$xmlRec->addChild($k,$v);
			}
			$retStr = $xml->asXML();
		}
		elseif($this->returnFormat == 'html'){
			foreach($dwcArr as $k => $v){
				$retStr .= '<b>'.$k.'</b>: '.$v.'<br/>';
			}
		}
		else{
			$retStr = implode(',',$dwcArr);
		}
		$this->logMsg($retStr);

		return $retStr;
	}

	public function setCharset($cs){
		$this->charset = strtolower($cs);
	}

	public function setReturnFormat($rFormat){
		$this->returnFormat = strtolower($rFormat);
	}

	public function getErrorStr(){
		return $errorStr;
	}

	public function setVerbose($v){
		$this->verbose = $v;
		if($v){
			//Create log handler
			if($this->logHandler = fopen('../content/logs/salix_webservice_'.date('Ymd').'.log', 'a')) {
				$this->logMsg('New instance created ('.date('Y-m-d H:i:s').')');
			}
		}
	}

	//Misc functions
	private function logMsg($msg) {
		if($this->verbose){
			fwrite($this->logHandler, $this->instanceTS.': '.$msg."\n");
		}
	}

	private function cleanOcrInput($ocrInput){
		$retStr = trim($ocrInput);
		//Get rid of UTF-8 curly smart quotes and dashes
		$badwordchars=array("\xe2\x80\x98", // left single quote
							"\xe2\x80\x99", // right single quote
							"\xe2\x80\x9c", // left double quote
							"\xe2\x80\x9d", // right double quote
							"\xe2\x80\x94", // em dash
							"\xe2\x80\xa6" // elipses
		);
		$fixedwordchars=array("'", "'", '"', '"', '-', '...');
		$retStr = str_replace($badwordchars, $fixedwordchars, $retStr);
		return $retStr;
	}

	private function cleanDwcArr($dwcArr){
		//Do some cleaning and standardization
		if($dwcArr && is_array($dwcArr)){
			$dwcArr = array_change_key_case($dwcArr);
			if(array_key_exists('scientificname',$dwcArr) && !array_key_exists('sciname',$dwcArr)){
				$dwcArr['sciname'] = $dwcArr['scientificname'];
				unset($dwcArr['scientificname']);
			}
			//Convert to UTF-8
			foreach($dwcArr as $k => $v){
				if($v){
					//If is a latin character set, convert to UTF-8
					if(mb_detect_encoding($v,'UTF-8,ISO-8859-1',true) == 'ISO-8859-1'){
						$dwcArr[$k] = mb_convert_encoding($v, 'UTF-8');
				}
				else{
					unset($dwcArr[$k]);
				}
			}
		}
		return $dwcArr;
	}
}
?>