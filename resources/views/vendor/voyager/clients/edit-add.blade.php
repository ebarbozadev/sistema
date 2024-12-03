@php
$edit = !is_null($dataTypeContent) && !is_null($dataTypeContent->getKey());
$add = is_null($dataTypeContent) || is_null($dataTypeContent->getKey());

$edit = !is_null($dataTypeContent->getKey());
$add = is_null($dataTypeContent->getKey());

$rows = $dataType->{$edit ? 'editRows' : 'addRows'};
// dd($row->details); // Descomente esta linha para ver o conteúdo de details
foreach ($rows as $row) {
if ($row->field == 'fuel_type') {
$fuelTypeOptions = (array) $row->details;
}

if ($row->field == 'exchange') {
if (isset($row->details->options)) {
$exchangeOptions = (array) $row->details->options;
} else {
$exchangeOptions = (array) $row->details;
}
}

if ($row->field == 'property_type') {
if (isset($row->details->options)) {
$propertyTypeOptions = (array) $row->details->options;
} else {
$propertyTypeOptions = (array) $row->details;
}
}

if ($row->field == 'gender') {
if (isset($row->details->options)) {
$genderOptions = (array) $row->details->options;
} else {
$genderOptions = (array) $row->details;
}
}

if ($row->field == 'has_elevator') {
if (isset($row->details->options)) {
$hasElevatorOptions = (array) $row->details->options;
} else {
$hasElevatorOptions = (array) $row->details;
}
}

if ($row->field == 'factory_warranty') {
if (isset($row->details->options)) {
$factoryWarrantyOptions = (array) $row->details->options;
} else {
$factoryWarrantyOptions = (array) $row->details;
}
}
}
@endphp

@extends('voyager::master')

@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    .panel .mce-panel {
        border-left-color: #fff;
        border-right-color: #fff;
    }

    .panel .mce-toolbar,
    .panel .mce-statusbar {
        padding-left: 20px;
    }

    .panel .mce-edit-area,
    .panel .mce-edit-area iframe,
    .panel .mce-edit-area iframe html {
        padding: 0 10px;
        min-height: 350px;
    }

    .mce-content-body {
        color: #555;
        font-size: 14px;
    }

    .panel.is-fullscreen .mce-statusbar {
        position: absolute;
        bottom: 0;
        width: 100%;
        z-index: 200000;
    }

    .panel.is-fullscreen .mce-tinymce {
        height: 100%;
    }

    .panel.is-fullscreen .mce-edit-area,
    .panel.is-fullscreen .mce-edit-area iframe,
    .panel.is-fullscreen .mce-edit-area iframe html {
        height: 100%;
        position: absolute;
        width: 99%;
        overflow-y: scroll;
        overflow-x: hidden;
        min-height: 100%;
    }

    .nav-tabs {
        display: flex;
        border-bottom: 1px solid #dee2e6;
        padding-left: 0;
        margin-bottom: 0;
        list-style: none;
    }

    .nav-tabs .nav-item {
        margin-bottom: -1px;
    }

    .nav-tabs .nav-link {
        display: block;
        padding: 10px 20px;
        cursor: pointer;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-bottom: none;
        color: #495057;
        text-decoration: none;
        border-radius: 4px 4px 0 0;
    }

    .nav-tabs .nav-link.active {
        background-color: #ffffff;
        color: #495057;
        border-color: #dee2e6 #dee2e6 #ffffff;
    }

    /* Estilos básicos do conteúdo das abas */
    .tab-content {
        border: 1px solid #dee2e6;
        border-radius: 0 4px 4px 4px;
        padding: 20px 0;
        background-color: #ffffff;
    }

    .tab-pane {
        display: none;
        padding: 20px;
    }

    .tab-pane.active {
        display: block;
    }
</style>
@stop

