<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PortalOccurrences extends Model{

	protected $table = 'portaloccurrences';
	protected $primaryKey = 'portalOccurrencesID';
	public $timestamps = false;

	protected $fillable = [ 'occid', 'portalID', 'pubid', 'targetOccid', 'verification', 'refreshtimestamp' ];

	public function portalIndex() {
		return $this->belongsTo(PortalIndex::class, 'portalID', 'portalID');
	}

	public function occurrence() {
		return $this->belongsTo(Occurrence::class, 'occid', 'occid');
	}
}