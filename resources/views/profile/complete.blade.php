<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Finalizar seu Cadastro de Distribuidor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-200">
                <div class="p-8">
                    <div class="mb-8 text-center">
                        <div class="flex justify-center mb-4">
                            <span class="bg-[#c5a059]/10 text-[#c5a059] p-3 rounded-full">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                            </span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Quase lá!</h3>
                        <p class="text-gray-500 mt-2">Para sua segurança e emissão de notas fiscais via ContaAzul, precisamos que complete os dados abaixo.</p>
                    </div>

                    @if ($errors->any() && !$errors->has('document'))
                        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 text-red-700">
                            <ul class="list-disc list-inside text-sm font-medium">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('profile.update-complete') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="document" value="CPF ou CNPJ *" />
                                <x-text-input id="document" name="document" type="text" class="mt-1 block w-full {{ $errors->has('document') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}" :value="old('document')" required />

                                {{-- AQUI ESTÁ O BLOCO DE ERRO ESPECÍFICO PARA O CPF --}}
                                @error('document')
                                    <p class="text-red-500 text-xs font-bold mt-2 uppercase tracking-wide">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <x-input-label for="phone" value="WhatsApp *" />
                                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone')" required />
                            </div>
                        </div>

                        <hr class="border-gray-100">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="zip_code" value="CEP *" />
                                <x-text-input id="zip_code" name="zip_code" type="text" class="mt-1 block w-full" :value="old('zip_code')" required onblur="buscarCep(this.value)" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="street" value="Endereço *" />
                                <x-text-input id="street" name="street" type="text" class="mt-1 block w-full bg-gray-50" :value="old('street')" required readonly />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="number" value="Número *" />
                                <x-text-input id="number" name="number" type="text" class="mt-1 block w-full" :value="old('number')" required />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="complement" value="Complemento" />
                                <x-text-input id="complement" name="complement" type="text" class="mt-1 block w-full" :value="old('complement')" />
                            </div>
                        </div>

                        <input type="hidden" id="neighborhood" name="neighborhood" value="{{ old('neighborhood') }}">
                        <input type="hidden" id="city" name="city" value="{{ old('city') }}">
                        <input type="hidden" id="state" name="state" value="{{ old('state') }}">

                        <div id="wrapper-endereco" class="hidden bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <p class="text-xs font-bold text-[#c5a059] uppercase tracking-widest mb-1">Localização Identificada:</p>
                            <p class="text-sm text-gray-600" id="endereco-texto"></p>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="w-full bg-black text-[#c5a059] py-4 rounded-xl font-extrabold uppercase tracking-widest text-xs hover:bg-[#c5a059] hover:text-black transition-all shadow-xl">
                                Salvar e Acessar Painel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/imask"></script>
    <script>
        // Máscaras
        IMask(document.getElementById('document'), {
            mask: [{ mask: '000.000.000-00', type: 'CPF' }, { mask: '00.000.000/0000-00', type: 'CNPJ' }]
        });
        IMask(document.getElementById('phone'), { mask: '(00) 00000-0000' });
        IMask(document.getElementById('zip_code'), { mask: '00000-000' });

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
                            document.getElementById('wrapper-endereco').classList.remove('hidden');
                            document.getElementById('number').focus();
                        } else {
                            alert("CEP não encontrado.");
                        }
                    });
            }
        }
    </script>
</x-app-layout>
