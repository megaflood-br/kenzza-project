@section('title', 'Sobre')
<x-store-layout>
    <div class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-6 lg:px-10">

            {{-- Cabeçalho --}}
            <div class="text-center mb-20">
                <h3 class="text-[10px] font-black text-[#B8860B] uppercase tracking-[0.4em] mb-4">Institucional</h3>
                <h1 class="text-5xl font-black text-gray-900 tracking-tighter uppercase italic">A nossa <span class="text-[#B8860B]">Empresa</span></h1>
                <div class="h-1.5 w-20 bg-black mx-auto mt-8 rounded-full"></div>
            </div>

            {{-- Conteúdo --}}
            <div class="space-y-12">

                <section class="group">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="text-2xl font-black text-gray-200 group-hover:text-[#B8860B] transition-colors duration-500">01</span>
                        <h2 class="text-lg font-black uppercase tracking-widest text-gray-900">Alta Performance Capilar</h2>
                    </div>
                    <div class="bg-gray-50 p-8 rounded-[2rem] border-l-4 border-[#B8860B]">
                        <p class="text-gray-600 leading-relaxed text-sm uppercase font-bold tracking-tight">
                            A <span class="text-black">K'enzza Hair Professional</span> nasceu com o propósito inegociável de elevar o padrão dos cosméticos capilares no mercado nacional.
                            <br><br>
                            Unindo rigor tecnológico, ativos de altíssima pureza e fórmulas inovadoras, desenvolvemos linhas completas de transformação, tratamento e finalização que atendem às exigências mais severas dos maiores profissionais da beleza.
                        </p>
                    </div>
                </section>

                <section class="group">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="text-2xl font-black text-gray-200 group-hover:text-[#B8860B] transition-colors duration-500">02</span>
                        <h2 class="text-lg font-black uppercase tracking-widest text-gray-900">A Nossa Filosofia</h2>
                    </div>
                    <p class="text-gray-500 leading-relaxed text-sm uppercase font-bold tracking-tight pl-10">
                        Nossa filosofia baseia-se no equilíbrio perfeito entre a saúde da fibra capilar e a estética impecável. Cada produto que carrega a marca K'enzza passa por testes rigorosos de eficácia, garantindo que distribuidores tenham em mãos um portfólio de alta rotação, que cabeleireiros conquistem resultados cirúrgicos em seus lavatórios e que o consumidor final experimente o verdadeiro padrão de salão todos os dias em casa.
                    </p>
                </section>

                <section class="group">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="text-2xl font-black text-gray-200 group-hover:text-[#B8860B] transition-colors duration-500">03</span>
                        <h2 class="text-lg font-black uppercase tracking-widest text-gray-900">Ecossistema de Sucesso</h2>
                    </div>
                    <p class="text-gray-500 leading-relaxed text-sm uppercase font-bold tracking-tight pl-10">
                        Mais do que fabricar cosméticos, a K'enzza desenvolve ecossistemas de sucesso. Oferecemos suporte estratégico ponta a ponta para a nossa rede de distribuidores homologados e condições exclusivas para salões parceiros, consolidando conexões sólidas que transformam o mercado de beleza profissional de forma sustentável e luxuosa.
                    </p>
                </section>

                {{-- Botões de Ação integrados na mesma identidade --}}
                <div class="pt-8 flex flex-col sm:flex-row justify-center gap-4 max-w-md mx-auto">
                    <a href="/seja-um-distribuidor"
                       class="text-center bg-[#B8860B] text-black font-black uppercase text-xs tracking-[0.2em] py-5 px-8 rounded-2xl hover:bg-black hover:text-[#B8860B] transition-all shadow-xl">
                        Seja um Distribuidor
                    </a>
                    <a href="{{ route('shop.index') }}"
                       class="text-center border-2 border-black text-black font-black uppercase text-xs tracking-[0.2em] py-5 px-8 rounded-2xl hover:bg-black hover:text-white transition-all">
                        Conhecer Produtos
                    </a>
                </div>

                <div class="pt-10 border-t border-gray-100 text-center">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em]">K'enzza Hair Professional</p>
                </div>

            </div>
        </div>
    </div>
</x-store-layout>
