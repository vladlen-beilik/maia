{{-- Template: Example --}}
@include('maia::templates.single_slot_head', ['key' => 'portfolio', 'single' => $portfolio, 'url' => $portfolio->getUrl(true)])
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
