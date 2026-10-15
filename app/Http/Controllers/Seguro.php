<?php

namespace App\Http\Controllers;

use App\Models\SeguroMatricula;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class Seguro extends Controller
{
    /**
     * Listar los seguros registrados.
     */
    public function index(Request $request)
    {
        $query = SeguroMatricula::with([
            'asignatura.estudiante',
        ]);

        if ($request->filled('asignatura_id')) {
            $query->where('asignatura_id', $request->input('asignatura_id'));
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        return $query->orderByDesc('id')->paginate(20);
    }

    /**
     * Registrar un seguro asociado a una matrícula.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asignatura_id' => [
                'required',
                'integer',
                'exists:asignaturas,id',
                Rule::unique('seguro_matriculas', 'asignatura_id'),
            ],
            'valor' => ['required', 'numeric', 'min:0'],
            'observacion' => ['nullable', 'string'],
        ]);

        $validated['estado'] = 0;

        $seguro = SeguroMatricula::create($validated);

        return response()->json([
            'message' => 'Seguro registrado correctamente.',
            'data' => $seguro->load('asignatura.estudiante'),
        ], 201);
    }

    /**
     * Consultar un seguro.
     */
    public function show($id)
    {
        $seguro = SeguroMatricula::with([
            'asignatura.estudiante',
        ])->findOrFail($id);

        return response()->json($seguro);
    }

    /**
     * Registrar el pago único del seguro.
     */
    public function pagar(Request $request, $id)
    {
        $validated = $request->validate([
            'fecha_pago' => ['required', 'date'],
            'forma_pago' => ['required', 'string', 'max:100'],
            'referencia' => ['nullable', 'string', 'max:255'],
            'observacion' => ['nullable', 'string'],
        ]);

        $seguro = SeguroMatricula::findOrFail($id);

        if ((int) $seguro->estado === 1) {
            return response()->json([
                'message' => 'El seguro ya se encuentra pagado.',
            ], 422);
        }

        $seguro->update([
            'estado' => 1,
            'fecha_pago' => $validated['fecha_pago'],
            'forma_pago' => $validated['forma_pago'],
            'referencia' => $validated['referencia'] ?? null,
            'observacion' => $validated['observacion'] ?? $seguro->observacion,
        ]);

        return response()->json([
            'message' => 'Pago del seguro registrado correctamente.',
            'data' => $seguro->fresh()->load('asignatura.estudiante'),
        ]);
    }
}
