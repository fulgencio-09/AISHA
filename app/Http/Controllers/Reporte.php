<?php

namespace App\Http\Controllers;

use App\Models\Abono;
use App\Models\Asignatura;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Reporte extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $date = Carbon::parse();
        $dates = $date->format('Y');

        $abono = Estudiante::join('asignaturas', 'estudiantes.id', '=', 'asignaturas.estudiante_id')
            ->where('estudiantes.estado', '=', 1)
            ->where('asignaturas.valor', '<', 1)
            ->select('estudiantes.id', 'estudiantes.name', 'asignaturas.estado as estad', 'estudiantes.documento', 'asignaturas.id as asignatura_id', 'asignaturas.valor as estado', 'asignaturas.total', 'asignaturas.año', 'asignaturas.semestre', 'asignaturas.valor')
            ->orderBy('asignaturas.id', 'desc')
            ->paginate(5);

        return [
            'pagination' => [
                'total' => $abono->total(),
                'current_page' => $abono->currentPage(),
                'per_page' => $abono->perPage(),
                'last_page' => $abono->lastPage(),
                'from' => $abono->firstItem(),
                'to' => $abono->lastItem(),
            ],
            'abono' => $abono,
            'dates' => $dates,
        ];
    }

    public function buscar(Request $request)
    {
        $inputs = $request->input('identidad');
        $date = Carbon::parse();
        $dates = $date->format('Y');

        $abono = Estudiante::join('asignaturas', 'estudiantes.id', '=', 'asignaturas.estudiante_id')
            ->where('estudiantes.estado', '=', 1)
            ->where('asignaturas.valor', '<', 1)
            ->where('estudiantes.documento', '=', $inputs)
            ->select('estudiantes.id', 'estudiantes.name', 'asignaturas.estado as estad', 'estudiantes.documento', 'asignaturas.id as asignatura_id', 'asignaturas.valor', 'asignaturas.total', 'asignaturas.año', 'asignaturas.semestre')
            ->orderBy('asignaturas.id', 'desc')
            ->paginate(5);

        return [
            'pagination' => [
                'total' => $abono->total(),
                'current_page' => $abono->currentPage(),
                'per_page' => $abono->perPage(),
                'last_page' => $abono->lastPage(),
                'from' => $abono->firstItem(),
                'to' => $abono->lastItem(),
            ],
            'abono' => $abono,
            'dates' => $dates,
        ];
    }

    public function cerrar(Request $request, $id)
    {
        $abono = Asignatura::find($id);
        $abono->estado = 0;
        $abono->save();
    }

    public function abrir(Request $request, $id)
    {
        $abono = Asignatura::find($id);
        $abono->estado = 1;
        $abono->save();
    }

    public function show(Request $request)
    {
        return view('home');
    }

    public function reporte(Request $request)
    {
        $asignatura_id = $request->input('asignatura_id');

        $pdf = Abono::join('asignaturas', 'asignaturas.id', '=', 'abonos.asignatura_id')
            ->join('estudiantes', 'estudiantes.id', '=', 'asignaturas.estudiante_id')
            ->where('asignatura_id', '=', $asignatura_id)
            ->select('name', 'tipo', 'documento', 'cantidad', 'fechas', 'formapago', 'semestre', 'año', 'periodo')
            ->get();

        return ['pdf' => $pdf];
    }

    public function listadomatriculado(Request $request)
    {
        $inicial = $request->input('finicial');
        $final = $request->input('ffinal');

        $reporte = Abono::join('asignaturas', 'abonos.asignatura_id', '=', 'asignaturas.id')
            ->join('estudiantes', 'estudiantes.id', '=', 'asignaturas.estudiante_id')
            ->join('users', 'users.id', '=', 'abonos.user_id')
            ->where('abonos.fechas', '>=', $inicial)
            ->where('abonos.fechas', '<=', $final)
            ->select(
                'estudiantes.documento as N° DOCUMENTO',
                'estudiantes.name as ESTUDIANTES',
                'estudiantes.telefono as TELEFONO',
                'estudiantes.correo as CORREO ELECTRONICO',
                'estudiantes.direccion as DIRECCION',
                DB::raw("CASE asignaturas.semestre WHEN 1 THEN 'Introductorio' WHEN 2 THEN 'Primer semestre' WHEN 3 THEN 'Segundo semestre' WHEN 4 THEN 'Tercero semestre' WHEN 5 THEN 'Cuarto semestre' ELSE 'Sin semestre' END AS SEMESTRE"),
                'asignaturas.total as VALOR MATRICULA',
                'asignaturas.valor as PENDIENTE',
                'abonos.cantidad as ABONOS REALIZADO',
                'abonos.formapago as FORMA DE PAGO',
                'abonos.fechas as FECHA DE PAGO',
                'users.name as CAJERO'
            )
            ->orderBy('abonos.id', 'asc')
            ->get();

        return ['reporte' => $reporte];
    }

    public function listado(Request $request)
    {
        $inicial = $request->input('finicial');
        $final = $request->input('ffinal');

        $reporte = Estudiante::join('asignaturas', 'estudiantes.id', '=', 'asignaturas.estudiante_id')
            ->where('asignaturas.created_at', '>=', $inicial)
            ->where('asignaturas.created_at', '<=', $final)
            ->where('estudiantes.estado', '=', 1)
            ->where('asignaturas.estado', '=', 1)
            ->select(
                'estudiantes.documento as DOCUMENTO',
                'estudiantes.name as ESTUDIANTE',
                'estudiantes.telefono as TELEFONO',
                'estudiantes.correo as CORREO ELECTRONICO',
                'estudiantes.direccion as DIRECCION',
                DB::raw("CASE asignaturas.semestre WHEN 1 THEN 'Introductorio' WHEN 2 THEN 'Primer semestre' WHEN 3 THEN 'Segundo semestre' WHEN 4 THEN 'Tercero semestre' WHEN 5 THEN 'Cuarto semestre' ELSE 'Sin semestre' END AS SEMESTRE"),
                'asignaturas.total as VALOR MATRICULA'
            )
            ->orderBy('estudiantes.documento', 'asc')
            ->get();

        return ['reporte' => $reporte];
    }

    private function consultaDeudas(Request $request)
    {
        $query = Estudiante::query()
            ->join('asignaturas', 'estudiantes.id', '=', 'asignaturas.estudiante_id')
            ->where('estudiantes.estado', 1);

        if ($request->filled('ano')) {
            $query->where('asignaturas.año', $request->input('ano'));
        }

        if ($request->filled('periodo')) {
            $query->where('asignaturas.periodo', $request->input('periodo'));
        }

        if ($request->filled('buscar')) {
            $buscar = trim($request->input('buscar'));
            $query->where(function ($q) use ($buscar) {
                $q->where('estudiantes.documento', 'like', '%' . $buscar . '%')
                    ->orWhere('estudiantes.name', 'like', '%' . $buscar . '%');
            });
        }

        $query->select(
            'estudiantes.id as estudiante_id',
            'estudiantes.documento',
            'estudiantes.name as estudiante',
            'asignaturas.año as ano',
            'asignaturas.periodo',
            DB::raw("SUM(CASE WHEN asignaturas.semestre = 1 THEN 1 ELSE 0 END) as semestre_1"),
            DB::raw("SUM(CASE WHEN asignaturas.semestre = 2 THEN 1 ELSE 0 END) as semestre_2"),
            DB::raw("SUM(CASE WHEN asignaturas.semestre = 3 THEN 1 ELSE 0 END) as semestre_3"),
            DB::raw("SUM(CASE WHEN asignaturas.semestre = 4 THEN 1 ELSE 0 END) as semestre_4"),
            DB::raw("SUM(CASE WHEN asignaturas.semestre = 5 THEN 1 ELSE 0 END) as semestre_5"),
            DB::raw('SUM(COALESCE(asignaturas.total, 0)) as matriculado'),
            DB::raw('SUM(COALESCE(asignaturas.total, 0) - COALESCE(asignaturas.valor, 0)) as pagado'),
            DB::raw('SUM(COALESCE(asignaturas.valor, 0)) as deuda')
        )
            ->groupBy('estudiantes.id', 'estudiantes.documento', 'estudiantes.name', 'asignaturas.año', 'asignaturas.periodo')
            ->orderBy('estudiantes.name');

        if ($request->boolean('solo_deuda', true)) {
            $query->havingRaw('SUM(COALESCE(asignaturas.valor, 0)) > 0');
        }

        return $query;
    }

    private function normalizarDeudas($items)
    {
        return collect($items)->map(function ($item) {
            $semestres = [];
            if ((int) $item->semestre_1 > 0) $semestres[] = 'Introductorio';
            if ((int) $item->semestre_2 > 0) $semestres[] = 'Primer semestre';
            if ((int) $item->semestre_3 > 0) $semestres[] = 'Segundo semestre';
            if ((int) $item->semestre_4 > 0) $semestres[] = 'Tercer semestre';
            if ((int) $item->semestre_5 > 0) $semestres[] = 'Cuarto semestre';

            $item->semestre_nombre = implode(', ', $semestres) ?: 'Sin semestre';
            $item->matriculado = (float) $item->matriculado;
            $item->pagado = (float) $item->pagado;
            $item->deuda = (float) $item->deuda;
            $item->clave = $item->estudiante_id . '-' . $item->ano . '-' . $item->periodo;
            return $item;
        });
    }

    public function anosDeudas()
    {
        $anos = Asignatura::query()
            ->whereNotNull('año')
            ->distinct()
            ->orderByDesc('año')
            ->pluck('año')
            ->values();

        return ['anos' => $anos];
    }

    public function deudas(Request $request)
    {
        $paginado = $this->consultaDeudas($request)->paginate(15)->appends($request->query());
        $items = $this->normalizarDeudas($paginado->getCollection());
        $paginado->setCollection($items);

        $resumenQuery = clone $this->consultaDeudas($request);
        $resumen = DB::query()->fromSub($resumenQuery, 'deudas')->selectRaw(
            'COUNT(*) as estudiantes, COALESCE(SUM(matriculado), 0) as matriculado, COALESCE(SUM(deuda), 0) as deuda'
        )->first();

        return [
            'deudas' => $paginado,
            'pagination' => [
                'total' => $paginado->total(),
                'current_page' => $paginado->currentPage(),
                'per_page' => $paginado->perPage(),
                'last_page' => $paginado->lastPage(),
                'from' => $paginado->firstItem(),
                'to' => $paginado->lastItem(),
            ],
            'resumen' => [
                'estudiantes' => (int) ($resumen->estudiantes ?? 0),
                'matriculado' => (float) ($resumen->matriculado ?? 0),
                'deuda' => (float) ($resumen->deuda ?? 0),
            ],
        ];
    }

    public function deudasExcel(Request $request)
    {
        $items = $this->normalizarDeudas($this->consultaDeudas($request)->get());

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Informe de Deudas');

        $headers = ['Documento', 'Estudiante', 'Año', 'Período', 'Semestre', 'Valor matrícula', 'Pagado', 'Deuda', 'Estado'];
        $sheet->fromArray($headers, null, 'A1');

        $row = 2;
        foreach ($items as $item) {
            $sheet->fromArray([
                $item->documento,
                $item->estudiante,
                $item->ano,
                $item->periodo,
                $item->semestre_nombre,
                $item->matriculado,
                $item->pagado,
                $item->deuda,
                $item->deuda > 0 ? 'Pendiente' : 'Pagado',
            ], null, 'A' . $row);
            $row++;
        }

        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        $sheet->getStyle('A1:I1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('D9EAF7');
        $sheet->getStyle('A1:I1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:I' . max(1, $row - 1));

        foreach (['F', 'G', 'H'] as $col) {
            $sheet->getStyle($col . '2:' . $col . max(2, $row - 1))->getNumberFormat()->setFormatCode('#,##0');
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle('H2:H' . max(2, $row - 1))->getFont()->setBold(true);

        $nombre = 'informe_deudas_' . ($request->input('ano') ?: 'todos') . '_' . ($request->input('periodo') ?: 'todos') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $nombre, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
