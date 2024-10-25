@php
    $redesSociais = [
        [
            'name' => 'WhatsApp',
            'link' =>
                'https://api.whatsapp.com/send?phone=' .
                $latestHome->whatsappNumber .
                '&text=' .
                $latestHome->whatsappMessage,
            'icon' => 'whatsapp',
        ],
        [
            'name' => 'Facebook',
            'link' => $latestHome->facebookLink,
            'icon' => 'facebook',
        ],
        [
            'name' => 'Instagram',
            'link' => $latestHome->instagramLink,
            'icon' => 'instagram',
        ],
    ];
@endphp

<section id="planos">
    <div class="container">
        <div class="container-topo">
            <div class="planos-descricao">
                <h3>{{ $latestHome->plans_title }}</h3>
                <p>{{ $latestHome->plans_description }}</p>
            </div>
            <div class="planos-cards">
                @foreach ($planos as $plano)
                    @if ($plano->status == 1)
                        <div class="card">
                            <div>
                                <h3 style="color: {{ $plano->color }}">{{ $plano->plan }}</h3>
                                <span
                                    style="color: {{ $plano->color }}">{{ $plano->value == 0 ? 'GRÁTIS' : 'R$ ' . $plano->value . '/mês' }}</span>
                            </div>
                            <p style="color: {{ $plano->color }}">{{ $plano->description }}</p>
                            <div class="card-button">
                                <a href="{{ $plano->link }}" target="__blank"
                                    style="color: var(--corBranco); background-color: {{ $plano->color }};">Anunciar</a>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        <div class="container-baixo">
            <span>Siga-nos nas redes sociais:</span>

            @foreach ($redesSociais as $redeSocial)
                @if ($redeSocial['link'] !== null)
                    <a href="{{ $redeSocial['link'] }}" target="
                    __blank">
                        <img src="{{ asset('images/' . $redeSocial['icon'] . '.svg') }}"
                            alt="{{ $redeSocial['icon'] }}">
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</section>
