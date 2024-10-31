@php
    use Illuminate\Support\Facades\Request;

    $segmento1 = Request::segment(1); // Retorna 'products'
    $segmento2 = Request::segment(2); // Retorna 'aeronaves'
@endphp


@extends('layouts.layout')

@section('title', 'Produtos')

@section('content')
    <section id="produtos">
        <div class="container">
            <div class="diretorio">
                <p>HOME / {{ $segmento1 }} / {{ $categoria->title }}</p>
            </div>
            <h2>{{ $categoria->title }}</h2>
            <p>{{ $categoria->description }}</p>

            <div id="produtos-lista">
                @foreach ($produtos as $produto)
                    <div class="card-produto">
                        <a href="{{ route('produtos.showProduct', ['slug' => $categoria->slug, 'id' => $produto->id]) }}">
                            @php
                                $imagens = json_decode($produto->image);
                                $categorias = $primeiraImagem = $imagens[0];

                                $estados = [
                                    'Acre' => 'AC',
                                    'Alagoas' => 'AL',
                                    'Amapá' => 'AP',
                                    'Amazonas' => 'AM',
                                    'Bahia' => 'BA',
                                    'Ceará' => 'CE',
                                    'Distrito Federal' => 'DF',
                                    'Espírito Santo' => 'ES',
                                    'Goiás' => 'GO',
                                    'Maranhão' => 'MA',
                                    'Mato Grosso' => 'MT',
                                    'Mato Grosso do Sul' => 'MS',
                                    'Minas Gerais' => 'MG',
                                    'Pará' => 'PA',
                                    'Paraíba' => 'PB',
                                    'Paraná' => 'PR',
                                    'Pernambuco' => 'PE',
                                    'Piauí' => 'PI',
                                    'Rio de Janeiro' => 'RJ',
                                    'Rio Grande do Norte' => 'RN',
                                    'Rio Grande do Sul' => 'RS',
                                    'Rondônia' => 'RO',
                                    'Roraima' => 'RR',
                                    'Santa Catarina' => 'SC',
                                    'São Paulo' => 'SP',
                                    'Sergipe' => 'SE',
                                    'Tocantins' => 'TO',
                                ];

                                // Obtém a UF do estado do produto
                                $UF = $estados[$produto->state] ?? 'UF não encontrada';

                                $precoFormatado = number_format($produto->price, 2, ',', '.');
                            @endphp

                            <img src="{{ asset('storage/' . $primeiraImagem) }}" alt="{{ $produto->title }}">

                            <div class="produto-descricao">
                                <span>REF: {{ $produto->id }}</span>
                                <h4>{{ $produto->title }}</h4>
                            </div>

                            <p><i class="fa-solid fa-location-dot"></i>{{ $produto->state }} | {{ $UF }}</p>

                            <div class="produto-valor">
                                <i class="fa-solid fa-arrow-right"></i>
                                <p>{{ $precoFormatado }}</p>
                            </div>

                            <span>{{ $categoria->title }}</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
