<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model{

	protected $table = 'omcollections';
	protected $primaryKey = 'collid';
	public $timestamps = false;

	protected $fillable = [
		'institutionCode', 'collectionCode', 'collectionName', 'collectionID', 'datasetID', 'datasetName', 'fullDescription', 'resourceJson', 'IndividualUrl', 'contactJson',
		'latitudeDecimal', 'longitudeDecimal', 'icon', 'collType', 'managementType', 'publicEdits', 'collectionGuid', 'rightsHolder', 'rights', 'usageTerm', 'dwcaUrl',
		'bibliographicCitation', 'accessRights', 'sortSeq'
	];

	protected $hidden = ['securityKey', 'guidTarget', 'aggKeysStr', 'dwcTermJson', 'publishToGbif', 'publishToIdigbio', 'dynamicProperties'];

	public function occurrence(){
		return $this->hasMany(Occurrence::class, 'collid', 'collid');
	}
}