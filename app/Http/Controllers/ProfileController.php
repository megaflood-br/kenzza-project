<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Exibe o formulário de perfil do usuário.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Atualiza as informações de perfil (Geral ou Endereço/Documentos).
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Se o formulário enviado contiver campos de endereço ou documentos
        if ($request->has('zip_code') || $request->has('document')) {
            $data = $request->validate([
                'document'     => ['required', 'string', 'max:20'],
                'phone'        => ['required', 'string', 'max:20'],
                'zip_code'     => ['required', 'string', 'max:10'],
                'street'       => ['required', 'string', 'max:255'],
                'number'       => ['required', 'string', 'max:20'],
                'complement'   => ['nullable', 'string', 'max:255'],
                'neighborhood' => ['required', 'string', 'max:255'],
                'city'         => ['required', 'string', 'max:255'],
                'state'        => ['required', 'string', 'max:2'],
            ]);

            // Limpa o documento antes de tentar salvar
            $data['document'] = preg_replace('/\D/', '', $data['document']);

        } else {
            // Validação padrão para Nome e Email
            $data = $request->validate([
                'name'  => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            ]);
        }

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Tenta salvar e barra o erro de CPF/CNPJ duplicado
        try {
            $user->save();
        } catch (\Illuminate\Database\QueryException $e) {
            // Verifica se o erro é de entrada duplicada (1062)
            if ($e->errorInfo[1] == 1062) {
                return back()->withErrors([
                    'document' => 'Este CPF/CNPJ já está cadastrado em outra conta.'
                ])->withInput();
            }
            throw $e;
        }

        // Se a requisição veio do Checkout (via Modal), apenas voltamos
        // Caso contrário, redirecionamos para o edit do perfil com a mensagem de status
        if ($request->header('referer') && str_contains($request->header('referer'), 'checkout')) {
            return back()->with('success', 'Endereço atualizado com sucesso!');
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Remove a conta do usuário.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Exibe a página de finalização de cadastro obrigatório.
     */
    public function complete(): View
    {
        return view('profile.complete');
    }

    /**
     * Processa a finalização do cadastro (Trava de Perfil).
     */
    public function updateComplete(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'document'     => 'required|string|max:255',
            'phone'        => 'required|string',
            'zip_code'     => 'required|string',
            'street'       => 'required|string',
            'number'       => 'required|string',
            'complement'   => 'nullable|string|max:255',
            'neighborhood' => 'required|string',
            'city'         => 'required|string',
            'state'        => 'required|string|max:2',
        ]);

        try {
            // Limpa máscaras (remove . - / ( ) e espaços)
            $document = preg_replace('/\D/', '', $request->document);
            $zip_code = preg_replace('/\D/', '', $request->zip_code);
            $phone    = preg_replace('/\D/', '', $request->phone);

            $user->forceFill([
                'document'          => $document,
                'phone'             => $phone,
                'zip_code'          => $zip_code,
                'street'            => $request->street,
                'number'            => $request->number,
                'complement'        => $request->complement,
                'neighborhood'      => $request->neighborhood,
                'city'              => $request->city,
                'state'             => $request->state,
                'profile_completed' => true,
            ])->save();

            //$request->session()->flash('success', 'Perfil finalizado! Bem-vindo.');
           // return redirect()->route('dashboard');
           return redirect()->route('painel.redirect')->with('success', 'Perfil finalizado! Bem-vindo.');

        } catch (\Illuminate\Database\QueryException $e) {
            // Trava específica de entrada duplicada para o formulário de trava
            if ($e->errorInfo[1] == 1062) {
                return back()->withErrors([
                    'document' => 'Atenção: Este CPF/CNPJ já está vinculado a outra conta!'
                ])->withInput();
            }
            return back()->withErrors(['error' => 'Erro de banco de dados: ' . $e->getMessage()])->withInput();
        } catch (\Exception $e) {
            // Qualquer outro erro fatal
            return back()->withErrors(['error' => 'Erro técnico: ' . $e->getMessage()])->withInput();
        }
    }
}
