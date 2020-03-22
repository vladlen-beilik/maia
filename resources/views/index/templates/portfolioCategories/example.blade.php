{{-- Template: Example --}}
@include('maia::templates.term_slot_head', ['key' => 'portfolioCategories', 'term' => $portfolioCategory, 'url' => $portfolioCategory->getUrl(true)])
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
