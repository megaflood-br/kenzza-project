@section('title', 'Política de Entrega')
<x-store-layout>
    <div class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-6 lg:px-10">

            {{-- Cabeçalho da Página --}}
            <div class="text-center mb-20">
                <h3 class="text-[10px] font-black text-[#B8860B] uppercase tracking-[0.4em] mb-4">Atendimento</h3>
                <h1 class="text-5xl font-black text-gray-900 tracking-tighter uppercase italic">Políticas de <span class="text-[#B8860B]">Envio</span></h1>
                <div class="h-1.5 w-20 bg-black mx-auto mt-8 rounded-full"></div>
            </div>

            {{-- Conteúdo --}}
            <div class="space-y-12">

                <section class="group">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="text-2xl font-black text-gray-200 group-hover:text-[#B8860B] transition-colors duration-500">01</span>
                        <h2 class="text-lg font-black uppercase tracking-widest text-gray-900">Prazo de Processamento</h2>
                    </div>
                    <p class="text-gray-500 leading-relaxed text-sm uppercase font-bold tracking-tight pl-10">
                        Após a confirmação do pagamento, os pedidos K'enzza Hair levam até <span class="text-black">2 dias úteis</span> para serem processados, embalados e coletados pela transportadora. Pedidos realizados em finais de semana ou feriados serão processados no próximo dia útil.
                    </p>
                </section>

                <section class="group">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="text-2xl font-black text-gray-200 group-hover:text-[#B8860B] transition-colors duration-500">02</span>
                        <h2 class="text-lg font-black uppercase tracking-widest text-gray-900">Métodos de Entrega</h2>
                    </div>
                    <p class="text-gray-500 leading-relaxed text-sm uppercase font-bold tracking-tight pl-10">
                        Utilizamos parceiros logísticos de alta performance (Correios e Transportadoras via Melhor Envio). O valor e o prazo de entrega são calculados automaticamente no carrinho com base no <span class="text-black">CEP e peso total</span> dos produtos profissionais selecionados.
                    </p>
                </section>

                <section class="group">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="text-2xl font-black text-gray-200 group-hover:text-[#B8860B] transition-colors duration-500">03</span>
                        <h2 class="text-lg font-black uppercase tracking-widest text-gray-900">Rastreamento</h2>
                    </div>
                    <p class="text-gray-500 leading-relaxed text-sm uppercase font-bold tracking-tight pl-10">
                        Assim que seu pedido for despachado, você receberá um e-mail com o <span class="text-[#B8860B]">código de rastreio</span>. Você também poderá acompanhar o status diretamente no seu painel de cliente K'enzza, na seção "Meus Pedidos".
                    </p>
                </section>

                <section class="group">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="text-2xl font-black text-gray-200 group-hover:text-[#B8860B] transition-colors duration-500">04</span>
                        <h2 class="text-lg font-black uppercase tracking-widest text-gray-900">Endereço de Entrega</h2>
                    </div>
                    <p class="text-gray-500 leading-relaxed text-sm uppercase font-bold tracking-tight pl-10">
                        Atenção: Não é possível alterar o endereço de entrega após a finalização do pedido devido aos protocolos de segurança. Certifique-se de que os dados estejam corretos no momento do checkout. Caso a encomenda retorne por erro de endereço, um novo frete será cobrado.
                    </p>
                </section>

                <div class="mt-20 p-10 bg-gray-50 rounded-[2.5rem] border border-gray-100 text-center">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Ainda tem dúvidas sobre sua entrega?</p>
                    <a href="https://wa.me/5511912443903" target="_blank" class="inline-block bg-black text-white px-10 py-4 rounded-full text-[11px] font-black uppercase tracking-[0.2em] hover:bg-[#B8860B] transition-all shadow-xl">
                        Falar com Suporte WhatsApp
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-store-layout>
