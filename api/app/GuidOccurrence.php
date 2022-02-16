<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GuidOccurrence extends Model
{

	protected $table = 'guidoccurrences';
	protected $primaryKey = 'guid';
	public $timestamps = false;

	protected $fillable = [];

	protected $hidden = [];

	public function occurrence() {
		return $this->belongsTo(Occurrence::class, 'occid', 'occid');
	}
}