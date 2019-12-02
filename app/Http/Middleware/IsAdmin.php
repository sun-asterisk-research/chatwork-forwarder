<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use App\Enums\UserType;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user() && Auth::user()->role == UserType::ADMIN) {
            return $next($request);
        }

        return redirect('/');
    }
}
