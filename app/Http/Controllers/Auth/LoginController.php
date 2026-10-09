<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | Este controlador maneja la autenticación de los usuarios.
    |
    */

    use AuthenticatesUsers;

    /**
     * Redirección después de iniciar sesión correctamente.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;


    /**
     * Crear una nueva instancia del controlador.
     */
    public function __construct()
    {
        /*
        |--------------------------------------------------------------------------
        | Middleware
        |--------------------------------------------------------------------------
        |
        | Solamente usuarios no autenticados pueden acceder al login.
        |
        */

        $this->middleware('guest')->except('logout');
    }


    /**
     * Campo utilizado para autenticación.
     *
     * En tu sistema utilizas "username" y no "email".
     */
    public function username()
    {
        return 'username';
    }


    /**
     * Procesar login.
     *
     * Primero valida:
     *
     * 1. Usuario
     * 2. Contraseña
     * 3. CAPTCHA
     *
     * Después continúa con la autenticación normal de Laravel.
     */
    public function login(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validar campos
        |--------------------------------------------------------------------------
        */

        $request->validate(
            [
                'username' => 'required',
                'password' => 'required',
                'captcha'  => 'required',
            ],
            [
                'username.required' => 'Ingrese el usuario.',
                'password.required' => 'Ingrese la contraseña.',
                'captcha.required'  => 'Ingrese el código de validación.',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Obtener CAPTCHA de la sesión
        |--------------------------------------------------------------------------
        */

        $captcha = $request->session()->get('captcha');


        /*
        |--------------------------------------------------------------------------
        | Verificar que exista CAPTCHA
        |--------------------------------------------------------------------------
        */

        if (!$captcha) {

            return back()
                ->withErrors([
                    'captcha' => 'Debe ingresar un código de validación.'
                ])
                ->withInput(
                    $request->only('username')
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Verificar estructura del CAPTCHA
        |--------------------------------------------------------------------------
        */

        if (
            !isset($captcha['hash']) ||
            !isset($captcha['expires_at'])
        ) {

            $request->session()->forget('captcha');

            return back()
                ->withErrors([
                    'captcha' => 'El código de validación no es válido.'
                ])
                ->withInput(
                    $request->only('username')
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Verificar expiración
        |--------------------------------------------------------------------------
        */

        try {

            $fechaExpiracion = Carbon::parse(
                $captcha['expires_at']
            );

            if (now()->greaterThan($fechaExpiracion)) {

                $request->session()->forget('captcha');

                return back()
                    ->withErrors([
                        'captcha' => 'El código de validación ha expirado. Genere uno nuevo.'
                    ])
                    ->withInput(
                        $request->only('username')
                    );
            }

        } catch (\Exception $e) {

            $request->session()->forget('captcha');

            return back()
                ->withErrors([
                    'captcha' => 'El código de validación no es válido.'
                ])
                ->withInput(
                    $request->only('username')
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Verificar código CAPTCHA
        |--------------------------------------------------------------------------
        |
        | strtoupper permite que:
        |
        | abc123
        |
        | y
        |
        | ABC123
        |
        | sean tratados de la misma manera.
        |
        */

        $codigoIngresado = strtoupper(
            trim($request->captcha)
        );


        /*
        |--------------------------------------------------------------------------
        | Comparar CAPTCHA
        |--------------------------------------------------------------------------
        */

        if (
            !Hash::check(
                $codigoIngresado,
                $captcha['hash']
            )
        ) {

            /*
            |--------------------------------------------------------------------------
            | Eliminar CAPTCHA incorrecto
            |--------------------------------------------------------------------------
            |
            | El usuario tendrá que generar uno nuevo.
            |
            */

            $request->session()->forget('captcha');

            return back()
                ->withErrors([
                    'captcha' => 'El código de validación es incorrecto.'
                ])
                ->withInput(
                    $request->only('username')
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CAPTCHA correcto
        |--------------------------------------------------------------------------
        |
        | Lo eliminamos inmediatamente para evitar que pueda
        | utilizarse nuevamente.
        |
        */

        $request->session()->forget('captcha');


        /*
        |--------------------------------------------------------------------------
        | Intentar autenticación
        |--------------------------------------------------------------------------
        |
        | A partir de aquí Laravel continúa con el proceso normal
        | de autenticación.
        |
        */

        if ($this->attemptLogin($request)) {

            return $this->sendLoginResponse($request);
        }


        /*
        |--------------------------------------------------------------------------
        | Login incorrecto
        |--------------------------------------------------------------------------
        */

        return $this->sendFailedLoginResponse($request);
    }


    /**
     * Respuesta después de login exitoso.
     *
     * Aquí mantenemos tu lógica de control de sesiones.
     */
    protected function sendLoginResponse(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Regenerar sesión
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerate();


        /*
        |--------------------------------------------------------------------------
        | Obtener sesión anterior del usuario
        |--------------------------------------------------------------------------
        */

        $previous_session = Auth::user()->session_id;


        /*
        |--------------------------------------------------------------------------
        | Destruir sesión anterior
        |--------------------------------------------------------------------------
        |
        | Esto evita que el usuario mantenga otra sesión activa.
        |
        */

        if ($previous_session) {

            Session::getHandler()->destroy(
                $previous_session
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Guardar nueva sesión
        |--------------------------------------------------------------------------
        */

        Auth::user()->session_id = Session::getId();

        Auth::user()->save();


        /*
        |--------------------------------------------------------------------------
        | Limpiar intentos fallidos
        |--------------------------------------------------------------------------
        */

        $this->clearLoginAttempts($request);


        /*
        |--------------------------------------------------------------------------
        | Redireccionar
        |--------------------------------------------------------------------------
        */

        return $this->authenticated(
            $request,
            $this->guard()->user()
        ) ?: redirect()->intended(
            $this->redirectPath()
        );
    }


    /**
     * Cerrar sesión.
     */
    public function logout(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Cerrar sesión de Laravel
        |--------------------------------------------------------------------------
        */

        Auth::logout();


        /*
        |--------------------------------------------------------------------------
        | Invalidar sesión
        |--------------------------------------------------------------------------
        */

        $request->session()->invalidate();


        /*
        |--------------------------------------------------------------------------
        | Generar nuevo token CSRF
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerateToken();


        /*
        |--------------------------------------------------------------------------
        | Regresar al login
        |--------------------------------------------------------------------------
        */

        return redirect()->route('login');
    }
}