<?php
include_once($GLOBALS['SERVER_ROOT'] . '/classes/utilities/UploadUtil.php');

use PHPUnit\Framework\TestCase;

final class UploadUtilTest extends TestCase {
	/* UploadUtil::parseFileName tests */
	public function test_trival_file_parsing(): void {
		$file = UploadUtil::parseFileName('trival.jpg');

		$this->assertSame('trival', $file['name']);
		$this->assertSame('jpg', $file['extension']);
	}

	public function test_directory_file_parsing(): void {
		$file = UploadUtil::parseFileName('dir/trival.mp4');

		$this->assertSame('trival', $file['name']);
		$this->assertSame('mp4', $file['extension']);
	}

	public function test_simple_url_path(): void {
		$file = UploadUtil::parseFileName('https://localhost:80/dir/trival.mp4');

		$this->assertSame('trival', $file['name']);
		$this->assertSame('mp4', $file['extension']);
	}

	public function test_url_query(): void {
		$file = UploadUtil::parseFileName('https://localhost:80/dir/trival.png?x=500&y=500');

		$this->assertSame('trival', $file['name']);
		$this->assertSame('png', $file['extension']);
	}

	/* UploadUtil::cleanFileName tests */
	public function test_filename_cleaning_period() {
		$this->assertSame('filename', UploadUtil::cleanFileName('file.name'));
	}

	public function test_filename_cleaning_space_normalization() {
		$this->assertSame('long_file_name_test_attempt', UploadUtil::cleanFileName('long file%20name%23test__attempt'));
	}

	public function test_filename_cleaning_special_chars() {
		$this->assertSame('aaaaaaae', UploadUtil::cleanFileName('àáâãäåæ'));
		$this->assertSame('eeee', UploadUtil::cleanFileName('èéêë'));
		$this->assertSame('iiii', UploadUtil::cleanFileName('ìíîï'));
		$this->assertSame('ooooo', UploadUtil::cleanFileName('òóôõö'));
		$this->assertSame('uuuu', UploadUtil::cleanFileName('ùúûü'));
		$this->assertSame('n', UploadUtil::cleanFileName('ñ'));
	}

	public function test_filename_cleaning_trim_underscores() {
		$this->assertSame('test', UploadUtil::cleanFileName('_test_'));
		$this->assertSame('test', UploadUtil::cleanFileName('-test-'));
	}
	
	public function test_filename_cleaning_truncate_30() {
		$this->assertSame(
			'very_long_name_still_mega_long', 
			UploadUtil::cleanFileName('very_long_name_still_mega_long_cutthis')
		);
	}

}
