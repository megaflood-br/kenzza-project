<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Captura o usuário autenticado
        $user = Auth::user();

        // LÓGICA DE REDIRECIONAMENTO CORRIGIDA POR ROLE (Incluindo manager)
        if (in_array($user->role, ['admin', 'manager', 'editor'])) {
            // Admin, Manager e Editor vão direto para o Dashboard Administrativo comum
            return redirect()->intended(route('dashboard'));
        } elseif ($user->role === 'distributor') {
            // Distribuidor vai EXCLUSIVAMENTE para o seu Painel de Parceiro
            return redirect()->intended(route('distributor.panel'));
        }

        // Consumidores (Pessoa Física) e Salões (Profissional) voltam para a Loja
        return redirect()->intended(route('shop.home'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('shop.home');
    }
}
