<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;

class ResponseWrapper
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
        $result = $next($request);

        return $result;
    }
}