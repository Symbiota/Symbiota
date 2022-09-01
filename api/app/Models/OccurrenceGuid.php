<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OccurrenceGuid extends Model{

	protected $table = 'guidoccurrences';
	protected $primaryKey = 'guid';
	public $timestamps = false;

	protected $fillable = [];

	protected $hidden = [];

	public function occurrence() {
		return $this->belongsTo(Occurrence::class, 'occid', 'occid');
	}
}