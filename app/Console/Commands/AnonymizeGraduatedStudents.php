<?php

namespace App\Console\Commands;

use App\Enums\GraduationClearanceStatus;
use App\Models\GraduationClearance;
use App\Services\AuditService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AnonymizeGraduatedStudents extends Command
{
    protected $signature = 'anonymize:graduated-students';

    protected $description = 'Anonymize personal data of students who graduated before the retention period';

    public function handle(): int
    {
        $retentionYears = config('eteeap.retention_years', 5);
        $cutoff = now()->subYears($retentionYears);

        $clearances = GraduationClearance::where('status', GraduationClearanceStatus::Completed)
            ->where('tor_released_at', '<=', $cutoff)
            ->with([
                'student.applicantProfile',
                'student.experienceRecords',
                'student.educationRecords',
            ])
           ->get();

        if ($clearances->isEmpty()) {
            $this->info('No students to anonymize.');

            return Command::SUCCESS;
        }

        $anonymized = 0;

        foreach ($clearances as $clearance) {
            $user = $clearance->student;

            DB::transaction(function () use ($user) {
                $user->update([
                    'name' => "Anonymized Student {$user->id}",
                    'email' => "anonymized.{$user->id}@adfc.edu.ph",
                ]);

                $user->applicantProfile?->update([
                    'address' => null,
                    'birth_date' => null,
                ]);

                foreach ($user->experienceRecords as $record) {
                    $record->update([
                        'company' => null,
                        'position' => null,
                        'description' => null,
                    ]);
                }

                foreach ($user->educationRecords as $record) {
                    $record->update([
                        'school_name' => null,
                        'degree' => null,
                        'field_of_study' => null,
                    ]);
                }

                app(AuditService::class)->log(
                    actor: null,
                    action: 'anonymize_graduated_student',
                    targetType: 'user',
                    targetId: $user->id,
                    old: ['name' => $user->getOriginal('name'), 'email' => $user->getOriginal('email')],
                    new: ['name' => $user->name, 'email' => $user->email],
                );
            });

            $anonymized++;
        }

        $this->info("Anonymized {$anonymized} student(s).");

        return Command::SUCCESS;
    }
}
