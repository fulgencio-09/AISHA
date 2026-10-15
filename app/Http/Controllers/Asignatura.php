<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use App\Models\Asignatura as ModelsAsignatura;
use App\Models\Estudiante;
use App\Models\SeguroMatricula;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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
        $asignatura = ModelsAsignatura::findOrFail($id);
        $asignatura->valor = $request->valor;
        $asignatura->total = $request->total;
        $asignatura->semestre = $request->semestre;
        $asignatura->sede = $request->sede;
        $asignatura->periodo = $request->periodo;
        $asignatura->save();

        return response()->json($asignatura);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'estudiante_id' => ['required', 'integer', 'exists:estudiantes,id'],
            'año' => ['required', 'integer', 'min:2021'],
            'periodo' => ['required', Rule::in([1, 2, '1', '2'])],
            'semestre' => ['required', 'integer', 'between:1,5'],
            'sede' => ['required', 'string', 'max:100'],
            'total' => ['required', 'numeric', 'min:0'],
            'valor' => ['required', 'numeric', 'min:0'],
            'seguro_valor' => ['required', 'numeric', 'gt:0'],
        ]);

        $inputs = $validated['estudiante_id'];
        $ano = $validated['año'];
        $periodo = $validated['periodo'];
        $semestre = $validated['semestre'];

        $verificador = ModelsAsignatura::where('estudiante_id', $inputs)
            ->where('año', $ano)
            ->where('periodo', $periodo)
            ->where('semestre', $semestre)
            ->exists();

        if ($verificador) {
            return response()->json([
                'message' => 'El estudiante ya tiene una matrícula registrada para el año, período y semestre seleccionados.'
            ], 422);
        }

        $verificador = ModelsAsignatura::where('estudiante_id', $inputs)
            ->where('año', $ano)
            ->where('periodo', $periodo)
            ->where('semestre', $semestre)
            ->where('valor', '>', 0)
            ->exists();

        if ($verificador) {
            return response()->json([
                'message' => 'La matrícula seleccionada tiene saldo pendiente y no puede registrarse nuevamente.'
            ], 422);
        }

        $asignatura = DB::transaction(function () use ($validated) {
            $seguroValor = $validated['seguro_valor'];
            unset($validated['seguro_valor']);

            // La tabla asignaturas exige user_id. Se registra el usuario autenticado
            // que realiza la matrícula para conservar la trazabilidad existente.
            $validated['user_id'] = Auth::id();

            $asignatura = ModelsAsignatura::create($validated);

            // El seguro nace automáticamente con la matrícula y queda pendiente.
            SeguroMatricula::create([
                'asignatura_id' => $asignatura->id,
                'valor' => $seguroValor,
                'estado' => 0,
            ]);

            return $asignatura->load('seguroMatricula');
        });

        return response()->json([
            'message' => 'Matrícula y seguro estudiantil registrados correctamente.',
            'data' => $asignatura,
        ], 201);
    }

    public function show(Request $request)
    {
        return view('home');
    }
}
