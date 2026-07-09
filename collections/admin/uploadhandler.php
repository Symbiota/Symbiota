<?php
/*
 * Handler can be used to automate IPT or DwC-Archive data harvests based on saved upload profiles (tables: uploadspecparameters, uploadspecmap)
 * PHP must be setup to run via command line. Automate by adding call to handler as a cron job, or scheduled task.
 * Variables in order: collid (required), uspid (required), importImages (optional, 0 or 1, 1 = default), importIdentificationHistory (optional, 0 or 1, 0 = default), matchCatalogNumber (optional, 0 or 1, 0 = default), matchOtherCatalogNumbers (optional, 0 or 1, 0 = default)
 * ex: php uploadhandler.php 382 331 1
 * If data imports fail, check your php configuration variables. In particular, look at:
 * post_max_size, upload_max_filesize, default_socket_timeout, max_execution_time, max_input_time, and allow_url_fopen
 */

include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SpecUploadDwca.php');

if($argc){
	$collid = $argv[1];
	$uspid = $argv[2];

	if(is_numeric($collid) && is_numeric($uspid)){
		$importImage = 1;
		$importIdent = 0;
		$matchCatNum = 0;
		$matchOtherCatNum = 0;
		if($argc > 3 && is_numeric($argv[3])){
			$importImage = $argv[3];
			if($argc > 4 && is_numeric($argv[4])){
				$importIdent = $argv[4];
				if($argc > 5 && is_numeric($argv[5])){
					$matchCatNum = $argv[5];
					if($argc > 6 && is_numeric($argv[6])){
						$matchOtherCatNum = $argv[6];
					}
				}
			}
		}
		$harvestManager = new SpecUploadDwca();
		$harvestManager->setVerboseMode(2,'scheduledHarvests');
		$harvestManager->setCollId($collid);
		$harvestManager->setUspid($uspid);
		$harvestManager->setUploadType(8);
		$harvestManager->setIncludeImages($importImage);
		$harvestManager->setIncludeIdentificationHistory($importIdent);
		$harvestManager->setMatchCatalogNumber($matchCatNum);
		$harvestManager->setMatchOtherCatalogNumbers($matchOtherCatNum);

		$harvestManager->readUploadParameters();
		$harvestManager->loadFieldMap();
		$harvestManager->uploadFile();
		$harvestManager->uploadData(true);
	}
}
?>