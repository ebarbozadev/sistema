<section id="publicidades">
    @foreach ($publicidades as $publicidade)
        @if ($publicidade->status == true)
            <a href="{{ $publicidade->url }}" target="__blank">
                <img src="{{ asset('storage/' . $publicidade->image) }}" alt="">
            </a>
        @endif
    @endforeach
</section>
