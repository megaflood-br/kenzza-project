<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-4">
        <h3 class="font-black uppercase tracking-widest text-gray-400 text-[10px] mb-6">Produtos Disponíveis</h3>
<div class="flex gap-2 mb-8 overflow-x-auto pb-2">
    <button wire:click="$set('selectedCategory', null)"
            class="px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest transition {{ !$selectedCategory ? 'bg-[#c5a059] text-black' : 'bg-white/5 text-gray-400 hover:bg-white/10' }}">
        Todos
    </button>
    @foreach($categories as $category)
        <button wire:click="$set('selectedCategory', {{ $category->id }})"
                class="px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest transition {{ $selectedCategory == $category->id ? 'bg-[#c5a059] text-black' : 'bg-white/5 text-gray-400 hover:bg-white/10' }}">
            {{ $category->nome }}
        </button>
    @endforeach
</div>
        @foreach($products as $product)
            <div class="bg-white p-5 rounded-[2rem] shadow-sm border border-gray-100 flex items-center justify-between group hover:shadow-xl transition-all duration-500">
                <div class="flex items-center gap-6">
                    <div class="w-24 h-24 bg-[#fcfcfc] rounded-3xl overflow-hidden p-4 border border-gray-50">
                        <img src="{{ $product->imagem ? asset('storage/'.$product->imagem) : 'https://via.placeholder.com/150' }}" class="object-contain w-full h-full group-hover:scale-110 transition-transform">
                    </div>
                    <div>
                        <h4 class="font-black text-gray-900 text-lg tracking-tighter">{{ $product->nome }}</h4>
                        <p class="text-[10px] text-[#c5a059] font-black uppercase tracking-widest mt-1">
                            Preço Unitário: R$ {{ number_format($product->preco_atual, 2, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button wire:click="addToCart({{ $product->id }})" class="bg-black text-white px-6 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-[#c5a059] hover:text-black transition-all shadow-lg">
                        Adicionar
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <div class="lg:col-span-1">
        <div class="bg-black rounded-[3rem] p-8 text-white sticky top-8 shadow-2xl border border-white/5">
            <h4 class="font-black uppercase tracking-[0.2em] text-[10px] text-[#c5a059] mb-8">Resumo do Pedido</h4>

            <div class="space-y-6 mb-10 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                @forelse($cartItems as $item)
                    <div class="flex justify-between items-start border-b border-white/10 pb-4">
                        <div class="flex-1">
                            <p class="text-xs font-bold uppercase tracking-tight">{{ $item['nome'] }}</p>
                            <p class="text-[10px] text-gray-500 font-bold uppercase mt-1">{{ $item['qty'] }}x R$ {{ number_format($item['preco'], 2, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-black text-[#c5a059]">R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</p>
                            <button wire:click="removeFromCart({{ $item['id'] }})" class="text-[9px] text-red-500 font-black uppercase tracking-tighter hover:text-white transition mt-1">Remover</button>
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center">
                        <p class="text-gray-600 text-[10px] font-black uppercase tracking-[0.2em]">Seu carrinho está vazio</p>
                    </div>
                @endforelse
            </div>

            <div class="border-t border-white/20 pt-8">
                <div class="flex justify-between items-end mb-8">
                    <span class="text-[10px] font-black uppercase text-gray-500 tracking-widest">Total do Pedido</span>
                    <span class="text-4xl font-black text-[#c5a059] tracking-tighter">R$ {{ number_format($total, 2, ',', '.') }}</span>
                </div>

              @if($total > 0)

              {{-- Adicione este bloco antes do botão de finalizar no order-system.blade.php --}}
{{-- Bloco de Forma de Pagamento Atualizado --}}
<div class="mb-8">
    <h4 class="font-black uppercase tracking-widest text-[#c5a059] text-[10px] mb-4">Forma de Pagamento</h4>
    <div class="grid grid-cols-1 gap-3">

        <label class="flex items-center gap-3 p-4 rounded-2xl border cursor-pointer transition-all {{ $paymentMethod == 'pix' ? 'border-[#c5a059] bg-[#c5a059]/10' : 'border-white/10 bg-white/5' }}">
            <input type="radio" wire:model.live="paymentMethod" value="pix" class="hidden">
            <div class="w-4 h-4 rounded-full border-2 border-[#c5a059] flex items-center justify-center">
                @if($paymentMethod == 'pix') <div class="w-2 h-2 bg-[#c5a059] rounded-full"></div> @endif
            </div>
            <span class="text-xs font-bold uppercase">PIX (Aprovação Imediata)</span>
        </label>

        <label class="flex items-center gap-3 p-4 rounded-2xl border cursor-pointer transition-all {{ $paymentMethod == 'boleto' ? 'border-[#c5a059] bg-[#c5a059]/10' : 'border-white/10 bg-white/5' }}">
            <input type="radio" wire:model.live="paymentMethod" value="boleto" class="hidden">
            <div class="w-4 h-4 rounded-full border-2 border-[#c5a059] flex items-center justify-center">
                @if($paymentMethod == 'boleto') <div class="w-2 h-2 bg-[#c5a059] rounded-full"></div> @endif
            </div>
            <span class="text-xs font-bold uppercase">Boleto Bancário</span>
        </label>

        <label class="flex items-center gap-3 p-4 rounded-2xl border cursor-pointer transition-all {{ $paymentMethod == 'cartao' ? 'border-[#c5a059] bg-[#c5a059]/10' : 'border-white/10 bg-white/5' }}">
            <input type="radio" wire:model.live="paymentMethod" value="cartao" class="hidden">
            <div class="w-4 h-4 rounded-full border-2 border-[#c5a059] flex items-center justify-center">
                @if($paymentMethod == 'cartao') <div class="w-2 h-2 bg-[#c5a059] rounded-full"></div> @endif
            </div>
            <span class="text-xs font-bold uppercase">Cartão de Crédito (Link)</span>
        </label>

        <label class="flex items-center gap-3 p-4 rounded-2xl border cursor-pointer transition-all {{ $paymentMethod == 'cheque' ? 'border-[#c5a059] bg-[#c5a059]/10' : 'border-white/10 bg-white/5' }}">
            <input type="radio" wire:model.live="paymentMethod" value="cheque" class="hidden">
            <div class="w-4 h-4 rounded-full border-2 border-[#c5a059] flex items-center justify-center">
                @if($paymentMethod == 'cheque') <div class="w-2 h-2 bg-[#c5a059] rounded-full"></div> @endif
            </div>
            <span class="text-xs font-bold uppercase">Cheque (Sob Consulta)</span>
        </label>

    </div>

    @if(session()->has('error'))
        <p class="text-red-500 text-[10px] font-black uppercase mt-2">{{ session('error') }}</p>
    @endif
</div>
    <button wire:click="checkout"
            wire:loading.attr="disabled"
            class="w-full bg-[#c5a059] text-black font-black uppercase py-5 rounded-[1.5rem] hover:bg-white transition-all shadow-xl shadow-[#c5a059]/10 text-xs tracking-[0.2em] disabled:opacity-50 disabled:cursor-not-allowed">

        <span wire:loading.remove>Finalizar Pedido</span>

        <div wire:loading class="flex items-center justify-center gap-2">
            <svg class="animate-spin h-4 w-4 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Processando...</span>
        </div>
    </button>
@endif
            </div>
        </div>
    </div>
</div>
