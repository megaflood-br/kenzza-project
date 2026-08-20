<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - K'enzza Hair Professional</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
    {{-- Apenas carrega o AlpineJS para o select funcionar corretamente --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-black text-white antialiased flex items-center justify-center min-h-screen p-6 py-16">

    <div class="max-w-md w-full">
        <div class="text-center mb-10">
            <a href="{{ route('shop.home') }}" class="inline-flex items-center gap-4">
                <div class="w-40"><img src="{{ asset('logo-k-bco.png') }}" alt="K'enzza" class="w-full h-auto object-contain"></div>
                <div class="w-[2px] h-10 bg-[#B8860B]"></div>
                <span class="text-[#B8860B] font-bold text-[10px] uppercase tracking-[0.3em] leading-none">Cadastro</span>
            </a>
        </div>

        <div class="bg-white rounded-[2.5rem] p-10 shadow-2xl">
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-2xl">
                    <p class="text-[10px] font-black uppercase tracking-widest text-red-700 mb-1">Ops! Verifique os dados:</p>
                    <ul class="list-disc list-inside text-[11px] text-red-600 font-bold space-y-0.5">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" x-data="{ role: '{{ old('role', 'consumer') }}' }" class="space-y-6">
                @csrf

                {{-- Nome --}}
                <div>
                    <label for="name" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nome Completo</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="w-full bg-gray-50 border-2 {{ $errors->has('name') ? 'border-red-500 focus:border-red-500' : 'border-gray-100 focus:border-[#B8860B]' }} text-black text-sm rounded-2xl py-4 px-6 outline-none transition-all placeholder-gray-300">
                </div>

                {{-- E-mail --}}
                <div>
                    <label for="email" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">E-mail</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required class="w-full bg-gray-50 border-2 {{ $errors->has('email') ? 'border-red-500 focus:border-red-500' : 'border-gray-100 focus:border-[#B8860B]' }} text-black text-sm rounded-2xl py-4 px-6 outline-none transition-all placeholder-gray-300">
                </div>

                {{-- WhatsApp (NOVO) --}}
                <div>
                    <label for="whatsapp" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">WhatsApp</label>
                    <input id="whatsapp" type="text" name="whatsapp" value="{{ old('whatsapp') }}" required placeholder="(00) 00000-0000"
                           class="w-full bg-gray-50 border-2 {{ $errors->has('whatsapp') ? 'border-red-500 focus:border-red-500' : 'border-gray-100 focus:border-[#B8860B]' }} text-black text-sm rounded-2xl py-4 px-6 outline-none transition-all placeholder-gray-300">
                </div>

                {{-- Tipo de Conta --}}
                <div>
                    <label for="role" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Eu sou</label>
                    <select id="role" name="role" x-model="role" class="w-full bg-gray-50 border-2 {{ $errors->has('role') ? 'border-red-500 focus:border-red-500' : 'border-gray-100 focus:border-[#B8860B]' }} text-black text-sm font-bold rounded-2xl py-4 px-6 outline-none transition-all appearance-none cursor-pointer">
                        <option value="consumer">Consumidor Final</option>
                        <option value="salon">Profissional / Salão</option>
                    </select>
                </div>

                {{-- Documento --}}
                <div>
                    <label for="document" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2" x-text="role === 'salon' ? 'CNPJ da Empresa' : 'CPF do Titular'">Documento</label>
                    <input id="document" type="text" name="document" value="{{ old('document') }}" required x-bind:placeholder="role === 'salon' ? '00.000.000/0000-00' : '000.000.000-00'" class="w-full bg-gray-50 border-2 {{ $errors->has('document') ? 'border-red-500 focus:border-red-500' : 'border-gray-100 focus:border-[#B8860B]' }} text-black text-sm rounded-2xl py-4 px-6 outline-none transition-all placeholder-gray-300">
                </div>

                {{-- Senhas --}}
                <div>
                    <label for="password" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Senha</label>
                    <input id="password" type="password" name="password" required class="w-full bg-gray-50 border-2 {{ $errors->has('password') ? 'border-red-500 focus:border-red-500' : 'border-gray-100 focus:border-[#B8860B]' }} text-black text-sm rounded-2xl py-4 px-6 outline-none transition-all placeholder-gray-300" placeholder="••••••••">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Confirmar Senha</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required class="w-full bg-gray-50 border-2 border-gray-100 focus:border-[#B8860B] text-black text-sm rounded-2xl py-4 px-6 outline-none transition-all placeholder-gray-300" placeholder="••••••••">
                </div>

                <div class="pt-2">
                     <button type="submit" class="w-full bg-black text-[#B8860B] font-black uppercase text-xs tracking-[0.2em] py-5 rounded-2xl hover:bg-[#B8860B] hover:text-black transition-all">Finalizar Cadastro</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const docInput = document.getElementById('document');
        const roleSelect = document.getElementById('role');
        const whatsInput = document.getElementById('whatsapp');

        // Máscara WhatsApp
        whatsInput.addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 11) v = v.slice(0, 11);
            v = v.replace(/^(\d{2})(\d)/g, '($1) $2');
            v = v.replace(/(\d{5})(\d)/, '$1-$2');
            e.target.value = v;
        });

        // Máscara CPF/CNPJ original
        function applyMask() {
            let value = docInput.value.replace(/\D/g, '');
            const role = roleSelect.value;
            if (role === 'salon') {
                if (value.length > 14) value = value.slice(0, 14);
                value = value.replace(/^(\d{2})(\d)/, '$1.$2').replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3').replace(/\.(\d{3})(\d)/, '.$1/$2').replace(/(\d{4})(\d)/, '$1-$2');
            } else {
                if (value.length > 11) value = value.slice(0, 11);
                value = value.replace(/^(\d{3})(\d)/, '$1.$2').replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3').replace(/(\d{3})\.(\d{3})\.(\d{3})(\d)/, '$1.$2.$3-$4');
            }
            docInput.value = value;
        }
        docInput.addEventListener('input', applyMask);
        roleSelect.addEventListener('change', function() { docInput.value = ''; applyMask(); });
    });
    </script>
</body>
</html>
