<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-black text-2xl text-gray-900 uppercase tracking-tighter">
                Gerenciar <span class="text-[#c5a059]">Produtos</span>
            </h2>
            <div class="flex gap-4 w-full sm:w-auto">
                 <a href="{{ route('products.create') }}" class="w-full sm:w-auto text-center bg-black text-[#c5a059] px-6 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-[#c5a059] hover:text-black transition-all shadow-md border border-[#c5a059]">
                    + Novo Produto
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 font-bold rounded-2xl uppercase text-xs tracking-widest shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- LISTA DE PRODUTOS (HÍBRIDA: Tabela no PC / Cards no Celular) --}}
            <div class="bg-white shadow-sm sm:rounded-[2rem] border border-gray-100 overflow-hidden">

                {{-- Oculta o cabeçalho no celular --}}
                <table class="w-full text-left border-collapse">
                    <thead class="hidden lg:table-header-group bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest">Produto</th>
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-[#B8860B] tracking-widest text-center italic">Distribuidor (Base)</th>
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Salão (+Margem)</th>
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Consumidor (+Margem)</th>
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Estoque</th>
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Ações</th>
                        </tr>
                    </thead>

                    <tbody class="flex flex-col lg:table-row-group divide-y lg:divide-y-0 divide-gray-100 p-4 lg:p-0 space-y-4 lg:space-y-0">
                        @forelse($products as $product)
                            {{-- CORREÇÃO: Usando 'grid grid-cols-3' no mobile ao invés de flex, para evitar divs inválidos dentro do TR --}}
                            <tr class="grid grid-cols-3 gap-y-2 lg:table-row bg-white lg:hover:bg-gray-50/50 transition-all p-5 lg:p-0 rounded-3xl lg:rounded-none border lg:border-0 border-gray-100 lg:border-b shadow-sm lg:shadow-none group relative">

                                {{-- 1. Produto e Foto --}}
                                <td class="col-span-3 py-2 lg:py-5 px-2 lg:px-6 flex justify-start lg:table-cell items-center">
                                    <div class="flex items-center gap-4 w-full">
                                        <div class="w-16 h-16 lg:w-12 lg:h-12 rounded-2xl bg-gray-50 flex-shrink-0 flex items-center justify-center border border-gray-200 overflow-hidden shadow-inner">
                                            @if($product->imagem)
                                                <img src="{{ asset('storage/' . $product->imagem) }}" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-xs font-black text-gray-300">K</span>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-bold text-sm lg:text-base text-gray-900 leading-tight">{{ $product->nome }}</p>
                                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mt-1">SKU: {{ $product->sku ?? 'N/A' }}</p>

                                            {{-- Estoque visível no mobile direto no bloco principal --}}
                                            <div class="lg:hidden mt-2">
                                                <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase {{ $product->estoque > 0 ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-red-50 text-red-700 border border-red-100' }}">
                                                    Estoque: {{ $product->estoque }} UN
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- 2. Preço Base (Distribuidor) --}}
                                <td class="col-span-1 py-2 lg:py-5 px-2 lg:px-6 flex flex-col lg:table-cell items-center justify-center text-center border-t border-gray-50 pt-4 lg:border-0 lg:pt-0 mt-2 lg:mt-0">
                                    <span class="lg:hidden text-[8px] font-black text-[#B8860B] uppercase tracking-widest mb-1 italic">Distribuidor</span>
                                    <span class="text-xs lg:text-sm font-black text-[#B8860B] bg-[#c5a059]/10 lg:bg-transparent px-3 py-1 lg:p-0 rounded-lg">
                                        R$ {{ number_format($product->preco_distribuidor, 2, ',', '.') }}
                                    </span>
                                </td>

                                {{-- 3. Preço Salão --}}
                                <td class="col-span-1 py-2 lg:py-5 px-2 lg:px-6 flex flex-col lg:table-cell items-center justify-center text-center border-t border-gray-50 pt-4 lg:border-0 lg:pt-0 mt-2 lg:mt-0">
                                    <span class="lg:hidden text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Salão</span>
                                    <span class="text-xs lg:text-sm font-bold text-gray-600">
                                        R$ {{ number_format($product->preco_salao, 2, ',', '.') }}
                                    </span>
                                </td>

                                {{-- 4. Preço Consumidor --}}
                                <td class="col-span-1 py-2 lg:py-5 px-2 lg:px-6 flex flex-col lg:table-cell items-center justify-center text-center border-t border-gray-50 pt-4 lg:border-0 lg:pt-0 mt-2 lg:mt-0">
                                    <span class="lg:hidden text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Consumidor</span>
                                    <span class="text-xs lg:text-sm font-bold text-gray-900">
                                        R$ {{ number_format($product->preco_consumidor, 2, ',', '.') }}
                                    </span>
                                </td>

                                {{-- 5. Estoque (Oculto no Celular pois já mostramos acima) --}}
                                <td class="hidden lg:table-cell py-5 px-6 text-center">
                                    <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest border {{ $product->estoque > 0 ? 'bg-green-50 text-green-700 border-green-100' : 'bg-red-50 text-red-700 border-red-100' }}">
                                        {{ $product->estoque }} UN
                                    </span>
                                </td>

                                {{-- 6. Ações --}}
                                <td class="col-span-3 py-4 lg:py-5 px-2 lg:px-6 flex justify-end lg:table-cell items-center lg:text-center border-t border-gray-50 lg:border-0 mt-2 lg:mt-0">
                                    <div class="flex justify-end lg:justify-center gap-2 w-full lg:w-auto">

                                        {{-- Editar --}}
                                        <a href="{{ route('products.edit', $product->id) }}" class="flex-1 lg:flex-none flex items-center justify-center gap-2 bg-gray-50 lg:bg-transparent text-gray-700 lg:text-gray-400 hover:text-black hover:bg-gray-100 px-4 py-3 lg:p-2 rounded-xl lg:rounded-2xl transition-all shadow-sm lg:shadow-none font-black text-[10px] uppercase tracking-widest">
                                            <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span class="lg:hidden">Editar</span>
                                        </a>

                                        {{-- Excluir --}}
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este produto?');" class="flex-1 lg:flex-none">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-red-50 lg:bg-transparent text-red-500 lg:text-gray-400 hover:text-red-600 hover:bg-red-100 px-4 py-3 lg:p-2 rounded-xl lg:rounded-2xl transition-all shadow-sm lg:shadow-none font-black text-[10px] uppercase tracking-widest">
                                                <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span class="lg:hidden">Excluir</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="hidden lg:table-row">
                                <td colspan="6" class="py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                        </div>
                                        <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Nenhum produto cadastrado.</p>
                                    </div>
                                </td>
                            </tr>
                            {{-- Empty State (Mobile) --}}
                            <div class="lg:hidden bg-white rounded-[2rem] border-2 border-dashed border-gray-100 p-12 text-center flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                </div>
                                <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">Nenhum produto cadastrado.</p>
                            </div>
                        @endforelse
                    </tbody>
                </table>

                {{-- Paginação --}}
                <div class="px-6 py-5 border-t border-gray-100 bg-gray-50/50">
                    {{ $products->links() }}
                </div>
            </div>

            <div class="h-20"></div>
        </div>
    </div>
</x-app-layout>
