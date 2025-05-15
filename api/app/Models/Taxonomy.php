<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Taxonomy extends Model{

	protected $table = 'taxa';
	public $timestamps = false;
	protected $primaryKey = 'tid';
	protected $hidden = [ 'phyloSortSequence', 'nomenclaturalStatus', 'nomenclaturalCode', 'statusNotes', 'hybrid', 'pivot', 'modifiedUid', 'modifiedTimeStamp', 'initialTimeStamp', 'InitialTimeStamp' ]; // @TODO I'm  not sure why sciName was hidden, but I don't think that it can be if we need to POST sciName with an endpoint
	protected $fillable = [ 'kingdomName', 'sciName', 'rankID', 'unitInd1', 'unitName1', 'unitInd2', 'unitName2', 'unitInd3', 'unitName3', 'cultivarEpithet', 'tradeName', 'author', 'source', 'notes', 'securitystatus', 'modifiedUid', 'modifiedTimeStamp' ]; // @TODO sciName?
	protected $maps = [ 'sciName' => 'scientificName' ];
	protected $appends = [ 'scientificName' ];
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

	//@TODO for create, cultivarEpithet needs to have single quotes purged during entry
	//@TODO for create, tradeName needs to be capitalized during entry
}
