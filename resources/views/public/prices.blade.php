<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Preços {{ $titulo }} - K'enzza Hair</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .bg-gold { background-color: #B8860B; }
        .text-gold { color: #B8860B; }
        .border-gold { border-color: #B8860B; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased"
      x-data="{
        search: '',
        totalVisible: 0,
        updateGlobalStatus() {
            let found = false;
            // Verifica em todos os blocos de categoria se há algum produto filtrado
            document.querySelectorAll('.category-block').forEach(el => {
                if (el.style.display !== 'none') found = true;
            });
            this.totalVisible = found ? 1 : 0;
        }
      }">

    <nav class="bg-black py-6 sticky top-0 z-50 shadow-lg">
        <div class="max-w-4xl mx-auto px-6 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-white font-black uppercase tracking-tighter text-xl">K'enzza</span>
                <div class="w-1 h-6 bg-gold"></div>
                <span class="text-gold font-bold text-[10px] uppercase tracking-[0.2em]">Tabela {{ $titulo }}</span>
            </div>
            <span class="text-gray-500 text-[10px] font-bold uppercase tracking-widest">v. 2025</span>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-10">

        <div class="mb-8 sticky top-24 z-40">
            <div class="relative group">
                <input
                    type="text"
                    x-model="search"
                    @input.debounce.200ms="updateGlobalStatus()"
                    placeholder="Busque por linha ou produto..."
                    class="w-full bg-white border-2 border-gray-100 focus:border-gold rounded-2xl py-4 pl-12 pr-4 shadow-xl focus:outline-none transition-all text-sm"
                >
                <div class="absolute left-4 top-4 text-gray-400 group-focus-within:text-gold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <button x-show="search.length > 0" x-cloak @click="search = ''; updateGlobalStatus()" class="absolute right-4 top-4 text-gray-400 hover:text-red-500">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
        </div>

        @forelse($categories as $familia => $produtos)
            <div
                class="mb-12 category-block"
                x-data="{
                    categoryName: '{{ strtolower($familia) }}',
                    allProducts: {{ json_encode($produtos) }},
                    get filtered() {
                        if (!search) return this.allProducts;
                        let s = search.toLowerCase();
                        if (this.categoryName.includes(s)) return this.allProducts;
                        return this.allProducts.filter(p => p.produto.toLowerCase().includes(s));
                    }
                }"
                x-show="filtered.length > 0"
                x-init="$watch('search', () => updateGlobalStatus())"
                x-cloak
            >
                <h2 class="text-sm font-black text-black uppercase tracking-[0.3em] mb-4 ml-2 border-l-4 border-gold pl-3">
                    {{ $familia }}
                </h2>

                <div class="bg-white shadow-xl rounded-[2.5rem] overflow-hidden border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <tbody class="divide-y divide-gray-50">
                                <template x-for="item in filtered" :key="item.produto">
                                    <tr class="hover:bg-gold/5 transition-colors group">
                                        <td class="py-5 px-8">
                                            <p class="text-sm font-bold text-gray-800 group-hover:text-gold transition-colors leading-tight" x-text="item.produto"></p>
                                        </td>
                                        <td class="py-5 px-4 text-center">
                                            <span class="inline-block bg-gray-100 px-3 py-1 rounded-full text-[9px] font-black text-gray-500 uppercase tracking-tighter" x-text="item.tamanho"></span>
                                        </td>
                                        <td class="py-5 px-8 text-right">
                                            <p class="font-black text-sm text-black tracking-tight" x-text="'R$ ' + parseFloat(item.preco).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></p>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-32">
                <p class="text-gray-400 font-black uppercase text-xs tracking-widest">Nenhum dado encontrado.</p>
            </div>
        @endforelse

        <div x-show="search !== '' && totalVisible === 0" class="text-center py-20" x-cloak>
            <div class="inline-block p-6 bg-gray-100 rounded-full mb-4">
                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <p class="text-gray-400 font-bold uppercase text-xs tracking-widest">Nenhum resultado para "<span x-text="search" class="text-gold"></span>"</p>
        </div>

        <div class="mt-20 text-center space-y-6 pb-12">
            <p class="text-[9px] font-bold text-gray-300 uppercase tracking-[0.5em]">K'enzza Hair Professional &copy; 2026</p>
        </div>
    </div>

</body>
</html>
