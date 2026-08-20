<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>K'enzza Professional | Seja um Distribuidor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <style>
        .bg-kenzza {
            background-image: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.85)),
                              url('https://images.unsplash.com/photo-1562322140-8baeececf3df?q=80&w=2069&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
        }
        .border-gold { border-color: #c5a059; }
        .bg-gold { background-color: #c5a059; }
    </style>
</head>
<body class="bg-black text-white min-h-screen flex flex-col items-center justify-center p-6 bg-kenzza">

    <div class="max-w-md w-full text-center">
        <div class="mb-8 flex flex-col items-center">
            <div class="w-32 h-32 rounded-lg border-2 border-[#c5a059] p-3 bg-black shadow-2xl mb-4 flex items-center justify-center">
                <img src="{{ asset('logo-k-bco.png') }}" alt="K'enzza" class="w-full h-full object-contain">
            </div>
            <h1 class="text-lg font-bold tracking-widest uppercase italic text-white">K'enzza Professional</h1>
        </div>

        <h2 class="text-xl font-semibold mb-2">Seja um Distribuidor Oficial</h2>
        <p class="text-gray-400 mb-8 text-sm">Junte-se a uma marca com mais de 20 anos de história e alta performance.</p>

        @if(session('success'))
            <div class="bg-green-900/50 border border-green-500 text-green-200 p-4 rounded mb-6 text-sm text-center">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('leads.store') }}" method="POST" class="space-y-4 text-left">
            @csrf
            <div>
                <label class="block text-xs uppercase tracking-wider text-gray-500 mb-1 ml-1">Nome Completo</label>
                <input type="text" name="nome" placeholder="Ex: Carlos Silva"
                    class="w-full p-3 rounded-lg bg-white/5 border border-white/10 focus:border-[#c5a059] outline-none transition" required>
            </div>

            <div class="flex gap-4">
                <div class="flex-1">
                    <label class="block text-xs uppercase tracking-wider text-gray-500 mb-1 ml-1">Cidade</label>
                    <input type="text" name="cidade" placeholder="Ex: Atibaia"
                        class="w-full p-3 rounded-lg bg-white/5 border border-white/10 focus:border-[#c5a059] outline-none transition" required>
                </div>
                <div class="w-24">
                    <label class="block text-xs uppercase tracking-wider text-gray-500 mb-1 ml-1">UF</label>
                    <select name="estado" class="w-full p-3 rounded-lg bg-white/5 border border-white/10 focus:border-[#c5a059] outline-none transition text-gray-400" required>
                        <option value="" disabled selected>--</option>
                        <option value="AC">AC</option><option value="AL">AL</option><option value="AP">AP</option><option value="AM">AM</option>
                        <option value="BA">BA</option><option value="CE">CE</option><option value="DF">DF</option><option value="ES">ES</option>
                        <option value="GO">GO</option><option value="MA">MA</option><option value="MT">MT</option><option value="MS">MS</option>
                        <option value="MG">MG</option><option value="PA">PA</option><option value="PB">PB</option><option value="PR">PR</option>
                        <option value="PE">PE</option><option value="PI">PI</option><option value="RJ">RJ</option><option value="RN">RN</option>
                        <option value="RS">RS</option><option value="RO">RO</option><option value="RR">RR</option><option value="SC">SC</option>
                        <option value="SP">SP</option><option value="SE">SE</option><option value="TO">TO</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider text-gray-500 mb-1 ml-1">WhatsApp</label>
                <input id="whatsapp" name="whatsapp" type="text" placeholder="(00) 00000-0000"
                    class="w-full p-3 rounded-lg bg-white/5 border border-white/10 focus:border-[#c5a059] outline-none transition mask-phone" required>
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider text-gray-500 mb-1 ml-1">E-mail (Opcional)</label>
                <input id="email" name="email" type="email" placeholder="exemplo@kenzza.com.br"
                    class="w-full p-3 rounded-lg bg-white/5 border border-white/10 focus:border-[#c5a059] outline-none transition mask-email">
            </div>

            <button type="submit"
                class="w-full bg-[#c5a059] hover:bg-[#b08d4a] text-black font-bold py-4 rounded-lg shadow-lg transform transition hover:-translate-y-1 uppercase tracking-widest text-sm mt-4">
                Solicitar Credenciamento
            </button>
        </form>

        <p class="mt-8 text-xs text-gray-600">© 2026 K'enzza Hair. Todos os direitos reservados.</p>
    </div>

    <script src="https://unpkg.com/imask"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Máscara Telefone
            const phoneEl = document.getElementById('whatsapp');
            IMask(phoneEl, { mask: '(00) 00000-0000' });

            // Filtro Email (Minúsculas e sem espaços)
            const emailEl = document.getElementById('email');
            emailEl.addEventListener('input', function() {
                this.value = this.value.toLowerCase().replace(/\s/g, '');
            });
        });
    </script>
</body>
</html>
