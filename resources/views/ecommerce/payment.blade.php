<x-store-layout>
    <div class="mt-6 py-16 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-6">

            <a href="{{ route('cart.index') }}" class="text-xs font-black uppercase text-gray-500 hover:text-black">← Voltar</a>

            <h1 class="text-3xl font-black text-gray-900 mt-6 mb-10 uppercase text-center">Formas de Pagamento</h1>

            {{-- 1. SE O PIX JÁ FOI GERADO (Fluxo de exibição do QR Code) --}}
            @if(!empty($pixPayload))
                <div class="mt-10 p-10 bg-white rounded-[3rem] shadow-2xl border-2 border-green-500 text-center">
                    <p class="font-black uppercase text-xs mb-4 text-green-600 tracking-widest">Seu PIX foi gerado com sucesso!</p>

                    <div class="flex justify-center mb-6">
                        <div class="border-4 border-gray-100 rounded-2xl overflow-hidden p-4 bg-white shadow-sm">
                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(250)->generate($pixPayload) !!}
                        </div>
                    </div>

                    <div class="space-y-3 text-left max-w-md mx-auto mb-8">
                        <label class="text-[10px] font-black uppercase text-gray-400 block ml-2 tracking-widest">Código Copia e Cola</label>
                        <div class="flex gap-2">
                            <input type="text" id="pixCode" value="{{ $pixPayload }}" readonly
                                class="flex-1 bg-gray-50 border-none rounded-2xl text-xs p-4 focus:ring-1 focus:ring-green-500 shadow-sm font-mono text-gray-700">
                            <button onclick="copyPix()"
                                class="bg-black text-white px-6 rounded-2xl font-black text-[10px] uppercase hover:bg-green-600 transition-all active:scale-95 shadow-lg tracking-wider">
                                Copiar
                            </button>
                        </div>
                    </div>

                    <form action="{{ route('checkout.pix.confirm') }}" method="POST">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <button type="submit" class="w-full bg-green-600 text-white py-5 rounded-full font-black uppercase tracking-widest text-xs hover:bg-green-700 transition active:scale-95 shadow-xl">
                            Já realizei o pagamento
                        </button>
                    </form>
                </div>
            @else
                {{-- 2. SE NÃO EXISTIR, EXIBE OS BOTÕES FORMULÁRIOS COM MÉTODO POST --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">

                    {{-- FORMULÁRIO DO CARTÃO DE CRÉDITO --}}
                    <form action="{{ route('checkout.process', ['method' => 'card']) }}" method="POST" class="w-full bg-white p-8 rounded-[3rem] shadow-xl border-2 border-transparent hover:border-[#B8860B] transition-all flex flex-col justify-between h-full">
                        @csrf
                        <div class="text-center mb-6 mt-2">
                            <h3 class="font-black text-gray-900 uppercase tracking-wider">Cartão de Crédito</h3>
                            <p class="text-[10px] text-gray-400 font-bold uppercase mt-2">Pague em até 12x via InfinitePay</p>
                        </div>

                        <div class="mb-6">
                            <select name="installments" id="installments" class="w-full text-xs font-bold border-gray-200 focus:border-[#B8860B] focus:ring-[#B8860B] rounded-xl shadow-sm text-center py-3 cursor-pointer appearance-none">
                                <option value="1">1x à vista de R$ {{ number_format($totalFinal, 2, ',', '.') }}</option>
                                <option value="2">2x sem juros de R$ {{ number_format($totalFinal / 2, 2, ',', '.') }}</option>
                                <option value="3">3x sem juros de R$ {{ number_format($totalFinal / 3, 2, ',', '.') }}</option>
                                <option value="4">4x sem juros de R$ {{ number_format($totalFinal / 4, 2, ',', '.') }}</option>
                                <option value="5">5x sem juros de R$ {{ number_format($totalFinal / 5, 2, ',', '.') }}</option>
                                <option value="6">6x sem juros de R$ {{ number_format($totalFinal / 6, 2, ',', '.') }}</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-black text-[#B8860B] py-4 rounded-full font-black uppercase tracking-widest text-[10px] hover:bg-[#B8860B] hover:text-black transition-all shadow-md">
                            Prosseguir para o Pagamento
                        </button>
                    </form>

                    {{-- FORMULÁRIO DO PIX --}}
                    <form action="{{ route('checkout.payment') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="w-full bg-white p-8 rounded-[3rem] shadow-xl text-center border-2 border-transparent hover:border-green-500 transition-all flex flex-col justify-center items-center h-full focus:outline-none">
                            <h3 class="font-black text-green-600 uppercase tracking-wider">Pagar com PIX</h3>
                            <p class="text-[10px] text-gray-400 font-bold uppercase mt-2">Gerar QR Code imediato</p>
                        </button>
                    </form>

                </div>
            @endif

        </div>
    </div>

    {{-- Script de Cópia Rápida --}}
    <script>
    function copyPix() {
        var copyText = document.getElementById("pixCode");
        if (!copyText) return;

        copyText.select();
        copyText.setSelectionRange(0, 99999);

        try {
            navigator.clipboard.writeText(copyText.value);
            alert("Código PIX Copia e Cola copiado! Agora basta abrir o aplicativo do seu banco e colar.");
        } catch (err) {
            document.execCommand("copy");
            alert("Código PIX copiado!");
        }
    }
    </script>
</x-store-layout>
