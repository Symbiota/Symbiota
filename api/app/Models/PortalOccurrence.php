<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalOccurrence extends Model{

	protected $table = 'portaloccurrences';
	protected $primaryKey = 'portalOccurrencesID';
	protected $fillable = ['occid', 'pubid', 'targetOccid', 'verification', 'refreshTimestamp' ];
	public $timestamps = false;

	public function portalIndex() {
		return $this->belongsTo(PortalIndex::class, 'portalID', 'portalID');
	}

	public function occurrence() {
		return $this->belongsTo(Occurrence::class, 'occid', 'occid');
	}
}