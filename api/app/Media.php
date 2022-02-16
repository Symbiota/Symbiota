<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{

	protected $table = 'images';
	protected $primaryKey = 'imgid';

	public $timestamps = false;

	protected $fillable = [
		'url', ' thumbnailurl', 'originalurl', 'archiveurl', 'photographer', 'photographeruid', 'imagetype', 'format', 'caption', 'owner', 'sourceurl', 'referenceUrl', 'copyright',
		'rights', 'accessrights', 'locality', 'occid', 'notes', 'anatomy', 'username', 'sourceIdentifier', 'mediaMD5', 'dynamicProperties', 'defaultDisplay', 'sortsequence', 'sortOccurrence'
	];

	protected $hidden = ['dynamicProperties'];

	public function occurrence() {
		return $this->belongsTo(Occurrence::class, 'occid', 'occid');
	}
}