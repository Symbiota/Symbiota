<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxonomyStatus extends Model{

	protected $table = 'taxstatus';
	protected $primaryKey = 'tid';
	protected $hidden = [ 'initialtimestamp' ]; //@TODO LEFT OFF HERE    
	protected $fillable = [ 'kingdomName', 'rankID', 'unitInd1', 'unitName1', 'unitInd2', 'unitName2', 'unitInd3', 'unitName3', 'cultivarEpithet', 'tradeName', 'author', 'source', 'notes', 'securitystatus', 'modifiedUid', 'modifiedTimeStamp' ]; // @TODO sciName?
	// protected $maps = [ 'sciName' => 'scientificName' ];
	// protected $appends = [ 'scientificName' ];
	public static $snakeAttributes = false;

	public function getScientificNameAttribute(){
		return $this->attributes['sciName'];
	}

	public function descriptions(){
		return $this->hasMany(TaxonomyDescription::class, 'tid', 'tid');
	}

	public function media(){
		return $this->hasMany(media::class, 'tid', 'tid');
	}
}
