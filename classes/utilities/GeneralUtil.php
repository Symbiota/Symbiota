<?php
/**
 *  Base static fucntions that are regularly used across all code.
 */

class GeneralUtil {

	public static function getDomain(){
		$domain = 'http://';
		if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) $domain = 'https://';
		if(!empty($GLOBALS['SERVER_HOST'])){
			if(substr($GLOBALS['SERVER_HOST'], 0, 4) == 'http') $domain = $GLOBALS['SERVER_HOST'];
			else $domain .= $GLOBALS['SERVER_HOST'];
		}
		else $domain .= $_SERVER['SERVER_NAME'];
		if($_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443 && !strpos($domain, ':'.$_SERVER['SERVER_PORT'])){
			$domain .= ':'.$_SERVER['SERVER_PORT'];
		}
		$domain = filter_var($domain, FILTER_SANITIZE_URL);
		return $domain;
	}

	public static function getRightsHtml($inputStr){
		$rightsOutput = '';
		if($inputStr){
			$inputStr = htmlspecialchars($inputStr, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
			//Use input as default value
			$rightsOutput = $inputStr;
			//Replace with badget if input as a creative commons URL
			$path = 'https://mirrors.creativecommons.org/presskit/buttons/88x31/png/';
			$ccBadgeArr = array(
				'/by-nc/' => 'by-nd.png',
				'/by-sa/' => 'by-sa.png',
				'/by-nc-sa/' => 'by-nc-sa.png',
				'/by/' => 'by.png',
				'/zero/' => 'cc-zero.png'
			);
			foreach($ccBadgeArr as $fragment => $fileName){
				if(strpos($inputStr, $fragment)){
					$rightsOutput = '<img src="' . $path . $fileName . '">';
				}
			}
			//If input is a URL, make output a clickable link
			if(substr($inputStr, 0, 4) == 'http'){
				$rightsOutput = '<a href="' . $inputStr . '" target="_blank">' . $rightsOutput . '</a>';
			}
		}
		$rightsOutput = '<span class="rights-span">' . $rightsOutput . '</span>';
		return $rightsOutput;
	}
}
