<?php
class MediaType {
	public const Image = 'image';
	public const Audio = 'audio';
	public const Video = 'video' ;

	public static function tryFrom(string $value) {
		if($value === self::Image || $value === self::Audio || $value === self::Video) {
			return $value;
		} else {
			return null;
		}
	}

	public static function values(): array {
		return [
			self::Image,
			self::Audio,
			self::Video
		];
	}
}
