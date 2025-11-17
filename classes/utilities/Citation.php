<?php
include_once($SERVER_ROOT . '/classes/utilities/GeneralUtil.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
Language::load('classes/utilities/Citation');

class Citation{

	public static function portal(){
		global $LANG, $DEFAULT_TITLE;

		echo $LANG['PUBLISHED_BY'] . ': ';
		if ($DEFAULT_TITLE) {
			echo $DEFAULT_TITLE;
		}
		else {
			echo $LANG['RESPONSIBLE_FOR'];
		};
		echo ' ' . $LANG['ACCESSED_THROUGH'] . ' ';
		if ($DEFAULT_TITLE) {
			echo $DEFAULT_TITLE;
		}
		else {
			echo $LANG['RESPONSIBLE_FOR'];
		};
		echo ' ' . $LANG['PORTAL'] . ' ' . GeneralUtil::getDomain() . ', ' . date('Y-m-d') . ').';
	}

	public static function collection($collName, $dwcaUrl, $recordID){
		global $LANG, $DEFAULT_TITLE;

		echo $collName . '. Occurrence dataset (ID: ' . $recordID . ') ' . $dwcaUrl . 'accessed via the ';
		if ($DEFAULT_TITLE) {
			echo $DEFAULT_TITLE;
		}
		else {
			echo $LANG['RESPONSIBLE_FOR'];
		};
		echo ' ' . $LANG['PORTAL'] . ' ' . GeneralUtil::getDomain() . ', ' . date('Y-m-d') . ').';
	}

	public static function GBIF($gbifTitle, $doi){
		global $LANG, $DEFAULT_TITLE;

		if ($DEFAULT_TITLE) {
			echo $DEFAULT_TITLE;
		}
		else {
			echo $LANG['RESPONSIBLE_FOR'];
		};

		echo ' (' . date('Y') . '). ' . $gbifTitle . '. Occurrence dataset https://doi.org/' . $doi . ' accessed via the ' ;

		if ($DEFAULT_TITLE) {
			echo $DEFAULT_TITLE;
		}
		else {
			echo $LANG['RESPONSIBLE_FOR'];
		};
		echo ' ' . GeneralUtil::getDomain() . ' on ' . date('Y-m-d') . ').';
	}
}
?>