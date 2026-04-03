<?php
/**
 *  Returns configuration properties. Most properties are returned from symbini.php config file, but will in the future will be returned directly from database
 */

class PortalProperties {

	public static function getTaxonomicAuthorities(){
		// List of taxonomic authorities currently supported within portal
		$supportedResources = array(
			'col'=>'Catalog of Life',
			'worms'=>'World Register of Marine Species',
			'bryonames' => 'The Bryophyte Nomenclator',
			'fdex'=>'Index Fungorum via F-Dex'
			//'tropicos'=>'TROPICOS',
			//'eol'=>'Encyclopedia of Life'
		);
		if(!isset($GLOBALS['TAXONOMIC_AUTHORITIES'])){
			//Only return CoL and WoRMS
			return array_intersect_key($supportedResources, array('col'=>'','worms'=>''));
		}
		return array_intersect_key($supportedResources, array_change_key_case($GLOBALS['TAXONOMIC_AUTHORITIES']));
	}

}
