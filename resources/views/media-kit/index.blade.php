<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-xl text-gray-800 uppercase tracking-tighter">Gerenciar Drive de Mídia</h2>
            <p class="text-[10px] text-[#c5a059] font-bold uppercase tracking-widest">Painel Administrativo</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <div class="bg-black rounded-[2.5rem] p-8 shadow-2xl border border-[#c5a059]/20">
                <h3 class="text-[#c5a059] font-black uppercase text-xs tracking-widest mb-6">Upload de Novo Material</h3>
                <form action="{{ route('media.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    @csrf
                    <div class="md:col-span-1">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Título do Arquivo</label>
                        <input type="text" name="titulo" required class="w-full rounded-xl border-none bg-zinc-900 text-white focus:ring-[#c5a059]" placeholder="Ex: Logo Dourada PNG">
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Categoria</label>
                        <select name="categoria" required class="w-full rounded-xl border-none bg-zinc-900 text-white focus:ring-[#c5a059]">
                            <option value="Logotipos">Logotipos</option>
                            <option value="Redes Sociais">Redes Sociais</option>
                            <option value="Vídeos Treinamento">Vídeos Treinamento</option>
                            <option value="Lançamentos">Lançamentos</option>
                        </select>
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Arquivo (Max 20MB)</label>
                        <input type="file" name="arquivo" required class="w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-[#c5a059] file:text-black">
                    </div>
                    <button type="submit" class="bg-[#c5a059] text-black font-black uppercase text-[10px] py-3 rounded-xl hover:bg-white transition shadow-lg">Enviar Arquivo</button>
                </form>
            </div>

            @forelse($medias as $categoria => $itens)
                <div class="bg-white rounded-[2.5rem] overflow-hidden border border-gray-100 shadow-sm mb-6">
                    <div class="bg-gray-50 px-8 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-[#c5a059]">{{ $categoria }}</h3>
                        <span class="text-[9px] bg-gray-200 text-gray-600 px-2 py-1 rounded-md font-bold">{{ count($itens) }} Itens</span>
                    </div>
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-white text-[9px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-50">
                            <tr>
                                <th class="px-8 py-3 w-16">Preview</th>
                                <th class="px-4 py-3">Arquivo</th>
                                <th class="px-8 py-3">Tipo</th>
                                <th class="px-8 py-3 text-right">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($itens as $media)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-4">
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden flex items-center justify-center border border-gray-200">
                                        @if(in_array(strtolower($media->tipo), ['jpg', 'jpeg', 'png', 'webp', 'gif']))
                                            <img src="{{ Storage::url($media->arquivo_path) }}" class="w-full h-full object-cover">
                                        @elseif(strtolower($media->tipo) === 'pdf')
                                            <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H9z" /><path d="M3 8a2 2 0 012-2v10h8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" /></svg>
                                        @elseif(in_array(strtolower($media->tipo), ['mp4', 'mov', 'avi']))
                                            <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm10 2v4l3-2-3-2z" /></svg>
                                        @else
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-900 text-sm">{{ $media->titulo }}</span>
                                        <span class="text-[9px] text-gray-400 lowercase truncate max-w-[200px]">{{ $media->arquivo_path }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-4 font-mono text-[10px] text-[#c5a059] font-bold uppercase">{{ $media->tipo }}</td>
                                <td class="px-8 py-4 text-right">
                                    <div class="flex justify-end items-center gap-4">
                                        <a href="{{ Storage::url($media->arquivo_path) }}" target="_blank" class="text-gray-400 hover:text-black transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>

                                        <form action="{{ route('media.destroy', $media->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-400 hover:text-red-600 font-black uppercase text-[10px] transition">Excluir</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @empty
                <div class="text-center py-20 bg-gray-50 rounded-[2.5rem] border-2 border-dashed border-gray-100">
                    <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Nenhum arquivo cadastrado no drive.</p>
                </div>
            @endforelse

        </div>
    </div>
</x-app-layout>
