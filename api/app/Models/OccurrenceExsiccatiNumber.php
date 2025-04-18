<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OccurrenceExsiccatiNumber extends Model{

	protected $table = 'omexsiccatinumbers';
	protected $primaryKey = 'omenid';
	public $timestamps = false;

	protected $fillable = [];
	protected $visible = [];
	protected $hidden = [];
	public static $snakeAttributes = false;

	public function title(){
		return $this->belongsTo(OccurrenceExsiccatiTitle::class, 'ometid');
	}

	public function occurrenceLinks(){
		return $this->hasMany(OccurrenceExsiccatiLink::class, 'omenid');
	}

	public function occurrences(){
		return $this->belongsToMany(Occurrence::class, 'omexsiccatiocclink', 'omenid', 'occid');
	}
}