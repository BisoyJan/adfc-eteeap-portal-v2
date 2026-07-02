<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStaffUserRequest;
use App\Http\Requests\Admin\SuspendUserRequest;
use App\Http\Requests\Admin\UpdateUserRolesRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService,
        private readonly NotificationService $notificationService,
    ) {}

    public function index(Request $request): Response
    {
        $users = User::query()
            ->with('roles')
            ->when($request->filled('role'), fn ($q) => $q->whereHas('roles', fn ($r) => $r->where('name', $request->input('role'))))
            ->when($request->filled('is_active'), fn ($q) => $q->where('is_active', $request->boolean('is_active')))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $roles = Role::orderBy('display_name')->get(['id', 'name', 'display_name']);

        return Inertia::render('admin/users/index', [
            'users' => $users,
            'roles' => $roles,
            'filters' => $request->only(['role', 'is_active']),
        ]);
    }

    public function store(StoreStaffUserRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);

        $user->roles()->sync(
            Role::whereIn('name', $request->input('roles'))->pluck('id')
        );

        $this->notificationService->send(
            $user,
            'account_welcome',
            __('Welcome to the ETEEAP Portal'),
            __('Your staff account has been created. Please log in to get started.'),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Staff user created.')]);

        return back();
    }

    public function updateRoles(UpdateUserRolesRequest $request, User $user): RedirectResponse
    {
        $oldRoles = $user->roles->pluck('name')->toArray();
        $newRoles = $request->input('roles');

        $user->roles()->sync(
            Role::whereIn('name', $newRoles)->pluck('id')
        );

        $this->auditService->log(
            $request->user(),
            'update_user_roles',
            'user',
            $user->id,
            ['roles' => $oldRoles],
            ['roles' => $newRoles],
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User roles updated.')]);

        return back();
    }

    public function suspend(SuspendUserRequest $request, User $user): RedirectResponse
    {
        $user->update([
            'is_active' => false,
            'suspended_at' => now(),
            'suspended_reason' => $request->input('reason'),
        ]);

        $this->auditService->log(
            $request->user(),
            'suspend_user',
            'user',
            $user->id,
            null,
            ['reason' => $request->input('reason')],
        );

        Inertia::flash('toast', ['type' => 'warning', 'message' => __('User suspended.')]);

        return back();
    }

    public function unsuspend(Request $request, User $user): RedirectResponse
    {
        $user->update([
            'is_active' => true,
            'suspended_at' => null,
            'suspended_reason' => null,
        ]);

        $this->auditService->log(
            $request->user(),
            'unsuspend_user',
            'user',
            $user->id,
            null,
            null,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User unsuspended.')]);

        return back();
    }
}
