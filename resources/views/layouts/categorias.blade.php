<section id="categorias">
    <div class="container">
        @foreach ($categorias as $categoria)
            <a href="{{ route('produtos.show', ['slug' => $categoria->slug]) }}" class="card">
                <h4>{{ $categoria->title }}</h4>
            </a>
        @endforeach
    </div>
</section>
