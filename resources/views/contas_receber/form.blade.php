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
            <select class="form-control" id="fornecedor" name="id_fornecedor" required>
                <option value="">Selecione o Fornecedor</option>
                <!-- Inserir fornecedores do banco -->
            </select>
        </div>

        <div class="form-group">
            <label for="data_pagamento">Data de Pagamento</label>
            <input type="date" class="form-control" id="data_pagamento" name="data_pagamento" value="{{ $conta->data_pagamento ?? old('data_pagamento') }}">
        </div>

        <div class="form-group">
            <label for="parcela">Parcela</label>
            <input type="text" class="form-control" id="parcela" name="parcela" value="{{ $conta->parcela ?? old('parcela') }}">
        </div>

        <div class="form-group">
            <label for="descricao">Descrição</label>
            <input type="text" class="form-control" id="descricao" name="descricao" value="{{ $conta->descricao ?? old('descricao') }}" required>
        </div>

        <div class="form-group">
            <label for="valor">Valor</label>
            <input type="number" step="0.01" class="form-control" id="valor" name="valor" value="{{ $conta->valor ?? old('valor') }}" required>
        </div>

        <div class="form-group">
            <label for="data_vencimento">Data de Vencimento</label>
            <input type="date" class="form-control" id="data_vencimento" name="data_vencimento" value="{{ $conta->data_vencimento ?? old('data_vencimento') }}" required>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control" id="status" name="status" required>
                <option value="Pendente" {{ isset($conta) && $conta->status === 'Pendente' ? 'selected' : '' }}>Pendente</option>
                <option value="Pago" {{ isset($conta) && $conta->status === 'Pago' ? 'selected' : '' }}>Pago</option>
                <option value="Atrasado" {{ isset($conta) && $conta->status === 'Atrasado' ? 'selected' : '' }}>Atrasado</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">{{ isset($conta) ? 'Atualizar' : 'Salvar' }}</button>
    </form>
</div>
@endsection