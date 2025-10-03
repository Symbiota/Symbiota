<?php
// Only needed for symbiotaAssociations version number.
include_once($SERVER_ROOT . '/classes/utilities/OccurrenceUtil.php');

class DwcArchiverOccurrence extends Manager{

	private $exportID;
	private $occurDefArr = array();
	private $schemaType;
	private $extended = false;
	private $includePaleo = false;
	private $includeAssocSeq = false;
	private $includeAcceptedNameUsage = false;
	private $relationshipArr;
	private $paleoGtsArr = null;
	private $serverDomain;

	public function __construct($conn){
		$this->conn = $conn;
	}

	public function __destruct(){
	}

	public function getOccurrenceArr(){
		if($this->schemaType == 'pensoft') $this->occurDefArr['fields']['Taxon_Local_ID'] = 'ctl.tid AS Taxon_Local_ID';
		else $this->occurDefArr['fields']['id'] = 'o.occid';
		$this->occurDefArr['terms']['institutionCode'] = 'http://rs.tdwg.org/dwc/terms/institutionCode';
		$this->occurDefArr['fields']['institutionCode'] = 'IFNULL(o.institutionCode,c.institutionCode) AS institutionCode';
		$this->occurDefArr['terms']['collectionCode'] = 'http://rs.tdwg.org/dwc/terms/collectionCode';
		$this->occurDefArr['fields']['collectionCode'] = 'IFNULL(o.collectionCode,c.collectionCode) AS collectionCode';
		$this->occurDefArr['terms']['ownerInstitutionCode'] = 'http://rs.tdwg.org/dwc/terms/ownerInstitutionCode';
		$this->occurDefArr['fields']['ownerInstitutionCode'] = 'o.ownerInstitutionCode';
		$this->occurDefArr['terms']['collectionID'] = 'http://rs.tdwg.org/dwc/terms/collectionID';
		$this->occurDefArr['fields']['collectionID'] = 'IFNULL(o.collectionID, c.collectionguid) AS collectionID';
		$this->occurDefArr['terms']['basisOfRecord'] = 'http://rs.tdwg.org/dwc/terms/basisOfRecord';
		$this->occurDefArr['fields']['basisOfRecord'] = 'o.basisOfRecord';
		$this->occurDefArr['terms']['occurrenceID'] = 'http://rs.tdwg.org/dwc/terms/occurrenceID';
		$this->occurDefArr['fields']['occurrenceID'] = 'o.occurrenceID';
		$this->occurDefArr['terms']['catalogNumber'] = 'http://rs.tdwg.org/dwc/terms/catalogNumber';
		$this->occurDefArr['fields']['catalogNumber'] = 'o.catalogNumber';
		$this->occurDefArr['terms']['otherCatalogNumbers'] = 'http://rs.tdwg.org/dwc/terms/otherCatalogNumbers';
		$this->occurDefArr['fields']['otherCatalogNumbers'] = 'o.otherCatalogNumbers';
		$this->occurDefArr['terms']['higherClassification'] = 'http://rs.tdwg.org/dwc/terms/higherClassification';
		$this->occurDefArr['fields']['higherClassification'] = 'x.higherClassification';
		$this->occurDefArr['terms']['kingdom'] = 'http://rs.tdwg.org/dwc/terms/kingdom';
		$this->occurDefArr['fields']['kingdom'] = 'x.kingdom';
		$this->occurDefArr['terms']['phylum'] = 'http://rs.tdwg.org/dwc/terms/phylum';
		$this->occurDefArr['fields']['phylum'] = 'x.phylum';
		$this->occurDefArr['terms']['class'] = 'http://rs.tdwg.org/dwc/terms/class';
		$this->occurDefArr['fields']['class'] = 'x.class';
		$this->occurDefArr['terms']['order'] = 'http://rs.tdwg.org/dwc/terms/order';
		$this->occurDefArr['fields']['order'] = 'x.order';
		$this->occurDefArr['terms']['family'] = 'http://rs.tdwg.org/dwc/terms/family';
		$this->occurDefArr['fields']['family'] = 'IFNULL(o.family,x.family) AS family';
		$this->occurDefArr['terms']['scientificName'] = 'http://rs.tdwg.org/dwc/terms/scientificName';
		$this->occurDefArr['fields']['scientificName'] = 'o.sciname AS scientificName';
		$this->occurDefArr['terms']['taxonID'] = 'http://rs.tdwg.org/dwc/terms/taxonID';
		$this->occurDefArr['fields']['taxonID'] = 'o.tidinterpreted as taxonID';
		$this->occurDefArr['terms']['scientificNameAuthorship'] = 'http://rs.tdwg.org/dwc/terms/scientificNameAuthorship';
		$this->occurDefArr['fields']['scientificNameAuthorship'] = 'IFNULL(o.scientificNameAuthorship, x.scientificNameAuthorship) AS scientificNameAuthorship';
		$this->occurDefArr['terms']['genus'] = 'http://rs.tdwg.org/dwc/terms/genus';
		$this->occurDefArr['fields']['genus'] = 'x.genus';
		$this->occurDefArr['terms']['subgenus'] = 'http://rs.tdwg.org/dwc/terms/subgenus';
		$this->occurDefArr['fields']['subgenus'] = 'x.subgenus';
		$this->occurDefArr['terms']['specificEpithet'] = 'http://rs.tdwg.org/dwc/terms/specificEpithet';
		$this->occurDefArr['fields']['specificEpithet'] = 'x.specificEpithet';
		$this->occurDefArr['terms']['verbatimTaxonRank'] = 'http://rs.tdwg.org/dwc/terms/verbatimTaxonRank';
		$this->occurDefArr['fields']['verbatimTaxonRank'] = 'x.verbatimTaxonRank';
		$this->occurDefArr['terms']['infraspecificEpithet'] = 'http://rs.tdwg.org/dwc/terms/infraspecificEpithet';
		$this->occurDefArr['fields']['infraspecificEpithet'] = 'x.infraspecificEpithet';
		$this->occurDefArr['terms']['cultivarEpithet'] = 'http://rs.tdwg.org/dwc/terms/cultivarEpithet';
		$this->occurDefArr['fields']['cultivarEpithet'] = 'x.cultivarEpithet';
		$this->occurDefArr['terms']['tradeName'] = 'http://rs.tdwg.org/dwc/terms/tradeName';
		$this->occurDefArr['fields']['tradeName'] = 'x.tradeName';
		$this->occurDefArr['terms']['taxonRank'] = 'http://rs.tdwg.org/dwc/terms/taxonRank';
		$this->occurDefArr['fields']['taxonRank'] = 'x.taxonRank';
		$this->occurDefArr['terms']['identifiedBy'] = 'http://rs.tdwg.org/dwc/terms/identifiedBy';
 		$this->occurDefArr['fields']['identifiedBy'] = 'o.identifiedBy';
 		$this->occurDefArr['terms']['dateIdentified'] = 'http://rs.tdwg.org/dwc/terms/dateIdentified';
 		$this->occurDefArr['fields']['dateIdentified'] = 'o.dateIdentified';
 		$this->occurDefArr['terms']['identificationReferences'] = 'http://rs.tdwg.org/dwc/terms/identificationReferences';
 		$this->occurDefArr['fields']['identificationReferences'] = 'o.identificationReferences';
 		$this->occurDefArr['terms']['identificationRemarks'] = 'http://rs.tdwg.org/dwc/terms/identificationRemarks';
 		$this->occurDefArr['fields']['identificationRemarks'] = 'o.identificationRemarks';
 		$this->occurDefArr['terms']['taxonRemarks'] = 'http://rs.tdwg.org/dwc/terms/taxonRemarks';
 		$this->occurDefArr['fields']['taxonRemarks'] = 'o.taxonRemarks';
 		$this->occurDefArr['terms']['identificationQualifier'] = 'http://rs.tdwg.org/dwc/terms/identificationQualifier';
 		$this->occurDefArr['fields']['identificationQualifier'] = 'o.identificationQualifier';
		if($this->includeAcceptedNameUsage) {
			$this->occurDefArr['terms']['acceptedNameUsage'] = 'http://rs.tdwg.org/dwc/terms/acceptedNameUsage';
			$this->occurDefArr['fields']['acceptedNameUsage'] = 'x.acceptedNameUsage';
			$this->occurDefArr['terms']['acceptedNameUsageAuthorship'] = '';
			$this->occurDefArr['fields']['acceptedNameUsageAuthorship'] = 'x.acceptedNameUsageAuthorship';
			$this->occurDefArr['terms']['acceptedNameUsageID'] = 'http://rs.tdwg.org/dwc/terms/acceptedNameUsageID';
			$this->occurDefArr['fields']['acceptedNameUsageID'] = 'x.acceptedNameUsageID';
		}
		$this->occurDefArr['terms']['typeStatus'] = 'http://rs.tdwg.org/dwc/terms/typeStatus';
		$this->occurDefArr['fields']['typeStatus'] = 'o.typeStatus';
		$this->occurDefArr['terms']['recordedBy'] = 'http://rs.tdwg.org/dwc/terms/recordedBy';
		$this->occurDefArr['fields']['recordedBy'] = 'o.recordedBy';
		$this->occurDefArr['terms']['associatedCollectors'] = 'https://symbiota.org/terms/associatedCollectors';
		$this->occurDefArr['fields']['associatedCollectors'] = 'o.associatedCollectors';
		$this->occurDefArr['terms']['recordNumber'] = 'http://rs.tdwg.org/dwc/terms/recordNumber';
		$this->occurDefArr['fields']['recordNumber'] = 'o.recordNumber';
		$this->occurDefArr['terms']['eventDate'] = 'http://rs.tdwg.org/dwc/terms/eventDate';
		$this->occurDefArr['fields']['eventDate'] = 'o.eventDate';
		$this->occurDefArr['terms']['eventDate2'] = 'https://symbiota.org/terms/eventDate2';
		$this->occurDefArr['fields']['eventDate2'] = 'o.eventDate2';
		$this->occurDefArr['terms']['year'] = 'http://rs.tdwg.org/dwc/terms/year';
		$this->occurDefArr['fields']['year'] = 'o.year';
		$this->occurDefArr['terms']['month'] = 'http://rs.tdwg.org/dwc/terms/month';
		$this->occurDefArr['fields']['month'] = 'o.month';
		$this->occurDefArr['terms']['day'] = 'http://rs.tdwg.org/dwc/terms/day';
		$this->occurDefArr['fields']['day'] = 'o.day';
		$this->occurDefArr['terms']['startDayOfYear'] = 'http://rs.tdwg.org/dwc/terms/startDayOfYear';
		$this->occurDefArr['fields']['startDayOfYear'] = 'o.startDayOfYear';
		$this->occurDefArr['terms']['endDayOfYear'] = 'http://rs.tdwg.org/dwc/terms/endDayOfYear';
		$this->occurDefArr['fields']['endDayOfYear'] = 'o.endDayOfYear';
		$this->occurDefArr['terms']['verbatimEventDate'] = 'http://rs.tdwg.org/dwc/terms/verbatimEventDate';
		$this->occurDefArr['fields']['verbatimEventDate'] = 'o.verbatimEventDate';
		$this->occurDefArr['terms']['occurrenceRemarks'] = 'http://rs.tdwg.org/dwc/terms/occurrenceRemarks';
		$this->occurDefArr['fields']['occurrenceRemarks'] = 'o.occurrenceRemarks';
		$this->occurDefArr['terms']['habitat'] = 'http://rs.tdwg.org/dwc/terms/habitat';
		$this->occurDefArr['fields']['habitat'] = 'o.habitat';
		$this->occurDefArr['terms']['substrate'] = 'https://symbiota.org/terms/substrate';
		$this->occurDefArr['fields']['substrate'] = 'o.substrate';
		$this->occurDefArr['terms']['verbatimAttributes'] = 'https://symbiota.org/terms/verbatimAttributes';
		$this->occurDefArr['fields']['verbatimAttributes'] = 'o.verbatimAttributes';
		$this->occurDefArr['terms']['behavior'] = 'http://rs.tdwg.org/dwc/terms/behavior';
		$this->occurDefArr['fields']['behavior'] = 'o.behavior';
		$this->occurDefArr['terms']['vitality'] = 'http://rs.tdwg.org/dwc/terms/vitality';
		$this->occurDefArr['fields']['vitality'] = 'o.vitality';
		$this->occurDefArr['terms']['fieldNumber'] = 'http://rs.tdwg.org/dwc/terms/fieldNumber';
		$this->occurDefArr['fields']['fieldNumber'] = 'o.fieldNumber';
		$this->occurDefArr['terms']['eventID'] = 'http://rs.tdwg.org/dwc/terms/eventID';
		$this->occurDefArr['fields']['eventID'] = 'o.eventID';
		$this->occurDefArr['terms']['informationWithheld'] = 'http://rs.tdwg.org/dwc/terms/informationWithheld';
		$this->occurDefArr['fields']['informationWithheld'] = 'o.informationWithheld';
		$this->occurDefArr['terms']['dataGeneralizations'] = 'http://rs.tdwg.org/dwc/terms/dataGeneralizations';
		$this->occurDefArr['fields']['dataGeneralizations'] = 'o.dataGeneralizations';
		$this->occurDefArr['terms']['dynamicProperties'] = 'http://rs.tdwg.org/dwc/terms/dynamicProperties';
		$this->occurDefArr['fields']['dynamicProperties'] = 'o.dynamicProperties';
		//$this->occurDefArr['terms']['associatedOccurrences'] = 'http://rs.tdwg.org/dwc/terms/associatedOccurrences';
		//$this->occurDefArr['fields']['associatedOccurrences'] = '';
		$this->occurDefArr['terms']['associatedSequences'] = 'http://rs.tdwg.org/dwc/terms/associatedSequences';
		$this->occurDefArr['fields']['associatedSequences'] = '';
		$this->occurDefArr['terms']['associatedTaxa'] = 'http://rs.tdwg.org/dwc/terms/associatedTaxa';
		$this->occurDefArr['fields']['associatedTaxa'] = 'o.associatedTaxa';
		$this->occurDefArr['terms']['reproductiveCondition'] = 'http://rs.tdwg.org/dwc/terms/reproductiveCondition';
		$this->occurDefArr['fields']['reproductiveCondition'] = 'o.reproductiveCondition';
		$this->occurDefArr['terms']['establishmentMeans'] = 'http://rs.tdwg.org/dwc/terms/establishmentMeans';
		$this->occurDefArr['fields']['establishmentMeans'] = 'o.establishmentMeans';
		$this->occurDefArr['terms']['cultivationStatus'] = 'https://symbiota.org/terms/cultivationStatus';
		$this->occurDefArr['fields']['cultivationStatus'] = 'o.cultivationStatus';
		$this->occurDefArr['terms']['lifeStage'] = 'http://rs.tdwg.org/dwc/terms/lifeStage';
		$this->occurDefArr['fields']['lifeStage'] = 'o.lifeStage';
		$this->occurDefArr['terms']['sex'] = 'http://rs.tdwg.org/dwc/terms/sex';
		$this->occurDefArr['fields']['sex'] = 'o.sex';
		$this->occurDefArr['terms']['individualCount'] = 'http://rs.tdwg.org/dwc/terms/individualCount';
		$this->occurDefArr['fields']['individualCount'] = 'CASE WHEN o.individualCount REGEXP("(^[0-9]+$)") THEN o.individualCount ELSE NULL END AS individualCount';
		$this->occurDefArr['terms']['samplingProtocol'] = 'http://rs.tdwg.org/dwc/terms/samplingProtocol';
		$this->occurDefArr['fields']['samplingProtocol'] = 'o.samplingProtocol';
		//$this->occurDefArr['terms']['samplingEffort'] = 'http://rs.tdwg.org/dwc/terms/samplingEffort';
		//$this->occurDefArr['fields']['samplingEffort'] = 'o.samplingEffort';
		$this->occurDefArr['terms']['preparations'] = 'http://rs.tdwg.org/dwc/terms/preparations';
		$this->occurDefArr['fields']['preparations'] = 'o.preparations';
		$this->occurDefArr['terms']['locationID'] = 'http://rs.tdwg.org/dwc/terms/locationID';
		$this->occurDefArr['fields']['locationID'] = 'o.locationID';
		$this->occurDefArr['terms']['continent'] = 'http://rs.tdwg.org/dwc/terms/continent';
		$this->occurDefArr['fields']['continent'] = 'o.continent';
		$this->occurDefArr['terms']['waterBody'] = 'http://rs.tdwg.org/dwc/terms/waterBody';
		$this->occurDefArr['fields']['waterBody'] = 'o.waterBody';
		$this->occurDefArr['terms']['islandGroup'] = 'http://rs.tdwg.org/dwc/terms/islandGroup';
		$this->occurDefArr['fields']['islandGroup'] = 'o.islandGroup';
		$this->occurDefArr['terms']['island'] = 'http://rs.tdwg.org/dwc/terms/island';
		$this->occurDefArr['fields']['island'] = 'o.island';
		$this->occurDefArr['terms']['country'] = 'http://rs.tdwg.org/dwc/terms/country';
		$this->occurDefArr['fields']['country'] = 'o.country';
		$this->occurDefArr['terms']['countryCode'] = 'http://rs.tdwg.org/dwc/terms/countryCode';
		$this->occurDefArr['fields']['countryCode'] = 'o.countryCode';
		$this->occurDefArr['terms']['stateProvince'] = 'http://rs.tdwg.org/dwc/terms/stateProvince';
		$this->occurDefArr['fields']['stateProvince'] = 'o.stateProvince';
		$this->occurDefArr['terms']['county'] = 'http://rs.tdwg.org/dwc/terms/county';
		$this->occurDefArr['fields']['county'] = 'o.county';
		$this->occurDefArr['terms']['municipality'] = 'http://rs.tdwg.org/dwc/terms/municipality';
		$this->occurDefArr['fields']['municipality'] = 'o.municipality';
		$this->occurDefArr['terms']['locality'] = 'http://rs.tdwg.org/dwc/terms/locality';
		$this->occurDefArr['fields']['locality'] = 'o.locality';
		$this->occurDefArr['terms']['locationRemarks'] = 'http://rs.tdwg.org/dwc/terms/locationRemarks';
		$this->occurDefArr['fields']['locationRemarks'] = 'o.locationremarks';
		$this->occurDefArr['terms']['recordSecurity'] = 'https://symbiota.org/terms/recordSecurity';
		$this->occurDefArr['fields']['recordSecurity'] = 'o.recordSecurity';
		$this->occurDefArr['terms']['securityReason'] = 'https://symbiota.org/terms/securityReason';
		$this->occurDefArr['fields']['securityReason'] = 'o.securityReason';
		$this->occurDefArr['terms']['decimalLatitude'] = 'http://rs.tdwg.org/dwc/terms/decimalLatitude';
		$this->occurDefArr['fields']['decimalLatitude'] = 'o.decimalLatitude';
		$this->occurDefArr['terms']['decimalLongitude'] = 'http://rs.tdwg.org/dwc/terms/decimalLongitude';
		$this->occurDefArr['fields']['decimalLongitude'] = 'o.decimalLongitude';
		$this->occurDefArr['terms']['geodeticDatum'] = 'http://rs.tdwg.org/dwc/terms/geodeticDatum';
		$this->occurDefArr['fields']['geodeticDatum'] = 'o.geodeticDatum';
		$this->occurDefArr['terms']['coordinateUncertaintyInMeters'] = 'http://rs.tdwg.org/dwc/terms/coordinateUncertaintyInMeters';
		$this->occurDefArr['fields']['coordinateUncertaintyInMeters'] = 'o.coordinateUncertaintyInMeters';
		//$this->occurDefArr['terms']['footprintWKT'] = 'http://rs.tdwg.org/dwc/terms/footprintWKT';
		//$this->occurDefArr['fields']['footprintWKT'] = 'o.footprintWKT';
		$this->occurDefArr['terms']['verbatimCoordinates'] = 'http://rs.tdwg.org/dwc/terms/verbatimCoordinates';
		$this->occurDefArr['fields']['verbatimCoordinates'] = 'o.verbatimCoordinates';
		$this->occurDefArr['terms']['georeferencedBy'] = 'http://rs.tdwg.org/dwc/terms/georeferencedBy';
		$this->occurDefArr['fields']['georeferencedBy'] = 'o.georeferencedBy';
		$this->occurDefArr['terms']['georeferenceProtocol'] = 'http://rs.tdwg.org/dwc/terms/georeferenceProtocol';
		$this->occurDefArr['fields']['georeferenceProtocol'] = 'o.georeferenceProtocol';
		$this->occurDefArr['terms']['georeferenceSources'] = 'http://rs.tdwg.org/dwc/terms/georeferenceSources';
		$this->occurDefArr['fields']['georeferenceSources'] = 'o.georeferenceSources';
		$this->occurDefArr['terms']['georeferenceVerificationStatus'] = 'http://rs.tdwg.org/dwc/terms/georeferenceVerificationStatus';
		$this->occurDefArr['fields']['georeferenceVerificationStatus'] = 'o.georeferenceVerificationStatus';
		$this->occurDefArr['terms']['georeferenceRemarks'] = 'http://rs.tdwg.org/dwc/terms/georeferenceRemarks';
		$this->occurDefArr['fields']['georeferenceRemarks'] = 'o.georeferenceRemarks';
		$this->occurDefArr['terms']['minimumElevationInMeters'] = 'http://rs.tdwg.org/dwc/terms/minimumElevationInMeters';
		$this->occurDefArr['fields']['minimumElevationInMeters'] = 'o.minimumElevationInMeters';
		$this->occurDefArr['terms']['maximumElevationInMeters'] = 'http://rs.tdwg.org/dwc/terms/maximumElevationInMeters';
		$this->occurDefArr['fields']['maximumElevationInMeters'] = 'o.maximumElevationInMeters';
		$this->occurDefArr['terms']['minimumDepthInMeters'] = 'http://rs.tdwg.org/dwc/terms/minimumDepthInMeters';
		$this->occurDefArr['fields']['minimumDepthInMeters'] = 'o.minimumDepthInMeters';
		$this->occurDefArr['terms']['maximumDepthInMeters'] = 'http://rs.tdwg.org/dwc/terms/maximumDepthInMeters';
		$this->occurDefArr['fields']['maximumDepthInMeters'] = 'o.maximumDepthInMeters';
		$this->occurDefArr['terms']['verbatimDepth'] = 'http://rs.tdwg.org/dwc/terms/verbatimDepth';
		$this->occurDefArr['fields']['verbatimDepth'] = 'o.verbatimDepth';
		$this->occurDefArr['terms']['verbatimElevation'] = 'http://rs.tdwg.org/dwc/terms/verbatimElevation';
		$this->occurDefArr['fields']['verbatimElevation'] = 'o.verbatimElevation';
		if($this->includePaleo){
			$this->occurDefArr['terms']['geologicalContextID'] = 'http://rs.tdwg.org/dwc/terms/geologicalContextID';
			$this->occurDefArr['fields']['geologicalContextID'] = 'paleo.geologicalContextID';
			$this->occurDefArr['terms']['earliestEonOrLowestEonothem'] = 'http://rs.tdwg.org/dwc/terms/earliestEonOrLowestEonothem';
			$this->occurDefArr['fields']['earliestEonOrLowestEonothem'] = '';
			$this->occurDefArr['terms']['earliestEraOrLowestErathem'] = 'http://rs.tdwg.org/dwc/terms/earliestEraOrLowestErathem';
			$this->occurDefArr['fields']['earliestEraOrLowestErathem'] = '';
			$this->occurDefArr['terms']['earliestPeriodOrLowestSystem'] = 'http://rs.tdwg.org/dwc/terms/earliestPeriodOrLowestSystem';
			$this->occurDefArr['fields']['earliestPeriodOrLowestSystem'] = '';
			$this->occurDefArr['terms']['earliestEpochOrLowestSeries'] = 'http://rs.tdwg.org/dwc/terms/earliestEpochOrLowestSeries';
			$this->occurDefArr['fields']['earliestEpochOrLowestSeries'] = '';
			$this->occurDefArr['terms']['earliestAgeOrLowestStage'] = 'http://rs.tdwg.org/dwc/terms/earliestAgeOrLowestStage';
			$this->occurDefArr['fields']['earliestAgeOrLowestStage'] = '';
			$this->occurDefArr['terms']['earlyInterval'] = 'https://symbiota.org/terms/paleo-earlyInterval';
			$this->occurDefArr['fields']['earlyInterval'] = 'paleo.earlyInterval';
			$this->occurDefArr['terms']['latestEonOrHighestEonothem'] = 'http://rs.tdwg.org/dwc/terms/latestEonOrHighestEonothem';
			$this->occurDefArr['fields']['latestEonOrHighestEonothem'] = '';
			$this->occurDefArr['terms']['latestEraOrHighestErathem'] = 'http://rs.tdwg.org/dwc/terms/latestEraOrHighestErathem';
			$this->occurDefArr['fields']['latestEraOrHighestErathem'] = '';
			$this->occurDefArr['terms']['latestPeriodOrHighestSystem'] = 'http://rs.tdwg.org/dwc/terms/latestPeriodOrHighestSystem';
			$this->occurDefArr['fields']['latestPeriodOrHighestSystem'] = '';
			$this->occurDefArr['terms']['latestEpochOrHighestSeries'] = '	http://rs.tdwg.org/dwc/terms/latestEpochOrHighestSeries';
			$this->occurDefArr['fields']['latestEpochOrHighestSeries'] = '';
			$this->occurDefArr['terms']['latestAgeOrHighestStage'] = 'http://rs.tdwg.org/dwc/terms/latestAgeOrHighestStage';
			$this->occurDefArr['fields']['latestAgeOrHighestStage'] = '';
			$this->occurDefArr['terms']['lateInterval'] = 'https://symbiota.org/terms/paleo-lateInterval';
			$this->occurDefArr['fields']['lateInterval'] = 'paleo.lateInterval';
			$this->occurDefArr['terms']['lowestBiostratigraphicZone'] = 'http://rs.tdwg.org/dwc/terms/lowestBiostratigraphicZone';
			$this->occurDefArr['fields']['lowestBiostratigraphicZone'] = 'paleo.biostratigraphy AS lowestBiostratigraphicZone';
			$this->occurDefArr['terms']['highestBiostratigraphicZone'] = 'http://rs.tdwg.org/dwc/terms/highestBiostratigraphicZone';
			$this->occurDefArr['fields']['highestBiostratigraphicZone'] = 'paleo.biostratigraphy AS highestBiostratigraphicZone';
			$this->occurDefArr['terms']['absoluteAge'] = 'https://symbiota.org/terms/paleo-absoluteAge';
			$this->occurDefArr['fields']['absoluteAge'] = 'paleo.absoluteAge';
			$this->occurDefArr['terms']['localStage'] = 'https://symbiota.org/terms/paleo-localStage';
			$this->occurDefArr['fields']['localStage'] = 'paleo.localStage';
			$this->occurDefArr['terms']['biota'] = 'https://symbiota.org/terms/paleo-biota';
			$this->occurDefArr['fields']['biota'] = 'paleo.biota';
			$this->occurDefArr['terms']['taxonEnvironment'] = 'https://symbiota.org/terms/paleo-taxonEnvironment';
			$this->occurDefArr['fields']['taxonEnvironment'] = 'paleo.taxonEnvironment';
			$this->occurDefArr['terms']['group'] = 'http://rs.tdwg.org/dwc/terms/group';
			$this->occurDefArr['fields']['group'] = 'paleo.lithogroup';
			$this->occurDefArr['terms']['formation'] = 'http://rs.tdwg.org/dwc/terms/formation';
			$this->occurDefArr['fields']['formation'] = 'paleo.formation';
			$this->occurDefArr['terms']['member'] = 'http://rs.tdwg.org/dwc/terms/member';
			$this->occurDefArr['fields']['member'] = 'paleo.member';
			$this->occurDefArr['terms']['bed'] = 'http://rs.tdwg.org/dwc/terms/bed';
			$this->occurDefArr['fields']['bed'] = 'paleo.bed';
			$this->occurDefArr['terms']['lithology'] = 'http://rs.tdwg.org/dwc/terms/lithology';
			$this->occurDefArr['fields']['lithology'] = 'paleo.lithology';
			$this->occurDefArr['terms']['stratRemarks'] = 'https://symbiota.org/terms/paleo-stratRemarks';
			$this->occurDefArr['fields']['stratRemarks'] = 'paleo.stratRemarks';
			$this->occurDefArr['terms']['element'] = 'https://symbiota.org/terms/paleo-element';
			$this->occurDefArr['fields']['element'] = 'paleo.element';
			$this->occurDefArr['terms']['slideProperties'] = 'https://symbiota.org/terms/paleo-slideProperties';
			$this->occurDefArr['fields']['slideProperties'] = 'paleo.slideProperties';
		}
		$this->occurDefArr['terms']['disposition'] = 'http://rs.tdwg.org/dwc/terms/disposition';
		$this->occurDefArr['fields']['disposition'] = 'o.disposition';
		$this->occurDefArr['terms']['language'] = 'http://purl.org/dc/terms/language';
		$this->occurDefArr['fields']['language'] = 'o.language';
		$this->occurDefArr['terms']['storageLocation'] = 'https://symbiota.org/terms/storageLocation';
		$this->occurDefArr['fields']['storageLocation'] = 'o.storageLocation';
		$this->occurDefArr['terms']['observerUid'] = 'https://symbiota.org/terms/observerUid';
		$this->occurDefArr['fields']['observerUid'] = 'o.observeruid';
		$this->occurDefArr['terms']['processingStatus'] = 'https://symbiota.org/terms/processingStatus';
		$this->occurDefArr['fields']['processingStatus'] = 'o.processingStatus';
		$this->occurDefArr['terms']['duplicateQuantity'] = 'https://symbiota.org/terms/duplicateQuantity';
		$this->occurDefArr['fields']['duplicateQuantity'] = 'o.duplicateQuantity';
		$this->occurDefArr['terms']['labelProject'] = 'https://symbiota.org/terms/labelProject';
		$this->occurDefArr['fields']['labelProject'] = 'o.labelProject';
		$this->occurDefArr['terms']['recordEnteredBy'] = 'https://symbiota.org/terms/recordEnteredBy';
		$this->occurDefArr['fields']['recordEnteredBy'] = 'o.recordEnteredBy';
		$this->occurDefArr['terms']['dateEntered'] = 'https://symbiota.org/terms/dateEntered';
		$this->occurDefArr['fields']['dateEntered'] = 'o.dateEntered';
		$this->occurDefArr['terms']['dateLastModified'] = 'http://rs.tdwg.org/dwc/terms/dateLastModified';
		$this->occurDefArr['fields']['dateLastModified'] = 'o.dateLastModified';
		$this->occurDefArr['terms']['modified'] = 'http://purl.org/dc/terms/modified';
		$this->occurDefArr['fields']['modified'] = 'IFNULL(o.modified,o.datelastmodified) AS modified';
		$this->occurDefArr['terms']['rights'] = 'http://purl.org/dc/elements/1.1/rights';
		$this->occurDefArr['fields']['rights'] = 'c.rights';
		$this->occurDefArr['terms']['rightsHolder'] = 'http://purl.org/dc/terms/rightsHolder';
		$this->occurDefArr['fields']['rightsHolder'] = 'c.rightsHolder';
		$this->occurDefArr['terms']['accessRights'] = 'http://purl.org/dc/terms/accessRights';
		$this->occurDefArr['fields']['accessRights'] = 'c.accessRights';
		$this->occurDefArr['terms']['sourcePrimaryKey-dbpk'] = 'https://symbiota.org/terms/sourcePrimaryKey-dbpk';
		$this->occurDefArr['fields']['sourcePrimaryKey-dbpk'] = 'o.dbpk';
		$this->occurDefArr['terms']['collID'] = 'https://symbiota.org/terms/collID';
		$this->occurDefArr['fields']['collID'] = 'c.collID';
		$this->occurDefArr['terms']['recordID'] = 'https://symbiota.org/terms/recordID';
		$this->occurDefArr['fields']['recordID'] = 'o.recordID';
		$this->occurDefArr['terms']['references'] = 'http://purl.org/dc/terms/references';
		$this->occurDefArr['fields']['references'] = '';
		if($this->schemaType == 'pensoft'){
			$this->occurDefArr['fields']['occid'] = 'o.occid';
		}

		foreach($this->occurDefArr as $k => $vArr){
			if($this->schemaType == 'dwc' || $this->schemaType == 'pensoft'){
				$trimArr = array('recordedByID','associatedCollectors','substrate','verbatimAttributes','cultivationStatus',
					'securityReason','genericcolumn1','genericcolumn2','storageLocation','observerUid','processingStatus',
					'duplicateQuantity','labelProject','dateEntered','dateLastModified','sourcePrimaryKey-dbpk');
				if($this->includePaleo){
					$trimArr = array_merge($trimArr, array('absoluteAge','stage','localStage','biostratigraphy','taxonEnvironment','stratRemarks','element','slideProperties', 'lithology'));
				}
				$this->occurDefArr[$k] = array_diff_key($vArr,array_flip($trimArr));
			}
			elseif($this->schemaType == 'symbiota'){
				$trimArr = array();
				if(!$this->extended){
					$trimArr = array('collectionID','rights','rightsHolder','accessRights','observerUid','processingStatus','duplicateQuantity','labelProject','dateEntered','dateLastModified');
				}
				$this->occurDefArr[$k] = array_diff_key($vArr,array_flip($trimArr));
			}
			elseif($this->schemaType == 'backup'){
				$trimArr = array('collectionID','rights','rightsHolder','accessRights');
				$this->occurDefArr[$k] = array_diff_key($vArr,array_flip($trimArr));
			}
			elseif($this->schemaType == 'backup-personal'){
				$trimArr = array('collectionID','rights','rightsHolder','accessRights');
				$this->occurDefArr[$k] = array_diff_key($vArr,array_flip($trimArr));
			}
			elseif($this->schemaType == 'coge'){
				$targetArr = array('id','basisOfRecord','institutionCode','collectionCode','catalogNumber','occurrenceID','family','scientificName','scientificNameAuthorship',
					'kingdom','phylum','class','order','genus','specificEpithet','infraSpecificEpithet','recordedBy','recordNumber','eventDate','year','month','day','fieldNumber',
					'eventID', 'locationID','continent','waterBody','islandGroup','island','country','stateProvince','county','municipality',
					'locality','recordSecurity','geodeticDatum','decimalLatitude','decimalLongitude','verbatimCoordinates',
					'minimumElevationInMeters','maximumElevationInMeters','verbatimElevation','maximumDepthInMeters','minimumDepthInMeters','establishmentMeans',
					'occurrenceRemarks','dateEntered','dateLastModified','recordID','references','collID');
				$this->occurDefArr[$k] = array_intersect_key($vArr,array_flip($targetArr));
			}
		}

		if($this->schemaType == 'dwc' || $this->schemaType == 'pensoft'){
			$this->occurDefArr['fields']['recordedBy'] = 'CONCAT_WS("; ",o.recordedBy,o.associatedCollectors) AS recordedBy';
			$this->occurDefArr['fields']['occurrenceRemarks'] = 'CONCAT_WS("; ",o.occurrenceRemarks,o.verbatimAttributes) AS occurrenceRemarks';
			$this->occurDefArr['fields']['habitat'] = 'CONCAT_WS("; ",o.habitat, o.substrate) AS habitat';
		}
		return $this->occurDefArr;
	}

