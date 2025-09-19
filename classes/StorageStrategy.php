<?php global $SERVER_ROOT;
include_once($SERVER_ROOT . "/classes/MediaException.php");
include_once($SERVER_ROOT . "/classes/MediaType.php");
include_once($SERVER_ROOT . "/classes/utilities/UploadUtil.php");
require_once($SERVER_ROOT . '/vendor/autoload.php');

/**
 * Class meant to abstract storage file operations when storing
 * files that are not temporary.
 *
 * The concept of a $file array is just the same keys as create
 * when php does a file upload for ease of integration.
 * file paths are also supported.
 */
abstract class StorageStrategy {
	/**
	 * If a file is given then return the storage path for that resource otherwise just return the root path.
	 * @param string|array $file {name: string, type: string, tmp_name: string, error: int, size: int}
	 * @return string
	 */
	abstract public function getDirPath($file): string;

	/**
	 * If a file is given then return the url path to that resource otherwise just return the root url path.
	 * @param string|array $file {name: string, type: string, tmp_name: string, error: int, size: int}
	 * @return string
	 */
	abstract public function getUrlPath($file): string;

	/**
	 * Function to check if a file exists for the storage location of the upload strategy.
	 * @param string|array $file {name: string, type: string, tmp_name: string, error: int, size: int}
	 * @return bool
	 */
	abstract public function file_exists($file): bool;

	/**
	 * Function to handle how a file should be uploaded.
	 * @param array $file {name: string, type: string, tmp_name: string, error: int, size: int}
	 * @return bool
	 * @throws MediaException(MediaException::DuplicateMediaFile)
	 */
	abstract public function upload(array $file): bool;

	/**
	 * Function to handle how a file should be removed.
	 * @param string|array $file {name: string, type: string, tmp_name: string, error: int, size: int}
	 * @return bool
	 * @throws MediaException(MediaException::DuplicateMediaFile)
	 */
	abstract public function remove($file): bool;

	/**
	 * Function to handle renaming an existing file.
	 * @param string $filepath
	 * @param array $new_filepath
	 * @return bool
	 * @throws MediaException(MediaException::FileDoesNotExist)
	 * @throws MediaException(MediaException::FileAlreadyExists)
	 */
	abstract public function rename(string $filepath, string $new_filepath): void;
}

/**
 * Local storage driver for StorageStrategy interface. Used for managing files
 * stored on server's file system.
 *
 * This can be a risky strategy given how php works and to use this
 * driver securely make sure to configure nginx or apache properly
 */
class LocalStorage extends StorageStrategy {
	private string $path;

	/**
	 * @param String $path Path is string filepath starting with no slash and ending
	 * with a slash. It serves as an extension of the root storage path to limit
	 * where files can be stored.
	 **/
	public function __construct(string $path = '') {
		$this->path = $path ?? '';
	}

	public function getDirPath($file = ''): string {
		$filename = is_array($file)? $file['name']: $file;
		return $GLOBALS['MEDIA_ROOT_PATH'] .
			(substr($GLOBALS['MEDIA_ROOT_PATH'],-1) != "/"? '/': '') .
			$this->path . $filename;
	}

	public function getUrlPath($file = ''): string {
		$filename = is_array($file)? $file['name']: $file;
		return $GLOBALS['MEDIA_ROOT_URL'] .
		   	(substr($GLOBALS['MEDIA_ROOT_URL'],-1) != "/"? '/': '') .
			$this->path . $filename;
	}

	public function file_exists($file): bool {
		$filename = is_array($file)? $file['name']: $file;

		if(str_contains($filename, $this->getUrlPath())) {
			$filename = str_replace($this->getUrlPath(), '', $filename);
		}

		return file_exists($this->getDirPath() . $filename);
	}

	public function upload(array $file): bool {
		$dir_path = $this->getDirPath();
		$filepath = $dir_path . $file['name'];

		// Create Storage Directory If it doesn't exist
		if(!is_dir($dir_path)) {
			mkdir($dir_path, 0764, true);
		}

		if(!is_writable($dir_path)) {
			throw new MediaException(MediaException::FilepathNotWritable, $dir_path);
		}

		if(file_exists($filepath)) {
			throw new MediaException(MediaException::DuplicateMediaFile);
		}

		//If Uploaded from $_POST then move file to new path
		if(is_uploaded_file($file['tmp_name'])) {
			move_uploaded_file($file['tmp_name'], $filepath);
		//If temp path is on server then just move to new location
		} else if(file_exists($file['tmp_name'])) {
			rename($file['tmp_name'], $filepath);
		//Otherwise assume tmp_name a url and stream file contents over
		} else {
			error_log("Moving" . $file['tmp_name'] . ' to ' . $filepath );
			file_put_contents($filepath, fopen($file['tmp_name'], 'r'));
		}

		return true;
	}


