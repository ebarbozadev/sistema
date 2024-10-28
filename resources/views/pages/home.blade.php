@extends('layouts.layout')

@section('content')
    @include('layouts.bannerPrincipal')
    @include('layouts.informacoes')

    <div id="divisao">
        <div id="cursos">
            @include('layouts.cursos')
        </div>
        <div id="principal">
            @include('layouts.videoPrincipal')
            @include('layouts.categorias')
        </div>
        <div id="publicidades">
            @include('layouts.publicidades')
        </div>
    </div>
    @include('layouts.videoSecundario')
    @include('layouts.planos')
    @include('layouts.parceiros')
    @include('layouts.rodape')
@endsection
