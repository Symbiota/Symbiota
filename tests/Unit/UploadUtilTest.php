<?php global $SERVER_ROOT;
include_once(__DIR__ . '/../../config/symbini.php');
include_once($GLOBALS['SERVER_ROOT'] . '/classes/utilities/UploadUtil.php');

use PHPUnit\Framework\TestCase;

final class UploadUtilTest extends TestCase {

	/* Checks to ensure all image mimes are in the mime_map */
	public function test_check_images_mimes(): void {
		foreach(UploadUtil::ALLOWED_IMAGE_MIMES as $mime) {
			$ext = UploadUtil::mime2ext($mime);
			$this->assertSame(UploadUtil::MIME_MAP[$mime] ?? false, $ext);
			$mimes_from_ext = Media::ext2Mime($ext)[0] ?? false;
		}
	}

	/* Checks to ensure all audio mimes are in the mime_map */
	public function test_check_audio_mimes(): void {
		foreach(UploadUtil::ALLOWED_AUDIO_MIMES as $mime) {
			$ext = UploadUtil::mime2ext($mime);
			$this->assertSame(UploadUtil::MIME_MAP[$mime] ?? false, $ext);
			$mimes_from_ext = Media::ext2Mime($ext)[0] ?? false;
		}
	}

	/* Checks to ensure all loan mimes are in the mime_map */
	public function test_check_loan_mimes(): void {
		foreach(UploadUtil::ALLOWED_LOAN_MIMES as $mime) {
			$ext = UploadUtil::mime2ext($mime);
			$this->assertSame(UploadUtil::MIME_MAP[$mime] ?? false, $ext);
		}
	}

	/* Checks to ensure all zip mimes are in the mime_map */
	public function test_check_zip_mimes(): void {
		foreach(UploadUtil::ALLOWED_ZIP_MIMES as $mime) {
			$ext = UploadUtil::mime2ext($mime);
			$this->assertSame(UploadUtil::MIME_MAP[$mime] ?? false, $ext);
		}
	}

	public function test_check_zip_ext(): void {
		foreach(UploadUtil::ALLOWED_ZIP_MIMES as $mime) {
			$mimes_from_ext = UploadUtil::ext2Mime('zip');
			if(is_array($mimes_from_ext)) {
				$this->assertContains($mimes_from_ext, $mime);
			} else {
				$this->assertSame($mimes_from_ext, $mime);
			}
		}
	}
}
