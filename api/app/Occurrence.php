<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Occurrence extends Model
{

	protected $table = 'omoccurrences';
	protected $primaryKey = 'occid';
	public $timestamps = false;

	protected $fillable = [
		'collid', 'dbpk', 'basisOfRecord', 'occurrenceID', 'catalogNumber', 'otherCatalogNumbers', 'ownerInstitutionCode', 'family', 'sciname', 'datasetID', 'organismID', 'institutionCode',
		'collectionCode', 'scientificNameAuthorship', 'taxonRemarks', 'identifiedBy', 'dateIdentified', 'identificationReferences', 'identificationRemarks', 'identificationQualifier',
		'typeStatus', 'recordedBy', 'recordNumber', 'associatedCollectors', 'eventDate', 'eventDate2', 'verbatimEventDate', 'eventTime', 'habitat', 'substrate', 'fieldNotes', 'fieldnumber',
		'eventID', 'occurrenceRemarks', 'informationWithheld', 'dataGeneralizations', 'associatedTaxa', 'dynamicProperties', 'verbatimAttributes', 'behavior', 'reproductiveCondition',
		'cultivationStatus', 'establishmentMeans', 'lifeStage', 'sex', 'individualCount', 'samplingProtocol', 'samplingEffort', 'preparations', 'locationID', 'continent', 'parentLocationID',
		'waterBody', 'islandGroup', 'island', 'countryCode', 'country', 'stateProvince', 'county', 'municipality', 'locality', 'localitySecurity', 'localitySecurityReason',
		'decimalLatitude', 'decimalLongitude', 'geodeticDatum', 'coordinateUncertaintyInMeters', 'footprintWKT', 'locationRemarks', 'verbatimCoordinates',
		'georeferencedBy', 'georeferencedDate', 'georeferenceProtocol', 'georeferenceSources', 'georeferenceVerificationStatus', 'georeferenceRemarks',
		'minimumElevationInMeters', 'maximumElevationInMeters', 'verbatimElevation', 'minimumDepthInMeters', 'maximumDepthInMeters', 'verbatimDepth', 'previousIdentifications',
		'availability', 'disposition', 'storageLocation', 'modified', 'language', 'processingstatus', 'recordEnteredBy', 'duplicateQuantity', 'labelProject', 'dynamicFields',
		'dateEntered', 'dateLastModified'
	];

	protected $hidden = ['dynamicFields'];

	public function identification()
	{
		return $this->hasMany(Identification::class, 'occid', 'occid');
	}

	public function media()
	{
		return $this->hasMany(Media::class, 'occid', 'occid');
	}

	public function guidOccurrence()
	{
	    return $this->hasMany(GuidOccurrence::class, 'occid', 'occid');
	}
}