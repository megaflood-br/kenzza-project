@section('title', 'Parceiro')
<x-store-layout>
    <div class="py-20 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto px-6 lg:px-10">

            {{-- Cabeçalho Principal --}}
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-[10px] bg-black text-[#c5a059] px-4 py-1.5 rounded-full font-black uppercase tracking-[0.2em]">
                    Tabelas de Acesso K'enzza
                </span>
                <h1 class="text-4xl md:text-5xl font-black text-gray-900 mt-6 uppercase tracking-tighter leading-none">
                    Uso Pessoal ou <span class="text-[#B8860B]">Compra de Salão</span>
                </h1>
                <p class="text-sm text-gray-500 font-medium uppercase mt-4 tracking-wider">
                    Saiba como funciona a liberação de preços exclusivos para profissionais da beleza.
                </p>
            </div>

            {{-- Cards de Modalidades --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-14">

                {{-- Card Consumidor --}}
                <div class="bg-white p-10 rounded-[3rem] shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all">
                    <div>
                        <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center mb-6">
                            <span class="text-lg">✨</span>
                        </div>
                        <h3 class="text-2xl font-black text-gray-900 uppercase tracking-tight">Consumidor Final</h3>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Uso Pessoal / CPF</p>

                        <p class="text-sm text-gray-500 mt-6 leading-relaxed">
                            Acesso imediato ao nosso portfólio completo de tratamento home care para cuidar dos fios com o padrão de excelência K'enzza no dia a dia. Compras simplificadas utilizando apenas o seu CPF.
                        </p>

                        <ul class="mt-8 space-y-3 text-xs font-bold uppercase text-gray-700 tracking-wider">
                            <li class="flex items-center gap-3 text-green-600">✓ Sem quantidade mínima de compra</li>
                            <li class="flex items-center gap-3">✓ Preços padrão de varejo para uso pessoal</li>
                            <li class="flex items-center gap-3">✓ Liberação imediata de cadastro na loja</li>
                        </ul>
                    </div>

                    <div class="mt-10">
                        <a href="{{ route('register') }}" class="block text-center bg-black text-white text-xs font-black uppercase tracking-widest py-4 rounded-2xl hover:bg-[#B8860B] transition-all">
                            Criar Cadastro CPF
                        </a>
                    </div>
                </div>

                {{-- Card Salão --}}
                <div class="bg-white p-10 rounded-[3rem] shadow-xl border-2 border-[#B8860B] flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute top-0 right-0 bg-[#B8860B] text-white text-[8px] font-black uppercase tracking-widest px-6 py-2 rounded-bl-2xl">
                        CNPJ ou Certificado
                    </div>

                    <div>
                        <div class="w-12 h-12 bg-amber-50 text-[#B8860B] rounded-2xl flex items-center justify-center mb-6">
                            <span class="text-lg">💼</span>
                        </div>
                        <h3 class="text-2xl font-black text-gray-900 uppercase tracking-tight">Compra de Salão</h3>
                        <p class="text-xs font-bold text-[#B8860B] uppercase tracking-widest mt-1">Uso Profissional</p>

                        <p class="text-sm text-gray-500 mt-6 leading-relaxed">
                            Modalidade exclusiva para profissionais e empresas do setor da beleza. Ao possuir o cadastro de Salão ativo, nossa plataforma aplica automaticamente a **Tabela com Desconto Maior** direto nos produtos.
                        </p>

                        <ul class="mt-6 space-y-3 text-xs font-bold uppercase text-gray-700 tracking-wider">
                            <li class="flex items-center gap-3 text-[#B8860B]">✓ Maior desconto disponível na plataforma</li>
                            <li class="flex items-center gap-3">✓ Visualização automática dos preços reduzidos</li>
                            <li class="flex items-center gap-3">✓ Liberação via CNPJ ou Certificado Profissional</li>
                        </ul>
                    </div>

                    <div class="mt-8 space-y-3">
                        <a href="{{ route('register') }}?type=salon" class="block text-center bg-[#B8860B] text-white text-xs font-black uppercase tracking-widest py-4 rounded-2xl hover:bg-black transition-all shadow-md">
                            Cadastrar com CNPJ de Salão
                        </a>

                        {{-- Alternativa para quem não tem CNPJ --}}
                        <a href="https://wa.me/5511937525151?text=Olá!%20Sou%20cabeleireiro%20autônomo%20e%20não%20possuo%20CNPJ.%20Gostaria%20de%20enviar%20meu%20certificado%20para%20liberar%20o%20desconto%20de%20salão."
                           target="_blank"
                           class="block text-center bg-transparent border-2 border-green-500 text-green-600 text-xs font-black uppercase tracking-widest py-3.5 rounded-2xl hover:bg-green-50 transition-all">
                            💬 Não tenho CNPJ (Validar por Certificado)
                        </a>
                    </div>
                </div>
            </div>

            {{-- Texto de Apoio sobre o Suporte --}}
            <p class="text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-20">
                * Nota: Para a validação via WhatsApp, certifique-se de já ter criado uma conta padrão com seu CPF antes de enviar o comprovante técnico.
            </p>

            {{-- Tabela Comparativa Rápida --}}
            <div class="bg-white rounded-[3rem] p-8 md:p-12 shadow-sm border border-gray-100 mb-20">
                <h3 class="text-xs font-black text-gray-900 mb-8 uppercase tracking-[0.3em] text-center md:text-left">
                    Regras de Faturamento por Perfil
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                <th class="pb-4">Critério</th>
                                <th class="pb-4 text-center">Consumidor</th>
                                <th class="pb-4 text-center text-[#B8860B]">Compra de Salão</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs font-bold uppercase tracking-wider text-gray-700 divide-y divide-gray-50">
                            <tr>
                                <td class="py-4 font-black text-gray-900">Documento / Comprovante</td>
                                <td class="py-4 text-center font-medium">CPF</td>
                                <td class="py-4 text-center text-[#B8860B] font-black">CNPJ Ativo ou Certificado Profissional</td>
                            </tr>
                            <tr>
                                <td class="py-4 font-black text-gray-900">Desconto Aplicado</td>
                                <td class="py-4 text-center text-gray-400 font-medium">Preço de Tabela</td>
                                <td class="py-4 text-center text-green-600">Desconto Maior Automático</td>
                            </tr>
                            <tr>
                                <td class="py-4 font-black text-gray-900">Linhas Técnicas</td>
                                <td class="py-4 text-center text-gray-300">Restrito</td>
                                <td class="py-4 text-center text-green-600">Totalmente Liberado</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Dúvidas Frequentes --}}
            <div class="max-w-3xl mx-auto" x-data="{ active: null }">
                <h3 class="text-xs font-black text-gray-900 mb-8 uppercase tracking-[0.3em] text-center">
                    Perguntas Frequentes
                </h3>

                <div class="space-y-4">
                    <div class="bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-sm">
                        <button @click="active = (active === 1 ? null : 1)" class="w-full p-6 text-left flex justify-between items-center focus:outline-none">
                            <span class="text-xs font-black uppercase text-gray-900 tracking-wider">Como o desconto de salão é ativado na minha conta?</span>
                            <span class="text-[#B8860B] font-black text-base" x-text="active === 1 ? '−' : '+'"></span>
                        </button>
                        <div x-show="active === 1" x-transition class="px-6 pb-6 text-sm text-gray-500 font-medium leading-relaxed border-t border-gray-50 pt-4">
                            Se você possuir CNPJ, a ativação é feita diretamente no formulário de cadastro. Caso seja profissional autônomo, basta criar a conta básica com seu CPF e clicar no botão do WhatsApp para enviar uma foto do seu certificado de formação técnica. Nossa equipe fará o upgrade do seu perfil manualmente.
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-sm">
                        <button @click="active = (active === 2 ? null : 2)" class="w-full p-6 text-left flex justify-between items-center focus:outline-none">
                            <span class="text-xs font-black uppercase text-gray-900 tracking-wider">Quais documentos servem como comprovante de atuação profissional?</span>
                            <span class="text-[#B8860B] font-black text-base" x-text="active === 2 ? '−' : '+'"></span>
                        </button>
                        <div x-show="active === 2" x-transition class="px-6 pb-6 text-sm text-gray-500 font-medium leading-relaxed border-t border-gray-50 pt-4">
                            Aceitamos certificados de conclusão de cursos de cabeleireiro, colorimetria, especializações técnicas do setor capilar, diplomas de beleza em geral ou comprovação de MEI ativo vinculado à área de estética e beleza.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-store-layout>
