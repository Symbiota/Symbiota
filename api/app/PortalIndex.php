<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class PortalIndex extends Model{
	protected $table = 'portalindex';
	protected $primaryKey = 'portalID';
	protected $fillable = ['portalName', 'acronym', 'portalDescription', 'urlRoot', 'symbiotaVersion', 'guid', 'manager', 'managerEmail', 'primaryLead', 'primaryLeadEmail', 'notes'];
	protected $guarded = [];
	protected $hidden = ['securityKey'];
	public $timestamps = false;

	public function portalOccurrences(){
		return $this->hasMany(PortalOccurrences::class, 'portalID', 'portalID');
	}

	public function portalPublications(){
		return $this->hasMany('App\portalpublications', 'portalID', 'portalID');
	}
}
