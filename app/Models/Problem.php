<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Solution;

class Problem extends Model
{
    protected $fillable = [
        'description',
    ];
    public $timestamps = false;

    public function solutions()
    {
        return $this->hasMany(Solution::class);
    }
}
