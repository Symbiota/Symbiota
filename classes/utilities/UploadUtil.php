<?php
include_once($SERVER_ROOT . "/classes/Media.php");
include_once($SERVER_ROOT . "/classes/MediaException.php");

class UploadUtil {

	const ALLOWED_LOAN_MIMES = [
		'text/plain',
		'image/jpeg', 'image/png',
		'application/pdf', 'application/msword',
		'application/vnd.ms-excel',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
	];

	const ALLOWED_IMAGE_MIMES = [
		'image/jpeg', 'image/png', 'image/gif'
	];

	const ALLOWED_AUDIO_MIMES = [
		'audio/mpeg', 'audio/wav', 'audio/ogg'
	];

	const ALLOWED_ZIP_MIMES = [
		'application/x-zip',
		'application/zip',
		'application/x-zip-compressed',
		'application/s-compressed',
		'multipart/x-zip',
		'text/csv',
		'text/x-comma-separated-values',
		'text/comma-separated-values',
		'application/vnd.msexcel',
	];

	/**
	 * Gets temporary file storage path for portal.
	 *
	 * Gets a directory path like "/some/path/" with slashs at start and finish.
	 * This function Should be used when doing processing across multiple calls.
	 * And should never point to a web root accessible path
	 *
	 * @return string
	 **/
	public static function getTempDir(): string {
		$temp_dir = $GLOBALS['TEMP_DIR_ROOT'] ?? ini_get('upload_tmp_dir') ;
		if(!$temp_dir) return false;
		if(substr($temp_dir,-1) != '/') {
			$temp_dir .= "/";
		}

		return $temp_dir;
	}

	/**
	 * Cross checks file type and extension to make sure they match.
	 * Also ensures file's mimetype matches $allowed_mimes which
	 * defaults to self::ALLOWED_IMAGE_MIMES
	 *
	 * @param array $uploaded_file Uses $_FILES like array
	 * @param array $allowed_mimes Description
	 * @return type
	 * @throws conditon
	 **/
	public static function checkFileUpload(array $uploaded_file, array $allowed_mimes = []): bool {
		if(count($allowed_mimes) <= 0) {
			$allowed_mimes = self::ALLOWED_IMAGE_MIMES;
		}

		if(!in_array($uploaded_file['type'], $allowed_mimes)) {
			throw new MediaException(MediaException::FileTypeNotAllowed, ' ' . $uploaded_file['type']);
		}

		$type_guess = mime_content_type($uploaded_file['tmp_name']);

		if($type_guess != $uploaded_file['type']) {
			throw new MediaException(MediaException::SuspiciousFile);
		}

		$guess_ext = self::mime2ext($type_guess);
		$provided_file_data = pathinfo($uploaded_file['name']);

		if(!$guess_ext || strtolower($guess_ext) != strtolower($provided_file_data['extension'])) {
			throw new MediaException(MediaException::SuspiciousFile);
		}

		return true;
	}

	/**
	  * This function returns the maximum files size that can be uploaded
	  * in PHP
	  *
	  * Reads ini variables post_max_size and upload_max_filesize and uses
	  * the minimum of the two.
	  *
	  * @returns int File size in bytes
	  **/
	public static function getMaximumFileUploadSize(): int {
		return min(
			self::size2Bytes(ini_get('post_max_size')),
			self::size2Bytes(ini_get('upload_max_filesize')),
		);
	}

	/**
	 * undocumented function summary
	 *
	 * Undocumented function long description
	 *
	 * @param Type $var Description
	 * @return type
	 * @throws conditon
	 **/
	public static function size2Bytes(string $size):int {
		// Remove the non-unit characters from the size.
		$unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
		// Remove the non-numeric characters from the size.
		$size = preg_replace('/[^0-9\.]/', '', $size);
		if ($unit) {
			// Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
			return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
		}
		else {
			return round($size);
		}
	}

