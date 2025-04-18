<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OccurrenceExsiccatiTitle extends Model{

	protected $table = 'omexsiccatititles';
	protected $primaryKey = 'ometid';
	public $timestamps = false;

	protected $fillable = [];
	protected $visible = [];
	protected $hidden = [];

	public function number(){
		return $this->hasMany(OccurrenceExsiccatiNumber::class, 'ometid', 'ometid');
	}

}