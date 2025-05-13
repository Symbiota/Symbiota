<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxonomyStatus extends Model{

	protected $table = 'taxstatus';
	protected $primaryKey = 'tid';
	protected $hidden = [ 'initialtimestamp' ];
	protected $fillable = [ 'tidaccepted', 'taxauthid', 'family', 'parenttid', 'unacceptabilityreason', 'modifiedUid'];
}
