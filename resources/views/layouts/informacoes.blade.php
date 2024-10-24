@php
    $titulos = [
        [
            'name' => 'PEDIDOS',
            'descricao' => $latestHome->corders_description,
            'cor' => '--corSecundaria',
            'link' => $latestHome->corders_link,
        ],
        [
            'name' => 'PESQUISAR',
            'descricao' => $latestHome->csearch_description,
            'cor' => '--corPrincipal',
            'link' => $latestHome->csearch_link,
        ],
        [
            'name' => 'CADASTRE-SE',
            'descricao' => $latestHome->cregister_description,
            'cor' => '--corTerciaria',
            'link' => $latestHome->cregister_link,
        ],
        [
            'name' => 'FAÇA SEU ANÚNCIO',
            'descricao' => $latestHome->cannounce_description,
            'cor' => '--corBranco',
            'link' => $latestHome->cannounce_link,
        ],
    ];
@endphp

<section id="informacoes">
    <div class="container">
        @foreach ($titulos as $titulo)
            <a href="{{ $titulo['link'] }}" class="card" style="background-color: var({{ $titulo['cor'] }})">
                <h4>{{ $titulo['name'] }}</h4>
                <p>{{ $titulo['descricao'] }}</p>
            </a>
        @endforeach
    </div>
</section>
