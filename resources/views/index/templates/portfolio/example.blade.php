{{-- Template: Example --}}
@include('maia::templates.single_slot_head', ['key' => 'portfolio', 'single' => $portfolio, 'url' => url(seo('seo_portfolio_prefix') . '/' . $portfolio->slug)])
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
