<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignatura extends Model
{
    protected $fillable = [
        
        'estudiante_id',
        'año',
        'valor',
        'periodo',
        'mat_matr',
        'total',
        'estado',
        'semestre',
        'sede'
    ];

}