	/**
	 * Checks if a given path leads to a file on system.
	 *
	 * Supports passing either the full absoulte path or the relative url path.
	 * Relative url paths will get converted to absoulte paths.
	 *
	 * @access private
	 * @param String $path Filepath that needs checking
	 * @return Bool
	 **/
	static private function on_system(string $path): Bool {
		//Check if path is absoulte path
		if(file_exists($path)) {
			return true;
		}
		//Convert url path to dir_path
		$dir_path = str_replace(
			$GLOBALS['MEDIA_ROOT_URL'],
			$GLOBALS['MEDIA_ROOT_PATH'],
			$path
		);

		return file_exists($dir_path);
	}

	public function remove($file): bool {
		$filename = is_array($file)? $file['name']: $file;

		if(!is_writable($GLOBALS['SERVER_ROOT'] . $filename)) {
			throw new MediaException(MediaException::FilepathNotWritable, $filename);
		}

		//Check Relative Path
		if($this->file_exists($filename)) {
			if(!unlink($this->getDirPath($filename))) {
				error_log("WARNING: File (path: " . $this->getDirPath($filename) . ") failed to delete from server in LocalStorage->remove");
				return false;
			};
			return true;
		}

		//Get Absoulte Path
		$dir_path = str_replace(
			$GLOBALS['MEDIA_ROOT_URL'],
			$GLOBALS['MEDIA_ROOT_PATH'],
			$filename
		);

		//Check Absolute path
		if($dir_path !== $filename && file_exists($dir_path)) {
			if(!unlink($dir_path)) {
				error_log("WARNING: File (path: " . $dir_path. ") failed to delete from server in LocalStorage->remove");
				return false;
			}
			return true;
		}

		return false;
	}

	public function rename(string $filepath, string $new_filepath): void {
		//Remove MEDIA_ROOT_PATH + Path from filepath if it exists
		global $SERVER_ROOT;

		$old_file = pathinfo($filepath);
		$new_file = pathinfo($new_filepath);

		if($old_file['extension'] != $new_file['extension']) {
			throw new MediaException(MediaException::IllegalRenameChangedFileType);
		}

		$dir_path = $this->getDirPath() . $this->path;
		$filepath = str_replace($dir_path, '', $GLOBALS['SERVER_ROOT'] . $filepath);
		$new_filepath = str_replace($dir_path, '', $GLOBALS['SERVER_ROOT'] . $new_filepath);

		//Constrain Rename to Scope of MEDIA_ROOT_PATH + Storage Path
		if($this->file_exists($new_filepath)) {
			throw new MediaException(MediaException::FileAlreadyExists);
		} else if(!$this->file_exists($filepath)) {
			throw new MediaException(MediaException::FileDoesNotExist);
		} else {
			rename($dir_path . $filepath, $dir_path . $new_filepath);
		}
	}
}

/**
 * S3 storage driver for StorageStrategy interface. Used for managing files
 * storage in s3 object storage on same server or remote server.
 *
 * If possible this is the recomended driver to use because it is more secure
 * by default.
 *
 * All technologies that support s3 client interface should work. However
 * the follow are onces we aim to support.
 * - CEPH
 */
class S3Storage extends StorageStrategy {
	private $client;
	private $path;

	/**
	 * Intializes s3 client and takes a path that will serve as the root s3 key path.
	 * This value should be prepended to incoming files and/or filepaths.
	 *
	 * S3 Client is intialized using the following symbini.php config variables
	 * $S3_REGION: region
	 * $S3_ENDPOINT: endpoint
	 * $S3_ACCESS_KEY_ID: credentials => key
	 * $S3_SECRET_ID: credentials => secret
	 *
	 * @param String $path Path is string filepath starting with no slash and ending
	 * with a slash. It serves as an extension of the root storage path to limit
	 * where files can be stored.
	 **/
	public function __construct($path = '') {
		$this->path = $path;
		$this->client =  new Aws\S3\S3Client([
			'version' => 'latest',
			'region'  => $GLOBALS['S3_REGION'],
			'endpoint' => $GLOBALS['S3_ENDPOINT'],
			'use_path_style_endpoint' => true,
			'credentials' => [
				'key' => $GLOBALS['S3_ACCESS_KEY_ID'],
				'secret' => $GLOBALS['S3_SECRET_ID']
			],
		]);
	}

