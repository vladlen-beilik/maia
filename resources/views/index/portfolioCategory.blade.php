@include('maia::templates.term_slot_head', ['key' => 'portfolioCategories', 'term' => $portfolioCategory, 'url' => url(seo('seo_portfolio_categories_prefix') . '/' . $portfolioCategory->slug)])
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
