<footer id="rodape">
    <div class="container">
        <img src="{{ asset('') . 'storage/' . $latestHome->logo }}" alt="Logo">
        <div class="rodape-institucional">
            <h4>Institucional</h4>
            <div>
                <a href="/pages/quem-somos">Quem Somos</a>
                <a href="#">Faça seu anúncio</a>
                <a href="#">Login</a>
                <a href="#">Contato</a>
            </div>

        </div>

        <div class="rodape-categorias">
            <h4>Categorias</h4>
            <div>
                @foreach ($categorias->take(6) as $categoria)
                    <a href="#">{{ $categoria->title }}</a> <!-- Exiba o nome da categoria -->
                @endforeach
            </div>
        </div>

        <div class="rodape-contatos">
            <h4>Contatos</h4>
            <div style="margin-bottom: 30px">
                <p>{{ $latestHome->email }}</p>
                <p>{{ $latestHome->whatsappMessage }}</p>
            </div>

            <div>
                <a href="#">Políticas e Termos</a>
            </div>
        </div>
    </div>
    <div class="rodape-copyright">
        <div>
            <p>© Copyright 2024. Todos os direitos reservados - BrainSupport | Agência DigitalOne</p>
            <p>Desenvolvido com energia 100% renováveis.</p>
        </div>
    </div>
</footer>
