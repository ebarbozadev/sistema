{{-- @php
    $tipoPropriedade = \App\Helpers\Utils::getTipoPropriedade($latestHome->property_type);
@endphp --}}

<section id="bannerPrincipal"
    style="background-image: url('{{ asset(str_replace('\\', '/', 'storage/' . $latestHome->bmain_image)) }}');">
    <div class="bannerPrincipal">
        <div class="bp_descricoes">
            <textarea disabled>{{ $latestHome->bmain_title }}</textarea>
            <p>{{ $latestHome->bmain_description }}</p>
            @if ($latestHome->bmain_link !== null)
                <a href="{{ $latestHome->bmain_link }}" target="__blank">saiba mais</a>
            @endif
        </div>

        <img class="bp_mouse" src="{{ asset('images/mouse.svg') }}" alt="Mouse">
    </div>
</section>
