<?php
class MappingUtil  {
	// This is [lat, lng] format
	static $DEFAULT_BOUNDARY = [
		['lat' => 62.8, 'lng' => -132.9],
		['lat' => -35.7, 'lng' => 142.6]
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
			if(!isset($coord['lat']) || !isset($coord['lng'])) {
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

		// This is [lat, lng]
		return [
			['lat' => floatval($coorArr[0]), 'lng' => floatval($coorArr[1])],
			['lat' => floatval($coorArr[2]), 'lng' => floatval($coorArr[3])]
		];
	}

	public static function getBoundsCentroid($bounds) : array {	
		$lat = ($bounds[0]['lat'] > $bounds[1]['lat']? 
			$bounds[0]['lat'] - $bounds[1]['lat']:
			$bounds[1]['lat'] - $bounds[0]['lat']) / 2;

		$lng = ($bounds[0]['lng'] > $bounds[1]['lng']? 
			$bounds[0]['lng'] - $bounds[1]['lng']:
			$bounds[1]['lng'] - $bounds[0]['lng']) / 2;

		return ['lat' => $lat, 'lng' => $lng];
	}
}
