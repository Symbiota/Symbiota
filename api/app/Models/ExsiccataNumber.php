<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExsiccataNumber extends Model {

	protected $table = 'omexsiccatinumbers';
	protected $primaryKey = 'omenid';
	protected $fillable = ['exsNumber', 'notes', 'initialTimestamp'];
	protected $visible = [];
	protected $hidden = [];
	public static $snakeAttributes = false;
	public $timestamps = false;

	public function exsiccata() {
		return $this->belongsTo(Exsiccata::class, 'ometid', 'ometid');
	}

	public function occurrenceLinks(){
		return $this->hasMany(ExsiccataOccurrence::class, 'omenid', 'omenid');
	}

	public function occurrences(){
		return $this->belongsToMany(Occurrence::class, 'omexsiccatiocclink', 'omenid', 'occid');
	}
}
?>
