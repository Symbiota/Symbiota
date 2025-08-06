<?php
include_once($SERVER_ROOT . "/classes/MediaException.php");
include_once($SERVER_ROOT . "/classes/MediaType.php");

abstract class StorageStrategy {
	/**
	 * If a file is given then return the storage path for that resource otherwise just return the root path.
	 * @param string | array $file {name: string, type: string, tmp_name: string, error: int, size: int}
	 * @return string
	 */
	abstract public function getDirPath($file): string;

	/**
	 * If a file is given then return the url path to that resource otherwise just return the root url path.
	 * @param string | array $file {name: string, type: string, tmp_name: string, error: int, size: int}
	 * @return string
	 */
	abstract public function getUrlPath($file): string;

	/**
	 * Function to check if a file exists for the storage location of the upload strategy.
	 * @param string | array $file {name: string, type: string, tmp_name: string, error: int, size: int}
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
	 * @param array $file {name: string, type: string, tmp_name: string, error: int, size: int}
	 * @return bool
	 * @throws MediaException(MediaException::DuplicateMediaFile)
	 */
	abstract public function remove(string $file): bool;

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

class LocalStorage extends StorageStrategy {
	private string $path;

	public function __construct($path = '') {
		$this->path = $path ?? '';
	}

	public function getDirPath($file = null): string {
		$file_name = is_array($file)? $file['name']: $file;
		return $GLOBALS['MEDIA_ROOT_PATH'] .
			(substr($GLOBALS['MEDIA_ROOT_PATH'],-1) != "/"? '/': '') .
			$this->path . $file_name;
	}

	public function getUrlPath($file = null): string {
		$file_name = is_array($file)? $file['name']: $file;
		return $GLOBALS['MEDIA_ROOT_URL'] .
		   	(substr($GLOBALS['MEDIA_ROOT_URL'],-1) != "/"? '/': '') .
		   	$this->path . $file_name;
	}

	/**
	 * Private help function for interal use that holds logic for how storage paths are created.
	 * @return string
	 */

	public function file_exists($file): bool {
		$filename = is_array($file)? $file['name']: $file;

		if(str_contains($filename, $this->getUrlPath())) {
			$filename = str_replace($this->getUrlPath(), '', $filename);
		}

		return file_exists($this->getDirPath() . $filename);
	}

	/**
	 * Upload implemenation stores files on the server and expect duplicate files to be handled by the caller
	 */
	public function upload(array $file): bool {
		$dir_path = $this->getDirPath();
		$file_path = $dir_path . $file['name'];

		// Create Storage Directory If it doesn't exist
		if(!is_dir($dir_path)) {
			mkdir($dir_path, 744, true);
		}

		if(file_exists($file_path)) {
			throw new MediaException(MediaException::DuplicateMediaFile);
		}

		//If Uploaded from $_POST then move file to new path
		if(is_uploaded_file($file['tmp_name'])) {
			move_uploaded_file($file['tmp_name'], $file_path);
		//If temp path is on server then just move to new location;
		} else if(file_exists($file['tmp_name'])) {
			rename($file['tmp_name'], $file_path);
		//Otherwise assume tmp_name a url and stream file contents over
		} else {
			error_log("Moving" . $file['tmp_name'] . ' to ' . $file_path );
			file_put_contents($file_path, fopen($file['tmp_name'], 'r'));
		}

		return true;
	}
	/**
	 * @return bool
	 * @param mixed $path
	 */
	static private function on_system($path) {
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

	public function remove(string $filename): bool {
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
