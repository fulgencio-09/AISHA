<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Respaldo;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('home');
    }

    public function matriculados()
    {
        $matriculados = DB::table('asignaturas as a')
            ->join('estudiantes as e', 'e.id', '=', 'a.estudiante_id')
            ->selectRaw("MONTH(a.created_at) AS mes_numero, COUNT(DISTINCT a.estudiante_id) AS cantidad")
            ->whereYear('a.created_at', now()->year)
            ->groupByRaw('MONTH(a.created_at)')
            ->orderByRaw('MONTH(a.created_at)')
            ->get();

        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        $resultado = [];

        foreach ($meses as $numero => $nombre) {
            $registro = $matriculados->firstWhere('mes_numero', $numero);
            $resultado[] = [
                'mes_numero' => $numero,
                'mes' => $nombre,
                'cantidad' => $registro ? (int) $registro->cantidad : 0,
            ];
        }

        return response()->json($resultado);
    }

    /**
     * Devuelve las combinaciones de año y periodo académico que realmente
     * existen en las matrículas. El filtro financiero trabaja con ambos
     * campos para no mezclar, por ejemplo, 2025 periodo 1 con 2026 periodo 1.
     */
    public function semestres()
    {
        $periodos = DB::table('asignaturas')
            ->whereNotNull('año')
            ->where('año', '!=', '')
            ->whereNotNull('periodo')
            ->where('periodo', '!=', '')
            ->select('año', 'periodo')
            ->distinct()
            ->orderBy('año', 'desc')
            ->orderBy('periodo')
            ->get();

        $resultado = $periodos->map(function ($periodo) {
            $ano = (string) $periodo->año;
            $periodoAcademico = (string) $periodo->periodo;

            return [
                'id' => $ano . '-' . $periodoAcademico,
                'ano' => $ano,
                'periodo' => $periodoAcademico,
                'nombre' => $ano . ' - Periodo ' . $periodoAcademico,
            ];
        })->values();

        return response()->json($resultado);
    }

    public function log()
    {
        $respaldos = Respaldo::with('user')
            ->orderByDesc('created_at')
            ->paginate(5);

        return [
            'pagination' => [
                'total' => $respaldos->total(),
                'current_page' => $respaldos->currentPage(),
                'per_page' => $respaldos->perPage(),
                'last_page' => $respaldos->lastPage(),
                'from' => $respaldos->firstItem(),
                'to' => $respaldos->lastItem(),
            ],
            'respaldos' => $respaldos->items(),
        ];
    }

    /**
     * Resume los valores únicamente para el año y periodo seleccionados.
     */
    public function resumenPagos(Request $request)
    {
        $ano = $request->filled('ano')
            ? trim((string) $request->input('ano'))
            : null;

        $periodo = $request->filled('periodo')
            ? trim((string) $request->input('periodo'))
            : null;

        $asignaturasQuery = DB::table('asignaturas');

        if ($ano !== null && $ano !== '') {
            $asignaturasQuery->where('año', $ano);
        }

        if ($periodo !== null && $periodo !== '') {
            $asignaturasQuery->where('periodo', $periodo);
        }

        $asignaturas = $asignaturasQuery
            ->selectRaw('COALESCE(SUM(total), 0) AS total_deuda, COALESCE(SUM(valor), 0) AS total_pendiente')
            ->first();

        $abonosQuery = DB::table('abonos')
            ->join('asignaturas', 'asignaturas.id', '=', 'abonos.asignatura_id');

        if ($ano !== null && $ano !== '') {
            $abonosQuery->where('asignaturas.año', $ano);
        }

        if ($periodo !== null && $periodo !== '') {
            $abonosQuery->where('asignaturas.periodo', $periodo);
        }

        $totalPagado = $abonosQuery->sum('abonos.cantidad');

        return [
            'total_deuda' => (float) ($asignaturas->total_deuda ?? 0),
            'total_pagado' => (float) $totalPagado,
            'total_pendiente' => (float) ($asignaturas->total_pendiente ?? 0),
        ];
    }
}
