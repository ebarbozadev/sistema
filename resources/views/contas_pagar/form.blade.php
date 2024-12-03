@extends('voyager::master')

@section('content')
<div class="container">
    <h1>{{ isset($conta) ? 'Editar Conta' : 'Nova Conta' }}</h1>
    <form method="POST" action="{{ isset($conta) ? route('voyager.pagar-contas.update', $conta->id) : route('voyager.pagar-contas.store') }}">
        @csrf
        @if(isset($conta))
        @method('PUT')
        @endif

        <div class="form-group">
            <label for="fornecedor">Fornecedor</label>
            <select class="form-control" id="fornecedor" name="ID_FORNECEDOR" required>
                <option value="">Selecione o Fornecedor</option>
                <!-- Inserir fornecedores do banco -->
            </select>
        </div>

        <div class="form-group">
            <label for="data_pagamento">Data de Pagamento</label>
            <input type="date" class="form-control" id="data_pagamento" name="DATA_PAGAMENTO" value="{{ $conta->DATA_PAGAMENTO ?? old('DATA_PAGAMENTO') }}">
        </div>

        <div class="form-group">
            <label for="parcela">Parcela</label>
            <input type="text" class="form-control" id="parcela" name="PARCELA" value="{{ $conta->PARCELA ?? old('PARCELA') }}">
        </div>

        <div class="form-group">
            <label for="descricao">Descrição</label>
            <input type="text" class="form-control" id="descricao" name="DESCRICAO" value="{{ $conta->DESCRICAO ?? old('DESCRICAO') }}" required>
        </div>

        <div class="form-group">
            <label for="valor">Valor</label>
            <input type="number" step="0.01" class="form-control" id="valor" name="VALOR" value="{{ $conta->VALOR ?? old('VALOR') }}" required>
        </div>

        <div class="form-group">
            <label for="data_vencimento">Data de Vencimento</label>
            <input type="date" class="form-control" id="data_vencimento" name="DATA_VENCIMENTO" value="{{ $conta->DATA_VENCIMENTO ?? old('DATA_VENCIMENTO') }}" required>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control" id="status" name="STATUS" required>
                <option value="Pendente" {{ isset($conta) && $conta->STATUS === 'Pendente' ? 'selected' : '' }}>Pendente</option>
                <option value="Pago" {{ isset($conta) && $conta->STATUS === 'Pago' ? 'selected' : '' }}>Pago</option>
                <option value="Atrasado" {{ isset($conta) && $conta->STATUS === 'Atrasado' ? 'selected' : '' }}>Atrasado</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">{{ isset($conta) ? 'Atualizar' : 'Salvar' }}</button>
    </form>
</div>
@endsection