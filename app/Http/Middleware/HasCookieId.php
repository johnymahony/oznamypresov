<?php

namespace App\Http\Middleware;

use App\CookieId;
use Closure;

class HasCookieId
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $hasCookie = $request->hasCookie('cookie_id');
        $cookie = false;
        if ( ! $hasCookie) {
            try {

            $cookie = CookieId::firstOrCreate([
                'browser' => $request->userAgent(),
                'ip' => $request->ip(),
            ]);
            $request->cookie_id = $cookie->id;
            } catch (\Exception $e) {

            }
        }


        $response = $next($request);

        if ( ! $hasCookie && $cookie) {
            $response->cookie('cookie_id', $cookie->id);
        }

        return $response;
    }
}
