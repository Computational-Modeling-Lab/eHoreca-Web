<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\WProducer;
use App\Models\User;

class CheckTokenWProd
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
        if (!$request->bearerToken()) {
            return \Helper::instance()->horeca_http_no_access();
        }

        $user = \DB::table('users')->where('token', $request->bearerToken())->first();

        if ($user->role === 'admin') {
            return $next($request);
        }

        if ($user->role === 'w_producer' ||
            $user->role === 'w_producer_employee'
        ) {
            // $wprod = \DB::table('w_producers')->where('users', 'like', "%{".$user->id."}%")->get();
            $toReturn = WProducer::get();
            $wprod = null;
            foreach($toReturn as $w_producerRecord) {
                if (isset($w_producerRecord->users) && in_array($user->id, $w_producerRecord->users)) {
                    $wprod = $w_producerRecord;
                }
            };

            if ($wprod->is_approved === "Yes") {
                return $next($request);
            } else {
                return response('This large producer company is not yet approved by our admins! Please wait for your approval.', 403);
            }
        }

        return \Helper::instance()->horeca_http_no_access();
    }
}
