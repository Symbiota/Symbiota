<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exsiccata extends Model {
    protected $table = 'omexsiccatititles';
    protected $primaryKey = 'ometid';
    protected $hidden = ['lasteditedby'];
    protected $fillable = [];
    protected $maps = [];
    protected $appends = [];
}
