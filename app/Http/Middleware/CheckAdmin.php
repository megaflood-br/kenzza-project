<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
{
    // Verifica se o usuário está logado E se o campo 'role' no banco é 'admin'
    // Ajuste 'role' para o nome da sua coluna no banco (ex: is_admin, type, etc)
    if (auth()->check() && auth()->user()->role === 'admin') {
        return $next($request);
    }

    // Se não for admin, redireciona para a home ou exibe erro 403
    return redirect('/')->with('error', 'Acesso negado. Área restrita a administradores.');
}
}
