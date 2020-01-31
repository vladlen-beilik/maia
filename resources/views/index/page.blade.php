@include('maia::templates.single_slot_head', ['single' => $page, 'setting' => 'seo-pages', 'url' => url(setting('seo-pages.prefix') . '/' . $page->slug)])
@extends('maia::index')
@section('content')
{{--    --}}
@stop

@section('css')
{{--    --}}
@stop

@section('js')
{{--    --}}
@stop
