<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sheet->nome }} - K'enzza Professional</title>
    {{-- Carrega o Tailwind do seu projeto --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 antialiased font-sans">

    {{-- Header Minimalista --}}
    <header class="bg-white py-6 border-b border-gray-100 shadow-sm flex justify-center">
        <img src="{{ asset('logo-k-preto.png') }}" class="h-10 w-auto" alt="K'enzza Professional">
    </header>

    <main class="max-w-xl mx-auto px-6 py-10">
        {{-- Foto do Produto --}}
        <div class="mb-10 flex justify-center bg-white rounded-[3rem] p-8 shadow-sm border border-gray-100">
            <img src="{{ asset('storage/' . $sheet->foto) }}" class="max-h-72 object-contain" alt="{{ $sheet->nome }}">
        </div>

        {{-- Identificação --}}
        <div class="text-center mb-10">
            <span class="text-[9px] font-black uppercase tracking-[0.3em] text-[#B8860B]">Ficha Técnica Oficial</span>
            <h1 class="text-3xl font-black uppercase tracking-tighter mt-2">{{ $sheet->nome }}</h1>
            <div class="w-12 h-1 bg-[#B8860B] mx-auto mt-4 rounded-full"></div>
        </div>

        {{-- Conteúdo Técnico --}}
        <div class="space-y-10">
            <section>
                <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-3">Sobre o Produto</h2>
                <p class="text-gray-600 leading-relaxed italic">{{ $sheet->descricao }}</p>
            </section>

            <section class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-[#B8860B] mb-4">Ativos & Tecnologia</h2>
                <p class="text-sm text-gray-700 leading-relaxed">{{ $sheet->ativos_tecnologia }}</p>
            </section>

            <section>
                <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-[#B8860B] mb-4">Como Utilizar</h2>
                <div class="border-l-4 border-[#B8860B] pl-6 text-sm text-gray-700 leading-relaxed">
                    {{ $sheet->modo_usar }}
                </div>
            </section>
        </div>

        {{-- QR Code de Compartilhamento --}}
        <div class="mt-16 p-8 bg-white rounded-[3rem] border border-gray-100 flex flex-col items-center text-center shadow-sm">
            <div class="p-2 bg-gray-50 rounded-2xl border border-gray-100 mb-4">
                {!! $qrCode !!}
            </div>
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Link Permanente para Rótulo</p>
            <p class="text-[8px] text-gray-400 mt-1">ID: {{ $sheet->id }} | K'enzza Professional</p>
        </div>

        {{-- Botão de Download PDF --}}
        @if($sheet->pdf)
            <div class="mt-8">
                <a href="{{ asset('storage/' . $sheet->pdf) }}" target="_blank" class="flex items-center justify-center gap-3 w-full bg-black text-white py-5 rounded-full text-[10px] font-black uppercase tracking-widest hover:bg-[#B8860B] transition-all shadow-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                    Acessar PDF Completo
                </a>
            </div>
        @endif

        {{-- SEÇÃO DE COMPARTILHAMENTO --}}
        <div class="mt-6 border-t border-gray-200 pt-6">
            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 text-center mb-4">Compartilhar Ficha</h3>
            <div class="flex flex-col sm:flex-row gap-3">
                {{-- WhatsApp --}}
                <a href="https://api.whatsapp.com/send?text={{ urlencode('Confira a ficha técnica: ' . $sheet->nome . ' - ' . request()->url()) }}"
                   target="_blank"
                   class="flex-1 flex items-center justify-center gap-2 bg-[#25D366] text-white py-4 rounded-full text-[10px] font-black uppercase tracking-widest hover:bg-[#128C7E] transition-all shadow-md">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.031 0C5.385 0 0 5.385 0 12.032c0 2.128.553 4.195 1.605 6.02L.152 23.46l5.568-1.458a12.037 12.037 0 006.311 1.761h.005c6.645 0 12.03-5.385 12.03-12.031C24.066 5.385 18.677 0 12.031 0zm0 21.758a10.021 10.021 0 01-5.115-1.396l-.367-.218-3.805.997 1.015-3.71-.24-.38a10.008 10.008 0 01-1.524-5.297c0-5.534 4.502-10.035 10.036-10.035 5.533 0 10.033 4.501 10.033 10.035 0 5.535-4.5 10.004-10.033 10.004zm5.508-7.533c-.302-.15-1.787-.88-2.064-.98-.277-.1-.478-.15-.68.15s-.78 1.01-.956 1.22c-.176.21-.353.24-.655.09a8.212 8.212 0 01-2.42-1.493 9.07 9.07 0 01-1.68-2.09c-.176-.3-.018-.465.132-.615.136-.135.302-.345.453-.525.15-.18.2-.3.301-.5.101-.2.051-.375-.025-.525-.075-.15-.68-1.64-.931-2.245-.245-.59-.495-.51-.68-.52h-.58c-.2 0-.527.075-.803.425-.277.35-1.055 1.025-1.055 2.5 0 1.475 1.08 2.9 1.23 3.1.15.2 2.115 3.225 5.126 4.525.716.31 1.275.495 1.71.635.72.23 1.37.195 1.865.12.556-.085 1.787-.73 2.039-1.435.251-.705.251-1.31.176-1.435-.076-.125-.277-.2-.58-.35z"/>
                    </svg>
                    WhatsApp
                </a>

                {{-- Copiar Link --}}
                <button onclick="copiarLink()" class="flex-1 flex items-center justify-center gap-2 bg-gray-200 text-gray-800 py-4 rounded-full text-[10px] font-black uppercase tracking-widest hover:bg-gray-300 transition-all shadow-md" id="btn-copiar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                    <span>Copiar Link</span>
                </button>
            </div>
        </div>
    </main>

    <footer class="py-12 text-center">
        <p class="text-[9px] font-bold text-gray-300 uppercase tracking-widest">Desenvolvido por K'enzza Digital</p>
    </footer>

    {{-- Script para copiar o link --}}
    <script>
        function copiarLink() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                const btn = document.getElementById('btn-copiar');
                const span = btn.querySelector('span');

                // Feedback visual de sucesso
                const textoOriginal = span.innerText;
                span.innerText = 'Copiado!';
                btn.classList.add('bg-green-100', 'text-green-700');

                // Retorna ao normal após 2 segundos
                setTimeout(() => {
                    span.innerText = textoOriginal;
                    btn.classList.remove('bg-green-100', 'text-green-700');
                }, 2000);
            }).catch(err => {
                alert('Erro ao copiar o link. Tente copiar a URL diretamente do navegador.');
            });
        }
    </script>
</body>
</html>
