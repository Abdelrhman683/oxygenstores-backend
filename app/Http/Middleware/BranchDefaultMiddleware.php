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

        $branchId = null;

        if (Auth::guard('customer')->check()) {
            $user = Auth::guard('customer')->user();
            $branchId = $user->branch_id;
        } else {
            $branchId = getSelectedBranchId();
        }

        // If no branch is selected, default to Riyadh (querying directly from database, bypassing cache)
        if (!$branchId) {
            $branchId = DB::table('branches')
                ->where('name', 'like', '%الرياض%')
                ->orWhere('name', 'like', '%Riyadh%')
                ->value('id') ?? 1; // Fallback to branch ID 1

            if (Auth::guard('customer')->check()) {
                Auth::guard('customer')->user()->update(['branch_id' => $branchId]);
            } else {
                session(['branch_id' => $branchId]);
                if (session()->has('guest_id')) {
                    \App\Models\GuestUser::where('id', session('guest_id'))->update(['branch_id' => $branchId]);
                }
            }
        } else {
            session(['branch_id' => $branchId]);
        }

        return $next($request);
    }

}
