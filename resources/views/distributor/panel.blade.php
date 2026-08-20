@php
    $frases = [
        "O sucesso é a soma de pequenos esforços repetidos dia após dia.",
        "A melhor maneira de prever o futuro é criá-lo.",
        "Grandes coisas nunca vêm de zonas de conforto.",
        "O único lugar onde o sucesso vem antes do trabalho é no dicionário.",
        "Não conte os dias, faça os dias contarem.",
        "Seu único limite é você mesmo.",
        "O sucesso não é o final, o fracasso não é fatal: é a coragem de continuar que conta.",
        "A persistência é o caminho do êxito.",
        "Trabalhe em silêncio, deixe seu sucesso ser seu barulho.",
        "Venda valor, não preço. O valor permanece, o preço é esquecido.",
        "Grandes coisas em negócios nunca são feitas por uma única pessoa; elas são feitas por uma equipe de pessoas.",
        "Não foque na meta, foque no sistema que leva você até ela. O resultado é apenas a consequência do processo.",
        "Oportunidades de negócios são como ônibus: sempre há outro vindo, mas você precisa estar na parada certa para embarcar.",
        "Seus clientes mais insatisfeitos são sua maior fonte de aprendizado.",
        "Inovação é o que distingue um líder de um seguidor.",
        "O risco mais perigoso de todos é o risco de passar a vida sem fazer o que você quer, apostando que poderá comprar a liberdade de fazê-lo mais tarde.",
        "Não tente ser o melhor do mercado; tente ser único. Quando você é único, a competição torna-se irrelevante.",
        "Escalabilidade começa com a coragem de fazer coisas que não escalam no início, para entender profundamente o que seu cliente precisa."
    ];
    $fraseDoDia = $frases[array_rand($frases)];
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Painel do Distribuidor - K\'enzza') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-black rounded-3xl p-8 mb-10 shadow-2xl relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <h3 class="text-white text-3xl font-bold mb-2">Olá, {{ Auth::user()->name }}!</h3>

                        <p class="text-[#c5a059] italic text-sm font-medium mb-4 max-w-lg">
                            "{{ $fraseDoDia }}"
                        </p>

                        <div class="flex items-center gap-2">
                            <span class="bg-[#c5a059] text-black px-4 py-1.5 rounded-full font-bold text-[10px] uppercase tracking-widest shadow-lg">
                                Distribuidor Ativo
                            </span>

                            @if(Auth::user()->tier)
                                <span class="px-4 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest shadow-lg
                                    {{ Auth::user()->tier == 'diamante' ? 'bg-blue-500 text-white' : '' }}
                                    {{ Auth::user()->tier == 'black' ? 'bg-gray-700 text-white border border-gray-600' : '' }}
                                    {{ Auth::user()->tier == 'gold' ? 'bg-yellow-500 text-black' : '' }}">
                                    Nível {{ Auth::user()->tier }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="hidden md:block text-right">
                        <p class="text-gray-500 text-[10px] uppercase font-bold tracking-widest mb-1">Status da Conta</p>
                        <p class="text-green-400 text-xs font-bold flex items-center gap-2 justify-end">
                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span> Verificado
                        </p>
                    </div>
                </div>

                <div class="absolute right-[-20px] top-[-20px] opacity-10">
                    <svg class="w-64 h-64 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl transition-all group border-b-4 border-b-[#c5a059]">
                    <div class="bg-[#c5a059]/10 w-16 h-16 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-[#c5a059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                    </div>
                    <h4 class="font-bold text-gray-900 uppercase text-sm tracking-widest mb-2">Pedidos</h4>
                    <p class="text-xs text-gray-500 mb-6">Consulte seu histórico e realize novas compras.</p>
                    <a href="{{ route('orders.index') }}" class="block w-full bg-black text-white text-center py-3 rounded-xl font-bold text-[10px] uppercase tracking-widest hover:bg-[#c5a059] transition shadow-lg">
                        Acessar Área
                    </a>
                </div>

                <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl transition-all group">
                    <div class="bg-gray-50 w-16 h-16 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform text-[#c5a059]">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                    <h4 class="font-bold text-gray-900 uppercase text-sm tracking-widest mb-2">Fichas Técnicas</h4>
                    <p class="text-xs text-gray-500 mb-6">Composição e modo de uso de cada produto.</p>
                    <a href="{{ route('sheets.index') }}" class="block w-full bg-gray-100 text-gray-600 text-center py-3 rounded-xl font-bold text-[10px] uppercase tracking-widest hover:bg-[#c5a059] hover:text-black transition">
                        Ver Detalhes
                    </a>
                </div>

                <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm hover:shadow-xl transition-all group">
                    <div class="bg-gray-50 w-16 h-16 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform text-[#c5a059]">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <h4 class="font-bold text-gray-900 uppercase text-sm tracking-widest mb-2">Kits Divulgação</h4>
                    <p class="text-xs text-gray-500 mb-6">Fotos de alta qualidade e artes para redes sociais.</p>
                    <a href="{{ route('media.index') }}" class="block w-full bg-gray-50 text-gray-600 text-center py-3.5 rounded-xl font-bold text-[10px] uppercase tracking-[0.2em] hover:bg-black hover:text-white transition-all duration-300">
                        Acessar Acervo
                    </a>
                </div>

                <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl transition-all group border-b-4 border-b-red-400">
                    <div class="bg-red-50 w-16 h-16 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                    <h4 class="font-bold text-gray-900 uppercase text-sm tracking-widest mb-2">Suporte</h4>
                    <p class="text-xs text-gray-500 mb-6">Abra um chamado para resolver problemas técnicos.</p>
                    <a href="{{ route('tickets.index') }}" class="block w-full bg-red-500 text-white text-center py-3 rounded-xl font-bold text-[10px] uppercase tracking-widest hover:bg-black transition">Meus Tickets</a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
