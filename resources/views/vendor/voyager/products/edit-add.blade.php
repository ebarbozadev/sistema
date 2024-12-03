@php
$edit = !is_null($dataTypeContent->getKey());
$add = is_null($dataTypeContent->getKey());
$categories = \App\Models\Categoria::all();
$brands = \App\Models\Marca::all();
$lines = \App\Models\Linha::all();

$idCategoria = $dataTypeContent->id_categoria ?? '';
$idMarca = $dataTypeContent->id_marca ?? '';
$idLinha = $dataTypeContent->id_linha ?? '';
$typeCategoria = $idCategoria ? \App\Models\Categoria::find($idCategoria)->type ?? '' : '';

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
                        <!-- Nome -->
                        <div class="form-group">
                            <label for="nome">Nome</label>
                            <input type="text" class="form-control" id="nome" name="nome" required
                                value="{{ old('nome', $dataTypeContent->nome ?? '') }}">
                        </div>

                        <!-- Descrição -->
                        <div class="form-group">
                            <label for="descricao">Descrição</label>
                            <input type="text" class="form-control" id="descricao" name="descricao"
                                value="{{ old('descricao', $dataTypeContent->descricao ?? '') }}">
                        </div>

                        <!-- Imagens -->
                        <div class="form-group">
                            <label for="imagens">Imagens</label>
                            <input type="file" name="imagens[]" multiple="multiple" accept="image/*">
                            @if ($edit && isset($dataTypeContent->imagens))
                            @php
                            $imagens = json_decode($dataTypeContent->imagens, true);
                            @endphp
                            @foreach ($imagens as $imagem)
                            <div>
                                <!-- <a href="#" class="btn btn-danger btn-sm remove-image" data-id="{{ $imagem }}">Remover</a> -->
                                <img src="{{ Voyager::image($imagem) }}" style="max-width: 150px;">
                                <!-- <a href="#" class="btn btn-danger btn-sm">Remover</a> -->
                            </div>
                            @endforeach
                            @endif
                        </div>

                        <!-- Estoque -->
                        <div class="form-group">
                            <label for="estoque">Estoque</label>
                            <input type="number" class="form-control" id="estoque" name="estoque"
                                value="{{ old('estoque', $dataTypeContent->estoque ?? '') }}">
                        </div>

                        <!-- Estoque Mínimo -->
                        <div class="form-group">
                            <label for="estoque_minimo">Estoque Mínimo</label>
                            <input type="number" class="form-control" id="estoque_minimo" name="estoque_minimo"
                                value="{{ old('estoque_minimo', $dataTypeContent->estoque_minimo ?? '') }}">
                        </div>

                        <!-- Preço de Custo -->
                        <div class="form-group">
                            <label for="preco_custo">Preço de Custo</label>
                            <input type="text" class="form-control" id="preco_custo" name="preco_custo"
                                value="{{ old('preco_custo', $dataTypeContent->preco_custo ?? '') }}">
                        </div>

                        <!-- Preço de Venda -->
                        <div class="form-group">
                            <label for="preco_venda">Preço de Venda</label>
                            <input type="text" class="form-control" id="preco_venda" name="preco_venda"
                                value="{{ old('preco_venda', $dataTypeContent->preco_venda ?? '') }}">
                        </div>

                        <!-- Categoria -->
                        <div class="form-group">
                            <label for="id_categoria">Categoria</label>
                            <select name="id_categoria" id="id_categoria" class="form-control">
                                @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('id_categoria', $idCategoria) == $category->id ? 'selected' : '' }}>
                                    {{ $category->nome }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Marca -->
                        <div class="form-group">
                            <label for="id_marca">Marca</label>
                            <select name="id_marca" id="id_marca" class="form-control">
                                @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}"
                                    {{ old('id_marca', $idMarca) == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->nome }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Linha -->
                        <div class="form-group">
                            <label for="id_linha">Linha</label>
                            <select name="id_linha" id="id_linha" class="form-control">
                                @foreach ($lines as $line)
                                <option value="{{ $line->id }}"
                                    {{ old('id_linha', $idLinha) == $line->id ? 'selected' : '' }}>
                                    {{ $line->nome }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label for="ativo">Ativo</label>
                            <select name="ativo" id="ativo" class="form-control">
                                <option value="1" {{ old('ativo', $dataTypeContent->ativo ?? '1') == '1' ? 'selected' : '' }}>
                                    Sim
                                </option>
                                <option value="0" {{ old('ativo', $dataTypeContent->ativo ?? '1') == '0' ? 'selected' : '' }}>
                                    Não
                                </option>
                            </select>
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

    function init() {
        let categorias = @php echo $categories @endphp;
        let categoriaAtual = @php echo $idCategoria @endphp;

        var categoriaSelecionada = categorias.find(function(categoria) {
            return categoria.id == categoriaAtual;
        });

        if (categoriaSelecionada.type === 'O') {
            $("#outros").show();

            $("#automoveis").hide();
            $("#imoveis").hide();
        } else if (categoriaSelecionada.type === 'A') {
            $("#automoveis").show();

            $("#outros").hide();
            $("#imoveis").hide();
        } else if (categoriaSelecionada.type === 'I') {
            $("#imoveis").show();

            $("#automoveis").hide();
            $("#outros").hide();
        }
    }

    init();

    function showCategoryId() {
        let categoryId = $('#category_id').val();

        let categoriaOutros = $('#outros');
        let categoriaAutomoveis = $('#automoveis');
        let categoriaImoveis = $('#imoveis');

        let categorias = @php echo $categories @endphp;

        var categoriaSelecionada = categorias.find(function(categoria) {
            return categoria.id == categoryId;
        });

        if (categoriaSelecionada.type === 'O') {
            $("#outros").show();

            $("#automoveis").hide();
            $("#imoveis").hide();
        } else if (categoriaSelecionada.type === 'A') {
            $("#automoveis").show();

            $("#outros").hide();
            $("#imoveis").hide();
        } else if (categoriaSelecionada.type === 'I') {
            $("#imoveis").show();

            $("#automoveis").hide();
            $("#outros").hide();
        }
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

        $('#id_categoria').on('change', function() {
            const categoriaId = $(this).val();
            const categoria = categorias.find(c => c.id == categoriaId);

            if (categoria.type === 'O') {
                $("#outros").show();
                $("#automoveis, #imoveis").hide();
            } else if (categoria.type === 'A') {
                $("#automoveis").show();
                $("#outros, #imoveis").hide();
            } else if (categoria.type === 'I') {
                $("#imoveis").show();
                $("#outros, #automoveis").hide();
            }
        });

        $(document).on('click', '.remove-image', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            if (confirm('Tem certeza que deseja remover esta imagem?')) {
                $.ajax({
                    url: `/products/media/remove`,
                    type: 'POST',
                    data: {
                        id: id,
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function(response) {
                        alert(response.message);
                        location.reload();
                    },
                    error: function() {
                        alert('Erro ao remover imagem.');
                    }
                });
            }
        });


        $(document).on('click', '.remove-multi-image', deleteHandler('img', true));
        $(document).on('click', '.remove-single-image', deleteHandler('img', false));
        $(document).on('click', '.remove-multi-file', deleteHandler('a', true));
        $(document).on('click', '.remove-single-file', deleteHandler('a', false));
        
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@stop