<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title><title>@hasSection('title')@yield('title') - @endif K'enzza Hair - Loja Oficial</title></title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Google tag (gtag.js) -->
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KKTV253H');</script>
<!-- End Google Tag Manager -->
</head>
<body class="font-sans antialiased bg-white">
    <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KKTV253H"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
    <div class="min-h-screen">
        {{-- Aqui chamamos o menu que tem o estilo da foto --}}
        @include('layouts.navigation-ecommerce')

        <main>
            {{ $slot }}
        </main>
    </div>
</body>
</html>
