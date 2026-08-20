<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">Dados Fiscais e Endereço</h2>
        <p class="mt-1 text-sm text-gray-600">Atualize suas informações para faturamento e integração com ContaAzul.</p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="document" value="CPF ou CNPJ" />
                <x-text-input id="document" name="document" type="text" class="mt-1 block w-full {{ $errors->has('document') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}" :value="old('document', $user->document)" />

                {{-- AQUI ESTÁ O BLOCO DE ERRO DE DUPLICIDADE --}}
                @error('document')
                    <p class="text-red-500 text-xs font-bold mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-input-label for="phone" value="Telefone/WhatsApp" />
                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <x-input-label for="zip_code" value="CEP" />
                <x-text-input id="zip_code" name="zip_code" type="text" class="mt-1 block w-full" :value="old('zip_code', $user->zip_code)" onblur="buscarCep(this.value)" />
            </div>
            <div class="md:col-span-2">
                <x-input-label for="street" value="Rua/Avenida" />
                <x-text-input id="street" name="street" type="text" class="mt-1 block w-full bg-gray-50 text-gray-500" :value="old('street', $user->street)" readonly />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <x-input-label for="number" value="Número" />
                <x-text-input id="number" name="number" type="text" class="mt-1 block w-full" :value="old('number', $user->number)" />
            </div>
            <div class="md:col-span-2">
                <x-input-label for="complement" value="Complemento" />
                <x-text-input id="complement" name="complement" type="text" class="mt-1 block w-full" :value="old('complement', $user->complement)" />
            </div>
        </div>

        <input type="hidden" id="neighborhood" name="neighborhood" value="{{ old('neighborhood', $user->neighborhood) }}">
        <input type="hidden" id="city" name="city" value="{{ old('city', $user->city) }}">
        <input type="hidden" id="state" name="state" value="{{ old('state', $user->state) }}">

        <p class="text-xs text-[#c5a059] font-bold" id="endereco-texto">
            @if($user->city)
                {{ $user->neighborhood }}, {{ $user->city }} - {{ $user->state }}
            @endif
        </p>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Salvar Dados Fiscais') }}</x-primary-button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-gray-600">Salvo com sucesso.</p>
            @endif
        </div>
    </form>

    <script src="https://unpkg.com/imask"></script>
    <script>
        // Máscara dinâmica CPF/CNPJ
        const docMask = IMask(document.getElementById('document'), {
            mask: [
                { mask: '000.000.000-00', type: 'CPF' },
                { mask: '00.000.000/0000-00', type: 'CNPJ' }
            ]
        });

        // Máscara Celular
        const phoneMask = IMask(document.getElementById('phone'), {
            mask: '(00) 00000-0000'
        });

        // Máscara CEP
        const zipMask = IMask(document.getElementById('zip_code'), {
            mask: '00000-000'
        });

        function buscarCep(val) {
            const cep = val.replace(/\D/g, '');
            if (cep.length === 8) {
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(res => res.json())
                    .then(data => {
                        if (!data.erro) {
                            document.getElementById('street').value = data.logradouro;
                            document.getElementById('neighborhood').value = data.bairro;
                            document.getElementById('city').value = data.localidade;
                            document.getElementById('state').value = data.uf;
                            document.getElementById('endereco-texto').innerText = `${data.bairro}, ${data.localidade} - ${data.uf}`;
                        }
                    });
            }
        }
    </script>
</section>
