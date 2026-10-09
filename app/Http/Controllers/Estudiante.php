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
    public function index(Request $request){
       $alumno=ModelsEstudiante::orderBy('id', 'desc')->paginate(5);
       return [
        'pagination'=>[
            'total'=>$alumno->total(),
            'current_page'=>$alumno->currentPage(),
            'per_page'=>$alumno->perPage(),
            'last_page'=>$alumno->lastPage(),
            'from'=>$alumno->firstItem(),
            'last_page'=>$alumno->lastPage(),
            'to'=>$alumno->lastPage(),
          ],
          'alumno'=>$alumno,
       ];
    }
    public function create(Request $request ){
        $input=$request->all();
        $inputs=$input['documento']; 
        $alumno=ModelsEstudiante::where('documento','=', $inputs)->orderBy('id', 'desc')->paginate(5);
        return [
            'pagination'=>[
                'total'=>$alumno->total(),
                'current_page'=>$alumno->currentPage(),
                'per_page'=>$alumno->perPage(),
                'last_page'=>$alumno->lastPage(),
                'from'=>$alumno->firstItem(),
                'last_page'=>$alumno->lastPage(),
                'to'=>$alumno->lastPage(),
              ],
              'alumno'=>$alumno,
           ];
        
    }
    public function buscar(Request $request ){
        $input=$request->all();
        $inputs=$input['documento']; 
        $alumno=ModelsEstudiante::where('documento','=', $inputs) ->orderBy('id', 'desc')->paginate(5);
        return [
            'pagination'=>[
                'total'=>$alumno->total(),
                'current_page'=>$alumno->currentPage(),
                'per_page'=>$alumno->perPage(),
                'last_page'=>$alumno->lastPage(),
                'from'=>$alumno->firstItem(),
                'last_page'=>$alumno->lastPage(),
                'to'=>$alumno->lastPage(),
              ],
              'alumno'=>$alumno,
           ];
       
    }
    public function update(Request $request , $id){
        $alumno=ModelsEstudiante::find($id); 
        $alumno->tipo = $request->tipo;
        $alumno->documento = $request->documento;    
        $alumno->name = $request->name;
        $alumno->direccion = $request->direccion;
        $alumno->telefono = $request->telefono;
        $alumno->correo = $request->correo;
        $alumno->estado = $request->estado;
        
       
        $alumno->save();
      
      
    }
    public function store(Request $request){
        $input=$request->all();
        $inputs = $input['documento'];
        $verificador=ModelsEstudiante::where('documento','=', $inputs)->get();
        if(isset($verificador[0])) { 
            return response()->json('no');
        }
        $alumno=ModelsEstudiante::create($input);
        
    }
    public function show(Request $request){
        return view('home');
    }

}
