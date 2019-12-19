<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <title>Chatwork forwarder</title>
</head>
<body>
    <div class="main">
        <div class="logo">
            <img src="{{asset('img/logo.png')}}" alt="">
        </div>
        <div class="content">
            <h1>ABOUT SYSTEM</h1>
            <p>Ứng dụng giúp các dự án có nhu cầu sử dụng chatwork api để gửi message hoặc muốn forward dữ liệu payload nhận được
                từ các service khác lên chatwork.</p>

            @if(Auth::guest())
            <a data-toggle="modal" href='#modal-login' class="btn btn-default">Login</a>
            @else
                @admin
                <a href="admin/dashboard" class="btn btn-default">Go to app</a>
                @else
                <a href="/dashboard" class="btn btn-default">Go to app</a>
                @endadmin
            @endif
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
                        <label for="uname"><b>Email</b></label>
                        <input id="email" placeholder="Enter email" type="email" class="form-control" name="email" value="{{ old('email') }}" autocomplete="email" autofocus>
                        <div class="has-error" id="error-email"></div>

                        <label for="psw"><b>Password</b></label>
                        <input id="password" placeholder="Enter password" type="password" class="form-control" name="password"  autocomplete="current-password">
                        <div class="has-error" id="error-password"></div>

                        <label>
                          <input type="checkbox" checked="checked" name="remember" {{ old('remember') ? 'checked' : '' }}> {{ __('Remember Me') }}
                        </label>

                        <a class="float_right" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>

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
