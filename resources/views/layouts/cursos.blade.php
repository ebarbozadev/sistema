@php
    $cursosExibidos = 0;
@endphp

<section id="cursos">
    <div class="swiper-container swiper-cursos">
        <div class="swiper-wrapper">
            @if ($cursos->count() < 6)
                @while ($cursosExibidos < 8)
                    @foreach ($cursos as $curso)
                        @if ($curso->status == true)
                            <a class="swiper-slide" href="{{ $curso->url }}" target="__blank">
                                <img src="{{ asset('storage/' . $curso->image) }}" alt="">
                            </a>
                        @endif
                    @endforeach
                    @php $cursosExibidos++; @endphp
                @endwhile
            @endif
        </div>
    </div>
</section>

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script>
    const swiperCursos = new Swiper('.swiper-cursos', {
        direction: 'vertical',
        loop: true,
        autoplay: {
            delay: 3000,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        slidesPerView: 6
    });
</script>
