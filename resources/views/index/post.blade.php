@include('maia::templates.single_slot_head', ['key' => 'posts', 'single' => $post, 'url' => url(seo('seo_posts_prefix') . '/' . $post->slug)])
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
