<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-gray-900 uppercase tracking-tighter">
            Categorias de <span class="text-[#c5a059]">Produtos</span>
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

            {{-- Formulário de Cadastro (Coluna Menor) --}}
            <div class="lg:col-span-1 lg:sticky lg:top-8">
                <form action="{{ route('categories.store') }}" method="POST" class="bg-white p-8 shadow-sm rounded-[2rem] border border-gray-100">
                    @csrf
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 bg-black text-[#c5a059] rounded-xl flex items-center justify-center font-black">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                        <h3 class="font-black text-sm uppercase tracking-widest text-gray-900">Nova Categoria</h3>
                    </div>

                    <div class="mb-6">
                        <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-2 block mb-2">Nome da Categoria</label>
                        <input type="text" name="nome" required placeholder="Ex: Finalizadores..."
                               class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm focus:ring-2 focus:ring-[#c5a059] font-bold text-gray-700 placeholder-gray-400 shadow-inner">
                    </div>

                    <button type="submit" class="w-full flex items-center justify-center gap-2 bg-black text-[#c5a059] py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-[#c5a059] hover:text-black transition-all shadow-md border border-transparent hover:border-black">
                        Salvar Categoria
                    </button>
                </form>
            </div>

            {{-- Listagem (Coluna Maior) --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Cabeçalho da Lista (Desktop) --}}
                <div class="hidden lg:grid grid-cols-12 gap-4 px-6 pb-2 border-b border-gray-200">
                    <div class="col-span-7 text-[10px] font-black uppercase text-gray-400 tracking-widest">Nome</div>
                    <div class="col-span-3 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Produtos Vinculados</div>
                    <div class="col-span-2 text-[10px] font-black uppercase text-gray-400 tracking-widest text-right">Ação</div>
                </div>

                {{-- Cards de Categorias --}}
                @forelse($categories as $category)
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-5 bg-white rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 gap-4 group">

                        {{-- 1. Nome da Categoria --}}
                        <div class="flex items-center gap-4 w-full sm:w-1/2 lg:w-7/12">
                            <div class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center border border-gray-100 flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                            </div>
                            <h4 class="font-bold text-gray-900 text-base leading-tight">{{ $category->nome }}</h4>
                        </div>

                        {{-- 2. Contagem de Produtos e Ação (Alinhados à direita/centro) --}}
                        <div class="flex items-center justify-between sm:justify-end w-full sm:w-1/2 lg:w-5/12 gap-6 border-t border-gray-50 sm:border-0 pt-4 sm:pt-0">

                            {{-- Contagem --}}
                            <div class="flex flex-col sm:items-center w-full sm:w-auto">
                                <span class="sm:hidden text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Vinculados</span>
                                <span class="bg-gray-100 text-gray-600 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border border-gray-200">
                                    {{ $category->products_count }} {{ Str::plural('Item', $category->products_count) }}
                                </span>
                            </div>

                            {{-- Botão Excluir --}}
                            <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta categoria? Os produtos vinculados a ela ficarão sem categoria.')" class="flex-shrink-0">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-10 h-10 flex items-center justify-center bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm" title="Excluir Categoria">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>

                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-[2rem] border-2 border-dashed border-gray-200 p-12 text-center flex flex-col items-center justify-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        </div>
                        <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">Nenhuma categoria cadastrada.</p>
                    </div>
                @endforelse

            </div>

        </div>
    </div>
</x-app-layout>
