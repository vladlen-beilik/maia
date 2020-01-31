<body id="maia-body">
    {!! setting('tracking.google_tag_manager_code_body') !!}
    @include('header')
    <div id="maia-wrapper">
        @yield('content')
    </div>
    @include('footer'){{"\n"}}
    @include('inc.js')
    @yield('js'){{"\n"}}
</body>
