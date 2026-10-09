<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    protected $fillable = [
        'semestre',
        'materia',
        'materias_id',
        'asignatura_id',
        'corte1',
        'corte2',
        'corte3',
        'habilitada_recuperacion'
    ];
    protected $casts = [
        'habilitada_recuperacion' => 'boolean'
    ];
}
