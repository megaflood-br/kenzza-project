@section('title', 'Meu Carrinho')
<x-store-layout>
    {{-- CORREÇÃO: Adicionado o ID master nesta div para o JavaScript nativo ler o escopo do Alpine sem erros --}}
    <div id="checkout-interaction-container" class="py-12 bg-gray-50 min-h-screen"
        x-data="{
            step: 1,
            isLogged: {{ auth()->check() ? 'true' : 'false' }},
            userHasAddress: {{ (auth()->check() && !empty(auth()->user()->cep ?? auth()->user()->zip_code)) ? 'true' : 'false' }},
            csrfToken: '{{ csrf_token() }}',

            /* Forma de Pagamento e Parcelas */
            paymentMethod: 'pix',
            installments: 1,
            interestRate: 0.0299, /* TAXA DE JUROS: 2.99% ao mês para compras a partir de 4x */

            /* Dados do Usuário Logado */
            userData: {
                name: '{!! auth()->check() ? addslashes(auth()->user()->name) : '' !!}',
                logradouro: '{!! auth()->check() ? addslashes(auth()->user()->logradouro ?? auth()->user()->address ?? auth()->user()->street) : '' !!}',
                numero: '{!! auth()->check() ? addslashes(auth()->user()->numero ?? auth()->user()->number) : '' !!}',
                bairro: '{!! auth()->check() ? addslashes(auth()->user()->neighborhood ?? auth()->user()->bairro ?? auth()->user()->district) : '' !!}',
                cidade: '{!! auth()->check() ? addslashes(auth()->user()->cidade ?? auth()->user()->city) : '' !!}',
                estado: '{!! auth()->check() ? addslashes(auth()->user()->estado ?? auth()->user()->state) : '' !!}',
                cep: '{!! auth()->check() ? addslashes(auth()->user()->cep ?? auth()->user()->zip_code ?? auth()->user()->postcode) : '' !!}'
            },

            /* Formulários Inline */
            loginEmail: '',
            loginPassword: '',
            loginError: '',

            addressForm: {
                cep: '',
                logradouro: '',
                numero: '',
                bairro: '',
                cidade: '',
                estado: ''
            },
            addressError: '',

            shipping: 0,
            shippingServiceId: '',
            shippingOptions: [],
            loading: false,
            cepExibicao: '{{ $cep ?? '' }}',
            subtotal: {{ array_sum(array_map(fn($item) => (float)$item['preco'] * (int)$item['quantidade'], session('cart', []))) }},

            /* Recupera o cupom */
            coupon: {
                tipo: '{!! session('coupon.tipo') ?? '' !!}',
                codigo: '{!! session('coupon.codigo') ?? '' !!}'
            },
            discountValue: {{ session('coupon.discount') ?? 0 }},

            get finalShipping() {
                if (this.coupon && this.coupon.tipo === 'frete_gratis') return 0;
                return parseFloat(this.shipping);
            },

            get total() {
                return (parseFloat(this.subtotal) - parseFloat(this.discountValue)) + this.finalShipping;
            },

            /* Função Matemática: Calcula Parcela com ou sem juros (Tabela Price) */
            calcInstallment(i) {
                let t = this.total;
                if (i <= 3) {
                    return t / i;
                } else {
                    let taxa = this.interestRate;
                    return t * (taxa * Math.pow(1 + taxa, i)) / (Math.pow(1 + taxa, i) - 1);
                }
            },

            /* Busca Automática ViaCEP */
            async buscarCepViaCep() {
                let cepLimpo = this.addressForm.cep.replace(/\D/g, '');

                if (cepLimpo.length > 5) {
                    this.addressForm.cep = cepLimpo.replace(/^(\d{5})(\d)/, '$1-$2');
                } else {
                    this.addressForm.cep = cepLimpo;
                }

                if (cepLimpo.length === 8) {
                    this.addressError = '';
                    try {
                        const response = await fetch(`https://viacep.com.br/ws/${cepLimpo}/json/`);
                        const data = await response.json();

                        if (!data.erro) {
                            this.addressForm.logradouro = data.logradouro;
                            this.addressForm.bairro = data.bairro;
                            this.addressForm.cidade = data.localidade;
                            this.addressForm.estado = data.uf;

                            this.$nextTick(() => {
                                document.getElementById('address_numero')?.focus();
                            });
                        } else {
                            this.addressError = 'CEP não encontrado. Preencha os campos manualmente.';
                        }
                    } catch (e) {
                        console.error('Erro ao buscar CEP na API externa.');
                    }
                }
            },

            async nextStep() {
                if (this.step === 1) {
                    if (this.isLogged) {
                        this.step = this.userHasAddress ? 4 : 3;
                    } else {
                        this.step = 2;
                    }
                    this.scrollinteraction();
                    return;
                }
                if (this.step === 2) {
                    if (this.isLogged) this.step = this.userHasAddress ? 4 : 3;
                    return;
                }
                if (this.step === 3) {
                    if (this.userHasAddress) {
                        this.step = 4;
                    } else {
                        alert('Por favor, salve seu endereço para continuar.');
                    }
                }
            },

            prevStep() {
                if (this.step === 4) {
                    this.step = 1;
                } else if (this.step === 3) {
                    this.step = this.isLogged ? 1 : 2;
                } else if (this.step === 2) {
                    this.step = 1;
                }
            },

            scrollinteraction() {
                this.$nextTick(() => {
                    let container = document.getElementById('checkout-interaction-container');
                    if (container) window.scrollTo({ top: container.offsetTop - 40, behavior: 'smooth' });
                });
            },

            async submitLogin() {
                this.loginError = '';
                if(!this.loginEmail || !this.loginPassword) {
                    this.loginError = 'Preencha todos os campos.';
                    return;
                }
                try {
                    const response = await fetch('{{ route('checkout.login.ajax') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ email: this.loginEmail, password: this.loginPassword })
                    });
                    const data = await response.json();

                    if(response.ok && data.sucesso) {
                        this.isLogged = true;
                        this.userData = data.user;

                        if(data.csrf) {
                            this.csrfToken = data.csrf;
                        }

                        let cepLimpo = data.user.cep ? data.user.cep.replace(/\D/g, '') : '';
                        this.userHasAddress = data.user.has_address || (cepLimpo.length === 8);

                        this.addressForm = {
                            cep: data.user.cep || '',
                            logradouro: data.user.logradouro || '',
                            numero: data.user.numero || '',
                            bairro: data.user.bairro || '',
                            cidade: data.user.cidade || '',
                            estado: data.user.estado || ''
                        };

                        if(cepLimpo.length === 8) {
                            this.cepExibicao = data.user.cep;
                            this.calculateShipping();
                        }

                        this.step = this.userHasAddress ? 4 : 3;
                        this.scrollinteraction();

                    } else {
                        this.loginError = data.erro || 'Falha na autenticação.';
                    }
                } catch(e) {
                    this.loginError = 'Erro ao processar login.';
                }
            },

            async submitAddress() {
                this.addressError = '';
                try {
                    const response = await fetch('{{ route('checkout.endereco-ajax') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.addressForm)
                    });
                    const data = await response.json();
                    if(response.ok && data.sucesso) {
                        this.userData.logradouro = this.addressForm.logradouro;
                        this.userData.numero = this.addressForm.numero;
                        this.userData.bairro = this.addressForm.bairro;
                        this.userData.cidade = this.addressForm.cidade;
                        this.userData.estado = this.addressForm.estado;
                        this.userData.cep = this.addressForm.cep;

                        this.userHasAddress = true;
                        this.cepExibicao = this.addressForm.cep;
                        await this.calculateShipping();
                        this.step = 4;
                    } else {
                        this.addressError = data.erro || 'Verifique os dados digitados.';
                    }
                } catch(e) {
                    this.addressError = 'Erro ao salvar endereço.';
                }
            },

            async applyCoupon(code) {
                if(!code) return;
                try {
                    const response = await fetch('{{ route('cart.coupon') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        },
                        body: JSON.stringify({ codigo: code })
                    });
                    const data = await response.json();
                    if(data.sucesso) {
                        window.location.reload();
                    } else {
                        alert(data.erro || data.mensagem);
                    }
                } catch (e) {
                    console.error('Erro ao aplicar cupom');
                }
            },

            aplicarMascara(valor) {
                if(!valor) return '';
                valor = valor.replace(/\D/g, '');
                if (valor.length > 5) {
                    valor = valor.replace(/^(\d{5})(\d)/, '$1-$2');
                }
                this.cepExibicao = valor;
            },

            async calculateShipping() {
                let cepLimpo = this.cepExibicao.replace(/\D/g, '');
                if (cepLimpo.length < 8) return;

                this.loading = true;
                try {
                    const response = await fetch('{{ route('product.frete') }}?cep=' + cepLimpo);
                    const data = await response.json();
                    if (data.sucesso) {
                        this.shippingOptions = data.opcoes;
                        if (this.shippingOptions.length > 0) {
                            this.shipping = this.shippingOptions[0].valor;
                            this.shippingServiceId = this.shippingOptions[0].id;
                        }
                    }
                } catch (error) {
                    console.error('Erro frete');
                } finally {
                    this.loading = false;
                }
            },

            init() {
                if (this.cepExibicao && this.cepExibicao.length >= 8) {
                    this.aplicarMascara(this.cepExibicao);
                    setTimeout(() => this.calculateShipping(), 500);
                }
            }
        }"
        x-init="init()">

        <div class="max-w-7xl mx-auto px-6 lg:px-10">

            @if(session('error'))
                <div class="mb-8 max-w-3xl mx-auto p-6 bg-red-50 border-l-4 border-red-500 rounded-r-3xl shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-red-800 font-black uppercase tracking-widest text-[10px] mb-1">Atenção</p>
                        <p class="text-red-600 font-bold text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="mb-12 max-w-3xl mx-auto">
                <div class="flex items-center justify-between relative">
                    <div class="absolute left-0 top-1/2 right-0 h-0.5 bg-gray-200 -translate-y-1/2 z-0"></div>
                    <div class="absolute left-0 top-1/2 h-0.5 bg-[#B8860B] -translate-y-1/2 z-0 transition-all duration-500"
                         :style="'width: ' + ((step - 1) / 3 * 100) + '%'"></div>

                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-black text-xs transition-all duration-300"
                             :class="step >= 1 ? 'bg-[#B8860B] text-white shadow-md' : 'bg-white text-gray-400 border border-gray-200'">1</div>
                        <span class="text-[9px] font-black uppercase tracking-wider mt-2 text-gray-900">Carrinho</span>
                    </div>

                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-black text-xs transition-all duration-300"
                             :class="step >= 2 ? 'bg-[#B8860B] text-white shadow-md' : 'bg-white text-gray-400 border border-gray-200'">2</div>
                        <span class="text-[9px] font-black uppercase tracking-wider mt-2" :class="step >= 2 ? 'text-gray-900' : 'text-gray-400'">Acesso</span>
                    </div>

                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-black text-xs transition-all duration-300"
                             :class="step >= 3 ? 'bg-[#B8860B] text-white shadow-md' : 'bg-white text-gray-400 border border-gray-200'">3</div>
                        <span class="text-[9px] font-black uppercase tracking-wider mt-2" :class="step >= 3 ? 'text-gray-900' : 'text-gray-400'">Endereço</span>
                    </div>

                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-black text-xs transition-all duration-300"
                             :class="step >= 4 ? 'bg-[#B8860B] text-white shadow-md' : 'bg-white text-gray-400 border border-gray-200'">4</div>
                        <span class="text-[9px] font-black uppercase tracking-wider mt-2" :class="step >= 4 ? 'text-gray-900' : 'text-gray-400'">Revisão</span>
                    </div>
                </div>
            </div>

            <h1 class="text-4xl font-black text-gray-900 mb-10 uppercase tracking-tighter">
                Seu <span class="text-[#B8860B]">Carrinho</span>
            </h1>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

                {{-- COLUNA DA ESQUERDA --}}
                <div class="lg:col-span-8">

                    {{-- PASSO 1 --}}
                    <div x-show="step === 1" class="space-y-4">
                        @forelse(session('cart', []) as $id => $details)
                            <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 flex items-center gap-6"
                                 x-data="{
                                    qtd: {{ (int)$details['quantidade'] }},
                                    preco: {{ (float)$details['preco'] }},
                                    async updateQty(newQty) {
                                        if(newQty < 1) return;
                                        await fetch(`/carrinho/atualizar/{{ $id }}`, {
                                            method: 'PATCH',
                                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                                            body: JSON.stringify({ quantidade: newQty })
                                        });
                                        window.location.reload();
                                    }
                                 }">

                                <div class="w-24 h-24 bg-gray-50 rounded-3xl flex items-center justify-center p-2">
                                    <img src="{{ asset('storage/' . $details['imagem']) }}" class="max-h-full max-w-full object-contain">
                                </div>

                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-900 text-lg">{{ $details['nome'] }}</h3>
                                    <div class="flex items-center gap-4 mt-3">
                                        <div class="flex items-center bg-gray-100 rounded-full p-1 border border-gray-200">
                                            <button @click="updateQty(qtd - 1)" class="w-8 h-8 flex items-center justify-center bg-white rounded-full">-</button>
                                            <span class="px-4 text-xs font-black text-gray-900" x-text="qtd"></span>
                                            <button @click="updateQty(qtd + 1)" class="w-8 h-8 flex items-center justify-center bg-white rounded-full">+</button>
                                        </div>
                                        <span class="text-[10px] text-[#B8860B] font-black uppercase tracking-widest">
                                            Un: R$ {{ number_format($details['preco'], 2, ',', '.') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <p class="font-black text-gray-900 text-lg">
                                        R$ <span x-text="(qtd * preco).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                                    </p>
                                    <form action="{{ route('cart.remove', $id) }}" method="POST">
                                        <input type="hidden" name="_token" :value="csrfToken">
                                        @method('DELETE')
                                        <button type="submit" class="text-[9px] text-red-400 font-black uppercase mt-2">Remover Item</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="bg-white p-20 rounded-[3rem] text-center border-2 border-dashed border-gray-200">
                                <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Seu carrinho está vazio.</p>
                            </div>
                        @endforelse

                        @if(count(session('cart', [])) > 0)
                            <div class="mt-10 flex flex-col sm:flex-row justify-between items-center gap-6 border-t border-gray-100 pt-8">
                                <a href="{{ route('shop.index') }}"
                                   class="w-full sm:w-auto text-center border-2 border-black text-black px-10 py-4 rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-black hover:text-[#c5a059] transition-all shadow-md">
                                    ← Continuar Comprando
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- PASSO 2 --}}
                    <div x-show="step === 2" class="bg-white p-10 rounded-[3rem] shadow-sm border border-gray-100">
                        <h2 class="text-xl font-black text-gray-900 mb-2 uppercase tracking-tight">Identificação obrigatória</h2>
                        <p class="text-xs text-gray-400 uppercase font-bold tracking-wider mb-6">Faça login para prosseguir com a entrega com segurança.</p>

                        <template x-if="loginError">
                            <div class="mb-4 p-4 bg-red-50 text-red-600 font-bold rounded-2xl text-xs uppercase" x-text="loginError"></div>
                        </template>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-2">E-mail</label>
                                <input type="email" x-model="loginEmail" class="w-full border-none bg-gray-50 rounded-full px-6 py-4 focus:ring-2 focus:ring-[#B8860B] font-bold text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-2">Senha</label>
                                <input type="password" x-model="loginPassword" class="w-full border-none bg-gray-50 rounded-full px-6 py-4 focus:ring-2 focus:ring-[#B8860B] font-bold text-sm">
                            </div>

                            <div class="pt-2 px-2 text-left">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">
                                    Não possui uma conta?
                                    <a href="{{ route('register') }}{{ request('type') === 'salon' ? '?type=salon' : '' }}" class="text-[#B8860B] hover:underline font-black ml-1">
                                        Cadastre-se aqui
                                    </a>
                                </p>
                            </div>

                            <div class="flex gap-4 pt-4">
                                <button type="button" @click="prevStep()" class="flex-1 border-2 border-black font-black uppercase text-xs py-4 rounded-full tracking-wider">Voltar</button>
                                <button type="button" @click="submitLogin()" class="flex-1 bg-black text-white font-black uppercase text-xs py-4 rounded-full tracking-wider hover:bg-[#B8860B] transition-all">Entrar e Avançar</button>
                            </div>
                        </div>
                    </div>

                    {{-- PASSO 3 --}}
                    <div x-show="step === 3" class="bg-white p-10 rounded-[3rem] shadow-sm border border-gray-100">
                        <h2 class="text-xl font-black text-gray-900 mb-2 uppercase tracking-tight">Endereço de Entrega</h2>
                        <p class="text-xs text-gray-400 uppercase font-bold tracking-wider mb-6">Confirme ou altere os seus dados cadastrais abaixo.</p>

                        <template x-if="addressError">
                            <div class="mb-4 p-4 bg-red-50 text-red-600 font-bold rounded-2xl text-xs uppercase" x-text="addressError"></div>
                        </template>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-1">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-2">CEP</label>
                                <input type="text" x-model="addressForm.cep" @input="buscarCepViaCep()" maxlength="9" placeholder="00000-000" class="w-full border-none bg-gray-50 rounded-full px-6 py-4 focus:ring-2 focus:ring-[#B8860B] font-bold text-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-2">Logradouro / Rua</label>
                                <input type="text" x-model="addressForm.logradouro" class="w-full border-none bg-gray-50 rounded-full px-6 py-4 focus:ring-2 focus:ring-[#B8860B] font-bold text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-2">Número</label>
                                <input type="text" id="address_numero" x-model="addressForm.numero" class="w-full border-none bg-gray-50 rounded-full px-6 py-4 focus:ring-2 focus:ring-[#B8860B] font-bold text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-2">Bairro</label>
                                <input type="text" x-model="addressForm.bairro" class="w-full border-none bg-gray-50 rounded-full px-6 py-4 focus:ring-2 focus:ring-[#B8860B] font-bold text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-2">Cidade</label>
                                <input type="text" x-model="addressForm.cidade" class="w-full border-none bg-gray-50 rounded-full px-6 py-4 focus:ring-2 focus:ring-[#B8860B] font-bold text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-2">Estado (UF)</label>
                                <input type="text" x-model="addressForm.estado" maxlength="2" placeholder="SP" class="w-full border-none bg-gray-50 rounded-full px-6 py-4 focus:ring-2 focus:ring-[#B8860B] font-bold text-sm uppercase">
                            </div>
                        </div>

                        <div class="flex gap-4 pt-6 mt-4 border-t border-gray-100">
                            <button type="button" @click="prevStep()" class="flex-1 border-2 border-black font-black uppercase text-xs py-4 rounded-full tracking-wider">Voltar</button>
                            <button type="button" @click="submitAddress()" class="flex-1 bg-black text-white font-black uppercase text-xs py-4 rounded-full tracking-wider hover:bg-[#B8860B] transition-all">Salvar Endereço</button>
                        </div>
                    </div>

                    {{-- PASSO 4: REVISÃO DE DADOS COMPLETA --}}
                    <div x-show="step === 4" class="bg-white p-10 rounded-[3rem] shadow-sm border border-gray-100">
                        <h2 class="text-xl font-black text-gray-900 mb-2 uppercase tracking-tight">Tudo pronto para o envio!</h2>
                        <p class="text-xs text-gray-400 uppercase font-bold tracking-wider mb-6">Confirme abaixo os dados de despacho.</p>

                        <div class="p-6 bg-gray-50 rounded-3xl border border-gray-100 mb-6 text-left">
                            <p class="text-xs font-black text-[#B8860B] uppercase tracking-widest mb-2">Destinatário</p>
                            <p class="text-sm font-bold text-gray-900 mb-4" x-text="userData.name"></p>

                            <p class="text-xs font-black text-[#B8860B] uppercase tracking-widest mb-2">Endereço de Entrega</p>
                            <p class="text-sm text-gray-700 font-bold leading-relaxed">
                                <span x-text="userData.logradouro"></span>, <span x-text="userData.numero"></span><br>
                                <span x-text="userData.bairro"></span> — <span x-text="userData.cidade"></span> / <span x-text="userData.estado"></span><br>
                                <span class="text-[#B8860B]" x-text="'CEP: ' + userData.cep"></span>
                            </p>
                        </div>

                        <div class="flex gap-4">
                            <button type="button" @click="userHasAddress = false; addressForm = { ...userData }; step = 3;"
                                    class="flex-1 border-2 border-black text-black px-8 py-4 rounded-full font-black uppercase text-xs tracking-wider hover:bg-black hover:text-white transition-all">
                                Alterar Dados
                            </button>

                            <button type="button" @click="prevStep()"
                                    class="flex-1 border-2 border-gray-300 text-gray-500 px-8 py-4 rounded-full font-black uppercase text-xs tracking-wider hover:border-black hover:text-black transition-all">
                                ← Voltar ao Carrinho
                            </button>
                        </div>
                    </div>

                </div>

                {{-- COLUNA DA DIREITA --}}
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100" x-data="{ tempCode: '' }">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 px-2">Possui um cupom?</label>
                        <div class="flex gap-2">
                            <input type="text" x-model="tempCode" placeholder="CÓDIGO" class="flex-1 text-xs border-none bg-gray-50 rounded-full px-5 py-3 focus:ring-2 focus:ring-[#B8860B] uppercase font-bold">
                            <button @click="applyCoupon(tempCode)" type="button" class="bg-[#B8860B] text-white px-6 py-3 rounded-full text-[10px] font-black uppercase">OK</button>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-100">
                        <h2 class="text-xs font-black text-gray-900 mb-8 uppercase tracking-[0.3em]">Resumo do Pedido</h2>

                        <div class="mb-8 p-6 bg-gray-50 rounded-3xl border border-gray-100">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 px-2">Entrega</label>
                            <div class="flex gap-2 mb-4">
                                <input type="text" x-model="cepExibicao" @input="aplicarMascara($event.target.value)" maxlength="9" placeholder="00000-000"
                                       class="flex-1 text-xs border-none bg-white rounded-full px-4 py-3 shadow-sm focus:ring-2 focus:ring-[#B8860B]">
                                <button type="button" @click="calculateShipping()" class="bg-black text-white px-6 py-3 rounded-full text-[10px] font-black">OK</button>
                            </div>
                            <div class="space-y-2" x-show="shippingOptions.length > 0">
                                <template x-for="(option, index) in shippingOptions" :key="index">
                                    <label class="flex items-center justify-between p-3 bg-white rounded-2xl border border-gray-100 cursor-pointer" :class="shippingServiceId == option.id ? 'border-[#B8860B]' : ''">
                                        <div class="flex items-center">
                                            <input type="radio" name="frete_op" @click="shipping = option.valor; shippingServiceId = option.id" :checked="shippingServiceId == option.id" class="text-[#B8860B]">
                                            <div class="ml-3">
                                                <p class="text-[11px] font-bold text-gray-900" x-text="option.nome"></p>
                                                <p class="text-[9px] text-gray-400 uppercase" x-text="option.prazo"></p>
                                            </div>
                                        </div>
                                        <span class="text-[11px] font-black text-[#B8860B]" x-text="'R$ ' + parseFloat(option.valor).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <div class="space-y-4 mb-8 px-2">
                            <div class="flex justify-between text-gray-500 text-xs font-bold uppercase tracking-widest">
                                <span>Subtotal</span>
                                <span class="font-black text-gray-900">R$ <span x-text="subtotal.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})">{{ number_format(array_sum(array_map(fn($item) => (float)$item['preco'] * (int)$item['quantidade'], session('cart', []))), 2, ',', '.') }}</span></span>
                            </div>

                            <div class="flex justify-between text-green-500 text-xs font-black uppercase tracking-widest" x-show="discountValue > 0">
                                <span x-text="'Desconto (' + coupon.codigo + ')'"></span>
                                <span>- R$ <span x-text="parseFloat(discountValue).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></span>
                            </div>

                            <div class="flex justify-between text-gray-500 text-xs font-bold uppercase tracking-widest">
                                <span>Frete</span>
                                <span class="font-black text-gray-900" x-text="coupon && coupon.tipo === 'frete_gratis' ? 'GRÁTIS' : (shipping == 0 ? '---' : 'R$ ' + parseFloat(shipping).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2}))">---</span>
                            </div>
                            <div class="flex justify-between text-2xl font-black text-gray-900 pt-6 border-t border-gray-100">
                                <span class="tracking-tighter uppercase">Total</span>
                                <span class="text-[#B8860B]">R$ <span x-text="total.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})">{{ number_format(array_sum(array_map(fn($item) => (float)$item['preco'] * (int)$item['quantidade'], session('cart', []))), 2, ',', '.') }}</span></span>
                            </div>
                        </div>

                        {{-- BOTÃO FINALIZAR - EXIBIDO NOS PASSOS 1, 2 E 3 --}}
                        <div x-show="step < 4">
                            <button type="button" @click="nextStep()" :disabled="subtotal <= 0"
                                    class="w-full bg-black hover:bg-[#B8860B] text-white py-4 rounded-full font-black uppercase tracking-wider text-xs transition-all disabled:bg-gray-200">
                                Finalizar Compra
                            </button>
                        </div>

                        {{-- BLOCO DE PAGAMENTO FINAL - PASSO 4 --}}
                        <div x-show="step === 4">

                            <div class="mb-6">
                                <h3 class="text-[10px] font-black text-gray-400 mb-3 uppercase tracking-widest px-2">Forma de Pagamento</h3>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="border-2 rounded-2xl p-4 cursor-pointer flex flex-col items-center justify-center transition-all shadow-sm"
                                           :class="paymentMethod === 'pix' ? 'border-green-500 bg-green-50' : 'border-gray-200 bg-white hover:border-green-300'">
                                        <input type="radio" name="pay_method" value="pix" x-model="paymentMethod" class="hidden">
                                        <span class="font-black text-green-600 uppercase tracking-widest text-xs mb-1">PIX</span>
                                        <span class="text-[9px] text-gray-500 font-bold uppercase">Aprovação Imediata</span>
                                    </label>

                                    <label class="border-2 rounded-2xl p-4 cursor-pointer flex flex-col items-center justify-center transition-all shadow-sm"
                                           :class="paymentMethod === 'cartao' ? 'border-[#B8860B] bg-yellow-50' : 'border-gray-200 bg-white hover:border-[#B8860B]'">
                                        <input type="radio" name="pay_method" value="cartao" x-model="paymentMethod" class="hidden">
                                        <span class="font-black text-gray-900 uppercase tracking-widest text-xs mb-1">Cartão</span>
                                        <span class="text-[9px] text-gray-500 font-bold uppercase">Até 12x InfinitePay</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Parcelas são escolhidas no checkout hospedado da InfinitePay --}}
                            <p class="text-[9px] text-gray-400 uppercase font-bold px-2 mb-6" x-show="paymentMethod === 'cartao'" x-cloak>
                                O parcelamento (até 12x) é escolhido na tela segura da InfinitePay.
                            </p>

                            <form id="formCheckoutFinal" action="{{ route('checkout.pagar') }}" method="POST">
                                @csrf
                                <input type="hidden" name="shipping_value" id="input_shipping_value" value="15.00">
                                <input type="hidden" name="shipping_service_id" id="input_shipping_service_id" value="1">

                                <input type="hidden" name="payment_method" id="input_payment_method" value="pix">
                                <input type="hidden" name="installments" id="input_installments" value="1">

                                <button type="button"
                                        onclick="dispararCheckoutNativo(this)"
                                        class="w-full bg-green-600 hover:bg-black text-white py-4 rounded-full font-black uppercase tracking-wider text-xs transition-all shadow-lg active:scale-95">
                                    Gerar Pedido e Pagar →
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-store-layout>

<style>
    /* Oculta os elementos Alpine.js até a inicialização */
    [x-cloak] { display: none !important; }
</style>

<script>
function dispararCheckoutNativo(botao) {
    botao.disabled = true;
    botao.innerText = "PROCESSANDO...";

    try {
        if (typeof Alpine !== 'undefined') {
            let alpineData = Alpine.$data(document.getElementById('checkout-interaction-container'));
            if (alpineData) {
                document.getElementById('input_shipping_value').value = alpineData.finalShipping;
                document.getElementById('input_shipping_service_id').value = alpineData.shippingServiceId;

                document.getElementById('input_payment_method').value = alpineData.paymentMethod;

                if(alpineData.paymentMethod === 'cartao') {
                    document.getElementById('input_installments').value = alpineData.installments;
                }
            }
        }
    } catch (e) {
        console.warn('Erro ao mapear variáveis do Alpine, usando fallback fixo.');
    }

    document.getElementById('formCheckoutFinal').submit();
}
</script>
