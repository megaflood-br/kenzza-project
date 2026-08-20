<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 uppercase tracking-tighter text-[#c5a059]">Novo Produto K'enzza</h2>
    </x-slot>

    {{-- Quill Assets --}}
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .ql-container { border-bottom-left-radius: 2rem; border-bottom-right-radius: 2rem; font-family: inherit; }
        .ql-toolbar { border-top-left-radius: 2rem; border-top-right-radius: 2rem; background: #f9fafb; border-color: #f3f4f6 !important; }
        .ql-editor { min-height: 250px; font-size: 15px; color: #374151; }
    </style>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form id="create-product-form" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-10 shadow-2xl rounded-[3rem] border border-gray-100">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                    {{-- Informações Principais --}}
                    <div class="md:col-span-2 space-y-6">
                        <div>
                            <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-2">Nome do Produto</label>
                            <input type="text" name="nome" value="{{ old('nome') }}" required placeholder="Ex: Kit Reconstrução Capilar"
                                   class="w-full mt-2 bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 focus:ring-[#c5a059] focus:border-[#c5a059] font-bold">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-2">Código EAN (Barcode)</label>
                                <input type="text" name="ean" value="{{ old('ean') }}" placeholder="Ex: 7891234567890"
                                       class="w-full mt-2 bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 focus:ring-[#c5a059] focus:border-[#c5a059] font-bold">
                            </div>
                            <div>
                                <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-2">SKU / Referência</label>
                                <input type="text" name="sku" value="{{ old('sku') }}" placeholder="Ex: KEN-001"
                                       class="w-full mt-2 bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 focus:ring-[#c5a059] focus:border-[#c5a059] font-bold uppercase">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            {{-- Preço Base --}}
                            <div class="p-4 bg-[#B8860B]/5 rounded-3xl border border-[#B8860B]/10">
                                <label class="text-[9px] font-black uppercase text-[#B8860B] tracking-widest mb-1 block">Preço Distribuidor (BASE)</label>
                                <div class="flex items-center">
                                    <span class="font-black text-[#B8860B] mr-1 text-sm">R$</span>
                                    <input type="number" step="0.01" name="preco_distribuidor" value="{{ old('preco_distribuidor') }}" required placeholder="0.00"
                                           class="w-full border-none bg-transparent focus:ring-0 font-black text-xl p-0 text-gray-900">
                                </div>
                            </div>
                            {{-- Estoque --}}
                            <div class="p-4 bg-gray-50 rounded-3xl border border-gray-100">
                                <label class="text-[9px] font-black uppercase text-gray-400 tracking-widest block mb-1">Estoque Inicial</label>
                                <input type="number" name="estoque" value="{{ old('estoque', 0) }}" required
                                       class="w-full border-none bg-transparent p-0 focus:ring-0 font-black text-xl">
                            </div>
                        </div>
                    </div>

                    {{-- Coluna Lateral: Categoria e Foto --}}
                    <div class="space-y-6">
                        <div>
                            <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-2">Categoria</label>
                            <select name="category_id" required class="w-full mt-2 bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 focus:ring-[#c5a059] focus:border-[#c5a059] font-bold appearance-none">
                                <option value="">Selecione...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-2">Foto do Produto</label>
                            <div class="mt-2">
                                <label class="flex flex-col items-center justify-center w-full h-44 border-2 border-gray-100 border-dashed rounded-[2.5rem] cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all group">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-6 h-6 mb-2 text-gray-300 group-hover:text-[#c5a059] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Upload Imagem</p>
                                    </div>
                                    <input type="file" name="imagem" class="hidden" accept="image/*" required />
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Logística e Medidas --}}
                    <div class="md:col-span-3 grid grid-cols-2 md:grid-cols-4 gap-6 p-8 bg-gray-50/50 rounded-[2.5rem] border border-gray-100">
                        <div class="col-span-2 md:col-span-4 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#c5a059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Logística e Medidas (Frete)</label>
                        </div>

                        <div>
                            <label class="text-[9px] font-black uppercase text-gray-400 tracking-widest px-2 block mb-1">Peso (Kg)</label>
                            <input type="number" step="0.001" name="peso" value="{{ old('peso', '0.300') }}" class="w-full bg-white border-gray-100 rounded-xl px-4 py-3 font-bold text-sm focus:ring-[#c5a059]">
                        </div>
                        <div>
                            <label class="text-[9px] font-black uppercase text-gray-400 tracking-widest px-2 block mb-1">Largura (cm)</label>
                            <input type="number" name="largura" value="{{ old('largura', '11') }}" class="w-full bg-white border-gray-100 rounded-xl px-4 py-3 font-bold text-sm focus:ring-[#c5a059]">
                        </div>
                        <div>
                            <label class="text-[9px] font-black uppercase text-gray-400 tracking-widest px-2 block mb-1">Altura (cm)</label>
                            <input type="number" name="altura" value="{{ old('altura', '17') }}" class="w-full bg-white border-gray-100 rounded-xl px-4 py-3 font-bold text-sm focus:ring-[#c5a059]">
                        </div>
                        <div>
                            <label class="text-[9px] font-black uppercase text-gray-400 tracking-widest px-2 block mb-1">Comprimento (cm)</label>
                            <input type="number" name="comprimento" value="{{ old('comprimento', '11') }}" class="w-full bg-white border-gray-100 rounded-xl px-4 py-3 font-bold text-sm focus:ring-[#c5a059]">
                        </div>
                    </div>

                    {{-- Descrição Detalhada com Quill --}}
                    <div class="md:col-span-3">
                        <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-2 mb-4 block">Descrição Detalhada do Produto</label>
                        <input type="hidden" name="descricao" id="descricao_real" value="{{ old('descricao') }}">
                        <div class="bg-white border border-gray-100 rounded-[2rem] overflow-hidden">
                            <div id="editor-visual">
                                {!! old('descricao') !!}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Botões de Ação --}}
                <div class="mt-12 flex justify-end items-center gap-6">
                    @if ($errors->any())
                        <div class="text-red-500 text-[9px] font-black uppercase tracking-widest">
                            Verifique os campos obrigatórios
                        </div>
                    @endif
                    <a href="{{ route('products.index') }}" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-black transition">Cancelar</a>
                    <button type="submit" class="bg-black text-[#c5a059] px-12 py-5 rounded-2xl font-black uppercase text-xs tracking-widest shadow-2xl hover:bg-[#c5a059] hover:text-black transition-all">
                        Cadastrar Produto
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPTS DO EDITOR --}}
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

        var form = document.getElementById('create-product-form');
        var inputDescricao = document.getElementById('descricao_real');

        form.onsubmit = function() {
            inputDescricao.value = quill.root.innerHTML;
            return true;
        };
    </script>
</x-app-layout>
