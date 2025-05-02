<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionStats extends Model{

	protected $table = 'omcollectionstats';
	protected $primaryKey = 'collid';
    const CREATED_AT = 'initialtimestamp';
    const UPDATED_AT = 'datelastmodified';
	public $timestamps = true;

	protected $fillable = [
		'collid', 'recordcnt', 'uploadedby'
	];

	protected $hidden = [];
	public static $snakeAttributes = false;

}