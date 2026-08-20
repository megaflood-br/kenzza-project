<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    {{-- Redirecionamento inteligente do Logo para painel.redirect --}}
                    <a href="{{ Auth::user()->role === 'consumer' ? route('shop.home') : route('painel.redirect') }}">
                        <div class="w-10 h-10 border border-gray-200 rounded p-1">
                            <img src="{{ asset('logo-k-preto.png') }}" class="h-full w-full object-contain">
                        </div>
                    </a>
                </div>

                <div class="hidden space-x-6 sm:-my-px sm:ms-10 sm:flex">
                    {{-- Link Início redireciona via painel.redirect e fica ativo nos painéis --}}
                    @if(Auth::user()->role === 'consumer')
                        <x-nav-link :href="route('shop.home')" :active="request()->routeIs('shop.home')">
                            {{ __('Loja') }}
                        </x-nav-link>
                        <x-nav-link :href="route('customer.panel')" :active="request()->routeIs('customer.panel')">
                            {{ __('Meu Painel') }}
                        </x-nav-link>
                        <x-nav-link :href="route('customer.orders')" :active="request()->routeIs('customer.orders*')">
                            {{ __('Meus Pedidos') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('painel.redirect')" :active="request()->routeIs('dashboard', 'distributor.panel')">
                            {{ __('Início') }}
                        </x-nav-link>
                    @endif

                    {{-- MENU COMERCIAL / CRM (Exclusivo Admin e Manager) --}}
                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'manager')
                        <div class="inline-flex items-center px-1 pt-1">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 focus:outline-none transition {{ request()->routeIs('crm.*') ? 'text-black border-b-2 border-[#c5a059]' : '' }}">
                                        <span>Comercial</span>
                                        <svg class="ms-1 h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('crm.distributors')">{{ __('Carteira de Clientes') }}</x-dropdown-link>
                                    <x-dropdown-link :href="route('crm.orders.create')">{{ __('Emitir Pedido') }}</x-dropdown-link>

                                    {{-- A gerente também precisa ver a gestão geral de pedidos e gerir a equipe --}}
                                    @if(Auth::user()->role === 'manager')
                                        <hr class="border-gray-100 my-1">
                                        <x-dropdown-link :href="route('admin.orders.index')">{{ __('Todos os Pedidos') }}</x-dropdown-link>
                                        <x-dropdown-link :href="route('users.index')">{{ __('Gerir Equipe (Usuários)') }}</x-dropdown-link>
                                    @endif
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif

                    {{-- MENU ADMIN & EDITOR --}}
                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'editor')
                        <x-nav-link :href="route('leads.index')" :active="request()->routeIs('leads.*')">{{ __('Leads') }}</x-nav-link>

                        @if(Auth::user()->role === 'admin')
                            <div class="inline-flex items-center px-1 pt-1">
                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button class="inline-flex items-center text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 focus:outline-none transition {{ request()->routeIs('users.*', 'admin.notifications.*', 'admin.logs.*') ? 'text-black border-b-2 border-[#c5a059]' : '' }}">
                                            <span>Usuários</span>
                                            <svg class="ms-1 h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        <x-dropdown-link :href="route('users.index')">{{ __('Gerir Usuários') }}</x-dropdown-link>
                                        <x-dropdown-link :href="route('admin.notifications.create')">{{ __('Enviar Notificações') }}</x-dropdown-link>
                                        <hr class="border-gray-100 my-1">
                                        <x-dropdown-link :href="route('admin.logs.index')" :active="request()->routeIs('admin.logs.*')">
                                            {{ __('Logs de Auditoria') }}
                                        </x-dropdown-link>
                                    </x-slot>
                                </x-dropdown>
                            </div>

                            <div class="inline-flex items-center px-1 pt-1">
                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button class="inline-flex items-center text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 focus:outline-none transition {{ request()->routeIs('banners.*') ? 'text-black border-b-2 border-[#c5a059]' : '' }}">
                                            <span>E-commerce</span>
                                            <svg class="ms-1 h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        <x-dropdown-link :href="route('banners.index')">{{ __('Banners & Slides') }}</x-dropdown-link>
                                    </x-slot>
                                </x-dropdown>
                            </div>
                        @endif

                        {{-- CONTAGEM EM TEMPO REAL DOS PEDIDOS PENDENTES --}}
                        @php
                            $pendingOrdersCount = \App\Models\Order::whereIn('status', ['pendente', 'aguardando_confirmacao'])->count();
                        @endphp

                        <x-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')" class="relative">
                            {{ __('Gestão Pedidos') }}
                            @if($pendingOrdersCount > 0)
                                <span class="ms-1 text-[10px] bg-red-600 text-white px-1.5 rounded-full">{{ $pendingOrdersCount }}</span>
                            @endif
                        </x-nav-link>

                        {{-- MENU CATÁLOGO --}}
                        <div class="inline-flex items-center px-1 pt-1">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 focus:outline-none transition {{ request()->routeIs('products.*', 'categories.*', 'admin.settings.margins') ? 'text-black border-b-2 border-[#c5a059]' : '' }}">
                                        <span>Catálogo</span>
                                        <svg class="ms-1 h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('products.index')">{{ __('Produtos') }}</x-dropdown-link>
                                    <x-dropdown-link :href="route('categories.index')">{{ __('Categorias') }}</x-dropdown-link>
                                    <x-dropdown-link :href="route('coupons.index')" > {{ __('Cupons de Desconto') }}</x-dropdown-link>

                                    @if(Auth::user()->role === 'admin')
                                        <hr class="border-gray-100 my-1">
                                        <x-dropdown-link :href="route('admin.settings.margins')" class="text-[#B8860B] font-bold">
                                            {{ __('Margens de Lucro') }}
                                        </x-dropdown-link>
                                    @endif
                                </x-slot>
                            </x-dropdown>
                        </div>

                        <div class="inline-flex items-center px-1 pt-1">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 focus:outline-none transition {{ request()->routeIs('sheets.*', 'media.admin') ? 'text-black border-b-2 border-[#c5a059]' : '' }}">
                                        <span>Materiais</span>
                                        <svg class="ms-1 h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('sheets.index.admin')">{{ __('Fichas Técnicas') }}</x-dropdown-link>
                                    <x-dropdown-link :href="route('media.admin')">{{ __('Drive de Mídia') }}</x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif

                    {{-- MENU PARCEIRO (DISTRIBUIDOR E REPRESENTANTE) --}}
                    @if(in_array(Auth::user()->role, ['distributor', 'representative']))
                        <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">{{ __('Pedidos') }}</x-nav-link>

                        {{-- Condicional Inteligente para a Tabela --}}
                        @if(Auth::user()->role === 'representative')
                            <x-nav-link :href="route('prices.representative')" :active="request()->routeIs('prices.representative')">{{ __('Tabela de Preços') }}</x-nav-link>
                        @else
                            <x-nav-link :href="route('prices.public')" :active="request()->routeIs('prices.public')">{{ __('Tabela de Preços') }}</x-nav-link>
                        @endif

                        <x-nav-link :href="route('sheets.index')" :active="request()->routeIs('sheets.index')">{{ __('Fichas Técnicas') }}</x-nav-link>
                        <x-nav-link :href="route('media.index')" :active="request()->routeIs('media.index')">{{ __('Drive de Mídia') }}</x-nav-link>
                    @endif

                    <x-nav-link :href="route('tickets.index')" :active="request()->routeIs('tickets.*')">{{ __('Suporte') }}</x-nav-link>
                </div>
            </div>

            {{-- MENU DIREITO: USUÁRIO --}}
            <div class="flex items-center">
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="font-bold text-sm text-gray-500 hover:text-gray-700 transition flex items-center gap-2">
                                <span class="bg-gray-100 px-2 py-1 rounded text-[10px] uppercase text-[#c5a059]">{{ Auth::user()->role }}</span>
                                <span>{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">{{ __('Meu Perfil') }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Sair') }}</x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                {{-- Hambúrguer Mobile --}}
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = true" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 transition">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MENU MOBILE (SIDEBAR) --}}
    <div x-show="open" class="fixed inset-0 z-[100] sm:hidden" style="display: none;">
        <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="open = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div x-show="open" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="fixed right-0 top-0 h-full w-72 bg-white shadow-2xl flex flex-col">

            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <img src="{{ asset('logo-k-preto.png') }}" class="h-8 w-auto">
                <button @click="open = false" class="text-gray-400 hover:text-black">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto py-4">
                @if(Auth::user()->role === 'consumer')
                    <x-responsive-nav-link :href="route('shop.home')" :active="request()->routeIs('shop.home')">{{ __('Loja') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('customer.panel')" :active="request()->routeIs('customer.panel')">{{ __('Meu Painel') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('customer.orders')" :active="request()->routeIs('customer.orders*')">{{ __('Meus Pedidos') }}</x-responsive-nav-link>
                @else
                    {{-- Uso do painel.redirect e regra de ativo dupla --}}
                    <x-responsive-nav-link :href="route('painel.redirect')" :active="request()->routeIs('dashboard', 'distributor.panel')">{{ __('Início') }}</x-responsive-nav-link>
                @endif

                {{-- MENU COMERCIAL MOBILE --}}
                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'manager')
                    <div class="px-4 py-2 mt-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Comercial') }}</div>
                    <x-responsive-nav-link :href="route('crm.distributors')">{{ __('Carteira de Clientes') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('crm.orders.create')">{{ __('Emitir Pedido') }}</x-responsive-nav-link>
                    @if(Auth::user()->role === 'manager')
                        <x-responsive-nav-link :href="route('admin.orders.index')">{{ __('Todos os Pedidos') }}</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('users.index')">{{ __('Gerir Equipe (Usuários)') }}</x-responsive-nav-link>
                    @endif
                @endif

                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'editor')
                    <div class="px-4 py-2 mt-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Gestão') }}</div>
                    <x-responsive-nav-link :href="route('leads.index')">{{ __('Leads') }}</x-responsive-nav-link>

                    {{-- BADGE NO MOBILE TAMBÉM --}}
                    @php
                        $pendingOrdersCount = \App\Models\Order::whereIn('status', ['pendente', 'aguardando_confirmacao'])->count();
                    @endphp
                    <x-responsive-nav-link :href="route('admin.orders.index')" class="flex justify-between items-center">
                        {{ __('Pedidos') }}
                        @if($pendingOrdersCount > 0)
                            <span class="text-[10px] bg-red-600 text-white px-2 py-0.5 rounded-full">{{ $pendingOrdersCount }}</span>
                        @endif
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('products.index')">{{ __('Produtos') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('coupons.index')" > {{ __('Cupons de Desconto') }}</x-responsive-nav-link>

                    @if(Auth::user()->role === 'admin')
                        <x-responsive-nav-link :href="route('admin.settings.margins')">{{ __('Margens de Lucro') }}</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('users.index')">{{ __('Gerir Usuários') }}</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.logs.index')" :active="request()->routeIs('admin.logs.*')">{{ __('Logs de Auditoria') }}</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('banners.index')">{{ __('Banners & Slides') }}</x-responsive-nav-link>
                    @endif

                    <x-responsive-nav-link :href="route('sheets.index.admin')">{{ __('Fichas Técnicas') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('media.admin')">{{ __('Drive de Mídia') }}</x-responsive-nav-link>
                @endif

                {{-- MENU PARCEIRO MOBILE --}}
                @if(in_array(Auth::user()->role, ['distributor', 'representative']))
                    <div class="px-4 py-2 mt-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Minha Área') }}</div>
                    <x-responsive-nav-link :href="route('orders.index')">{{ __('Meus Pedidos') }}</x-responsive-nav-link>

                    {{-- Condicional Inteligente Mobile --}}
                    @if(Auth::user()->role === 'representative')
                        <x-responsive-nav-link :href="route('prices.representative')">{{ __('Tabela de Preços') }}</x-responsive-nav-link>
                    @else
                        <x-responsive-nav-link :href="route('prices.public')">{{ __('Tabela de Preços') }}</x-responsive-nav-link>
                    @endif

                    <x-responsive-nav-link :href="route('sheets.index')">{{ __('Fichas Técnicas') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('media.index')">{{ __('Drive de Mídia') }}</x-responsive-nav-link>
                @endif

                <div class="border-t border-gray-50 mt-4"></div>
                <x-responsive-nav-link :href="route('tickets.index')">{{ __('Suporte') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('Meu Perfil') }}</x-responsive-nav-link>
            </div>
            <div id="pwa-install-container" class="hidden px-4 py-3 border-t border-gray-100">
                <button id="pwa-install-btn" class="flex items-center gap-3 w-full text-left">
                    <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-[#B8860B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-gray-900">Instalar App</p>
                        <p class="text-[9px] text-gray-400 uppercase font-black">Acesso rápido à K'enzza</p>
                    </div>
                </button>
            </div>
            <div class="p-6 border-t border-gray-100 bg-gray-50">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left font-black uppercase text-[10px] tracking-widest text-red-600">
                        {{ __('Sair do Sistema') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
