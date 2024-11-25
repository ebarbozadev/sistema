@extends('voyager::master')

@section('content')
<div id="frenteDeCaixa" class="container">
    <h1>Venda</h1>

    <div class="pdvDados">
        <div class="left-section">
            {{-- Formulário para Adicionar Produto --}}
            @include('components._add_product_form')

            {{-- Tabela de Produtos --}}
            @include('components._product_table', ['products' => $products])
        </div>

        <div class="right-section">
            {{-- Resumo do Pagamento --}}
            @include('components._payment_summary', ['summary' => $summary])

            {{-- Formulário de Pagamento --}}
            @include('components._payment_form')

            <div class="payments">
                <h3>Pagamentos Registrados</h3>
                <ul>
                    @forelse ($payments as $payment)
                    <li>
                        Método: {{ ucfirst($payment['method']) }} - Valor: R$ {{ number_format($payment['amount'], 2, ',', '.') }}
                    </li>
                    @empty
                    <li>Nenhum pagamento registrado ainda.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    {{-- Ações no Rodapé --}}
    @include('components._footer_actions')
</div>
@endsection

<style>
    #frenteDeCaixa .pdvDados {
        display: flex;
        justify-content: space-between;
        gap: 20px;
    }
</style>