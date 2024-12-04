@extends('voyager::master')

@section('content')
<div class="container">
    <h1>Detalhes do Caixa</h1>

    <p><strong>ID do Caixa:</strong> {{ $caixa->id }}</p>
    <p><strong>Saldo Inicial:</strong> R$ {{ number_format($caixa->saldo_inicial, 2, ',', '.') }}</p>
    <p><strong>Saldo Atual:</strong> R$ {{ number_format($caixa->saldo_atual, 2, ',', '.') }}</p>
    <p><strong>Data de Abertura:</strong> {{ $caixa->data_abertura }}</p>
    <p><strong>Data de Fechamento:</strong> {{ $caixa->data_fechamento ? $caixa->data_fechamento : '-' }}</p>
    <p><strong>Status:</strong> {{ $caixa->status }}</p>

    <h3>Movimentações</h3>
    @if ($caixa->movimentacoes->count() > 0)
    <table class="table">
        <thead>
            <tr>
                <th>Data</th>
                <th>Descrição</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($caixa->movimentacoes as $movimentacao)
            <tr>
                <td>{{ $movimentacao->DATA_MOVIMENTACAO }}</td>
                <td>{{ $movimentacao->DESCRICAO }}</td>
                <td>R$ {{ number_format($movimentacao->VALOR, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>Não há movimentações neste caixa.</p>
    @endif
</div>
@endsection