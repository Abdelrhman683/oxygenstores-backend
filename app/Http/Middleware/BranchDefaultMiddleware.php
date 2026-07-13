<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class BranchDefaultMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // Skip for admin/vendor/seller routes
        if ($request->is('admin*') || $request->is('vendor*') || $request->is('seller*') || $request->is('api/v1/admin*') || $request->is('api/v1/seller*')) {
            return $next($request);
        }

        return $next($request);
    }

}
