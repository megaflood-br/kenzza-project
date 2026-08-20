<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Listagem de usuários com filtros.
     */
    public function index(Request $request)
    {
        // Agora o manager também pode acessar
        abort_if(!in_array(Auth::user()->role, ['admin', 'editor', 'manager']), 403);

        $search = $request->query('search');
        $role = $request->query('role');

        $query = User::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // TRAVA DE SEGURANÇA 1: Se for manager, força a busca a mostrar apenas representantes
        if (Auth::user()->role === 'manager') {
            $query->where('role', 'representative');
        } elseif ($role) {
            $query->where('role', $role);
        }

        $users = $query->latest()->get();

        return view('users.index', compact('users', 'search', 'role'));
    }

    public function create()
    {
        abort_if(!in_array(Auth::user()->role, ['admin', 'editor', 'manager']), 403);
        return view('users.create');
    }

    public function store(Request $request)
    {
        abort_if(!in_array(Auth::user()->role, ['admin', 'editor', 'manager']), 403);

        // TRAVA DE SEGURANÇA 2: Se for manager, injetamos 'representative' à força na requisição
        if (Auth::user()->role === 'manager') {
            $request->merge(['role' => 'representative']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:admin,manager,editor,distributor,representative,salon,consumer',
            'tier' => 'nullable|string|in:gold,black,diamante',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'tier' => $request->role === 'distributor' ? $request->tier : null,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')->with('success', 'Usuário criado com sucesso!');
    }

    public function edit(User $user)
    {
        abort_if(!in_array(Auth::user()->role, ['admin', 'editor', 'manager']), 403);

        // TRAVA DE SEGURANÇA 3: Impede o manager de abrir edição de um Admin ou outro cargo
        if (Auth::user()->role === 'manager' && $user->role !== 'representative') {
            return redirect()->route('users.index')->with('error', 'Acesso negado: Você só pode editar contas de Representantes.');
        }

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        abort_if(!in_array(Auth::user()->role, ['admin', 'editor', 'manager']), 403);

        // TRAVA DE SEGURANÇA 4: Na hora de salvar a edição, mesma regra
        if (Auth::user()->role === 'manager') {
            if ($user->role !== 'representative') {
                return redirect()->route('users.index')->with('error', 'Acesso negado.');
            }
            $request->merge(['role' => 'representative']);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'string', 'in:admin,manager,editor,distributor,representative,salon,consumer'],
            'document' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        // Limpa o documento antes de salvar (remover pontos/traços)
        $documentoLimpo = $request->document ? preg_replace('/[^0-9]/', '', $request->document) : null;

        $user->fill([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'document' => $documentoLimpo,
        ]);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Tenta salvar e barra o erro de CPF/CNPJ duplicado
        try {
            $user->save();
        } catch (\Illuminate\Database\QueryException $e) {
            // Verifica se o erro é de entrada duplicada (1062)
            if ($e->errorInfo[1] == 1062) {
                // Retorna para a tela de edição do painel com uma mensagem de erro vermelha
                return back()->with('error', 'Atenção: Este CPF/CNPJ já está vinculado a outro usuário no sistema!')->withInput();
            }
            throw $e;
        }

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        // Apenas Admin (controle total) pode deletar
        if (Auth::user()->role !== 'admin') {
            return back()->with('error', 'Apenas administradores podem excluir usuários!');
        }

        if (Auth::id() === $user->id) {
            return back()->with('error', 'Você não pode excluir a sua própria conta!');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuário removido com sucesso!');
    }
}
