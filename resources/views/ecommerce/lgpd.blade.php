@section('title', 'LGPD')
<x-store-layout>
    <div class="mt-6 py-16 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-6">

            {{-- Botão Voltar --}}
            <a href="{{ route('shop.home') }}" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-black mb-8 inline-block transition-colors">
                ← Voltar para a Loja
            </a>

            {{-- Título da Página --}}
            <h1 class="text-3xl md:text-4xl font-black text-gray-900 mb-10 uppercase tracking-tighter text-center">
                Privacidade e <span class="text-[#B8860B]">LGPD</span>
            </h1>

            {{-- Container Principal --}}
            <div class="bg-white p-8 md:p-14 rounded-[3rem] shadow-xl border border-gray-100 space-y-12">

                {{-- Seção 1 --}}
                <section>
                    <h2 class="text-xs font-black text-gray-900 mb-4 uppercase tracking-[0.3em] border-b border-gray-100 pb-4">
                        1. Nosso Compromisso
                    </h2>
                    <p class="text-sm text-gray-600 leading-relaxed font-medium text-justify">
                        A <strong>K'enzza Professional</strong> leva a sua privacidade a sério. Esta política descreve como coletamos, usamos, armazenamos e protegemos os seus dados pessoais, em total conformidade com a Lei Geral de Proteção de Dados (Lei nº 13.709/2018 - LGPD). Nosso objetivo é garantir transparência e segurança em todas as suas interações com a nossa plataforma.
                    </p>
                </section>

                {{-- Seção 2 --}}
                <section>
                    <h2 class="text-xs font-black text-gray-900 mb-4 uppercase tracking-[0.3em] border-b border-gray-100 pb-4">
                        2. Dados Coletados
                    </h2>
                    <p class="text-sm text-gray-600 leading-relaxed font-medium mb-4 text-justify">
                        Coletamos apenas as informações estritamente necessárias para fornecer nossos produtos e serviços de forma eficiente:
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <span class="text-[#B8860B] mr-3 font-black">✓</span>
                            <span class="text-sm text-gray-600 font-medium"><strong>Dados Cadastrais:</strong> Nome completo, CPF/CNPJ (necessário para validação de profissionais), e-mail e telefone.</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-[#B8860B] mr-3 font-black">✓</span>
                            <span class="text-sm text-gray-600 font-medium"><strong>Dados de Entrega:</strong> Endereço completo, CEP e dados complementares para despacho.</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-[#B8860B] mr-3 font-black">✓</span>
                            <span class="text-sm text-gray-600 font-medium"><strong>Dados de Navegação:</strong> Cookies essenciais, endereço IP e páginas visitadas para melhorar sua experiência.</span>
                        </li>
                    </ul>
                </section>

                {{-- Seção 3 --}}
                <section>
                    <h2 class="text-xs font-black text-gray-900 mb-4 uppercase tracking-[0.3em] border-b border-gray-100 pb-4">
                        3. Finalidade do Uso
                    </h2>
                    <p class="text-sm text-gray-600 leading-relaxed font-medium text-justify">
                        Seus dados são utilizados exclusivamente para: processar e entregar seus pedidos; validar seu nível de acesso (como salão/profissional) para aplicação de tabelas de preços específicas; enviar atualizações sobre o status da compra; cumprir obrigações legais e fiscais (emissão de notas); e, caso você autorize, enviar ofertas e novidades da nossa marca.
                    </p>
                </section>

                {{-- Seção 4 --}}
                <section>
                    <h2 class="text-xs font-black text-gray-900 mb-4 uppercase tracking-[0.3em] border-b border-gray-100 pb-4">
                        4. Compartilhamento de Dados
                    </h2>
                    <p class="text-sm text-gray-600 leading-relaxed font-medium text-justify">
                        A K'enzza não vende ou aluga seus dados. O compartilhamento ocorre apenas com parceiros essenciais para a operação, tais como: gateways de pagamento (como InfinitePay) para processamento seguro de cartões e PIX; transportadoras e Correios para viabilizar a entrega; e fornecedores de infraestrutura de TI e hospedagem, todos submetidos a rigorosos contratos de confidencialidade.
                    </p>
                </section>

                {{-- Seção 5 --}}
                <section>
                    <h2 class="text-xs font-black text-gray-900 mb-4 uppercase tracking-[0.3em] border-b border-gray-100 pb-4">
                        5. Seus Direitos (Titular dos Dados)
                    </h2>
                    <p class="text-sm text-gray-600 leading-relaxed font-medium mb-4 text-justify">
                        Você tem controle total sobre suas informações. A qualquer momento, você pode solicitar:
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                            <p class="text-[10px] font-black text-gray-900 uppercase tracking-widest mb-1">Acesso e Correção</p>
                            <p class="text-xs text-gray-500 font-medium">Visualizar e atualizar dados incompletos ou desatualizados em seu painel.</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                            <p class="text-[10px] font-black text-gray-900 uppercase tracking-widest mb-1">Anonimização ou Exclusão</p>
                            <p class="text-xs text-gray-500 font-medium">Remover dados desnecessários, exceto aqueles exigidos por lei (ex: Notas Fiscais).</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                            <p class="text-[10px] font-black text-gray-900 uppercase tracking-widest mb-1">Portabilidade</p>
                            <p class="text-xs text-gray-500 font-medium">Solicitar a transferência dos seus dados para outro fornecedor.</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                            <p class="text-[10px] font-black text-gray-900 uppercase tracking-widest mb-1">Revogação</p>
                            <p class="text-xs text-gray-500 font-medium">Retirar o consentimento para o recebimento de e-mails de marketing.</p>
                        </div>
                    </div>
                </section>

                {{-- Seção 6 --}}
                <section>
                    <h2 class="text-xs font-black text-gray-900 mb-4 uppercase tracking-[0.3em] border-b border-gray-100 pb-4">
                        6. Segurança
                    </h2>
                    <p class="text-sm text-gray-600 leading-relaxed font-medium text-justify">
                        Adotamos medidas técnicas, administrativas e organizacionais para proteger seus dados contra acessos não autorizados, perdas ou alterações. Utilizamos criptografia e certificados SSL, e nossos servidores operam sob rigorosos padrões de segurança.
                    </p>
                </section>

                {{-- Seção 7 --}}
                <section class="bg-black text-white p-8 rounded-3xl">
                    <h2 class="text-xs font-black text-[#B8860B] mb-4 uppercase tracking-[0.3em]">
                        7. Contato e Encarregado (DPO)
                    </h2>
                    <p class="text-sm text-gray-300 leading-relaxed font-medium mb-6">
                        Se você tiver dúvidas sobre esta política ou desejar exercer seus direitos, entre em contato com nosso Encarregado de Proteção de Dados:
                    </p>
                    <div class="space-y-2">
                        <p class="text-xs font-bold uppercase tracking-wider text-white"><span class="text-gray-500 mr-2">E-mail:</span> privacidade@kenzza.com.br</p>

                    </div>
                </section>

            </div>

            <p class="text-center text-[10px] font-bold text-gray-400 mt-8 uppercase tracking-widest">
                Última atualização: {{ date('d/m/Y') }}
            </p>

        </div>
    </div>
</x-store-layout>
