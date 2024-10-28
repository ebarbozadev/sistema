<header>
    <div class="container">
        <a href="/">
            <img src="{{ asset('') . 'storage/' . $latestHome->logo }}" alt="Logo TV Fazenda">
        </a>

        <i class="fa-solid fa-bars" id="menu-icon" onclick="toggleMenu()"></i>

        <ul id="nav-menu">
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

<script>
    function toggleMenu() {
        const navMenu = document.getElementById('nav-menu');

        if (navMenu.style.display === 'flex') {
            navMenu.style.display = 'none';
        } else {
            navMenu.style.display = 'flex';
        }
    }
</script>
