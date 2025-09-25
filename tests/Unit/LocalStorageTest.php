<?php
include_once($GLOBALS['SERVER_ROOT'] . '/classes/Media.php');
include_once($GLOBALS['SERVER_ROOT'] . '/classes/StorageStrategy.php');

use PHPUnit\Framework\TestCase;

final class SymbiotaUploadStrategyTest extends TestCase {
	const root_path = 'image/root/path/';
	const root_url = 'image/root/url/';

	private static function initGlobals() :void {
		$GLOBALS['MEDIA_ROOT_PATH'] = self::root_path;
		$GLOBALS['MEDIA_ROOT_URL'] = self::root_url;
	}

	public function testInstitutionCodeOnly(): void {
		self::initGlobals();

		$upload_strategy = new LocalStorage(get_occurrence_upload_path('INST_CODE', null, null));

		$this->assertSame(self::root_path . 'INST_CODE/'. date('Ym') . '/', $upload_strategy->getDirPath());
		$this->assertSame(self::root_url . 'INST_CODE/'. date('Ym') . '/' , $upload_strategy->getUrlPath());
	}

	public function testSlashInsertion(): void {
		$GLOBALS['MEDIA_ROOT_PATH'] = 'image/root/path';
		$GLOBALS['MEDIA_ROOT_URL'] = 'image/root/url';

		$upload_strategy = new LocalStorage(get_occurrence_upload_path('INST_CODE', null, null));

		$this->assertSame('image/root/path/INST_CODE/'. date('Ym') . '/', $upload_strategy->getDirPath());
		$this->assertSame('image/root/url/INST_CODE/'. date('Ym') . '/', $upload_strategy->getUrlPath());
	}


	public function testInstAndCollCode(): void {
		self::initGlobals();

		$upload_strategy = new LocalStorage(get_occurrence_upload_path('INST_CODE', 'COLL_CODE', null));

		$this->assertSame(self::root_path . 'INST_CODE_COLL_CODE/'. date('Ym') . '/', $upload_strategy->getDirPath());
		$this->assertSame(self::root_url . 'INST_CODE_COLL_CODE/'. date('Ym') . '/' , $upload_strategy->getUrlPath());
	}

	public function testInstCollCodeCatNumber(): void {
		self::initGlobals();

		$upload_strategy = new LocalStorage(get_occurrence_upload_path('INST_CODE', 'COLL_CODE', '8994'));

		$this->assertSame(self::root_path . 'INST_CODE_COLL_CODE/'. '00008' . '/', $upload_strategy->getDirPath());
		$this->assertSame(self::root_url . 'INST_CODE_COLL_CODE/'. '00008' . '/' , $upload_strategy->getUrlPath());
	}

	public function testInstCollCodeCatString(): void {
		self::initGlobals();

		$upload_strategy = new LocalStorage(get_occurrence_upload_path('INST_CODE', 'COLL_CODE', 'BLS4578'));

		$this->assertSame(self::root_path . 'INST_CODE_COLL_CODE/'. 'BLS4' . '/', $upload_strategy->getDirPath());
		$this->assertSame(self::root_url . 'INST_CODE_COLL_CODE/'. 'BLS4' . '/' , $upload_strategy->getUrlPath());
	}

	public function testSpaceReplace(): void {
		self::initGlobals();

		$upload_strategy = new LocalStorage(get_occurrence_upload_path('INST CODE', 'COLL_CODE', 'BLS4578'));

		$this->assertSame(self::root_path . 'INST_CODE_COLL_CODE/'. 'BLS4' . '/', $upload_strategy->getDirPath());
		$this->assertSame(self::root_url . 'INST_CODE_COLL_CODE/'. 'BLS4' . '/' , $upload_strategy->getUrlPath());
	}
}
