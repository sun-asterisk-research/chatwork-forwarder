<!DOCTYPE html>
<html class="no-js">

<head>
    <meta charset="utf-8">
    <title>Slack Forwarder</title>
    <meta name="description" content="ProUI is a Responsive Bootstrap Admin Template created by pixelcave and published on Themeforest.">
    <meta name="author" content="pixelcave">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/themes.css') }}">
    <link rel="stylesheet" href="{{ mix('/css/style.css') }}">
    <link href="{{ asset('css/toastr.css') }}" rel="stylesheet">
    <script src="{{ asset('js/modernizr-2.7.1-respond-1.4.2.min.js') }}"></script>
</head>

<body>
    <div id="page-wrapper" class="full-height">
        <div id="page-container" class="sidebar-partial sidebar-visible-lg sidebar-no-animations">
            @include('layouts.sidebar')
            <div id="main-container">
                @include('layouts.header')

                <div id="page-content">
                    @yield('content')
                </div>

                @include('layouts.footer')
            </div>
        </div>
    </div>

    <!-- Bootstrap.js, Jquery plugins and Custom JS code -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/plugins.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ mix('/js/custom.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <!-- Load and execute javascript code used only in this page -->
    <script src="{{ asset('js/tablesDatatables.js') }}"></script>
    @yield('script')
    <script>
        $(function() {
            TablesDatatables.init();
        });
    </script>
    @yield('js')
</body>

</html>
