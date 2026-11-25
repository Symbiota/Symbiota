<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalPublication extends Model{

	protected $table = 'portalpublications';
	protected $primaryKey = 'pubid';
	public $timestamps = false;

	protected $fillable = [ 'pubTitle', 'description', 'guid', 'collid', 'portalID', 'direction', 'criteriaJson',
		'includeDeterminations', 'includeImages', 'autoUpdate', 'lastDateUpdate', 'updateInterval', 'createdUid' ];

	public function portalIndex() {
		return $this->belongsTo(PortalIndex::class, 'portalID', 'portalID');
	}

	public function portalOccurrences(){
		return $this->hasMany(PortalOccurrence::class, 'pubid', 'pubid');
	}
}
?>