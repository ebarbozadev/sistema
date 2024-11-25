@extends('voyager::master')

@section('content')
<div class="page-content container-fluid">
    <h1>Lista de Clientes</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($clients as $client)
            <tr>
                <td>{{ $client->id }}</td>
                <td>{{ $client->name }}</td>
                <td>{{ $client->email }}</td>
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
@endsection