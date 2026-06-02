<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpsertCollaboratorsQueue extends Model
{
    protected $table = 'upsert_collaborators_queue';
    protected $fillable = ['cpf'];
    public $timestamps = false;
}
