@extends('layouts.layout')

@section('title', 'Produtos')

@section('content')
    <section id="produtos">
        <div class="container">
            <h2>Produtos</h2>

            <!-- Formulário de Filtros -->
            <form id="produtos-filtro" method="GET" action="{{ route('produtos.index') }}">
                <div class="inputs">
                    <div class="form-group">
                        <label for="type">Tipo</label>
                        <select name="type" id="type">
                            <option value="">Todos os tipos</option>
                            @foreach ($types as $key => $value)
                                <option value="{{ $key }}"
                                    {{ ($filters['type'] ?? '') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="category">Categoria</label>
                        <select name="category" id="category">
                            <option value="">Todas as categorias</option>
                            @foreach ($categorias as $category)
                                <option value="{{ $category->id }}"
                                    {{ ($filters['category'] ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Pesquisar</label>
                        <input placeholder="Digite sua busca" type="text" name="description" id="description"
                            value="{{ $filters['description'] ?? '' }}">
                    </div>
                    <div class="form-group">
                        <button type="submit">Buscar</button>
                    </div>
                </div>
            </form>

            <!-- Lista de Produtos -->
            <div id="produtos-lista">
                @foreach ($produtos as $produto)
                    @php

                        $propertyTypes = [
                            'RE' => 'RESIDENTIAL',
                            'CO' => 'COMMERCIAL',
                            'IN' => 'INDUSTRIAL',
                            'MI' => 'MIXED',
                            'FA' => 'FARM',
                            'SI' => 'SMALL FARM',
                            'CH' => 'COUNTRY HOUSE',
                            'HE' => 'ESTATE',
                            'TE' => 'LAND',
                            'AR' => 'WAREHOUSE/STORAGE',
                        ];

                        $categorySlug = Str::slug($produto->category->title);
                        $propertyTypeSlug = isset($propertyTypes[$produto->property_type])
                            ? Str::slug($propertyTypes[$produto->property_type])
                            : null;

                        $link =
                            $produto->property_type !== null
                                ? route('produtos.showProductWithPropertyType', [
                                    'categoria' => $categorySlug,
                                    'propertyType' => $propertyTypeSlug,
                                    'id' => $produto->id,
                                ])
                                : route('produtos.showProduct', [
                                    'slug' => $categorySlug,
                                    'id' => $produto->id,
                                ]);
                    @endphp


                    <div class="card-produto">
                        <a href="{{ $link }}">
                            @php
                                $imagens = json_decode($produto->image);
                                $primeiraImagem = $imagens[0] ?? null;
                                $precoFormatado = number_format($produto->price, 2, ',', '.');
                            @endphp

                            @if ($primeiraImagem)
                                <img src="{{ asset('storage/' . $primeiraImagem) }}" alt="{{ $produto->title }}">
                            @else
                                <img src="{{ asset('images/no-image.png') }}" alt="Sem imagem">
                            @endif

                            <div class="produto-descricao">
                                <span>REF: {{ $produto->id }}</span>
                                <h4>{{ \Illuminate\Support\Str::limit($produto->title, 50) }}</h4>
                            </div>

                            <p><i class="fa-solid fa-location-dot"></i>{{ $produto->city }} | {{ $produto->state }}</p>

                            <div class="produto-valor">
                                <i class="fa-solid fa-arrow-right"></i>
                                <p>R$ {{ $precoFormatado }}</p>
                            </div>

                            <span>
                                {{ $produto->property_type !== null ? $propertyTypes[$produto->property_type] ?? $produto->property_type : strtoupper($produto->category->title) }}
                            </span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
