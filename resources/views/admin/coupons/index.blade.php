<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-black text-2xl text-gray-900 uppercase tracking-tighter">
                Gestão de <span class="text-[#c5a059]">Cupons</span>
            </h2>
            <button x-data="" @click.prevent="$dispatch('open-modal', 'add-coupon')" class="w-full sm:w-auto bg-black text-[#c5a059] px-6 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-[#c5a059] hover:text-black transition-all shadow-md border border-[#c5a059]">
                + Novo Cupom
            </button>
        </div>
    </x-slot>

    {{-- INTEGRAÇÃO ALPINE: Centraliza os dados reativos para a edição --}}
    <div class="py-12 bg-gray-50 min-h-screen" x-data="{
        editForm: {
            id: '',
            codigo: '',
            tipo: 'percentual',
            valor: '',
            limite_uso: 0,
            validade: '',
            category_ids: []
        },
        openEdit(couponData) {
            this.editForm = {
                id: couponData.id,
                codigo: couponData.codigo,
                tipo: couponData.tipo,
                valor: couponData.valor,
                limite_uso: couponData.limite_uso,
                validade: couponData.validade ? couponData.validade.substring(0, 10) : '',
                category_ids: couponData.categories.map(c => c.id)
            };
            $dispatch('open-modal', 'edit-coupon');
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- LISTA DE CUPONS (HÍBRIDA: Tabela no PC / Cards no Celular) --}}
            <div class="bg-white shadow-sm sm:rounded-[2rem] border border-gray-100 overflow-hidden">

                {{-- Oculta o cabeçalho no celular --}}
                <table class="w-full text-left border-collapse">
                    <thead class="hidden lg:table-header-group bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest">Código</th>
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Tipo / Valor</th>
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Restrição</th>
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Uso / Limite</th>
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Validade</th>
                            <th class="py-5 px-6 text-[10px] font-black uppercase text-gray-400 tracking-widest text-right">Ações</th>
                        </tr>
                    </thead>

                    <tbody class="flex flex-col lg:table-row-group divide-y lg:divide-y-0 divide-gray-100 p-4 lg:p-0 space-y-4 lg:space-y-0">
                        @forelse($coupons as $coupon)
                            <tr class="flex flex-col lg:table-row bg-white lg:hover:bg-gray-50/50 transition-all p-5 lg:p-0 rounded-3xl lg:rounded-none border lg:border-0 border-gray-100 lg:border-b shadow-sm lg:shadow-none group relative {{ !$coupon->ativo ? 'opacity-70' : '' }}">

                                {{-- 1. Código --}}
                                <td class="py-3 lg:py-5 px-2 lg:px-6 flex justify-between lg:table-cell items-center">
                                    <span class="lg:hidden text-[10px] font-black text-gray-400 uppercase tracking-widest">Código</span>
                                    <span class="bg-gray-900 text-[#c5a059] px-4 py-2 rounded-xl font-black text-xs uppercase tracking-widest shadow-inner border border-black">
                                        {{ $coupon->codigo }}
                                    </span>
                                </td>

                                {{-- 2. Tipo e Valor --}}
                                <td class="py-3 lg:py-5 px-2 lg:px-6 flex justify-between lg:table-cell items-center lg:text-center border-t border-gray-50 lg:border-0">
                                    <span class="lg:hidden text-[10px] font-black text-gray-400 uppercase tracking-widest">Benefício</span>
                                    <div class="flex flex-col lg:items-center">
                                        <span class="font-black text-sm text-gray-900 leading-tight">
                                            {{ $coupon->tipo == 'percentual' ? 'Desconto %' : 'Frete Grátis' }}
                                        </span>
                                        @if($coupon->tipo == 'percentual')
                                            <span class="text-[#B8860B] font-black text-[10px] uppercase tracking-widest mt-0.5">{{ number_format($coupon->valor, 0) }}% OFF</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- 3. Restrição de Categoria --}}
                                <td class="py-3 lg:py-5 px-2 lg:px-6 flex justify-between lg:table-cell items-center lg:text-center border-t border-gray-50 lg:border-0">
                                    <span class="lg:hidden text-[10px] font-black text-gray-400 uppercase tracking-widest">Aplica-se a</span>
                                    <div class="flex justify-end lg:justify-center">
                                        @if($coupon->categories->isNotEmpty())
                                            <span class="text-[9px] bg-amber-50 text-[#B8860B] border border-amber-100 font-black uppercase px-3 py-1.5 rounded-lg tracking-widest text-center"
                                                  title="{{ $coupon->categories->pluck('nome')->implode(', ') }}">
                                                {{ $coupon->categories->count() }} {{ $coupon->categories->count() == 1 ? 'Categoria' : 'Categorias' }}
                                            </span>
                                        @else
                                            <span class="text-[9px] bg-green-50 text-green-600 border border-green-100 font-black uppercase px-3 py-1.5 rounded-lg tracking-widest text-center">
                                                Loja Toda
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- 4. Uso / Limite --}}
                                <td class="py-3 lg:py-5 px-2 lg:px-6 flex justify-between lg:table-cell items-center lg:text-center border-t border-gray-50 lg:border-0">
                                    <span class="lg:hidden text-[10px] font-black text-gray-400 uppercase tracking-widest">Uso/Limite</span>
                                    <div class="flex items-center gap-2 lg:justify-center">
                                        <span class="text-xs font-black text-gray-900">{{ $coupon->vezes_usado }}</span>
                                        <span class="text-gray-300">/</span>
                                        <span class="text-xs font-bold text-gray-500">{{ $coupon->limite_uso > 0 ? $coupon->limite_uso : '∞' }}</span>
                                    </div>
                                </td>

                                {{-- 5. Validade --}}
                                <td class="py-3 lg:py-5 px-2 lg:px-6 flex justify-between lg:table-cell items-center lg:text-center border-t border-gray-50 lg:border-0">
                                    <span class="lg:hidden text-[10px] font-black text-gray-400 uppercase tracking-widest">Validade</span>
                                    <span class="text-[10px] font-black uppercase tracking-widest {{ $coupon->validade && \Carbon\Carbon::parse($coupon->validade)->isPast() ? 'text-red-500' : 'text-gray-500' }}">
                                        {{ $coupon->validade ? date('d/m/Y', strtotime($coupon->validade)) : 'Vitalício' }}
                                    </span>
                                </td>

                                {{-- 6. Ações --}}
                                <td class="py-4 lg:py-5 px-2 lg:px-6 flex justify-center lg:table-cell lg:text-right border-t border-gray-50 lg:border-0 mt-2 lg:mt-0">
                                    <div class="flex justify-between lg:justify-end items-center w-full lg:w-auto gap-4">

                                        {{-- Botão de Status (Ativar/Inativar) --}}
                                        <form action="{{ route('coupons.toggle', $coupon->id) }}" method="POST" class="flex-1 lg:flex-none">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="w-full lg:w-auto flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all {{ $coupon->ativo ? 'bg-green-50 text-green-600 hover:bg-red-50 hover:text-red-600' : 'bg-gray-100 text-gray-500 hover:bg-green-50 hover:text-green-600' }}">
                                                <div class="w-1.5 h-1.5 rounded-full {{ $coupon->ativo ? 'bg-green-500' : 'bg-gray-400' }}"></div>
                                                {{ $coupon->ativo ? 'Ativo' : 'Inativo' }}
                                            </button>
                                        </form>

                                        <div class="flex items-center gap-2 flex-1 lg:flex-none justify-end">
                                            {{-- Botão Editar --}}
                                            <button type="button" @click="openEdit({{ $coupon->toJson() }})" class="p-2 text-gray-400 hover:text-black hover:bg-gray-100 rounded-xl transition" title="Editar Cupom">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>

                                            {{-- Botão Remover --}}
                                            <form action="{{ route('coupons.destroy', $coupon->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja apagar este cupom?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition" title="Excluir Cupom">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr class="hidden lg:table-row">
                                <td colspan="6" class="py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                                        </div>
                                        <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Nenhum cupom cadastrado.</p>
                                    </div>
                                </td>
                            </tr>
                            {{-- Empty State (Mobile) --}}
                            <div class="lg:hidden bg-white rounded-[2rem] border-2 border-dashed border-gray-100 p-12 text-center flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                                </div>
                                <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">Nenhum cupom cadastrado.</p>
                            </div>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="h-20"></div>

            {{-- MODAIS ORIGINAIS (Mantidos exatamente iguais) --}}

            {{-- MODAL DE CADASTRO (NOVO) --}}
            <x-modal name="add-coupon" focusable>
                <div class="p-8 sm:p-10 bg-white" x-data="{ tipo_cupom: 'percentual' }">
                    <form action="{{ route('coupons.store') }}" method="POST">
                        @csrf
                        <h3 class="text-xl sm:text-2xl font-black text-gray-900 uppercase tracking-tighter mb-8">Criar Novo Cupom</h3>

                        <div class="space-y-6">
                            <div>
                                <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-2">Código do Cupom</label>
                                <input type="text" name="codigo" required class="w-full mt-2 bg-gray-50 border-none rounded-2xl px-6 py-4 font-black uppercase focus:ring-[#B8860B]" placeholder="EX: KENZZA10">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-2">Tipo</label>
                                    <select name="tipo" x-model="tipo_cupom" class="w-full mt-2 bg-gray-50 border-none rounded-2xl px-6 py-4 font-bold focus:ring-[#B8860B]">
                                        <option value="percentual">Percentual (%)</option>
                                        <option value="frete_gratis">Frete Grátis</option>
                                    </select>
                                </div>
                                <div x-show="tipo_cupom === 'percentual'">
                                    <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-2">Valor (%)</label>
                                    <input type="number" name="valor" class="w-full mt-2 bg-gray-50 border-none rounded-2xl px-6 py-4 font-black focus:ring-[#B8860B]" placeholder="10">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-2">Limite de Uso</label>
                                    <input type="number" name="limite_uso" class="w-full mt-2 bg-gray-50 border-none rounded-2xl px-6 py-4 font-bold focus:ring-[#B8860B]" value="0">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-2">Validade</label>
                                    <input type="date" name="validade" class="w-full mt-2 bg-gray-50 border-none rounded-2xl px-6 py-4 font-bold focus:ring-[#B8860B]">
                                </div>
                            </div>

                            <div class="border-t border-gray-100 pt-6">
                                <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-2 block mb-3">Aplicável em quais categorias?</label>
                                <div class="bg-gray-50 p-6 rounded-2xl max-h-48 overflow-y-auto grid grid-cols-1 gap-3 border border-gray-100/50 custom-scrollbar">
                                    @foreach($categories as $category)
                                        <label class="flex items-center gap-3 bg-white p-3.5 rounded-xl border border-gray-100 cursor-pointer shadow-sm hover:border-[#B8860B] transition-all">
                                            <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" class="rounded text-[#B8860B] focus:ring-[#B8860B] border-gray-200 w-4 h-4">
                                            <span class="text-xs font-bold text-gray-700 uppercase tracking-wide">{{ $category->nome }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 flex flex-col sm:flex-row justify-end gap-4 border-t border-gray-50 pt-6">
                            <button type="button" @click="$dispatch('close-modal', 'add-coupon')" class="text-[10px] font-black uppercase text-gray-400 px-6 py-4 sm:py-0 text-center">Cancelar</button>
                            <button type="submit" class="w-full sm:w-auto bg-black text-[#c5a059] px-10 py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-[#c5a059] hover:text-black transition-all shadow-md">Salvar Cupom</button>
                        </div>
                    </form>
                </div>
            </x-modal>

            {{-- MODAL DE EDIÇÃO DE CUPOM --}}
            <x-modal name="edit-coupon" focusable>
                <div class="p-8 sm:p-10 bg-white">
                    <form :action="'/admin/cupons/' + editForm.id" method="POST">
                        @csrf
                        @method('PUT')
                        <h3 class="text-xl sm:text-2xl font-black text-gray-900 uppercase tracking-tighter mb-8">Editar Cupom</h3>

                        <div class="space-y-6">
                            <div>
                                <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-2">Código do Cupom</label>
                                <input type="text" name="codigo" required x-model="editForm.codigo" class="w-full mt-2 bg-gray-50 border-none rounded-2xl px-6 py-4 font-black uppercase focus:ring-[#B8860B]">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-2">Tipo</label>
                                    <select name="tipo" x-model="editForm.tipo" class="w-full mt-2 bg-gray-50 border-none rounded-2xl px-6 py-4 font-bold focus:ring-[#B8860B] appearance-none cursor-pointer">
                                        <option value="percentual">Percentual (%)</option>
                                        <option value="frete_gratis">Frete Grátis</option>
                                    </select>
                                </div>
                                <div x-show="editForm.tipo === 'percentual'">
                                    <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-2">Valor (%)</label>
                                    <input type="number" name="valor" x-model="editForm.valor" class="w-full mt-2 bg-gray-50 border-none rounded-2xl px-6 py-4 font-black focus:ring-[#B8860B]">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-2">Limite de Uso</label>
                                    <input type="number" name="limite_uso" x-model="editForm.limite_uso" class="w-full mt-2 bg-gray-50 border-none rounded-2xl px-6 py-4 font-bold focus:ring-[#B8860B]">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-2">Validade</label>
                                    <input type="date" name="validade" x-model="editForm.validade" class="w-full mt-2 bg-gray-50 border-none rounded-2xl px-6 py-4 font-bold focus:ring-[#B8860B]">
                                </div>
                            </div>

                            <div class="border-t border-gray-100 pt-6">
                                <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-2 block mb-3">Categorias Aplicáveis</label>
                                <div class="bg-gray-50 p-6 rounded-2xl max-h-48 overflow-y-auto grid grid-cols-1 gap-3 border border-gray-100/50 custom-scrollbar">
                                    @foreach($categories as $category)
                                        <label class="flex items-center gap-3 bg-white p-3.5 rounded-xl border border-gray-100 cursor-pointer shadow-sm hover:border-[#B8860B] transition-all">
                                            <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                                   :checked="editForm.category_ids.includes({{ $category->id }})"
                                                   class="rounded text-[#B8860B] focus:ring-[#B8860B] border-gray-200 w-4 h-4">
                                            <span class="text-xs font-bold text-gray-700 uppercase tracking-wide">{{ $category->nome }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 flex flex-col sm:flex-row justify-end gap-4 border-t border-gray-50 pt-6">
                            <button type="button" @click="$dispatch('close-modal', 'edit-coupon')" class="text-[10px] font-black uppercase text-gray-400 px-6 py-4 sm:py-0 text-center">Cancelar</button>
                            <button type="submit" class="w-full sm:w-auto bg-black text-[#c5a059] px-10 py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-[#c5a059] hover:text-black transition-all shadow-md">Atualizar Cupom</button>
                        </div>
                    </form>
                </div>
            </x-modal>

        </div>
    </div>
</x-app-layout>

<style>
    /* Oculta os modais no carregamento */
    [x-cloak] { display: none !important; }

    /* Scrollbar personalizada para as listas de categorias */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #e5e7eb;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background-color: #d1d5db;
    }
</style>
