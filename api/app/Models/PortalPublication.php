<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalPublication extends Model{

	protected $table = 'portalpublications';
	protected $primaryKey = 'pubid';
	public $timestamps = false;

	protected $fillable = [  ];

	public function portalIndex() {
		return $this->belongsTo(PortalIndex::class, 'portalID', 'portalID');
	}
}