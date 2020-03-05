{{-- Template: Example --}}
@include('maia::templates.term_slot_head', ['key' => 'postTags', 'term' => $postTag, 'url' => url(seo('seo_post_tags_prefix') . '/' . $postTag->slug)])
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
