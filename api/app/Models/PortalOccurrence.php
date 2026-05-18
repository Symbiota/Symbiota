<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalOccurrence extends Model{

	protected $table = 'portaloccurrences';
	protected $primaryKey = 'portalOccurrencesID';
	protected $fillable = ['occid', 'pubid', 'remoteOccid', 'verification', 'refreshTimestamp' ];
	public $timestamps = false;

	public function portalPublication() {
		return $this->belongsTo(PortalPublication::class, 'pubid', 'pubid');
	}

	public function occurrence() {
		return $this->belongsTo(Occurrence::class, 'occid', 'occid');
	}
}
?>