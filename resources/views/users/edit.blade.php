<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Usuário') }}: {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- BLOCO DE ALERTA PARA ERROS DE BANCO DE DADOS (Ex: CPF Duplicado) --}}
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm">
                    <p class="text-red-700 font-bold text-sm">{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-8">
                    <form method="POST" action="{{ route('users.update', $user->id) }}" class="max-w-md space-y-6">
                        @csrf
                        @method('PATCH')

                        {{-- Nome Completo --}}
                        <div>
                            <x-input-label for="name" value="Nome Completo" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        {{-- E-mail --}}
                        <div>
                            <x-input-label for="email" value="Endereço de E-mail" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        {{-- Nível de Acesso (TRAVA VISUAL PARA O MANAGER) --}}
                        <div>
                            <x-input-label for="role" value="Nível de Acesso" />

                            @if(auth()->user()->role === 'manager')
                                {{-- Visão bloqueada para a Gerente --}}
                                <input type="hidden" name="role" value="representative">
                                <x-text-input type="text" value="Representante Comercial" class="mt-1 block w-full bg-gray-100 text-gray-500 cursor-not-allowed" disabled />
                                <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-widest font-bold">Você só possui permissão para gerenciar Representantes.</p>
                            @else
                                {{-- Visão completa para Admin/Editor --}}
                                <select name="role" id="role" class="mt-1 block w-full border-gray-300 focus:border-[#B8860B] focus:ring-[#B8860B] rounded-md shadow-sm">
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrador</option>
                                    <option value="editor" {{ old('role', $user->role) == 'editor' ? 'selected' : '' }}>Editor</option>
                                    <option value="distributor" {{ old('role', $user->role) == 'distributor' ? 'selected' : '' }}>Distribuidor</option>
                                    <option value="salon" {{ old('role', $user->role) == 'salon' ? 'selected' : '' }}>Salão / Profissional</option>
                                    <option value="consumer" {{ old('role', $user->role) == 'consumer' ? 'selected' : '' }}>Consumidor</option>
                                    <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Gerente (Comercial)</option>
                                    <option value="representative" {{ old('role', $user->role) == 'representative' ? 'selected' : '' }}>Representante</option>
                                </select>
                            @endif
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        {{-- Campo Documento (CPF/CNPJ) --}}
                        <div>
                            <x-input-label for="document" value="CPF ou CNPJ (Somente números)" />
                            <x-text-input id="document" name="document" type="text" class="mt-1 block w-full {{ session('error') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}" :value="old('document', $user->document)" required />
                            <p class="text-[10px] text-gray-500 mt-1 uppercase">Essencial para definir o preço Profissional (Salão).</p>
                            <x-input-error :messages="$errors->get('document')" class="mt-2" />
                        </div>

                        {{-- Campo Telefone --}}
                        <div>
                            <x-input-label for="telefone" value="Telefone / WhatsApp" />
                            <x-text-input id="telefone" name="telefone" type="text" class="mt-1 block w-full" :value="old('telefone', $user->telefone ?? $user->phone)" />
                            <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
                        </div>

                        {{-- Seção de Senha --}}
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <p class="text-[10px] text-gray-500 mb-4 uppercase font-bold tracking-widest">Alterar Senha (Deixe em branco para manter a atual)</p>

                            <div>
                                <x-input-label for="password" value="Nova Senha" />
                                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="password_confirmation" value="Confirmar Nova Senha" />
                                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" />
                            </div>
                        </div>

                        {{-- Botões --}}
                        <div class="flex items-center gap-4 pt-4">
                            <x-primary-button>Salvar Alterações</x-primary-button>
                            <a href="{{ route('users.index') }}" class="text-sm text-gray-600 hover:underline font-bold">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
