<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Ticket #{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}
            </h2>
            <a href="{{ route('tickets.index') }}" class="text-sm font-bold text-gray-500 hover:text-black uppercase tracking-widest">
                &larr; Voltar para Lista
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-black rounded-3xl p-8 mb-6 shadow-2xl text-white">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <span class="text-[#c5a059] text-[10px] font-bold uppercase tracking-[0.2em]">Assunto</span>
                        <h1 class="text-2xl font-bold">{{ $ticket->subject }}</h1>
                    </div>
                    <span class="px-4 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest
    {{ $ticket->status == 'open' ? 'bg-green-500 text-white' : ($ticket->status == 'in_progress' ? 'bg-blue-500 text-white' : 'bg-gray-700 text-gray-300') }}">
    {{ $ticket->status_label }} </span>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 border-t border-white/10 pt-6">
                    <div>
                        <p class="text-gray-500 text-[9px] uppercase font-bold tracking-widest">Aberto por</p>
                        <p class="text-sm font-bold">{{ $ticket->user->name }}</p>
                    </div>
                  <div>
    <p class="text-gray-500 text-[9px] uppercase font-bold tracking-widest">Prioridade</p>
    <p class="text-sm font-bold uppercase {{ $ticket->priority == 'high' ? 'text-red-400' : 'text-white' }}">
        {{ $ticket->priority_label }} </p>
</div>
                    <div>
                        <p class="text-gray-500 text-[9px] uppercase font-bold tracking-widest">Data de Abertura</p>
                        <p class="text-sm font-bold">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-[9px] uppercase font-bold tracking-widest">Nível</p>
                        <p class="text-sm font-bold uppercase text-[#c5a059]">{{ $ticket->user->tier ?? 'Padrão' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 mb-6">
                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">Mensagem Original:</h4>
                <div class="prose max-w-none text-gray-700 leading-relaxed">
                    {!! nl2br(e($ticket->message)) !!}
                </div>
            </div>

            <div class="mt-10 space-y-6">
                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-4 italic text-center">Histórico da Conversa</h4>

                @forelse($ticket->replies as $reply)
                    <div class="flex flex-col {{ $reply->user_id === auth()->id() ? 'items-end' : 'items-start' }}">
                        <div class="max-w-[85%] md:max-w-[70%] rounded-3xl p-6 shadow-sm
                            {{ $reply->user_id === auth()->id() ? 'bg-black text-white rounded-tr-none' : 'bg-white border border-gray-100 rounded-tl-none' }}">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-[9px] font-black uppercase tracking-tighter {{ $reply->user_id === auth()->id() ? 'text-[#c5a059]' : 'text-gray-400' }}">
                                    {{ $reply->user->name }}
                                </span>
                                <span class="text-[9px] opacity-40 italic">{{ $reply->created_at->format('d/m H:i') }}</span>
                            </div>

                            <p class="text-sm leading-relaxed">{!! nl2br(e($reply->message)) !!}</p>

                            @if($reply->attachment)
                                <div class="mt-4 pt-4 border-t border-gray-100/10">
                                    <a href="{{ asset('storage/' . $reply->attachment) }}" target="_blank" class="block">
                                        <img src="{{ asset('storage/' . $reply->attachment) }}" class="rounded-xl max-w-full h-auto shadow-lg hover:scale-[1.02] transition-transform">
                                        <p class="text-[9px] mt-2 opacity-50 uppercase tracking-widest text-center">Clique para ampliar</p>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-300 text-xs uppercase tracking-widest">Nenhuma resposta ainda.</p>
                @endforelse
            </div>

            @if($ticket->status !== 'closed')
                <div class="mt-10 bg-white rounded-3xl p-8 shadow-2xl border border-gray-100">
                    <form action="{{ route('tickets.replies.store', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-6">
                            <label for="message" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 block">Escrever Mensagem</label>
                            <textarea id="message" name="message" rows="4" class="w-full border-gray-200 rounded-2xl focus:ring-[#c5a059] focus:border-[#c5a059] shadow-sm" placeholder="Digite sua resposta aqui..." required></textarea>
                        </div>

                        <div class="mb-6 bg-gray-50 p-4 rounded-2xl border border-dashed border-gray-200">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 block">Anexar Imagem (Opcional)</label>
                            <input type="file" name="attachment" accept="image/*" class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-[#c5a059]/10 file:text-[#c5a059] hover:file:bg-[#c5a059]/20">
                            <p class="text-[9px] text-gray-400 mt-2 italic">Formatos aceitos: JPG, PNG, GIF. Máximo: 2MB.</p>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-[#c5a059] text-black px-10 py-3 rounded-xl font-bold uppercase text-xs hover:bg-black hover:text-white transition shadow-lg">
                                Enviar Resposta
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="mt-10 bg-gray-50 rounded-3xl p-8 text-center border border-dashed border-gray-200">
                    <p class="text-gray-400 font-bold uppercase text-[10px] tracking-widest">Este ticket foi encerrado e não aceita novas mensagens.</p>
                </div>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'editor')
                <div class="mt-12 bg-gray-100 rounded-3xl p-8 border border-gray-200 shadow-inner">
                    <h4 class="font-bold text-gray-900 mb-4 text-sm uppercase tracking-widest">Painel de Gestão</h4>
                    <form action="{{ route('tickets.update', $ticket->id) }}" method="POST" class="flex flex-wrap items-center gap-4">
                        @csrf
                        @method('PATCH')

                        <select name="status" class="rounded-xl border-gray-300 text-sm focus:ring-[#c5a059] focus:border-[#c5a059] min-w-[200px]">
                           <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Reabrir Chamado</option>
    <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>Marcar como: Em Atendimento</option>
    <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Encerrar e Finalizar</option>    </select>

                        <button type="submit" class="bg-black text-white px-8 py-2 rounded-xl font-bold uppercase text-[10px] tracking-widest hover:bg-gray-800 transition">
                            Atualizar Status
                        </button>
                    </form>
                </div>
                <div class="h-40"></div>
            @endif

        </div>
    </div>
</x-app-layout>
