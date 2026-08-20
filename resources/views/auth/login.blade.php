<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - K'enzza Hair Professional</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-black text-white antialiased flex items-center justify-center min-h-screen p-6">

    <div class="max-w-md w-full">
        <div class="text-center mb-10">
            <a href="{{ route('shop.home') }}" class="inline-flex items-center gap-4">
                <div class="w-40">
                    <img src="{{ asset('logo-k-bco.png') }}" alt="K'enzza" class="w-full h-auto object-contain">
                </div>

                <div class="w-[2px] h-10 bg-[#B8860B]"></div>

                <span class="text-[#B8860B] font-bold text-[10px] uppercase tracking-[0.3em] leading-none">
                    {{ request()->routeIs('distribuidor.login') ? 'Parceiros' : 'Loja' }}
                </span>
            </a>
        </div>

        <div class="bg-white rounded-[2.5rem] p-10 shadow-2xl">
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">E-mail</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           class="w-full bg-gray-50 border-2 border-gray-100 focus:border-[#B8860B] rounded-2xl py-4 px-6 text-black text-sm outline-none transition-all placeholder-gray-300"
                           placeholder="seu@email.com">
                    @if ($errors->has('email'))
                        <p class="text-red-500 text-[10px] font-bold mt-2 uppercase">{{ $errors->first('email') }}</p>
                    @endif
                </div>

                <div>
                    <label for="password" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Senha</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                           class="w-full bg-gray-50 border-2 border-gray-100 focus:border-[#B8860B] rounded-2xl py-4 px-6 text-black text-sm outline-none transition-all placeholder-gray-300"
                           placeholder="••••••••">
                    @if ($errors->has('password'))
                        <p class="text-red-500 text-[10px] font-bold mt-2 uppercase">{{ $errors->first('password') }}</p>
                    @endif
                </div>

                <div class="flex items-center justify-between">
                    <label for="remember_me" class="flex items-center gap-2 cursor-pointer group">
                        <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-[#B8860B] focus:ring-[#B8860B]">
                        <span class="text-[11px] font-bold text-gray-400 group-hover:text-gray-600 transition-colors uppercase tracking-tighter">Lembrar-me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-[11px] font-bold text-[#B8860B] hover:underline uppercase tracking-tighter">
                            Esqueceu a senha?
                        </a>
                    @endif
                </div>

                <button type="submit"
                        class="w-full bg-black text-[#B8860B] font-black uppercase text-xs tracking-[0.2em] py-5 rounded-2xl hover:bg-[#B8860B] hover:text-black transition-all shadow-xl">
                    Entrar na Conta
                </button>
            </form>

            <div class="mt-8 text-center border-t border-gray-50 pt-8">
                @if(request()->routeIs('distribuidor.login'))
                    {{-- TEXTO CASO SEJA DISTRIBUIDOR --}}
                    <p class="text-[11px] font-bold text-gray-400 uppercase mb-2">Acesso restrito a parceiros homologados</p>
                    <a href="/seja-um-distribuidor" class="text-[11px] font-black text-black hover:text-[#B8860B] uppercase transition-colors">Solicitar cadastro de distribuidor</a>
                @else
                    {{-- TEXTO CASO SEJA CLIENTE DA LOJA --}}
                    <p class="text-[11px] font-bold text-gray-400 uppercase mb-2">Novo por aqui?</p>
                    <a href="{{ route('register') }}" class="text-[11px] font-black text-black hover:text-[#B8860B] uppercase transition-colors">Criar minha conta de cliente</a>
                @endif
            </div>
        </div>

        <div class="mt-10 text-center">
            <a href="{{ route('shop.home') }}" class="text-[9px] font-bold text-gray-600 hover:text-[#B8860B] uppercase tracking-[0.5em] transition-colors italic">
                ← Voltar para a Loja
            </a>
        </div>
    </div>

</body>
</html>
