<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchController extends Controller
{
    /**
     * POST /set-branch
     * Save the selected branch for the current user or guest session.
     */
    public function setBranch(Request $request): JsonResponse
    {
        $request->validate([
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'city_name' => ['nullable', 'string'],
        ]);

        $branchId = (int) $request->branch_id;

        // Store in session and cookie for immediate request and browser access
        session(['branch_id' => $branchId]);
        cookie()->queue('selected_branch_id', $branchId, 60 * 24 * 30);

        if ($request->filled('city_name')) {
            session(['branch_city_name' => $request->city_name]);
            cookie()->queue('selected_branch_city_name', $request->city_name, 60 * 24 * 30);
        }

        if (Auth::guard('customer')->check()) {
            // Logged-in customer: persist branch_id in the users table
            Auth::guard('customer')->user()->update(['branch_id' => $branchId]);
        } else {
            // Guest: store in database and session
            $guestId = session('guest_id');
            $guest = $guestId ? \App\Models\GuestUser::find($guestId) : null;
            if ($guest) {
                $guest->update(['branch_id' => $branchId]);
            } else {
                $guest = \App\Models\GuestUser::create([
                    'ip_address' => $request->ip(),
                    'branch_id' => $branchId,
                    'created_at' => now(),
                ]);
                session(['guest_id' => $guest->id]);
            }
        }

        if (request()->hasSession()) {
            session()->save();
        }

        $branch = Branch::find($branchId);

        return response()->json([
            'success'     => true,
            'branch_id'   => $branchId,
            'branch_name' => $branch?->name,
            'city_name'   => $request->city_name,
        ]);
    }
}
