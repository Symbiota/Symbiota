<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/OccurrenceDownload.php');
include_once($SERVER_ROOT . '/classes/OccurrenceMapManager.php');
include_once($SERVER_ROOT . '/classes/DwcArchiverCore.php');

$sourcePage = array_key_exists("sourcepage", $_REQUEST) ? $_REQUEST["sourcepage"] : "specimen";
$schema = array_key_exists("schema", $_REQUEST) ? $_REQUEST["schema"] : "symbiota";
$cSet = array_key_exists("cset", $_POST) ? $_POST["cset"] : '';
$isPublicSearch = (empty($_REQUEST['publicsearch'])) ? 0 : 1;		//Value false by default

$token = $_POST['downloadToken'] ?? null;
if ($token) {
	setcookie('downloadToken', $token, [
		'expires' => time() + 60,
		'path' => '/',
		'samesite' => 'Lax'
	]);
}

if ($schema == 'backup') {
	$collid = $_POST['collid'];
	if ($collid && is_numeric($collid)) {
		//check permissions due to sensitive localities not being redacted
		if ($IS_ADMIN || (array_key_exists('CollAdmin', $USER_RIGHTS) && in_array($collid, $USER_RIGHTS['CollAdmin']))) {
			$dwcaHandler = new DwcArchiverCore();
			$dwcaHandler->setSchemaType('backup');
			$dwcaHandler->setCharSetOut($cSet);
			$dwcaHandler->setVerboseMode(0);
			$dwcaHandler->setIncludeDets(1);
			$dwcaHandler->setIncludeImgs(1);
			$dwcaHandler->setIncludeAttributes(1);
			if ($dwcaHandler->hasMaterialSamples($collid)) $dwcaHandler->setIncludeMaterialSample(1);
			if ($dwcaHandler->hasIdentifiers($collid)) $dwcaHandler->setIncludeIdentifiers(1);
			if ($dwcaHandler->hasAssociations($collid)) $dwcaHandler->setIncludeAssociations(1);
			$dwcaHandler->setRedactLocalities(0);
			$dwcaHandler->setCollArr($collid);

			$archiveFile = $dwcaHandler->createDwcArchive();

			if ($archiveFile) {
				ob_start();
				ob_clean();
				ob_end_flush();
				header('Content-Description: Symbiota Occurrence Backup File (DwC-Archive data package)');
				header('Content-Type: application/zip');
				header('Content-Disposition: attachment; filename=' . basename($archiveFile));
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header('Content-Length: ' . filesize($archiveFile));
				//od_end_clean();
				readfile($archiveFile);
				unlink($archiveFile);
			} else {
				$errMsg = $dwcaHandler->getErrorMessage();
				if($errMsg) echo $errMsg;
				else echo 'ERROR creating output file. Query probably did not include any records.';
			}
		}
	}
} else {
	$zip = (array_key_exists('zip', $_POST) ? $_POST['zip'] : 0);
	$allowedFormats = ['csv', 'tab'];
	$formatFromPost = (array_key_exists('format', $_POST) ? $_POST['format'] : 'csv');
	$format = in_array($formatFromPost, $allowedFormats) ? $formatFromPost : 'csv';
	$extended = (array_key_exists('extended', $_POST) ? $_POST['extended'] : 0);

	$redactLocalities = 1;
	$rareReaderArr = array();
	if ($IS_ADMIN || array_key_exists("CollAdmin", $USER_RIGHTS)) {
		$redactLocalities = 0;
	} elseif (array_key_exists("RareSppAdmin", $USER_RIGHTS) || array_key_exists("RareSppReadAll", $USER_RIGHTS)) {
		$redactLocalities = 0;
	} else {
		if (array_key_exists('CollEditor', $USER_RIGHTS)) {
			$rareReaderArr = $USER_RIGHTS['CollEditor'];
		}
		if (array_key_exists('RareSppReader', $USER_RIGHTS)) {
			$rareReaderArr = array_unique(array_merge($rareReaderArr, $USER_RIGHTS['RareSppReader']));
		}
	}
	$occurManager = null;
	if ($sourcePage == 'specimen') {
		//Search variables are set with the initiation of OccurrenceManager object
		$occurManager = new OccurrenceManager();
	} else {
		$occurManager = new OccurrenceMapManager();
	}
	$occurManager->getSearchTerm('customfield');
	$occurManager->setApplyFullProtections(false); //Full security protections for downloads are handled within the DwcArchiverCore class
	if ($schema == 'georef') {
		$dlManager = new OccurrenceDownload();
		if($isPublicSearch){
			$dlManager->setIsPublicDownload($isPublicSearch);
			$dlManager->setSqlWhere($occurManager->getSqlWhere());
		}
		$dlManager->setSchemaType($schema);
		$dlManager->setExtended($extended);
		$dlManager->setCharSetOut($cSet);
		$dlManager->setDelimiter($format);
		$dlManager->setZipFile($zip);
		$dlManager->addCondition('decimalLatitude', 'NOT_NULL', '');
		$dlManager->addCondition('decimalLongitude', 'NOT_NULL', '');
		if (array_key_exists('targetcollid', $_POST) && $_POST['targetcollid']) {
			$dlManager->addCondition('collid', 'EQUALS', $_POST['targetcollid']);
		}
		if (array_key_exists('processingstatus', $_POST) && $_POST['processingstatus']) {
			$dlManager->addCondition('processingstatus', 'EQUALS', $_POST['processingstatus']);
		}
		if (array_key_exists('customfield1', $_POST) && $_POST['customfield1']) {
			$dlManager->addCondition($_POST['customfield1'], $_POST['customtype1'], $_POST['customvalue1']);
		}
		$dlManager->downloadData();
	} elseif ($schema == 'checklist') {
		$dlManager = new OccurrenceDownload();
		if($isPublicSearch){
			$dlManager->setSqlWhere($occurManager->getSqlWhere());
		}
		$dlManager->setSchemaType($schema);
		$dlManager->setCharSetOut($cSet);
		$dlManager->setDelimiter($format);
		$dlManager->setZipFile($zip);
		$dlManager->setTaxonFilter(array_key_exists("taxonFilterCode", $_POST) ? $_POST["taxonFilterCode"] : 0);
		$dlManager->downloadData();
	} else {
		$dwcaHandler = new DwcArchiverCore();
		$dwcaHandler->setVerboseMode(0);
		if ($schema == 'coge') {
			$dwcaHandler->setCollArr($_POST['collid']);
			$dwcaHandler->setCharSetOut('UTF-8');
			$dwcaHandler->setSchemaType('coge');
			$dwcaHandler->setExtended(false);
			$dwcaHandler->setDelimiter('csv');
			$dwcaHandler->setRedactLocalities(0);
			$dwcaHandler->setIncludeDets(0);
			$dwcaHandler->setIncludeImgs(0);
			$dwcaHandler->setIncludeAttributes(0);
			$dwcaHandler->setIncludeMaterialSample(0);
			$dwcaHandler->setIncludeIdentifiers(0);
			$dwcaHandler->setIncludeAssociations(0);
			$dwcaHandler->addCondition('catalognumber', 'NOT_NULL');
			$dwcaHandler->addCondition('locality', 'NOT_NULL');
			if (array_key_exists('processingstatus', $_POST) && $_POST['processingstatus']) {
				$dwcaHandler->addCondition('processingstatus', 'EQUALS', $_POST['processingstatus']);
			}
			for ($i = 1; $i < 4; $i++) {
				if (array_key_exists('customfield' . $i, $_POST) && $_POST['customfield' . $i]) {
					$dwcaHandler->addCondition($_POST['customfield' . $i], $_POST['customtype' . $i], $_POST['customvalue' . $i]);
				}
			}
		} else {
			//Is an occurrence download
			if ($isPublicSearch) $dwcaHandler->setIsPublicDownload($isPublicSearch);
			$dwcaHandler->setCharSetOut($cSet);
			$dwcaHandler->setSchemaType($schema);
			$dwcaHandler->setExtended($extended);
			$dwcaHandler->setDelimiter($format);
			$dwcaHandler->setRedactLocalities($redactLocalities);
			if ($rareReaderArr) $dwcaHandler->setRareReaderArr($rareReaderArr);

			if(isset($_POST['source']) && $_POST['source'] == 'collection_exporter'){
				//Request is coming from exporter.php, thus we need to do some custom adjustments
				$dwcaHandler->setCollArr($_POST['targetcollid']);
				if (!empty($_POST['processingstatus'])) {
					$dwcaHandler->addCondition('processingstatus', 'EQUALS', $_POST['processingstatus']);
				}
				for ($x = 1; $x < 4; $x++){
					if (!empty($_POST['customfield' . $x])) {
						$dwcaHandler->addCondition($_POST['customfield' . $x], $_POST['customtype' . $x], $_POST['customvalue' . $x]);
					}
				}
				if (!empty($_POST['stateid'])) {
					$dwcaHandler->addCondition('stateid', 'EQUALS', $_POST['stateid']);
				} elseif (array_key_exists('traitid', $_POST) && $_POST['traitid']) {
					$dwcaHandler->addCondition('traitid', 'EQUALS', $_POST['traitid']);
				}
				if (isset($_POST['newrecs']) && $_POST['newrecs'] == 1) {
					$dwcaHandler->addCondition('dbpk', 'IS_NULL');
					$dwcaHandler->addCondition('catalognumber', 'NOT_NULL');
				}
			}
			$dwcaHandler->setCustomWhereSql($occurManager->getSqlWhere());

			// Added for Occurrence Table Display Editor Download Functionality
			for ($i = 1; $i  < 10; $i ++) {
				if ($occurManager->getSearchTerm('customfield' . $i)) {
					$dwcaHandler->addCondition(
						$occurManager->getSearchTerm('customfield' . $i),
						$occurManager->getSearchTerm('customtype' . $i),
						$occurManager->getSearchTerm('customvalue' . $i)
					);
				}
			}

			// Traits Support
			if ($occurManager->getSearchTerm('stateid')) {
				$dwcaHandler->addCondition('stateid', 'EQUALS', $occurManager->getSearchTerm('stateid'));
			} elseif ($occurManager->getSearchTerm('traitid')) {
				$dwcaHandler->addCondition('traitid', 'EQUALS', $occurManager->getSearchTerm('traitid'));
			}
			if ($occurManager->getSearchTerm('polygons')) {
				$dwcaHandler->setPolygons($occurManager->getSearchTerm('polygons'));
			}
			$dwcaHandler->setPaleoWithSql($occurManager->getPaleoSqlWith());
		}

		$outputFile = null;
		if ($zip) {
			//Ouput file is a zip file
			$includeIdent = (array_key_exists('identifications', $_POST) ? 1 : 0);
			$dwcaHandler->setIncludeDets($includeIdent);
			$includeImages = (array_key_exists('images', $_POST) ? 1 : 0);
			$dwcaHandler->setIncludeImgs($includeImages);
			$includeAttributes = (array_key_exists('attributes', $_POST) ? 1 : 0);
			$dwcaHandler->setIncludeAttributes($includeAttributes);
			$includeMaterialSample = (array_key_exists('materialsample', $_POST) ? 1 : 0);
			$dwcaHandler->setIncludeMaterialSample($includeMaterialSample);
			$includeIdentifiers = (array_key_exists('identifiers', $_POST) ? 1 : 0);
			$dwcaHandler->setIncludeIdentifiers($includeIdentifiers);
			$includeAssociations = (array_key_exists('associations', $_POST) ? 1 : 0);
			$dwcaHandler->setIncludeAssociations($includeAssociations);

			$outputFile = $dwcaHandler->createDwcArchive();
		} else {
			//Output file is a flat occurrence file (not a zip file)
			$outputFile = $dwcaHandler->getOccurrenceFile();
		}
		if ($outputFile) {
			// ob_start();
			$contentDesc = '';
			if ($schema == 'dwc') {
				$contentDesc = 'Darwin Core ';
			} else {
				$contentDesc = 'Symbiota ';
			}
			$contentDesc .= 'Occurrence ';
			if ($zip) {
				$contentDesc .= 'Archive ';
			}
			$contentDesc .= 'File';
			ob_start();
			ob_clean();
			ob_end_flush();
			header('Content-Description: ' . $contentDesc);

			if ($zip) {
				header('Content-Type: application/zip');
			} elseif ($format == 'csv') {
				header('Content-Type: text/csv; charset=' . $CHARSET);
			} else {
				header('Content-Type: text/html; charset=' . $CHARSET);
			}

			header('Content-Disposition: attachment; filename=' . basename($outputFile));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($outputFile));
			// ob_clean();
			flush();
			//od_end_clean();
			readfile($outputFile);
			unlink($outputFile);
		} else {
			header("Content-type: text/plain");
			header("Content-Disposition: attachment; filename=NoData.txt");
			if($dwcaHandler->getErrorMessage()) echo $dwcaHandler->getErrorMessage();
			else echo 'The query failed to return records. Please modify query criteria and try again.';
		}
	}
}
