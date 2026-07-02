<?php

namespace App\Providers;

use App\Models\AdviserAssignment;
use App\Models\Application;
use App\Models\AssessmentForm;
use App\Models\Document;
use App\Models\EnhancementActivity;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GraduationClearance;
use App\Models\Interview;
use App\Models\Portfolio;
use App\Observers\AdviserAssignmentObserver;
use App\Observers\ApplicationObserver;
use App\Observers\AssessmentFormObserver;
use App\Observers\DocumentObserver;
use App\Observers\EnhancementActivityObserver;
use App\Observers\EnrollmentObserver;
use App\Observers\GradeObserver;
use App\Observers\GraduationClearanceObserver;
use App\Observers\InterviewObserver;
use App\Observers\PortfolioObserver;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerObservers();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    protected function registerObservers(): void
    {
        Application::observe(ApplicationObserver::class);
        Document::observe(DocumentObserver::class);
        Portfolio::observe(PortfolioObserver::class);
        Interview::observe(InterviewObserver::class);
        AssessmentForm::observe(AssessmentFormObserver::class);
        Enrollment::observe(EnrollmentObserver::class);
        AdviserAssignment::observe(AdviserAssignmentObserver::class);
        EnhancementActivity::observe(EnhancementActivityObserver::class);
        Grade::observe(GradeObserver::class);
        GraduationClearance::observe(GraduationClearanceObserver::class);
    }
}
