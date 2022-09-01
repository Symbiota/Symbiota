<?php
/**
 *  Base static fucntions that are regularly used across all code.
 */

class UtilityFunctions {

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

}
