<?php
include_once($SERVER_ROOT.'/classes/SpecUploadBase.php');
class SpecUploadINat extends SpecUploadBase{

	private $iNatData;
	private $APIToken;
	private $addLink;
	private $fullImport;

	// ID on iNaturalist of the observation field to store a URL linking back to Symbiota
	// - URL = 4565
	// - Link to Symbiota Record = 16685
	// - A portal-specific one could be added, e.g., SEINet Record
	private $urlFieldID = 16685;

	function __construct() {
 		parent::__construct();
	}

	public function __destruct(){
 		parent::__destruct();
	}

	public function getINatData($data){

		// Decode the JSON from the iNat import into an array
		$this->iNatData = json_decode($data, true);

		//json_decode should return null if it cannot decode
		if($this->iNatData ){
			$this->occurSourceArr = $this->getHeaderArr();
			$this->setImageSourceArr();
		}
	}

	// Cycles through the iNaturalist data array and gets all the data fields present in any of the records
	private function getHeaderArr(){
		
		$headerArr = Array();

		//Grab all the header terms from the data
		if(is_array($this->iNatData)) {
			foreach ( (array) $this->iNatData as $record) {
				$headerArr = array_merge($headerArr, array_diff(array_keys($record), $headerArr));
			}
		}

		// Terms to skip for mapping
		$skipKeys = array("thumbnailURL", "images", "binomialName", "uuid", "iNatID", "iNatURL", "iNatUsername", "dbpk");

		// Remove terms to skip for mapping
		$headerArr = array_diff($headerArr, $skipKeys);

		return $headerArr;
	}

	// Cycles through the iNaturalist data array and gets all the image data fields present in any of the records
	private function setImageSourceArr(){

		//Grab header terms
		$headerArr = Array();

		if(is_array($this->iNatData)) {
			foreach ($this->iNatData as $record) {

				// TODO: may be able to skip this and assume all images will have all the same data
				foreach ($record['images'] as $image) {
					$headerArr = array_merge($headerArr, array_diff(array_keys($image), $headerArr));
				}
			}
		}

		// Terms to skip for mapping
		$skipKeys = array();

		// Remove any skipped terms
		$headerArr = array_diff($headerArr, $skipKeys);

		$this->imageSourceArr = $headerArr;
		return true;
	}


	// Upload the iNaturalist data into the upload___temp tables
	public function uploadData($finalTransfer){

		// Check to make sure data is present
		if($this->iNatData) {
			set_time_limit(7200);
		 	ini_set("max_input_time",240);

			$this->outputMsg('<li>Initiating import from iNaturalist observations.</li>');
		 	//First, delete all records in uploadspectemp table associated with this collection
			$this->prepUploadData();

			//Grab data
			$this->transferCount = 0;
			$this->outputMsg('<li>Beginning to load records...</li>',1);
			foreach($this->iNatData as $recordArr) {

				$recMap = Array();

				// Make all of the data fields lowercase
				$recordArr = array_change_key_case($recordArr, CASE_LOWER);
				
				// Convert the observation data into a Symbiota style array
				foreach($this->occurFieldMap as $symbField => $sMap){
					if(array_key_exists($sMap['field'],$recordArr)){
						$valueStr = $recordArr[$sMap['field']];
						$recMap[$symbField] = $valueStr;
					}
				}

				//If dbpk does not exist, set from the iNat record uuid
				if(!isset($recMap['dbpk']) || !$recMap['dbpk']) $recMap['dbpk'] = $recordArr['uuid'];

				// Load the observation data into the uploadspectemp table
				$this->loadRecord($recMap);
				
				// If images should be included, load the image data into uploadimagetemp
				if($this->includeImages && isset($recordArr['images'])){

					foreach ($recordArr['images'] as $image) {

						// Construct an image map array for upload
						$imgMap = $this->processImage($image);

						// Add the dbpk key used for the occurrence data
						$imgMap['dbpk'] = $recMap['dbpk'];

						// Load the image data into uploadimagetemp
						$this->loadMediaRecord($imgMap);
						unset($imgMap);
					}
				}
				unset($recMap);
			}

			// Run record cleaning steps on the upload___temp tables
			$this->cleanUpload();
		}
	}

	// Abbreviated function from SpecUploadBase, but adds dbpk matching, and removes records already in portal if not replacing data
	protected function cleanUpload(){

		// For iNaturalist records, update existing records based on matching dbpk
		$this->updateOccidMatchingDbpk();

		// If fullImport is not set, don't import records that are already in the portal or replace data
		if(!$this->fullImport) $this->removeExistingRecords();

		//Perform general cleaning and parsing tasks
		$this->recordCleaningStage1();

		$this->cleanImages();

		//Reset $treansferCnt so that count is accurate since some records may have been deleted due to data integrety issues
		$this->setTransferCount();
		$this->setIdentTransferCount(); // Not used at this point, but could be in the future. 
		$this->setImageTransferCount();

	}

