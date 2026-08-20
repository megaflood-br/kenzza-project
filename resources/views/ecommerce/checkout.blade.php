@section('title', 'Checkout')
<x-store-layout>
    <div class="bg-gray-50 min-h-screen py-12" x-data="{ openModal: false }">
        <div class="max-w-7xl mx-auto px-6 lg:px-10">

            <div class="flex flex-col lg:flex-row gap-8 items-start justify-center">

                <div class="w-full lg:w-3/5 space-y-6">
                    <h1 class="text-4xl font-black text-black uppercase tracking-tighter mb-4">
                        Finalizar <span class="text-[#B8860B]">Pedido</span>
                    </h1>

                    {{-- PASSO 1: ENDEREÇO --}}
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 min-h-[250px] flex flex-col justify-between">
                        <div class="flex justify-between items-center mb-6">
                            <div class="flex items-center gap-4">
                                <span class="bg-black text-white w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm border-2 border-[#B8860B]">1</span>
                                <h2 class="text-lg font-black uppercase tracking-widest">Endereço de Entrega</h2>
                            </div>
                            <button @click="openModal = true" class="text-[#B8860B] text-[10px] font-black uppercase tracking-[0.2em] hover:underline">
                                ALTERAR ENDEREÇO
                            </button>
                        </div>

                        <div class="bg-gray-50 p-8 rounded-[2rem] flex-grow border border-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-3 text-sm text-gray-600">
                                <p><strong class="text-gray-900 uppercase text-[10px] tracking-widest block mb-1">Destinatário</strong> {{ auth()->user()->name }}</p>
                                <p><strong class="text-gray-900 uppercase text-[10px] tracking-widest block mb-1">CEP</strong> {{ auth()->user()->zip_code ?? 'Não informado' }}</p>
                                <div class="col-span-full mt-2 border-t border-gray-200 pt-4">
                                    <strong class="text-gray-900 uppercase text-[10px] tracking-widest block mb-1">Endereço Completo</strong>
                                    @if(auth()->user()->street)
                                        <p class="font-medium text-gray-800 leading-relaxed">
                                            {{ auth()->user()->street }}, {{ auth()->user()->number }}
                                            @if(auth()->user()->complement) - {{ auth()->user()->complement }} @endif
                                            <br>{{ auth()->user()->neighborhood }} - {{ auth()->user()->city }} / {{ auth()->user()->state }}
                                        </p>
                                    @else
                                        <span class="text-red-500 font-bold italic text-xs">Endereço incompleto. Clique em alterar para prosseguir.</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- PASSO 2: PAGAMENTO --}}
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                        <div class="flex items-center gap-4 mb-8">
                            <span class="bg-black text-white w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm border-2 border-[#B8860B]">2</span>
                            <h2 class="text-lg font-black uppercase tracking-widest">Forma de Pagamento</h2>
                        </div>

                        <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4" x-data="{ method: 'pix' }">
                                <label class="relative flex flex-col p-6 border-2 rounded-[2rem] cursor-pointer transition-all items-center text-center justify-center h-36"
                                    :class="method === 'pix' ? 'border-[#B8860B] bg-amber-50/20' : 'border-gray-50 bg-gray-50/50'">
                                    <input type="radio" name="payment_method" value="pix" x-model="method" class="hidden">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/50/Pix_%28Brazil%29_logo.svg" class="h-6 mb-3">
                                    <span class="text-[11px] font-black uppercase tracking-widest">Pix</span>
                                    <span class="text-[10px] text-green-600 font-black mt-1">5% OFF</span>
                                </label>

                                <label class="relative flex flex-col p-6 border-2 rounded-[2rem] cursor-pointer transition-all items-center text-center justify-center h-36"
                                    :class="method === 'card' ? 'border-[#B8860B] bg-amber-50/20' : 'border-gray-50 bg-gray-50/50'">
                                    <input type="radio" name="payment_method" value="card" x-model="method" class="hidden">
                                    <div class="flex gap-1 mb-3">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" class="h-4">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/f/fe/Visa_Inc._logo_%281992%E2%80%931999%29.svg" class="h-4">
                                    </div>
                                    <span class="text-[11px] font-black uppercase tracking-widest">Cartão</span>
                                    <span class="text-[10px] text-gray-400 font-bold mt-1">Até 12x</span>
                                </label>

                                <label class="relative flex flex-col p-6 border-2 rounded-[2rem] cursor-pointer transition-all items-center text-center justify-center h-36"
                                    :class="method === 'boleto' ? 'border-[#B8860B] bg-amber-50/20' : 'border-gray-50 bg-gray-50/50'">
                                    <input type="radio" name="payment_method" value="boleto" x-model="method" class="hidden">
                                    <svg class="h-7 w-7 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    <span class="text-[11px] font-black uppercase tracking-widest">Boleto</span>
                                </label>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ASIDE: REVISÃO --}}
                <aside class="w-full lg:w-2/5 mt-[68px]">
                    <div class="bg-white p-10 rounded-[3rem] shadow-xl border border-gray-100 sticky top-10">
                        <h2 class="text-xs font-black uppercase tracking-[0.3em] mb-8 border-b border-gray-50 pb-6">Revisão do Pedido</h2>

                        <div class="space-y-5 mb-8 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                            @foreach($cart as $id => $item)
                            <div class="flex justify-between items-center text-[13px]">
                                <span class="text-gray-500 font-medium leading-tight flex-1 mr-4">{{ $item['quantidade'] }}x {{ $item['nome'] }}</span>
                                <span class="font-black text-gray-900">R$ {{ number_format($item['preco'] * $item['quantidade'], 2, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>

                        <div class="space-y-4 border-t border-gray-100 pt-8">
                            <div class="flex justify-between text-gray-400 text-[11px] font-black uppercase tracking-widest">
                                <span>Subtotal</span>
                                <span class="text-gray-900">R$ {{ number_format(array_sum(array_map(fn($i) => $i['preco'] * $i['quantidade'], $cart)), 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-gray-400 text-[11px] font-black uppercase tracking-widest">
                                <span>Frete</span>
                                <span class="text-gray-900">R$ {{ number_format(session('shipping_value', 0), 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-3xl pt-8 mt-4 border-t border-gray-50">
                                <span class="font-black uppercase tracking-tighter italic text-gray-900">TOTAL</span>
                                <span class="font-black text-[#B8860B] tracking-tighter">R$ {{ number_format(array_sum(array_map(fn($i) => $i['preco'] * $i['quantidade'], $cart)) + session('shipping_value', 0), 2, ',', '.') }}</span>
                            </div>
                        </div>

                        <button type="submit" form="checkout-form"
                            class="w-full bg-black text-white text-center py-5 rounded-full mt-10 font-black uppercase tracking-[0.2em] text-[11px] hover:bg-[#B8860B] hover:text-black transition-all shadow-2xl active:scale-95 shadow-black/10">
                            Confirmar e Pagar
                        </button>

                        <div class="mt-8 flex items-center justify-center gap-2 opacity-30 grayscale text-[9px] font-black uppercase tracking-widest">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"></path></svg>
                            Ambiente de Pagamento Criptografado
                        </div>
                    </div>
                </aside>
            </div>
        </div>

        {{-- MODAL DE ENDEREÇO --}}
        <div x-show="openModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 py-12">
                <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="openModal = false"></div>
                <div class="bg-white rounded-[3rem] shadow-2xl relative z-[101] w-full max-w-xl p-12">
                    <h3 class="text-2xl font-black uppercase italic mb-10 tracking-tighter">Alterar <span class="text-[#B8860B]">Endereço</span></h3>

                    <form action="{{ route('checkout.update-address') }}" method="POST" class="grid grid-cols-2 gap-5">
                        @csrf
                        <div class="col-span-1">
                            <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest block mb-2 ml-2">CEP</label>
                            <input type="text" name="zip_code" value="{{ auth()->user()->zip_code }}" class="w-full border-none rounded-2xl bg-gray-50 text-sm font-bold focus:ring-1 focus:ring-[#B8860B] p-4" required>
                        </div>
                        <div class="col-span-1">
                            <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest block mb-2 ml-2">CPF/CNPJ</label>
                            <input type="text" name="document" value="{{ auth()->user()->document }}" class="w-full border-none rounded-2xl bg-gray-50 text-sm font-bold focus:ring-1 focus:ring-[#B8860B] p-4" required>
                        </div>
                        <div class="col-span-2">
                            <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest block mb-2 ml-2">Rua/Avenida</label>
                            <input type="text" name="street" value="{{ auth()->user()->street }}" class="w-full border-none rounded-2xl bg-gray-50 text-sm font-bold focus:ring-1 focus:ring-[#B8860B] p-4" required>
                        </div>
                        <div class="col-span-1">
                            <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest block mb-2 ml-2">Número</label>
                            <input type="text" name="number" value="{{ auth()->user()->number }}" class="w-full border-none rounded-2xl bg-gray-50 text-sm font-bold focus:ring-1 focus:ring-[#B8860B] p-4" required>
                        </div>
                        <div class="col-span-1">
                            <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest block mb-2 ml-2">Complemento</label>
                            <input type="text" name="complement" value="{{ auth()->user()->complement }}" class="w-full border-none rounded-2xl bg-gray-50 text-sm font-bold focus:ring-1 focus:ring-[#B8860B] p-4">
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest block mb-2 ml-2">Bairro</label>
                            <input type="text" name="neighborhood" value="{{ auth()->user()->neighborhood }}" class="w-full border-none rounded-2xl bg-gray-50 text-sm font-bold focus:ring-1 focus:ring-[#B8860B] p-4" required>
                        </div>
                        <div class="col-span-1 md:col-span-1">
                            <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest block mb-2 ml-2">Cidade</label>
                            <input type="text" name="city" value="{{ auth()->user()->city }}" class="w-full border-none rounded-2xl bg-gray-50 text-sm font-bold focus:ring-1 focus:ring-[#B8860B] p-4" required>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest block mb-2 ml-2">Estado (UF)</label>
                            <input type="text" name="state" value="{{ auth()->user()->state }}" maxlength="2" class="w-full border-none rounded-2xl bg-gray-50 text-sm font-bold focus:ring-1 focus:ring-[#B8860B] p-4 uppercase" required>
                        </div>

                        <input type="hidden" name="phone" value="{{ auth()->user()->phone ?? '000000000' }}">

                        <div class="col-span-2 flex gap-4 mt-8">
                            <button type="button" @click="openModal = false" class="flex-1 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Cancelar</button>
                            <button type="submit" class="flex-1 py-5 bg-black text-white rounded-full font-black uppercase tracking-widest text-[11px] hover:bg-[#B8860B] transition-all shadow-xl active:scale-95">Atualizar Dados</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-store-layout>
