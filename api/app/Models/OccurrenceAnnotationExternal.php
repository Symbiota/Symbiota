<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OccurrenceAnnotationExternal extends Model{

	protected $table = 'omoccurrevisions';
	protected $primaryKey = 'orid';
	public $timestamps = false;

	protected $fillable = [];
	protected $visible = [];
	protected $hidden = [];

	public function occurrence(){
		return $this->belongsTo(Occurrence::class, 'occid', 'occid');
	}
}