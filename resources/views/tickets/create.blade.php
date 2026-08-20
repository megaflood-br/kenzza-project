<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Abrir Novo Chamado') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-100 p-8">
                <form action="{{ route('tickets.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="subject" value="Assunto / Título do Problema" />
                        <x-text-input id="subject" name="subject" type="text" class="mt-1 block w-full" placeholder="Ex: Problema com pedido #123" required />
                    </div>

                    <div>
                        <x-input-label for="priority" value="Urgência" />
                        <select id="priority" name="priority" class="mt-1 block w-full border-gray-300 focus:border-[#c5a059] focus:ring-[#c5a059] rounded-xl shadow-sm">
                            <option value="low">Baixa - Dúvidas gerais</option>
                            <option value="medium" selected>Média - Problemas técnicos</option>
                            <option value="high">Alta - Erro em pagamento ou entrega</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="message" value="Descrição Detalhada" />
                        <textarea id="message" name="message" rows="5" class="mt-1 block w-full border-gray-300 focus:border-[#c5a059] focus:ring-[#c5a059] rounded-xl shadow-sm" placeholder="Descreva o que está a acontecer..." required></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('tickets.index') }}" class="text-sm text-gray-500 hover:text-black">Cancelar</a>
                        <button type="submit" class="bg-black text-[#c5a059] px-8 py-3 rounded-xl font-bold uppercase text-xs hover:bg-[#c5a059] hover:text-black transition shadow-xl">
                            Enviar Chamado
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
