<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gerenciar Banners') }}
            </h2>
            <a href="{{ route('banners.create') }}" class="bg-black text-white px-6 py-2 rounded-full text-xs font-bold hover:bg-indigo-600 transition shadow-sm">
                + Novo Banner
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 text-green-700 rounded-xl shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6">
                @forelse($banners as $banner)
                    <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-3xl p-6 flex flex-col md:flex-row items-center gap-6">

                        <div class="w-full md:w-48 h-24 bg-gray-100 rounded-2xl overflow-hidden border border-gray-50">
                            <img src="{{ asset('storage/' . $banner->imagem_desktop) }}" class="w-full h-full object-cover" title="Banner PC">
                        </div>

                        <div class="w-20 h-24 bg-gray-100 rounded-2xl overflow-hidden border border-gray-50 hidden md:block">
                            <img src="{{ asset('storage/' . $banner->imagem_mobile) }}" class="w-full h-full object-cover" title="Banner Mobile">
                        </div>

                        <div class="flex-1 text-center md:text-left">
                            <h3 class="text-lg font-bold text-gray-900">{{ $banner->titulo ?? 'Banner sem título' }}</h3>
                            <div class="flex flex-wrap justify-center md:justify-start gap-3 mt-1">
                                <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-1 rounded-md font-bold uppercase tracking-widest">Ordem: {{ $banner->ordem }}</span>
                                @if($banner->ativo)
                                    <span class="text-[10px] bg-green-100 text-green-600 px-2 py-1 rounded-md font-bold uppercase tracking-widest">Ativo</span>
                                @endif
                            </div>
                            @if($banner->link)
                                <p class="text-xs text-indigo-500 truncate max-w-xs mt-2 font-medium italic">{{ $banner->link }}</p>
                            @endif
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('banners.edit', $banner->id) }}" class="p-3 bg-gray-50 text-gray-500 rounded-2xl hover:bg-black hover:text-white transition-all shadow-sm group">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                            </a>

                            <form action="{{ route('banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover este banner?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-3 bg-red-50 text-red-500 rounded-2xl hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-1.8c0-.662-.538-1.2-1.2-1.2h-3.6c-.662 0-1.2.538-1.2 1.2v1.8m7.5 0a48.112 48.112 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="bg-white border-2 border-dashed border-gray-100 rounded-3xl p-20 text-center">
                        <p class="text-gray-400 font-light text-lg italic">Nenhum banner cadastrado no momento.</p>
                        <a href="{{ route('banners.create') }}" class="text-indigo-500 font-bold mt-2 inline-block hover:underline">Clique aqui para criar o primeiro</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
