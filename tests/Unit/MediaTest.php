<?php
include_once($GLOBALS['SERVER_ROOT'] . '/classes/Media.php');

use PHPUnit\Framework\TestCase;

final class MediaTest extends TestCase {
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
}
