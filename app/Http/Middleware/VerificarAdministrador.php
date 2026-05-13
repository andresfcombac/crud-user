<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarAdministrador
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(\Illuminate\Http\Request $request, \Closure $next)
{
    // Verificamos si existe sesión activa y si el rol coincide
    if (session()->has('user_id') && session('user_rol') === 'Administrador') {
        return $next($request);
    }

    // Si es Operador o Soporte, bloqueamos el acceso y lo regresamos con un error
    return redirect()->route('clientes.index')->withErrors([
        'seguridad_error' => ' Acceso denegado: Tu nivel de usuario no tiene permisos para realizar esta acción de edición o eliminación.'
    ]);
}

}
