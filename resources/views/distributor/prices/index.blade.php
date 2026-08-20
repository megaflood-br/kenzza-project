<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-xl text-gray-800 uppercase tracking-tighter">Minha Tabela de Preços</h2>
            <div class="flex gap-2">
                <a href="{{ route('prices.pdf', ['categoria' => $selectedCategory]) }}" class="bg-red-600 text-white px-4 py-2 rounded-xl font-black uppercase text-[9px] tracking-widest hover:bg-red-700 transition">PDF</a>
                <a href="{{ route('prices.xls') }}" class="bg-green-600 text-white px-4 py-2 rounded-xl font-black uppercase text-[9px] tracking-widest hover:bg-green-700 transition">Excel</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

           {{-- Badge de Nível Personalizado --}}
<div class="mb-12 flex justify-center">
    <div class="relative group">
        {{-- Efeito de brilho dinâmico baseado no nível --}}
        @php
            $bgGradient = match(auth()->user()->tier) {
                'Diamante' => 'from-blue-600 to-cyan-400',
                'Gold'     => 'from-yellow-600 to-orange-400',
                'Black'    => 'from-gray-900 to-gray-600',
                default    => 'from-gray-400 to-gray-200'
            };

            $textColor = match(auth()->user()->tier) {
                'Diamante' => 'text-blue-600',
                'Gold'     => 'text-[#c5a059]',
                'Black'    => 'text-gray-900',
                default    => 'text-gray-500'
            };

            $iconColor = match(auth()->user()->tier) {
                'Diamante' => 'text-blue-500',
                'Gold'     => 'text-yellow-500',
                'Black'    => 'text-black',
                default    => 'text-gray-400'
            };
        @endphp

        <div class="absolute -inset-1 bg-gradient-to-r {{ $bgGradient }} rounded-[2rem] blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>

        <div class="relative bg-white border border-gray-100 px-12 py-6 rounded-[2rem] shadow-sm flex flex-col items-center">
            <span class="text-[10px] font-black uppercase text-gray-400 tracking-[0.3em] mb-1">Seu Nível Atual</span>

            <div class="flex items-center gap-3">
                {{-- Ícone Dinâmico --}}
                @if(auth()->user()->tier === 'Diamante')
                    <svg class="w-6 h-6 {{ $iconColor }}" fill="currentColor" viewBox="0 0 20 20"><path d="M11 3a1 1 0 10-2 0h2zM4.503 5.341a1 1 0 00-1.342.373l-.001.002a1 1 0 00.372 1.342l1.342-.373a1 1 0 00-.371-1.344zM16.84 5.714a1 1 0 111.342.371l-.371 1.342a1 1 0 11-1.342-.372l.371-1.341zM9 9a1 1 0 000 2h1a1 1 0 100-2H9z" /><path fill-rule="evenodd" d="M12.828 3.172a.5.5 0 00-.707 0L10.293 5.029l-1.83-1.83a.5.5 0 10-.707.707l2.122 2.121a.5.5 0 00.707 0l2.121-2.121a.5.5 0 000-.707zM2.5 11a.5.5 0 000 1h15a.5.5 0 100-1H2.5z" clip-rule="evenodd" /></svg>
                @elseif(auth()->user()->tier === 'Gold')
                    <svg class="w-6 h-6 {{ $iconColor }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                @else
                    <svg class="w-6 h-6 {{ $iconColor }}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 4.946-2.397 9.332-6 11.545-3.603-2.213-6-6.599-6-11.545 0-.68.056-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                @endif

                <h3 class="text-3xl font-black uppercase tracking-tighter {{ $textColor }}">
                    Nível {{ auth()->user()->tier }}
                </h3>
            </div>
        </div>
    </div>
