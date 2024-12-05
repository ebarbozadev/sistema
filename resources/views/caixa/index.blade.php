@extends('voyager::master')

@section('content')
<div class="container">
    <h1>Caixas</h1>

    <!-- Mensagens de Sucesso ou Erro -->
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <!-- Botão para Visualizar o Caixa Atual ou Abrir um Novo Caixa -->
    @if($caixaAberto)
    <a href="{{ route('caixa.detalhes', $caixaAberto->id) }}" class="btn btn-primary mb-3">
        Visualizar Caixa Atual
    </a>
    @else
    <a href="{{ route('caixa.abrir.form') }}" class="btn btn-success mb-3">
        Abrir Caixa
    </a>
    @endif

    <!-- Tabela de Caixas -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Empresa</th>
                <th>Usuário</th>
                <th>Saldo Inicial</th>
                <th>Saldo Atual</th>
                <th>Status</th>
                <th>Data de Abertura</th>
                <th>Data de Fechamento</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($caixas as $caixa)
            <tr>
                <td>{{ $caixa->id }}</td>
                <td>{{ $caixa->empresa->nome ?? 'N/A' }}</td>
                <td>{{ $caixa->usuario->name ?? 'N/A' }}</td>
                <td>R$ {{ number_format($caixa->saldo_inicial, 2, ',', '.') }}</td>
                <td>R$ {{ number_format($caixa->saldo_atual, 2, ',', '.') }}</td>
                <td>{{ $caixa->status }}</td>
                <td>{{ $caixa->data_abertura ? \Carbon\Carbon::parse($caixa->data_abertura)->format('d/m/Y') : '-' }}</td>
                <td>{{ $caixa->data_fechamento ? \Carbon\Carbon::parse($caixa->data_fechamento)->format('d/m/Y') : '-' }}</td>
                <td>
                    <a href="{{ route('caixa.detalhes', $caixa->id) }}" class="btn btn-info btn-sm">Detalhes</a>

                    @if($caixa->status === 'Aberto')
                    <form action="{{ route('caixa.fechar', $caixa->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja fechar este caixa?')">Fechar Caixa</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Paginação -->
    {{ $caixas->links() }}
</div>
@endsection