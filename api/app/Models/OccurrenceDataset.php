<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OccurrenceDataset extends Model{

	protected $table = 'omoccurdatasets';
	protected $primaryKey = 'datasetID';
	public $timestamps = false;

	protected $fillable = [];

	protected $hidden = [ 'uid', 'collid', 'dynamicProperties', 'includeInSearch', 'parentDatasetID', 'isPublic' ];

	public function occurrence() {
		return $this->belongsTo(Occurrence::class, 'occid', 'occid');
	}
}