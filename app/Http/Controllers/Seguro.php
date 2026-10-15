<?php

namespace App\Http\Controllers;

use App\Models\Asignatura;
use App\Models\SeguroMatricula;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class Seguro extends Controller
{
    /**
     * Listar las matrículas y su estado de seguro.
     * El listado parte de asignaturas para mostrar también matrículas
     * que todavía no tienen un registro en seguro_matriculas.
     */
    public function index(Request $request)
    {
        $query = Asignatura::with(['estudiante', 'seguroMatricula']);

        if ($request->filled('asignatura_id')) {
            $query->where('id', $request->input('asignatura_id'));
        }

        if ($request->filled('ano')) {
            $query->where('año', $request->input('ano'));
        }

        if ($request->filled('periodo')) {
            $query->where('periodo', $request->input('periodo'));
        }

        if ($request->filled('semestre')) {
            $query->where('semestre', $request->input('semestre'));
        }

        if ($request->filled('buscar')) {
            $buscar = trim($request->input('buscar'));
            $query->whereHas('estudiante', function ($q) use ($buscar) {
                $q->where('documento', 'like', '%' . $buscar . '%')
                    ->orWhere('name', 'like', '%' . $buscar . '%');
            });
        }

        if ($request->filled('estado')) {
            $estado = (int) $request->input('estado');

            if ($estado === 1) {
                $query->whereHas('seguroMatricula', function ($q) {
                    $q->where('estado', true);
                });
            } else {
                $query->where(function ($q) {
                    $q->whereDoesntHave('seguroMatricula')
                        ->orWhereHas('seguroMatricula', function ($q2) {
                            $q2->where('estado', false);
                        });
                });
            }
        }

        $porPagina = min(max((int) $request->input('per_page', 10), 1), 50);
        $matriculas = $query->orderByDesc('id')->paginate($porPagina)->withQueryString();

        $matriculas->getCollection()->transform(function ($asignatura) {
            $seguro = $asignatura->seguroMatricula;

            return [
                'id' => $seguro?->id,
                'asignatura_id' => $asignatura->id,
                'valor' => $seguro?->valor,
                'estado' => $seguro?->estado,
                'fecha_pago' => $seguro?->fecha_pago,
                'forma_pago' => $seguro?->forma_pago,
                'referencia' => $seguro?->referencia,
                'observacion' => $seguro?->observacion,
                'seguro_registrado' => $seguro !== null,
                'asignatura' => $asignatura,
            ];
        });

        return $matriculas;
    }

    /** Registrar un seguro asociado a una matrícula. */
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

    /** Consultar un seguro. */
    public function show($id)
    {
        $seguro = SeguroMatricula::with(['asignatura.estudiante'])->findOrFail($id);
        return response()->json($seguro);
    }

    /** Registrar el pago único del seguro. */
    public function pagar(Request $request, $id)
    {
        $validated = $request->validate([
            'fecha_pago' => ['required', 'date'],
            'forma_pago' => ['required', 'string', 'max:100'],
            'referencia' => ['nullable', 'string', 'max:255'],
            'observacion' => ['nullable', 'string'],
            // Si se envía desde el frontend, el valor debe coincidir exactamente
            // con el valor parametrizado en la matrícula.
            'valor_pago' => ['nullable', 'numeric', 'min:0'],
        ]);

        $seguro = SeguroMatricula::findOrFail($id);

        if ((int) $seguro->estado === 1) {
            return response()->json([
                'message' => 'El seguro ya se encuentra pagado.',
            ], 422);
        }

        // El seguro es de pago único: no se permite pagar ni un peso menos
        // ni un peso más del valor registrado en la matrícula.
        $valorEsperadoCentavos = (int) round(((float) $seguro->valor) * 100);
        $valorPago = array_key_exists('valor_pago', $validated)
            ? (float) $validated['valor_pago']
            : (float) $seguro->valor;
        $valorPagoCentavos = (int) round($valorPago * 100);

        if ($valorPagoCentavos !== $valorEsperadoCentavos) {
            return response()->json([
                'message' => 'El valor del pago debe ser exactamente ' . number_format((float) $seguro->valor, 0, ',', '.') . '. No se permite pagar un valor menor ni mayor al indicado en la matrícula.',
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
            'message' => 'Pago del seguro registrado correctamente por ' . number_format((float) $seguro->valor, 0, ',', '.') . '.',
            'data' => $seguro->fresh()->load('asignatura.estudiante'),
        ]);
    }
}
