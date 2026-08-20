<div x-data="{
        showCookieBanner: false,
        init() {
            // Verifica se o usuário já tomou uma decisão anteriormente
            if (!localStorage.getItem('kenzza_cookie_consent')) {
                // Pequeno atraso para uma entrada mais suave
                setTimeout(() => {
                    this.showCookieBanner = true;
                }, 1000);
            }
        },
        acceptCookies() {
            localStorage.setItem('kenzza_cookie_consent', 'accepted');
            this.showCookieBanner = false;
        },
        declineCookies() {
            localStorage.setItem('kenzza_cookie_consent', 'essential_only');
            this.showCookieBanner = false;
        }
    }"
    x-show="showCookieBanner"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0 translate-y-10"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-10"
    style="display: none;"
    class="fixed bottom-0 left-0 right-0 z-[100] p-4 pointer-events-none sm:p-6 lg:p-8"
>
    {{-- Layout flutuante arredondado --}}
    <div class="max-w-screen-xl mx-auto pointer-events-auto">
        <div class="bg-gray-900 rounded-[2rem] shadow-2xl border border-gray-800 p-6 sm:p-8 flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">

            {{-- Detalhe dourado decorativo --}}
            <div class="absolute top-0 left-0 w-2 h-full bg-[#B8860B]"></div>

            <div class="flex-1">
                <h3 class="text-white font-black uppercase tracking-widest text-xs mb-2">Privacidade e Cookies</h3>
                <p class="text-gray-400 text-sm font-medium leading-relaxed">
                    Utilizamos cookies para melhorar sua experiência de navegação, personalizar conteúdos e analisar nosso tráfego. Ao continuar, você concorda com o uso de cookies e com nossa
                    <a href="/lgpd" class="text-[#B8860B] hover:text-white font-bold transition-colors underline decoration-gray-700 underline-offset-4">Política de Privacidade e LGPD</a>.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto shrink-0">
                <button @click="declineCookies()" type="button" class="px-6 py-3 rounded-full text-xs font-black uppercase tracking-widest text-white border-2 border-gray-700 hover:border-gray-500 hover:bg-gray-800 transition-all text-center">
                    Apenas Essenciais
                </button>

                <button @click="acceptCookies()" type="button" class="px-6 py-3 rounded-full text-xs font-black uppercase tracking-widest text-black bg-[#B8860B] hover:bg-white transition-all text-center shadow-[0_0_15px_rgba(184,134,11,0.3)]">
                    Aceitar Todos
                </button>
            </div>
        </div>
    </div>
</div>
