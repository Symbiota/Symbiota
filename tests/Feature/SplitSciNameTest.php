<?php

use PHPUnit\Framework\TestCase;
include_once($GLOBALS['SERVER_ROOT'] . '/classes/TaxonomyEditorManager.php');

class SplitSciNameTest extends TestCase {
    public function testSplitScinameFromOccArrEasy() {
        $testArr = [];
        $testArr['sciname'] = 'Acer rubrum Fisher newtest FRESH';
        $testArr['specificepithet'] = 'rubrum';
        $testArr['scientificnameauthorship'] = 'FISHER';
        $testArr['tradeName'] = 'FRESH';
        $testArr['cultivarEpithet'] = 'newtest';
        $taxonEditorObj = new TaxonomyEditorManager();
        $result = $taxonEditorObj->splitScinameFromOccArr($testArr);

        $expectedResult = [];
        $expectedResult['base'] = 'Acer rubrum';
		$expectedResult['cultivarEpithet'] = 'newtest';
		$expectedResult['tradeName'] = 'FRESH';
		$expectedResult['author'] = 'FISHER';
        $expectedResult['nonItal'] = '';

        $this->assertEquals($expectedResult, $result);
    }

    public function testSplitScinameFromOccArrMissingFeaturesInOccArray() {
        $testArr = [];
        $testArr['sciname'] = 'Acer rubrum Fisher newtest FRESH';
        $testArr['specificepithet'] = 'rubrum';
        $testArr['scientificnameauthorship'] = 'FISHER';
        $taxonEditorObj = new TaxonomyEditorManager();
        $result = $taxonEditorObj->splitScinameFromOccArr($testArr);

        $expectedResult = [];
        $expectedResult['base'] = 'Acer rubrum';
		$expectedResult['cultivarEpithet'] = '';
		$expectedResult['tradeName'] = '';
		$expectedResult['author'] = 'FISHER';
        $expectedResult['nonItal'] = 'newtest FRESH';

        $this->assertEquals($expectedResult, $result);
    }
    public function testSplitScinameFromOccArrWithSubsp() {
        $testArr = [];
        $testArr['sciname'] = 'Acer rubrum subsp. carolinianum';
        $testArr['specificepithet'] = 'rubrum';
        $testArr['scientificnameauthorship'] = '(Walter) W. Stone';
        $taxonEditorObj = new TaxonomyEditorManager();
        $result = $taxonEditorObj->splitScinameFromOccArr($testArr);

        $expectedResult = [];
        $expectedResult['base'] = 'Acer rubrum';
		$expectedResult['cultivarEpithet'] = '';
		$expectedResult['tradeName'] = '';
		$expectedResult['author'] = '(Walter) W. Stone';
        $expectedResult['nonItal'] = 'subsp. carolinianum';

        $this->assertEquals($expectedResult, $result);
    }
}
