@include('maia::templates.single_slot_head', ['key' => 'posts', 'single' => $post, 'url' => $post->getUrl(true)])
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