	/**
	 * Utility function to parse out useful information when uploading and processing files.
	 *
	 * Wrapper to use parse_url, pathinfo, and parse_str in order to have a stable
	 * and consistent filepath / url parsing utility.
	 *
	 * @phpstan-type DecomposedUrl array{
	 *		scheme?: string
	 *		host?: string
	 *		port?: int
	 *		path?: string
	 *		basename?: string
	 *		extension?: string
	 *		dirname?: string
	 *		filename?: string
	 *		query?: string
	 *		query_parts?: { key_name: string, ...}
	 *	}
	 *
	 * @param string $url Can be a url or pathname or file name.
	 * @return Bool | DecomposedUrl
	 **/
	public static function decomposeUrl(string $url) {
		$parts = parse_url($url);

		if (!is_array($parts)) return false;

		# Pull out host information
		if (array_key_exists('host', $parts) && strrpos($parts['host'], '.') !== false) {
			$parts['tld'] = end(explode('.', $parts['host']));
		}

		# Parse path information
		if (array_key_exists('path', $parts)) {
			$parts = array_merge($parts, pathinfo($parts['path']));
		}

		# Parse out query
		if (array_key_exists('query', $parts)) {
			parse_str($parts['query'], $parts['query_parts']);
		}
		return $parts;
	}

	/**
	 * Checks if remote file provided by $url matches $allowed_mimes then
	 * proceeds to download into a temporary folder
	 *
	 * Be sure to check file after upload.
	 *
	 * @param string $url Url path desired to download
	 * @return array
	 * @throws MediaException
	 * @throws Exception
	 **/
	public static function downloadFromRemote(string $url, array $allowed_mimes	= []): array {
		$info = self::getRemoteFileInfo($url);

		if(count($allowed_mimes) <= 0) {
			$allowed_mimes = self::ALLOWED_IMAGE_MIMES;
		}

		// Sanity Check files extension and claimed type before uploading
		// This does not guarantee the file is safe but weeds out simple
		// malicous file upload attempts. Actual file should also be checked after download.
		if(!in_array($info['type'], $allowed_mimes)) {
			throw new MediaException(MediaException::FileTypeNotAllowed, ' ' . $info['type']);
		} else if(self::mime2ext($info['type']) !== $info['extension']) {
			throw new MediaException(MediaException::SuspiciousFile);
		}

		$availableMemory = self::getMaximumFileUploadSize() - memory_get_usage();
		if($availableMemory < intval($info['size'])) {
			throw new Exception('Error: File is to large to upload');
		}

		$tempPath = self::getTempDir() . $info['name'];

		file_put_contents(
			$tempPath,
			fopen($url, 'r')
		);

		$info['tmp_name'] = $tempPath;

		return $info;
	}

	/*
	 * Curls url for header information and returns a $_FILES like file array
	 * @param string $url
	 * return array | bool
	 */
	public static function getRemoteFileInfo(string $url) {
		if(!function_exists('curl_init')) throw new Exception('Curl is not installed');
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);

		$data = curl_exec($ch);

		//If there is no data then throw error
		if($data === false && $errno = curl_errno($ch)) {
			$message = curl_strerror($errno);
			curl_close($ch);
			throw new Exception($message);
		}

		$retCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if($retCode >= 400) {
			error_log(
				'Error Status ' . $retCode . ' in getRemoteFileInfo LINE:' . __LINE__ .
				' URL:' . $url
			);
			return false;
		}

		$file_size_bytes = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
		$file_type_mime = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

		curl_close($ch);

		// Check response header for a provided filename in "Content-Disposition"
		$headers = explode("\r\n", $data);
		$response_file_name = '';
		if ($found = preg_grep('/^Content-Disposition\: /', $headers)) {
			preg_match("/.* filename[*]?=(?:utf-8[']{2})?(.*)/",current($found),$matches);
			if (!empty($matches)){
				$response_file_name = urldecode($matches[1]);
			}
		}

		// Use filename sent in response header.  Otherwise fallback to contents of URL.
		if(!empty($response_file_name)){
			$parsed_file = Media::parseFileName($response_file_name);
		}
		else {
			$parsed_file = Media::parseFileName($url);
		}

		$parsed_file['name'] = Media::cleanFileName($parsed_file['name']);

		if(!$parsed_file['extension'] && $file_type_mime) {
			$parsed_file['extension'] = self::mime2ext($file_type_mime);
		}

