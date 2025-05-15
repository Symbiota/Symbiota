<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxonomyStatus extends Model{

	protected $table = 'taxstatus';
	protected $primaryKey = 'tid';
	public $timestamps = false;
	protected $hidden = [ 'initialtimestamp', 'modifiedUid' ];
	protected $fillable = [ 'tidaccepted', 'taxauthid', 'family', 'parenttid', 'Unacceptabilityreason', 'modifiedUid'];
}
