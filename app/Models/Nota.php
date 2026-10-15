<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'semestre',
        'materia',
        'materia_id',
        'asignatura_id',
        'corte1',
        'corte2',
        'corte3',
        'habilitada_recuperacion',
    ];

    protected $casts = [
        'habilitada_recuperacion' => 'boolean',
    ];
}
