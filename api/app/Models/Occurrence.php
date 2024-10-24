<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Occurrence extends Model{

	protected $table = 'omoccurrences';
	protected $primaryKey = 'occid';
	public $timestamps = false;

	protected $fillable = [ 'collid', 'dbpk', 'basisOfRecord', 'occurrenceID', 'catalogNumber', 'otherCatalogNumbers', 'family', 'scientificName', 'sciname', 'genus', 'specificEpithet', 'datasetID', 'organismID',
		'taxonRank', 'infraspecificEpithet', 'institutionCode', 'collectionCode', 'scientificNameAuthorship', 'taxonRemarks', 'identifiedBy', 'dateIdentified', 'identificationReferences',
		'identificationRemarks', 'identificationQualifier', 'typeStatus', 'recordedBy', 'recordNumber', 'associatedCollectors', 'eventDate', 'eventDate2',
		'verbatimEventDate', 'eventTime', 'habitat', 'substrate', 'fieldNotes', 'fieldNumber', 'eventID', 'occurrenceRemarks', 'informationWithheld', 'dataGeneralizations',
		'associatedTaxa', 'dynamicProperties', 'verbatimAttributes', 'behavior', 'reproductiveCondition', 'cultivationStatus', 'establishmentMeans', 'lifeStage', 'sex', 'individualCount',
		'samplingProtocol', 'samplingEffort', 'preparations', 'locationID', 'continent', 'parentLocationID', 'country', 'stateProvince', 'county', 'municipality', 'waterBody', 'islandGroup',
		'island', 'countryCode', 'locality', 'localitySecurity', 'localitySecurityReason', 'decimalLatitude', 'decimalLongitude', 'geodeticDatum', 'coordinateUncertaintyInMeters',
		'footprintWKT', 'locationRemarks', 'verbatimCoordinates', 'georeferencedBy', 'georeferencedDate', 'georeferenceProtocol', 'georeferenceSources',
		'georeferenceVerificationStatus', 'georeferenceRemarks', 'minimumElevationInMeters', 'maximumElevationInMeters', 'verbatimElevation', 'minimumDepthInMeters', 'maximumDepthInMeters',
		'verbatimDepth', 'availability', 'disposition', 'storageLocation', 'modified', 'language', 'processingStatus', 'recordEnteredBy', 'duplicateQuantity', 'labelProject', 'recordID', 'dateEntered'];
	protected $hidden = [ 'collection', 'scientificName', 'recordedbyid', 'observerUid', 'labelProject', 'recordEnteredBy', 'associatedOccurrences', 'previousIdentifications',
		'verbatimCoordinateSystem', 'coordinatePrecision', 'footprintWKT', 'dynamicFields', 'institutionID', 'collectionID', 'genericColumn1', 'genericColumn2' ];
	public static $snakeAttributes = false;

	public function getInstitutionCodeAttribute($value){
		if(!$value){
			$value = $this->collection->institutionCode;
		}
		return $value;
	}

	public function getCollectionCodeAttribute($value){
		if(!$value){
			$value = $this->collection->collectionCode;
		}
		return $value;
	}

	public function getOccurrenceIDAttribute($value){
		if(!$value && $this->collection->guidTarget == 'symbiotaUUID'){
			$value = $this->attributes['recordID'];
		}
		return $value;
	}

	public function collection(){
		return $this->belongsTo(Collection::class, 'collid', 'collid');
	}

	public function identification(){
		return $this->hasMany(OccurrenceIdentification::class, 'occid', 'occid');
	}

	public function media(){
		return $this->hasMany(Media::class, 'occid', 'occid');
	}

	public function annotationExternal(){
		return $this->hasMany(OccurrenceAnnotationExternal::class, 'occid', 'occid');
	}

	public function annotationInternal(){
		return $this->hasMany(OccurrenceAnnotationInternal::class, 'occid', 'occid');
	}

	public function annotationInternalColl(){
		return $this->hasMany(OccurrenceAnnotationInternal::class, 'occid', 'occid')->where('collid', 1);
	}

	public function portalPublications(){
		return $this->belongsToMany(PortalPublication::class, 'portaloccurrences', 'occid', 'pubid')->withPivot('remoteOccid');;
	}
}