@foreach($products as $product)
    <div class="group bg-white border border-gray-100 rounded-[1.5rem] md:rounded-[2.5rem] p-3 md:p-6 transition-all hover:shadow-2xl flex flex-col h-full">
        <a href="{{ route('shop.product', $product->slug) }}" class="block aspect-square flex items-center justify-center mb-4 overflow-hidden bg-gray-50/50 rounded-[1rem] md:rounded-[2rem]">
            <img src="{{ asset('storage/' . $product->imagem) }}" class="max-h-[85%] w-auto object-contain transition-transform duration-700 group-hover:scale-110">
        </a>

        <div class="text-center flex-grow flex flex-col justify-between">
            <h3 class="text-[10px] md:text-sm font-bold text-gray-900 uppercase tracking-tighter line-clamp-2 h-8 md:h-10 px-1">
                {{ $product->nome }}
            </h3>

            <p class="text-[#B8860B] font-black text-sm md:text-xl mt-2 tracking-tighter">
                R$ {{ number_format($product->preco_atual, 2, ',', '.') }}
            </p>
        </div>

        <form action="{{ route('cart.add', $product->slug) }}" method="POST" class="mt-4">
            @csrf
            <button aria-label="Ir para o carrinho de compras" class="w-full bg-black text-white py-2.5 md:py-4 rounded-full text-[8px] md:text-[10px] font-black uppercase tracking-widest hover:bg-[#B8860B] transition-all shadow-lg active:scale-95">
                Comprar
            </button>
        </form>
    </div>
@endforeach
