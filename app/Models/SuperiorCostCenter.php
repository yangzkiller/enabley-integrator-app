<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuperiorCostCenter extends Model
{
    protected $table = 'centros_de_custo_superiores';
    protected $primaryKey = 'id_centro_de_custo_superior';
    public $timestamps = false;
}
