<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Nota;
use App\Models\Periodo;
use App\Models\Asignatura;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class Notas extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $date = Carbon::parse();
        $dates = $date->format('Y');
        $nota = Estudiante::join('asignaturas', 'estudiantes.id', '=', 'asignaturas.estudiante_id')
            ->where('asignaturas.estado', '=', 1)
            ->where('estudiantes.estado', '=', 1)
            ->select('estudiantes.name', 'asignaturas.mat_matr', 'estudiantes.documento', 'asignaturas.semestre', 'asignaturas.id as asignatura_id', 'asignaturas.año', 'estudiantes.id')
            ->orderBy('asignaturas.id', 'desc')
            ->paginate(10);

        return [
            'pagination' => [
                'total' => $nota->total(),
                'current_page' => $nota->currentPage(),
                'per_page' => $nota->perPage(),
                'last_page' => $nota->lastPage(),
                'from' => $nota->firstItem(),
                'to' => $nota->lastItem(),
            ],
            'nota' => $nota,
        ];
    }

    public function busca(Request $request)
    {
        $identidad = $request->input('identidad');
        $nota = Estudiante::join('asignaturas', 'estudiantes.id', '=', 'asignaturas.estudiante_id')
            ->where('estudiantes.documento', '=', $identidad)
            ->where('asignaturas.estado', '=', 1)
            ->select('estudiantes.name', 'asignaturas.mat_matr', 'estudiantes.documento', 'asignaturas.semestre', 'asignaturas.id as asignatura_id', 'asignaturas.año', 'estudiantes.id')
            ->orderBy('asignaturas.id', 'desc')
            ->paginate(10);

        return [
            'pagination' => [
                'total' => $nota->total(),
                'current_page' => $nota->currentPage(),
                'per_page' => $nota->perPage(),
                'last_page' => $nota->lastPage(),
                'from' => $nota->firstItem(),
                'to' => $nota->lastItem(),
            ],
            'nota' => $nota,
        ];
    }

    public function buscar($id)
    {
        return response()->json(Nota::where('asignatura_id', '=', $id)->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'semestre' => 'required|integer|min:1|max:5',
            'materias_id' => 'required|integer|exists:materias,id',
            'asignatura_id' => 'required|integer|exists:asignaturas,id',
            'materia' => 'required|string|max:255',
            'corte1' => 'nullable|numeric|min:0|max:5',
            'corte2' => 'nullable|numeric|min:0|max:5',
            'corte3' => 'nullable|numeric|min:0|max:5',
        ]);

        $data['user_id'] = Auth::id();

        return Nota::create($data);
    }

    public function show(Request $request)
    {
        return view('home');
    }

    public function update(Request $request, $id)
    {
        $nota = Nota::findOrFail($id);
        $nota->corte1 = $request->corte1;
        $nota->corte2 = $request->corte2;
        $nota->corte3 = $request->corte3;
        $nota->save();

        return [
            'asignatura_id' => $request->asignatura_id
        ];
    }

    public function destroy(Nota $nota)
    {
        $nota->delete();
        return response()->json(['message' => 'Nota eliminada']);
    }

    public function periodo()
    {
        return response()->json(Periodo::all());
    }

    public function agregarnotas(Request $request)
    {
        $request->validate([
            'semestre' => 'required|integer|min:1|max:5',
            'matricula' => 'required|integer|exists:asignaturas,id',
        ]);

        $matricula = (int) $request->matricula;
        $semestre = (int) $request->semestre;
        $userId = Auth::id();

        $not = Asignatura::findOrFail($matricula);

        // La matrícula recibida debe corresponder al semestre seleccionado.
        if ((int) $not->semestre !== $semestre) {
            return response()->json([
                'message' => 'El semestre seleccionado no corresponde a la matrícula.'
            ], 422);
        }

        $materias = Asignatura::join('materias', 'asignaturas.semestre', '=', 'materias.semestre')
            ->where('asignaturas.id', '=', $matricula)
            ->where('asignaturas.semestre', '=', $semestre)
            ->select(
                'asignaturas.semestre',
                'asignaturas.id as asignatura_id',
                'materias.id as materia_id',
                'materias.materia'
            )
            ->get();

        if ($materias->isEmpty()) {
            return response()->json([
                'message' => 'No existen materias configuradas para este semestre.'
            ], 422);
        }

        foreach ($materias as $materia) {
            Nota::updateOrCreate(
                [
                    'user_id' => $userId,
                    'asignatura_id' => $materia->asignatura_id,
                    'materia_id' => $materia->materia_id,
                    'semestre' => $materia->semestre,
                ],
                [
                    'materia' => $materia->materia,
                    'matriculado_en' => now(),
                ]
            );
        }

        $not->mat_matr = 'Si';
        $not->save();

        return response()->json([
            'message' => 'Materias agregadas correctamente.',
            'total_materias' => $materias->count(),
        ]);
    }
}
