<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxonomyDescription extends Model{

	protected $table = 'taxadescrblock';
	protected $primaryKey = 'tdbid';
	protected $hidden = [  ];
	protected $fillable = [  ];

    public function statement(){
    	return $this->hasMany(TaxonomyDescriptionStatement::class, 'tdbid', 'tdbid');
	}

}
