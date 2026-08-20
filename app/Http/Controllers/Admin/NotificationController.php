<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Berkayk\OneSignal\OneSignalClient;

class NotificationController extends Controller
{
    protected $oneSignal;

    public function __construct(OneSignalClient $oneSignal)
    {
        $this->oneSignal = $oneSignal;
    }

    public function index()
    {
        $userCounts = [
            'all' => User::count(),
            'consumer' => User::whereIn('role', ['consumer', 'salon'])->count(),
            'distributor' => User::where('role', 'distributor')->count(),
        ];

        return view('admin.notifications.create', compact('userCounts'));
    }

    /**
     * Salva a preferência de Push do usuário no banco de dados.
     */
    public function togglePush(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'status' => 'required|boolean'
        ]);

        // Atualiza a coluna no banco de dados
        $user->update([
            'push_enabled' => $request->status
        ]);

        return response()->json([
            'sucesso' => true,
            'mensagem' => $request->status ? 'Notificações ativadas no perfil!' : 'Notificações desativadas no perfil!'
        ]);
    }

    /**
     * Envia a notificação com suporte a segmentação por público-alvo.
     */
    public function send(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:100',
            'message'     => 'required|string',
            'url'         => 'nullable|url',
            'target_role' => 'required|string|in:all,consumer,distributor'
        ]);

        $message = $request->message;
        $title   = $request->title;
        $url     = $request->url;
        $role    = $request->target_role;

        // Se o alvo for "todos", usamos o método simplificado
        if ($role === 'all') {
            $this->oneSignal->sendNotificationToAll(
                $message,
                $url,
                null,
                null,
                null,
                $title
            );
        } else {
            // Se houver um alvo específico, usamos filtros por tags do OneSignal
            $this->oneSignal->sendNotificationCustom([
                'contents' => [
                    'en' => $message,
                    'pt' => $message
                ],
                'headings' => [
                    'en' => $title,
                    'pt' => $title
                ],
                'url' => $url,
                'filters' => [
                    [
                        'field'    => 'tag',
                        'key'      => 'user_role',
                        'relation' => '=',
                        'value'    => $role
                    ]
                ],
            ]);
        }

        return back()->with('success', 'Notificação enviada com sucesso para o público selecionado!');
    }
}
