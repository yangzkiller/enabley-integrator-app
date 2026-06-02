<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Problem;

class Solution extends Model
{
    protected $fillable = [
        'problem_id',
        'description',
    ];
    public $timestamps = false;

    public function problem()
    {
        return $this->belongsTo(Problem::class);
    }
}
