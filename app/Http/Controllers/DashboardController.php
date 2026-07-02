<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Redirect the authenticated user to their role-specific dashboard.
     */
    public function index(Request $request): RedirectResponse
    {
        $user = $request->user();

        return match (true) {
            $user->hasRole('admin') => redirect()->route('admin.dashboard'),
            $user->hasRole('coordinator') => redirect()->route('coordinator.dashboard'),
            $user->hasAnyRole(['assessor_internal', 'assessor_external']) => redirect()->route('assessor.dashboard'),
            default => redirect()->route('applicant.dashboard'),
        };
    }
}
