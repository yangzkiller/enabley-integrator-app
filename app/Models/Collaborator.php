<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collaborator extends Model
{
    protected $table = 'colaboradores';
    protected $primaryKey = 'id_colaborador';
    public $timestamps = false;

    public function funcao()
    {
        return $this->belongsTo(Funcao::class, 'id_funcao', 'id_funcao');
    }

    public function triple()
    {
        return Triple::where('branch_id', $this->id_filial)
            ->where('cc_id', $this->id_centro_de_custo)
            ->where('ccs_id', $this->id_centro_de_custo_superior)
            ->first();
    }
}