@section('page_title', __('voyager::generic.' . ($edit ? 'edit' : 'add')) . ' ' .
$dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
<h1 class="page-title">
    <i class="{{ $dataType->icon }}"></i>
    {{ __('voyager::generic.' . ($edit ? 'edit' : 'add')) . ' ' . $dataType->getTranslatedAttribute('display_name_singular') }}
</h1>
@include('voyager::multilingual.language-selector')
@stop

@section('content')

<div class="page-content container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <form role="form" class="form-edit-add"
                    action="@if ($edit) {{ route('voyager.' . $dataType->slug . '.update', $dataTypeContent->getKey()) }}@else{{ route('voyager.' . $dataType->slug . '.store') }} @endif"
                    method="POST" enctype="multipart/form-data">
                    @if ($edit)
                    {{ method_field('PUT') }}
                    @endif
                    {{ csrf_field() }}

                    <div class="panel-body">
                        <div class="form-group">
                            <label for="nome">Nome</label>
                            <input required type="text" class="form-control" id="nome" name="nome"
                                placeholder="Nome" value="{{ old('nome', $dataTypeContent->nome ?? '') }}">
                        </div>

                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input required type="email" class="form-control" id="email" name="email"
                                placeholder="E-mail" value="{{ old('email', $dataTypeContent->email ?? '') }}">
                        </div>

                        <div class="form-group">
                            <label for="tipo_pessoa">Tipo de Pessoa</label>
                            <select name="tipo_pessoa" id="tipo_pessoa" class="form-control">
                                <option value="">Selecione</option>
                                <option value="f" {{ old('tipo_pessoa', $dataTypeContent->tipo_pessoa ?? '') == 'f' ? 'selected' : '' }}>Física</option>
                                <option value="j" {{ old('tipo_pessoa', $dataTypeContent->tipo_pessoa ?? '') == 'j' ? 'selected' : '' }}>Jurídica</option>
                            </select>
                        </div>


                        <div class="form-group">
                            <label for="documento">Documento</label>
                            <input required type="text" class="form-control" id="documento" name="documento"
                                placeholder="Documentoo" value="{{ old('documento', $dataTypeContent->documento ?? '') }}">
                        </div>

                        <div class="form-group">
                            <label for="data_nascimento">Data de Nascimento</label>
                            <input type="date" class="form-control" id="data_nascimento" name="data_nascimento"
                                value="{{ old('data_nascimento', $dataTypeContent->data_nascimento)}}">
                        </div>

                        <div class="form-group">
                            <label for="ativoActive">Status</label>

                            <label for="ativoActive">
                                <input {{ ($dataTypeContent->ativo == "1" || $dataTypeContent->ativo === null) ? 'checked' : '' }} type="radio" name="ativo" id="ativoActive" value="1">

                                Ativo
                            </label>

                            <label for="ativoInactive">
                                <input {{ ($dataTypeContent->ativo == "0") ? 'checked' : '' }} type="radio" name="ativo" id="ativoActive" value="1">
                                Inativo
                            </label>
                        </div>

                        <div class="form-group">
                            <!-- Abas de Navegação -->
                            <ul class="nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active" data-tab="residencial" onclick="openTab(event, 'residencial')">Residencial</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-tab="comercial" onclick="openTab(event, 'comercial')">Comercial</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-tab="outro" onclick="openTab(event, 'outro')">Outro</a>
                                </li>
                            </ul>

                            <!-- Conteúdo das Abas -->
                            <div class="tab-content">
                                <!-- Residencial -->
                                <div class="tab-pane active" id="residencial">
                                    <div class="form-group">
                                        <label for="telefone_residencial">Telefone Residencial</label>
                                        <input type="text" class="form-control" id="telefone_residencial" name="telefone_residencial"
                                            value="{{ old('telefone_residencial', $dataTypeContent->telefone_residencial ?? '') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="endereco_residencial">Endereço Residencial</label>
                                        <input type="text" class="form-control" id="endereco_residencial" name="endereco_residencial"
                                            value="{{ old('endereco_residencial', $dataTypeContent->endereco_residencial ?? '') }}">
                                    </div>
                                </div>

                                <!-- Comercial -->
                                <div class="tab-pane" id="comercial">
                                    <div class="form-group">
                                        <label for="telefone_comercial">Telefone Comercial</label>
                                        <input type="text" class="form-control" id="telefone_comercial" name="telefone_comercial"
                                            value="{{ old('telefone_comercial', $dataTypeContent->telefone_comercial ?? '') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="endereco_comercial">Endereço Comercial</label>
                                        <input type="text" class="form-control" id="endereco_comercial" name="endereco_comercial"
                                            value="{{ old('endereco_comercial', $dataTypeContent->endereco_comercial ?? '') }}">
                                    </div>
                                </div>

                                <!-- Outro -->
                                <div class="tab-pane" id="outro">
                                    <div class="form-group">
                                        <label for="telefone_outros">Telefone Outro</label>
                                        <input type="text" class="form-control" id="telefone_outros" name="telefone_outros"
                                            value="{{ old('telefone_outros', $dataTypeContent->telefone_outros ?? '') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="endereco_outros">Endereço Outro</label>
                                        <input type="text" class="form-control" id="endereco_outros" name="endereco_outros"
                                            value="{{ old('endereco_outros', $dataTypeContent->endereco_outros ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-footer">
                        <button type="submit" class="btn btn-primary save">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-danger" id="confirm_delete_modal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}
                </h4>
            </div>

            <div class="modal-body">
                <h4>{{ __('voyager::generic.are_you_sure_delete') }} '<span class="confirm_delete_name"></span>'</h4>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                    data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                <button type="button" class="btn btn-danger"
                    id="confirm_delete">{{ __('voyager::generic.delete_confirm') }}</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('javascript')

<script>
    var params = {};
    var $file;

    function openTab(event, tabName) {
        // Remove a classe 'active' de todas as abas e conteúdos
        let tabLinks = document.querySelectorAll('.nav-link');
        let tabContents = document.querySelectorAll('.tab-pane');

        tabLinks.forEach(link => link.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));

        // Adiciona a classe 'active' à aba e conteúdo clicados
        event.currentTarget.classList.add('active');
        document.getElementById(tabName).classList.add('active');
    }


    function deleteHandler(tag, isMulti) {
        return function() {
            $file = $(this).siblings(tag);

            params = {
                slug: '{{ $dataType->slug }}',
                filename: $file.data('file-name'),
                id: $file.data('id'),
                field: $file.closest('.img_settings_container').data('field-name'),
                multi: isMulti,
                _token: '{{ csrf_token() }}'
            }

            $('.confirm_delete_name').text(params.filename);
            $('#confirm_delete_modal').modal('show');
        };
    }

    $('document').ready(function() {


        $('#slug').slugify();

        $('.toggleswitch').bootstrapToggle();

        //Init datepicker for date fields if data-datepicker attribute defined
        //or if browser does not handle date inputs
        $('.form-group input[type=date]').each(function(idx, elt) {
            if (elt.type != 'date' || elt.hasAttribute('data-datepicker')) {
                elt.type = 'text';
                $(elt).datetimepicker($(elt).data('datepicker'));
            }
        });

        @if($isModelTranslatable)
        $('.side-body').multilingual({
            "editing": true
        });
        @endif

        $('.side-body input[data-slug-origin]').each(function(i, el) {
            $(el).slugify();
        });

        $(document).on('click', '.remove-multi-image', deleteHandler('img', true));
        $(document).on('click', '.remove-single-image', deleteHandler('img', false));
        $(document).on('click', '.remove-multi-file', deleteHandler('a', true));
        $(document).on('click', '.remove-single-file', deleteHandler('a', false));


        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@stop