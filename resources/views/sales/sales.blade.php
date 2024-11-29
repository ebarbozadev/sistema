@extends('voyager::master')

@section('content')
<div id="frenteDeCaixa" class="container">
    <h1>Venda</h1>

    {{-- Formulário para Adicionar Produto --}}
    @include('components._add_product_form')

    {{-- Tabela de Produtos --}}
    @include('components._product_table', ['products' => $products])

    {{-- Resumo do Pagamento --}}
    
    {{-- Formulário de Pagamento --}}
    <!-- @include('components._payment_form') -->
    
    @include('components.infoVenda')
    
    @include('components._payment_section')
    
    @include('components._payment_summary', ['summary' => $summary])
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