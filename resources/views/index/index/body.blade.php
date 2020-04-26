<body id="maia-body" class="{{body_class()}}">
    {!! $globalKey->tracking_body !!}
    @include('header')
    <div id="maia-wrapper">@if (trim($__env->yieldContent('content')))@yield('content')@endif</div>
    @include('footer')
    @include('inc.js')
    @if (trim($__env->yieldContent('js')))@yield('js')@endif{{"\n"}}
</body>
