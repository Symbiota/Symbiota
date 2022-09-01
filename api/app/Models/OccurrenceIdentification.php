<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OccurrenceIdentification extends Model{

	protected $table = 'omoccurdeterminations';
	protected $primaryKey = 'occid';
	public $timestamps = false;

	protected $fillable = [];

	protected $hidden = [];

	public function occurrence() {
		return $this->belongsTo(Occurrence::class, 'occid', 'occid');
	}
}