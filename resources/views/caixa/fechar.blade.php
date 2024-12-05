    @extends('voyager::master')

    @section('content')
    <div class="container">
        <h1>Fechar Caixa Anterior</h1>

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

        <p><strong>ID do Caixa:</strong> {{ $caixa->id }}</p>
        <p><strong>Saldo Inicial:</strong> R$ {{ number_format($caixa->saldo_inicial, 2, ',', '.') }}</p>
        <p><strong>Saldo Atual:</strong> R$ {{ number_format($caixa->saldo_atual, 2, ',', '.') }}</p>
        <p><strong>Data de Abertura:</strong> {{ $caixa->data_abertura }}</p>

        <form action="{{ route('caixa.fechar', $caixa->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="saldo_fechamento">Saldo de Fechamento:</label>
                <input type="number" step="0.01" name="saldo_fechamento" id="saldo_fechamento" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Fechar Caixa</button>
        </form>
    </div>
    @endsection