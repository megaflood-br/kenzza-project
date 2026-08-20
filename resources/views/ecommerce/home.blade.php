@section('seo')
    <meta name="description" content="K'enzza Hair Professional: Cosméticos profissionais de alta performance para cabelos. Encontre progressivas, tratamentos e linhas home care para salões e distribuidores.">
@endsection
<x-store-layout>
    {{-- Swiper CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <style>
        .swiper-wrapper-linear {
            transition-timing-function: linear !important;
        }
    </style>

    <div class="bg-white overflow-x-hidden">
        {{-- SEÇÃO DE BANNERS --}}
        @if(isset($banners) && $banners->count() > 0)
            <div class="max-w-7xl mx-auto px-4 sm:px-8 lg:px-10 pt-4 md:pt-8">
                <div class="swiper mainSwiper rounded-[1.5rem] md:rounded-[2.5rem] overflow-hidden shadow-sm border border-gray-100">
                    <div class="swiper-wrapper">
                        @foreach($banners as $banner)
                            <div class="swiper-slide">
                                {{-- CORREÇÃO: Removido $product->nome que causava erro e adicionado title dinâmico --}}
                                <a href="{{ $banner->link ?? '#' }}"
                                   title="Ver detalhes: {{ $banner->titulo ?? 'Destaque K\'enzza' }}"
                                   class="block">

                                    {{-- CORREÇÃO SEO: Adicionado alt descritivo nas imagens --}}
                                    <img src="{{ asset('storage/' . $banner->imagem_desktop) }}"
                                         alt="Banner K'enzza - {{ $banner->titulo ?? 'Linha Profissional' }}"
                                         class="w-full hidden md:block aspect-[21/9] object-cover">

                                    <img src="{{ asset('storage/' . $banner->imagem_mobile) }}"
                                         alt="Banner K'enzza - {{ $banner->titulo ?? 'Linha Profissional' }}"
                                         class="w-full md:hidden aspect-[16/9] object-cover">
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <!--<div class="swiper-pagination"></div>-->
                </div>
            </div>
        @endif

        {{-- SEÇÃO DE CATEGORIAS --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 md:py-20">
            @php
                $categories = [
                    ['slug' => 'uso-diario', 'img' => '/usodiario.jpeg', 'title' => 'Uso Diário', 'sub' => 'Home Care'],
                    ['slug' => 'hidratacao-nutricao', 'img' => '/hidratacao.jpeg', 'title' => 'Hidratação', 'sub' => 'Nutrição'],
                    ['slug' => 'cachos', 'img' => '/cachos.jpeg', 'title' => 'Cachos', 'sub' => 'Definição'],
                    ['slug' => 'coloracoes', 'img' => '/color.jpeg', 'title' => 'Colorações', 'sub' => 'K\'enzza Color'],
                    ['slug' => 'finalizadores', 'img' => '/final.jpeg', 'title' => 'Finalizadores', 'sub' => 'Toque Final'],
                ];
            @endphp

            {{-- VERSÃO MOBILE --}}
            <div class="md:hidden">
                <div class="swiper catSwiper !overflow-visible">
                    <div class="swiper-wrapper swiper-wrapper-linear">
                        @foreach(array_merge($categories, $categories) as $cat)
                            <div class="swiper-slide !w-[140px]">
                                <a href="{{ route('shop.index', ['slug' => $cat['slug']]) }}"
                                   title="Categoria {{ $cat['title'] }}"
                                   class="group relative block aspect-[3/4] rounded-[1.5rem] overflow-hidden shadow-lg">
                                    <img src="{{ asset($cat['img']) }}" class="absolute inset-0 w-full h-full object-cover" alt="Categoria {{ $cat['title'] }}">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                                    <div class="absolute inset-0 flex flex-col items-center justify-end pb-4 m-2">
                                        <span class="text-white font-kenzza text-sm tracking-tighter uppercase">{{ $cat['title'] }}</span>
                                        <span class="text-[#B8860B] text-[6px] font-black uppercase tracking-widest">{{ $cat['sub'] }}</span>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- VERSÃO DESKTOP --}}
            <div class="hidden md:grid grid-cols-5 gap-8">
                @foreach($categories as $cat)
                    <a href="{{ route('shop.index', ['slug' => $cat['slug']]) }}"
                       title="Explorar {{ $cat['title'] }}"
                       class="group relative block h-[500px] rounded-[2.5rem] overflow-hidden shadow-2xl transition-all duration-500 hover:-translate-y-3">
                        <img src="{{ asset($cat['img']) }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" alt="Linha {{ $cat['title'] }}">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-80 group-hover:opacity-90"></div>
                        <div class="absolute inset-0 flex flex-col items-center justify-end pb-10 border border-white/10 rounded-[2.5rem] m-3">
                            <span class="text-white font-kenzza text-2xl tracking-tighter uppercase mb-1">{{ $cat['title'] }}</span>
                            <span class="text-[#B8860B] text-[10px] font-black uppercase tracking-[0.3em]">{{ $cat['sub'] }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- PRODUTOS EM DESTAQUE --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-black italic uppercase tracking-tighter text-gray-900">Destaques</h2>
                <div class="h-1 w-20 bg-[#B8860B] mx-auto mt-4"></div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-10">
                @foreach($featuredProducts as $product)
                    <div class="group flex flex-col h-full bg-white border border-gray-50 rounded-[1.5rem] md:rounded-[3rem] p-3 md:p-6 transition-all hover:shadow-2xl">
                        <a href="{{ route('shop.product', $product->slug) }}"
                        title="Ver {{ $product->nome }}"
                        class="block aspect-square flex items-center justify-center mb-4 overflow-hidden bg-white rounded-[1.2rem] md:rounded-[2.2rem]">
                            <img src="{{ asset('storage/' . $product->imagem) }}"
                                class="max-h-[85%] w-auto object-contain transition-transform duration-700 group-hover:scale-110"
                                alt="Produto {{ $product->nome }}">
                        </a>

                        <div class="text-center flex-grow flex flex-col justify-between">
                            {{-- AJUSTE: Altura aumentada de h-8 para h-12 para caber 2 linhas uppercase --}}
                            <h3 class="text-[10px] md:text-sm font-black text-gray-900 uppercase tracking-tighter line-clamp-2 h-12 flex items-center justify-center px-1">
                                {{ $product->nome }}
                            </h3>

                            <p class="text-[#B8860B] font-black text-sm md:text-xl mt-3 tracking-tighter">
                                R$ {{ number_format($product->preco_atual, 2, ',', '.') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Swiper JS --}}
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new Swiper(".mainSwiper", {
                loop: true,
                autoplay: { delay: 5000 },
                pagination: { el: ".swiper-pagination", clickable: true },
            });

            if (window.innerWidth < 1024) {
                new Swiper(".catSwiper", {
                    slidesPerView: "auto",
                    spaceBetween: 16,
                    loop: true,
                    speed: 5000,
                    autoplay: { delay: 0, disableOnInteraction: false },
                    freeMode: true,
                });
            }
        });
    </script>
</x-store-layout>
