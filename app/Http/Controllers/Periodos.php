<?php

namespace App\Http\Controllers;

use App\Models\Periodo;
use Illuminate\Http\Request;

class Periodos extends Controller
{
    public function semestres()
{
    return Periodo::where('estado', 1)
        ->orderBy('anio', 'desc')
        ->orderBy('periodo', 'desc')
        ->get();
}



}
