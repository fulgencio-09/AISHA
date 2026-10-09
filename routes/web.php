<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Generar;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
*/


Route::get('/', function () {
    return view('auth.login');
});
Route::get('/captcha', [Generar::class, 'generar'])->name('captcha');

Auth::routes();
Route::group(['middleware'=>['auth','checkip']], function(){

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//usuario
Route::put('/cambio/passwords/{id}', [App\Http\Controllers\Auth\RegisterController::class, 'password'])->name('pass');
Route::get('/cambio/password', [App\Http\Controllers\Auth\RegisterController::class, 'show'])->name('password');
Route::resource('/user', App\Http\Controllers\Auth\RegisterController::class );
Route::resource('/alumno', App\Http\Controllers\Estudiante::class );
Route::resource('/abono', App\Http\Controllers\Abonos::class );
Route::resource('/nota', App\Http\Controllers\Notas::class );

Route::resource('/matricula', App\Http\Controllers\Asignatura::class );
Route::get('/users/{id}', [App\Http\Controllers\Auth\RegisterController::class, 'consulta'])->name('users');
Route::post('/alumno/buscar', [App\Http\Controllers\Estudiante::class,'buscar'])->name('a/personas');
Route::get('/notas/{id}', [App\Http\Controllers\Notas::class, 'buscar'])->name('n/personas');
Route::post('/nota/busca', [App\Http\Controllers\Notas::class, 'busca'])->name('n/personas');
Route::post('/notas/guardar', [App\Http\Controllers\Notas::class, 'store'])->name('n/personas');
Route::post('/agregar/materia', [App\Http\Controllers\Notas::class, 'agregarnotas'])->name('n/personas');
Route::get('/periodos', [App\Http\Controllers\Notas::class, 'periodo'])->name('n/personas');
//Route::put('/notes/{$id}', [App\Http\Controllers\Notas::class, 'update'])->name('nota/list');
Route::post('/asignatura/buscar', [App\Http\Controllers\Asignatura::class, 'buscar'])->name('as/personas');
Route::post('/abono/buscar', [App\Http\Controllers\Abonos::class, 'buscar'])->name('ab/personas');
Route::post('/notas/buscar', [App\Http\Controllers\Notas::class, 'busca'])->name('ab/personas');
Route::post('/abono/busca', [App\Http\Controllers\Abonos::class, 'busca'])->name('ab/personas');
Route::post('/abono/list', [App\Http\Controllers\Abonos::class, 'create'])->name('abj/personas');
Route::post('/facturas', [App\Http\Controllers\Abonos::class, 'factura'])->name('abj/personas');
Route::get('/abono/credito/{id}', [App\Http\Controllers\Abonos::class, 'credito'])->name('abj/personas');
Route::get('/abono/eliminar/{id}/{user_id}', [App\Http\Controllers\Abonos::class, 'eliminar'])->name('abj/personas');
Route::resource('/reporte', App\Http\Controllers\Reporte::class );
Route::post('/reporte/busca', [App\Http\Controllers\Reporte::class, 'buscar'])->name('abj/personas');
Route::post('/abono/pdf', [App\Http\Controllers\Reporte::class, 'reporte'])->name('abj/personas');
Route::get('/cerrar/{id}', [App\Http\Controllers\Reporte::class, 'cerrar'])->name('as/personas');
//Route::get('/notas/{id}', [App\Http\Controllers\Reporte::class, 'notas'])->name('as/personas');
Route::get('/abrir/{id}', [App\Http\Controllers\Reporte::class, 'abrir'])->name('as/personas');
Route::post('/exportar', [App\Http\Controllers\Reporte::class, 'listadomatriculado'])->name('exportar.listado');
Route::post('/exportar_', [App\Http\Controllers\Reporte::class, 'listado'])->name('exportar.listado');
Route::get('/relacion', [App\Http\Controllers\Reporte::class, 'show'])->name('exportar.listado');
// routes/api.php

Route::post('/enviar', [App\Http\Controllers\CorreoController::class, 'enviar']);

// Dashboard: únicamente usuarios administrativos.
Route::middleware('admin')->group(function(){
    Route::get('/dashboard/matriculados-mes', [App\Http\Controllers\HomeController::class, 'matriculados'])->name('matriculados');
    Route::get('/respaldos', [App\Http\Controllers\HomeController::class, 'log'])->name('log');
    Route::get('/dashboard/resumen-pagos', [App\Http\Controllers\HomeController::class, 'resumenPagos']);
    Route::get('/dashboard/semestres', [App\Http\Controllers\Periodos::class, 'semestres']);
});



});
