<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cadastrar Novo Usuário') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-8">
                    <form method="POST" action="{{ route('users.store') }}" class="max-w-md space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="name" value="Nome Completo" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" value="Endereço de E-mail" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="role" value="Nível de Acesso" />

                            {{-- TRAVA VISUAL: Se for a gerente, o campo fica bloqueado no visual --}}
                            @if(auth()->user()->role === 'manager')
                                <input type="hidden" name="role" value="representative">
                                <x-text-input type="text" value="Representante Comercial" class="mt-1 block w-full bg-gray-100 text-gray-500 cursor-not-allowed" disabled />
                                <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-widest font-bold">Você só possui permissão para criar Representantes.</p>
                            @else
                                <select name="role" id="role" class="border-gray-300 focus:border-[#B8860B] focus:ring-[#B8860B] rounded-md shadow-sm mt-1 block w-full">
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                    <option value="editor" {{ old('role') == 'editor' ? 'selected' : '' }}>Editor</option>
                                    <option value="distributor" {{ old('role') == 'distributor' ? 'selected' : '' }}>Distribuidor</option>
                                    <option value="salon" {{ old('role') == 'salon' ? 'selected' : '' }}>Salão</option>
                                    <option value="consumer" {{ old('role') == 'consumer' ? 'selected' : '' }}>Consumidor</option>
                                    <option value="manager" {{ (isset($user) && $user->role === 'manager') || old('role') === 'manager' ? 'selected' : '' }}>Gerente (Comercial)</option>
                                    <option value="representative" {{ (isset($user) && $user->role === 'representative') || old('role') === 'representative' ? 'selected' : '' }}>Representante</option>
                                </select>
                            @endif
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password" value="Senha" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" value="Confirmar Senha" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                        </div>

                        <div class="flex items-center gap-4 pt-4">
                            <x-primary-button>Criar Acesso</x-primary-button>
                            <a href="{{ route('users.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
