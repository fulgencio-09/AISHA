<?php

namespace App\Http\Controllers;

use App\Models\Estudiante as ModelsEstudiante;
use Illuminate\Http\Request;

class Estudiante extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = ModelsEstudiante::query()->orderBy('id', 'desc');

        if ($request->filled('buscar')) {
            $buscar = trim($request->input('buscar'));
            $query->where(function ($q) use ($buscar) {
                $q->where('documento', 'like', '%' . $buscar . '%')
                    ->orWhere('name', 'like', '%' . $buscar . '%');
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->input('tipo'));
        }

        if ($request->filled('estado')) {
            $query->where('estado', (int) $request->input('estado'));
        }

        $alumno = $query->paginate(10)->withQueryString();

        return [
            'pagination' => [
                'total' => $alumno->total(),
                'current_page' => $alumno->currentPage(),
                'per_page' => $alumno->perPage(),
                'last_page' => $alumno->lastPage(),
                'from' => $alumno->firstItem() ?? 0,
                'to' => $alumno->lastItem() ?? 0,
            ],
            'alumno' => $alumno,
        ];
    }

    public function create(Request $request)
    {
        $inputs = $request->input('documento');
        $alumno = ModelsEstudiante::where('documento', '=', $inputs)->orderBy('id', 'desc')->paginate(5);

        return [
            'pagination' => [
                'total' => $alumno->total(),
                'current_page' => $alumno->currentPage(),
                'per_page' => $alumno->perPage(),
                'last_page' => $alumno->lastPage(),
                'from' => $alumno->firstItem(),
                'to' => $alumno->lastItem(),
            ],
            'alumno' => $alumno,
        ];
    }

    public function buscar(Request $request)
    {
        $inputs = $request->input('documento');
        $alumno = ModelsEstudiante::where('documento', '=', $inputs)->orderBy('id', 'desc')->paginate(5);

        return [
            'pagination' => [
                'total' => $alumno->total(),
                'current_page' => $alumno->currentPage(),
                'per_page' => $alumno->perPage(),
                'last_page' => $alumno->lastPage(),
                'from' => $alumno->firstItem(),
                'to' => $alumno->lastItem(),
            ],
            'alumno' => $alumno,
        ];
    }

    public function update(Request $request, $id)
    {
        $alumno = ModelsEstudiante::findOrFail($id);
        $alumno->tipo = $request->tipo;
        $alumno->documento = $request->documento;
        $alumno->name = $request->name;
        $alumno->direccion = $request->direccion;
        $alumno->telefono = $request->telefono;
        $alumno->correo = $request->correo;
        $alumno->estado = $request->estado;
        $alumno->save();

        return response()->json(['message' => 'Estudiante actualizado correctamente.']);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $inputs = $input['documento'];
        $verificador = ModelsEstudiante::where('documento', '=', $inputs)->get();

        if (isset($verificador[0])) {
            return response()->json('no');
        }

        ModelsEstudiante::create($input);

        return response()->json(['message' => 'Estudiante creado correctamente.']);
    }

    public function show(Request $request)
    {
        return view('home');
    }
}
