@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' Contas a Pagar')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-dollar"></i> Contas a Pagar
        </h1>
        @can('add', app(\App\Models\ContaPagar::class))
            <a href="{{ route('voyager.pagar-contas.create') }}" class="btn btn-success btn-add-new">
                <i class="voyager-plus"></i> <span>{{ __('voyager::generic.add_new') }}</span>
            </a>
        @endcan
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        @if ($dataTypeContent->isNotEmpty())
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
                                        @foreach($dataTypeContent as $conta)
                                            <tr>
                                                <td>{{ $conta->id }}</td>
                                                <td>{{ $conta->DESCRICAO }}</td>
                                                <td>R$ {{ number_format($conta->VALOR, 2, ',', '.') }}</td>
                                                <td>
                                                    @if ($conta->STATUS === 'Pago')
                                                        <span class="label label-success">{{ $conta->STATUS }}</span>
                                                    @elseif($conta->STATUS === 'Pendente')
                                                        <span class="label label-warning">{{ $conta->STATUS }}</span>
                                                    @else
                                                        <span class="label label-danger">{{ $conta->STATUS }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($conta->DATA_VENCIMENTO)->format('d/m/Y') }}</td>
                                                <td class="no-sort no-click bread-actions">
                                                    @can('edit', $conta)
                                                        <a href="{{ route('voyager.pagar-contas.edit', $conta->id) }}" title="Editar" class="btn btn-sm btn-primary pull-right edit">
                                                            <i class="voyager-edit"></i> <span>{{ __('voyager::generic.edit') }}</span>
                                                        </a>
                                                    @endcan
                                                    @can('delete', $conta)
                                                        <a href="javascript:;" title="Excluir" class="btn btn-sm btn-danger pull-right delete" data-id="{{ $conta->id }}" id="delete-{{ $conta->id }}">
                                                            <i class="voyager-trash"></i> <span>{{ __('voyager::generic.delete') }}</span>
                                                        </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{ $dataTypeContent->links() }}
                        @else
                            <p class="text-center">{{ __('voyager::generic.no_results') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de exclusão --}}
    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <i class="voyager-trash"></i> {{ __('voyager::generic.delete_question') }} Conta a Pagar?
                    </h4>
                </div>
                <div class="modal-footer">
                    <form action="#" id="delete_form" method="POST">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm" value="{{ __('voyager::generic.delete_confirm') }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <script>
        var deleteFormAction;
        $('td').on('click', '.delete', function (e) {
            $('#delete_form')[0].action = '{{ route('voyager.pagar-contas.destroy', '__id') }}'.replace('__id', $(this).data('id'));
            $('#delete_modal').modal('show');
        });
    </script>
@stop
