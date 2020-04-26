@include('maia::templates.term_slot_head', ['key' => 'postCategories', 'term' => $postCategory, 'url' => $postCategory->getUrl(true)])
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
