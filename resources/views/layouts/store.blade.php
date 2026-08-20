<!DOCTYPE html>
<html lang="pt-br">
<head>
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KKTV253H');</script>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('title')@yield('title') - @endif K'enzza Hair - Loja Oficial</title>

    {{-- PWA Manifest e Ícones --}}
    <link rel="manifest" href="{{ asset('site.webmanifest') }}?v=2">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="K'enzza">
    <link class="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=2">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- OneSignal SDK --}}
    <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
    <script>
        window.OneSignalDeferred = window.OneSignalDeferred || [];
        OneSignalDeferred.push(async function(OneSignal) {
            try {
                await OneSignal.init({
                    appId: "2eaed96d-5ff2-4460-97db-e3c33fcf91f2",
                    safari_web_id: "web.onesignal.auto.17387431-a83d-4c8e-8a03-7f2878a87383",
                    serviceWorkerParam: { scope: '/' },
                    serviceWorkerPath: 'OneSignalSDKWorker.js',
                });

                @auth
                    await OneSignal.login("{{ Auth::user()->email }}");
                    await OneSignal.User.addTag("user_role", "{{ Auth::user()->role }}");
                @endauth
            } catch (e) {
                console.warn("OneSignal ignorado no ambiente local.");
            }
        });
    </script>

    <style>
        .font-kenzza { font-family: "Times New Roman", Times, serif; }
        .dropdown-shadow { box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }
    </style>

    {{-- Meta Descrição Dinâmica --}}
    @yield('seo')

