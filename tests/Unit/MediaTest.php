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

	/* Media::getAllowedMime tests */
	public function test_mime_check_happy(): void {
		$GLOBALS['ALLOWED_MEDIA_MIME_TYPES'] = ['image/jpeg'];

		$result = Media::getAllowedMime('image/jpeg');
		$this->assertSame('image/jpeg', $result);
	}

	public function test_mime_check_fail(): void {
		$GLOBALS['ALLOWED_MEDIA_MIME_TYPES'] = [
			'image/jpeg'
		];

		$result = Media::getAllowedMime('image/png');

		$this->assertSame(false, $result);
	}

	public function test_mime_check_array(): void {
		$GLOBALS['ALLOWED_MEDIA_MIME_TYPES'] = ['image/jpeg'];
		$result = Media::getAllowedMime(['image/jpeg', 'image/pjpeg']);
		$this->assertSame('image/jpeg', $result);

		$result = Media::getAllowedMime(['image/pjpeg', 'image/jpeg']);
		// $this->assertSame('image/jpeg', $result);

		$result = Media::getAllowedMime(['image/bmp', 'image/x-bmp']);
		$this->assertSame(false, $result);

		$GLOBALS['ALLOWED_MEDIA_MIME_TYPES'] = null;

		$result = Media::getAllowedMime(['image/pjpeg', 'image/jpeg']);
		// $this->assertSame('image/pjpeg', $result);
	}

	/* Media::ext2Mime tests */
	public function test_mime2ext_fetch_image_type() {
		$result = Media::ext2Mime('gif');
		$this->assertSame('image/gif', $result);

		$result = Media::ext2Mime('gif', 'image');
		$this->assertSame('image/gif', $result);

		$result = Media::ext2Mime('gif', 'audio');
		$this->assertSame(false, $result);
	} 

	public function test_mime2ext_fetch_audio_type() {
		$result = Media::ext2Mime('ogg');
		$this->assertSame('audio/ogg', $result);

		$result = Media::ext2Mime('ogg', 'audio');
		$this->assertSame('audio/ogg', $result);

		$result = Media::ext2Mime('ogg', 'image');
		$this->assertSame(false, $result);
	} 

	/* Media::cleanFileName tests */
	public function test_filename_cleaning_period() {
		$this->assertSame('filename', Media::cleanFileName('file.name'));
	}

	public function test_filename_cleaning_space_normalization() {
		$this->assertSame('long_file_name_test_attempt', Media::cleanFileName('long file%20name%23test__attempt'));
	}

	public function test_filename_cleaning_special_chars() {
		$this->assertSame('aaaaaaa', Media::cleanFileName('àáâãäåæ'));
		$this->assertSame('eeee', Media::cleanFileName('èéêë'));
		$this->assertSame('iiii', Media::cleanFileName('ìíîï'));
		$this->assertSame('ooooo', Media::cleanFileName('òóôõö'));
		$this->assertSame('uuuu', Media::cleanFileName('ùúûü'));
		$this->assertSame('n', Media::cleanFileName('ñ'));
	}

	public function test_filename_cleaning_trim_underscores() {
		$this->assertSame('test', Media::cleanFileName('_test_'));
		$this->assertSame('test', Media::cleanFileName('-test-'));
	}
	
	public function test_filename_cleaning_truncate_30() {
		$this->assertSame(
			'very_long_name_still_mega_long', 
			Media::cleanFileName('very_long_name_still_mega_long_cutthis')
		);
	}

	/* Media getMediaTypeStrFromMime */
	public function test_mime_str_parse_happy() {
		$this->assertSame(MediaType::Image, Media::getMediaTypeStrFromMime('image/png'));
		$this->assertSame(MediaType::Audio, Media::getMediaTypeStrFromMime('audio/png'));
	}

	public function test_mime_str_parse_fail() {
		$this->expectException(MediaException::class);

		Media::getMediaTypeStrFromMime('type/not');
	}
}
