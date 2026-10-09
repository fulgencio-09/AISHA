<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Nota;
use App\Models\Periodo;
use App\Models\Asignatura;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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
            //->join('notas','asignaturas.id','=', 'notas.asignatura_id')        
            ->where('asignaturas.estado', '=', 1)
            ->where('estudiantes.estado', '=', 1)
            //->where('asignaturas.año', '=' , $dates)
            ->select('estudiantes.name', 'asignaturas.mat_matr','estudiantes.documento', 'asignaturas.semestre', 'asignaturas.id as asignatura_id', 'asignaturas.año', 'estudiantes.id')->orderBy('id', 'desc')->paginate(10);

        if (isset($nota[0])) {
        }
        return [
            'pagination' => [
                'total' => $nota->total(),
                'current_page' => $nota->currentPage(),
                'per_page' => $nota->perPage(),
                'last_page' => $nota->lastPage(),
                'from' => $nota->firstItem(),
                'last_page' => $nota->lastPage(),
                'to' => $nota->lastPage(),
            ],
            'nota' => $nota,
        ];
    }
    public function busca(Request $request){
        $input = $request->all();
        $identidad = $input['identidad'];
        $nota = Estudiante::join('asignaturas', 'estudiantes.id', '=', 'asignaturas.estudiante_id')
             ->where('estudiantes.documento', '=', $identidad)        
            ->where('asignaturas.estado', '=', 1)                 
            ->select('estudiantes.name', 'asignaturas.mat_matr','estudiantes.documento', 'asignaturas.semestre', 'asignaturas.id as asignatura_id', 'asignaturas.año', 'estudiantes.id')->orderBy('id', 'desc')->paginate(10);

        if (isset($nota[0])) {
        }
        return [
            'pagination' => [
                'total' => $nota->total(),
                'current_page' => $nota->currentPage(),
                'per_page' => $nota->perPage(),
                'last_page' => $nota->lastPage(),
                'from' => $nota->firstItem(),
                'last_page' => $nota->lastPage(),
                'to' => $nota->lastPage(),
            ],
            'nota' => $nota,
        ];
    }
    public function buscar($id)
    {
        $notas = Nota::where('asignatura_id', '=', $id)->get();


        return response()->json($notas);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $data = $request->validate([
            'semestre' => 'required|integer|min:1|max:5',
            'materias_id' => 'required|integer|exists:materias,id',
            'asignatura_id' => 'required|integer|exists:asignaturas,id',
            'materia' => 'required|string|max:255',
            'corte1' => 'nullable|numeric|min:0|max:5',
            'corte2' => 'nullable|numeric|min:0|max:5',
            'corte3' => 'nullable|numeric|min:0|max:5',
            //'habilitada_recuperacion' => 'boolean'
        ]);

        return Nota::create($data);
    }

    public function show(Request $request)
    {
        return view('home');
    }

    public function update(Request $request, $id)
    {
        //dd($request->all());
        $asignatura_id = $request->asignatura_id;
        $notas = Nota::find($id);
        $notas->corte1 = $request->corte1;
        $notas->corte2 = $request->corte2;
        $notas->corte3 = $request->corte3;
        $notas->save();

        return [
            'asignatura_id' => $asignatura_id
        ];
    }
    public function destroy(Nota $nota)
    {
        $nota->delete();
        return response()->json(['message' => 'Nota eliminada']);
    }
    public function periodo()
    {
        $periodos = Periodo::all();
        return response()->json($periodos);
    }
    public function agregarnotas(Request $request)
    {//dd($request->all());
        $id = $request->semestre;
        $matricula = $request->matricula;
        $notas = Asignatura::join('materias', 'asignaturas.semestre', '=', 'materias.semestre')
            ->where('asignaturas.semestre','=', $id)
            ->where('asignaturas.id','=', $matricula)
            ->select(
                'asignaturas.semestre',
                'asignaturas.id as asignatura_id',
                'materias.id as materia_id',
                'materias.materia'
            )
            ->get();
        $matriculado = 'Si';
        $not = Asignatura::find($matricula);
        $not->mat_matr = $matriculado;
        $not->save();
        foreach ($notas as $nota) {
            Nota::updateOrCreate(
                [
                    'user_id'       => $request->user_id,
                    'asignatura_id' => $nota->asignatura_id,
                    'materia_id'   => $nota->materia_id,
                    'semestre'      => $nota->semestre

                ],
                [
                    'materia'         => $nota->materia,
                    'matriculado_en'  => now()
                ]

            );
        }

        return response()->json(['message' => 'Notas registradas o actualizadas correctamente']);
    }
}
