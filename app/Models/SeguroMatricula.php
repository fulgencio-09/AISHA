<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeguroMatricula extends Model
{
    use HasFactory;

    protected $table = 'seguro_matriculas';

    protected $fillable = [
        'asignatura_id',
        'valor',
        'estado',
        'fecha_pago',
        'forma_pago',
        'referencia',
        'observacion',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'estado' => 'boolean',
        'fecha_pago' => 'date',
    ];

    public function asignatura(): BelongsTo
    {
        return $this->belongsTo(Asignatura::class);
    }
}
