<header>
    <div class="container">
        <a href="/">
            <img src="{{ asset('') . 'storage/' . $latestHome->logo }}" alt="Logo TV Fazenda">
        </a>

        <ul>
            @foreach ($navegacoes as $navegacao)
                <li>
                    <a href="{{ $navegacao['url'] }}" class="{{ $navegacao['active'] ? 'nav-active' : '' }}">
                        {{ $navegacao['titulo'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</header>
