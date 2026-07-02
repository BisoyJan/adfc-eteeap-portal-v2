<?php

namespace App\Console\Commands;

use App\Models\EnhancementActivity;
use App\Models\Interview;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendDeadlineReminders extends Command
{
    protected $signature = 'eteeap:send-deadline-reminders';

    protected $description = 'Send deadline reminders for enhancement activities and interviews';

    public function __construct(
        protected NotificationService $notifications,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->sendActivityReminders();
        $this->sendInterviewReminders();

        $this->info('Deadline reminders sent successfully.');

        return Command::SUCCESS;
    }

    protected function sendActivityReminders(): void
    {
        $days = [7, 3, 1];

        foreach ($days as $day) {
            $targetDate = now()->addDays($day)->startOfDay();

            $activities = EnhancementActivity::whereIn('status', ['assigned', 'in_progress'])
                ->whereNotNull('due_date')
                ->whereDate('due_date', $targetDate)
                ->with('student')
                ->get();

            foreach ($activities as $activity) {
                $exists = Notification::where('user_id', $activity->student_id)
                    ->where('type', 'deadline_reminder')
                    ->where('data->activity_id', $activity->id)
                    ->where('data->days_before', $day)
                    ->whereDate('created_at', today())
                    ->exists();

                if ($exists) {
                    continue;
                }

                $this->notifications->send(
                    $activity->student,
                    'deadline_reminder',
                    "Activity Due in {$day} Day" . ($day > 1 ? 's' : ''),
                    "\"{$activity->title}\" is due on {$activity->due_date->format('F j, Y')}.",
                    ['activity_id' => $activity->id, 'days_before' => $day],
                    email: true,
                );
            }
        }
    }

    protected function sendInterviewReminders(): void
    {
        $targetDate = now()->addDay()->startOfDay();

        $interviews = Interview::where('status', 'scheduled')
            ->whereDate('scheduled_at', $targetDate)
            ->with('application.user', 'panelists')
            ->get();

        foreach ($interviews as $interview) {
            $recipients = collect([$interview->application?->user])
                ->concat($interview->panelists)
                ->filter();

            foreach ($recipients as $recipient) {
                $exists = Notification::where('user_id', $recipient->id)
                    ->where('type', 'interview_reminder')
                    ->where('data->interview_id', $interview->id)
                    ->whereDate('created_at', today())
                    ->exists();

                if ($exists) {
                    continue;
                }

                $this->notifications->send(
                    $recipient,
                    'interview_reminder',
                    'Interview Tomorrow',
                    "Your interview is scheduled tomorrow at {$interview->scheduled_at->format('g:i A')}.",
                    ['interview_id' => $interview->id],
                    email: true,
                );
            }
        }
    }
}
