@extends('voyager::master')

@push('css')
<style>
    .meu-estilo {
        color: blue;
    }
</style>
@endpush

@section('content')
<div class="page-content container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1>Minha Página Personalizada</h1>
            <p>Conteúdo da página aqui.</p>
        </div>
    </div>
</div>
@endsection

@push('javascript')
<script>

</script>
@endpush