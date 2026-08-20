<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Notifications\WelcomeNotification; // <-- IMPORTAÇÃO ADICIONADA AQUI

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Padroniza o Nome (Ex: "cArLoS SILVA" vira "Carlos Silva")
        $nomePadronizado = mb_convert_case(mb_strtolower($request->name, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');

        // Limpa os campos antes de validar e salvar
        $documentoLimpo = preg_replace('/[^0-9]/', '', $request->document);
        $telefoneLimpo  = preg_replace('/[^0-9]/', '', $request->whatsapp);

        // Merge no request para a validação usar os dados limpos e padronizados
        $request->merge([
            'name'     => $nomePadronizado,
            'document' => $documentoLimpo,
            'whatsapp' => $telefoneLimpo
        ]);

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role'     => ['required', 'string', 'in:consumer,salon'],
            'document' => ['required', 'string', 'max:20', 'unique:users,document'],
            'whatsapp' => ['required', 'string', 'min:10', 'max:11'],
        ],
        // 2º Array: Mensagens totalmente personalizadas
        [
            'document.unique' => 'Este CPF ou CNPJ já está cadastrado em outra conta.',
            'email.unique'    => 'Este e-mail já está sendo utilizado.',
            'whatsapp.min'    => 'O número de WhatsApp informado é inválido.',
        ],
        // 3º Array: Tradução das colunas
        [
            'name'     => 'nome completo',
            'email'    => 'e-mail',
            'document' => 'CPF/CNPJ',
            'whatsapp' => 'WhatsApp',
            'password' => 'senha'
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            'document' => $documentoLimpo,
            'phone'    => $telefoneLimpo,
            'password' => Hash::make($request->password),
        ]);

        // DESATIVADO: Impede o Breeze de tentar acionar a tela de verificação
        // event(new Registered($user));
        $user->markEmailAsVerified();

        // GATILHO DE BOAS-VINDAS ADICIONADO AQUI
        // Dispara o e-mail de forma síncrona logo após o registro e verificação
        $user->notify(new WelcomeNotification($user));

        Auth::login($user);

        return redirect('/minha-conta/painel');
    }
}
