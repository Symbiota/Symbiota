<?php
include_once($SERVER_ROOT . "/classes/Database.php");
include_once($SERVER_ROOT . "/classes/Sanitize.php");
include_once($SERVER_ROOT . "/classes/StorageStrategy.php");
include_once($SERVER_ROOT . "/classes/MediaType.php");
include_once($SERVER_ROOT . "/classes/MediaException.php");
include_once($SERVER_ROOT . '/classes/utilities/QueryUtil.php');
include_once($SERVER_ROOT . '/classes/utilities/OccurrenceUtil.php');
include_once($SERVER_ROOT . '/classes/utilities/UploadUtil.php');

if(file_exists($SERVER_ROOT.'/content/lang/classes/Media.'.$LANG_TAG.'.php')) {
	include_once($SERVER_ROOT.'/content/lang/classes/Media.'.$LANG_TAG.'.php');
} else {
	include_once($SERVER_ROOT.'/content/lang/classes/Media.en.php');
}

function get_occurrence_upload_path($institutioncode, $collectioncode, $catalognumber = null) {
		$root = $institutioncode . ($collectioncode? '_'. $collectioncode: '') . '/';

		if($catalognumber) {
			//Clean out Symbols that would interfere with
			$derived_cat_num = str_replace(array('/','\\',' '), '', $catalognumber);

			//Grab any characters in the range of 0-8 then any amount digits
			if(preg_match('/^(\D{0,8}\d{4,})/', $derived_cat_num, $matches)){
				//Truncate cat number to keep directories from getting out of hand
				$derived_cat_num = substr($matches[1], 0, -3);

				//If derived catalog number is a number less then five pad front with 0's
				if(is_numeric($derived_cat_num) && strlen($derived_cat_num) < 5) {
					$derived_cat_num = str_pad($derived_cat_num, 5, "0", STR_PAD_LEFT);
				}

				$root .= $derived_cat_num . '/';
			//backup catalogNumber
			} else {
				$root .= '00000/';
			}
		//Use date as a backup so that main directory doesn't get filled up but can debug
		} else {
			$root .= date('Ym') . '/';
		}

		return $root;
}

class Media {
	private static $mediaRootPath;
	private static $mediaRootUrl;

	private static $errors = [];
	private static $storage_driver = LocalStorage::class;

	private const DEFAULT_THUMBNAIL_WIDTH_PX = 200;
	private const DEFAULT_WEB_WIDTH_PX = 1600;
	private const DEFAULT_LARGE_WIDTH_PX = 3168;
	private const WEB_FILE_SIZE_LIMIT = 300000;
	private const DEFAULT_JPG_COMPRESSION = 70;
	private const DEFAULT_TEST_ORIENTATION = false;

	private const DEFAULT_GEN_LARGE_IMG = true;
	private const DEFAULT_GEN_WEB_IMG = true;
	private const DEFAULT_GEN_THUMBNAIL_IMG = true;

	// Used to maintain the same select between getMedia and fetchOccurrenceMedia
	const MEDIA_ITEM_SELECT_SCHEMA = [
		'm.mediaID',
		'm.url',
		'm.originalUrl',
		'm.thumbnailUrl',
		'm.sourceUrl',
		'm.mediaType',
		'm.format',
		'm.occid',
		'm.tid',
		'm.caption',
		'm.locality',
		'm.notes',
		'm.creatorUid',
		'm.creator',
		'm.username',
		'm.owner',
		'm.copyright',
		'm.rights',
		'm.sortSequence',
		'm.sortOccurrence',
		"IFNULL(m.creator,CONCAT_WS(' ',u.firstname,u.lastname)) AS creatorDisplay",
		't.sciname',
		't.author',
		't.rankid'
	];

	public static function setStorageDriver(StorageStrategy $storage_driver): void {
		$this->storage_driver = $storage_driver::class;
	}

	private static function getMediaRootPath(): string {
		if(self::$mediaRootPath) {
			return self::$mediaRootPath;
		}else if(substr($GLOBALS['MEDIA_ROOT_PATH'],-1) != "/") {
			return self::$mediaRootPath = $GLOBALS['MEDIA_ROOT_PATH'] . '/';
		} else {
			return self::$mediaRootPath = $GLOBALS['MEDIA_ROOT_PATH'];
		}
	}

	private static function getMediaRootUrl(): string {
		if(self::$mediaRootUrl) {
			return self::$mediaRootUrl;
		}else if(substr($GLOBALS['MEDIA_ROOT_URL'],-1) != "/") {
			return self::$mediaRootUrl = $GLOBALS['MEDIA_ROOT_URL'] . '/';
		} else {
			return self::$mediaRootUrl = $GLOBALS['MEDIA_ROOT_URL'];
		}
	}

