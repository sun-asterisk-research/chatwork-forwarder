@extends('layouts.app')
<link rel="stylesheet" href="{{ mix('/css/login.css') }}">
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="imgcontainer">
                    <img src="img/avatar2.jpg" alt="Avatar" class="avatar">
                </div>

                <div class="container">

                    <label for="uname"><b>Email</b></label>
                    <input id="email" placeholder="Enter mmail" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email" autofocus>

                    <label for="psw"><b>Password</b></label>
                    <input id="password" placeholder="Enter Password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"  autocomplete="current-password">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    
                    <label>
                      <input type="checkbox" checked="checked" name="remember" {{ old('remember') ? 'checked' : '' }}> {{ __('Remember Me') }}
                    </label>

                    <a class="float_right" href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>

                    <button type="submit" class="btn btn-primary">{{ __('Login') }}</button>
                    <a href="/redirect" class="google btn"><i class="fa fa-google fa-fw"></i> Login with Google+</a>
                </div>
        </form>
    </div>
</div>
</div>
@endsection
