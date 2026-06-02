<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnableyFunction extends Model
{
    protected $fillable = [
        'identifier',
        'sector_ext_id',
        'name',
    ];
    public $timestamps = false;
}
