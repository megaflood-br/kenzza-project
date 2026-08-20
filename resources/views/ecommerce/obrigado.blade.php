<x-store-layout>
    <div class="py-10 bg-gray-50 min-h-screen flex items-center justify-center px-6">
        <div class="bg-white p-10 md:p-16 rounded-[3rem] shadow-xl border border-gray-100 max-w-2xl w-full text-center">

            {{-- Ícone de Sucesso --}}
            <div class="w-24 h-24 bg-green-50 text-green-500 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            {{-- Mensagem Principal --}}
            <h1 class="text-4xl md:text-5xl font-black text-gray-900 mb-6 uppercase tracking-tighter">
                Pedido <span class="text-[#B8860B]">Confirmado!</span>
            </h1>

            <p class="text-gray-500 font-bold mb-10 leading-relaxed text-sm md:text-base">
                Muito obrigado por comprar na K'enzza. O seu pagamento foi processado com sucesso! Já estamos preparando os seus produtos com todo o cuidado para o envio.
                <br><br>
                Enviamos os detalhes da compra e o comprovante para o seu e-mail.
            </p>

            {{-- Botões de Ação --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('customer.orders') }}"
                   class="w-full sm:w-auto bg-black text-white px-10 py-4 rounded-full font-black uppercase tracking-wider text-xs hover:bg-[#B8860B] transition-all shadow-md">
                    Ver Meus Pedidos
                </a>

                <a href="{{ route('shop.home') }}"
                   class="w-full sm:w-auto border-2 border-black text-black px-10 py-4 rounded-full font-black uppercase tracking-wider text-xs hover:bg-black hover:text-white transition-all">
                    Voltar para a Loja
                </a>
            </div>

        </div>
    </div>
</x-store-layout>
