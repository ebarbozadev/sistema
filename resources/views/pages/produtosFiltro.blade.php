@extends('layouts.layout')

@section('title', 'Produtos')

@section('content')
    <section id="produtos">
        <div class="container">
            <div class="diretorio">
                <p>HOME / PRODUTOS / {{ $categoria->title }}</p>
            </div>
            <h2>{{ $categoria->title }}</h2>
            <p>{{ $categoria->description }}</p>

            <div id="produtos-lista">
                @foreach ($produtos as $produto)
                    <a href="{{ route('produtos.showProduct', ['slug' => $categoria->slug, 'id' => $produto->id]) }}"
                        class="card">
                        <h4>{{ $produto->title }}</h4>
                    </a>

                    @dd($produto->image)
                @endforeach
            </div>
        </div>
    </section>
@endsection
