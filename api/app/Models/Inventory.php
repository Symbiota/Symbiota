<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model{

	protected $table = 'fmchecklists';
	protected $primaryKey = 'clid';
	public $timestamps = false;

	protected $fillable = [];

	protected $hidden = [ 'title', 'type', 'dynamicSql', 'parentparentClid', 'access', 'cidKeyLimits', 'defaultSettings', 'dynamicProperties', 'uid', 'expiration', 'dateLastModified' ];

	public function taxa(){
		return $this->belongsToMany(Taxonomy::class, 'fmchklsttaxalink', 'clid', 'tid');
	}
}