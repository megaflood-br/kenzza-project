<x-store-layout>
    <div class="py-12 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <h2 class="text-4xl font-black text-black uppercase tracking-tighter">
                    Olá, <span class="text-[#B8860B]">{{ explode(' ', Auth::user()->name)[0] }}</span>
                </h2>
                <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mt-2">Bem-vindo à sua conta exclusiva K'enzza</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <a href="{{ route('customer.orders') }}" class="group bg-white p-10 rounded-[2.5rem] shadow-sm border border-gray-100 hover:border-[#B8860B] transition-all duration-500">
                    <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#B8860B] transition-colors">
                        <svg class="w-6 h-6 text-[#B8860B] group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-black uppercase tracking-tighter">Meus Pedidos</h3>
                    <p class="text-gray-500 text-sm mt-2">Acompanhe suas compras e entregas.</p>
                </a>

                <a href="{{ route('profile.edit') }}" class="group bg-white p-10 rounded-[2.5rem] shadow-sm border border-gray-100 hover:border-[#B8860B] transition-all duration-500">
                    <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#B8860B] transition-colors">
                        <svg class="w-6 h-6 text-[#B8860B] group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-black uppercase tracking-tighter">Dados Pessoais</h3>
                    <p class="text-gray-500 text-sm mt-2">Gerencie suas informações e senhas.</p>
                </a>

                <a href="{{ route('tickets.index') }}" class="group bg-white p-10 rounded-[2.5rem] shadow-sm border border-gray-100 hover:border-[#B8860B] transition-all duration-500">
                    <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#B8860B] transition-colors">
                        <svg class="w-6 h-6 text-[#B8860B] group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-black uppercase tracking-tighter">Suporte</h3>
                    <p class="text-gray-500 text-sm mt-2">Dúvidas ou ajuda com seu pedido.</p>
                </a>
            </div>
        </div>
    </div>
</x-store-layout>
