<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
{
    $userRole = auth()->user() ? strtolower(trim(auth()->user()->role)) : null;
    $allowedRoles = array_map('strtolower', $roles);

    if (!auth()->check() || !in_array($userRole, $allowedRoles)) {
        \Log::warning('Acesso negado para role: ' . $userRole . ' nas rotas permitidas: ' . implode(',', $allowedRoles));
        return redirect()->route('shop.home')->with('error', 'Acesso negado.');
    }

    return $next($request);
}
}
