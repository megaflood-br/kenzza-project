<x-store-layout>
    <div class="bg-gray-50 min-h-screen flex items-center justify-center py-20 px-6">
        <div class="max-w-md w-full">

            <div class="bg-white p-10 md:p-12 rounded-[3rem] shadow-xl border border-gray-100">
                <div class="text-center mb-10">
                    <h1 class="text-3xl font-black uppercase italic tracking-tighter text-gray-900">Criar <span class="text-[#B8860B]">Cadastro</span></h1>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-2">Junte-se à experiência Kenzza</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    {{-- Nome --}}
                    <div>
                        <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest ml-4 mb-2 block">Nome Completo</label>
                        <input type="text" name="name" :value="old('name')" required autofocus
                               class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-1 focus:ring-[#B8860B]">
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest ml-4 mb-2 block">E-mail</label>
                        <input type="email" name="email" :value="old('email')" required
                               class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-1 focus:ring-[#B8860B]">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Senha --}}
                    <div>
                        <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest ml-4 mb-2 block">Senha</label>
                        <input type="password" name="password" required
                               class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-1 focus:ring-[#B8860B]">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    {{-- Confirmar Senha --}}
                    <div>
                        <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest ml-4 mb-2 block">Confirmar Senha</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-1 focus:ring-[#B8860B]">
                    </div>

                    <button type="submit" class="w-full bg-black text-white py-5 rounded-full font-black uppercase tracking-[0.2em] text-[11px] hover:bg-[#B8860B] hover:text-black transition-all shadow-xl mt-4">
                        Finalizar Cadastro
                    </button>
                </form>

                <div class="mt-8 text-center">
                    <a href="{{ route('login') }}" class="text-[10px] font-black text-[#B8860B] uppercase tracking-widest hover:underline">
                        Já tenho conta? Fazer Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-store-layout>
