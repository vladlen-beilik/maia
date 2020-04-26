{{-- Template: Example --}}
@include('maia::templates.term_slot_head', ['key' => 'postTags', 'term' => $postTag, 'url' => $postTag->getUrl(true)])
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
