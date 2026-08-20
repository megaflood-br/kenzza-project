@section('title', 'Potítica de Devolução')
<x-store-layout>
    <div class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-6 lg:px-10">

            {{-- Cabeçalho da Página --}}
            <div class="text-center mb-20">
                <h3 class="text-[10px] font-black text-[#B8860B] uppercase tracking-[0.4em] mb-4">Atendimento</h3>
                <h1 class="text-5xl font-black text-gray-900 tracking-tighter uppercase italic">Política de <span class="text-[#B8860B]">Devolução</span></h1>
                <div class="h-1.5 w-20 bg-black mx-auto mt-8 rounded-full"></div>
            </div>

            {{-- Conteúdo --}}
            <div class="space-y-12">

                <section class="group">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="text-2xl font-black text-gray-200 group-hover:text-[#B8860B] transition-colors duration-500">01</span>
                        <h2 class="text-lg font-black uppercase tracking-widest text-gray-900">Critério de Devolução</h2>
                    </div>
                    <p class="text-gray-500 leading-relaxed text-sm uppercase font-bold tracking-tight pl-10">
                        Na <span class="text-black">K'enzza Hair</span>, aceitamos a devolução de produtos <span class="text-[#B8860B]">exclusivamente nos casos de defeito de fabricação</span>. Por questões de segurança e higiene em produtos de uso profissional/pessoal, não realizamos trocas por desistência ou insatisfação após o recebimento.
                    </p>
                </section>

                <section class="group">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="text-2xl font-black text-gray-200 group-hover:text-[#B8860B] transition-colors duration-500">02</span>
                        <h2 class="text-lg font-black uppercase tracking-widest text-gray-900">Como Solicitar</h2>
                    </div>
                    <p class="text-gray-500 leading-relaxed text-sm uppercase font-bold tracking-tight pl-10">
                        Caso identifique um defeito, você tem até <span class="text-black">7 dias corridos</span> após o recebimento para entrar em contato. É obrigatório o envio de fotos ou vídeos que comprovem a falha do produto para iniciarmos a análise técnica.
                    </p>
                </section>

                <section class="group">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="text-2xl font-black text-gray-200 group-hover:text-[#B8860B] transition-colors duration-500">03</span>
                        <h2 class="text-lg font-black uppercase tracking-widest text-gray-900">Análise e Retorno</h2>
                    </div>
                    <p class="text-gray-500 leading-relaxed text-sm uppercase font-bold tracking-tight pl-10">
                        Após o contato, nossa equipe avaliará a solicitação em até <span class="text-black">5 dias úteis</span>. Confirmado o defeito, forneceremos as orientações para logística reversa e seguiremos com o estorno ou envio de um novo item. O produto deve ser devolvido em sua embalagem original.
                    </p>
                </section>

                <div class="mt-20 p-10 bg-gray-50 rounded-[2.5rem] border border-gray-100 text-center">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Identificou um defeito? Entre em contato</p>
                    <a href="https://wa.me/5511912443903" target="_blank" class="inline-block bg-black text-white px-10 py-4 rounded-full text-[11px] font-black uppercase tracking-[0.2em] hover:bg-[#B8860B] transition-all shadow-xl">
                        Falar com Suporte WhatsApp
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-store-layout>
