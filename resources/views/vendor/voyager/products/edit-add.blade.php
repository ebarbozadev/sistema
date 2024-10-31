@php
    $edit = !is_null($dataTypeContent->getKey());
    $add = is_null($dataTypeContent->getKey());
    $categories = \App\Models\Category::all();

    $idCategoria = $dataTypeContent->category_id ?? '';
    $typeCategoria = \App\Models\Category::find($idCategoria)->type ?? '';

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

                            {{-- Obrigatório --}}
                            <section id="obrigatorios">
                                <!-- Campo Title -->
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input required type="text" class="form-control" id="title" name="title"
                                        placeholder="Title" value="{{ old('title', $dataTypeContent->title ?? '') }}">
                                </div>

                                <!-- Campo Description -->
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <input required type="text" class="form-control" id="description" name="description"
                                        placeholder="Description"
                                        value="{{ old('description', $dataTypeContent->description ?? '') }}">
                                </div>

                                <!-- Campo Price -->
                                <div required class="form-group">
                                    <label for="price">Price</label>
                                    <input type="text" class="form-control" id="price" name="price"
                                        placeholder="Price" value="{{ old('price', $dataTypeContent->price ?? '') }}">
                                </div>

                                <div class="panel-body">
                                    <p>Image</p>
                                    @if (isset($dataTypeContent->image))
                                        @php
                                            $images = is_array($dataTypeContent->image)
                                                ? $dataTypeContent->image
                                                : json_decode($dataTypeContent->image);
                                        @endphp
                                        @if ($images)
                                            @foreach ($images as $image)
                                                <div class="img_settings_container" data-field-name="image"
                                                    style="float:left;padding-right:15px;">
                                                    <a href="#" class="voyager-x remove-multi-image"
                                                        style="position: absolute;"></a>
                                                    <img src="{{ Voyager::image($image) }}"
                                                        data-file-name="{{ $image }}"
                                                        data-id="{{ $dataTypeContent->getKey() }}"
                                                        style="max-width:200px; height:auto; clear:both; display:block; padding:2px; border:1px solid #ddd; margin-bottom:5px;">
                                                </div>
                                            @endforeach
                                        @endif
                                    @endif
                                    <div class="clearfix"></div>
                                    <input type="file" name="image[]" multiple="multiple" accept="image/*">
                                </div>

                                <!-- Campo Slug -->
                                <div class="form-group">
                                    <label for="slug">Slug</label>
                                    <input type="text" class="form-control" id="slug" name="slug"
                                        placeholder="Slug" value="{{ old('slug', $dataTypeContent->slug ?? '') }}">
                                </div>

                                <!-- Campo Cidade -->
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" class="form-control" id="city" name="city"
                                        placeholder="City" value="{{ old('city', $dataTypeContent->city ?? '') }}">
                                </div>

                                <!-- Campo Estado -->
                                <div class="form-group">
                                    <label for="state">State</label>
                                    <select required class="form-control" name="state" id="state">
                                        <option value="Acre">Acre</option>
                                        <option value="Alagoas">Alagoas</option>
                                        <option value="Amapá">Amapá</option>
                                        <option value="Amazonas">Amazonas</option>
                                        <option value="Bahia">Bahia</option>
                                        <option value="Ceará">Ceará</option>
                                        <option value="Distrito Federal">Distrito Federal</option>
                                        <option value="Espírito Santo">Espírito Santo</option>
                                        <option value="Goiás">Goiás</option>
                                        <option value="Maranhão">Maranhão</option>
                                        <option value="Mato Grosso">Mato Grosso</option>
                                        <option value="Mato Grosso do Sul">Mato Grosso do Sul</option>
                                        <option value="Minas Gerais">Minas Gerais</option>
                                        <option value="Pará">Pará</option>
                                        <option value="Paraíba">Paraíba</option>
                                        <option value="Paraná">Paraná</option>
                                        <option value="Pernambuco">Pernambuco</option>
                                        <option value="Piauí">Piauí</option>
                                        <option value="Rio de Janeiro">Rio de Janeiro</option>
                                        <option value="Rio Grande do Norte">Rio Grande do Norte</option>
                                        <option value="Rio Grande do Sul">Rio Grande do Sul</option>
                                        <option value="Rondônia">Rondônia</option>
                                        <option value="Roraima">Roraima</option>
                                        <option value="Santa Catarina">Santa Catarina</option>
                                        <option value="São Paulo">São Paulo</option>
                                        <option value="Sergipe">Sergipe</option>
                                        <option value="Tocantins">Tocantins</option>
                                    </select>
                                </div>

                                <!-- Campo Status -->
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select required class="form-control" name="status" id="status">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="category_id">Categoria</label>
                                    <select required onchange="showCategoryId()" class="form-control" id="category_id"
                                        name="category_id">
                                        <option value="">Selecione uma categoria</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                @if (old('category_id', $dataTypeContent->category_id ?? '') == $category->id) selected @endif>
                                                {{ $category->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </section>

                            {{-- Imóveis --}}
                            <section id="imoveis">
                                <!-- Campo Property Type -->
                                <div class="form-group">
                                    <label for="property_type">Property Type</label>
                                    <select class="form-control" id="property_type" name="property_type">
                                        <option value="">Select the Property Type</option>
                                        @foreach ($propertyTypeOptions as $propertyType => $label)
                                            <option value="{{ $propertyType }}"
                                                @if (old('property_type', $dataTypeContent->property_type ?? '') == $propertyType) selected @endif>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Campo Number of Rooms -->
                                <div class="form-group">
                                    <label for="number_of_rooms">Number of Rooms</label>
                                    <input type="number" class="form-control" id="number_of_rooms"
                                        name="number_of_rooms" placeholder="Number of Rooms"
                                        value="{{ old('number_of_rooms', $dataTypeContent->number_of_rooms ?? '') }}">
                                </div>

                                <!-- Campo Number of Bathrooms -->
                                <div class="form-group">
                                    <label for="number_of_bathrooms">Number of Bathrooms</label>
                                    <input type="number" class="form-control" id="number_of_bathrooms"
                                        name="number_of_bathrooms" placeholder="Number of Bathrooms"
                                        value="{{ old('number_of_bathrooms', $dataTypeContent->number_of_bathrooms ?? '') }}">
                                </div>

                                <!-- Campo Area -->
                                <div class="form-group">
                                    <label for="area">Area</label>
                                    <input type="number" class="form-control" id="area" name="area"
                                        placeholder="Area" value="{{ old('area', $dataTypeContent->area ?? '') }}">
                                </div>

                                <!-- Campo Build Area -->
                                <div class="form-group">
                                    <label for="build_area">Build Area</label>
                                    <input type="number" class="form-control" id="build_area" name="build_area"
                                        placeholder="Build Area"
                                        value="{{ old('build_area', $dataTypeContent->build_area ?? '') }}">
                                </div>

                                <!-- Campo Location -->
                                <div class="form-group">
                                    <label for="location">Location</label>
                                    <input type="text" class="form-control" id="location" name="location"
                                        placeholder="Location"
                                        value="{{ old('location', $dataTypeContent->location ?? '') }}">
                                </div>

                                <!-- Campo Number of Floors -->
                                <div class="form-group">
                                    <label for="number_of_floors">Number of Floors</label>
                                    <input type="number" class="form-control" id="number_of_floors"
                                        name="number_of_floors" placeholder="Number of Floors"
                                        value="{{ old('number_of_floors', $dataTypeContent->number_of_floors ?? '') }}">
                                </div>

                                <!-- Campo Has Elevator -->
                                <div class="form-group">
                                    <label for="has_elevator">Has Elevator</label>
                                    <select class="form-control" id="has_elevator" name="has_elevator">
                                        <option value="">Select the Has Elevator</option>
                                        @foreach ($hasElevatorOptions as $hasElevator => $label)
                                            <option value="{{ $hasElevator }}"
                                                @if (old('has_elevator', $dataTypeContent->has_elevator ?? '') == $hasElevator) selected @endif>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </section>

                            {{-- Automoveis --}}
                            <section id="automoveis">
                                <!-- Campo Make -->
                                <div class="form-group">
                                    <label for="make">Make</label>
                                    <input type="text" class="form-control" id="make" name="make"
                                        placeholder="Make" value="{{ old('make', $dataTypeContent->make ?? '') }}">
                                </div>

                                <!-- Campo Model -->
                                <div class="form-group">
                                    <label for="model">Model</label>
                                    <input type="text" class="form-control" id="model" name="model"
                                        placeholder="Model" value="{{ old('model', $dataTypeContent->model ?? '') }}">
                                </div>

                                <!-- Campo Year -->
                                <div class="form-group">
                                    <label for="year">Year</label>
                                    <input type="text" class="form-control" id="year" name="year"
                                        placeholder="Year" value="{{ old('year', $dataTypeContent->year ?? '') }}">
                                </div>

                                <!-- Campo Mileage -->
                                <div class="form-group">
                                    <label for="mileage">Mileage</label>
                                    <input type="number" class="form-control" id="mileage" name="mileage"
                                        placeholder="Mileage"
                                        value="{{ old('mileage', $dataTypeContent->mileage ?? '') }}">
                                </div>

                                <!-- Campo Fuel Type -->
                                <div class="form-group">
                                    <label for="fuel_type">Fuel Type</label>
                                    <select class="form-control" id="fuel_type" name="fuel_type">
                                        <option value="">Select the Fuel Type</option>
                                        @foreach ($fuelTypeOptions as $fuel => $label)
                                            <option value="{{ $fuel }}"
                                                @if (old('fuel_type', $dataTypeContent->fuel_type ?? '') == $fuel) selected @endif>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Campo Number of Doors -->
                                <div class="form-group">
                                    <label for="number_of_doors">Number of Doors</label>
                                    <input type="number" class="form-control" id="number_of_doors"
                                        name="number_of_doors" placeholder="Number of Doors"
                                        value="{{ old('number_of_doors', $dataTypeContent->number_of_doors ?? '') }}">
                                </div>

                                <!-- Campo Exchange -->
                                <div class="form-group">
                                    <label for="exchange">Exchange</label>
                                    <select class="form-control" id="exchange" name="exchange">
                                        <option value="">Select the Type Exchange</option>
                                        @foreach ($exchangeOptions as $exchange => $label)
                                            <option value="{{ $exchange }}"
                                                @if (old('exchange', $dataTypeContent->exchange ?? '') == $exchange) selected @endif>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Campo Color -->
                                <div class="form-group">
                                    <label for="color">Color</label>
                                    <input type="color" class="form-control" id="color" name="color"
                                        placeholder="Color" value="{{ old('color', $dataTypeContent->color ?? '') }}">
                                </div>

                                <!-- Campo Exchange -->
                                <div class="form-group">
                                    <label for="factory_warranty">Factory Warranty</label>
                                    <select class="form-control" id="factory_warranty" name="factory_warranty">
                                        <option value="">Have Factory Warranty?</option>
                                        @foreach ($factoryWarrantyOptions as $factory_warranty => $label)
                                            <option value="{{ $factory_warranty }}"
                                                @if (old('factory_warranty', $dataTypeContent->factory_warranty ?? '') == $factory_warranty) selected @endif>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Fim carros --}}
                            </section>

                            {{-- Outros --}}
                            <section id="outros">
                                <!-- Campo Breed -->
                                <div class="form-group">
                                    <label for="breed">Breed</label>
                                    <input type="text" class="form-control" id="breed" name="breed"
                                        placeholder="Breed" value="{{ old('breed', $dataTypeContent->breed ?? '') }}">
                                </div>

                                <!-- Campo Health Status -->
                                <div class="form-group">
                                    <label for="health_status">Health Status</label>
                                    <input type="text" class="form-control" id="health_status" name="health_status"
                                        placeholder="Health Status"
                                        value="{{ old('health_status', $dataTypeContent->health_status ?? '') }}">
                                </div>

                                <!-- Campo Age -->
                                <div class="form-group">
                                    <label for="age">Age</label>
                                    <input type="number" class="form-control" id="age" name="age"
                                        placeholder="Age" value="{{ old('age', $dataTypeContent->age ?? '') }}">
                                </div>

                                <!-- Campo Quantity -->
                                <div class="form-group">
                                    <label for="quantity">Quantity</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity"
                                        placeholder="Quantity"
                                        value="{{ old('quantity', $dataTypeContent->quantity ?? '') }}">
                                </div>

                                <!-- Campo Unit Price -->
                                <div class="form-group">
                                    <label for="unit_price">Unit Price</label>
                                    <input type="number" class="form-control" id="unit_price" name="unit_price"
                                        placeholder="Unit Price"
                                        value="{{ old('unit_price', $dataTypeContent->unit_price ?? '') }}">
                                </div>

                                <!-- Campo Weight -->
                                <div class="form-group">
                                    <label for="weight">Weight</label>
                                    <input type="number" class="form-control" id="weight" name="weight"
                                        placeholder="Weight" value="{{ old('weight', $dataTypeContent->weight ?? '') }}">
                                </div>

                                <!-- Campo Quality -->
                                <div class="form-group">
                                    <label for="quality">Quality</label>
                                    <input type="text" class="form-control" id="quality" name="quality"
                                        placeholder="Quality"
                                        value="{{ old('quality', $dataTypeContent->quality ?? '') }}">
                                </div>

                                <!-- Campo Gender -->
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select class="form-control" id="gender" name="gender">
                                        <option value="">Select the Gender</option>
                                        @foreach ($genderOptions as $gender => $label)
                                            <option value="{{ $gender }}"
                                                @if (old('gender', $dataTypeContent->gender ?? '') == $gender) selected @endif>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </section>
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

            @if ($isModelTranslatable)
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

            $('#confirm_delete').on('click', function() {
                $.post('{{ route('voyager.' . $dataType->slug . '.media.remove') }}', params, function(
                    response) {
                    if (response &&
                        response.data &&
                        response.data.status &&
                        response.data.status == 200) {

                        toastr.success(response.data.message);
                        $file.parent().fadeOut(300, function() {
                            $(this).remove();
                        })
                    } else {
                        toastr.error("Error removing file.");
                    }
                });

                $('#confirm_delete_modal').modal('hide');
            });
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@stop
