@extends('voyager::master')

@section('content')
<div class="container">
    <h1>Informações da Movimentação</h1>
    <p><strong>ID:</strong> {{ $movimentacao->ID }}</p>
    <p><strong>Tipo:</strong> {{ $movimentacao->TIPO_MOVIMENTACAO }}</p>
    <p><strong>Descrição:</strong> {{ $movimentacao->DESCRICAO }}</p>
    <p><strong>Valor:</strong> R$ {{ number_format($movimentacao->VALOR, 2, ',', '.') }}</p>
    <p><strong>Data:</strong> {{ $movimentacao->DATA_MOVIMENTACAO->format('d/m/Y H:i') }}</p>
</div>
@endsection