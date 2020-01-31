<head>
{{--{!! explode('/', \Request::path())[0] == 'admin/login' || stripos(\Request::path(), 'admin/password/reset') !== false || \Request::path() == 'admin/register' || stripos(\Request::path(),--}}
{{--'admin') !== false ? '' : setting('tracking.google_tag_manager_code_head') ? "<!-- Google Tag Manager -->\n" . setting('tracking.google_tag_manager_code_head') . "\n" : '' !!}--}}

{!! trim($__env->yieldContent('json_ld')) ? "<!-- Schema -->\n" . str_replace('&#039;', '"', html_entity_decode($__env->yieldContent('json_ld'), ENT_COMPAT, 'UTF-8')) . "\n" : '' !!}
{{--@php stripos(\Request::path(), 'admin') !== false ? $favicon = vox_asset('images/favicon') : $favicon = asset(setting('site.favicon_path')) @endphp--}}
<!-- Favicon -->
{{--<link rel="icon" sizes="32x32" href="{{ $favicon . '/favicon32-32.png' }}" />--}}
{{--<link rel="icon" sizes="128x128" href="{{ $favicon . '/favicon128-128.png' }}" />--}}
{{--<link rel="icon" sizes="152x152" href="{{ $favicon . '/favicon152-152.png' }}" />--}}
{{--<link rel="icon" sizes="167x167" href="{{ $favicon . '/favicon167-167.png' }}" />--}}
{{--<link rel="icon" sizes="180x180" href="{{ $favicon . '/favicon180-180.png' }}" />--}}
{{--<link rel="icon" sizes="192x192" href="{{ $favicon . '/favicon192-192.png' }}" />--}}
{{--<link rel="icon" sizes="196x196" href="{{ $favicon . '/favicon196-196.png' }}" />--}}

<!-- Robots -->
{{--@if(\Request::path() == 'admin/login' || stripos(\Request::path(), 'admin/password/reset') !== false || \Request::path() == 'admin/register' || stripos(\Request::path(), 'admin') !== false)--}}
{{--<meta name="robots" content="Noindex, Nofollow, Noodp" />--}}
{{--<meta name="googlebot" content="Noindex, Nofollow, Noodp" />--}}
{{--<meta name="yandex" content="Noindex, Nofollow, Noodp" />--}}
{{--@else--}}
{{--@if (trim($__env->yieldContent('robots_all')))--}}
{{--<meta name="robots" content="@yield('robots_all')" />--}}
{{--<meta name="googlebot" content="@yield('robots_google')" />--}}
{{--<meta name="yandex" content="@yield('robots_yandex')" />--}}
{{--<meta name="bingbot" content="@yield('robots_bing')" />--}}
{{--<meta name="slurp" content="@yield('robots_yahoo')" />--}}
{{--@else--}}
{{--<meta name="robots" content="Noindex, Nofollow, Noodp" />--}}
{{--@endif--}}
{{--@endif--}}
{{--@if(\Request::path() == 'admin/login' || stripos(\Request::path(), 'admin/password/reset') !== false || \Request::path() == 'admin/register' || stripos(\Request::path(), 'admin') !== false)--}}
{{--@else--}}

<!-- Index -->
{{--<meta name="document-state" content="@yield('document_state')" />--}}
@endif

<!-- Metatags custom -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta name="author" content="SpaceCode" />
<link rel="author" href="https://spacecode.dev">
<meta name="csrf-token" content="{{csrf_token()}}">

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

<!-- CSS -->
{{--@if(stripos(\Request::path(), 'admin') !== false)--}}
{{--{!! packer('css', 'material.css', 'admin') !!}--}}
{{--{!! packer('css', 'app.css', 'admin') !!}--}}
{{--@yield('css')--}}
{{--@if(!empty(config('vox.additional_css')))--}}
{{--@foreach(config('vox.additional_css') as $css)<link rel="stylesheet" type="text/css" href="{{ asset($css) }}">@endforeach--}}
{{--@endif--}}
{{--@else--}}
{{--@include('inc.css')--}}
{{--@yield('css'){{"\n"}}--}}
{{--@endif--}}

<!-- Metatags content -->
{{--<title>@yield('meta_title')</title>--}}
{{--@if (trim($__env->yieldContent('meta_description')))<meta name="description" content="@yield('meta_description')" />{{"\n"}}@endif--}}
{{--@if (trim($__env->yieldContent('meta_keywords')))<meta name="keywords" content="@yield('meta_keywords')" />{{"\n"}}@endif--}}
{{--@if (trim($__env->yieldContent('open_graph'))){!! html_entity_decode($__env->yieldContent('open_graph'), ENT_COMPAT, 'UTF-8') . "\n" !!}@endif--}}
</head>
