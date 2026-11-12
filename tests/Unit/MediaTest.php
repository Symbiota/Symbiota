<?php
include_once($GLOBALS['SERVER_ROOT'] . '/classes/Media.php');

use PHPUnit\Framework\TestCase;

final class MediaTest extends TestCase {
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

		$result = Media::getAllowedMime(['image/bmp', 'image/x-bmp']);
		$this->assertSame(false, $result);

		$GLOBALS['ALLOWED_MEDIA_MIME_TYPES'] = null;

		$result = Media::getAllowedMime(['image/pjpeg', 'image/jpeg']);
		$this->assertSame('image/pjpeg', $result);
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
