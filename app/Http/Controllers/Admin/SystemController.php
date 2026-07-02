<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSystemConfigRequest;
use App\Models\Setting;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SystemController extends Controller
{
    public function health(): Response
    {
        $dbOk = false;
        try {
            DB::connection()->getPdo();
            $dbOk = true;
        } catch (\Exception) {
        }

        $cacheOk = false;
        try {
            Cache::put('health_check', true, 60);
            $cacheOk = Cache::has('health_check');
        } catch (\Exception) {
        }

        $storageOk = false;
        try {
            $storageOk = Storage::disk('local')->put('health_check.txt', 'ok');
            if ($storageOk) {
                Storage::disk('local')->delete('health_check.txt');
            }
        } catch (\Exception) {
        }

        $configKeys = ['min_age', 'min_experience_years', 'adviser_max_students', 'student_max_subjects', 'panel_min_assessors'];
        $configValues = [];
        foreach ($configKeys as $key) {
            $configValues[$key] = Setting::get($key, config("eteeap.$key"));
        }

        return Inertia::render('admin/system/index', [
            'health' => [
                'db' => $dbOk,
                'queue' => config('queue.default'),
                'cache' => $cacheOk,
                'storage' => $storageOk,
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'environment' => app()->environment(),
            ],
            'config' => $configValues,
        ]);
    }

    public function updateConfig(UpdateSystemConfigRequest $request): RedirectResponse
    {
        $keys = ['min_age', 'min_experience_years', 'adviser_max_students', 'student_max_subjects', 'panel_min_assessors'];
        $oldValues = [];
        $newValues = [];

        foreach ($keys as $key) {
            if ($request->filled($key)) {
                $oldValues[$key] = Setting::get($key, config("eteeap.$key"));
                $newValues[$key] = $request->input($key);
                Setting::set($key, $request->input($key));
            }
        }

        app(AuditService::class)->log(
            $request->user(),
            'update_config',
            'system_config',
            null,
            $oldValues,
            $newValues,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('System configuration updated.')]);

        return back();
    }
}
