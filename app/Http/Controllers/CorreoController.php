<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacionGeneral;
use Illuminate\Http\Request;

class CorreoController extends Controller
{
    public function enviar(Request $request)
    {
        $datos = $request->validate([
            'email' => 'required|email',
            'nombre' => 'required',
            'mensaje' => 'required',
        ]);

        Mail::to($datos['email'])->send(new NotificacionGeneral($datos));

        return response()->json(['success' => true]);
    }
}
