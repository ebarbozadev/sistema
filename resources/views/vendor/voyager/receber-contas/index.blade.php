@extends('voyager::master')

@section('page_title', 'Contas a Receber')

@section('content')
<div class="page-content container-fluid">
    <h1>Gestão de Contas a Receber</h1>
    <div class="panel panel-bordered">
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descrição</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Data de Vencimento</th>
                        <th>Data de Recebimento</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contasReceber as $conta)
                    <tr>
                        <td>{{ $conta->id }}</td>
                        <td>{{ $conta->descricao }}</td>
                        <td>{{ number_format($conta->valor, 2, ',', '.') }}</td>
                        <td>{{ $conta->status }}</td>
                        <td>{{ $conta->data_vencimento }}</td>
                        <td>{{ $conta->data_recebimento }}</td>
                        <td>
                            <a href="{{ route('voyager.receber-contas.edit', $conta->id) }}" class="btn btn-sm btn-warning">Editar</a>
                            <form action="{{ route('voyager.receber-contas.destroy', $conta->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">Nenhuma conta encontrada.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop