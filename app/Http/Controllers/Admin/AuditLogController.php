<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Exibe a listagem dos logs de auditoria
     */
    public function index(Request $request)
    {
        // Cria uma query base trazendo o relacionamento do usuário que fez a ação
        $query = AuditLog::with('user')->latest();

        // Filtro por Usuário (opcional, caso queira buscar logs de um usuário específico)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtro por Ação (create, update, delete)
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Pagina de 50 em 50 para não pesar o banco de dados
        $logs = $query->paginate(50)->withQueryString();

        return view('admin.logs.index', compact('logs'));
    }

    /**
     * Exibe os detalhes de um log específico (útil para ver o antes/depois do JSON)
     */
    public function show(string $id)
    {
        $log = AuditLog::with('user')->findOrFail($id);

        return view('admin.logs.show', compact('log'));
    }
}
