<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class OccurrenceDataset extends Model{

	protected $table = 'omoccurdatasets';
	protected $primaryKey = 'datasetID';
	public $timestamps = false;

	protected $fillable = [];

	protected $hidden = [ 'uid', 'collid', 'dynamicProperties', 'includeInSearch', 'parentDatasetID' ];

	protected static function booted() {
		static::addGlobalScope('published', function (Builder $builder) {
			$builder->where('isPublic', 1);
		});
	}

	public function occurrence() {
		return $this->belongsToMany(Occurrence::class, 'omoccurdatasetlink', 'datasetID', 'occid');
	}
}