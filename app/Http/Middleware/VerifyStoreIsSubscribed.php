<?php

namespace App\Http\Middleware;

use App\Store;
use Closure;

class VerifyStoreIsSubscribed
{
    /**
     * The URIs that should be reachable without checking Shopify charge.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $store = Store::find($request->route()->parameter('storeId'));

        return $next($request);
    }
}
