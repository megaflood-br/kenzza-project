@section('title', 'Termos e Condições')
<x-store-layout>
    <div class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-6 lg:px-10">

            {{-- Cabeçalho --}}
            <div class="text-center mb-20">
                <h3 class="text-[10px] font-black text-[#B8860B] uppercase tracking-[0.4em] mb-4">Jurídico</h3>
                <h1 class="text-5xl font-black text-gray-900 tracking-tighter uppercase italic">Termos e <span class="text-[#B8860B]">Condições</span></h1>
                <div class="h-1.5 w-20 bg-black mx-auto mt-8 rounded-full"></div>
            </div>

            {{-- Conteúdo --}}
            <div class="space-y-12">

                <section class="group">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="text-2xl font-black text-gray-200 group-hover:text-[#B8860B] transition-colors duration-500">01</span>
                        <h2 class="text-lg font-black uppercase tracking-widest text-gray-900">Pagamentos e Cobrança</h2>
                    </div>
                    <div class="bg-gray-50 p-8 rounded-[2rem] border-l-4 border-[#B8860B]">
                        <p class="text-gray-600 leading-relaxed text-sm uppercase font-bold tracking-tight">
                            Informamos que todas as transações financeiras desta loja são processadas via <span class="text-black">Asaas</span>.
                            <br><br>
                            <span class="text-[#B8860B]">Importante:</span> Na fatura do seu cartão de crédito ou comprovante de PIX, a cobrança aparecerá em nome de:
                            <span class="text-black block mt-2 text-base italic font-black">ALYCIA QUEIROZ COSMETICOS LTDA</span>
                            Esta é a nossa unidade de faturamento oficial responsável pela marca K'enzza Hair Professional.
                        </p>
                    </div>
                </section>

                <section class="group">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="text-2xl font-black text-gray-200 group-hover:text-[#B8860B] transition-colors duration-500">02</span>
                        <h2 class="text-lg font-black uppercase tracking-widest text-gray-900">Uso do Site</h2>
                    </div>
                    <p class="text-gray-500 leading-relaxed text-sm uppercase font-bold tracking-tight pl-10">
                        Ao acessar o site da K'enzza Hair, você concorda em cumprir estes termos de serviço, todas as leis e regulamentos aplicáveis. O uso de nossos produtos profissionais deve seguir estritamente as instruções contidas nas embalagens ou fichas técnicas fornecidas.
                    </p>
                </section>

                <section class="group">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="text-2xl font-black text-gray-200 group-hover:text-[#B8860B] transition-colors duration-500">03</span>
                        <h2 class="text-lg font-black uppercase tracking-widest text-gray-900">Propriedade Intelectual</h2>
                    </div>
                    <p class="text-gray-500 leading-relaxed text-sm uppercase font-bold tracking-tight pl-10">
                        Todo o conteúdo deste site, incluindo logos, imagens de produtos e textos, é de propriedade exclusiva da <span class="text-black">Fik Xique Moda e Acessórios</span> e da marca K'enzza Hair, sendo protegidos por leis de direitos autorais.
                    </p>
                </section>

                <section class="group">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="text-2xl font-black text-gray-200 group-hover:text-[#B8860B] transition-colors duration-500">04</span>
                        <h2 class="text-lg font-black uppercase tracking-widest text-gray-900">Limitação de Responsabilidade</h2>
                    </div>
                    <p class="text-gray-500 leading-relaxed text-sm uppercase font-bold tracking-tight pl-10">
                        A K'enzza Hair não se responsabiliza pelo uso indevido de produtos de uso exclusivo profissional por pessoas não capacitadas. Recomendamos que as linhas de transformação sejam aplicadas apenas por cabeleireiros habilitados.
                    </p>
                </section>

                <div class="pt-10 border-t border-gray-100 text-center">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em]">Última atualização: Maio de 2026</p>
                </div>

            </div>
        </div>
    </div>
</x-store-layout>
