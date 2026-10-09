<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abono extends Model
{
    protected $fillable = [
        'fechas',         
        'cantidad',      
        'estado',     
        'deuda',    
        'asignatura_id',
        'formapago',
        'user_id',
        
        
    ];
}
