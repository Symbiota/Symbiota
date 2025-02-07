<?php
class MappingUtil  {
	static $DEFAULT_BOUNDARY = [
		['lat' => 42.3, 'lng' => -124.49],
		['lat' => -124.49, 'lng' => -114.69]
	];

	/**
	 * Checks if input is an array containing of lenght 2 that contains
	 * the keys 'lat' and 'lng'
	 * @param mixed $bounds input you wish to validate as proper bounds
	 * @return bool
	 **/
	public static function isValidBounds($bounds): bool {
		if(!is_array($bounds) || count($bounds) != 2) return false;

		foreach($bounds as $coord) {
			if(isset($coord['lat']) || isset($coord['lng'])) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Gets a portals default mapping boundary. 
	 * Older portals may have strings in there config (symbini.php) file 
	 * which means it needs to be parsed. Newer portals may 
	 * use the same format as MappingUtil::$DEFAULT_BOUNDARY
	 *
	 * @return array  
	 **/
	public static function getMappingBoundary() : array {	
		$bounds = $GLOBALS['MAPPING_BOUNDARIES'];
		if(empty($bounds)){
			return self::$DEFAULT_BOUNDARY;
		}

		if(self::isValidBounds($bounds)) {
			return $bounds;
		}

		$coorArr = explode(';', $bounds);

		if(!is_array($coorArr) || count($coorArr) != 4) {
			return self::$DEFAULT_BOUNDARY;
		}

		//$latCen = ($boundLatMax + $boundLatMin)/2;
		//$longCen = ($boundLngMax + $boundLngMin)/2;

		return [
			['lat' => $coorArr[0], 'lat' => $coorArr[1]],
			['lat' => $coorArr[2], 'lat' => $coorArr[3]]
		];
	}
}
