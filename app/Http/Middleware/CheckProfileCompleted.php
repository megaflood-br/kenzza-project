<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProfileCompleted
{
    public function handle(Request $request, Closure $next): Response
{
    $user = auth()->user();

    // 1. Só atua se for distribuidor
    if ($user && $user->role === 'distributor') {

        // 2. Verifica se o perfil está incompleto
        if (!$user->profile_completed) {

            // 3. A REGRA DE OURO: Só redireciona se já não estiver na página de completar
            // Isso evita que o middleware "empurre" o usuário enquanto ele já está na página correta
            if (!$request->is('complete-profile*')) {
                return redirect()->route('profile.complete');
            }
        } else {
            // 4. Se o perfil JÁ está completo, mas o usuário tenta aceder a /complete-profile
            // nós o tiramos de lá e mandamos para o painel (evita o usuário ficar preso na tela de cadastro)
            if ($request->is('complete-profile*')) {
                return redirect()->route('distributor.panel');
            }
        }
    }

    return $next($request);
}
}
