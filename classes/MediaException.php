<?php
class MediaException extends Exception {
	public const InvalidMediaType = 'INVALID_MEDIA_TYPE';
	public const DuplicateMediaFile = 'DUPLICATE_MEDIA_FILE';
	public const FileDoesNotExist = 'FILE_DOES_NOT_EXIST';
	public const FileAlreadyExists = 'FILE_ALREADY_EXISTS';
	public const SuspiciousFile = 'SUSPICIOUS_FILE';
	public const IllegalRenameChangedFileType = 'ILLEGAL_RENAME_CHANGED_FILE_TYPE';
	public const FileTypeNotAllowed = 'FILE_TYPE_NOT_ALLOWED';
	public const FilepathNotWritable = 'FILEPATH_NOT_WRITABLE';

	function __construct(string $case, string $message = ''){
		global $LANG, $LANG_TAG, $SERVER_ROOT;

		if(file_exists($SERVER_ROOT.'/content/lang/classes/Media.'.$LANG_TAG.'.php')) {
			include_once($SERVER_ROOT.'/content/lang/classes/Media.'.$LANG_TAG.'.php');
		} else {
			include_once($SERVER_ROOT.'/content/lang/classes/Media.en.php');
		}

		if($message) {
			parent::__construct($LANG[$case] . ': ' . $message);
		} else {
			parent::__construct($LANG[$case]);
		}
	}
}