</head>
<body class="font-sans antialiased bg-gray-50" x-data="{ mobileMenu: false }">
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KKTV253H"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
{{-- MENU MOBILE --}}
    <div x-show="mobileMenu" class="fixed inset-0 z-[9999] lg:hidden" style="display: none;">
        <div x-show="mobileMenu"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileMenu = false"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[9999]"></div>

        <nav x-show="mobileMenu"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="fixed inset-y-0 left-0 w-full max-w-xs bg-white shadow-2xl flex flex-col justify-between py-8 px-6 overflow-hidden z-[10000]">

            <div class="flex flex-col">
                <div class="flex items-center justify-between mb-8">
                    <span class="font-kenzza text-2xl font-bold uppercase tracking-tighter">Kenzza</span>
                    <button @click="mobileMenu = false" class="p-2 text-gray-400 hover:text-black">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- CAMPO DE BUSCA MOBILE --}}
                <div class="mb-8">
                    <form action="{{ route('shop.index') }}" method="GET" class="relative w-full">
                        <input type="text" name="search" placeholder="O QUE VOCÊ PROCURA?"
                               class="w-full bg-gray-50 border border-gray-100 rounded-full py-3 px-5 text-[10px] font-black uppercase tracking-widest focus:ring-2 focus:ring-[#B8860B] focus:bg-white transition-all outline-none">
                        <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#B8860B]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </form>
                </div>

                <div class="flex flex-col space-y-6">
                    <a href="{{ route('shop.home') }}" class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-900 border-b border-gray-50 pb-4">Início</a>
                    <a href="{{ route('shop.index') }}" class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-900 border-b border-gray-50 pb-4">Todos os Produtos</a>

                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex justify-between items-center text-[11px] font-black uppercase tracking-[0.2em] text-gray-900 border-b border-gray-50 pb-4">
                            Categorias
                            <svg class="w-4 h-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" class="mt-4 ml-4 space-y-4" style="display: none;">
                            @foreach($categories as $category)
                                <a href="{{ route('shop.index', $category->slug) }}" class="block text-[11px] font-bold text-gray-900 hover:text-[#B8860B] uppercase tracking-widest">
                                    {{ $category->nome }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    @auth
                        <a href="{{ route('painel.redirect') }}" class="text-[11px] font-black text-[#B8860B] uppercase tracking-[0.2em]">Meu Painel</a>
                    @else
                        <a href="{{ route('login') }}" class="text-[11px] font-black text-[#B8860B] uppercase tracking-[0.2em]">Entrar / Cadastrar</a>
                    @endauth
                </div>
            </div>

            <div id="pwa-install-container" class="hidden border-t border-gray-100 pt-6 mt-auto">
                <button id="pwa-install-btn" class="flex items-center gap-3 w-full text-left group">
                    <div class="w-10 h-10 bg-black rounded-xl flex items-center justify-center shadow-lg group-active:scale-95 transition">
                        <svg class="w-5 h-5 text-[#B8860B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-900">Instalar App</p>
                        <p class="text-[9px] text-gray-400 uppercase font-black">Acesso rápido à K'enzza</p>
                    </div>
                </button>
            </div>
        </nav>
    </div>

    {{-- HEADER DESKTOP E BARRA SUPERIOR --}}
    <nav class="bg-white shadow-sm border-b sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10">
            <div class="flex justify-between h-20 lg:h-24 items-center">

                <div class="flex lg:hidden">
                    <button @click="mobileMenu = true" aria-label="Abrir menu de navegação" class="p-2 text-gray-600">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>

                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('shop.home') }}" class="flex items-center gap-2 group">
                        <img src="{{ asset('logo-k-preto.png') }}" alt="Logo da Kenzza" class="h-8 lg:h-10 w-auto">
                        <span class="hidden lg:block font-kenzza text-2xl lg:text-3xl font-bold text-black uppercase leading-none">
                            Kenzza
                        </span>
                    </a>
                </div>

                {{-- CAMPO DE BUSCA DESKTOP --}}
                <div class="hidden lg:flex flex-1 mx-8 justify-center">
                    <form action="{{ route('shop.index') }}" method="GET" class="w-full max-w-sm relative">
                        <input type="text" name="search" placeholder="O QUE VOCÊ PROCURA?"
                               class="w-full bg-gray-50 border border-gray-100 rounded-full py-2.5 px-5 text-[10px] font-black uppercase tracking-widest focus:ring-2 focus:ring-[#B8860B] focus:bg-white transition-all outline-none">
                        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#B8860B]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </form>
                </div>

                <div class="hidden lg:flex space-x-8 items-center h-full">
                    <a href="{{ route('shop.home') }}" class="text-[11px] font-bold text-gray-900 uppercase tracking-widest hover:text-[#B8860B]">Início</a>

                    <div class="relative group h-full flex items-center">
                        <button class="text-[11px] font-bold text-gray-900 uppercase tracking-widest hover:text-[#B8860B] flex items-center gap-1">
                            Para seu Cabelo
                            <svg class="w-3 h-3 text-gray-400 group-hover:text-[#B8860B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div class="absolute top-full left-0 w-64 bg-white border border-gray-100 shadow-2xl dropdown-shadow rounded-b-2xl py-4 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-[99999] transform translate-y-0">
                            @forelse($categories as $category)
                                <a href="{{ route('shop.index', $category->slug) }}" class="block px-6 py-3 text-[11px] font-bold text-gray-900 hover:bg-gray-50 hover:text-[#B8860B] uppercase tracking-widest transition-colors">
                                    {{ $category->nome }}
                                </a>
                            @empty
                                <span class="block px-6 py-2 text-[10px] text-gray-400 uppercase tracking-widest">Aguardando produtos...</span>
                            @endforelse
                        </div>
                    </div>

                    <a href="{{ route('shop.index') }}" class="text-[11px] font-bold text-gray-900 uppercase tracking-widest hover:text-[#B8860B]">Produtos</a>
                </div>

                {{-- Botão Seja Distribuidor --}}
                @if(!auth()->check() || (auth()->user()->role !== 'distributor' && auth()->user()->role !== 'admin'))
                    <a href="https://kenzza.com.br/seja-um-distribuidor"
                       class="flex items-center px-4 py-1.5 md:px-6 md:py-2.5 bg-black text-white text-[8px] md:text-[10px] font-black uppercase tracking-[0.1em] md:tracking-[0.2em] rounded-full border border-transparent hover:bg-[#B8860B] hover:scale-105 transition-all duration-300 shadow-lg shadow-black/10 ml-2 md:ml-4 whitespace-nowrap">
                        Seja um Distribuidor
                    </a>
                @endif

                {{-- Ícones da Direita: Carrinho e Conta --}}
                <div class="flex items-center space-x-2 lg:space-x-4 flex-1 justify-end">
                    <a href="{{ route('cart.index') }}" aria-label="Ir para o carrinho de compras" class="relative p-2 text-gray-700 hover:text-[#B8860B]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7"><path d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                        @php $qty = array_sum(array_column(session('cart', []), 'quantidade')); @endphp
                        @if($qty > 0)
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-[10px] font-black text-black bg-[#B8860B] rounded-full border-2 border-white transform translate-x-1/2 -translate-y-1/2">{{ $qty }}</span>
                        @endif
                    </a>

                    {{-- Ícone de Conta Restaurado e Unificado --}}
                    @auth
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                             <button @click="open = !open" class="w-10 h-10 rounded-full bg-black text-white font-bold text-xs border-2 border-[#B8860B] flex items-center justify-center overflow-hidden active:scale-95 transition-transform focus:outline-none">
                                {{ substr(Auth::user()->name, 0, 1) }}
                             </button>
                             <div x-show="open" style="display: none;" class="absolute right-0 mt-3 w-52 bg-white border border-gray-100 shadow-2xl rounded-2xl py-2 z-[60]">
                                <a href="{{ route('painel.redirect') }}" class="block px-4 py-2 text-[10px] font-black text-gray-700 hover:bg-gray-50 hover:text-[#B8860B] uppercase transition-colors">Painel</a>
                                <div class="border-t border-gray-50 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-[10px] font-black text-red-500 hover:bg-red-50 uppercase transition-colors">Sair</button>
                                </form>
                             </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" aria-label="Fazer login ou se cadastrar" class="p-2 text-gray-700 hover:text-[#B8860B] transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main>
        @auth
            @if(!auth()->user()->profile_completed)
                <div x-data="{ show: true }" x-show="show" class="relative bg-[#B8860B] text-white py-3 px-4 shadow-md" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="max-w-7xl mx-auto flex items-center justify-between flex-wrap">
                        <div class="flex items-center flex-1">
                            <span class="flex p-2 rounded-lg bg-black/20">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            </span>
                            <p class="ml-3 font-bold text-[10px] md:text-xs uppercase tracking-widest leading-tight">
                                Seu perfil está incompleto! <span class="hidden md:inline">Preencha seus dados de endereço para liberar o cálculo de frete.</span>
                            </p>
                        </div>
                        <div class="order-3 mt-2 flex-shrink-0 w-full sm:order-2 sm:mt-0 sm:w-auto">
                            <a href="{{ route('profile.edit') }}" class="flex items-center justify-center px-4 py-2 border border-transparent rounded-full shadow-sm text-[10px] font-black uppercase tracking-widest bg-black text-white hover:bg-white hover:text-black transition-all">
                                Completar Agora
                            </a>
                        </div>
                        <div class="order-2 flex-shrink-0 sm:order-3 sm:ml-3">
                            <button @click="show = false" type="button" class="-mr-1 flex p-2 rounded-md hover:bg-black/10 focus:outline-none">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        @endauth
        {{ $slot }}
    </main>

    {{-- FOOTER RESTAURADO E COMPLETO --}}
    <footer class="bg-white border-t border-gray-100 pt-20 pb-10 mt-20">
        <div class="max-w-7xl mx-auto px-6 lg:px-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
                {{-- Coluna 1: Marca --}}
                <div class="flex flex-col gap-6">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('logo-k-preto.png') }}" alt="Logo da Kenzza" class="h-8 w-auto">
                        <span class="font-kenzza text-2xl font-bold uppercase tracking-tighter">K'enzza</span>
                    </div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-relaxed">
                        Cosméticos profissionais de alta performance. Desenvolvido para realçar a beleza natural com tecnologia e sofisticação.
                    </p>
                </div>

                {{-- Coluna 2: Links --}}
                <div>
                    <h4 class="text-[11px] font-black uppercase tracking-[0.2em] mb-8 text-gray-900">Institucional</h4>
                    <ul class="space-y-4">
                        <li><a href="{{ route('shop.about') }}" class="text-[10px] font-bold text-gray-400 hover:text-black uppercase tracking-widest transition">Sobre a Marca</a></li>
                        <li><a href="{{ route('shop.index') }}" class="text-[10px] font-bold text-gray-400 hover:text-black uppercase tracking-widest transition">Nossos Produtos</a></li>
                        <li><a href="https://kenzza.com.br/seja-um-distribuidor" class="text-[10px] font-bold text-gray-400 hover:text-black uppercase tracking-widest transition">Seja Distribuidor</a></li>
                    </ul>
                </div>

                {{-- Coluna 3: Suporte --}}
                <div>
                    <h4 class="text-[11px] font-black uppercase tracking-[0.2em] mb-8 text-gray-900">Atendimento</h4>
                    <ul class="space-y-4">
                        <li><a href="https://wa.me/5511937525151" class="text-[10px] font-bold text-gray-400 hover:text-black uppercase tracking-widest transition">WhatsApp</a></li>
                         <li><a href="{{ route('shop.membership') }}" class="text-[10px] font-bold text-gray-400 hover:text-black uppercase tracking-widest transition">Tipos de Contas</a></li>
                        <li><a href="{{ route('shop.shipping') }}" class="text-[10px] font-bold text-gray-400 hover:text-black uppercase tracking-widest transition">Políticas de Envio</a></li>
                        <li><a href="{{ route('shop.terms') }}" class="text-[10px] font-bold text-gray-400 hover:text-black uppercase tracking-widest transition">Termos e Condições</a></li>
                         <li><a href="{{ route('shop.lgpd') }}" class="text-[10px] font-bold text-gray-400 hover:text-black uppercase tracking-widest transition">Privacidade e LGPD</a></li>
                         <li><a href="{{ route('politica-devolucao') }} "class="text-[10px] font-bold text-gray-400 hover:text-black uppercase tracking-widest transition">Política de Devolução</a></li>
                    </ul>
                </div>

                {{-- Coluna 4: Pagamento --}}
                <div>
                    <h4 class="text-[11px] font-black uppercase tracking-[0.2em] mb-8 text-gray-900">Pagamento</h4>
                    <div class="flex flex-wrap gap-3 opacity-40 grayscale hover:grayscale-0 hover:opacity-100 transition-all duration-500">
                        <img src="https://img.icons8.com/color/48/visa.png" class="h-6 w-auto" alt="Visa">
                        <img src="https://img.icons8.com/color/48/mastercard.png" class="h-6 w-auto" alt="Mastercard">
                        <img src="https://img.icons8.com/color/48/pix.png" class="h-6 w-auto" alt="Pix">
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest">
                    CNPJ 50.075.328/0001-45 &copy; 2026 K'enzza Hair Professional. Todos os direitos reservados.
                </p>
                <div class="flex gap-6 text-[10px] font-black uppercase tracking-widest text-gray-300">
                    <span>Feito com <span class="text-red-500">♥</span> por Megaflood</span>
                </div>
            </div>
        </div>
    </footer>

    <script>
        let deferredPrompt;
        const installContainer = document.getElementById('pwa-install-container');
        const installBtn = document.getElementById('pwa-install-btn');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            if (installContainer) installContainer.classList.remove('hidden');
        });

        if (installBtn) {
            installBtn.addEventListener('click', async () => {
                if (!deferredPrompt) return;
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                deferredPrompt = null;
                installContainer.classList.add('hidden');
            });
        }
    </script>
    <x-cookie-banner />
    </script>
    <x-cookie-banner />

    <script type="text/javascript">
        var Tawk_API = Tawk_API || {};
        var Tawk_LoadStart = new Date();

        // 1. Identifica o usuário logado e força a leitura da página atual
        Tawk_API.onLoad = function() {
            Tawk_API.setAttributes({
                @if(auth()->check())
                'name': '{{ auth()->user()->name }}',
                'email': '{{ auth()->user()->email }}',
                @endif
                'Página Atual': document.title,
                'URL': window.location.href
            }, function (error) {});
        };

        // 2. Garante a atualização da rota caso use navegação dinâmica (Livewire/Turbolinks)
        document.addEventListener('livewire:navigated', function () {
            if (typeof Tawk_API !== 'undefined' && Tawk_API.addEvent) {
                Tawk_API.addEvent('page_view', {
                    'title': document.title,
                    'url': window.location.href
                });
            }
        });

        (function(){
            var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
            s1.async=true;
            s1.src='https://embed.tawk.to/6a14931aca67221c346bebce/1jpg5t929';
            s1.charset='UTF-8';
            s1.setAttribute('crossorigin','*');
            s0.parentNode.insertBefore(s1,s0);
        })();
    </script>
</body>
</html>
