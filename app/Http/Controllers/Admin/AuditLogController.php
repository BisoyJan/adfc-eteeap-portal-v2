<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuditLogController extends Controller
{
    public function index(Request $request): Response
    {
        $query = AuditLog::query()->with('actor:id,name,email');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date('date_to'));
        }

        if ($request->filled('actor')) {
            $query->where('user_id', $request->integer('actor'));
        }

        if ($request->filled('action')) {
            $query->where('action', $request->string('action'));
        }

        if ($request->filled('target_type')) {
            $query->where('target_type', $request->string('target_type'));
        }

        $logs = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $actionTypes = AuditLog::distinct()->pluck('action')->sort()->values();
        $targetTypes = AuditLog::distinct()->pluck('target_type')->sort()->values();

        return Inertia::render('admin/audit-logs/index', [
            'logs' => $logs,
            'filters' => $request->only(['date_from', 'date_to', 'actor', 'action', 'target_type']),
            'actionTypes' => $actionTypes,
            'targetTypes' => $targetTypes,
        ]);
    }

    public function show(AuditLog $auditLog): Response
    {
        $auditLog->load('actor:id,name,email');

        return Inertia::render('admin/audit-logs/show', [
            'log' => $auditLog,
        ]);
    }
}
