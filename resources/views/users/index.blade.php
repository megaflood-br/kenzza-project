<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-900 leading-tight uppercase tracking-tighter">
                {{ __('Gerenciar Usuários') }}
            </h2>
            <a href="{{ route('users.create') }}" class="bg-black text-[#c5a059] px-6 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-[#c5a059] hover:text-black transition-all shadow-md border border-[#c5a059]">
                Novo Usuário
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- BARRA DE BUSCA E FILTROS PREMIUM --}}
            <div class="bg-white p-6 md:p-8 rounded-[2rem] border border-gray-100 shadow-sm mb-6">
                <form action="{{ route('users.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">

                    {{-- Campo de Busca --}}
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Buscar Nome ou E-mail..."
                               class="w-full bg-gray-50 border-none rounded-2xl py-4 pl-12 pr-4 text-sm focus:ring-2 focus:ring-[#c5a059] font-bold text-gray-700 placeholder-gray-400 shadow-inner">
                    </div>

                    {{-- Filtro de Nível (Role) CORRIGIDO COM TODOS OS CARGOS --}}
                    <div class="w-full md:w-56 relative">
                       <select name="role" class="w-full bg-gray-50 border-none rounded-2xl py-4 pl-4 pr-10 text-sm focus:ring-2 focus:ring-[#c5a059] font-bold text-gray-700 shadow-inner cursor-pointer appearance-none">
                            <option value="">Todos os Níveis</option>
                            <option value="admin" {{ ($role ?? '') == 'admin' ? 'selected' : '' }}>Administrador</option>
                            <option value="manager" {{ ($role ?? '') == 'manager' ? 'selected' : '' }}>Gerente Comercial</option>
                            <option value="editor" {{ ($role ?? '') == 'editor' ? 'selected' : '' }}>Editor</option>
                            <option value="distributor" {{ ($role ?? '') == 'distributor' ? 'selected' : '' }}>Distribuidor</option>
                            <option value="representative" {{ ($role ?? '') == 'representative' ? 'selected' : '' }}>Representante</option> {{-- Adicionado --}}
                            <option value="salon" {{ ($role ?? '') == 'salon' ? 'selected' : '' }}>Salão / Profissional</option>
                            <option value="consumer" {{ ($role ?? '') == 'consumer' ? 'selected' : '' }}>Consumidor</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>

                    {{-- Botões de Ação --}}
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 md:flex-none bg-black text-[#c5a059] px-8 py-4 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-[#c5a059] hover:text-black transition-all shadow-md">
                            Filtrar
                        </button>
                        @if(!empty($search) || !empty($role))
                            <a href="{{ route('users.index') }}" class="bg-gray-100 text-gray-400 px-6 py-4 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-red-50 hover:text-red-500 transition-all flex items-center justify-center">
                                Limpar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- CONTADOR DE RESULTADOS --}}
            <div class="mb-6 flex items-center">
                <span class="bg-[#c5a059] text-black text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded-full shadow-sm">
                    {{ $users->count() }} Usuário(s) {{ (!empty($search) || !empty($role)) ? 'encontrado(s)' : 'no total' }}
                </span>
            </div>

            {{-- LISTA DE USUÁRIOS EM CARTÕES --}}
            <div class="space-y-4">
                @forelse ($users as $user)
                    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between p-6 bg-white rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 gap-6 group">

                        {{-- Avatar e Informações (abre o perfil) --}}
                        <a href="{{ route('users.show', $user) }}" class="flex items-center gap-5 w-full lg:w-auto min-w-0 group/profile">
                            <div class="w-14 h-14 rounded-full bg-black text-[#c5a059] flex items-center justify-center font-black text-xl uppercase shadow-inner flex-shrink-0">
                                {{ mb_substr($user->name, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-1">
                                    <h4 class="font-bold text-gray-900 text-lg leading-tight group-hover/profile:text-[#c5a059] transition-colors">{{ $user->name }}</h4>

                                    {{-- BADGE DINÂMICA INTELIGENTE (PHP 8.0+) --}}
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest
                                        {{ match($user->role) {
                                            'admin' => 'bg-black text-[#c5a059]',
                                            'manager' => 'bg-purple-100 text-purple-700',
                                            'editor' => 'bg-blue-100 text-blue-700',
                                            'distributor' => 'bg-gray-200 text-gray-800',
                                            'representative' => 'bg-amber-100 text-amber-800', {{-- Cor para o Representante --}}
                                            'salon' => 'bg-pink-100 text-pink-700',
                                            default => 'bg-[#c5a059]/20 text-[#c5a059]'
                                        } }}">
                                        {{ match($user->role) {
                                            'admin' => 'Administrador',
                                            'manager' => 'Gerente Comercial',
                                            'editor' => 'Editor',
                                            'distributor' => 'Distribuidor',
                                            'representative' => 'Representante', {{-- Texto para o Representante --}}
                                            'salon' => 'Salão / Profissional',
                                            default => 'Consumidor'
                                        } }}
                                    </span>
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-center text-xs text-gray-500 mt-1 gap-1 sm:gap-2">
                                    <span class="font-bold text-gray-700">{{ $user->email }}</span>
                                    <span class="hidden sm:inline text-gray-300">•</span>
                                    <span class="text-[10px] uppercase tracking-wider font-bold text-gray-400 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ $user->created_at->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                            </div>
                        </a>

                        {{-- Ações (Perfil, Editar, Excluir, Sua Conta) --}}
                        <div class="flex items-center gap-3 w-full lg:w-auto pt-4 lg:pt-0 border-t lg:border-0 border-gray-50">

                            <a href="{{ route('users.show', $user) }}" class="flex-1 lg:flex-none flex items-center justify-center gap-2 bg-black text-[#c5a059] px-6 py-3 h-12 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-[#c5a059] hover:text-black transition-all shadow-sm">
                                Perfil
                            </a>

                            {{-- Botão Editar --}}
                            <a href="{{ route('users.edit', $user->id) }}" class="flex-1 lg:flex-none flex items-center justify-center gap-2 bg-gray-50 text-gray-900 px-6 py-3 h-12 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-black hover:text-[#c5a059] transition-all shadow-sm">
                                Editar
                            </a>

                            {{-- Verificação: Não deixar excluir a própria conta --}}
                            @if(auth()->id() !== $user->id)
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Deseja realmente excluir este usuário?')" class="flex-shrink-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-12 h-12 flex items-center justify-center bg-red-50 text-red-500 rounded-2xl hover:bg-red-500 hover:text-white transition-all shadow-sm" title="Excluir Usuário">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            @else
                                {{-- Selo "Sua Conta" --}}
                                <div class="w-12 h-12 flex items-center justify-center bg-blue-50 text-blue-500 rounded-2xl shadow-sm" title="Esta é a sua conta">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    {{-- Empty State (Nenhum Resultado) --}}
                    <div class="bg-white rounded-[2rem] border-2 border-dashed border-gray-200 p-16 text-center flex flex-col items-center justify-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Nenhum usuário encontrado para esta busca.</p>
                    </div>
                @endforelse
            </div>

            <div class="h-20"></div>
        </div>
    </div>
</x-app-layout>
