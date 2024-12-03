@extends('voyager::master')

@section('page_title', 'Caixas')

@section('content')
<div class="page-content container-fluid">
    <h1>Gestão de Caixas</h1>
    <div class="panel panel-bordered">
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Saldo Inicial</th>
                        <th>Saldo Atual</th>
                        <th>Status</th>
                        <th>Data de Abertura</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($caixas as $caixa)
                    <tr>
                        <td>{{ $caixa->id }}</td>
                        <td>{{ number_format($caixa->saldo_inicial, 2, ',', '.') }}</td>
                        <td>{{ number_format($caixa->saldo_atual, 2, ',', '.') }}</td>
                        <td>{{ $caixa->status }}</td>
                        <td>{{ $caixa->data_abertura }}</td>
                        <td>
                            <a href="{{ route('voyager.caixas.edit', $caixa->id) }}" class="btn btn-sm btn-primary">Editar</a>
                            <a href="{{ route('voyager.caixas.info', $caixa->id) }}" class="btn btn-sm btn-info">Detalhes</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">Nenhum caixa encontrado.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop