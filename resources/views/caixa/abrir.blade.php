@extends('voyager::master')

@section('content')
<div class="container">
    <h1>Abrir Caixa</h1>

    <!-- Mensagens de Erro ou Sucesso -->
    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('caixa.abrir') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="saldo_inicial">Saldo Inicial:</label>
            <input type="number" step="0.01" name="saldo_inicial" id="saldo_inicial" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success mt-2">Abrir Caixa</button>
    </form>
</div>
@endsection