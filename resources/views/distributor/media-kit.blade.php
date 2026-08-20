<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-xl text-gray-800 uppercase tracking-tighter">
                {{ __('Kits de Divulgação & Mídia') }}
            </h2>
            <p class="text-[10px] text-[#c5a059] font-bold uppercase tracking-widest">Materiais Exclusivos</p>
        </div>
    </x-slot>

    <div class="py-12 bg-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex flex-wrap gap-2 mb-10 border-b border-gray-100 pb-6">
                <a href="{{ route('media.index') }}"
                   class="px-5 py-2 rounded-full text-[10px] font-black uppercase tracking-widest transition-all {{ !request('categoria') ? 'bg-black text-[#c5a059]' : 'bg-gray-100 text-gray-400 hover:bg-gray-200' }}">
                    Todos
                </a>

                @foreach($categoriasDisponiveis as $cat)
                    <a href="{{ route('media.index', ['categoria' => $cat]) }}"
                       class="px-5 py-2 rounded-full text-[10px] font-black uppercase tracking-widest transition-all {{ request('categoria') === $cat ? 'bg-black text-[#c5a059]' : 'bg-gray-100 text-gray-400 hover:bg-gray-200' }}">
                        {{ $cat }}
                    </a>
                @endforeach
            </div>

            @forelse($medias as $categoria => $itens)
                <div class="mb-12">
                    <h3 class="text-xs font-black uppercase tracking-[0.3em] text-gray-400 mb-6 flex items-center gap-4">
                        {{ $categoria }}
                        <div class="h-[1px] bg-gray-100 flex-1"></div>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach($itens as $media)
                            <div class="group bg-[#fcfcfc] rounded-3xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-500">
                                <div class="aspect-square bg-white rounded-2xl mb-4 flex items-center justify-center border border-gray-50 overflow-hidden relative">
                                    @if(in_array(strtolower($media->tipo), ['jpg', 'jpeg', 'png', 'webp']))
                                        <img src="{{ Storage::url($media->arquivo_path) }}" class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-700">
                                    @else
                                        <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    @endif
                                </div>

                                <h4 class="text-sm font-bold text-gray-900 mb-1 truncate">{{ $media->titulo }}</h4>
                                <p class="text-[9px] text-gray-400 uppercase font-black mb-4">{{ strtoupper($media->tipo) }}</p>

                                <a href="{{ Storage::url($media->arquivo_path) }}" download class="block w-full bg-black text-white text-center py-3 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-[#c5a059] hover:text-black transition-all">
                                    Download
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-20 bg-gray-50 rounded-[3rem] border-2 border-dashed border-gray-100">
                    <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">
                        {{ request('categoria') ? 'Nenhum material encontrado nesta categoria.' : 'Aguardando upload de materiais...' }}
                    </p>
                </div>
            @endforelse

        </div>
    </div>
</x-app-layout>
