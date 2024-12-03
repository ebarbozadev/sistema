@extends('voyager::master')

@section('content')
<div class="container">
    <h1>Editar Movimentação</h1>
    <form method="POST" action="{{ route('caixa.movimentacao.store', $movimentacao->ID) }}">
        @csrf
        <div class="form-group">
            <label>Tipo de Movimentação</label>
            <select name="TIPO_MOVIMENTACAO" class="form-control" required>
                <option value="Venda" {{ $movimentacao->TIPO_MOVIMENTACAO == 'Venda' ? 'selected' : '' }}>Venda</option>
                <option value="Sangria" {{ $movimentacao->TIPO_MOVIMENTACAO == 'Sangria' ? 'selected' : '' }}>Sangria</option>
                <option value="Suprimento" {{ $movimentacao->TIPO_MOVIMENTACAO == 'Suprimento' ? 'selected' : '' }}>Suprimento</option>
                <option value="Pagamento" {{ $movimentacao->TIPO_MOVIMENTACAO == 'Pagamento' ? 'selected' : '' }}>Pagamento</option>
                <option value="Recebimento" {{ $movimentacao->TIPO_MOVIMENTACAO == 'Recebimento' ? 'selected' : '' }}>Recebimento</option>
            </select>
        </div>
        <div class="form-group">
            <label>Valor</label>
            <input type="number" name="VALOR" class="form-control" value="{{ $movimentacao->VALOR }}" step="0.01" required>
        </div>
        <div class="form-group">
            <label>Descrição</label>
            <input type="text" name="DESCRICAO" class="form-control" value="{{ $movimentacao->DESCRICAO }}">
        </div>
        <button type="submit" class="btn btn-success">Salvar</button>
    </form>
</div>
@endsection