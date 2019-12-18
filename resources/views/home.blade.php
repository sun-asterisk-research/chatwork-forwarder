<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
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
            <a href="/login" class="btn btn-default">Login</a>
            @else
            <a href="/dashboard" class="btn btn-default">Go to app</a>
            @endif
        </div>
    </div>
</body>
</html>
