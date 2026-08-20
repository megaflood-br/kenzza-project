<x-app-layout>
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-xl font-bold text-gray-900 uppercase tracking-wider">Logs de Auditoria do Sistema</h1>
                </div>

                {{-- Tabela de Registros --}}
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-800 text-white font-bold uppercase text-[11px] tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-left">Data/Hora</th>
                                <th class="px-4 py-3 text-left">Ação</th>
                                <th class="px-4 py-3 text-left">Descrição</th>
                                <th class="px-4 py-3 text-left">IP</th>
                                <th class="px-4 py-3 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($logs as $log)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-500 font-mono text-xs">
                                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($log->action === 'create')
                                            <span class="px-2 py-1 text-[10px] font-black rounded-full bg-green-100 text-green-800 uppercase">Criar</span>
                                        @elseif($log->action === 'update')
                                            <span class="px-2 py-1 text-[10px] font-black rounded-full bg-blue-100 text-blue-800 uppercase">Editar</span>
                                        @else
                                            <span class="px-2 py-1 text-[10px] font-black rounded-full bg-red-100 text-red-800 uppercase">Deletar</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-700 font-medium">
                                        {{ $log->description }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500 font-mono">
                                        {{ $log->ip_address }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <a href="{{ route('admin.logs.show', $log->id) }}" class="inline-flex items-center px-3 py-1 bg-black text-white text-xs font-black rounded-full hover:bg-gray-700 transition uppercase tracking-widest">
                                            Detalhes
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-400 font-medium uppercase tracking-wider text-xs">
                                        Nenhuma atividade registrada ainda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginação do Laravel --}}
                <div class="mt-4">
                    {{ $logs->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
