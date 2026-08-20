<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Novo Banner K'enzza</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Título (Opcional)</label>
                                <input type="text" name="titulo" class="w-full border-gray-100 bg-gray-50 rounded-xl focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Link de Destino</label>
                                <input type="url" name="link" placeholder="https://..." class="w-full border-gray-100 bg-gray-50 rounded-xl">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Ordem de Exibição</label>
                                <input type="number" name="ordem" value="1" class="w-full border-gray-100 bg-gray-50 rounded-xl">
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="p-4 bg-indigo-50 rounded-2xl border border-dashed border-indigo-200">
                                <label class="block text-xs font-bold text-indigo-400 mb-2">Banner PC (1920x800px)</label>
                                <input type="file" name="imagem_desktop" required class="text-xs">
                            </div>
                            <div class="p-4 bg-purple-50 rounded-2xl border border-dashed border-purple-200">
                                <label class="block text-xs font-bold text-purple-400 mb-2">Banner Celular (800x800px)</label>
                                <input type="file" name="imagem_mobile" required class="text-xs">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit" class="bg-black text-white px-10 py-3 rounded-full font-bold hover:bg-indigo-600 transition shadow-lg">
                            Salvar Banner
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
