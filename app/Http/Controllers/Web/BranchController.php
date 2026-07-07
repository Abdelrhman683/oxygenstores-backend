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
        ]);

        $branchId = (int) $request->branch_id;

        if (Auth::guard('customer')->check()) {
            // Logged-in customer: persist branch_id in the users table
            Auth::guard('customer')->user()->update(['branch_id' => $branchId]);
        } else {
            // Guest: store in session
            session(['branch_id' => $branchId]);
        }

        $branch = Branch::find($branchId);

        return response()->json([
            'success'     => true,
            'branch_id'   => $branchId,
            'branch_name' => $branch?->name,
        ]);
    }
}
