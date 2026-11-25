<?php
/*This is
 * This is a static class used to make santizing variables "in" to the databse and "out" to the client webpage easy to use, compact, and secure
 */
class Sanitize {

	public static function out(Mixed $val): Mixed {
		if(is_array($val)) {
			return array_map(fn ($v) => self::out($v), $val);
		} else if (is_numeric($val)) {
			$intCast = intval($val);
			$floatCast = floatval($val);

			return $intCast == $floatCast? $intCast: $floatCast;
		} else if(is_bool($val) || is_string($val)) {
			return htmlspecialchars($val, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
		} else {
			return null;
		}
	}

	public static function int(Mixed $val): int|string{
		if(!$val) return '';
		return filter_var($val, FILTER_SANITIZE_NUMBER_INT);
	}

	public static function float(Mixed $val): float {
		if(is_numeric($val)) return $val;
	}

	public static function outString(Mixed $val): string {
		if(!is_string($val) && !is_numeric($val) && !is_bool($val)) return '';
		return htmlspecialchars($val, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
	}
}
?>
