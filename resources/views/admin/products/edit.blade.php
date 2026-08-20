<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 uppercase tracking-tighter text-[#c5a059]">
            Editar Produto: {{ $product->nome }}
        </h2>
    </x-slot>

    {{-- Quill Assets --}}
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .ql-container { border-bottom-left-radius: 1.5rem; border-bottom-right-radius: 1.5rem; font-family: inherit; }
        .ql-toolbar { border-top-left-radius: 1.5rem; border-top-right-radius: 1.5rem; background: #f9fafb; border-color: #e5e7eb !important; }
        .ql-editor { min-height: 300px; font-size: 15px; color: #374151; }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-[2rem] border border-gray-100 overflow-hidden">

                <form id="edit-product-form" action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="p-8 lg:p-12">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                        {{-- Coluna Principal: Nome, EAN e Preços --}}
                        <div class="md:col-span-2 space-y-8">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Nome do Produto</label>
                                <input type="text" name="nome" value="{{ old('nome', $product->nome) }}" class="w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 focus:ring-[#c5a059] focus:border-[#c5a059] font-bold">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Código EAN (Barcode)</label>
                                    <input type="text" name="ean" value="{{ old('ean', $product->ean) }}" placeholder="Ex: 7891234567890" class="w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 focus:ring-[#c5a059] focus:border-[#c5a059] font-bold">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">SKU / Referência</label>
                                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 focus:ring-[#c5a059] focus:border-[#c5a059] font-bold uppercase">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Preço Distribuidor (Base)</label>
                                    <input type="text" name="preco_distribuidor" value="{{ old('preco_distribuidor', number_format($product->preco_distribuidor, 2, ',', '.')) }}" class="w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 focus:ring-[#c5a059] focus:border-[#c5a059] font-black text-[#B8860B]">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Estoque Atual</label>
                                    <input type="number" name="estoque" value="{{ old('estoque', $product->estoque) }}" class="w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 focus:ring-[#c5a059] focus:border-[#c5a059] font-bold">
                                </div>
                            </div>
                        </div>

                        {{-- Coluna Lateral: Categoria --}}
                        <div class="space-y-8">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Categoria</label>
                                <select name="category_id" class="w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 focus:ring-[#c5a059] focus:border-[#c5a059] font-bold">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->nome }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Preview de Imagem --}}
                            <div class="p-6 bg-gray-50 rounded-[2rem] border border-gray-100">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4">Imagem do Produto</label>
                                <div class="w-full aspect-square rounded-2xl overflow-hidden border-2 border-white shadow-sm mb-4">
                                    <img src="{{ asset('storage/' . $product->imagem) }}" class="w-full h-full object-cover">
                                </div>
                                <input type="file" name="imagem" class="text-[9px] text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[9px] file:font-black file:uppercase file:bg-black file:text-[#c5a059] hover:file:bg-[#c5a059] hover:file:text-black transition-all">
                            </div>
                        </div>

                        {{-- SEÇÃO DE LOGÍSTICA --}}
                        <div class="md:col-span-3 bg-gray-50/50 p-8 rounded-[2rem] border border-gray-100">
                            <div class="flex items-center gap-2 mb-6">
                                <svg class="w-4 h-4 text-[#c5a059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Logística e Medidas (Para Frete)</span>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                <div>
                                    <label class="block text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">Peso (kg)</label>
                                    <input type="text" name="peso" value="{{ old('peso', $product->peso) }}" placeholder="Ex: 0.500" class="w-full bg-white border-gray-100 rounded-xl px-4 py-3 text-sm focus:ring-[#c5a059]">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">Largura (cm)</label>
                                    <input type="text" name="largura" value="{{ old('largura', $product->largura) }}" placeholder="Ex: 20" class="w-full bg-white border-gray-100 rounded-xl px-4 py-3 text-sm focus:ring-[#c5a059]">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">Altura (cm)</label>
                                    <input type="text" name="altura" value="{{ old('altura', $product->altura) }}" placeholder="Ex: 10" class="w-full bg-white border-gray-100 rounded-xl px-4 py-3 text-sm focus:ring-[#c5a059]">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">Comprimento (cm)</label>
                                    <input type="text" name="comprimento" value="{{ old('comprimento', $product->comprimento) }}" placeholder="Ex: 30" class="w-full bg-white border-gray-100 rounded-xl px-4 py-3 text-sm focus:ring-[#c5a059]">
                                </div>
                            </div>
                        </div>

                        {{-- DESCRIÇÃO COM EDITOR --}}
                        <div class="md:col-span-3">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4">Descrição Detalhada</label>
                            <input type="hidden" name="descricao" id="descricao_real" value="{{ old('descricao', $product->descricao) }}">
                            <div class="bg-white border-gray-100">
                                <div id="editor-visual" style="height: 350px;">
                                    {!! old('descricao', $product->descricao) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 flex justify-end items-center gap-6">
                        <a href="{{ route('products.index') }}" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-black transition">Cancelar</a>
                        <button type="submit" class="bg-black text-[#c5a059] px-10 py-5 rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-[#c5a059] hover:text-black transition-all shadow-2xl">
                            Atualizar Produto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        var quill = new Quill('#editor-visual', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['clean']
                ]
            }
        });

        var form = document.getElementById('edit-product-form');
        var inputDescricao = document.getElementById('descricao_real');

        form.onsubmit = function() {
            inputDescricao.value = quill.root.innerHTML;
            return true;
        };
    </script>
</x-app-layout>
