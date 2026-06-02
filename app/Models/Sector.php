<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Triple;
use App\Models\Unit;

class Sector extends Model
{
    protected $fillable = [
        'name',
        'unit_id',
        'ext_id',
    ];
    public $timestamps = false;
    
    public function triples()
    {
        return $this->hasMany(Triple::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
