<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-black text-2xl text-gray-900 uppercase tracking-tighter">
                Disparo de <span class="text-[#c5a059]">Push</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-8 p-5 bg-green-50 border-l-4 border-green-500 text-green-700 font-bold rounded-2xl uppercase text-[10px] tracking-widest shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-[2rem] sm:rounded-[3rem] border border-gray-100 overflow-hidden">
                <div class="p-8 sm:p-12">

                    <div class="flex items-center gap-4 mb-10">
                        <div class="w-14 h-14 bg-black text-[#c5a059] rounded-2xl flex items-center justify-center shadow-inner shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-black text-xl text-gray-900 uppercase tracking-tighter">Nova Notificação</h3>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Envie alertas em tempo real para os dispositivos</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.notifications.send') }}" method="POST" class="space-y-6">
                        @csrf

                        <div>
                            <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-2 block mb-2">Título da Notificação *</label>
                            <input type="text" name="title" required placeholder="Ex: Promoção Exclusiva K'enzza!"
                                   class="w-full bg-gray-50 border-none rounded-2xl py-4 px-6 text-sm focus:ring-2 focus:ring-[#c5a059] font-bold text-gray-900 placeholder-gray-400 shadow-inner transition-all">
                        </div>

                        <div>
                            <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-2 block mb-2">Mensagem (Corpo) *</label>
                            <textarea name="message" rows="3" required placeholder="Descreva o que o usuário vai ler na tela do celular..."
                                      class="w-full bg-gray-50 border-none rounded-2xl py-4 px-6 text-sm focus:ring-2 focus:ring-[#c5a059] font-bold text-gray-900 placeholder-gray-400 shadow-inner transition-all resize-none"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-50 pt-6 mt-6">
                            <div>
                                <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-2 block mb-2">Público-Alvo</label>
                                <div class="relative">
                                    <select name="target_role" class="w-full bg-gray-50 border-none rounded-2xl py-4 pl-6 pr-10 text-sm focus:ring-2 focus:ring-[#c5a059] font-bold text-gray-900 shadow-inner cursor-pointer appearance-none transition-all">
                                        <option value="all">Todos os Usuários</option>
                                        <option value="consumer">Apenas Consumidores (Salões)</option>
                                        <option value="distributor">Apenas Distribuidores</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-[#c5a059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-2 block mb-2 flex justify-between items-center">
                                    Link de Destino
                                    <span class="text-gray-300 text-[8px]">(Opcional)</span>
                                </label>
                                <input type="url" name="url" placeholder="https://..."
                                       class="w-full bg-gray-50 border-none rounded-2xl py-4 px-6 text-sm focus:ring-2 focus:ring-[#c5a059] font-bold text-blue-600 placeholder-gray-400 shadow-inner transition-all">
                            </div>
                        </div>

                        <div class="pt-10 mt-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-6">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Ação irreversível
                            </p>
                            <button type="submit" class="w-full sm:w-auto bg-black text-[#c5a059] px-10 py-5 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-[#c5a059] hover:text-black transition-all shadow-xl border border-[#c5a059] flex items-center justify-center gap-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                Disparar Notificação
                            </button>
                        </div>
                    </form>

                </div>
            </div>

            <div class="h-20"></div>
        </div>
    </div>
</x-app-layout>
