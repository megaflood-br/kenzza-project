<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
class TicketController extends Controller
{
    public function index() {
    $user = auth()->user();
    // Se for admin/editor vê tudo, se for distribuidor vê só os dele
    $tickets = ($user->role === 'admin' || $user->role === 'editor')
        ? Ticket::with('user')->latest()->get()
        : Ticket::where('user_id', $user->id)->latest()->get();

    return view('tickets.index', compact('tickets'));
}

public function store(Request $request) {
    $request->validate([
        'subject' => 'required|string|max:255',
        'message' => 'required|string',
        'priority' => 'required|in:low,medium,high',
    ]);

    Ticket::create([
        'user_id' => auth()->id(),
        'subject' => $request->subject,
        'message' => $request->message,
        'priority' => $request->priority,
    ]);

    return redirect()->route('tickets.index')->with('success', 'Ticket aberto com sucesso!');
}

public function create() {
    return view('tickets.create');
}

public function show(Ticket $ticket)
{
    if (auth()->user()->role === 'distributor' && $ticket->user_id !== auth()->id()) {
        abort(403);
    }

    // Carrega o usuário do ticket E as respostas com os respectivos usuários
    $ticket->load(['user', 'replies.user']);

    return view('tickets.show', compact('ticket'));
}

public function update(Request $request, Ticket $ticket)
{
    // Apenas admin/editor podem mudar o status
    if (auth()->user()->role === 'distributor') {
        abort(403);
    }

    $request->validate(['status' => 'required|in:open,in_progress,closed']);

    $ticket->update(['status' => $request->status]);

    return back()->with('success', 'Status do ticket atualizado!');
}
}
