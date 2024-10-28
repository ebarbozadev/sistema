@php
    $publicidadesExibidas = 0;
@endphp

<section id="publicidades">
    <div class="swiper-publicidades">
        <div class="swiper-wrapper">
            @if ($publicidades->count() < 6)
                @while ($publicidadesExibidas < 6)
                    @foreach ($publicidades as $publicidade)
                        @if ($publicidade->status == true)
                            <a class="swiper-slide" href="{{ $publicidade->url }}" target="__blank">
                                <img src="{{ asset('storage/' . $publicidade->image) }}" alt="">
                            </a>
                        @endif
                    @endforeach
                    @php $publicidadesExibidas++; @endphp
                @endwhile
            @endif
        </div>
    </div>
</section>

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script>
    const swiperPublicidades = new Swiper('.swiper-publicidades', {
        direction: 'vertical',
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
