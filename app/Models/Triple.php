<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Unit;
use App\Models\Sector;
use App\Models\Branch;
use App\Models\CostCenter;
use App\Models\SuperiorCostCenter;

class Triple extends Model
{
    protected $fillable = [
        'branch_id',
        'cc_id',
        'ccs_id',
        'unit_id',
        'sector_id',
        'ignored',
    ];

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'ignored' => 'boolean',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id_filial', 'filiais');
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class, 'cc_id', 'id_centro_de_custo', 'centros_de_custo');
    }

    public function superiorCostCenter()
    {
        return $this->belongsTo(SuperiorCostCenter::class, 'ccs_id', 'id_centro_de_custo_superior', 'centros_de_custo_superiores');
    }
    
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }

    public function isSolved(): bool
    {
        return ($this->unit && $this->sector) || ($this->ignored === true);
    }

    public function collaborators(array $columns = ['*'])
    {
        return Collaborator::where('id_filial', $this->branch_id)
            ->where('id_centro_de_custo', $this->cc_id)
            ->where('id_centro_de_custo_superior', $this->ccs_id)
            ->get($columns);
    }
}
