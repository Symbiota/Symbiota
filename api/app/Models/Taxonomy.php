<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Taxonomy extends Model{

	protected $table = 'taxa';
	protected $primaryKey = 'tid';
	protected $hidden = [ 'phyloSortSequence', 'nomenclaturalStatus', 'nomenclaturalCode', 'statusNotes', 'hybrid', 'pivot' ];
	protected $fillable = [  ];

	public function descriptions(){
		return $this->hasMany(TaxonomyDescription::class, 'tid', 'tid');
	}

	public function media(){
		return $this->hasMany(media::class, 'tid', 'tid');
	}
}