	public function getSqlOccurrences($fieldArr){
		$sql = '';
		if($this->exportID){
			if($fieldArr){
				$sqlFrag = '';
				foreach($fieldArr as $fieldName => $colName){
					if($colName){
						$sqlFrag .= ', '.$colName;
					}
					else{
						$sqlFrag .= ', "" AS t_'.$fieldName;
					}
				}
				$sql = 'SELECT '.trim($sqlFrag,', ');
			}
			$sql .= ' FROM omoccurrences o INNER JOIN omcollections c ON o.collid = c.collid INNER JOIN omexportoccurrences x ON o.occid = x.occid ';
			if($this->includePaleo) $sql .= 'LEFT JOIN omoccurpaleo paleo ON o.occid = paleo.occid ';
			$sql .= 'WHERE (x.omExportID = ' . $this->exportID . ') ';
			//if($fullSql) $sql .= ' ORDER BY c.collid ';
			//echo '<div>'.$sql.'</div>'; exit;
		}
		return $sql;
	}

	//Special functions for appending additional data
	public function setOtherCatalogNumbers(){
		$status = false;
		$sql = 'UPDATE omexportoccurrences e INNER JOIN
			(SELECT i.occid, GROUP_CONCAT(CONCAT(i.identifierName, if(i.identifierName != "",": ",""), i.identifierValue) SEPARATOR "; ") as ocn
			FROM omoccuridentifiers i INNER JOIN omexportoccurrences e2 ON i.occid = e2.occid
			WHERE e2.omExportID = 333
			GROUP BY i.occid) intab ON e.occid = intab.occid
			SET e.otherCatalogNumbers = intab.ocn
			WHERE e.omExportID = ?';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('i', $this->exportID);
			if($stmt->execute()) $status = true;
			else{
				$this->errorMessage = 'ERROR batch linking occurrences from download: ' . $stmt->error;
				$this->logOrEcho($this->errorMessage);
			}
			$stmt->close();
		}
		else $this->errorMessage = $this->conn->error;
		return $status;
	}

	public function setExsiccate(){
		$status = false;
		$sql = 'UPDATE omexportoccurrences x INNER JOIN omexsiccatiocclink l ON x.occid = l.occid
			INNER JOIN omexsiccatinumbers n ON l.omenid = n.omenid
			INNER JOIN omexsiccatititles t ON n.ometid = t.ometid
			SET x.occurrenceRemarks = CONCAT_WS("; ", x.occurrenceRemarks, CONCAT_WS(" ", t.title, CONCAT("[", t.abbreviation, "]"), CONCAT(", ", t.editor), CONCAT(", ", t.exsrange), CONCAT(", exs #: ", n.exsnumber), CONCAT(" (", l.notes, ")")))
			WHERE x.omExportID = ?';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('i', $this->exportID);
			if($stmt->execute()) $status = true;
			else{
				$this->errorMessage = 'ERROR batch linking occurrences from download: ' . $stmt->error;
				$this->logOrEcho($this->errorMessage);
			}
			$stmt->close();
		}
		else $this->errorMessage = $this->conn->error;
		return $status;
	}

	//Set Associations
	public function getAssociationStr($occid, $associationType = null){
		$occid = filter_var($occid, FILTER_SANITIZE_NUMBER_INT);
		if($occid){

			// Return symbiotaAssociations JSON for the associatedOccurrences field instead of the text string generated below
			// TODO: There is room for fine-tuning what conditions will return the JSON
			// Seems like it should be turned on for schemaType == 'backup', but re-importing that backup currently fails
			if ($this->schemaType == 'symbiota') return $this->getAssociationJSON($occid);

			$internalAssocOccidArr = array();
			$assocArr = array();
			//Get associations defined within omoccurassociations (both subject and object)
			$assocTypeArr = array('internalOccurrence', 'externalOccurrence');
			if($associationType) $assocTypeArr = explode(' ', $associationType);
			$sql = 'SELECT assocID, occid, associationType, occidAssociate, relationship, subType, resourceUrl, identifier, basisOfRecord, verbatimSciname, recordID FROM omoccurassociations
				WHERE (occid = '.$occid.' OR occidAssociate = '.$occid.') AND associationType IN("'.implode('","', $assocTypeArr).'") ';
			$rs = $this->conn->query($sql);
			if($rs){
				while($r = $rs->fetch_object()){
					$relOccid = $r->occidAssociate;
					$relationship = $r->relationship;
					if($occid == $r->occidAssociate){
						//Association was defined within secondary occurrence record, thus switch subject/object
						$relOccid = $r->occid;
						$relationship = $this->getInverseRelationship($relationship);
					}
					if($relOccid){
						//Is an internally defined association
						$assocArr[$r->assocID]['occidassoc'] = $relOccid;
						$internalAssocOccidArr[$relOccid][] = $r->assocID;
					}
					elseif($r->resourceUrl){
						$assocArr[$r->assocID]['resourceUrl'] = $r->resourceUrl;
						$assocArr[$r->assocID]['identifier'] = $r->identifier;
					}
					$assocArr[$r->assocID]['relationship'] = $relationship;
					$assocArr[$r->assocID]['subtype'] = $r->subType;
					if($r->basisOfRecord) $assocArr[$r->assocID]['basisOfRecord'] = $r->basisOfRecord;
					if($r->verbatimSciname) $assocArr[$r->assocID]['scientificName'] = $r->verbatimSciname;
					if($r->recordID) $assocArr[$r->assocID]['recordID'] = $r->recordID;
				}
				$rs->free();
			}

			//Append resource URLs to each output record
			if($internalAssocOccidArr){
				$identifierArr = $this->getInternalResourceIdentifiers($internalAssocOccidArr);
				foreach($identifierArr as $internalOccid => $idArr){
					foreach($internalAssocOccidArr[$internalOccid] as $targetAssocID){
						$assocArr[$targetAssocID] = array_merge($assocArr[$targetAssocID], $idArr);
					}
				}
			}
			//Create output strings
			$retStr = '';
			foreach($assocArr as $assocateArr){
				if($associationType == 'observational'){
					$retStr .= ' | ' . $assocateArr['relationship'];
					if(!empty($assocateArr['scientificName'])) $retStr .= ': '.$assocateArr['scientificName'];
				}
				else{
					$retStr .= ' | relationship: ' . $assocateArr['relationship'];
					if(!empty($assocateArr['subtype'])) $retStr .= ' (' . $assocateArr['subtype'] . ')';
					if(!empty($assocateArr['identifier'])) $retStr .= ', identifier: ' . $assocateArr['identifier'];
					elseif(!empty($assocateArr['recordID'])) $retStr .= ', recordID:  ' . $assocateArr['recordID'];
					if(!empty($assocateArr['basisOfRecord'])) $retStr .= ', basisOfRecord: ' . $assocateArr['basisOfRecord'];
					if(!empty($assocateArr['scientificName'])) $retStr .= ', scientificName: ' . $assocateArr['scientificName'];
					if(!empty($assocateArr['resourceUrl'])) $retStr .= ', resourceUrl:  ' . $assocateArr['resourceUrl'];
				}
			}
		}
		return trim($retStr,' |');
	}

	// Function to return any associations as JSON for the associatedOccurrences field
	private function getAssociationJSON($occid) {

		// Build SQL to find any associations for the occurrence record passed with occid
		$sql = 'SELECT occid, associationType, occidAssociate, relationship, subType, identifier, basisOfRecord, resourceUrl, verbatimSciname, locationOnHost
			FROM omoccurassociations
			WHERE (occid = ' . $occid . ' OR occidAssociate = ' . $occid . ') ';
		if ($rs = $this->conn->query($sql)) {

			// No associations, so just return an empty string, and quit the function
			if (!$rs->num_rows) return '';

			// Build verbatimText array
			// Get any pre-existing contents of the associatedOccurrences field in omoccurrences
			$verbatimText = $this->getVerbatimTextObject($occid);
			// Check if the contents of the field already is JSON
			if ($verbatimText['verbatimText']){
				if ($assocOccArr = json_decode($verbatimText['verbatimText'], true)) {
					$verbatimText['verbatimText'] = '';
					// There's already JSON here
					// TODO: What should we do? Perform some checks?
				}
			}

			// No associatedOccurrences array exists, so build one
			if (!isset($assocOccArr)) {

				// Build JSON array
				$assocOccArr = array();

				// Build symbiotaAssociations array
				$symbiotaAssociations = array();
				$symbiotaAssociations['type'] = 'symbiotaAssociations';
				$symbiotaAssociations['version'] = OccurrenceUtil::$assocOccurVersion;
				$symbiotaAssociations['associations'] = array();

				// Add the symbiotaAssociations array
				array_push($assocOccArr, $symbiotaAssociations);

				// Add the verbatimText array
				array_push($assocOccArr, $verbatimText);
			}

			// Make an array to hold occurrence IDs that need an additional guid (internalOccurrences)
			$relOccidArr = array();

			// Get each associated occurrence
			while ($assocArr = $rs->fetch_assoc()) {

				// Filter out any empty fields
				$assocArr = array_filter($assocArr);

				// Set the association type field
				if (array_key_exists('occidAssociate', $assocArr)) {
					$assocArr['type'] = 'internalOccurrence';
				} else if (array_key_exists('identifier', $assocArr) || array_key_exists('resourceUrl', $assocArr)) {
					$assocArr['type'] = 'externalOccurrence';
				} else if (array_key_exists('verbatimSciname', $assocArr)) {
					$assocArr['type'] = 'genericObservation';
				} else {
					// Should not happen, but if so, this seems to be the best fit
					$assocArr['type'] = 'genericObservation';
				}

				// Check for cases where the occidAssociate is this occid.
				// In those cases, we need to switch the occid and occidAssociate and get the inverse relationship
				if (array_key_exists('occidAssociate', $assocArr) && $assocArr['occidAssociate'] == $occid) {
					$assocArr['occidAssociate'] = $assocArr['occid'];
					$assocArr['relationship'] = $this->getInverseRelationship($assocArr['relationship']);
				}

				// remove occid key, no longer needed
				unset($assocArr['occid']);

				// Check if the associated occurrence is an internal occurrence
				// If so, we need to flag this to add the GUID identifier & resource url, in case it gets imported in another portal
				if (array_key_exists('occidAssociate', $assocArr)) {
					array_push($relOccidArr, $assocArr['occidAssociate']);
				}

				// Add associated occurrence array to the full associatedOccurrences array
				array_push($assocOccArr[0]['associations'], $assocArr);

			}

			// There are some associated occurrences with an internal occidAssociate
			// For these, we need to get their guids and construct reference URLs, in case they become external references
			if ($relOccidArr) {
				$identifierArr = $this->getInternalResourceIdentifiers($relOccidArr);
				foreach($identifierArr as $internalOccid => $idArr){
					foreach ($assocOccArr[0]['associations'] as $index => $associateArr) {
						if (array_key_exists('occidAssociate', $associateArr) && $assocOccArr[0]['associations'][$index]['occidAssociate'] == $internalOccid) {
							// Add the GUID as the identifier, and the resource URL in case this ends up being treated as an external resource
							$assocOccArr[0]['associations'][$index] = array_merge($assocOccArr[0]['associations'][$index], $idArr);
						}
					}
				}
			}

			// Return the full symbiotaAssociations array as JSON
			// TODO: this is returning "null" for fields that are empty, like verbatimText.
			return json_encode( $assocOccArr, JSON_UNESCAPED_SLASHES);
		}
	}

	private function appendSpecimenDuplicateAssociations($occid, &$assocArr, &$internalAssocOccidArr){
		$sql = 'SELECT s.occid, l.occid as occidAssociate
				FROM omoccurduplicatelink s INNER JOIN omoccurduplicates d ON s.duplicateid = d.duplicateid
				INNER JOIN omoccurduplicatelink l ON d.duplicateid = l.duplicateid
				WHERE s.occid IN('.$occid.') AND s.occid != l.occid ';
		$rs = $this->conn->query($sql);
		if($rs){
			while($r = $rs->fetch_object()){
				$assocKey = 'sd-'.$r->occidAssociate;
				$assocArr[$assocKey]['occidassoc'] = $r->occidAssociate;
				$assocArr[$assocKey]['relationship'] = 'herbariumSpecimenDuplicate';
				$internalAssocOccidArr[$r->occidAssociate][] = $assocKey;
			}
			$rs->free();
		}
	}

	private function getVerbatimTextObject($occid){
		$verbatimText = array('type' => 'verbatimText', 'verbatimText' => '');

		$sql = 'SELECT associatedOccurrences FROM omoccurrences WHERE occid = ' . $occid;
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			if($r->associatedOccurrences) $verbatimText['verbatimText'] = $r->associatedOccurrences;
		}
		$rs->free();
		return $verbatimText;
	}

	private function getInternalResourceIdentifiers($internalAssocOccidArr){
		$retArr = array();
		//Replace GUID identifiers with occurrenceID values
		$sql = 'SELECT occid, sciname, occurrenceID, recordID FROM omoccurrences WHERE occid IN('.implode(',',array_keys($internalAssocOccidArr)).')';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->occid]['scientificName'] = $r->sciname;
			$guid = $r->recordID;
			if($r->occurrenceID) $guid = $r->occurrenceID;
			$retArr[$r->occid]['identifier'] = $guid;
			$resourceUrl = $guid;
			if(substr($resourceUrl, 0, 4) != 'http'){
				$resourceUrl = $this->serverDomain.$GLOBALS['CLIENT_ROOT'].'/collections/individual/index.php?guid='.$guid;
			}
			$retArr[$r->occid]['resourceUrl'] = $resourceUrl;
		}
		$rs->free();
		return $retArr;
	}

	public function setIncludeAssociatedSequences(){
		$sql = 'SELECT occid FROM omoccurgenetic LIMIT 1';
		$rs = $this->conn->query($sql);
		if($rs->num_rows) $this->includeAssocSeq = true;
		$rs->free();
	}

	public function getAssociatedSequencesStr($occid){
		$retStr = '';
		if(is_numeric($occid)){
			$sql = 'SELECT identifier, resourceName, title, locus, resourceUrl FROM omoccurgenetic WHERE occid = '.$occid;
			$rs = $this->conn->query($sql);
			if($rs){
				while($r = $rs->fetch_object()){
					$retStr .= '|'.$r->resourceName.', ';
					if($r->title) $retStr .= $r->title.', ';
					if($r->identifier) $retStr .= $r->identifier.', ';
					if($r->locus) $retStr .= $r->locus.', ';
					$retStr .= $r->resourceUrl;
				}
				$rs->free();
			}
		}
		return trim($retStr,' |,');
	}

	private function getInverseRelationship($relationship){
		if(!$this->relationshipArr) $this->setRelationshipArr();
		if(array_key_exists($relationship, $this->relationshipArr)) return $this->relationshipArr[$relationship];
		return $relationship;
	}

	private function setRelationshipArr(){
		if(!$this->relationshipArr){
			$sql = 'SELECT t.term, t.inverseRelationship FROM ctcontrolvocabterm t INNER JOIN ctcontrolvocab v  ON t.cvid = v.cvid WHERE v.tableName = "omoccurassociations" AND v.fieldName = "relationship"';
			if($rs = $this->conn->query($sql)){
				while($r = $rs->fetch_object()){
					$this->relationshipArr[$r->term] = $r->inverseRelationship;
					$this->relationshipArr[$r->inverseRelationship] = $r->term;
				}
				$rs->free();
			}
		}
	}

	//Append Taxonomic data
	public function setTaxonomy(){
		$this->setUpperTaxonomy();
		$this->setBaseTaxonomy();
		$this->setSpeciesTaxonomy();
		$this->setTaxonRank();
		$this->setAcceptedTaxonomy();
	}

	private function setUpperTaxonomy(){
		if($this->exportID){
			//Set parent rank fields for all occurrences
			$nodeArr = array(10 => 'kingdom', 30 => 'phylum', 60 => 'class', 100 => 'order', 140 => 'family', 180 => 'genus', 190 => 'subgenus');
			foreach($nodeArr as $rankID => $fieldName){
				$sql = 'UPDATE omexportoccurrences x INNER JOIN taxaenumtree e ON x.taxonID = e.tid
					INNER JOIN taxa t ON e.parentTid = t.tid
					SET x.' . $fieldName . ' = t.sciname
					WHERE x.omExportID = ? AND t.rankid = ?';
				if($stmt = $this->conn->prepare($sql)){
					try{
						$stmt->bind_param('ii', $this->exportID, $rankID);
						$stmt->execute();
					} catch (mysqli_sql_exception $e){
						$this->errorMessage = $stmt->error;
					}
					$stmt->close();
				}
			}
		}
	}

	private function setBaseTaxonomy(){
		if($this->exportID){
			//Set field of the current rank for occurrences ID to higher ranks
			$nodeArr = array(10 => 'kingdom', 30 => 'phylum', 60 => 'class', 100 => 'order', 140 => 'family');
			foreach($nodeArr as $rankID => $fieldName){
				$sql = 'UPDATE omexportoccurrences x INNER JOIN taxa t ON x.taxonID = t.tid
					SET x.' . $fieldName . ' = t.sciname
					WHERE x.omExportID = ? AND t.rankid = ?';
				if($stmt = $this->conn->prepare($sql)){
					try{
						$stmt->bind_param('ii', $this->exportID, $rankID);
						$stmt->execute();
					} catch (mysqli_sql_exception $e){
						$this->errorMessage = $stmt->error;
					}
					$stmt->close();
				}
			}
		}
	}

	private function setSpeciesTaxonomy(){
		if($this->exportID){
			//Set taxon fields for occurrences ID to species
			$sql = 'UPDATE omexportoccurrences x INNER JOIN taxa t ON x.taxonID = t.tid
				SET x.genus = CONCAT_WS(" ", t.unitInd1, t.unitName1),
				x.specificEpithet = CONCAT_WS(" ", t.unitInd2, t.unitName2),
				x.verbatimTaxonRank = t.unitInd3, x.infraspecificEpithet = t.unitName3,
				x.cultivarEpithet = t.cultivarEpithet, x.tradeName = t.tradeName,
				x.scientificNameAuthorship = t.sciname
				WHERE x.omExportID = ? AND t.rankid >= 180';
			if($stmt = $this->conn->prepare($sql)){
				try{
					$stmt->bind_param('i', $this->exportID);
					$stmt->execute();
				} catch (mysqli_sql_exception $e){
					$this->errorMessage = $stmt->error;
				}
				$stmt->close();
			}
		}
	}

	private function setTaxonRank(){
		if($this->exportID){
			$sql = 'UPDATE omexportoccurrences x INNER JOIN taxa t ON x.taxonID = t.tid
				INNER JOIN taxonunits u ON t.rankid = u.rankid
				SET x.taxonRank = u.rankName
				WHERE x.omExportID = ? AND x.kingdom = u.kingdomName';
			if($stmt = $this->conn->prepare($sql)){
				try{
					$stmt->bind_param('i', $this->exportID);
					$stmt->execute();
				} catch (mysqli_sql_exception $e){
					$this->errorMessage = $stmt->error;
				}
				$stmt->close();
			}
		}
	}

	private function setAcceptedTaxonomy(){
		if($this->includeAcceptedNameUsage && $this->exportID){
			//Set basic taxon level terms by matching on occurrence tid
			$sql = 'UPDATE omexportoccurrences x INNER JOIN taxstatus ts ON x.taxonID = ts.tid
				INNER JOIN taxa t ON ts.tidaccepted = t.tid
				SET x.acceptedNameUsageID = t.tid, x.acceptedNameUsage = t.sciname, x.acceptedNameUsageAuthorship = t.scientificNameAuthorship
				WHERE x.omExportID = ?';
			if($stmt = $this->conn->prepare($sql)){
				try{
					$stmt->bind_param('i', $this->exportID);
					$stmt->execute();
				} catch (mysqli_sql_exception $e){
					$this->errorMessage = $stmt->error;
				}
				$stmt->close();
			}
		}
	}

	//Paleo data
	public function appendPaleoTerms(&$r){
		$this->setPaleoGtsTerms();
		if($this->paleoGtsArr){
			if(!empty($r['earlyInterval'])){
				$targetArr = array(20 => 't_earliestEonOrLowestEonothem', 30 => 't_earliestEraOrLowestErathem', 40 => 't_earliestPeriodOrLowestSystem', 50 => 't_earliestEpochOrLowestSeries', 60 => 't_earliestAgeOrLowestStage');
				$this->setPaleoTerm($r, $r['earlyInterval'], $targetArr);
			}
			if(!empty($r['lateInterval'])){
				$targetArr = array(20 => 't_latestEonOrHighestEonothem', 30 => 't_latestEraOrHighestErathem', 40 => 't_latestPeriodOrHighestSystem', 50 => 't_latestEpochOrHighestSeries', 60 => 't_latestAgeOrHighestStage');
				$this->setPaleoTerm($r, $r['lateInterval'], $targetArr);
			}
		}
	}

	private function setPaleoTerm(&$r, $term, $targetArr){
		if($term && !empty($this->paleoGtsArr[$term])){
			$rankid = $this->paleoGtsArr[$term]['r'];
			if ($rankid > 10){
				$r[$targetArr[$rankid]] = $term;
				$this->setPaleoTerm($r, $this->paleoGtsArr[$term]['p'], $targetArr);
			}
		}
	}

	private function setPaleoGtsTerms(){
		if($this->paleoGtsArr === null){
			//Set paleo GTS terms array
			$this->paleoGtsArr = array();
			$sql = 'SELECT g.gtsTerm, g.rankid, p.gtsTerm as parentTerm
				FROM omoccurpaleogts g LEFT JOIN omoccurpaleogts p ON g.parentGtsID = p.gtsID ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$this->paleoGtsArr[$r->gtsTerm]['r'] = $r->rankid;
				$this->paleoGtsArr[$r->gtsTerm]['p'] = $r->parentTerm;
			}
			$rs->free();
		}
	}

	//Setter and getter
	public function setExportID($id){
		$this->exportID = $id;
	}

	public function setSchemaType($t, $observerUid = 0){
		if($t == 'backup' && $observerUid) $this->schemaType = 'backup-personal';
		else $this->schemaType = $t;
	}

	public function setExtended($e){
		if($e) $this->extended = true;
	}

	public function setIncludePaleo($bool){
		if($bool) $this->includePaleo = true;
		else if(!empty($GLOBALS['ACTIVATE_PALEO'])) $this->includePaleo = true;
	}

	public function setIncludeAcceptedNameUsage(bool $bool): void {
		$this->includeAcceptedNameUsage = $bool;
	}

	public function setServerDomain($domain){
		$this->serverDomain = $domain;
	}
}
?>
