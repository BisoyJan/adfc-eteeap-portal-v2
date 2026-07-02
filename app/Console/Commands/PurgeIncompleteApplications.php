<?php

namespace App\Console\Commands;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Services\AuditService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurgeIncompleteApplications extends Command
{
    protected $signature = 'applications:purge-incomplete';

    protected $description = 'Delete incomplete draft applications older than the inactive purge period';

    public function handle(): int
    {
        $purgeMonths = config('eteeap.inactive_purge_months', 12);
        $cutoff = now()->subMonths($purgeMonths);

        $applications = Application::where('status', ApplicationStatus::Draft)
            ->where('updated_at', '<=', $cutoff)
            ->with('documents')
            ->get();

        if ($applications->isEmpty()) {
            $this->info('No incomplete applications to purge.');

            return Command::SUCCESS;
        }

        $purged = 0;

        foreach ($applications as $application) {
            DB::transaction(function () use ($application) {
                $disk = config('eteeap.document.storage_disk', 'local');

                foreach ($application->documents as $document) {
                    Storage::disk($disk)->delete($document->file_path);
                    $document->delete();
                }

                $applicationId = $application->id;
                $application->delete();

                app(AuditService::class)->log(
                    actor: null,
                    action: 'purge_incomplete_application',
                    targetType: 'application',
                    targetId: $applicationId,
                    old: ['status' => ApplicationStatus::Draft->value],
                );
            });

            $purged++;
        }

        $this->info("Purged {$purged} incomplete application(s).");

        return Command::SUCCESS;
    }
}