	public function getDirPath($file = ''): string {
		$file_name = is_array($file)? $file['name']: $file;

		return $GLOBALS['MEDIA_ROOT_URL'] .
			(substr($GLOBALS['MEDIA_ROOT_URL'],-1) != "/"? '/': '') .
			$this->path . $file_name;
	}

	public function getUrlPath($file = ''): string {
		$file_name = is_array($file)? $file['name']: $file;
		return $GLOBALS['MEDIA_DOMAIN'] . $GLOBALS['MEDIA_ROOT_URL'] .
		   	(substr($GLOBALS['MEDIA_ROOT_URL'],-1) != "/"? '/': '') .
		   	$this->path . $file_name;
	}

	public function file_exists($file): bool {
		$filename = is_array($file)? $file['name']: $file;

		return $this->client->doesObjectExistV2($GLOBALS['S3_MEDIA_BUCKET_NAME'], self::getPathFromUrl($filename));
	}

	public function upload(array $file): bool {
		if(!file_exists($file['tmp_name'])) {
			throw new MediaException(MediaException::FileDoesNotExist, $file['tmp_name']);
		}

		$this->client->putObject([
			'Bucket' => $GLOBALS['S3_MEDIA_BUCKET_NAME'],
			'Key'    => $GLOBALS['MEDIA_ROOT_URL'] . (substr($GLOBALS['MEDIA_ROOT_URL'],-1) != "/"? '/': '') . $this->path . $file['name'],
			'ContentType' => $file['type'],
			'Body'   => fopen($file['tmp_name'], "r"),
			'ACL' => 'public-read'
		]);

		return true;
	}

	/**
	 * Takes url and normailzes to just the key path without the
	 * Media Bucket Name.
	 *
	 * @param String $url s3 url either with s3:// url or only path url or filepath
	 * @return Bool|String
	 **/
	function getPathFromUrl(String $url) {
		$url_parts = UploadUtil::decomposeUrl($url);
		$bucket_path = '/' . $GLOBALS['S3_MEDIA_BUCKET_NAME'];
		$path = $url_parts['path'];

		if($path === $url_parts['basename']) {
			$path = self::getDirPath($path);
		}

		if(strpos($path, $bucket_path) === 0) {
			return substr($path, strlen($bucket_path));
		}

		return $path;
	}

	public function remove($file): bool {
		$filename = is_array($file)? $file['name']: $file;
		$trimed_file_name = str_replace($GLOBALS['MEDIA_DOMAIN'] . $GLOBALS['S3_MEDIA_BUCKET_NAME'],'', $filename);

		$result = $this->client->deleteObject([
			'Bucket' => $GLOBALS['S3_MEDIA_BUCKET_NAME'],
			'Key'    => self::getPathFromUrl($filename),
		]);

		if(($metadata = $result->get('@metadata')) && ($metadata['statusCode'] ?? false) === 204) {
			return true;
		}

		return false;
	}

	public function rename(String $filepath, String $new_filepath): Void {
		$src_path = self::getPathFromUrl($filepath);
		$result = $this->client->copyObject([
			'Bucket' => $GLOBALS['S3_MEDIA_BUCKET_NAME'],
			'CopySource' => $GLOBALS['S3_MEDIA_BUCKET_NAME'] . $src_path,
			'Key' => self::getPathFromUrl($new_filepath)
		]);

		$this->remove($filepath);
	}
}

class StorageFactory {
	/**
	 * Static Factory for creating correct storage driver based
	 * on symbini config.
	 *
	 * Requires $STORAGE_DRIVER be set in the config/symbini.php
	 * to either 'local' or 's3'.
	 *
	 * @param String $path Path is string filepath starting with no slash and ending
	 * with a slash. It serves as an extension of the root storage path to limit
	 * where files can be stored.
	 * @return StorageStrategy
	 * @throws Exception if $STORAGE_DRIVER is not 'local' or 's3'
	 **/
	static function make(String $path = ''): StorageStrategy {
		switch($GLOBALS['STORAGE_DRIVER']) {
			case 'local': return new LocalStorage($path);
			case 's3': return new S3Storage($path);
			default: throw new Exception('STORAGE_DRIVER not configure properly. Use "local" or "s3" as options');
		}

	}
}
