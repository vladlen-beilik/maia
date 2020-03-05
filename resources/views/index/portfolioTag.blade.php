@include('maia::templates.term_slot_head', ['key' => 'portfolioTags', 'term' => $portfolioTag, 'url' => url(seo('seo_portfolio_tags_prefix') . '/' . $portfolioTag->slug)])
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
