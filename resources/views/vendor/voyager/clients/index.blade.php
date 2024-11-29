@extends('voyager::master')

@section('page_title', 'Clients')

@section('content')
<div class="page-content container-fluid">
    <div class="panel panel-bordered">
        <div class="panel-heading">
            <h3 class="panel-title">Lista de Clientes</h3>
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
                        <td>{{ $client->name }}</td>
                        <td>{{ $client->email }}</td>
                        <td>{{ $client->tp_people == 'f' ? 'Física' : 'Jurídica' }}</td>
                        <td>{{ $client->document }}</td>
                        <td>{{ $client->telephone_res }}</td>
                        <td>{{ $client->address }}</td>
                        <td>{{ $client->status == 1 ? 'Ativo' : 'Inativo' }}</td>
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