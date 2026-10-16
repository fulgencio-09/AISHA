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
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

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

        if ($request->filled('semestre')) {
            $query->where('asignaturas.semestre', $request->input('semestre'));
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

    private function prepararHojaExcel($sheet, $ultimaColumna, $ultimaFila, $titulo, $subtitulo, $anchoMinimo = [])
    {
        $sheet->mergeCells("A1:{$ultimaColumna}1");
        $sheet->mergeCells("A2:{$ultimaColumna}2");
        $sheet->getStyle("A1:{$ultimaColumna}1")->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle("A1:{$ultimaColumna}1")->getFont()->getColor()->setARGB('FFFFFF');
        $sheet->getStyle("A1:{$ultimaColumna}1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('536B84');
        $sheet->getStyle("A1:{$ultimaColumna}1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A2:{$ultimaColumna}2")->getFont()->setItalic(true)->setSize(10);
        $sheet->getStyle("A2:{$ultimaColumna}2")->getFont()->getColor()->setARGB('62758A');
        $sheet->getStyle("A2:{$ultimaColumna}2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getRowDimension(2)->setRowHeight(20);

        $headerRow = 4;
        $sheet->getStyle("A{$headerRow}:{$ultimaColumna}{$headerRow}")->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle("A{$headerRow}:{$ultimaColumna}{$headerRow}")->getFont()->getColor()->setARGB('FFFFFF');
        $sheet->getStyle("A{$headerRow}:{$ultimaColumna}{$headerRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('6C63FF');
        $sheet->getStyle("A{$headerRow}:{$ultimaColumna}{$headerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        $sheet->getRowDimension($headerRow)->setRowHeight(30);

        if ($ultimaFila >= 5) {
            $sheet->getStyle("A5:{$ultimaColumna}{$ultimaFila}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A5:{$ultimaColumna}{$ultimaFila}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('D5DCE4');
            for ($fila = 5; $fila <= $ultimaFila; $fila++) {
                if ($fila % 2 === 0) {
                    $sheet->getStyle("A{$fila}:{$ultimaColumna}{$fila}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F7F9FC');
                }
            }
        }

        foreach ($anchoMinimo as $columna => $ancho) {
            $sheet->getColumnDimension($columna)->setWidth($ancho);
        }

        $sheet->freezePane('A5');
        $sheet->setAutoFilter("A4:{$ultimaColumna}" . max(4, $ultimaFila));
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 4);
        $sheet->getPageMargins()->setTop(0.35)->setRight(0.25)->setBottom(0.35)->setLeft(0.25);
        $sheet->getHeaderFooter()->setOddFooter('&LGenerado por AISHA&CFecha: &D&P/&N');

        $sheet->setCellValue('A1', $titulo);
        $sheet->setCellValue('A2', $subtitulo);
    }

    public function deudasExcel(Request $request)
    {
        $items = $this->normalizarDeudas($this->consultaDeudas($request)->get());

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Informe de Deudas');

        $headers = ['Documento', 'Estudiante', 'Año', 'Período', 'Semestre', 'Valor matrícula', 'Pagado', 'Deuda', 'Estado'];
        $sheet->fromArray($headers, null, 'A4');

        $row = 5;
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

        $ultimaFila = max(4, $row - 1);
        $this->prepararHojaExcel(
            $sheet,
            'I',
            $ultimaFila,
            'INFORME DE DEUDAS',
            'Reporte académico y financiero generado por AISHA',
            ['A' => 16, 'B' => 32, 'C' => 10, 'D' => 10, 'E' => 24, 'F' => 18, 'G' => 18, 'H' => 18, 'I' => 14]
        );

        foreach (['F', 'G', 'H'] as $col) {
            $sheet->getStyle($col . '5:' . $col . $ultimaFila)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle($col . '5:' . $col . $ultimaFila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
        $sheet->getStyle('A5:A' . $ultimaFila)->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('C5:D' . $ultimaFila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I5:I' . $ultimaFila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H5:H' . $ultimaFila)->getFont()->setBold(true);

        $nombre = 'informe_deudas_' . ($request->input('ano') ?: 'todos') . '_' . ($request->input('periodo') ?: 'todos') . '_' . ($request->input('semestre') ?: 'todos') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $nombre, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportarPagosExcel(Request $request)
    {
        $inicial = $request->input('finicial');
        $final = $request->input('ffinal');

        $reporte = Abono::join('asignaturas', 'abonos.asignatura_id', '=', 'asignaturas.id')
            ->join('estudiantes', 'estudiantes.id', '=', 'asignaturas.estudiante_id')
            ->join('users', 'users.id', '=', 'abonos.user_id')
            ->where('abonos.fechas', '>=', $inicial)
            ->where('abonos.fechas', '<=', $final)
            ->select(
                'estudiantes.documento as DOCUMENTO',
                'estudiantes.name as ESTUDIANTE',
                'estudiantes.telefono as TELEFONO',
                'estudiantes.correo as CORREO ELECTRONICO',
                'estudiantes.direccion as DIRECCION',
                DB::raw("CASE asignaturas.semestre WHEN 1 THEN 'Introductorio' WHEN 2 THEN 'Primer semestre' WHEN 3 THEN 'Segundo semestre' WHEN 4 THEN 'Tercer semestre' WHEN 5 THEN 'Cuarto semestre' ELSE 'Sin semestre' END AS SEMESTRE"),
                'asignaturas.total as VALOR MATRICULA',
                'asignaturas.valor as PENDIENTE',
                'abonos.cantidad as ABONO REALIZADO',
                'abonos.formapago as FORMA DE PAGO',
                'abonos.fechas as FECHA DE PAGO',
                'users.name as CAJERO'
            )
            ->orderBy('abonos.id', 'asc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pagos realizados');

        $headers = ['Documento', 'Estudiante', 'Teléfono', 'Correo electrónico', 'Dirección', 'Semestre', 'Valor matrícula', 'Pendiente', 'Abono realizado', 'Forma de pago', 'Fecha de pago', 'Cajero'];
        $sheet->fromArray($headers, null, 'A4');

        $row = 5;
        foreach ($reporte as $item) {
            $sheet->fromArray([
                $item->DOCUMENTO,
                $item->ESTUDIANTE,
                $item->TELEFONO,
                $item->{'CORREO ELECTRONICO'},
                $item->DIRECCION,
                $item->SEMESTRE,
                (float) $item->{'VALOR MATRICULA'},
                (float) $item->PENDIENTE,
                (float) $item->{'ABONO REALIZADO'},
                $item->{'FORMA DE PAGO'},
                $item->{'FECHA DE PAGO'},
                $item->CAJERO,
            ], null, 'A' . $row);
            $row++;
        }

        $ultimaFila = max(4, $row - 1);
        $this->prepararHojaExcel(
            $sheet,
            'L',
            $ultimaFila,
            'LISTADO DE PAGOS REALIZADOS',
            'Pagos registrados entre ' . ($inicial ?: 'sin fecha inicial') . ' y ' . ($final ?: 'sin fecha final'),
            ['A' => 16, 'B' => 30, 'C' => 16, 'D' => 28, 'E' => 30, 'F' => 22, 'G' => 18, 'H' => 18, 'I' => 18, 'J' => 20, 'K' => 16, 'L' => 28]
        );

        foreach (['G', 'H', 'I'] as $col) {
            $sheet->getStyle($col . '5:' . $col . $ultimaFila)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle($col . '5:' . $col . $ultimaFila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
        $sheet->getStyle('A5:A' . $ultimaFila)->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('C5:C' . $ultimaFila)->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('K5:K' . $ultimaFila)->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        $sheet->getStyle('F5:F' . $ultimaFila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('J5:K' . $ultimaFila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $filaTotal = $ultimaFila + 2;
        if ($ultimaFila >= 5) {
            $sheet->setCellValue('H' . $filaTotal, 'TOTAL ABONOS');
            $sheet->setCellValue('I' . $filaTotal, '=SUM(I5:I' . $ultimaFila . ')');
            $sheet->getStyle('H' . $filaTotal . ':I' . $filaTotal)->getFont()->setBold(true);
            $sheet->getStyle('H' . $filaTotal . ':I' . $filaTotal)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('E8F5E9');
            $sheet->getStyle('I' . $filaTotal)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('H' . $filaTotal . ':I' . $filaTotal)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('C8D6E5');
        }

        $nombre = 'pagos_realizados_' . ($inicial ?: 'inicio') . '_' . ($final ?: 'fin') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $nombre, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
