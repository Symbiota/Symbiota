<?php
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

class MediaException extends Exception {
	public const InvalidMediaType = 'INVALID_MEDIA_TYPE';
	public const DuplicateMediaFile = 'DUPLICATE_MEDIA_FILE';
	public const FileDoesNotExist = 'FILE_DOES_NOT_EXIST';
	public const FileAlreadyExists = 'FILE_ALREADY_EXISTS';
	public const SuspiciousFile = 'SUSPICIOUS_FILE';
	public const IllegalRenameChangedFileType = 'ILLEGAL_RENAME_CHANGED_FILE_TYPE';
	public const FileTypeNotAllowed = 'FILE_TYPE_NOT_ALLOWED';
	public const FilepathNotWritable = 'FILEPATH_NOT_WRITABLE';
	public const NotEnoughMemoryImage = 'NOT_ENOUGH_MEMORY_IMAGE';
	public const ExceedMaxSize = 'EXCEED_MAX_SIZE';
	public const NoFileUploaded = 'NO_FILE_UPLOADED';
	public const PartialUpload = 'PARTIAL_UPLOAD';
	public const MissingTempDir = 'MISSING_TEMP_DIR';
	public const UploadStoppedByExtension = 'UPLOAD_STOPPED_BY_EXTENSION';
	public const UnknownUploadError = 'UNKNOWN_UPLOAD_ERROR';

	function __construct(string $case, string $message = ''){
		global $LANG, $LANG_TAG, $SERVER_ROOT;

		Language::load('classes/Media');

		if($message) {
			parent::__construct($LANG[$case] . ': ' . $message);
		} else {
			parent::__construct($LANG[$case]);
		}
	}
}
