<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Respaldo;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    // Dashboard
    public function matriculados()
    {
        $matriculados = DB::table('asignaturas as a')
            ->join('estudiantes as e', 'e.id', '=', 'a.estudiante_id')
            ->selectRaw("
                MONTH(a.created_at) AS mes_numero,
                COUNT(DISTINCT a.estudiante_id) AS cantidad
            ")
            ->whereYear('a.created_at', now()->year)
            ->groupByRaw('MONTH(a.created_at)')
            ->orderByRaw('MONTH(a.created_at)')
            ->get();

        $meses = [
            1  => 'Enero',
            2  => 'Febrero',
            3  => 'Marzo',
            4  => 'Abril',
            5  => 'Mayo',
            6  => 'Junio',
            7  => 'Julio',
            8  => 'Agosto',
            9  => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
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
     * Semestres disponibles para el filtro financiero del dashboard.
     *
     * Se obtienen directamente de asignaturas porque ese es el campo que
     * realmente utiliza el resumen de pagos para filtrar. Así el dashboard
     * no depende de que la tabla periodos tenga registros activos.
     */
    public function semestres()
    {
        $periodos = DB::table('asignaturas')
            ->whereNotNull('semestre')
            ->where('semestre', '!=', '')
            ->select('semestre')
            ->distinct()
            ->orderBy('semestre')
            ->get();

        $resultado = $periodos->map(function ($periodo) {
            $valor = (string) $periodo->semestre;

            return [
                'id' => $valor,
                'periodo' => $valor,
                'nombre' => 'Semestre ' . $valor,
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

    public function resumenPagos(Request $request)
    {
        // El filtro es obligatorio para el dashboard: se selecciona desde
        // la lista de semestres obtenida de asignaturas.
        $semestre = $request->filled('semestre')
            ? trim((string) $request->input('semestre'))
            : null;

        // TOTAL DEUDA Y PENDIENTE
        $asignaturasQuery = DB::table('asignaturas');

        if ($semestre !== null && $semestre !== '') {
            $asignaturasQuery->where('semestre', $semestre);
        }

        $asignaturas = $asignaturasQuery
            ->selectRaw('
                COALESCE(SUM(total), 0) AS total_deuda,
                COALESCE(SUM(valor), 0) AS total_pendiente
            ')
            ->first();

        // TOTAL PAGADO
        $abonosQuery = DB::table('abonos')
            ->join(
                'asignaturas',
                'asignaturas.id',
                '=',
                'abonos.asignatura_id'
            );

        if ($semestre !== null && $semestre !== '') {
            $abonosQuery->where('asignaturas.semestre', $semestre);
        }

        $totalPagado = $abonosQuery->sum('abonos.cantidad');

        return [
            'total_deuda' => (float) ($asignaturas->total_deuda ?? 0),
            'total_pagado' => (float) $totalPagado,
            'total_pendiente' => (float) ($asignaturas->total_pendiente ?? 0),
        ];
    }
}
