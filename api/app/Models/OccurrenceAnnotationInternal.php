<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OccurrenceAnnotationInternal extends Model{

	protected $table = 'omoccuredits';
	protected $primaryKey = 'ocedid';
	public $timestamps = false;

	protected $fillable = [];
	protected $visible = [];
	protected $hidden = [];

	public function occurrence(){
		return $this->belongsTo(Occurrence::class, 'occid', 'occid');
	}

}