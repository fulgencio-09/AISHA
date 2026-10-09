<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Closure;
Use Illuminate\Support\Facades\Session;
Use Redirect;
use Illuminate\Http\Request;

class CheckIpAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
       //$ip = $_SERVER['REMOTE_ADDR'];
      
       //echo $estado;
       
       // $ip!="";
        //$ip = request()->ip(); 
       
       /*$acceso = DB::table('users')->where('','=',$estado)
       ->get();*/
    
      
     
        
        $estado = Auth::user()->estado;
       if($estado==='0'){ 
           Session::flash('message','Usted no esta Autorizado.');
            return redirect()->route('login');
            
        }
        //caso contrario seguimos con petición
        return $next($request);
    }
}