</div>
{{-- Abaixo do Badge de Nível e acima das Tabelas --}}
<div class="mb-8 flex flex-wrap justify-center gap-3 px-4">
    {{-- Botão "Todos" --}}
    <a href="{{ route('prices.index') }}"
       class="px-6 py-2 rounded-full text-[10px] font-black uppercase tracking-widest transition-all
       {{ !$selectedCategory ? 'bg-black text-[#c5a059] shadow-lg' : 'bg-white text-gray-400 border border-gray-100 hover:bg-gray-50' }}">
        Todos
    </a>

    @foreach($allCategories as $cat)
        <a href="{{ route('prices.index', ['categoria' => $cat->id]) }}"
           class="px-6 py-2 rounded-full text-[10px] font-black uppercase tracking-widest transition-all
           {{ $selectedCategory == $cat->id ? 'bg-black text-[#c5a059] shadow-lg' : 'bg-white text-gray-400 border border-gray-100 hover:bg-gray-50' }}">
            {{ $cat->nome }}
        </a>
    @endforeach
</div>

{{-- Listagem das Tabelas (O seu código de loop @foreach($categories...) continua aqui abaixo) --}}
            {{-- Listagem por Categorias --}}
            @foreach($categories as $category)
                @if($category->products->count() > 0)
                    <div class="mb-10">
                        {{-- Título da Categoria --}}
                        <div class="flex items-center gap-4 mb-4 px-4">
                            <h4 class="text-[11px] font-black uppercase text-[#c5a059] tracking-[0.4em] whitespace-nowrap">
                                {{ $category->nome }}
                            </h4>
                            <div class="h-[1px] w-full bg-gray-100"></div>
                        </div>

                        <div class="bg-white shadow-xl sm:rounded-[2rem] overflow-hidden border border-gray-100">
                            <div class="p-8">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="border-b border-gray-100">
                                            <th class="py-4 px-4 text-[10px] font-black uppercase text-gray-400 tracking-widest">Produto</th>
                                            <th class="py-4 px-4 text-[10px] font-black uppercase text-gray-400 tracking-widest text-right">Seu Preço Unitário</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($category->products as $product)
                                            <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                                                <td class="py-4 px-4 flex items-center gap-3">
                                                    <div class="w-12 h-12 rounded-xl bg-gray-100 overflow-hidden border border-gray-100">
                                                        @if($product->imagem)
                                                            <img src="{{ asset('storage/' . $product->imagem) }}" class="w-full h-full object-cover">
                                                        @else
                                                            <div class="w-full h-full flex items-center justify-center font-black text-[10px] text-gray-300">K</div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <span class="font-bold text-sm text-gray-900 block leading-tight">{{ $product->nome }}</span>
                                                        <span class="text-[9px] text-gray-400 font-black uppercase tracking-widest">{{ $product->sku ?? 'SEM SKU' }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-4 px-4 text-right">
                                                    <span class="font-black text-sm text-black">R$ {{ number_format($product->preco_atual, 2, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
@if($productsWithoutCategory->count() > 0)
    <div class="mb-10">
        <div class="flex items-center gap-4 mb-4 px-4">
            <h4 class="text-[11px] font-black uppercase text-gray-400 tracking-[0.4em] whitespace-nowrap">
                Outros Produtos (Sem Categoria)
            </h4>
            <div class="h-[1px] w-full bg-gray-100"></div>
        </div>

        <div class="bg-white shadow-xl sm:rounded-[2rem] overflow-hidden border border-gray-100">
            <div class="p-8">
                <table class="w-full text-left">
                    <tbody>
                        @foreach($productsWithoutCategory as $product)
                            <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                                <td class="py-4 px-4 flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-gray-100 overflow-hidden border border-gray-100">
                                        @if($product->imagem)
                                            <img src="{{ asset('storage/' . $product->imagem) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center font-black text-[10px] text-gray-300">K</div>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-bold text-sm text-gray-900 block leading-tight">{{ $product->nome }}</span>
                                        <span class="text-[9px] text-gray-400 font-black uppercase tracking-widest">{{ $product->sku ?? 'SEM SKU' }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <span class="font-black text-sm text-black">R$ {{ number_format($product->preco_atual, 2, ',', '.') }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
            @if($categories->isEmpty())
                <div class="text-center py-20">
                    <p class="text-gray-400 font-black uppercase text-[10px] tracking-widest italic">Nenhum produto disponível no momento.</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
