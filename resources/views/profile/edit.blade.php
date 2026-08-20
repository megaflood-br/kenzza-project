<x-store-layout>
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">



            {{-- Informações Básicas --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Dados Fiscais e Endereço --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-fiscal-information-form')
                </div>
            </div>

            {{-- Alterar Senha --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- SEÇÃO DE NOTIFICAÇÕES ATUALIZADA (OneSignal) --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg mb-6">
                <header>
                    <h2 class="text-lg font-medium text-gray-900">Notificações Push</h2>
                    <p class="mt-1 text-sm text-gray-600">Gerencie como você recebe avisos de pedidos e novidades diretamente no seu dispositivo.</p>
                </header>

                <div class="mt-6 flex items-center gap-4">
                    <button id="onesignal-btn" onclick="togglePushSubscription()"
                        class="inline-flex items-center px-6 py-3 bg-black border border-transparent rounded-full font-black text-[10px] text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150 shadow-lg">
                        VERIFICANDO...
                    </button>
                    <span id="push-status-text" class="text-[10px] font-black uppercase text-gray-400 tracking-widest italic"></span>
                </div>
            </div>

            {{-- Excluir Conta --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>

    {{-- Script de Controle do OneSignal --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            window.OneSignalDeferred = window.OneSignalDeferred || [];
            OneSignalDeferred.push(function(onesignal) {
                // Escuta mudanças de inscrição para sincronizar com o banco
                onesignal.User.PushSubscription.addEventListener("change", function(event) {
                    syncPushStatusWithLaravel(event.current.optedIn);
                });
                updatePushUI();
            });
        });

        async function updatePushUI() {
            try {
                const isSubscribed = OneSignal.User.PushSubscription.optedIn;
                const btn = document.getElementById('onesignal-btn');
                const statusText = document.getElementById('push-status-text');

                if (!btn) return;

                if (isSubscribed) {
                    btn.innerText = "DESATIVAR NOTIFICAÇÕES";
                    btn.style.backgroundColor = "#dc2626"; // red-600
                    statusText.innerText = "Ativo neste dispositivo";
                } else {
                    btn.innerText = "ATIVAR NOTIFICAÇÕES";
                    btn.style.backgroundColor = "#000000"; // Black
                    statusText.innerText = "Inativo";
                }
            } catch (e) {
                console.error("Erro OneSignal UI:", e);
            }
        }

        async function togglePushSubscription() {
            console.log("Iniciando toggle...");
            const btn = document.getElementById('onesignal-btn');

            // Verifica se o objeto OneSignal existe
            if (typeof OneSignal === 'undefined') {
                console.error("SDK do OneSignal não encontrado!");
                alert("Erro: O bloqueador de anúncios ou o navegador impediu o OneSignal.");
                return;
            }

            const isSubscribed = OneSignal.User.PushSubscription.optedIn;
            console.log("Status atual de inscrição:", isSubscribed);

            btn.innerText = "PROCESSANDO...";
            btn.disabled = true;

            try {
                if (isSubscribed) {
                    console.log("Tentando desativar (optOut)...");
                    await OneSignal.User.PushSubscription.optOut();
                } else {
                    console.log("Tentando ativar (optIn)...");
                    // Isso força a abertura do popup de permissão do Chrome
                    await OneSignal.Slidedown.promptPush();
                    await OneSignal.User.PushSubscription.optIn();
                }

                // Após mudar no OneSignal, avisamos o seu servidor na Hostinger
                const novoStatus = OneSignal.User.PushSubscription.optedIn;
                console.log("Novo status obtido:", novoStatus);
                await syncPushStatusWithLaravel(novoStatus);

            } catch (e) {
                console.error("Falha no processo:", e);
                alert("Ocorreu um erro ao processar. Verifique se as notificações estão bloqueadas na barra de endereços do seu navegador.");
            } finally {
                btn.disabled = false;
                updatePushUI();
            }
        }

        // Função para avisar o seu Controller na Hostinger sobre a mudança
        async function syncPushStatusWithLaravel(status) {
            try {
                await fetch('{{ route('admin.notifications.toggle') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status: status })
                });
            } catch (e) {
                console.error("Erro ao sincronizar com servidor.");
            }
        }
    </script>
</x-store-layout>
