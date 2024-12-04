@extends('voyager::master')

@section('page_title', 'Contas a Pagar')

@section('page_header')
<div class="container-fluid">
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-dollar"></i> Contas a Receber
        </h1>
        <a href="{{ route('voyager.pagar-contas.create') }}" class="btn btn-success btn-add-new">
            <i class="voyager-plus"></i> <span>Adicionar Nova</span>
        </a>
    </div>
</div>
@stop

@section('content')
<div class="page-content browse container-fluid">
    @include('voyager::alerts')

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Descrição</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Data de Vencimento</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contas as $conta)
                                <tr>
                                    <td>{{ $conta->id }}</td>
                                    <td>{{ $conta->descricao }}</td>
                                    <td>{{ number_format($conta->valor, 2, ',', '.') }}</td>
                                    <td>{{ $conta->status }}</td>
                                    <td>{{ $conta->data_vencimento }}</td>
                                    <td>
                                        <a href="{{ route('voyager.pagar-contas.edit', $conta) }}" class="btn btn-sm btn-warning">Editar</a>
                                        <form action="{{ route('voyager.pagar-contas.destroy', $conta) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Nenhuma conta encontrada.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Exibe a paginação -->
                    {{ $contas->links() }}
                </div>
            </div>

        </div>
    </div>
</div>
@stop