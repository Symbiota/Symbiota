<?php
class MediaType {
	public const Image = 'image';
	public const Audio = 'audio';
	public const Video = 'video' ;
	public const Misc = 'misc' ;

	private const misc_types = [
		'text',
		'application'
	];

	public static function tryFrom(string $value): ?string {
		if($value === self::Image || $value === self::Audio || $value === self::Video) {
			return $value;
		} elseif(in_array($value, self::misc_types)) {
			return self::Misc;
		} else {
			return null;
		}
	}

	public static function values(): array {
		return [
			self::Image,
			self::Audio,
			self::Video,
			self::Misc
		];
	}
}
