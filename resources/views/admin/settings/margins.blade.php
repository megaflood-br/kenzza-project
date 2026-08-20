<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 uppercase tracking-tighter">Configurar Margens de Lucro</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-100">
                <form action="{{ route('admin.settings.margins.update') }}" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Margem Salão (Sobre Distribuidor %)</label>
                            <input type="number" name="margin_salon" value="{{ $margins['margin_salon'] }}" class="w-full border-gray-200 rounded-full px-5 py-3 font-bold">
                            <p class="text-[9px] text-gray-400 mt-1 italic">Ex: 120 adicionará 120% ao valor base.</p>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Margem Consumidor (Sobre Distribuidor %)</label>
                            <input type="number" name="margin_consumer" value="{{ $margins['margin_consumer'] }}" class="w-full border-gray-200 rounded-full px-5 py-3 font-bold">
                        </div>

                        <button type="submit" class="w-full bg-black text-[#B8860B] py-4 rounded-full font-black uppercase tracking-widest hover:bg-[#B8860B] hover:text-black transition-all">
                            Salvar Configurações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
