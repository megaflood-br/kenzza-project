<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>K'enzza Professional | Alta Performance Capilar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/dist/css/all.min.css">
   <link rel="manifest" href="{{ asset('site.webmanifest') }}?v=2">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-title" content="K'enzza">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=2">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=2">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}?v=2">
</head>
<body class="bg-black text-white min-h-screen flex flex-col items-center justify-center p-6 text-center">

    <div class="max-w-xl w-full">
        <div class="mb-10 flex justify-center">
            <div class="w-40 h-40 rounded-2xl border-2 border-[#c5a059] p-4 bg-black shadow-[0_0_20px_rgba(197,160,89,0.2)] flex items-center justify-center">
                <img src="{{ asset('logo-k-bco.png') }}" alt="K'enzza" class="w-full h-full object-contain">
            </div>
        </div>

        <h1 class="text-2xl md:text-3xl font-light tracking-[0.3em] uppercase mb-4">
            Transforme a beleza dos fios
        </h1>
        <p class="text-gray-400 text-sm md:text-base mb-10 font-light tracking-wide px-4">
            Sua marca de alta performance em cosméticos capilares.<br>
            Escolha como deseja interagir conosco:
        </p>

        <div class="space-y-4 px-6 mb-12">
            <a href="https://wa.me/5511937525151?text=Olá! Gostaria de mais informações sobre os produtos Kenzza."
               target="_blank"
               class="flex items-center justify-center gap-3 w-full py-4 border border-[#c5a059] text-[#c5a059] rounded-full hover:bg-[#c5a059] hover:text-black transition-all duration-300 font-bold uppercase tracking-widest text-sm">
                <i class="fa-brands fa-whatsapp text-lg"></i>
                Tenho interesse em comprar
            </a>

            <a href="{{ url('/seja-um-distribuidor') }}"
               class="flex items-center justify-center gap-3 w-full py-4 bg-[#c5a059] text-black rounded-full hover:bg-[#b08d4a] transition-all duration-300 font-bold uppercase tracking-widest text-sm shadow-lg">
                <i class="fa-solid fa-handshake text-lg"></i>
                Seja um Distribuidor
            </a>
        </div>

        <div class="flex justify-center gap-8 border-t border-gray-800 pt-8">
            <a href="https://instagram.com/kenzzaprofessional" target="_blank" class="text-gray-400 hover:text-[#c5a059] transition-colors text-2xl">
                <i class="fa-brands fa-instagram"></i>
            </a>
            <a href="https://facebook.com/kenzzaprofessional" target="_blank" class="text-gray-400 hover:text-[#c5a059] transition-colors text-2xl">
                <i class="fa-brands fa-facebook-f"></i>
            </a>
        </div>

        <p class="mt-12 text-[10px] text-gray-600 uppercase tracking-[0.2em]">
            © 2026 K'enzza Hair. Todos os direitos reservados.
        </p>
    </div>

</body>
</html>
