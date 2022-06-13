<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
class CheckTokenAdmin
{
    /**
     * Handle an incoming request. Checks if user is authenticated to make the call
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!$request->bearerToken())
            return \Helper::instance()->horeca_http_no_access();

        $user = User::where('token', $request->bearerToken())->first();
        if ($user->role === 'admin')
            return $next($request);

        return \Helper::instance()->horeca_http_no_access();
    }
}
