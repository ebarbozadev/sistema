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

        <p><strong>ID do Caixa:</strong> {{ $caixaAberto->id }}</p>
        <p><strong>Saldo Inicial:</strong> R$ {{ number_format($caixaAberto->saldo_inicial, 2, ',', '.') }}</p>
        <p><strong>Saldo Atual:</strong> R$ {{ number_format($caixaAberto->saldo_atual, 2, ',', '.') }}</p>
        <p><strong>Data de Abertura:</strong> {{ $caixaAberto->data_abertura->format('d/m/Y H:i') }}</p>
        <p>Aqui: {{now()}}</p>

        <form action="{{ route('caixa.fecharAnterior', $caixaAberto->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="saldo_fechamento">Saldo de Fechamento</label>
                <input type="number" name="saldo_fechamento" id="saldo_fechamento" class="form-control" step="0.01" required>
            </div>
            <button type="submit" class="btn btn-primary">Fechar Caixa</button>
        </form>
    </div>
    @endsection