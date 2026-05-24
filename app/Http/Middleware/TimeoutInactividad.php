<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TimeoutInactividad
{
    public function handle(Request $request, Closure $next): Response
    {
        // Solo aplicamos la validación si el usuario ya está logueado en el sistema
        if (session()->has('user_id')) {
            $ultimoAcceso = session('ultimo_acceso_tiempo');
            $tiempoLimite = 3 * 60; // 3 minutos convertidos a segundos

            if ($ultimoAcceso && (time() - $ultimoAcceso > $tiempoLimite)) {
                // EXTRACCIÓN: Destruimos la sesión por completo
                session()->forget(['user_id', 'ultimo_acceso_tiempo']);
                
                return redirect()->route('login')->withErrors([
                    'login_error' => 'Tu sesión ha sido cerrada automáticamente por inactividad (Límite: 3 minutos).'
                ]);
            }

            // Actualizamos la estampa de tiempo con la hora del clic actual
            session(['ultimo_acceso_tiempo' => time()]);
        }

        return $next($request);
    }
}
