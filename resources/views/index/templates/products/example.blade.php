{{-- Template: Example --}}
@include('maia::templates.single_slot_head', ['key' => 'products', 'single' => $product, 'url' => $product->getUrl(true)])
@extends('maia::index')
@section('content')
    {{----}}
@stop

@section('css')
    {{----}}
@stop

@section('js')
    {{----}}
@stop
