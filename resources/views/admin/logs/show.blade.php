<x-app-layout>
    <div class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="flex justify-between items-center mb-6 border-b pb-4">
                    <h1 class="text-xl font-bold text-gray-900 uppercase tracking-wider">Detalhes do Registro de Auditoria</h1>
                    <a href="{{ route('admin.logs.index') }}" class="text-xs font-black uppercase tracking-widest text-gray-500 hover:text-black transition">
                        &larr; Voltar para Lista
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 text-sm">
                    <div>
                        <p class="text-gray-500 font-bold uppercase text-[11px]">Responsável</p>
                        <p class="font-semibold text-gray-900">{{ $log->user ? $log->user->name : 'Sistema / Webhook' }} ({{ $log->user ? $log->user->email : 'N/A' }})</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-bold uppercase text-[11px]">Data e Hora</p>
                        <p class="font-mono text-gray-900">{{ $log->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-bold uppercase text-[11px]">Navegador / Dispositivo</p>
                        <p class="text-gray-700 text-xs truncate" title="{{ $log->user_agent }}">{{ $log->user_agent }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-bold uppercase text-[11px]">Tabela Alvo (Model)</p>
                        <p class="font-mono text-gray-700 text-xs">{{ $log->auditable_type }} (ID: {{ $log->auditable_id }})</p>
                    </div>
                </div>

                <div class="mb-6 p-4 bg-gray-100 rounded-lg border">
                    <p class="text-gray-500 font-bold uppercase text-[11px] mb-1">Ação Executada</p>
                    <p class="text-base font-semibold text-gray-900">{{ $log->description }}</p>
                </div>

                {{-- Painel de Comparação de Dados (JSON) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-xs font-black uppercase text-red-600 tracking-wider mb-2">Dados Anteriores (Antes)</h3>
                        <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto font-mono text-xs shadow-inner h-64 border border-gray-800"><code>{{ $log->old_values ? json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : 'Nenhum dado anterior registrado (Criação)' }}</code></pre>
                    </div>
                    <div>
                        <h3 class="text-xs font-black uppercase text-green-600 tracking-wider mb-2">Dados Atuais (Depois)</h3>
                        <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto font-mono text-xs shadow-inner h-64 border border-gray-800"><code>{{ $log->new_values ? json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : 'Nenhum novo dado registrado (Exclusão)' }}</code></pre>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
