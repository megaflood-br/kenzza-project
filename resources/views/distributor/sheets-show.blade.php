<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tighter">
                {{ $sheet->nome }}
            </h2>
            <a href="{{ route('sheets.index') }}" class="text-[10px] font-bold text-gray-400 hover:text-black uppercase tracking-[0.2em] transition">
                &larr; Voltar ao Acervo
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">

                <div class="sticky top-8">
                    <div class="bg-[#fcfcfc] rounded-[3rem] p-12 border border-gray-50 shadow-inner flex items-center justify-center min-h-[500px]">
                        @if($sheet->foto)
                            <img src="{{ asset('storage/' . $sheet->foto) }}" class="max-h-[600px] object-contain drop-shadow-2xl" alt="{{ $sheet->nome }}">
                        @endif
                    </div>

                    @if($sheet->pdf)
                        <div class="mt-8">
                            <a href="{{ asset('storage/' . $sheet->pdf) }}" target="_blank" class="flex items-center justify-center gap-3 w-full bg-red-600 text-white py-5 rounded-2xl font-black uppercase text-xs tracking-[0.2em] hover:bg-black transition-all shadow-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                Baixar Dossier em PDF
                            </a>
                        </div>
                    @endif
                </div>

                <div class="space-y-12">
                    <section>
                        <span class="text-[#c5a059] text-[10px] font-black uppercase tracking-[0.3em] mb-2 block">Descrição do Produto</span>
                        <h1 class="text-4xl font-black text-gray-900 mb-6 leading-tight uppercase tracking-tighter">{{ $sheet->nome }}</h1>
                        <div class="prose prose-sm text-gray-600 leading-relaxed italic">
                            {!! nl2br(e($sheet->descricao)) !!}
                        </div>
                    </section>

                    <div class="grid grid-cols-1 gap-8">
                        <div class="border-l-4 border-[#c5a059] pl-6 py-2">
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Ativos & Tecnologia</h4>
                            <p class="text-gray-800 text-sm leading-relaxed">{{ $sheet->ativos_tecnologia }}</p>
                        </div>

                        <div class="border-l-4 border-black pl-6 py-2">
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Funções e Performance</h4>
                            <p class="text-gray-800 text-sm leading-relaxed">{{ $sheet->funcoes }}</p>
                        </div>

                        <div class="border-l-4 border-[#c5a059] pl-6 py-2">
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Diferenciais Competitivos</h4>
                            <p class="text-gray-800 text-sm leading-relaxed">{{ $sheet->diferenciais }}</p>
                        </div>

                        <div class="bg-gray-50 p-8 rounded-[2rem] border border-gray-100">
                            <h4 class="text-[10px] font-black text-gray-900 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <span class="w-2 h-2 bg-[#c5a059] rounded-full"></span> Ritual de Aplicação
                            </h4>
                            <p class="text-gray-600 text-sm leading-relaxed italic">{!! nl2br(e($sheet->modo_usar)) !!}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6 pt-8 border-t border-gray-100">
                        <div>
                            <h4 class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Dados Analíticos</h4>
                            <p class="text-[11px] text-gray-500 leading-tight uppercase">{{ $sheet->dados_analiticos }}</p>
                        </div>
                        <div>
                            <h4 class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Segurança</h4>
                            <p class="text-[11px] text-gray-500 leading-tight uppercase">{{ $sheet->seguranca }}</p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-24 pt-12 border-t border-gray-100 text-center">
                <p class="text-[10px] text-gray-300 font-bold uppercase tracking-[0.5em]">K'enzza Professional • Copyright 2026</p>
            </div>
        </div>
    </div>
</x-app-layout>
