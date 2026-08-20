<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Banner: {{ $banner->titulo }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Título</label>
                                <input type="text" name="titulo" value="{{ $banner->titulo }}" class="w-full border-gray-100 bg-gray-50 rounded-xl focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Link</label>
                                <input type="url" name="link" value="{{ $banner->link }}" class="w-full border-gray-100 bg-gray-50 rounded-xl">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Ordem</label>
                                <input type="number" name="ordem" value="{{ $banner->ordem }}" class="w-full border-gray-100 bg-gray-50 rounded-xl">
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-xs font-bold text-indigo-400 mb-2">Alterar Banner PC</label>
                                <img src="{{ asset('storage/' . $banner->imagem_desktop) }}" class="w-full h-20 object-cover rounded-xl mb-2 border">
                                <input type="file" name="imagem_desktop" class="text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-purple-400 mb-2">Alterar Banner Celular</label>
                                <img src="{{ asset('storage/' . $banner->imagem_mobile) }}" class="w-full h-20 object-cover rounded-xl mb-2 border">
                                <input type="file" name="imagem_mobile" class="text-xs">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-between items-center">
                        <a href="{{ route('banners.index') }}" class="text-gray-400 hover:text-gray-600 font-bold text-sm">Cancelar</a>
                        <button type="submit" class="bg-black text-white px-10 py-3 rounded-full font-bold hover:bg-indigo-600 transition shadow-lg">
                            Atualizar Banner
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