	// Removes records that already exist in the portal from the upload___temp tables (when not updating data)
	private function removeExistingRecords() {

		$this->outputMsg('<li style="margin-left:10px;">Avoiding the import of iNaturalist records already present...</li> ');

		// Delete from uploadimagetemp
		$sql = 'DELETE ui.* FROM uploadimagetemp ui INNER JOIN uploadspectemp u ON ui.dbpk = u.dbpk WHERE u.dbpk IS NOT NULL AND u.occid IS NOT NULL;';
		if(!$this->conn->query($sql)){
			$this->outputMsg('<li style="margin-left:20px;">ERROR not importing images for records already present in the portal '.$this->conn->error.'</li> ');
		}

		// Delete from uploadkeyvaluetemp
		$sql = 'DELETE uk.* FROM uploadkeyvaluetemp uk INNER JOIN uploadspectemp u ON uk.dbpk = u.dbpk WHERE u.dbpk IS NOT NULL AND u.occid IS NOT NULL;';
		if(!$this->conn->query($sql)){
			$this->outputMsg('<li style="margin-left:20px;">ERROR not importing key-values for records already present in the portal '.$this->conn->error.'</li> ');
		}

		// Delete from uploadspectemp
		$sql = 'DELETE FROM uploadspectemp WHERE dbpk IS NOT NULL AND occid IS NOT NULL;';
		if(!$rs = $this->conn->query($sql)){
			$this->outputMsg('<li style="margin-left:20px;">ERROR not importing records already present in the portal '.$this->conn->error.'</li> ');
		} else {
			$this->outputMsg('<li style="margin-left:20px;">Not importing ' . $this->conn->affected_rows . ' record' . ($this->conn->affected_rows == 1 ? '' : 's') . ' already present in the portal</li> ');
		}
	}

	// Runs to transfer from the upload___temp tables to the real Symbiota ones
	// Same as the function in SpecUploadBase, but adds addSymbiotaLinkbacks();
	public function finalTransfer(){
		$this->recordCleaningStage2();
		$this->transferOccurrences();
		$this->transferIdentificationHistory();
		$this->transferImages();
		// if($GLOBALS['QUICK_HOST_ENTRY_IS_ACTIVE']) $this->transferHostAssociations();
		$this->transferAssociatedOccurrences();
		// Add Symbiota linkbacks to iNat observations if specified. 
		if($this->addLink) $this->addSymbiotaLinkbacks();
		$this->finalCleanup();
		$this->outputMsg('<li style="">Upload Procedure Complete ('.date('Y-m-d h:i:s A').')!</li>');
		$this->outputMsg(' ');
	}

	// Convert the images data into a Symbiota style array
	private function processImage($imageArr){

		$imgMap = Array();

		// Make all of the image fields lowercase
		$imageArr = array_change_key_case($imageArr, CASE_LOWER);

		foreach($this->imageFieldMap as $symbField => $sMap){
			if(array_key_exists($sMap['field'],$imageArr)){
				$valueStr = $imageArr[$sMap['field']];
				$imgMap[$symbField] = $valueStr;
			}
		}
		return $imgMap;
	}

	// Run if addLinks is enabled. After records are imported, adds symbiota links to those iNaturalist records
	private function addSymbiotaLinkbacks(){

		$this->outputMsg('<li style="margin-left:10px; color: blue;" id="inatlinks">Adding Symbiota linkbacks to iNaturalist observations...</li>');

		// Adds a javascript element to do all the work, and includes an array of Symbiota links to add.
		$script = '
		<script src="https://herbarium.science.oregonstate.edu/iNat/iNatJS/inatjs-class.js" type="text/javascript"></script>
		<script>

			// ID on iNaturalist of the observation field to store a URL linking back to Symbiota
			const urlFieldID = ' . $this->urlFieldID . ';

			// Make a new class instance
			const iNat = new iNatJS();

			// Save a JSON object of occids and links
			const links = ' . json_encode($this->iNatLinkbacks) . ';

			// Add links to iNaturalist Observations
			function addLinks(){

				// Iterate through each of the observations to add a link to
				Object.entries(links).forEach(([occid, obsID]) => {

					// Get server root
					let serverRoot = window.location.href.slice(0, window.location.href.indexOf("collections/admin"));

					// Construct url
					let url = serverRoot + "collections/individual/index.php?occid=" + occid;

					// Add the link as an observation field
					iNat.addObsField(obsID, urlFieldID, url, function(success, response) { 

						if(success){
							// Link added successfully
						} else {

							// Problem adding the link, log an error
							let errormsg = "Error adding Symbiota url to iNaturalist observation for <a href=\"https://www.inaturalist.org/observations/" + obsID + "\">" + obsID + "</a>";
							$("#inatlinks").after("<li style=\"margin-left:20px; color: red;\">" + errormsg + "</li>");
						}
					});

					// Check the API submission queue until all the requests have completed
					iNat.checkiNatQueue(100, function(status) {

						// All requests completed, update the information printed out
						if (status) {
							$("#inatlinks").css("color", "");
							$("#inatlinks").html("Successfully added Symbiota links to iNaturalist observations");
						}
					});
				});
			}

			// Check authentication. We cannot add links without being authenticated
			iNat.checkAuthentication("' . $this->APIToken . '", function(success, data) {

				if (success) {
					// Authenticated, so add the links
					addLinks();
				} else {
					// Not authenticated, so print out an error
					$("#inatlinks").css("color", red);
					$("#inatlinks").html("Symbiota linkbacks not added. User is no longer authenticated on iNaturalist.");
				}
			});
		</script>';
		$this->outputMsg($script);
	}

	// Class variable setters

	public function setApiToken($token){
		$this->APIToken = $token;
	}

	public function setAddLink($bool){
		$this->addLink = $bool;
	}

	public function setFullImport($bool){
		$this->fullImport = $bool;
	}
}