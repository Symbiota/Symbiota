<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model{

	protected $table = 'omcollections';
	protected $primaryKey = 'collid';
	public $timestamps = false;

	protected $fillable = [
		'InstitutionCode', 'CollectionCode', 'CollectionName', 'collectionId', 'datasetID', 'datasetName', 'fulldescription', 'resourceJson', 'IndividualUrl', 'contactJson',
		'latitudedecimal', 'longitudedecimal', 'icon', 'CollType', 'ManagementType', 'PublicEdits', 'collectionguid', 'rightsHolder', 'rights', 'usageTerm', 'dwcaUrl',
		'bibliographicCitation', 'accessrights', 'SortSeq'
	];

	protected $hidden = ['securitykey', 'guidtarget', 'aggKeysStr', 'dwcTermJson', 'publishToGbif', 'publishToIdigbio', 'dynamicProperties'];

	public function occurrence(){
		return $this->hasMany(Occurrence::class, 'collid', 'collid');
	}
}