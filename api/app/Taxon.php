<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Taxon extends Model
{

	protected $table = 'taxa';
    protected $primaryKey = 'tid';
    protected $fillable = ['kingdomName', 'rankId', 'sciName', 'unitInd1', 'unitName1', 'unitInd2', 'unitName2', 'unitInd3', 'unitName3', 'author', 'phyloSortSequence', 'reviewStatus', 'displayStatus', 'isLegitimate', 'nomenclaturalStatus', 'nomenclaturalCode', 'statusNotes', 'source', 'notes', 'hybrid', 'securityStatus', 'modifiedTimeStamp', 'initialTimeStamp'];

    public function taxStatuses()
    {
        return $this->hasMany('App\Taxstatus', 'tid', 'TID');
    }

}
