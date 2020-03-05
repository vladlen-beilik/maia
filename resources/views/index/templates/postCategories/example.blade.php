{{-- Template: Example --}}
@include('maia::templates.term_slot_head', ['key' => 'postCategories', 'term' => $postCategory, 'url' => url(seo('seo_post_categories_prefix') . '/' . $postCategory->slug)])
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
