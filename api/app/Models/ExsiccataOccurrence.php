<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExsiccataOccurrence extends Model{

	protected $table = 'omexsiccatiocclink';
	protected $primaryKey = 'omexid';
	protected $fillable = [];
	protected $visible = [];
	protected $hidden = [];
	public $timestamps = false;

	public function number(){
		return $this->hasOne(ExsiccataNumber::class, 'omenid', 'omenid');
	}

	public function occurrence(){
		return $this->hasOne(Occurrence::class, 'occid', 'occid');
	}

}
