<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('admin*') || $request->is('vendor*') || $request->is('seller*') || $request->is('change-language')) {
            if (!session()->has('local')) {
                session()->put('local', 'sa');
            }
            if (!session()->has('direction')) {
                session()->put('direction', 'rtl');
            }
            App::setLocale(session()->get('local'));
            return $next($request);
        } else {
            $originalLocal = session()->get('local');
            $originalDirection = session()->get('direction');

            session()->put('local', 'sa');
            session()->put('direction', 'rtl');
            App::setLocale('sa');

            $response = $next($request);

            if ($originalLocal !== null) {
                session()->put('local', $originalLocal);
            } else {
                session()->forget('local');
            }

            if ($originalDirection !== null) {
                session()->put('direction', $originalDirection);
            } else {
                session()->forget('direction');
            }

            return $response;
        }
    }
}
