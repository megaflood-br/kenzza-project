<?php

namespace App\Http\Controllers;

use App\Models\LeadDistribuidor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use App\Notifications\WelcomeDistributor;

class LeadController extends Controller
{
    /**
     * Listagem de Leads com Filtro de Busca
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $query = LeadDistribuidor::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nome', 'LIKE', "%{$search}%")
                  ->orWhere('cidade', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $leads = $query->latest()->get();
        return view('leads.index', compact('leads', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
            'whatsapp' => 'required|string',
            'email' => 'nullable|email|max:255',
        ]);

        try {
            $whatsapp = preg_replace('/\D/', '', $request->whatsapp);

            LeadDistribuidor::create([
                'nome' => $request->nome,
                'cidade' => $request->cidade,
                'estado' => $request->estado,
                'whatsapp' => $whatsapp,
                'email' => $request->email ? strtolower($request->email) : null,
            ]);

            return back()->with('success', 'Sua solicitação foi enviada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro Crítico ao salvar Lead: ' . $e->getMessage());
            return back()->with('error', 'Erro interno ao processar cadastro. Nossa equipe foi notificada.');
        }
    }

    public function promote(LeadDistribuidor $lead)
    {
        // 1. Verifica se o usuário tem e-mail (necessário para virar distribuidor e receber link)
        if (!$lead->email) {
            return back()->with('error', 'Não é possível promover: Este lead não possui um e-mail cadastrado.');
        }

        // 2. Verifica se o e-mail já existe no banco
        if (User::where('email', $lead->email)->exists()) {
            return back()->with('error', 'Este e-mail já está cadastrado como usuário!');
        }

        // 3. Cria o usuário com uma senha aleatória super segura e inacessível
        $user = User::create([
            'name'     => $lead->nome,
            'email'    => $lead->email,
            'password' => Hash::make(Str::random(32)),
            'role'     => 'distributor',
        ]);

        // 4. Gera o Token de Redefinição de Senha e Dispara a Notificação
        $token = Password::broker()->createToken($user);
        $user->notify(new WelcomeDistributor($token));

        // 5. Remove o Lead da fila
        $lead->delete();

        return redirect()->route('leads.index')->with('success', "{$lead->nome} promovido a Distribuidor! Um e-mail com o link de criação de senha foi enviado.");
    }

    public function destroy(LeadDistribuidor $lead)
    {
        $lead->delete();
        return back()->with('success', 'Lead removido com sucesso.');
    }
}
