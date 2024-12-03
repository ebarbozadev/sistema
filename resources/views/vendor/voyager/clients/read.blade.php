@extends('voyager::master')

@section('page_title', __('voyager::generic.view').' '.$dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
<h1 class="page-title">
    <i class="{{ $dataType->icon }}"></i> {{ __('voyager::generic.viewing') }} {{ ucfirst($dataType->getTranslatedAttribute('display_name_singular')) }} &nbsp;

    @can('edit', $dataTypeContent)
    <a href="{{ route('voyager.'.$dataType->slug.'.edit', $dataTypeContent->getKey()) }}" class="btn btn-info">
        <i class="glyphicon glyphicon-pencil"></i> <span class="hidden-xs hidden-sm">{{ __('voyager::generic.edit') }}</span>
    </a>
    @endcan
    @can('delete', $dataTypeContent)
    @if($isSoftDeleted)
    <a href="{{ route('voyager.'.$dataType->slug.'.restore', $dataTypeContent->getKey()) }}" title="{{ __('voyager::generic.restore') }}" class="btn btn-default restore" data-id="{{ $dataTypeContent->getKey() }}" id="restore-{{ $dataTypeContent->getKey() }}">
        <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">{{ __('voyager::generic.restore') }}</span>
    </a>
    @else
    <a href="javascript:;" title="{{ __('voyager::generic.delete') }}" class="btn btn-danger delete" data-id="{{ $dataTypeContent->getKey() }}" id="delete-{{ $dataTypeContent->getKey() }}">
        <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">{{ __('voyager::generic.delete') }}</span>
    </a>
    @endif
    @endcan
    @can('browse', $dataTypeContent)
    <a href="{{ route('voyager.'.$dataType->slug.'.index') }}" class="btn btn-warning">
        <i class="glyphicon glyphicon-list"></i> <span class="hidden-xs hidden-sm">{{ __('voyager::generic.return_to_list') }}</span>
    </a>
    @endcan
</h1>
@include('voyager::multilingual.language-selector')
@stop

@section('content')
<div class="page-content read container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered" style="padding-bottom:5px;">
                <div class="panel-body">
                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Nome</h3>
                    </div>
                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->nome }}</p>
                    </div>

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Email</h3>
                    </div>
                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->email }}</p>
                    </div>

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Tipo de Pessoa</h3>
                    </div>
                    <div class="panel-body" style="padding-top:0;">
                        <p>
                            @if($dataTypeContent->tipo_pessoa == 'J')
                            Jurídica
                            @elseif($dataTypeContent->tipo_pessoa == 'F')
                            Física
                            @else
                            Não especificado
                            @endif
                        </p>
                    </div>

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Documento</h3>
                    </div>
                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->documento }}</p>
                    </div>

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Telefone Residencial</h3>
                    </div>
                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->telefone_residencial }}</p>
                    </div>

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Endereço Residencial</h3>
                    </div>
                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->endereco_residencial }}</p>
                    </div>

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Telefone Comercial</h3>
                    </div>
                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->telefone_comercial }}</p>
                    </div>

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Endereço Comercial</h3>
                    </div>
                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->endereco_comercial }}</p>
                    </div>

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Telefone Outro</h3>
                    </div>
                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->telefone_outro }}</p>
                    </div>

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Endereço Outro</h3>
                    </div>
                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ $dataTypeContent->endereco_outro }}</p>
                    </div>

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Status</h3>
                    </div>
                    <div class="panel-body" style="padding-top:0;">
                        <p>
                            @if($dataTypeContent->ativo == 1)
                            Active
                            @else
                            Inactive
                            @endif
                        </p>
                    </div>

                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Data de Aniversário</h3>
                    </div>
                    <div class="panel-body" style="padding-top:0;">
                        <p>{{ \Carbon\Carbon::parse($dataTypeContent->data_aniversario)->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Single delete modal --}}
<div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="voyager-trash"></i> {{ __('voyager::generic.delete_question') }} {{ strtolower($dataType->getTranslatedAttribute('display_name_singular')) }}?</h4>
            </div>
            <div class="modal-footer">
                <form action="{{ route('voyager.'.$dataType->slug.'.index') }}" id="delete_form" method="POST">
                    {{ method_field('DELETE') }}
                    {{ csrf_field() }}
                    <input type="submit" class="btn btn-danger pull-right delete-confirm"
                        value="{{ __('voyager::generic.delete_confirm') }} {{ strtolower($dataType->getTranslatedAttribute('display_name_singular')) }}">
                </form>
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@stop

@section('javascript')
@if ($isModelTranslatable)
<script>
    $(document).ready(function() {
        $('.side-body').multilingual();
    });
</script>
@endif
<script>
    var deleteFormAction;
    $('.delete').on('click', function(e) {
        var form = $('#delete_form')[0];

        if (!deleteFormAction) {
            // Save form action initial value
            deleteFormAction = form.action;
        }

        form.action = deleteFormAction.match(/\/[0-9]+$/) ?
            deleteFormAction.replace(/([0-9]+$)/, $(this).data('id')) :
            deleteFormAction + '/' + $(this).data('id');

        $('#delete_modal').modal('show');
    });
</script>
@stop