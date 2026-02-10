<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OccurrenceOcr extends Model{

	protected $table = 'specprocessorrawlabels';
	protected $primaryKey = 'prlid';
	public $timestamps = false;

	protected $fillable = [];
	protected $hidden = ['occid', 'processingvariables', 'score'];
	public static $snakeAttributes = false;

	public function media(){
		return $this->belongsTo(Media::class, 'imgid', 'imgid');
	}

}