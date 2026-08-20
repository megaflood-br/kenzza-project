<x-store-layout>
    <div class="bg-gray-50 min-h-screen flex items-center justify-center py-20 px-6">
        <div class="max-w-md w-full">

            {{-- Card de Login --}}
            <div class="bg-white p-10 md:p-12 rounded-[3rem] shadow-xl border border-gray-100">
                <div class="text-center mb-10">
                    <h1 class="text-3xl font-black uppercase italic tracking-tighter text-gray-900">Acesso à <span class="text-[#B8860B]">Conta</span></h1>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-2">Bem-vinda à Kenzza Hair Professional</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest ml-4 mb-2 block">E-mail</label>
                        <input type="email" name="email" :value="old('email')" required autofocus
                               class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-1 focus:ring-[#B8860B] transition-all">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Senha --}}
                    <div>
                        <div class="flex justify-between items-center ml-4 mb-2">
                            <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest block">Senha</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-[9px] font-black text-[#B8860B] uppercase tracking-widest hover:underline">
                                    Esqueceu a senha?
                                </a>
                            @endif
                        </div>
                        <input type="password" name="password" required autocomplete="current-password"
                               class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-1 focus:ring-[#B8860B] transition-all">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    {{-- Lembrar-me --}}
                    <div class="flex items-center ml-4">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-[#B8860B] focus:ring-[#B8860B]">
                        <span class="ml-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Manter conectado</span>
                    </div>

                    {{-- Botão Entrar --}}
                    <button type="submit" class="w-full bg-black text-white py-5 rounded-full font-black uppercase tracking-[0.2em] text-[11px] hover:bg-[#B8860B] hover:text-black transition-all shadow-xl active:scale-95 shadow-black/5">
                        Entrar na Loja
                    </button>
                </form>

                <div class="mt-10 pt-10 border-t border-gray-50 text-center">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">Ainda não tem conta?</p>
                    <a href="{{ route('register') }}" class="inline-block border-2 border-black px-10 py-4 rounded-full font-black uppercase tracking-widest text-[10px] hover:bg-black hover:text-white transition-all">
                        Criar minha conta
                    </a>
                </div>
            </div>

            <div class="text-center mt-8">
                <a href="{{ route('shop.home') }}" class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] hover:text-black transition-colors">
                    ← Voltar para a Loja
                </a>
            </div>
        </div>
    </div>
</x-store-layout>
