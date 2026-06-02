<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Triple;
use App\Models\Sector;
class Unit extends Model
{
    protected $fillable = [
        'name',
        'ext_id',
    ];
    public $timestamps = false;
    
    public function triples()
    {
        return $this->hasMany(Triple::class);
    }

    public function sectors()
    {
        return $this->hasMany(Sector::class);
    }
}
