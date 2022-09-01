<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MorphologyCharacter extends Model{

	protected $table = 'kmcharacters';
	protected $primaryKey = 'cid';
	public $timestamps = false;

	protected $fillable = [
		''
	];

	protected $hidden = [];

}