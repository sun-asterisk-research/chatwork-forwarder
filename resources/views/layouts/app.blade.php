<!DOCTYPE html>
<html class="no-js">

<head>
    <meta charset="utf-8">
    <title>Chatword Fowarder</title>
    <meta name="description" content="ProUI is a Responsive Bootstrap Admin Template created by pixelcave and published on Themeforest.">
    <meta name="author" content="pixelcave">
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/themes.css') }}">
    <script src="{{ asset('js/modernizr-2.7.1-respond-1.4.2.min.js') }}"></script>
</head>

<body>
    <div id="page-wrapper">
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

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <!-- Bootstrap.js, Jquery plugins and Custom JS code -->
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/plugins.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ mix('/js/custom.js') }}"></script>
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
