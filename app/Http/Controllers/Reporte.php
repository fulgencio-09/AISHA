<?php

namespace App\Http\Controllers;


use App\Models\Abono;
use App\Models\Asignatura;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
class Reporte extends Controller
{ public function __construct()
       
    {
        $this->middleware('auth');
    }
    public function index(Request $request){

        $date = Carbon::parse();
        $dates = $date->format('Y');

        $abono=Estudiante::join('asignaturas','estudiantes.id','=','asignaturas.estudiante_id')
        ->where('estudiantes.estado','=', 1)              
        ->where('asignaturas.valor','<', 1) 
        ->select('estudiantes.id','estudiantes.name', 'asignaturas.estado as estad', 'estudiantes.documento', 'asignaturas.id as asignatura_id','asignaturas.valor as estado' ,'asignaturas.total','asignaturas.año','asignaturas.semestre','asignaturas.valor')
        ->orderBy('asignaturas.id', 'desc')
        ->paginate(5);
        return [
         'pagination'=>[
             'total'=>$abono->total(),
             'current_page'=>$abono->currentPage(),
             'per_page'=>$abono->perPage(),
             'last_page'=>$abono->lastPage(),
             'from'=>$abono->firstItem(),
             'last_page'=>$abono->lastPage(),
             'to'=>$abono->lastPage(),
           ],
           'abono'=>$abono,
           'dates'=>$dates
        ];
     }
     public function buscar(Request $request){
        $input=$request->all();
        $inputs=$input['identidad']; 
        $date = Carbon::parse();
        $dates = $date->format('Y');
       
        $abono=Estudiante::join('asignaturas','estudiantes.id','=','asignaturas.estudiante_id')
        ->where('estudiantes.estado','=', 1)              
        ->where('asignaturas.valor','<', 1) 
        ->where('estudiantes.documento','=', $inputs ) 
        ->select('estudiantes.id','estudiantes.name', 'asignaturas.estado as estad', 'estudiantes.documento', 'asignaturas.id as asignatura_id','asignaturas.valor' ,'asignaturas.total','asignaturas.año','asignaturas.semestre')
        ->orderBy('asignaturas.id', 'desc')
        ->paginate(5);
        return [
         'pagination'=>[
             'total'=>$abono->total(),
             'current_page'=>$abono->currentPage(),
             'per_page'=>$abono->perPage(),
             'last_page'=>$abono->lastPage(),
             'from'=>$abono->firstItem(),
             'last_page'=>$abono->lastPage(),
             'to'=>$abono->lastPage(),
           ],
           'abono'=>$abono,
           'dates'=>$dates
        ];
     }
     public function cerrar(Request $request, $id){
        $cero = 0;
        $abono = Asignatura::find($id);        
       $abono->estado = $cero;
        $abono->save();
     }
    public function abrir(Request $request, $id){
        $cero = 1;
        $abono = Asignatura::find($id);        
       $abono->estado = $cero;
        $abono->save();
        

    }
     public function show(Request $request){
        return view('home');
     }
     public function reporte(Request $request){
       
       
        $input=$request->all();           
        $asignatura_id=$input['asignatura_id']; 

        $pdf=Abono::join('asignaturas','asignaturas.id','=','abonos.asignatura_id')
        ->join('estudiantes','estudiantes.id','=','asignaturas.estudiante_id')
        ->where('asignatura_id','=', $asignatura_id)
        ->select('name','tipo','documento','cantidad','fechas','formapago','semestre','año', 'periodo')     
        ->get();
        
        

        
        return[
           
            'pdf'=>$pdf,
           
        ];
    }
    public function listadomatriculado(Request $request){
        $input=$request->all();
        $inicial=$input['finicial'];    
        $final=$input['ffinal']; 
        $reporte = Abono::join('asignaturas','abonos.asignatura_id','=','asignaturas.id')
       ->join('estudiantes','estudiantes.id','=','asignaturas.estudiante_id')
       ->join('users','users.id','=','abonos.user_id')
        ->where('abonos.fechas','>=', $inicial)              
        ->where('abonos.fechas','<=', $final ) 
        ->select('estudiantes.documento as N° DOCUMENTO', 'estudiantes.name as ESTUDIANTES', 'estudiantes.telefono as TELEFONO','estudiantes.correo as CORREO ELECTRONICO', 'estudiantes.direccion as DIRECCION',
         DB::raw("
        CASE asignaturas.semestre
            WHEN 1 THEN 'Introductorio'
            WHEN 2 THEN 'Primer semestre'
            WHEN 3 THEN 'Segundo semestre'
            WHEN 4 THEN 'Tercero semestre'
            WHEN 5 THEN 'Cuarto semestre'
            ELSE 'Sin semestre'
        END AS SEMESTRE
    "), 'asignaturas.total as VALOR MATRICULA'
        ,'asignaturas.valor as PENDIENTE' ,'abonos.cantidad as ABONOS REALIZADO' ,
        'abonos.formapago as FORMA DE PAGO'  ,'abonos.fechas as FECHA DE PAGO','users.name as CAJERO') ->orderBy('abonos.id', 'asc')
        ->get();
        return[
            'reporte'=>$reporte,
            
        ];
        
    }
    public function listado(Request $request){
        $input=$request->all();
        $inicial=$input['finicial'];    
        $final=$input['ffinal']; 
       $reporte =Estudiante::join('asignaturas','estudiantes.id','=','asignaturas.estudiante_id')       
        ->where('asignaturas.created_at','>=', $inicial)              
        ->where('asignaturas.created_at','<=', $final ) 
        ->where('estudiantes.estado','=' , 1)
        ->where('asignaturas.estado','=', 1)        
        ->select('estudiantes.documento as DOCUMENTO', 'estudiantes.name as ESTUDIANTE', 'estudiantes.telefono as TELEFONO'
        ,'estudiantes.correo as CORREO ELECTRONICO', 'estudiantes.direccion as DIRECCION', DB::raw("
        CASE asignaturas.semestre
            WHEN 1 THEN 'Introductorio'
            WHEN 2 THEN 'Primer semestre'
            WHEN 3 THEN 'Segundo semestre'
            WHEN 4 THEN 'Tercero semestre'
            WHEN 5 THEN 'Cuarto semestre'
            ELSE 'Sin semestre'
        END AS SEMESTRE
    "),  'asignaturas.total as VALOR MATRICULA'
         ) ->orderBy('estudiantes.documento', 'asc')
        ->get();
        return[
            'reporte'=>$reporte,
            
        ];

}
}