<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;

class TicketReplyController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        // 1. Validação
        $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        $attachmentPath = null;

        // 2. Upload da imagem
        if ($request->hasFile('attachment')) {
            // Salva em storage/app/public/tickets
            $attachmentPath = $request->file('attachment')->store('tickets', 'public');
        }

        // 3. Criação do registro no banco
        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
            'attachment' => $attachmentPath,
        ]);

        // 4. Atualização do status do ticket
        $newStatus = auth()->user()->role === 'distributor' ? 'open' : 'in_progress';
        $ticket->update(['status' => $newStatus]);

        return back()->with('success', 'Resposta enviada!');
    }
}
