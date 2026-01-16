<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model{

	protected $table = 'omcollections';
	protected $primaryKey = 'collID';
	public $timestamps = false;

	protected $fillable = [
		'institutionCode', 'collectionCode', 'collectionName', 'collectionID', 'fullDescription', 'individualUrl',
		'latitudeDecimal', 'longitudeDecimal', 'collType', 'managementType', 'publicEdits', 'collectionGuid', 'rightsHolder', 'rights',
		 'accessRights', 'sortSeq', 'icon'
	];

	protected $hidden = ['securityKey', 'guidTarget', 'aggKeysStr', 'dwcTermJson', 'publishToGbif', 'publishToIdigbio', 'dynamicProperties'];
	public static $snakeAttributes = false;

	public function occurrence(){
		return $this->hasMany(Occurrence::class, 'collid', 'collid');
	}
}