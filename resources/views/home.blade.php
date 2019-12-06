@if(Auth::guest())
    <a href="/login" class="btn btn-default">Login</a>
@else
    <a href="/dashboard" class="btn btn-default">dashboard</a>
@endif
