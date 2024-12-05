@extends('voyager::master')

@section('content')
<div class="container">
    <h1>Detalhes do Caixa</h1>

    <p><strong>ID do Caixa:</strong> {{ $caixa->id }}</p>
    <p><strong>Saldo Inicial:</strong> R$ {{ number_format($caixa->saldo_inicial, 2, ',', '.') }}</p>
    <p><strong>Saldo Atual:</strong> R$ {{ number_format($caixa->saldo_atual, 2, ',', '.') }}</p>
    <p><strong>Saldo de Fechamento:</strong>
        @if($caixa->saldo_fechamento !== null)
        R$ {{ number_format($caixa->saldo_fechamento, 2, ',', '.') }}
        @else
        -
        @endif
    </p>
    <p><strong>Data de Abertura:</strong> {{ $caixa->data_abertura ? \Carbon\Carbon::parse($caixa->data_abertura)->format('d/m/Y') : '-' }}</p>
    <p><strong>Data de Fechamento:</strong> {{ $caixa->data_fechamento ? \Carbon\Carbon::parse($caixa->data_fechamento)->format('d/m/Y') : '-' }}</p>
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
                <td>{{ $movimentacao->data_movimentacao ? \Carbon\Carbon::parse($movimentacao->data_movimentacao)->format('d/m/Y') : '-' }}</td>
                <td>{{ $movimentacao->descricao }}</td>
                <td>R$ {{ number_format($movimentacao->valor, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>Não há movimentações neste caixa.</p>
    @endif

    <h3>Vendas no Caixa</h3>
    @if ($vendas->count() > 0)
    @foreach ($vendas as $venda)
    <div class="venda">
        <h4>Venda ID: {{ $venda->id }}</h4>
        <p><strong>Data da Venda:</strong> {{ $venda->data_venda ? \Carbon\Carbon::parse($venda->data_venda)->format('d/m/Y') : '-' }}</p>
        <p><strong>Total da Venda:</strong> R$ {{ number_format($venda->vl_total, 2, ',', '.') }}</p>
        <p><strong>Status:</strong> {{ $venda->status }}</p>

        <h5>Itens Vendidos:</h5>
        @if ($venda->itens->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Valor Unitário</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($venda->itens as $item)
                <tr>
                    <td>{{ $item->produto->nome ?? 'N/A' }}</td>
                    <td>{{ $item->quantidade }}</td>
                    <td>R$ {{ number_format($item->vl_unitario, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($item->vl_total, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p>Não há itens vendidos nesta venda.</p>
        @endif
    </div>
    @endforeach
    @else
    <p>Não há vendas registradas neste caixa.</p>
    @endif
</div>
@endsection