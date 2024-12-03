@extends('voyager::master')

@section('page_title', 'Clients')

@section('content')
<div class="page-content container-fluid">
    <div class="panel panel-bordered">
        <div class="panel-heading">
            <h3 class="panel-title">Lista de Clientes</h3>
            <div class="panel-actions">
                <!-- Botão para cadastrar -->
                <a href="{{ route('voyager.clients.create') }}" class="btn btn-success">
                    <i class="voyager-plus"></i> Novo Cliente
                </a>
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Tipo de Pessoa</th>
                        <th>Documento</th>
                        <th>Telefone Residencial</th>
                        <th>Endereço</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clients as $client)
                    <tr>
                        <td>{{ $client->nome }}</td>
                        <td>{{ $client->email }}</td>
                        <td>{{ $client->tipo_pessoa == 'F' ? 'Física' : 'Jurídica' }}</td>
                        <td>{{ $client->documento }}</td>
                        <td>{{ $client->telefone_residencial }}</td>
                        <td>{{ $client->endereco_residencial }}</td>
                        <td>{{ $client->ativo == 1 ? 'Ativo' : 'Inativo' }}</td>
                        <td>
                            <a href="{{ route('voyager.clients.edit', $client->id) }}" class="btn btn-sm btn-primary">Editar</a>

                            <form action="{{ route('voyager.clients.destroy', $client->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $clients->links() }} <!-- Paginação -->
        </div>
    </div>
</div>
@endsection