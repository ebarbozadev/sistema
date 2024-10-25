<section id="cursos">
    @foreach ($cursos as $curso)
        @if ($curso->status == true)
            <a href="{{ $curso->url }}" target="__blank">
                <img src="{{ asset('storage/' . $curso->image) }}" alt="">
            </a>
        @endif
    @endforeach
</section>