	/**
	 * Pulls file name out of directory path or url
	 *
	 * Note: The url parsing expects the filename to not be in the query or hash
	 *
	 * @param string $filepath Can be a file or url path
	 * return array<string,mixed>
	 * @return array<string,mixed>
	 */
	public static function parseFileName(string $filepath): array {
		$file_name = $filepath;

		//Filepath maybe a url so clear out url query if it exists
		$query_pos = strpos($file_name,'?');
		if($query_pos) $file_name = substr($file_name, 0, $query_pos);

		$file_parts = pathinfo($file_name);

		return [
			'name' => $file_parts['filename'],
			'tmp_name' => $filepath,
			'extension' => (!empty($file_parts['extension'])) ? strtolower($file_parts['extension']) : ''
		];
	}
	/**
	 * @return string
	 * @param array<int,mixed> $media_arr
	 * @param mixed $thumbnail
	 */
	public static function render_media_item(array $media_arr, $thumbnail=false) {
		if($media_arr['mediaType'] == MediaType::Audio && !$thumbnail) {
			$src = $media_arr['url'];
			$format = $media_arr['format'];
			$html = <<< HTML
			<audio controls>
				<source src="$src" type="$format"/>
				Your browser does not support the audio element.
			</audio>
			HTML;

			return $html;
		} else if($media_arr['mediaType'] == MediaType::Image || ($media_arr['tnurl']?? $media_arr['thumbnailUrl'])) {
			$thumbnail = $media_arr['tnurl']?? $media_arr['thumbnailUrl'];
			$url = $media_arr['url'];
			$caption = $media_arr['caption'];
			if(!$thumbnail && $url) {
				$thumbnail = $url;
			} else if(!$thumbnail &&  $media_arr['originalUrl']) {
				$thumbnail = $media_arr['originalUrl'];
			}
			$nav_url = $media_arr['url'] ?? $media_arr['originalUrl'];

			$html = <<< HTML
			<a target="_blank" href="$nav_url">
			<img 
				style="max-width: 200px"
				border="1" 
				src="$thumbnail" 
				title="$caption" 
				alt="Thumbnail image of current specimen" 
			/>
			</a>
			HTML;

			return $html;
		} else {
			global $LANG;
			return '<div style="width: 200px; height:242px; border: solid black 1px; display: flex; align-items: center; justify-content:center">' . $LANG['UNKNOWN_MEDIA_TYPE_MSG'] . '
			</div>';
		}
	}
	/**
	 * @param mixed $url
	 * @param mixed $text
	 */
	static function render_media_link($url, $text) {
		$slash_route = substr($url, 0, 1) == '/';
		if(array_key_exists('MEDIA_DOMAIN',$GLOBALS) && $slash_route) {
			$url = $GLOBALS['MEDIA_DOMAIN'] . $url;
		}
		$clean_url = htmlspecialchars($url, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
		$clean_text = htmlspecialchars($text, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);

		return '<a href="' . $clean_url . '">'. $clean_text . '</a>';
	}

	/**
	 * @param mixed $mime
	 */
	public static function getAllowedMime($mime) {
		// Fall back if ALLOWED_MEDIA_MIME_TYPES is not present
		if(!isset($GLOBALS['ALLOWED_MEDIA_MIME_TYPES'])) {
			return is_array($mime) && count($mime) > 0? $mime[0]: $mime;
		} else if(is_array($mime)) {
			foreach($mime as $type) {
				if(in_array($type, $GLOBALS['ALLOWED_MEDIA_MIME_TYPES'])) {
					return $type;
				}
			}
		} else {
			if(in_array($mime, $GLOBALS['ALLOWED_MEDIA_MIME_TYPES'])) {
				return $mime;
			}
		}

		return false;
	}

	/**
	 * @param string $ext
	 * @return string | bool
	 */
	public static function ext2Mime(string $ext, string $type = '') {
		$image = [
			'bmp' => ['image/bmp', 'image/x-bmp', 'image/x-bitmap', 'image/x-xbitmap', 'image/x-win-bitmap', 'image/x-windows-bmp', 'image/ms-bmp', 'image/x-ms-bmp'],
			'cdr' => ['image/cdr', 'image/x-cdr'],
			'gif' => 'image/gif',
			'ico' => ['image/x-icon', 'image/x-ico', 'image/vnd.microsoft.icon' ],
			'jpg' => ['image/jpeg', 'image/jpeg', 'image/pjpeg'],
			'jpeg' => ['image/jpeg', 'image/jpeg', 'image/pjpeg'],
			'jp2' => ['image/jp2', 'image/jpx', 'image/jpm'],
			'png' => ['image/png', 'image/x-png'],
			'psd' => 'image/vnd.adobe.photoshop',
			'svg' => 'image/svg+xml',
			'tif' => 'image/tiff',
			'tiff' => 'image/tiff',
			'webp' => 'image/webp'
		];

		$audio = [
			'aac' => 'audio/x-acc',
			'ac3' => 'audio/ac3',
			'aif' => ['audio/x-aiff', 'audio/aiff'],
			'au' => 'audio/x-au',
			'flac' => 'audio/x-flac',
			'm4a' => ['audio/mp4', 'audio/x-m4a'],
			'mp4' => 'audio/mp4',
			'mid' => 'audio/midi',
			'mp3' => [ 'audio/mp3', 'audio/mpeg', 'audio/mpg', 'audio/mpeg3' ],
			'ogg' => 'audio/ogg',
			'ra' => 'audio/x-realaudio',
			'ram' => 'audio/x-pn-realaudio',
			'rpm' => 'audio/x-pn-realaudio-plugin',
			'wav' => ['audio/wav', 'audio/wave', 'audio/x-wav'],
			'wma' => 'audio/x-ms-wma',
		];

		if($type === MediaType::Image) {
			return $image[$ext] ?? false;
		} else if ($type=== MediaType::Audio) {
			return $audio[$ext] ?? false;
		} else {
			$audio_result = $audio[$ext] ?? false;
			$image_result = $image[$ext] ?? false;
			if($audio_result && !$image_result) {
				return $audio_result;
			} else if(!$audio_result && $image_result) {
				return $image_result;
			} else {
				// There was some mime type ambiguity so return false
				return false;
			}
		}
	}

	/**
	 * Strips out undesired characters from from a pure file name string
	 *
	 * @param string $file_name A file name without the extension
	 * return string
	 */
	public static function cleanFileName(string $file_name):string {
		$file_name = str_replace(".","", $file_name);
		$file_name = str_replace(array("%20","%23"," ","__"),"_",$file_name);
		$file_name = str_replace("__","_",$file_name);
		$file_name = str_replace(array(chr(231),chr(232),chr(233),chr(234),chr(260)),"a",$file_name);
		$file_name = str_replace(array(chr(230),chr(236),chr(237),chr(238)),"e",$file_name);
		$file_name = str_replace(array(chr(239),chr(240),chr(241),chr(261)),"i",$file_name);
		$file_name = str_replace(array(chr(247),chr(248),chr(249),chr(262)),"o",$file_name);
		$file_name = str_replace(array(chr(250),chr(251),chr(263)),"u", $file_name);
		$file_name = str_replace(array(chr(264),chr(265)),"n",$file_name);
		$file_name = preg_replace("/[^a-zA-Z0-9\-_]/", "", $file_name);
		$file_name = trim($file_name,' _-');

		if(strlen($file_name) > 30) {
			$file_name = substr($file_name, 0, 30);
		}

		return $file_name;
	}

	private static function isValidFile($file): bool {
		return $file && !empty($file) && isset($file['error']) && $file['error'] === 0;
	}

	/* Internal Function for creating a file array for media that doesn't need to be uploaded. Primarly used for media upload */
	private static function parse_map_only_file(array $clean_post_arr): array {
		// Map only files must have format and a url
		if(!(isset($clean_post_arr['originalUrl']) || isset($clean_post_arr['url']))) {
			return [];
		}

		$url = $clean_post_arr['originalUrl'] ?? $clean_post_arr['url'];
		$file_type_mime = $clean_post_arr['format'] ?? '';
		$media_upload_type = $clean_post_arr['mediaUploadType'] ?? '';

		if($media_upload_type) {
			$media_upload_type = MediaType::tryFrom($media_upload_type);
		}

		$parsed_file = self::parseFileName($url);
		$parsed_file['name'] = self::cleanFileName($parsed_file['name']);

		if(!$parsed_file['extension'] && $file_type_mime) {
			$parsed_file['extension'] = UploadUtil::mime2ext($file_type_mime);
		} else if (!$file_type_mime && $parsed_file['extension']) {
			$file_type_mime = self::ext2Mime($parsed_file['extension'], $media_upload_type);

			// If There is a bunch of potential mime types just assume the first one
			// this is not perfect and could result weird errors for fringe types
			// but for current use case should be an issue. Types are order by most likely.
			if(is_array($file_type_mime) && count($file_type_mime) > 0) {
				$file_type_mime = $file_type_mime[0];
			}
		}

		return [
			'name' => $parsed_file['name'] . ($parsed_file['extension'] ? '.' .$parsed_file['extension']: ''),
			'tmp_name' => $url,
			'error' => 0,
			'type' => $file_type_mime ?? '',
			'size' => null
		];
	}

	/**
     * Function to insert a record into the media table. does not handle file uploads
	 * of any kind, but will check to make sure the originalUrl has an allowed format
	 * for the media table.
	 *
	 * @param array<int,mixed> $post_arr
	 * @param Mysqli|Null $conn Optional field if caller wants to keep connection for transactions
	 * @return void
	**/
	public static function insert(array $post_arr, $conn = null): array {
		if(!$conn) {
			$conn = Database::connect('write');
		}

		$clean_post_arr = Sanitize::in($post_arr);

		//Not Sure if I Need
		$mapLargeImg = !($clean_post_arr['nolgimage']?? true);

		$sql = <<< SQL
		SELECT tidinterpreted 
		FROM omoccurrences 
		WHERE tidinterpreted IS NOT NULL AND occid = ? 
		SQL;

		$taxon_result = QueryUtil::executeQuery(
			$conn,
			$sql,
			[$clean_post_arr['occid']]
		);

		if(!isset($clean_post_arr['tid']) && $row = $taxon_result->fetch_object()) {
			$clean_post_arr['tid'] = $row->tidinterpreted;
		}

		if(!($clean_post_arr['copytoserver'] ?? false) && !($clean_post_arr['format'] ?? false)) {
			$file = self::parse_map_only_file($clean_post_arr);

			if( (!self::isValidFile($file) || !$file['type']) ) {
				$file = UploadUtil::getRemoteFileInfo($clean_post_arr['originalUrl']);
			}

			$clean_post_arr['format'] = $file['type'] ?? null;

			if(!isset($clean_post_arr['sourceIdentifier'])) {
				$clean_post_arr['sourceIdentifier'] = 'filename: ' . $file['name'];
			}
		}

		if(!self::getAllowedMime($clean_post_arr['format'])) {
			throw new MediaException(MediaException::FileTypeNotAllowed, ' ' . $file['type']);
		}

		$keyValuePairs = [
			"tid" => $clean_post_arr["tid"] ?? null,
			"occid" => $clean_post_arr["occid"] ?? null,
			"url" => $clean_post_arr['weburl'] ?? $clean_post_arr['url'] ?? null,
			"thumbnailUrl" => $clean_post_arr["thumbnailUrl"] ?? null,
			// Will get popluated below
			"originalUrl" => $clean_post_arr['originalUrl'],
			"archiveUrl" => $clean_post_arr["archiveUrl"] ?? null,// Only Occurrence import
			// This is a very bad name that refers to source or downloaded url
			"sourceUrl" => $clean_post_arr["sourceUrl"] ?? null,// TPImageEditorManager / Occurrence import
			"referenceUrl" => $clean_post_arr["referenceUrl"] ?? null,// check keys again might not be one,
			"creator" => $clean_post_arr["creator"] ?? null,
			"creatorUid" => OccurrenceUtil::verifyUser($clean_post_arr["creatorUid"] ?? null, $conn),
			"format" =>  $clean_post_arr['format'],
			"caption" => $clean_post_arr["caption"] ?? null,
			"owner" => $clean_post_arr["owner"] ?? null,
			"locality" => $clean_post_arr["locality"] ?? null,
			"anatomy" => $clean_post_arr["anatomy"] ?? null,
			"notes" => $clean_post_arr["notes"] ?? null,
			"username" => Sanitize::in($GLOBALS['USERNAME']),
			// check if its is_numeric?
			"sortOccurrence" => $clean_post_arr['sortOccurrence'] ?? null,
			"sourceIdentifier" => $clean_post_arr['sourceIdentifier'] ?? null,
			"rights" => $clean_post_arr['rights'] ?? null,
			"accessRights" => $clean_post_arr['accessRights'] ?? null,
			"copyright" => $clean_post_arr['copyright'] ?? null,
			"hashFunction" => $clean_post_arr['hashFunction'] ?? null,
			"hashValue" => $clean_post_arr['hashValue'] ?? null,
			"mediaMD5" => $clean_post_arr['mediaMD5'] ?? null,
			"recordID" => $clean_post_arr['recordID'] ?? UuidFactory::getUuidV4(),
			"mediaType" => self::getMediaTypeStrFromMime($clean_post_arr['format']),
		];

		$sort_sequence = $clean_post_arr['sortsequence'] ?? $clean_post_arr['sortSequence'] ?? false;
		$keyValuePairs["sortsequence"] = is_numeric($sort_sequence)? $sort_sequence: 50;

		$keys = implode(",", array_keys($keyValuePairs));
		$parameters = str_repeat('?,', count($keyValuePairs) - 1) . '?';

		$sql = <<< SQL
		INSERT INTO media($keys) VALUES ($parameters)
		SQL;

		$result = QueryUtil::executeQuery($conn, $sql, array_values($keyValuePairs));
		//Insert to other tables as needed like imagetags...

		$media_id = $conn->insert_id;
		self::update_tags($media_id, $clean_post_arr, $conn);

		// Attach created id to metadata
		$keyValuePairs['mediaID'] = $media_id;

		return $keyValuePairs;
	}

	/**
	 * Function to use $_POST data to link uploaded files as media assets to tids or occids
	 * If all you want to do is insert a record see the insert function. This is a wrapper
	 * of that function that also handles file and remote file uploads with their need security checks.
	 *
	 * @param array<int,mixed> $post_arr
	 * @param StorageStrategy $storage Class where and how to save files. If left empty will not store files
	 * @param array $file {name: string, type: string, tmp_name: string, error: int, size: int} Post file data, if none given will assume remote resource
	 * @return bool
	**/
	public static function uploadAndInsert($post_arr, $file, $storage): void {
		$createdFilepaths = [];

		$conn = Database::connect('write');
		mysqli_begin_transaction($conn);

		try {
			if(!self::isValidFile($file) && ($post_arr['copytoserver'] ?? false)) {
				$file = UploadUtil::downloadFromRemote($post_arr['originalUrl'], $GLOBALS['ALLOWED_MEDIA_MIME_TYPES']);
				$createdFilepaths[] = $file['tmp_name'];
			}

			if(self::isValidFile($file)) {
				UploadUtil::checkFileUpload($file, $GLOBALS['ALLOWED_MEDIA_MIME_TYPES']);
				$post_arr['format'] = $file['type'];
				$post_arr['originalUrl'] = $storage->getUrlPath() . $file['name'];

				if(!isset($post_arr['sourceIdentifier'])) {
					$post_arr['sourceIdentifier'] = 'filename: ' . $file['name'];
				}
			}
			
			$media_metadata = self::insert($post_arr, $conn);
			$media_type = MediaType::tryFrom($media_metadata['mediaType']);

			if(self::isValidFile($file)) {
				//Check if file exists
				if($storage->file_exists($file)) {
					//Add mediaID onto end of file name which should be unique within portal
					$file['name'] = self::addToFilename($file['name'], '_' . $media_metadata['mediaID']);

					//Fail case the appended mediaID is taken stops after 10
					$cnt = 1;
					while($storage->file_exists($file) && $cnt < 10) {
						$file['name'] = self::addToFilename($file['name'], '_' . $cnt);
						$cnt++;
					}
					$updated_path = $storage->getUrlPath() . $file['name'];

					//Update source url to reflect new filename
					self::update_metadata([
						'url' => $updated_path,
						'originalUrl' => $updated_path
					], $media_metadata['mediaID'], $conn);
				}

				//Generate Deriatives if needed
				if($media_type === MediaType::Image) {
					$start_mem_limit = ini_get('memory_limit');
					// Update mem limit if not set to 256M already
					if(UploadUtil::size2Bytes(ini_get('memory_limit')) < UploadUtil::size2Bytes('256M')) {
						ini_set('memory_limit', '256M');
					}

					$size = getimagesize($file['tmp_name']);

					$metadata = [
						'pixelXDimension' => $size[0],
						'pixelYDimension' => $size[1]
					];

					$width = $size[0];
					$height = $size[1];

					$storage->upload($file);

					$urls = [ 
						'thumbnailUrl' => [
							'name' => self::addToFilename($file['name'], '_tn'),
							'width' => $GLOBALS['IMG_TN_WIDTH']?? 200,
							'height' => 0
						],
						'url' => [
							'name' =>self::addToFilename($file['name'], '_lg'),
							'width' => $GLOBALS['IMG_WEB_WIDTH']?? 1400,
							'height' => 0
						]
					];

					foreach($urls as $url => $data) {
						if(!($media_metadata[$url] ?? false)) {
							self::create_image(
								$file['name'],
								$data['name'],
								$storage,
								$data['width'],
								$data['height']
							);

							if($storage->file_exists($data['name'])) {
								$metadata[$url] = $storage->getUrlPath($data['name']);
								$createdFilepaths[] = $url;
							}

						}
					}
					self::update_metadata($metadata, $media_metadata['mediaID'], $conn);
				} elseif($media_type === MediaType::Audio) {
					$storage->upload($file);
				}
			}

			mysqli_commit($conn);
		} catch(Throwable $th) {
			mysqli_rollback($conn);

			foreach($createdFilepaths as $filepath) {
				unlink($filepath);
			}

			array_push(self::$errors, $th->getMessage());
		}
	}

	public static function getMediaTypeStrFromMime(string $mime) {
		$media_type_str = explode('/', $mime)[0];
		$media_type = MediaType::tryFrom($media_type_str);

		if(!$media_type) throw new MediaException(MediaException::InvalidMediaType, ' ' . $media_type_str);

		return $media_type_str;
	}

	private static function addToFilename(string $filename, string $ext): string {
		return substr_replace(
			$filename,
			$ext,
			strrpos($filename, '.'),
			0
		);
	}

	/**
	 * @return void
	 */
	public static function remap(int $media_id, int $new_occid, StorageStrategy $old_strategy, StorageStrategy $new_strategy): void {
		$media_arr = self::getMedia($media_id);
		$update_arr = ['occid' => $new_occid];
		$move_files = [];

		if($media_arr['url']) {
			$file = self::parseFileName($media_arr['url']);
			$filename = $file['name'] . $file['extension'];

			//Check if stored in our system if so move to path
			if($old_strategy->file_exists($filename)) {
				$update_arr['url'] = $new_strategy->getUrlPath($filename);
				array_push($move_files, $filename);
			}
		}

		$remap_urls = ['url', 'originalUrl', 'thumbnailUrl'];
		foreach($remap_urls as $url) {
			if($media_arr[$url]) {
				$file = self::parseFileName($media_arr[$url]);
				$filename = $file['name'] . '.' . $file['extension'];

				//Check if stored in our system if so move to path
				if($old_strategy->file_exists($filename) && $old_strategy->getDirPath() !== $new_strategy->getDirPath()) {
					$url_path = $new_strategy->getUrlPath($filename);

					if(!in_array($url_path, $update_arr)) {
						$file = [
							'name' => $filename,
							'tmp_name' => $old_strategy->getDirPath($filename)
						];
						array_push($move_files, $file);
					}

					$update_arr[$url] = $url_path;
				}
			}
		}

		self::update_metadata($update_arr, $media_id);

		foreach($move_files as $file) {
			$new_strategy->upload($file);
			$old_strategy->remove($file['name']);
		}
	}

	public static function disassociate($media_id): void {
		self::update_metadata(['occid' => null], $media_id);
	}

	/**
	 * @return void
	 * @param mixed $media_id
	 * @param mixed $tag_arr
	 * @param mixed $conn
	 */
	private static function update_tags($media_id, $tag_arr, $conn = null): void {
		if(!$conn) {
			$conn = Database::connect('write');
		}

		$tags = QueryUtil::executeQuery($conn, 'SELECT tagkey FROM imagetagkey');

		$remove_tags = [];
		$add_tags = [];
		foreach ($tags as $tagRow) {
			$tag = $tagRow['tagkey'];
			$new_value = $tag_arr['ch_' . $tag] ?? $tag_arr['ch_' . strtolower($tag)] ?? false;
			$old_value = $tag_arr['hidden_' . $tag] ?? $tag_arr['hidden_' . strtolower($tag)] ?? false;
			if($new_value !== $old_value) {
				if($new_value === '1') {
					array_push($add_tags, $tag);
				} else {
					array_push($remove_tags, $tag);
				}
			}
		}

		foreach($add_tags as $add) {
			QueryUtil::executeQuery($conn, 'INSERT INTO imagetag (mediaID, keyvalue) VALUES (?, ?)', [$media_id, $add]);
		}

		foreach($remove_tags as $remove) {
			QueryUtil::executeQuery($conn, 'DELETE FROM imagetag where mediaID = ? and keyvalue = ?', [$media_id, $remove]);
		}
	}

	static function getErrors() {
		$errors = self::$errors ?? [];
		self::$errors = [];
		return $errors;
	}

	private static function check_file_rename(string $old_filepath, string $new_filepath) {		
		if($old_filepath && $new_filepath) {
			$old_file = self::parseFileName($old_filepath);
			$new_file = self::parseFileName($new_filepath);

			if($old_file['extension'] != $new_file['extension']) {
				throw new MediaException(MediaException::IllegalRenameChangedFileType);
			}
		}

		return true;
	}

	/**
	 * Function used for pulling media meta_data out of input array and updating
	 * the corresponding mediaID.
	 * @return bool
	 * @param mixed $media_id MediaID associated from database
	 * @param mixed $media_arr Expects keys to be camel case. Keys that do not
	 * match $meta_data keys will be not be used.
	 */
	public static function update($media_id, $media_arr, StorageStrategy $storage) {

		$meta_data = [
			"tid",
			"occid",
			"url",
			"thumbnailUrl",
			"originalUrl",
			"archiveUrl",
			"sourceUrl",
			"referenceUrl",
			"creator",
			"creatorUid",
			"format",
			"caption",
			"owner",
			"locality",
			"anatomy",
			"notes",
			"username",
			"sortSequence",
			"sortOccurrence",
			"sourceIdentifier",
			"rights",
			"accessRights",
			"copyright",
			"hashFunction",
			"hashValue",
			"mediaMD5",
			"recordID",
			"mediaType",
		];


		$data = [];

		//Map keys to values
		foreach ($meta_data as $key) {
			if(array_key_exists($key, $media_arr)) {
				$data[$key] = $media_arr[$key];
			}
		}

		$conn = Database::connect('write');
		mysqli_begin_transaction($conn);
		try {
			$current_media_arr = self::getMedia($media_id);
			// If file is stored locally then check to make sure the extension is not being changed
			foreach(['url', 'thumbnailUrl', 'originalUrl'] as $url) {
				if(array_key_exists($url, $data) && $storage->file_exists($current_media_arr[$url])) {
					self::check_file_rename(
						$current_media_arr[$url], 
						$data[$url]
					);
				}
			}

			self::update_metadata($data, $media_id, $conn);
			self::update_tags($media_id, $media_arr, $conn);

			if(array_key_exists("renameweburl", $media_arr)) {
				$storage->rename($current_media_arr['url'], $data['url']);
			}

			if(array_key_exists("renametnurl", $media_arr)) {
				$storage->rename($current_media_arr['thumbnailUrl'], $data['thumbnailUrl']);
			}

			if(array_key_exists("renameorigurl", $media_arr)) {
				$storage->rename($current_media_arr['originalUrl'], $data['originalUrl']);
			}

			mysqli_commit($conn);
			return true;
		} catch(Exception $e) {
			array_push(self::$errors, $e->getMessage());

			mysqli_rollback($conn);
			error_log('ERROR: Media update failed on mediaID '
				. $media_id . ' ' . $e->getMessage()
			);
			return false;
		}
	}

	/*
	 * While the function does create an image it does so to resize it
	 *
	 * This function is a wrapper to call the correct image generation function based on what image handler is configured in a given Symbiota Portal. Most use gd
	 *
	 * @param string $src_file Filename to image base
	 * @param string $new_file Filename for newly resized image
	 * @param StorageStrategy $storage Class that instructs where how how an image should be stored
	 * @param int $new_width Maximum width for the new image if zero will box to height
	 * @param int $new_height Maximum height for the new image if zero will box to width
	 */
	public static function create_image($src_file, $new_file, StorageStrategy $storage, $new_width, $new_height): void {
		global $USE_IMAGE_MAGICK;

		if($USE_IMAGE_MAGICK) {
			self::create_image_imagick($src_file, $new_file, $storage, $new_width, $new_height);
		} elseif(extension_loaded('gd') && function_exists('gd_info')) {
			self::create_image_gd($src_file, $new_file, $storage, $new_width, $new_height);
		} else {
			throw new Exception('No image handler for image conversions');
		}

		//If file doesn't according to the upload strategy then upload it to the correct place. This will only run if the media storage is remote to the server
		if(!$storage->file_exists($new_file)) {
			$storage->upload([
				'name' => $new_file,
				'tmp_name' => $storage->getDirPath($new_file),
			]);
		}
	}

	/*
	 * While the function does create an image it does so to resize it
	 *
	 * This function is implemenation for Symbiota Portals using imagick.
	 * Most portals using imagick have ImageMagick installed on server and make system calls in order to use it.
	 * At the time of making this function no know portals have the imagick pecl package installed but and implemenation was made as we are potentially heading in that direction.
	 *
	 * @param string $src_file Filename to image base
	 * @param string $new_file Filename for newly resized image
	 * @param StorageStrategy $storage Class that instructs where how how an image should be stored
	 * @param int $new_width Maximum width for the new image if zero will box to height
	 * @param int $new_height Maximum height for the new image if zero will box to width
	 */
	private static function create_image_imagick(
		string $src_file, string $new_file,
		StorageStrategy $storage,
		int $new_width, int $new_height
	): void {
		$src_path = $storage->getDirPath($src_file);
		$new_path = $storage->getDirPath($new_file);

		if($new_height === 0 && $new_width === 0) {
			throw new Exception('Must have width or height as non zero values');
		} else if($new_height === 0) {
			$new_height = $new_width;
		} else if($new_width === 0) {
			$new_width = $new_height;
		}

		if(extension_loaded('imagick')) {
			$new_image = new Imagick();
			$new_image->readImage($src_path);
			$new_image->resizeImage($new_height, $new_width, Imagick::FILTER_LANCZOS, 1, TRUE);
			$new_image->writeImage($new_path);
			$new_image->destroy();
		} else {
			$qualityRating = self::DEFAULT_JPG_COMPRESSION;

			if($new_width < 300) {
				$ct = system('convert '. $src_path . ' -thumbnail ' . $new_width .' x ' . ($new_width * 1.5).' '.$new_path);
			} else {
				$ct = system('convert '. $src_path . ' -resize ' . $new_width.'x' . ($new_width * 1.5) . ($qualityRating?' -quality '.$qualityRating:'').' '.$new_path);
			}

			if(!file_exists($new_path)){
				error_log('ERROR: Image failed to be created in Imagick function (target path: '.$new_path.')');
			}
		}
	}

	/*
	 * While the function does create an image it does so to resize it
	 *
	 * This function is implemenation for Symbiota Portals using gd.
	 * Gd is the typical default configuration for most portals
	 *
	 * @param string $src_file Filename to image base
	 * @param string $new_file Filename for newly resized image
	 * @param StorageStrategy $storage Class that instructs where how how an image should be stored
	 * @param int $new_width Maximum width for the new image if zero will box to height
	 * @param int $new_height Maximum height for the new image if zero will box to width
	 */
	private static function create_image_gd(
		string $src_file, string $new_file,
		StorageStrategy $storage,
		int $new_width, int $new_height
	): void {

		$src_path = $storage->getDirPath($src_file);
		$new_path = $storage->getDirPath($new_file);

		if($new_width === 0 && $new_height === 0) {
			throw new Exception('Must have width or height as non zero values');
		}

		$size = getimagesize($src_path);

		$width = $size[0];
		$height = $size[1];
		$mime_type = $size['mime'];

		if(!self::enough_memory_gd($size[0], $size[1])) {
			throw new Exception('Not enough memory to create image: ' . $new_file);
		}

		$orig_width = $width;
		$orig_height = $height;

		if($height > $new_height && $new_height !== 0) {
			$width = intval(($new_height / $height) * $width);
			$height = $new_height;
		}

		if($width > $new_width && $new_width !== 0) {
			$height = intval(($new_width / $width) * $height);
			$width = $new_width;
		}

		$image = match($mime_type) {
			'image/jpeg' => imagecreatefromjpeg($src_path),
			'image/png' => imagecreatefrompng($src_path),
			'image/gif' => imagecreatefromgif($src_path),
			default => throw new Exception(
				'Mime Type: ' . $mime_type . ' not supported for creation'
			)
		};

		$new_image = imagecreatetruecolor($width, $height);

		//This is need to maintain transparency if this is here
		if($mime_type === 'image/png') {
			imagealphablending($new_image, false);
			imagesavealpha($new_image, true);
		}

		imagecopyresampled($new_image, $image, 0, 0, 0, 0, $width, $height, $orig_width, $orig_height);

		//Handle Specific file types here
		if($mime_type === 'image/png') {
			imagepng($new_image, $new_path);
		} else {
			imagejpeg($new_image, $new_path);
		}

		imagedestroy($image);
	}

	private static function enough_memory_gd($x, $y, $rgb = 3) {
		// 1.7 is some coef related to gd or overhead not entirely sure
		return  ($x * $y * 1.7 * $rgb) < (UploadUtil::size2Bytes(ini_get('memory_limit')) - memory_get_usage());
	}

	/**
	 * For updating metadata in the media table only
	 *
	 * This function is assumes clean data because it is interal
	 *
	 * @param array $metadata_arr Key value array of Media table attributes
	 * @return void
	 * @throws Exception
	 **/
	private static function update_metadata(array $metadata_arr, int $media_id, mysqli $conn = null): void {
		$values = [];
		$parameter_str = '';

		foreach ($metadata_arr as $key => $value) {
			if($parameter_str !== '') $parameter_str .= ', ';
			$parameter_str .= $key . " = ?";
			$values[] = ($value === '') ? null : $value;
		}
		$values[] = $media_id;

		$sql = 'UPDATE media set '. $parameter_str . ' where mediaID = ?';
		QueryUtil::executeQuery(
			$conn ?? Database::connect('write'),
			$sql,
			$values
		);
	}

	/**
	 * @param int $media_id Media_id that will be deleted from Media table
	 * @param bool $remove_files Database delete will also remove file
	**/
	public static function delete($media_id, $remove_files = true): void {
		$conn = Database::connect('write');
		$result = QueryUtil::executeQuery(
			$conn,
			'SELECT url, thumbnailUrl, originalUrl from media where mediaID = ?',
			[$media_id]
		);
		$media_urls = $result->fetch_assoc();

		$queries = [
			'DELETE FROM specprocessorrawlabels WHERE mediaID = ?',
			'DELETE FROM imagetag WHERE mediaID = ?',
			'DELETE FROM media WHERE mediaID = ?'
		];
		mysqli_begin_transaction($conn);
		try {
			foreach ($queries as $query) {
				QueryUtil::executeQuery($conn, $query, [$media_id]);
			}

			//Unlink all files
			if($remove_files) {
				foreach($media_urls as $url) {
					if($url && file_exists($GLOBALS['SERVER_ROOT'] . $url)) {
						if(!unlink($GLOBALS['SERVER_ROOT'] . $url)) {
							error_log("WARNING: File (path: " . $url . ") failed to delete from server");
						}
					}
				}
			}
			mysqli_commit($conn);
		} catch(Exception $e) {
			error_log("Error: couldnt' remove media of mediaID " . $media_id .": " . $e->getMessage());
			array_push(self::$errors, $e->getMessage());
			mysqli_rollback($conn);
		}
	}

	/**
	 * @param int $media_id
	 * @param string mediaType Should use MediaType Constants
	 */
	public static function getMedia(int $media_id, string $media_type = null): Array {
		if(!$media_id) return [];
		$parameters = [$media_id];
		$sql ='SELECT ' . implode(', ', self::MEDIA_ITEM_SELECT_SCHEMA) .' FROM media m ' .
		'LEFT JOIN taxa t ON t.tid = m.tid ' .
		'LEFT JOIN users u on u.uid = m.creatorUid ' .
		'WHERE mediaID = ?';

		if($media_type) {
			$sql .= ' AND mediaType = ?';
			array_push($parameters, $media_type);
		}

		$sql .= ' ORDER BY sortOccurrence ASC';
		$results = QueryUtil::executeQuery(Database::connect('readonly'), $sql, $parameters);
		$media = self::get_media_items($results);
		if(count($media) <= 0) {
			return [];
		} else {
			return Sanitize::out($media[$media_id]);
		}
	}

	/**
	 * @param int $tid
	 * @param string $media_type Should use MediaType Constants
	 */
	public static function getByTid(int $tid, string $media_type = null): Array {
		if(!$tid) return [];
		$parameters = [$tid];

		$sql ='SELECT ' . implode(',', self::MEDIA_ITEM_SELECT_SCHEMA) . ' FROM media m '.
			'LEFT JOIN taxa t ON t.tid = m.tid ' .
			'LEFT JOIN users u on u.uid = m.creatorUid ' .
			'WHERE m.tid = ?';

		if($media_type) {
			$sql .= ' AND mediaType = ?';
			array_push($parameters, $media_type);
		}

		$sql .= ' ORDER BY sortsequence IS NULL ASC, sortsequence ASC';
		$results = QueryUtil::executeQuery(Database::connect('readonly'), $sql, $parameters);

		return Sanitize::out(self::get_media_items($results));
	}

	/**
	 * @param int $occid
	 * @param string $media_type Should use MediaType constants
	 */
	public static function fetchOccurrenceMedia(int $occid, string $media_type = null): Array {
		if(!$occid) return [];

		$parameters = [$occid];
		$sql = 'SELECT '. implode(',', self::MEDIA_ITEM_SELECT_SCHEMA).' FROM media m ' .
			'LEFT JOIN taxa t ON t.tid = m.tid ' .
			'LEFT JOIN users u on u.uid = m.creatorUid ' .
			'WHERE m.occid = ?';

		if($media_type) {
			$sql .= ' AND m.mediaType = ?';
			array_push($parameters, $media_type);
		}

		$sql .= ' ORDER BY sortOccurrence IS NULL ASC, sortOccurrence ASC';

		$results = QueryUtil::executeQuery(Database::connect('readonly'), $sql, $parameters);

		return Sanitize::out(self::get_media_items($results));
	}

	/**
	 * @param MysqliResult $results
	 * @param mixed $results
	 */
	private static function get_media_items($results): array {
		$media_items = Array();

		while($row = $results->fetch_assoc()){
			$media_items[$row['mediaID']] = $row;
		}
		$results->free();

		return $media_items;
	}

	/**
	 * @param int|array $media_id
	 * @param Mysqli $conn
	 * @return array<string>
	 */
	public static function getMediaTags(int|array $media_id, mysqli $conn = null): array {
		$sql = 'SELECT t.mediaID, k.tagkey, k.shortlabel, k.description_en FROM imagetag t
		INNER JOIN imagetagkey k ON t.keyvalue = k.tagkey
		WHERE t.mediaID ';

		if(is_array($media_id)) {
			$count = count($media_id);
			if($count <= 0) {
				return [];
			}
			$sql .= 'IN (' . str_repeat('?,', $count - 1) . '?)';
		} else {
			$sql .= '= ?';
		}

		$res = QueryUtil::executeQuery(
			$conn?? Database::connect('readonly'),
			$sql,
			is_array($media_id)? $media_id: [$media_id]
		);
		$tags = [];
		while($row = $res->fetch_object()) {
			$tags[$row->mediaID][$row->tagkey] = $row->shortlabel;
		}
		$res->free();

		return Sanitize::out($tags);
	}

	/**
	 * @return array<string>
	 */
	public static function getCreatorArray(): array {
		$sql = <<< SQL
		SELECT u.uid, CONCAT_WS(', ',u.lastname,u.firstname) AS fullname 
		FROM users u 
		ORDER BY u.lastname, u.firstname 
		SQL;

		$result = QueryUtil::executeQuery(Database::connect('readonly'), $sql);
		$creators = array();

		while($row = $result->fetch_object()){
			$creators[$row->uid] = Sanitize::out($row->fullname);
		}
		$result->free();
		return $creators;
	}

	/**
	 * @return array<string>
	 */
	public static function getMediaTagKeys(): array {
		$retArr = Array();

		$sql = <<< SQL
		SELECT tagkey, description_en FROM imagetagkey ORDER BY sortorder;
		SQL;

		$result = QueryUtil::executeQuery(Database::connect('readonly'), $sql);
		while($r = $result->fetch_object()){
			$retArr[$r->tagkey] = Sanitize::out($r->description_en);
		}
		$result->free();
		return $retArr;
	}

	/**
	 * @param mixed $media_arr
	 */
	private static function imagesAreWritable($media_arr): bool{
		$bool = false;
		$testArr = array();
		if($media_arr['originalUrl']) $testArr[] = $media_arr['originalUrl'];
		if($media_arr['url']) $testArr[] = $media_arr['url'];
		if($media_arr['thumbnailUrl']) $testArr[] = $media_arr['thumbnailUrl'];

		$rootPath = self::getMediaRootPath();
		$rootUrl = self::getMediaRootUrl();

		foreach($testArr as $url) {
			if(strpos($url, $rootPath) === 0) {
				if(is_writable($rootPath.substr($url, strlen($rootUrl)))) {
					$bool = true;
				} else {
					$bool = false;
					break;
				}
			}
		}
		return $bool;
	}

	/**
	 * @param array<int,mixed> $media_arr
	 */
	private static function imageNotCatalogNumberLimited(array $media_arr, int $occid): bool{
		$bool = true;
		$testArr = array();
		if($media_arr['originalUrl']) $testArr[] = $media_arr['originalUrl'];
		if($media_arr['url']) $testArr[] = $media_arr['url'];
		if($media_arr['thumbnailUrl']) $testArr[] = $media_arr['thumbnailUrl'];
		//Load identifiers
		$idArr = array();
		$sql = 'SELECT o.catalogNumber, o.otherCatalogNumbers, i.identifierValue FROM omoccurrences o LEFT JOIN omoccuridentifiers i ON o.occid = i.occid WHERE o.occid = ?';
		$rs = QueryUtil::executeQuery(Database::connect('readonly'), $sql, [$occid]);
		$cnt = 0;
		while($r = $rs->fetch_object()){
			if(!$cnt){
				if($r->catalogNumber) $idArr[] = $r->catalogNumber;
				if($r->otherCatalogNumbers) $idArr[] = $r->otherCatalogNumbers;
			}
			if($r->identifierValue) $idArr[] = $r->identifierValue;
			$cnt++;
		}
		$rs->free();
		//Iterate through identifiers and check for identifiers in name
		foreach($idArr as $idStr){
			foreach($testArr as $url){
				if($fileName = substr($url, strrpos($url, '/'))){
					if(strpos($fileName, $idStr) !== false && !preg_match('/_\d{10}[_\.]{1}/', $fileName)){
						$bool = false;
						break 2;
					}
				}
			}
		}
		return $bool;
	}
	/**
	 * @return bool
	 * @param mixed $imgArr
	 * @param mixed $occid
	 */
	public static function isRemappable($imgArr, $occid): bool{
		$bool = false;
		//If all images are writable, then we can rename the images to ensure they will not match incoming images
		$bool = self::imagesAreWritable($imgArr);
		if(!$bool){
			//Or if the image name doesn't contain the catalog number or there is a timestamp added to filename
			$bool = self::imageNotCatalogNumberLimited($imgArr, $occid);
		}
		return $bool;
	}
}

?>
