<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Permite el acceso únicamente a usuarios administrativos.
     *
     * En AISHA el rol administrativo actual se identifica mediante
     * users.especialidad = "Admin".
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            abort(401);
        }

        $user = auth()->user();

        if (strtolower(trim((string) $user->especialidad)) !== 'admin') {
            abort(403, 'No tienes permisos para acceder a este recurso.');
        }

        return $next($request);
    }
}
