<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Services\WhatsAppService;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Abono;
use App\Models\Asignatura;
use App\Models\Estudiante;
use App\Models\Respaldo;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class Abonos extends Controller

{
    protected $whatsapp;
    public function __construct(WhatsAppService $whatsapp)

    {
        $this->middleware('auth');
        $this->whatsapp = $whatsapp;
    }
    public function factura(Request $request)
    {
        $data = [
            'name'      => $request->input('name'),
            'documento' => $request->input('documento'),
            'semestre'  => $request->input('semestre'),
            'formapago' => $request->input('formapago'),
            'fecha'     => $request->input('fecha'),
            'deuda'     => $request->input('deuda'),
            'cantidad'  => $request->input('cantidad'),
        ];
    
        // 1. Generar PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('factura.pdf', $data);
    
        // 2. Guardar en storage
        $fileName = 'facturas/pdf-' . time() . '.pdf';
        \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $pdf->output());
    
        // 3. URL pública
        $pdfUrl = asset('storage/' . $fileName);
    
        // 4. Enviar por WhatsApp
        $to = $request->input('to'); // número con indicativo, ej: 573001112233
        $result = $this->whatsapp->sendTemplate(
            $to,
            $pdfUrl,
            "Factura de Pago - {$data['name']}",
           
        );
    
        return response()->json([
            'status' => 'ok',
            'pdf' => $pdfUrl,
            'whatsapp_response' => $result
        ]);
    }
    public function index(Request $request)
    {

        $date = Carbon::parse();
        $dates = $date->format('Y');

        $abono = Estudiante::join('asignaturas', 'estudiantes.id', '=', 'asignaturas.estudiante_id')
            ->where('asignaturas.valor', '>', 0)
            ->select('estudiantes.id', 'estudiantes.name',  'asignaturas.sede', 'asignaturas.periodo', 'estudiantes.documento', 'asignaturas.valor as valor', 'asignaturas.año', 'asignaturas.id as asignatura_id', 'asignaturas.semestre')
            ->orderBy('asignaturas.id', 'desc')
            ->paginate(5);
        return [
            'pagination' => [
                'total' => $abono->total(),
                'current_page' => $abono->currentPage(),
                'per_page' => $abono->perPage(),
                'last_page' => $abono->lastPage(),
                'from' => $abono->firstItem(),
                'last_page' => $abono->lastPage(),
                'to' => $abono->lastPage(),
            ],
            'abono' => $abono,
            'dates' => $dates
        ];
    }

    public function create(Request $request)
    {
        $input = $request->all();
        $inputs = $input['id'];
        //$año=$input['año']; 
        $asignatura_id = $input['asignatura_id'];
        $abono = Abono::join('asignaturas', 'asignaturas.id', '=', 'abonos.asignatura_id')
            ->join('estudiantes', 'estudiantes.id', '=', 'asignaturas.estudiante_id')
            ->leftjoin('users', 'users.id', '=', 'abonos.user_id')
            ->where('abonos.asignatura_id', '=', $asignatura_id)
            ->where('abonos.estado', '=', 1)
            ->select('estudiantes.name', 'estudiantes.documento', 'abonos.formapago', 'users.name as nombre', 'abonos.deuda', 'abonos.id', 'abonos.asignatura_id', 'asignaturas.semestre', 'estudiantes.telefono','asignaturas.año', 'abonos.cantidad', 'asignaturas.valor', 'abonos.fechas')
            ->get();

        return [
            'abono' => $abono
        ];
    }
    public function credito(Request $request, $id)
    {
        //dd($request->all());
        $date = Carbon::parse();
        $dates = $date->format('Y');

        $cred = Asignatura::join('estudiantes', 'estudiantes.id', '=', 'asignaturas.estudiante_id')
            ->where('asignaturas.id', '=', $id)
            ->where('asignaturas.estado', '=', 1)
            ->where('valor', '>', 0)
            ->select('valor', 'asignaturas.id', 'año', 'periodo', 'semestre', 'name', 'documento')
            ->get();

        return [
            'cred' => $cred
        ];
    }
    public function buscar(Request $request)
    {
        $input = $request->all();
        $inputs = $input['fecha'];
        $estudiante_id = $input['estudiante_id'];
        $abono = Abono::where('fecha', '=', $inputs)
            ->where('estudiante_id', '=', $estudiante_id)
            ->where('estado', '=', 1)
            ->orderBy('id', 'desc')->paginate(5);
        return [
            'abono' => $abono
        ];
    }
    public function busca(Request $request)
    {
        $input = $request->all();
        $abono = Estudiante::join('asignaturas', 'estudiantes.id', '=', 'asignaturas.estudiante_id')
            ->where('estudiantes.estado', '=', 1)
            ->where('asignaturas.valor', '>', 0)
            ->where('estudiantes.documento',  'LIKE', '%' .  $input['identidad']  . '%')
            ->select('estudiantes.id', 'asignaturas.id as asignatura_id', 'estudiantes.name', 'estudiantes.telefono','estudiantes.documento', 'asignaturas.valor', 'asignaturas.año', 'asignaturas.semestre', 'asignaturas.valor')
            ->orderBy('id', 'desc')
            ->paginate(5);
        return [
            'pagination' => [
                'total' => $abono->total(),
                'current_page' => $abono->currentPage(),
                'per_page' => $abono->perPage(),
                'last_page' => $abono->lastPage(),
                'from' => $abono->firstItem(),
                'last_page' => $abono->lastPage(),
                'to' => $abono->lastPage(),
            ],
            'abono' => $abono,

        ];
    }
    public function eliminar(Request $request, $id, $user_id)
    {



        $debitado = Abono::join('asignaturas', 'asignaturas.id', '=', 'abonos.asignatura_id')
            ->where('abonos.id', '=', $id)
            ->select('abonos.id', 'abonos.asignatura_id',  'abonos.cantidad', 'abonos.deuda', 'abonos.fechas', 'asignaturas.valor')
            ->first();
        /// dd($debitado->all());
        if ($debitado) {

            // Guardar respaldo
            Respaldo::create([
                'id_matricula'  => $debitado->asignatura_id,
                'valores'       => $debitado->cantidad,
                'fecha'         => $debitado->fechas,
                'deudas'         => $debitado->deuda,
                'user_id'         => $user_id,
                'respaldo_en'   => now(),
            ]);

            // Sumar cantidad + valor
            $suma = $debitado->valor + $debitado->cantidad;

            // Actualizar la asignatura
            $editar = Asignatura::find($debitado->asignatura_id);
            if ($editar) {
                $editar->valor = $suma;
                $editar->save();
            }
        }
        Abono::find($debitado->id)->delete();
        return [
            'debitado' => $debitado,
        ];
    }
    public function store(Request $request)
    {
        // dd($request->all());
        $input = $request->all();
        $asignatura_id = $input['asignatura_id'];
        $cantidad = $input['cantidad'];


        $abonos = Asignatura::join('estudiantes', 'estudiantes.id', '=', 'asignaturas.estudiante_id')
            ->where('asignaturas.id', '=', $asignatura_id)
            ->select('estudiantes.id', 'estudiantes.name', 'estudiantes.documento', 'asignaturas.valor', 'asignaturas.total', 'asignaturas.año', 'asignaturas.semestre')
            ->get();
        if (isset($abonos[0])) {

            foreach ($abonos as $a) {

                $credit = $a->valor;
                $total = $a->total;
                $resta = ($credit - $cantidad);

                if ($cantidad > $credit) {
                    return response()->json('no');
                } else {
                    $abono = Abono::create($input);
                    $credi = Asignatura::find($asignatura_id);
                    $credi->valor = $resta;
                    $credi->save();
                }
            }
        }
        return ['input' => $input];
    }
    public function show(Request $request)
    {
        return view('home');
    }
    public function reporte(Request $request)
    {

        $date = Carbon::parse();
        $ano = $date->format('Y');

        $mes = $date->format('m');

        $dia = $date->format('d');
        $input = $request->all();
        $inputs = $input['id'];
        $año = $input['año'];
        $semestre = $input['semestre'];
        $asignatura_id = $input['asignatura_id'];
        $pdf = Abono::where('estudiante_id', '=', $inputs)
            // ->where('fecha', '=', $año)
            ->where('asignatura_id', '=', $asignatura_id)
            ->where('estado', '=', 1)->get();

        $estudent = Estudiante::join('asignaturas', 'estudiantes.id', '=', 'asignaturas.estudiante_id')
            ->where('estudiantes.id', '=', $inputs)
            ->where('estudiantes.estado', '=', 1)
            ->where('asignaturas.año', '=', $año)
            ->where('asignaturas.cantidad', '<', 1)
            ->where('asignaturas.semestre', '=', $semestre)
            ->select('estudiantes.id', 'estudiantes.name', 'estudiantes.documento', 'asignaturas.cantidad as estado', 'asignaturas.año', 'asignaturas.semestre', 'asignaturas.cantidad')
            ->get();
        return [

            'estudent' => $estudent,
            'pdf' => $pdf,
            'dia' => $dia,
            'mes' => $mes,
            'ano' => $ano

        ];
    }
}
