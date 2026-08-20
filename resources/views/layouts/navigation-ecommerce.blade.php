<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                {{-- Logo redirecionando para a loja --}}
                <a href="{{ route('shop.home') }}">
                    <img src="{{ asset('logo-k-preto.png') }}" class="h-9 w-auto">
                </a>

                {{-- Links Desktop --}}
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('shop.home')" :active="request()->routeIs('shop.*')">Loja</x-nav-link>
                    <x-nav-link :href="route('customer.panel')" :active="request()->routeIs('customer.panel')">Meu Painel</x-nav-link>
                    <x-nav-link :href="route('customer.orders')" :active="request()->routeIs('customer.orders*')">Meus Pedidos</x-nav-link>
                    <x-nav-link :href="route('tickets.index')" :active="request()->routeIs('tickets.*')">Suporte</x-nav-link>
                </div>
            </div>

            <div class="flex items-center gap-4">
                {{-- Carrinho --}}
                <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-400 hover:text-black">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </a>

                {{-- Botão Menu Mobile (Gaveta) --}}
                <button @click="open = true" class="p-2 rounded-full bg-black text-[#c5a059]">
                    <span class="text-[10px] font-black px-2">{{ Auth::user()->name }}</span>
                </button>


            </div>
        </div>
    </div>

    {{-- GAVETA LATERAL (Igual à sua foto) --}}
    <div x-show="open" class="fixed inset-0 z-[100] sm:hidden" style="display: none;">
        <div x-show="open" x-transition.opacity @click="open = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div x-show="open" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" class="fixed right-0 top-0 h-full w-72 bg-white shadow-2xl flex flex-col">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Acesso de Cliente</p>
                <button @click="open = false" class="text-gray-400 hover:text-black">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex-1 py-4">

                  <x-responsive-nav-link :href="route('customer.orders')">MEUS PEDIDOS</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.edit')">MEU PERFIL</x-responsive-nav-link>
                <div class="border-t border-gray-50 my-4"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-xs font-black text-red-500 uppercase">SAIR DA CONTA</button>
                </form>
            </div>
        </div>
    </div>
</nav>
