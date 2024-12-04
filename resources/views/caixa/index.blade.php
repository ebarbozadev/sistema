@extends('voyager::master')

@section('content')
<div class="container">
    <h1>Gerenciar Caixa</h1>

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    @if ($caixaAberto)
    <h3>Caixa Aberto</h3>
    <p><strong>ID do Caixa:</strong> {{ $caixaAberto->id }}</p>
    <p><strong>Saldo Inicial:</strong> R$ {{ number_format($caixaAberto->saldo_inicial, 2, ',', '.') }}</p>
    <p><strong>Saldo Atual:</strong> R$ {{ number_format($caixaAberto->saldo_atual, 2, ',', '.') }}</p>
    <p><strong>Data de Abertura:</strong> {{ $caixaAberto->data_abertura }}</p>

    <form action="{{ route('caixa.fechar', $caixaAberto->id) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-danger">Fechar Caixa</button>
    </form>
    @else
    <h3>Nenhum Caixa Aberto</h3>
    <form action="{{ route('caixa.abrir') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="saldo_inicial">Saldo Inicial</label>
            <input type="number" name="saldo_inicial" id="saldo_inicial" class="form-control" step="0.01" min="0" required>
        </div>
        <button type="submit" class="btn btn-primary">Abrir Caixa</button>
    </form>
    @endif
</div>
@endsection