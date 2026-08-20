<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tighter text-center md:text-left">
                {{ $sheet->nome }}
            </h2>
            <a href="{{ route('sheets.index') }}" class="text-[10px] font-bold text-gray-400 hover:text-black uppercase tracking-[0.2em] transition">
                &larr; Voltar ao Acervo
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-start">

                {{-- COLUNA DA ESQUERDA: Imagem, Botão e QR Code --}}
                <div class="lg:sticky lg:top-8 flex flex-col gap-8">
                    <div class="bg-white rounded-[3rem] p-8 md:p-12 border border-gray-50 shadow-inner flex items-center justify-center min-h-[300px] md:min-h-[500px]">
                        @if($sheet->foto)
                            <img src="{{ asset('storage/' . $sheet->foto) }}" class="max-h-[400px] md:max-h-[600px] w-full object-contain" alt="{{ $sheet->nome }}">
                        @endif
                    </div>

                    @if($sheet->pdf)
                        <div>
                            <a href="{{ asset('storage/' . $sheet->pdf) }}" target="_blank" class="flex items-center justify-center gap-3 w-full bg-red-600 text-white py-5 rounded-2xl font-black uppercase text-xs tracking-[0.2em] hover:bg-black transition-all shadow-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                Baixar Dossier em PDF
                            </a>
                        </div>
                    @endif

                    @if(isset($qrCode))
                        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center w-full max-w-sm mx-auto">
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4">QR Code para Rótulo</h4>

                            <div class="mb-4 flex justify-center items-center w-full">
                                {!! $qrCode !!}
                            </div>

                            <p class="text-[9px] text-gray-500 font-bold uppercase tracking-tighter">
                                Este é o código que deve ser<br>impresso no frasco do produto.
                            </p>
                            <a href="data:image/svg+xml;base64,{{ base64_encode($qrCode) }}" download="QR_Kenzza_{{ $sheet->id }}.svg" class="text-xs font-bold text-gray-400 mt-3 hover:text-black transition-all underline">
                                Baixar em Vetor (Gráfica)
                            </a>
                        </div>
                    @endif
                </div>

                {{-- COLUNA DA DIREITA: Textos e Informações --}}
                <div class="space-y-12">
                    <section>
                        <span class="text-[#c5a059] text-[10px] font-black uppercase tracking-[0.3em] mb-2 block">Descrição do Produto</span>
                        <h1 class="text-3xl md:text-4xl font-black text-gray-900 mb-6 leading-tight uppercase tracking-tighter">{{ $sheet->nome }}</h1>
                        <div class="prose prose-sm text-gray-600 leading-relaxed italic break-words">
                            {!! nl2br(e($sheet->descricao)) !!}
                        </div>
                    </section>

                    <div class="grid grid-cols-1 gap-8">
                        <div class="border-l-4 border-[#c5a059] pl-6 py-2">
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Ativos & Tecnologia</h4>
                            <p class="text-gray-800 text-sm leading-relaxed break-words">{{ $sheet->ativos_tecnologia }}</p>
                        </div>

                        <div class="border-l-4 border-black pl-6 py-2">
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Funções e Performance</h4>
                            <p class="text-gray-800 text-sm leading-relaxed break-words">{{ $sheet->funcoes }}</p>
                        </div>

                        <div class="border-l-4 border-[#c5a059] pl-6 py-2">
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Diferenciais Competitivos</h4>
                            <p class="text-gray-800 text-sm leading-relaxed break-words">{{ $sheet->diferenciais }}</p>
                        </div>

                        <div class="bg-gray-50 p-6 md:p-8 rounded-[2rem] border border-gray-100">
                            <h4 class="text-[10px] font-black text-gray-900 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <span class="w-2 h-2 bg-[#c5a059] rounded-full"></span> Ritual de Aplicação
                            </h4>
                            <p class="text-gray-600 text-sm leading-relaxed italic break-words">{!! nl2br(e($sheet->modo_usar)) !!}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-8 border-t border-gray-100">
                        <div>
                            <h4 class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Dados Analíticos</h4>
                            <p class="text-[11px] text-gray-500 leading-tight uppercase break-words">{{ $sheet->dados_analiticos }}</p>
                        </div>
                        <div>
                            <h4 class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Segurança</h4>
                            <p class="text-[11px] text-gray-500 leading-tight uppercase break-words">{{ $sheet->seguranca }}</p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-24 pt-12 border-t border-gray-100 text-center">
                <p class="text-[10px] text-gray-300 font-bold uppercase tracking-[0.5em]">K'enzza Professional • Copyright {{ date('Y') }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
