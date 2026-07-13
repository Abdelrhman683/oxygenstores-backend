<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\GuestUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestMiddleware
{

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (!Auth::guard('customer')->check()) {
            $guestId = session('guest_id');
            $guestExists = $guestId ? GuestUser::where('id', $guestId)->exists() : false;

            if (!$guestExists) {
                if ($guestId) {
                    session()->forget('guest_id');
                }

                $guest = GuestUser::create([
                    'ip_address' => $request->ip(),
                    'branch_id' => null,
                    'created_at' => now(),
                ]);

                session()->put('guest_id', $guest?->id);
            }
        }
        return $next($request);
    }

}
