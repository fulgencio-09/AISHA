<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use App\Models\Asignatura as ModelsAsignatura;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class Asignatura extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $asignatura = Estudiante::orderBy('id', 'desc')->paginate(5);

        return [
            'pagination' => [
                'total' => $asignatura->total(),
                'current_page' => $asignatura->currentPage(),
                'per_page' => $asignatura->perPage(),
                'last_page' => $asignatura->lastPage(),
                'from' => $asignatura->firstItem(),
                'to' => $asignatura->lastPage(),
            ],
            'asignatura' => $asignatura,
        ];
    }

    public function create(Request $request)
    {
        $input = $request->all();
        $inputs = $input['name'];
        $asignatura = ModelsAsignatura::where('name', '=', $inputs)->get();

        return [
            'asignatura' => $asignatura
        ];
    }

    public function edit(Request $request, $id)
    {
        $currentYear = Carbon::now()->year;
        $date = range($currentYear, 2021);
        $estudiante = Estudiante::where('estudiantes.id', '=', $id)->get();

        $matriculass = Estudiante::join('asignaturas', 'estudiantes.id', '=', 'asignaturas.estudiante_id')
            ->where('estudiantes.id', '=', $id)
            ->select(
                'estudiantes.id',
                'estudiantes.name',
                'estudiantes.tipo',
                'estudiantes.documento',
                'asignaturas.semestre',
                'asignaturas.periodo',
                'asignaturas.sede',
                'asignaturas.id as id_asignatura',
                'asignaturas.valor',
                'asignaturas.total',
                'asignaturas.año'
            )
            ->orderBy('id', 'desc')
            ->paginate(5);

        if (isset($matriculass[0])) {
            foreach ($matriculass as $a) {
                $matricula = $a->semestre;

                if ($matricula === 1) {
                    $json = [
                        ['matricula' => ['matricula' => 1]],
                        ['matricula' => ['matricula' => 2]],
                    ];
                    $matricula = Arr::pluck($json, 'matricula');
                } elseif ($matricula === 2) {
                    $json = [
                        ['matricula' => ['matricula' => 2]],
                        ['matricula' => ['matricula' => 3]],
                    ];
                    $matricula = Arr::pluck($json, 'matricula');
                } elseif ($matricula === 3) {
                    $json = [
                        ['matricula' => ['matricula' => 3]],
                        ['matricula' => ['matricula' => 4]],
                    ];
                    $matricula = Arr::pluck($json, 'matricula');
                } elseif ($matricula === 4) {
                    $json = [
                        ['matricula' => ['matricula' => 4]],
                        ['matricula' => ['matricula' => 5]],
                    ];
                    $matricula = Arr::pluck($json, 'matricula');
                } elseif ($matricula === 5) {
                    $json = [
                        ['matricula' => ['matricula' => 5]],
                    ];
                    $matricula = Arr::pluck($json, 'matricula');
                } else {
                    $json = [
                        ['matricula' => ['matricula' => 1]],
                        ['matricula' => ['matricula' => 2]],
                        ['matricula' => ['matricula' => 3]],
                        ['matricula' => ['matricula' => 4]],
                        ['matricula' => ['matricula' => 5]],
                    ];
                    $matricula = Arr::pluck($json, 'matricula');
                }
            }

            return [
                'matricula' => $matricula,
                'matriculass' => $matriculass,
                'estudiante' => $estudiante,
                'id' => $id,
                'date' => $date,
            ];
        }

        $json = [
            ['matricula' => ['matricula' => 1]],
            ['matricula' => ['matricula' => 2]],
            ['matricula' => ['matricula' => 3]],
            ['matricula' => ['matricula' => 4]],
            ['matricula' => ['matricula' => 5]],
        ];

        $matricula = Arr::pluck($json, 'matricula');

        return [
            'matriculass' => $matriculass,
            'matricula' => $matricula,
            'id' => $id,
            'date' => $date,
        ];
    }

    public function buscar(Request $request)
    {
        $input = $request->all();
        $inputs = $input['name'];
        $asignatura = ModelsAsignatura::where('name', '=', $inputs)->get();

        return [
            'asignatura' => $asignatura
        ];
    }

    public function update(Request $request, $id)
    {
        $asignatura = ModelsAsignatura::find($id);
        $asignatura->valor = $request->valor;
        $asignatura->total = $request->total;
        $asignatura->año = $request->año;
        $asignatura->semestre = $request->semestre;
        $asignatura->sede = $request->sede;
        $asignatura->periodo = $request->periodo;
        $asignatura->save();
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $inputs = $input['estudiante_id'];
        $ano = $input['año'] ?? null;
        $periodo = $input['periodo'] ?? null;
        $semestre = $input['semestre'] ?? null;

        // Una matrícula se considera repetida solamente cuando coincide
        // estudiante + año + periodo + semestre.
        $verificador = ModelsAsignatura::where('estudiante_id', $inputs)
            ->where('año', $ano)
            ->where('periodo', $periodo)
            ->where('semestre', $semestre)
            ->get();

        if (isset($verificador[0])) {
            return response()->json('no');
        }

        // Si ya existe la misma matrícula académica con saldo pendiente,
        // se mantiene la validación de deuda para ese mismo año/período.
        $verificador = ModelsAsignatura::where('estudiante_id', $inputs)
            ->where('año', $ano)
            ->where('periodo', $periodo)
            ->where('semestre', $semestre)
            ->where('valor', '>', 0)
            ->get();

        if (isset($verificador[0])) {
            return response()->json('debe');
        }

        $asignatura = ModelsAsignatura::create($input);

        return response()->json($asignatura);
    }

    public function show(Request $request)
    {
        return view('home');
    }
}
