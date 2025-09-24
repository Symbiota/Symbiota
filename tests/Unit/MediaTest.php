<?php
include_once(__DIR__ . '/../config/symbini.php');

include_once($GLOBALS['SERVER_ROOT'] . '/classes/Media.php');

use PHPUnit\Framework\TestCase;

final class MediaTest extends  TestCase {
	/* Media::parseFileName tests */
	public function test_trival_file_parsing(): void {
		$file = Media::parseFileName('trival.jpg');

		$this->assertSame('trival', $file['name']);
		$this->assertSame('jpg', $file['extension']);
	}

	public function test_directory_file_parsing(): void {
		$file = Media::parseFileName('dir/trival.mp4');

		$this->assertSame('trival', $file['name']);
		$this->assertSame('mp4', $file['extension']);
	}

	public function test_simple_url_path(): void {
		$file = Media::parseFileName('https://localhost:80/dir/trival.mp4');

		$this->assertSame('trival', $file['name']);
		$this->assertSame('mp4', $file['extension']);
	}

	public function test_url_query(): void {
		$file = Media::parseFileName('https://localhost:80/dir/trival.png?x=500&y=500');

		$this->assertSame('trival', $file['name']);
		$this->assertSame('png', $file['extension']);
	}

	/*
	public function test_url_query_no_file_extension(): void {
		$file = Media::parseFileName('https://api.idigbio.org/v2/media/db1aa6ed4e075c86b0dd3911554295a4?size=webview');

		//Limits to 30 characters so only a4 on the end should be cut off
		$this->assertSame('db1aa6ed4e075c86b0dd3911554295_'. time(), $file['name']);
		$this->assertSame('', $file['extension']);
	}

	// Media::parseFileName tests
	public function testUrlExists(): void {
		$file_info = Media::getRemoteFileInfo('https://selectree.calpoly.edu/images/logos/UFEI.png');
		$this->assertIsString($file_info['name']);
		var_dump($file_info);
	}
	public function testUrlExistsComplicated(): void {
		$file_info = Media::getRemoteFileInfo('https://api.idigbio.org/v2/media/db1aa6ed4e075c86b0dd3911554295a4?size=webview');
		$this->assertIsString($file_info['name']);
		var_dump($file_info);
	}

	// Media::createImage tests
	public function testCreateImage(): void {
		$GLOBALS['USE_IMAGE_MAGICK'] = true;
		$this->assertTrue(extension_loaded('imagick'));
		$src_image = $SERVER_ROOT . '/temp/images/BLMAR/202408/UFEI_1723591447.png';
		$new_image = $SERVER_ROOT . '/temp/images/BLMAR/202408/UFEI_1723591447_tn.png';
		Media::create_image($src_image, $new_image, 200, 200);
		$this->assertTrue(file_exists($new_image));
	}
	*/
}
