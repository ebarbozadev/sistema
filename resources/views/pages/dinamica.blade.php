@extends('layouts.layout')

@section('content')
    <div id="dinamica" class="container">
        <h1>{{ $page->title }}</h1>
        <p>{!! $page->description !!}</p>
    </div>
@endsection
