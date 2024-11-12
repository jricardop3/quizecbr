<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Verifica se o usuário está autenticado e se o papel (role) é admin
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        // Caso contrário, redireciona para uma página de erro ou responde com uma mensagem de autorização
        return response()->json(['error' => 'Acesso não autorizado. necéssario autorização de administrador'], 403);
    }
}
