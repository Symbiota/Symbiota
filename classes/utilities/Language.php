<?php

class Language {
	CONST BASE_LANG_PATH = '/content/lang/';

	/**
	 * Loads language files stored at $SERVER_ROOT/content/lang of
	 * the corresponding $LANG_TAG. Uses english as a backup.
	 *
	 * @param string|array $path Filepath to load language files for. Exclude .php extension
	 * @return void
	 **/
	static function load(mixed $path): void {
		if(is_array($path)) {
			foreach($path as $p) {
				self::load_path($p);
			}
		} else {
			self::load_path($path);
		}
	}

	private static function load_path(string $path): void {
		global $SERVER_ROOT, $LANG_TAG, $LANG;
		$path = $SERVER_ROOT . self::BASE_LANG_PATH . $path . '.' . ($LANG_TAG ?? 'en') . '.php';
		if(file_exists($path)) {
			if(empty($LANG)){
				include_once($path);
			}
			else{
				self::merge_lang($path);
			}
			$override_path = $SERVER_ROOT . self::BASE_LANG_PATH . $path . '.' . ($LANG_TAG ?? 'en') .'override.php';
			if(file_exists($override_path)){
				self::merge_lang($override_path);
			}
			
		}
	}

	private static function merge_lang(string $path): void {
		global $LANG;
		$temp_lang = $LANG;
		include_once($path);
		$LANG = array_merge($temp_lang, $LANG);
		unset($temp_lang);
	}

}
