<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KKTV253H');</script>
<!-- End Google Tag Manager -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
       <title>@hasSection('title')@yield('title') - @endif K'enzza Hair - Loja Oficial</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link rel="manifest" href="{{ asset('site.webmanifest') }}?v=2">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-title" content="K'enzza">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=2">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=2">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}?v=2">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

       <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
        <script>
            window.OneSignalDeferred = window.OneSignalDeferred || [];
            OneSignalDeferred.push(async function(OneSignal) {

                @env('production')
                    if (window.location.hostname !== 'kenzza.com.br') {
                        window.location.replace("https://kenzza.com.br" + window.location.pathname);
                    }
                @endenv

                try {
                    await OneSignal.init({
                        appId: "2eaed96d-5ff2-4460-97db-e3c33fcf91f2",
                        safari_web_id: "web.onesignal.auto.17387431-a83d-4c8e-8a03-7f2878a87383",
                        serviceWorkerParam: { scope: '/' },
                        serviceWorkerPath: 'OneSignalSDKWorker.js',
                    });

                    @auth
                        // 1. CORREÇÃO CRÍTICA: Identifica o usuário pelo ID do Banco de Dados
                        await OneSignal.login("{{ Auth::id() }}");

                        // 2. Adiciona o e-mail como um "Alias" (secundário)
                        await OneSignal.User.addEmail("{{ Auth::user()->email }}");

                        // 3. ENVIA A TAG DE ROLE PARA SEGMENTAÇÃO
                        await OneSignal.User.addTag("user_role", "{{ Auth::user()->role }}");
                    @endauth
                } catch (e) {
                    console.warn("OneSignal: Inicialização ignorada ou erro de domínio.");
                }
            });
        </script>

    </head>
    <body class="font-sans antialiased {{ Auth::user()->role === 'consumer' ? 'bg-white' : 'bg-gray-100' }}">
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KKTV253H"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
        <div class="min-h-screen">
            {{-- Lógica de Alternância de Menus --}}
            @if(Auth::user()->role === 'consumer')
                {{-- Certifique-se de que este arquivo existe com o estilo da gaveta preta --}}
                @include('layouts.navigation-ecommerce')
            @else
                @include('layouts.navigation')
            @endif

            {{-- O Header só aparece se não for consumidor (opcional, para limpar o visual da loja) --}}
            @isset($header)
                @if(Auth::user()->role !== 'consumer')
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif
            @endisset

            <main>
                {{-- Alerta de Perfil Incompleto --}}
@auth
    @if(!auth()->user()->profile_completed)
        <div x-data="{ show: true }"
             x-show="show"
             class="relative bg-[#B8860B] text-white py-3 px-4 shadow-md"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0">
            <div class="max-w-7xl mx-auto flex items-center justify-between flex-wrap">
                <div class="flex items-center flex-1">
                    <span class="flex p-2 rounded-lg bg-black/20">
                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
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
                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif
@endauth
                {{ $slot }}
            </main>
        </div>

        <script src="https://unpkg.com/imask"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const phoneElements = document.querySelectorAll('.mask-phone');
                phoneElements.forEach(el => {
                    IMask(el, { mask: '(00) 00000-0000' });
                });

                const emailElements = document.querySelectorAll('.mask-email');
                emailElements.forEach(el => {
                    el.addEventListener('input', function() {
                        this.value = this.value.toLowerCase().replace(/\s/g, '');
                    });
                });
            });
        </script>
        <script>
    let deferredPrompt;
    const installContainer = document.getElementById('pwa-install-container');
    const installBtn = document.getElementById('pwa-install-btn');

    window.addEventListener('beforeinstallprompt', (e) => {
        // Impede o Chrome de mostrar o banner automático (nós vamos mostrar o nosso)
        e.preventDefault();
        // Guarda o evento para disparar depois
        deferredPrompt = e;
        // Mostra o botão no menu mobile
        if (installContainer) {
            installContainer.classList.remove('hidden');
        }
    });

    if (installBtn) {
        installBtn.addEventListener('click', async () => {
            if (!deferredPrompt) return;

            // Mostra o prompt de instalação do navegador
            deferredPrompt.prompt();

            // Verifica se o usuário aceitou ou recusou
            const { outcome } = await deferredPrompt.userChoice;
            console.log(`Usuário escolheu: ${outcome}`);

            // Limpa o prompt para não ser usado de novo
            deferredPrompt = null;
            // Esconde o botão novamente
            installContainer.classList.add('hidden');
        });
    }

    // Esconde o botão se o app já estiver instalado
    window.addEventListener('appinstalled', () => {
        if (installContainer) {
            installContainer.classList.add('hidden');
        }
        deferredPrompt = null;
        console.log('PWA instalado com sucesso!');
    });
</script>

        @livewireScripts


    </body>
</html>
