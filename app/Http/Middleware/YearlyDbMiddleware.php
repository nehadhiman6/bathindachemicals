<?php

namespace App\Http\Middleware;

use Closure;

class YearlyDbMiddleware
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
        getYearlyDbConn(true);
        getPrvYearDbConn(true);
        getCompDbConn(true);
        return $next($request);
    }
}
