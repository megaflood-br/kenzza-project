<x-app-layout>
    @php
        $roleLabel = match($user->role) {
            'admin' => 'Administrador',
            'manager' => 'Gerente Comercial',
            'editor' => 'Editor',
            'distributor' => 'Distribuidor',
            'representative' => 'Representante',
            'salon' => 'Salão / Profissional',
            default => 'Consumidor',
        };
        $roleClass = match($user->role) {
            'admin' => 'bg-black text-[#c5a059]',
            'manager' => 'bg-purple-100 text-purple-700',
            'editor' => 'bg-blue-100 text-blue-700',
            'distributor' => 'bg-gray-200 text-gray-800',
            'representative' => 'bg-amber-100 text-amber-800',
            'salon' => 'bg-pink-100 text-pink-700',
            default => 'bg-[#c5a059]/20 text-[#c5a059]',
        };
        $phone = $user->telefone ?? $user->phone;
        $phoneDigits = preg_replace('/\D/', '', (string) $phone);
        $enderecoLinhas = array_filter([
            trim(implode(', ', array_filter([$user->street ?? $user->logradouro, $user->number ?? $user->numero]))),
            $user->complement,
            $user->neighborhood ?? $user->bairro,
            trim(implode(' - ', array_filter([$user->city ?? $user->cidade, $user->state ?? $user->estado]))),
            $user->zip_code ?? $user->cep,
        ]);
    @endphp

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <a href="{{ route('users.index') }}" class="text-[10px] font-black text-[#c5a059] uppercase tracking-widest hover:underline flex items-center gap-2 mb-2">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Voltar à lista
                </a>
                <h2 class="font-black text-2xl text-gray-900 leading-tight uppercase tracking-tighter">
                    Perfil do <span class="text-[#c5a059]">Usuário</span>
                </h2>
            </div>
            <a href="{{ route('users.edit', $user) }}" class="bg-black text-[#c5a059] px-6 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-[#c5a059] hover:text-black transition-all shadow-md border border-[#c5a059]">
                Editar cadastro
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 md:p-8">
                <div class="flex flex-col lg:flex-row gap-8">
                    <div class="flex items-start gap-5 flex-1">
                        <div class="w-16 h-16 rounded-full bg-black text-[#c5a059] flex items-center justify-center font-black text-2xl uppercase shadow-inner flex-shrink-0">
                            {{ mb_substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-3 mb-2">
                                <h3 class="font-black text-xl text-gray-900 tracking-tight">{{ $user->name }}</h3>
                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $roleClass }}">{{ $roleLabel }}</span>
                                @if($user->tier)
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-black text-[#c5a059]">{{ $user->tier }}</span>
                                @endif
                            </div>
                            <p class="text-sm font-bold text-gray-700">{{ $user->email }}</p>
                            <p class="text-[10px] uppercase tracking-widest font-bold text-gray-400 mt-2">
                                Cadastrado em {{ $user->created_at->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:w-[28rem]">
                        <div class="bg-gray-50 rounded-2xl p-4">
                            <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Pedidos</p>
                            <p class="mt-1 font-black text-2xl text-gray-900">{{ $stats['orders_count'] }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-4">
                            <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Pagos</p>
                            <p class="mt-1 font-black text-2xl text-gray-900">{{ $stats['paid_count'] }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-4 col-span-2">
                            <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Total em pedidos pagos</p>
                            <p class="mt-1 font-black text-2xl text-[#c5a059]">R$ {{ number_format($stats['ltv'], 2, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 md:p-8 space-y-6">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-400">Informações</h4>

                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Telefone</p>
                        @if($phone)
                            <p class="mt-1 font-bold text-gray-900">{{ $phone }}</p>
                            @if($phoneDigits)
                                <a href="https://wa.me/55{{ $phoneDigits }}" target="_blank" class="inline-flex mt-2 text-[10px] font-black uppercase tracking-widest text-green-600 hover:underline">WhatsApp</a>
                            @endif
                        @else
                            <p class="mt-1 text-sm text-gray-400">Não informado</p>
                        @endif
                    </div>

                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">CPF / CNPJ</p>
                        <p class="mt-1 font-bold text-gray-900">{{ $user->document ?: 'Não informado' }}</p>
                    </div>

                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Endereço</p>
                        @if(count($enderecoLinhas))
                            <p class="mt-1 text-sm font-bold text-gray-800 leading-relaxed">{{ implode(' · ', $enderecoLinhas) }}</p>
                        @else
                            <p class="mt-1 text-sm text-gray-400">Não informado</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-2">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">E-mail verificado</p>
                            <p class="mt-1 text-sm font-black uppercase {{ $user->email_verified_at ? 'text-green-600' : 'text-amber-600' }}">
                                {{ $user->email_verified_at ? 'Sim' : 'Não' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Último pedido</p>
                            <p class="mt-1 text-sm font-bold text-gray-800">
                                {{ $stats['last_order_at'] ? $stats['last_order_at']->timezone('America/Sao_Paulo')->format('d/m/Y') : '—' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 md:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-400">Pedidos feitos</h4>
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-300">{{ $stats['orders_count'] }} registro(s)</span>
                    </div>

                    <div class="space-y-4">
                        @forelse($user->orders as $order)
                            @php
                                $statusClass = match($order->status) {
                                    'pago', 'aprovado', 'entregue' => 'bg-green-100 text-green-700',
                                    'enviado', 'em_separacao' => 'bg-blue-100 text-blue-700',
                                    'cancelado' => 'bg-red-100 text-red-700',
                                    default => 'bg-amber-100 text-amber-700',
                                };
                            @endphp
                            <div class="border border-gray-100 rounded-3xl p-5 hover:border-[#c5a059]/40 transition-colors">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div>
                                        <p class="font-black text-gray-900">Pedido #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</p>
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mt-1">
                                            {{ $order->created_at->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }}
                                            @if($order->metodo_pagamento)
                                                · {{ str_replace('_', ' ', $order->metodo_pagamento) }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $statusClass }}">{{ $order->status }}</span>
                                        <span class="font-black text-[#c5a059]">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                                    </div>
                                </div>

                                @if($order->codigo_rastreio)
                                    <p class="mt-3 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                        Rastreio: <span class="text-gray-800">{{ $order->codigo_rastreio }}</span>
                                    </p>
                                @endif

                                @if($order->items->isNotEmpty())
                                    <div class="mt-4 divide-y divide-gray-50">
                                        @foreach($order->items as $item)
                                            <div class="py-2 flex justify-between gap-4 text-sm">
                                                <span class="font-bold text-gray-700">
                                                    {{ $item->product->nome ?? 'Produto removido' }}
                                                    <span class="text-gray-400 font-black text-[10px] uppercase ml-1">x{{ $item->quantidade }}</span>
                                                </span>
                                                <span class="font-black text-gray-900">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="border-2 border-dashed border-gray-200 rounded-3xl p-10 text-center">
                                <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Nenhum pedido encontrado para este usuário.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            @if($user->tickets->isNotEmpty())
                <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 md:p-8">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4">Tickets recentes</h4>
                    <div class="space-y-3">
                        @foreach($user->tickets as $ticket)
                            <div class="flex items-center justify-between gap-4 py-2">
                                <p class="font-bold text-gray-800 text-sm">{{ $ticket->subject }}</p>
                                <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">{{ $ticket->status_label }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="h-10"></div>
        </div>
    </div>
</x-app-layout>
