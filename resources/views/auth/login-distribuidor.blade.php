<x-guest-layout>
    <div class="mb-4 text-center">
        <h2 class="text-2xl font-bold text-gray-800">Portal do Distribuidor</h2>
        <p class="text-sm text-gray-500 uppercase tracking-widest mt-2">K'enzza Hair Professional</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" value="E-mail Corporativo" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Senha de Acesso" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-600">Lembrar acesso</span>
            </label>
            <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-md font-bold uppercase text-xs hover:bg-black transition">
                Entrar no Portal
            </button>
        </div>
    </form>
</x-guest-layout>
