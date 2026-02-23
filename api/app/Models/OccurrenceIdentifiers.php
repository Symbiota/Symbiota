<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OccurrenceIdentifiers extends Model{

	protected $table = 'omoccuridentifiers';
	protected $primaryKey = 'idomoccuridentifiers';
	public $timestamps = false;

	protected $fillable = [];

	protected $hidden = [ 'idomoccuridentifiers', 'format', 'notes', 'sortBy', 'recordID', 'modifiedUid', 'modifiedTimestamp' ];

	public function occurrence() {
		return $this->belongsTo(Occurrence::class, 'occid', 'occid');
	}
}