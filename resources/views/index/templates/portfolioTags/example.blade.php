{{-- Template: Example --}}
@include('maia::templates.term_slot_head', ['key' => 'portfolioTags', 'term' => $portfolioTag, 'url' => $portfolioTag->getUrl(true)])
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
