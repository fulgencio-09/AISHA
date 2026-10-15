<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function seguroMatricula(): HasOne
    {
        return $this->hasOne(SeguroMatricula::class);
    }
}
