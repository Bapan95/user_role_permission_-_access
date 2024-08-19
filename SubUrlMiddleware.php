<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\URL;

class SubUrlMiddleware
{
    public function handle($request, Closure $next)
    {
        $currentUrl = URL::current();
        $parsedUrl = parse_url($currentUrl);
        $subUrl = trim(isset($parsedUrl['path']) ? $parsedUrl['path'] : '', '/');

        view()->share('sub_url', $subUrl);

        return $next($request);
    }
}
