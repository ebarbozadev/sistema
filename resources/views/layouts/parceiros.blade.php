@php
    $parceirosExibidos = 0;
@endphp

<section id="parceiros">
    <div class="container">
        <div class="swiper-container swiper-parceiros">
            <div class="swiper-wrapper" id="wrapper-parceiros">
                @if ($parceiros->count() < 6)
                    @while ($parceirosExibidos < 8)
                        @foreach ($parceiros as $parceiro)
                            @if ($parceiro->status == true)
                                @php
                                    $pathImage = \App\Helpers\Utils::fixUrlBar(asset('storage/' . $parceiro->image));
                                @endphp

                                <a href="{{ $parceiro->url }}" class="swiper-slide card">
                                    <img src="{{ $pathImage }}" alt="">
                                </a>

                                {{-- <div class="swiper-slide card" style="background-image: url('{{ $pathImage }}');">
                                </div>
                                <a class="swiper-slide" href="{{ $parceiro->url }}" target="__blank">
                                    <img src="{{ asset('storage/' . $parceiro->image) }}" alt="">
                                </a> --}}
                            @endif
                        @endforeach
                        @php $parceirosExibidos++; @endphp
                    @endwhile
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Swiper JS -->
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script>
    const swiperParceiros = new Swiper('.swiper-parceiros', {
        direction: 'horizontal',
        loop: true,
        autoplay: {
            delay: 3000,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        slidesPerView: 6,
        spaceBetween: 10,
    });
</script>
