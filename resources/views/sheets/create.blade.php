<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nova Ficha Técnica') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-200">
                <div class="p-8">
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg shadow-sm">
                            <p class="font-bold mb-2">Ops! Verifique os campos abaixo:</p>
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('sheets.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="nome" value="Nome do Produto *" />
                                <x-text-input id="nome" name="nome" type="text" class="mt-1 block w-full" value="{{ old('nome') }}" placeholder="Ex: Progressiva K'enzza" required />
                            </div>
                            <div>
                                <x-input-label for="foto" value="Foto (JPG/PNG) *" />
                                <input type="file" name="foto" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 transition cursor-pointer">
                            </div>
                            <div>
                                <x-input-label for="pdf" value="Ficha em PDF *" />
                                <input type="file" name="pdf" accept=".pdf" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 transition cursor-pointer">
                            </div>
                        </div>

                        <div>
                            <x-input-label for="descricao" value="Descrição Geral *" />
                            <textarea name="descricao" rows="3" required class="mt-1 block w-full border-gray-300 focus:border-[#c5a059] focus:ring-[#c5a059] rounded-xl shadow-sm">{{ old('descricao') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="ativos_tecnologia" value="Ativos e Tecnologia *" />
                                <textarea name="ativos_tecnologia" rows="3" required class="mt-1 block w-full border-gray-300 focus:border-[#c5a059] focus:ring-[#c5a059] rounded-xl shadow-sm">{{ old('ativos_tecnologia') }}</textarea>
                            </div>
                            <div>
                                <x-input-label for="funcoes" value="Funções *" />
                                <textarea name="funcoes" rows="3" required class="mt-1 block w-full border-gray-300 focus:border-[#c5a059] focus:ring-[#c5a059] rounded-xl shadow-sm">{{ old('funcoes') }}</textarea>
                            </div>
                        </div>

                        {{-- CORREÇÃO: Adicionado Diferenciais ao lado de Modo de Usar --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="diferenciais" value="Diferenciais *" />
                                <textarea name="diferenciais" rows="3" required class="mt-1 block w-full border-gray-300 focus:border-[#c5a059] focus:ring-[#c5a059] rounded-xl shadow-sm">{{ old('diferenciais') }}</textarea>
                            </div>
                            <div>
                                <x-input-label for="modo_usar" value="Modo de Usar *" />
                                <textarea name="modo_usar" rows="3" required class="mt-1 block w-full border-gray-300 focus:border-[#c5a059] focus:ring-[#c5a059] rounded-xl shadow-sm">{{ old('modo_usar') }}</textarea>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="dados_analiticos" value="Dados Analíticos *" />
                                <textarea name="dados_analiticos" rows="3" required class="mt-1 block w-full border-gray-300 focus:border-[#c5a059] focus:ring-[#c5a059] rounded-xl shadow-sm">{{ old('dados_analiticos') }}</textarea>
                            </div>
                            <div>
                                <x-input-label for="seguranca" value="Segurança *" />
                                <textarea name="seguranca" rows="3" required class="mt-1 block w-full border-gray-300 focus:border-[#c5a059] focus:ring-[#c5a059] rounded-xl shadow-sm">{{ old('seguranca') }}</textarea>
                            </div>
                        </div>

                        <div class="flex justify-end items-center pt-8 border-t border-gray-100 gap-8">
                            <a href="{{ route('sheets.index') }}" class="text-sm font-bold text-gray-400 uppercase tracking-widest hover:text-red-500 transition">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-[#c5a059] text-black px-10 py-4 rounded-lg font-extrabold uppercase tracking-widest text-xs hover:bg-black hover:text-[#c5a059] transition-all duration-300 shadow-lg border border-[#c5a059]">
                                Salvar Ficha Técnica
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
