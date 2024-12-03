@extends('voyager::master')

@section('content')
<div class="container">
    <h1>Abrir Caixa</h1>
    <form method="POST" action="{{ route('caixa.movimentacao.store') }}">
        @csrf
        <div class="form-group">
            <label>Saldo Inicial</label>
            <input type="number" name="SALDO_INICIAL" class="form-control" step="0.01" required>
        </div>
        <button type="submit" class="btn btn-success">Abrir Caixa</button>
    </form>
</div>
@endsection