<head>
{!! $globalKey['tracking-head'] !!}
{!! $globalKey['json_ld'] !!}
{!! $globalKey['favicon'] !!}
    <!-- Robots -->
@if(trim($__env->yieldContent('robots_all')) && siteIndex())
    <meta name="robots" content="@yield('robots_all')" />
    <meta name="googlebot" content="@yield('robots_google')" />
    <meta name="yandex" content="@yield('robots_yandex')" />
    <meta name="bingbot" content="@yield('robots_bing')" />
    <meta name="duckduckbot" content="@yield('robots_duck')" />
    <meta name="baiduspider" content="@yield('robots_baidu')" />
    <meta name="slurp" content="@yield('robots_yahoo')" />
@else
    <meta name="robots" content="noindex, nofollow, noodp" />
@endif

    <!-- Index -->
    <meta name="document-state" content="@yield('document_state')" />

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
    @include('inc.css')
    @if (trim($__env->yieldContent('css')))@yield('css'){{"\n"}}@endif

    <!-- Metatags content -->
    <title>@yield('meta_title')</title>{{"\n"}}
    @if (trim($__env->yieldContent('meta_description')))<meta name="description" content="@yield('meta_description')" />{{"\n"}}@endif
    @if (trim($__env->yieldContent('meta_keywords')))<meta name="keywords" content="@yield('meta_keywords')" />{{"\n"}}@endif
    @if (trim($__env->yieldContent('open_graph'))){!! html_entity_decode($__env->yieldContent('open_graph'), ENT_COMPAT, 'UTF-8') . "\n" !!}@endif{{"\n"}}
</head>
