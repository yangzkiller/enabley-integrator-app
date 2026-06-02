<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostCenter extends Model
{
    protected $table = 'centros_de_custo';
    protected $primaryKey = 'id_centro_de_custo';
    public $timestamps = false;
}
