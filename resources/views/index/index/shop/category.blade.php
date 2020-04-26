@include('maia::templates.term_slot_head', ['key' => 'productCategories', 'term' => $productCategory, 'url' => $productCategory->getUrl(true)])
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
