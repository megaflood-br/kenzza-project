<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Importante para o TinyCP e Cloudflare repassarem o HTTPS correto
        $middleware->trustProxies(at: '*');

        // 1. ALIASES (Apelidos para seus Middlewares personalizados)
        $middleware->alias([
            'profile.complete' => \App\Http\Middleware\CheckProfileCompleted::class,
            'admin'            => \App\Http\Middleware\CheckAdmin::class,
            'role'             => \App\Http\Middleware\CheckRole::class,
        ]);

        // 2. CSRF (Exceções para Webhooks unificadas)
        $middleware->validateCsrfTokens(except: [
            'webhook/*', // Libera InfinitePay, Melhor Envio e Asaas de uma vez só!
        ]);

        // 3. REDIRECIONAMENTO INTELIGENTE (Resolve o Too Many Redirects)
        // Se o usuário já estiver logado e tentar acessar /login ou /register
        $middleware->redirectUsersTo(function () {
            $user = Auth::user();

            if ($user && in_array($user->role, ['distributor', 'admin', 'editor'])) {
                return '/dashboard';
            }

            return '/'; // Consumidor e Salão voltam para a Loja
        });

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
