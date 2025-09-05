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
			include_once($path);
		}
	}
}
