<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <title>Slack forwarder</title>
</head>

<body>
    <div class="top-header">
        <img src="{{ asset('img/landing/logo.png') }}" class="top-header__logo" />
        <img src="{{ asset('img/landing/top-img.png') }}" class="top-header__bgr" />
        <div class="top-header__group">
            <div class="top-header__intro">
                <p>Easily forwarding payload to Slack</p>
                <p>with Customizable Message</p>
            </div>
            @if(Auth::guest())
                <button data-toggle="modal" href='#modal-login' class="top-header__btn">Login</button>
            @else
            @admin
                <a href="/admin/dashboard" class="top-header__btn">Go to app</a>
            @else
                <a href="/dashboard" class="top-header__btn">Go to app</a>
            @endadmin
            @endif
        </div>
    </div>

    <div class="feature">
        <p class="feature__title">
            FEATURES
        </p>
        <img src="{{ asset('img/landing/feature-bgr.png') }}" class="feature__bgr" />
        <div class="feature__btm"></div>
        <div class="feature__detail">
            <div class="feature__box">
                <p>Easy to track Statistic</p>
                <img src="{{ asset('img/landing/feature-1.png') }}" />
            </div>
            <div class="feature__box">
                <p>Various webhook setting with customizable message</p>
                <img src="{{ asset('img/landing/feature-2.png') }}" />
            </div>
            <div class="feature__box">
                <p>Payload and message history</p>
                <img src="{{ asset('img/landing/feature-3.png') }}" />
            </div>
        </div>
    </div>

    <div class="use-case">
        <p class="use-case__title">
            USE CASES
        </p>
        <div class="use-case__detail">
            <div class="use-case__box">
                <h5>Forward</h5>
                <p>Forward alert from Server/Application Monitoring service like Sentry, Updown, Grafana ... to Slack</p>
                <img src="{{ asset('img/landing/use-case-1.png') }}" />
            </div>
            <div class="use-case__box">
                <h5>Notification</h5>
                <p>Send notification to Slack when there is a new Pull Request on Github</p>
                <img src="{{ asset('img/landing/use-case-2.png') }}" />
            </div>
            <div class="use-case__box">
                <h5>Contents</h5>
                <p>Intergrate with other services that allow sending webhooks...</p>
                <img src="{{ asset('img/landing/use-case-3.png') }}" />
            </div>
        </div>
    </div>

    <div class="footer">
        <img src="{{ asset('img/landing/footer-bgr.png') }}" class="footer__bgr" />
        <img src="{{ asset('img/landing/logo.png') }}" class="footer__logo" />
        <div class="footer__detail">
            <p>
                Made by <a href="https://research.sun-asterisk.com" target="_blank">Sun* R&D Lab</a>
            </p>
            <p>We ♥ Open Source</p>
            <p>This service is open-sourced at <a href="https://github.com/sun-asterisk-research/chatwork-forwarder/tree/slack" target="_blank">Github</a>
        </div>
    </div>

    <div class="modal fade" id="modal-login">
        <div class="modal-dialog">
            <div class="login-form">
                {{ Form::open(['method' => 'POST', 'id' => 'login_form']) }}
                @csrf
                <div class="login_title">
                    <h1>Login</h1>
                </div>

                <div class="form-group">
                    <div class="has-error" id="error-email"></div>
                    <label for="uname"><b>Email</b></label>
                    <input id="email" placeholder="Enter email" type="email" class="form-control mb-5" name="email" value="{{ old('email') }}" autocomplete="email" autofocus>

                    <label for="psw"><b>Password</b></label>
                    <input id="password" placeholder="Enter password" type="password" class="form-control" name="password" autocomplete="current-password">
                    <div class="has-error" id="error-password"></div>

                    <div class="form-group__footer">
                        <label>
                            <input type="checkbox" checked="checked" name="remember" {{ old('remember') ? 'checked' : '' }}> {{ __('Remember Me') }}
                        </label>

                        <a class="float_right" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    </div>

                    <button type="submit" class="btn btn-primary">{{ __('Login') }}</button>
                    <a href="/redirect" class="google btn"><i class="fa fa-google fa-fw"></i> Login with Google+</a>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/login.js') }}"></script>
</body>

</html>
