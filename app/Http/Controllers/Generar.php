<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class Generar extends Controller
{
    public function generar(Request $request)
    {
        $caracteres = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    
        $codigo = '';
    
        for ($i = 0; $i < 6; $i++) {
            $codigo .= $caracteres[random_int(0, strlen($caracteres) - 1)];
        }
    
        // Guardar código en sesión
        $request->session()->put('captcha', [
            'hash' => Hash::make($codigo),
            'expires_at' => now()->addMinutes(5),
        ]);
    
        // Tamaño de imagen
        $width = 220;
        $height = 60;
    
        $imagen = imagecreatetruecolor($width, $height);
    
        // Colores
        $fondo = imagecolorallocate($imagen, 245, 245, 245);
        $negro = imagecolorallocate($imagen, 30, 30, 30);
        $gris = imagecolorallocate($imagen, 150, 150, 150);
        $grisClaro = imagecolorallocate($imagen, 210, 210, 210);
    
        // Fondo
        imagefill($imagen, 0, 0, $fondo);
    
        // Líneas de ruido
        for ($i = 0; $i < 8; $i++) {
    
            imageline(
                $imagen,
                random_int(0, $width),
                random_int(0, $height),
                random_int(0, $width),
                random_int(0, $height),
                $gris
            );
        }
    
        // Puntos
        for ($i = 0; $i < 200; $i++) {
    
            imagesetpixel(
                $imagen,
                random_int(0, $width - 1),
                random_int(0, $height - 1),
                $grisClaro
            );
        }
    
        // Dibujar caracteres
        $x = 25;
    
        for ($i = 0; $i < strlen($codigo); $i++) {
    
            $y = random_int(20, 40);
    
            // Fuente interna de GD
            $fuente = random_int(4, 5);
    
            imagestring(
                $imagen,
                $fuente,
                $x,
                $y,
                $codigo[$i],
                $negro
            );
    
            $x += 30;
        }
    
        // Mostrar imagen
        ob_start();
    
        imagepng($imagen);
    
        $contenido = ob_get_clean();
    
        imagedestroy($imagen);
    
        return response($contenido)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }
}