{{-- Template: Example --}}
@include('maia::templates.single_slot_head', ['key' => 'pages', 'single' => $page, 'url' => url($page->slug)])
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
