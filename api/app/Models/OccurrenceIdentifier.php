<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OccurrenceIdentifier extends Model{

	protected $table = 'omoccuridentifiers';
	protected $primaryKey = 'idomoccuridentifiers';
	public $timestamps = false;

	protected $fillable = [];

	protected $hidden = [ 'format', 'notes', 'sortBy', 'modifiedUid', 'modifiedTimestamp' ];

	public function occurrence() {
		return $this->belongsTo(Occurrence::class, 'occid', 'occid');
	}
}