		return [
			'name' => $parsed_file['name'] . ($parsed_file['extension'] ? '.' .$parsed_file['extension']: ''),
			'tmp_name' => $url,
			'extension' => $parsed_file['extension'],
			'error' => 0,
			'type' => $file_type_mime,
			'size' => intval($file_size_bytes)
		];
	}

	/**
	 * @param string $mime
	 * @return string | bool
	 */
	public static function mime2ext(string $mime) {
		$mime_map = [
			'video/3gpp2' => '3g2',
			'video/3gp'=> '3gp',
			'video/3gpp'=> '3gp',
			'application/x-compressed'=> '7zip',
			'audio/x-acc'=> 'aac',
			'audio/ac3'=> 'ac3',
			'application/postscript' => 'ai',
			'audio/x-aiff' => 'aif',
			'audio/aiff' => 'aif',
			'audio/x-au' => 'au',
			'video/x-msvideo' => 'avi',
			'video/msvideo' => 'avi',
			'video/avi' => 'avi',
			'application/x-troff-msvideo' => 'avi',
			'application/macbinary' => 'bin',
			'application/mac-binary' => 'bin',
			'application/x-binary' => 'bin',
			'application/x-macbinary' => 'bin',
			'image/bmp' => 'bmp',
			'image/x-bmp' => 'bmp',
			'image/x-bitmap' => 'bmp',
			'image/x-xbitmap' => 'bmp',
			'image/x-win-bitmap' => 'bmp',
			'image/x-windows-bmp' => 'bmp',
			'image/ms-bmp' => 'bmp',
			'image/x-ms-bmp' => 'bmp',
			'application/bmp' => 'bmp',
			'application/x-bmp' => 'bmp',
			'application/x-win-bitmap' => 'bmp',
			'application/cdr' => 'cdr',
			'application/coreldraw' => 'cdr',
			'application/x-cdr' => 'cdr',
			'application/x-coreldraw' => 'cdr',
			'image/cdr' => 'cdr',
			'image/x-cdr' => 'cdr',
			'zz-application/zz-winassoc-cdr' => 'cdr',
			'application/mac-compactpro' => 'cpt',
			'application/pkix-crl' => 'crl',
			'application/pkcs-crl' => 'crl',
			'application/x-x509-ca-cert' => 'crt',
			'application/pkix-cert' => 'crt',
			'text/css' => 'css',
			'text/csv' => 'csv',
			'text/x-comma-separated-values' => 'csv',
			'text/comma-separated-values' => 'csv',
			'application/vnd.msexcel' => 'csv',
			'application/x-director' => 'dcr',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
			'application/x-dvi' => 'dvi',
			'message/rfc822' => 'eml',
			'application/x-msdownload' => 'exe',
			'video/x-f4v' => 'f4v',
			'audio/x-flac' => 'flac',
			'video/x-flv' => 'flv',
			'image/gif' => 'gif',
			'application/gpg-keys' => 'gpg',
			'application/x-gtar' => 'gtar',
			'application/x-gzip' => 'gzip',
			'application/mac-binhex40' => 'hqx',
			'application/mac-binhex' => 'hqx',
			'application/x-binhex40' => 'hqx',
			'application/x-mac-binhex40' => 'hqx',
			'text/html' => 'html',
			'image/x-icon' => 'ico',
			'image/x-ico' => 'ico',
			'image/vnd.microsoft.icon' => 'ico',
			'text/calendar' => 'ics',
			'application/java-archive' => 'jar',
			'application/x-java-application' => 'jar',
			'application/x-jar' => 'jar',
			'image/jp2' => 'jp2',
			'video/mj2' => 'jp2',
			'image/jpx' => 'jp2',
			'image/jpm' => 'jp2',
			'image/jpeg' => 'jpg',
			'image/pjpeg' => 'jpg',
			'application/x-javascript' => 'js',
			'application/json' => 'json',
			'text/json' => 'json',
			'application/vnd.google-earth.kml+xml' => 'kml',
			'application/vnd.google-earth.kmz' => 'kmz',
			'text/x-log' => 'log',
			'audio/x-m4a' => 'm4a',
			'audio/mp4' => 'm4a',
			'application/vnd.mpegurl' => 'm4u',
			'audio/midi' => 'mid',
			'application/vnd.mif' => 'mif',
			'video/quicktime' => 'mov',
			'video/x-sgi-movie' => 'movie',
			'audio/mpeg' => 'mp3',
			'audio/mpg' => 'mp3',
			'audio/mpeg3' => 'mp3',
			'audio/mp3' => 'mp3',
			'video/mp4' => 'mp4',
			'video/mpeg' => 'mpeg',
			'application/oda' => 'oda',
			'audio/ogg' => 'ogg',
			'video/ogg' => 'ogg',
			'application/ogg' => 'ogg',
			'font/otf' => 'otf',
			'application/x-pkcs10' => 'p10',
			'application/pkcs10' => 'p10',
			'application/x-pkcs12' => 'p12',
			'application/x-pkcs7-signature' => 'p7a',
			'application/pkcs7-mime' => 'p7c',
			'application/x-pkcs7-mime' => 'p7c',
			'application/x-pkcs7-certreqresp' => 'p7r',
			'application/pkcs7-signature' => 'p7s',
			'application/pdf' => 'pdf',
			'application/octet-stream' => 'pdf',
			'application/x-x509-user-cert' => 'pem',
			'application/x-pem-file' => 'pem',
			'application/pgp' => 'pgp',
			'application/x-httpd-php' => 'php',
			'application/php' => 'php',
			'application/x-php' => 'php',
			'text/php' => 'php',
			'text/x-php' => 'php',
			'application/x-httpd-php-source' => 'php',
			'image/png' => 'png',
			'image/x-png' => 'png',
			'application/powerpoint' => 'ppt',
			'application/vnd.ms-powerpoint' => 'ppt',
			'application/vnd.ms-office' => 'ppt',
			'application/msword' => 'doc',
			'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
			'application/x-photoshop' => 'psd',
			'image/vnd.adobe.photoshop' => 'psd',
			'audio/x-realaudio' => 'ra',
			'audio/x-pn-realaudio' => 'ram',
			'application/x-rar' => 'rar',
			'application/rar' => 'rar',
			'application/x-rar-compressed' => 'rar',
			'audio/x-pn-realaudio-plugin' => 'rpm',
			'application/x-pkcs7' => 'rsa',
			'text/rtf' => 'rtf',
			'text/richtext' => 'rtx',
			'video/vnd.rn-realvideo' => 'rv',
			'application/x-stuffit' => 'sit',
			'application/smil' => 'smil',
			'text/srt' => 'srt',
			'image/svg+xml' => 'svg',
			'application/x-shockwave-flash' => 'swf',
			'application/x-tar' => 'tar',
			'application/x-gzip-compressed' => 'tgz',
			'image/tiff' => 'tiff',
			'font/ttf' => 'ttf',
			'text/plain' => 'txt',
			'text/x-vcard' => 'vcf',
			'application/videolan' => 'vlc',
			'text/vtt' => 'vtt',
			'audio/x-wav' => 'wav',
			'audio/wave' => 'wav',
			'audio/wav' => 'wav',
			'application/wbxml' => 'wbxml',
			'video/webm' => 'webm',
			'image/webp' => 'webp',
			'audio/x-ms-wma' => 'wma',
			'application/wmlc' => 'wmlc',
			'video/x-ms-wmv' => 'wmv',
			'video/x-ms-asf' => 'wmv',
			'font/woff' => 'woff',
			'font/woff2' => 'woff2',
			'application/xhtml+xml' => 'xhtml',
			'application/excel' => 'xl',
			'application/msexcel' => 'xls',
			'application/x-msexcel' => 'xls',
			'application/x-ms-excel' => 'xls',
			'application/x-excel' => 'xls',
			'application/x-dos_ms_excel' => 'xls',
			'application/xls' => 'xls',
			'application/x-xls' => 'xls',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
			'application/vnd.ms-excel' => 'xlsx',
			'application/xml' => 'xml',
			'text/xml' => 'xml',
			'text/xsl' => 'xsl',
			'application/xspf+xml' => 'xspf',
			'application/x-compress' => 'z',
			'application/x-zip' => 'zip',
			'application/zip' => 'zip',
			'application/x-zip-compressed' => 'zip',
			'application/s-compressed' => 'zip',
			'multipart/x-zip' => 'zip',
			'text/x-scriptzsh' => 'zsh',
		];

		return isset($mime_map[$mime]) ? $mime_map[$mime] : false;
	}
}
