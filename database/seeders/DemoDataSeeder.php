<?php

namespace Database\Seeders;

use App\Enums\{
    ActivityStatus,
    ActivityType,
    ApplicationStatus,
    AssessmentStage,
    AssessmentVerdict,
    CompetencyLevel,
    EnrollmentStatus,
    EnrollmentSubjectStatus,
    EquivalencyMatrixStatus,
    EquivalencyMatrixType,
    EquivalencyRuleType,
    GradeStatus,
    GraduationClearanceStatus,
    InterviewStatus,
    RecommendedAction,
    SubjectCategory,
};
use App\Models\{
    AdviserAssignment,
    ApplicantProfile,
    Application,
    AssessmentForm,
    ClearanceDocument,
    CurriculumSubject,
    Document,
    EducationRecord,
    EnhancementActivity,
    Enrollment,
    EnrollmentSubject,
    EquivalencyMatrix,
    EquivalencyMatrixItem,
    EquivalencyRule,
    ExperienceRecord,
    Grade,
    GraduationClearance,
    Interview,
    Notification,
    Portfolio,
    Role,
    Rubric,
    Setting,
    User,
};
use App\Services\{
    ApplicationService,
    AssessmentService,
    DocumentService,
    EnrollmentService,
    EquivalencyService,
    GradeService,
    GraduationService,
    InterviewService,
    PortfolioService,
};
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DemoDataSeeder extends Seeder
{
    protected ApplicationService $applications;
    protected AssessmentService $assessments;
    protected DocumentService $documents;
    protected EnrollmentService $enrollments;
    protected EquivalencyService $equivalency;
    protected GradeService $grades;
    protected GraduationService $graduation;
    protected InterviewService $interviews;
    protected PortfolioService $portfolios;

    protected User $admin;
    protected User $coordinator;
    protected User $programHead;
    protected User $qaOfficer;
    protected User $vpaa;
    protected User $assessorInternal;
    protected User $assessorExternal;
    protected User $adviser1;
    protected User $adviser2;
    protected User $adviser3;

    protected array $curriculumSubjects = [];

    protected string $disk;

    public function run(): void
    {
        $this->applications = app(ApplicationService::class);
        $this->assessments = app(AssessmentService::class);
        $this->documents = app(DocumentService::class);
        $this->enrollments = app(EnrollmentService::class);
        $this->equivalency = app(EquivalencyService::class);
        $this->grades = app(GradeService::class);
        $this->graduation = app(GraduationService::class);
        $this->interviews = app(InterviewService::class);
        $this->portfolios = app(PortfolioService::class);

        $this->disk = config('eteeap.document.storage_disk', 'local');

        DB::beginTransaction();

        try {
            $this->cleanup();
            Storage::disk($this->disk)->makeDirectory('seed-temp');

            $this->seedUsers();
            $this->seedEquivalencyRules();
            $this->seedHappyPath();            // Applicant 1
            $this->seedRevisionScenario();     // Applicant 2
            $this->seedRejectedScenario();     // Applicant 3
            $this->seedInProgressStudent();    // Applicant 4
            $this->seedPartialGraduation();    // Applicant 5
            $this->seedDraftOnly();            // Applicant 6
            $this->seedAdviserWorkload();
            $this->seedSystemData();

            Storage::disk($this->disk)->deleteDirectory('seed-temp');

            DB::commit();

            $this->command?->info('Demo data seeded successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Storage::disk($this->disk)->deleteDirectory('seed-temp');
            throw $e;
        }
        $this->command?->info('Login credentials:');
        $this->command?->info('  admin@adfc.edu.ph / password');
        $this->command?->info('  coordinator@adfc.edu.ph / password');
        $this->command?->info('  programhead@adfc.edu.ph / password');
        $this->command?->info('  qaofficer@adfc.edu.ph / password');
        $this->command?->info('  vpaa@adfc.edu.ph / password');
        $this->command?->info('  assessor.int@adfc.edu.ph / password');
        $this->command?->info('  assessor.ext@adfc.edu.ph / password');
        $this->command?->info('  adviser1@adfc.edu.ph / password');
        $this->command?->info('  adviser2@adfc.edu.ph / password');
        $this->command?->info('  applicant1@adfc.edu.ph / password');
        $this->command?->info('  applicant2@adfc.edu.ph / password');
    }

    protected function seedUsers(): void
    {
        $this->admin = $this->createUser('admin', 'System Admin', 'admin@adfc.edu.ph');
        $this->coordinator = $this->createUser('coordinator', 'Coordinator', 'coordinator@adfc.edu.ph');
        $this->programHead = $this->createUser('program_head', 'Program Head', 'programhead@adfc.edu.ph');
        $this->qaOfficer = $this->createUser('qa_officer', 'QA Officer', 'qaofficer@adfc.edu.ph');
        $this->vpaa = $this->createUser('vpaa', 'VPAA', 'vpaa@adfc.edu.ph');
        $this->assessorInternal = $this->createUser('assessor_internal', 'Internal Assessor', 'assessor.int@adfc.edu.ph');
        $this->assessorExternal = $this->createUser('assessor_external', 'External Assessor', 'assessor.ext@adfc.edu.ph');
        $this->adviser1 = $this->createUser('adviser', 'Adviser 1', 'adviser1@adfc.edu.ph');
        $this->adviser2 = $this->createUser('adviser', 'Adviser 2', 'adviser2@adfc.edu.ph');
        $this->adviser3 = $this->createUser('adviser', 'Adviser 3', 'adviser3@adfc.edu.ph');

        $this->curriculumSubjects = CurriculumSubject::all()->keyBy('code')->all();
    }

    protected function createUser(string $roleName, string $displayName, string $email): User
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $displayName,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        if ($user->wasRecentlyCreated) {
            $user->name = $displayName;
            $user->save();
        }

        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $user->roles()->syncWithoutDetaching([$role->id]);
        }

        return $user;
    }

    protected function cleanup(): void
    {
        $demoEmails = [
            'admin@adfc.edu.ph',
            'coordinator@adfc.edu.ph',
            'programhead@adfc.edu.ph',
            'qaofficer@adfc.edu.ph',
            'vpaa@adfc.edu.ph',
            'assessor.int@adfc.edu.ph',
            'assessor.ext@adfc.edu.ph',
            'adviser1@adfc.edu.ph',
            'adviser2@adfc.edu.ph',
            'adviser3@adfc.edu.ph',
            'applicant1@adfc.edu.ph',
            'applicant2@adfc.edu.ph',
            'applicant3@adfc.edu.ph',
            'applicant4@adfc.edu.ph',
            'applicant5@adfc.edu.ph',
            'applicant6@adfc.edu.ph',
            'adviser2.student1@adfc.edu.ph',
            'adviser2.student2@adfc.edu.ph',
        ];

        $userIds = User::whereIn('email', $demoEmails)->pluck('id');

        GraduationClearance::whereIn('student_id', $userIds)->delete();
        Grade::whereIn('student_id', $userIds)->delete();
        EnrollmentSubject::whereIn('enrollment_id', fn ($q) => $q->select('id')->from('enrollments')->whereIn('student_id', $userIds))->delete();
        Enrollment::whereIn('student_id', $userIds)->delete();
        EnhancementActivity::whereIn('student_id', $userIds)->delete();
        AdviserAssignment::whereIn('student_id', $userIds)->orWhereIn('adviser_id', $userIds)->delete();
        AssessmentForm::whereIn('assessor_id', $userIds)->orWhereIn('application_id', fn ($q) => $q->select('id')->from('applications')->whereIn('user_id', $userIds))->delete();
        Interview::whereIn('created_by', $userIds)->orWhereIn('application_id', fn ($q) => $q->select('id')->from('applications')->whereIn('user_id', $userIds))->delete();
        Portfolio::whereIn('application_id', fn ($q) => $q->select('id')->from('applications')->whereIn('user_id', $userIds))->delete();
        Document::whereIn('user_id', $userIds)->delete();
        EquivalencyMatrixItem::whereIn('equivalency_matrix_id', fn ($q) => $q->select('id')->from('equivalency_matrices')->whereIn('application_id', fn ($q2) => $q2->select('id')->from('applications')->whereIn('user_id', $userIds)))->delete();
        EquivalencyMatrix::whereIn('application_id', fn ($q) => $q->select('id')->from('applications')->whereIn('user_id', $userIds))->delete();
        Application::whereIn('user_id', $userIds)->delete();
        Notification::whereIn('user_id', $userIds)->delete();
        EducationRecord::whereIn('user_id', $userIds)->delete();
        ExperienceRecord::whereIn('user_id', $userIds)->delete();
        ApplicantProfile::whereIn('user_id', $userIds)->delete();
        EquivalencyRule::query()->delete();
        Rubric::whereIn('user_id', $userIds)->delete();
        Setting::query()->delete();

        User::whereIn('id', $userIds)->where('email', '!=', 'admin@adfc.edu.ph')->delete();
    }

    protected function createTempPdf(): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'seed_') . '.pdf';
        $content = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[]/Count 0>>endobj\nxref\n0 3\n0000000000 65535 f \n0000000009 00000 n \n0000000058 00000 n \ntrailer<</Size 3/Root 1 0 R>>\nstartxref\n119\n%%EOF";
        file_put_contents($path, $content);

        return new UploadedFile($path, basename($path), 'application/pdf', null, true);
    }

    protected function uploadDocuments(User $applicant, Application $application, User $verifier, bool $verifyAllRequired = false): void
    {
        $types = collect(config('eteeap.document_types'));

        foreach ($types as $type) {
            $file = $this->createTempPdf();
            $document = $this->documents->store($applicant, $application, $type['key'], $file);

            if ($verifyAllRequired && $type['required']) {
                $this->documents->verify($document, $verifier);
            }
        }
    }

    protected function seedEquivalencyRules(): void
    {
        $subjects = $this->curriculumSubjects;

        $rules = [
            ['rule_type' => EquivalencyRuleType::Direct, 'condition_key' => 'education_level', 'condition_value' => 'college_graduate', 'curriculum_subject_code' => 'GE-101', 'equivalent_credits' => 3, 'priority' => 1],
            ['rule_type' => EquivalencyRuleType::Experience, 'condition_key' => 'bsit_aligned', 'condition_value' => 'true', 'curriculum_subject_code' => 'IT-101', 'equivalent_credits' => 3, 'priority' => 2],
            ['rule_type' => EquivalencyRuleType::Experience, 'condition_key' => 'years_min', 'condition_value' => '3', 'curriculum_subject_code' => 'CS-101', 'equivalent_credits' => 3, 'priority' => 3],
            ['rule_type' => EquivalencyRuleType::Certification, 'condition_key' => 'cert_name', 'condition_value' => 'nc2_it', 'curriculum_subject_code' => 'IT-104', 'equivalent_credits' => 3, 'priority' => 4],
            ['rule_type' => EquivalencyRuleType::Training, 'condition_key' => 'training_hours_min', 'condition_value' => '40', 'curriculum_subject_code' => 'IT-201', 'equivalent_credits' => 3, 'priority' => 5],
            ['rule_type' => EquivalencyRuleType::PriorEducation, 'condition_key' => 'subject_passed', 'condition_value' => 'GE-101', 'curriculum_subject_code' => 'GE-101', 'equivalent_credits' => 3, 'priority' => 6],
            ['rule_type' => EquivalencyRuleType::Direct, 'condition_key' => 'education_level', 'condition_value' => 'college_graduate', 'curriculum_subject_code' => 'GE-102', 'equivalent_credits' => 3, 'priority' => 7],
            ['rule_type' => EquivalencyRuleType::Experience, 'condition_key' => 'years_min', 'condition_value' => '5', 'curriculum_subject_code' => 'IT-202', 'equivalent_credits' => 3, 'priority' => 8],
        ];

        foreach ($rules as $rule) {
            $subjectCode = $rule['curriculum_subject_code'];
            unset($rule['curriculum_subject_code']);

            $subject = $this->curriculumSubjects[$subjectCode] ?? null;
            if (! $subject) {
                continue;
            }

            EquivalencyRule::create(array_merge($rule, [
                'curriculum_subject_id' => $subject->id,
                'is_active' => true,
            ]));
        }
    }

    protected function seedHappyPath(): void
    {
        $this->command?->info('Seeding Applicant 1 — complete happy path...');

        $applicant = $this->createUser('applicant', 'Applicant 1 (Happy Path)', 'applicant1@adfc.edu.ph');

        $applicant->applicantProfile()->create([
            'birth_date' => now()->subYears(35),
            'citizenship' => 'Filipino',
            'civil_status' => 'Married',
            'gender' => 'male',
            'address' => '123 Main St, Manila',
            'highest_education' => 'College Graduate',
            'last_school_attended' => 'University of the Philippines',
        ]);

        $applicant->experienceRecords()->create([
            'company' => 'TechCorp Inc.',
            'position' => 'Software Developer',
            'start_date' => now()->subYears(8),
            'end_date' => now()->subYears(2),
            'description' => 'Full-stack web development with PHP and JavaScript',
            'bsit_aligned' => true,
        ]);

        $applicant->experienceRecords()->create([
            'company' => 'StartupXYZ',
            'position' => 'Senior Developer',
            'start_date' => now()->subYears(2),
            'is_current' => true,
            'description' => 'Led development team building cloud-based SaaS platform',
            'bsit_aligned' => true,
        ]);

        $applicant->educationRecords()->create([
            'school_name' => 'University of the Philippines',
            'degree' => 'BS Computer Science',
            'field_of_study' => 'Computer Science',
            'start_year' => 2005,
            'end_year' => 2009,
            'is_graduated' => true,
            'education_level' => 'college',
        ]);

        $application = $this->applications->currentDraftFor($applicant);
        $this->applications->submit($application);

        $application->refresh();

        $this->applications->approve($application, $this->coordinator, 'All qualifications met. Approved for assessment.');

        $application->refresh();

        $this->uploadDocuments($applicant, $application, $this->coordinator, true);

        $portfolio = $this->portfolios->submit($application);

        $criteria = [
            ['name' => 'Educational Attainment', 'score' => 25, 'max_score' => 30, 'comments' => 'BS Computer Science graduate'],
            ['name' => 'Work Experience Relevance', 'score' => 30, 'max_score' => 30, 'comments' => '10+ years in software development'],
            ['name' => 'Communication Skills', 'score' => 15, 'max_score' => 20, 'comments' => 'Adequate written communication'],
        ];

        $this->assessments->submit($application, AssessmentStage::First, $this->coordinator, $criteria, AssessmentVerdict::Pass);

        $criteria2 = [
            ['name' => 'Portfolio Completeness', 'score' => 28, 'max_score' => 30, 'comments' => 'Complete set of evidence'],
            ['name' => 'Technical Competency', 'score' => 26, 'max_score' => 30, 'comments' => 'Strong technical background'],
            ['name' => 'Industry Experience Depth', 'score' => 25, 'max_score' => 30, 'comments' => 'Relevant industry exposure'],
        ];

        $this->assessments->submit($application, AssessmentStage::Second, $this->coordinator, $criteria2, AssessmentVerdict::Pass);

        $initialMatrix = $this->equivalency->forApplication($application, EquivalencyMatrixType::Initial, $this->programHead);

        $matrixItems = [];
        $subjectCodes = ['GE-101', 'GE-102', 'CS-101', 'IT-101', 'PE-101'];
        foreach ($subjectCodes as $code) {
            $subject = $this->curriculumSubjects[$code] ?? null;
            $matrixItems[] = [
                'curriculum_subject_id' => $subject?->id,
                'subject_code' => $code,
                'subject_title' => $subject?->title ?? $code,
                'units' => $subject?->units ?? 3,
                'competency_level' => CompetencyLevel::Full->value,
                'evidence_source' => 'Work experience and education',
                'equivalent_credits' => $subject?->units ?? 3,
                'recommended_action' => RecommendedAction::Credit->value,
                'notes' => 'Fully equivalent based on 10+ years experience',
            ];
        }

        $creditItems = ['IT-102', 'IT-103', 'IT-104', 'IT-201', 'GE-103', 'GE-104', 'PE-102', 'NSTP-101'];
        foreach ($creditItems as $code) {
            $subject = $this->curriculumSubjects[$code] ?? null;
            $matrixItems[] = [
                'curriculum_subject_id' => $subject?->id,
                'subject_code' => $code,
                'subject_title' => $subject?->title ?? $code,
                'units' => $subject?->units ?? 3,
                'competency_level' => CompetencyLevel::Partial->value,
                'evidence_source' => 'Partial experience match',
                'equivalent_credits' => ($subject?->units ?? 3) * 0.5,
                'recommended_action' => RecommendedAction::Enhance->value,
                'notes' => 'Needs enhancement activities to demonstrate full competency',
            ];
        }

        $enrollCodes = ['IT-105', 'IT-106', 'IT-202', 'IT-203', 'IT-E1'];
        foreach ($enrollCodes as $code) {
            $subject = $this->curriculumSubjects[$code] ?? null;
            $matrixItems[] = [
                'curriculum_subject_id' => $subject?->id,
                'subject_code' => $code,
                'subject_title' => $subject?->title ?? $code,
                'units' => $subject?->units ?? 3,
                'competency_level' => CompetencyLevel::None->value,
                'evidence_source' => null,
                'equivalent_credits' => 0,
                'recommended_action' => RecommendedAction::Enroll->value,
                'notes' => 'Must enroll in this subject',
            ];
        }

        $this->equivalency->saveItems($initialMatrix, $matrixItems);
        $this->equivalency->submit($initialMatrix);

        $interview = $this->interviews->assignPanel($application, $this->programHead, [
            $this->assessorInternal->id,
            $this->assessorExternal->id,
            $this->coordinator->id,
        ]);

        $this->interviews->schedule($interview, now()->addDays(14)->format('Y-m-d H:i:s'), 'ADFC Conference Room A');

        $interview->refresh();

        $f3Validation = [
            ['name' => 'Oral Presentation', 'score' => 45, 'max_score' => 50, 'comments' => 'Clear articulation of experience'],
            ['name' => 'Technical Q&A', 'score' => 40, 'max_score' => 50, 'comments' => 'Answered all questions confidently'],
        ];

        $this->assessments->submit($application, AssessmentStage::ThirdValidation, $this->assessorInternal, $f3Validation, AssessmentVerdict::Pass);

        $f3Validation2 = [
            ['name' => 'Oral Presentation', 'score' => 42, 'max_score' => 50, 'comments' => 'Good presentation skills'],
            ['name' => 'Technical Q&A', 'score' => 38, 'max_score' => 50, 'comments' => 'Demonstrated adequate knowledge'],
        ];

        $this->assessments->submit($application, AssessmentStage::ThirdValidation, $this->assessorExternal, $f3Validation2, AssessmentVerdict::Pass);

        $f3CoordinatorValidation = [
            ['name' => 'Oral Presentation', 'score' => 44, 'max_score' => 50, 'comments' => 'Satisfactory performance'],
            ['name' => 'Technical Q&A', 'score' => 39, 'max_score' => 50, 'comments' => 'Met minimum competency requirements'],
        ];

        $this->assessments->submit($application, AssessmentStage::ThirdValidation, $this->coordinator, $f3CoordinatorValidation, AssessmentVerdict::Pass);

        $f3Oral = [
            ['name' => 'Communication', 'score' => 18, 'max_score' => 20, 'comments' => 'Excellent communication'],
            ['name' => 'Problem Solving', 'score' => 16, 'max_score' => 20, 'comments' => 'Systematic approach'],
            ['name' => 'Professional Demeanor', 'score' => 19, 'max_score' => 20, 'comments' => 'Professional throughout'],
        ];

        $this->assessments->submit($application, AssessmentStage::ThirdOral, $this->assessorInternal, $f3Oral, AssessmentVerdict::Pass);

        $this->interviews->complete($interview->refresh());

        $finalMatrix = $this->equivalency->forApplication($application, EquivalencyMatrixType::Final, $this->qaOfficer);

        $finalItems = [];
        foreach ($this->curriculumSubjects as $code => $subject) {
            $action = match (true) {
                in_array($code, ['GE-101', 'GE-102', 'CS-101', 'IT-101', 'PE-101']) => RecommendedAction::Credit,
                in_array($code, ['IT-102', 'IT-103', 'IT-104', 'IT-201', 'GE-103', 'GE-104', 'PE-102', 'NSTP-101']) => RecommendedAction::Enhance,
                default => RecommendedAction::Enroll,
            };

            $finalItems[] = [
                'curriculum_subject_id' => $subject->id,
                'subject_code' => $code,
                'subject_title' => $subject->title,
                'units' => $subject->units,
                'competency_level' => match ($action) {
                    RecommendedAction::Credit => CompetencyLevel::Full->value,
                    RecommendedAction::Enhance => CompetencyLevel::Partial->value,
                    RecommendedAction::Enroll => CompetencyLevel::None->value,
                },
                'evidence_source' => $action === RecommendedAction::Enroll ? null : 'Work experience and panel validation',
                'equivalent_credits' => $action === RecommendedAction::Credit ? $subject->units : ($action === RecommendedAction::Enhance ? $subject->units * 0.5 : 0),
                'recommended_action' => $action->value,
            ];
        }

        $this->equivalency->saveItems($finalMatrix, $finalItems);
        $this->equivalency->approve($finalMatrix, $this->qaOfficer);

        $enrollment = $this->enrollments->createFromApplication($application->refresh(), $this->coordinator, 'AY 2026-2027 First Semester');

        $adviserAssignment = AdviserAssignment::create([
            'student_id' => $enrollment->student_id,
            'adviser_id' => $this->adviser1->id,
            'term' => 'AY 2026-2027 First Semester',
            'assigned_by' => $this->programHead->id,
            'assigned_at' => now(),
            'is_active' => true,
        ]);

        $rubric = Rubric::create([
            'user_id' => $this->adviser1->id,
            'curriculum_subject_id' => $this->curriculumSubjects['IT-205']->id,
            'title' => 'Capstone Project Evaluation',
            'criteria' => [
                ['name' => 'Problem Definition', 'weight' => 20, 'levels' => [['score' => 0, 'description' => 'Poor'], ['score' => 50, 'description' => 'Adequate'], ['score' => 100, 'description' => 'Excellent']]],
                ['name' => 'Methodology', 'weight' => 25, 'levels' => [['score' => 0, 'description' => 'Poor'], ['score' => 50, 'description' => 'Adequate'], ['score' => 100, 'description' => 'Excellent']]],
                ['name' => 'Implementation', 'weight' => 30, 'levels' => [['score' => 0, 'description' => 'Poor'], ['score' => 50, 'description' => 'Adequate'], ['score' => 100, 'description' => 'Excellent']]],
                ['name' => 'Presentation', 'weight' => 25, 'levels' => [['score' => 0, 'description' => 'Poor'], ['score' => 50, 'description' => 'Adequate'], ['score' => 100, 'description' => 'Excellent']]],
            ],
            'max_score' => 100,
            'is_template' => false,
        ]);

        $activities = [
            ['activity_type' => ActivityType::ModularStudy, 'title' => 'Data Structures Deep Dive', 'curriculum_subject_id' => $this->curriculumSubjects['IT-103']->id, 'status' => ActivityStatus::Approved, 'score' => 88, 'feedback' => 'Excellent work'],
            ['activity_type' => ActivityType::CaseAnalysis, 'title' => 'Database Design Case Study', 'curriculum_subject_id' => $this->curriculumSubjects['IT-104']->id, 'status' => ActivityStatus::Submitted, 'score' => null, 'feedback' => null],
            ['activity_type' => ActivityType::Webinar, 'title' => 'Web Technologies Webinar', 'curriculum_subject_id' => $this->curriculumSubjects['IT-201']->id, 'status' => ActivityStatus::Assigned, 'score' => null, 'feedback' => null],
        ];

        foreach ($activities as $act) {
            EnhancementActivity::create(array_merge($act, [
                'student_id' => $enrollment->student_id,
                'adviser_id' => $this->adviser1->id,
                'due_date' => now()->addDays(30),
            ]));
        }

        $submittedEnrolledSubjectIds = [];
        foreach ($enrollment->subjects as $es) {
            if ($es->status === EnrollmentSubjectStatus::Enrolled) {
                $submittedEnrolledSubjectIds[] = $es->id;
            }
        }

        $gradedSubjects = array_slice($submittedEnrolledSubjectIds, 0, 3);
        foreach ($gradedSubjects as $index => $esId) {
            $gradeData = [
                'grade' => match ($index) { 0 => '1.50', 1 => '1.75', 2 => '2.00' },
                'grade_point' => match ($index) { 0 => 1.5, 1 => 1.75, 2 => 2.0 },
                'rubric_id' => $rubric->id,
                'rubric_score' => match ($index) { 0 => 88, 1 => 85, 2 => 80 },
            ];

            $grade = $this->grades->store($esId, $this->adviser1, $applicant, $gradeData);
            $this->grades->submit($grade->refresh());
            $this->grades->approve($grade->refresh(), $this->programHead);
        }

        $clearance = GraduationClearance::create([
            'student_id' => $applicant->id,
            'status' => GraduationClearanceStatus::Pending,
        ]);

        $docTypes = collect(config('eteeap.clearance_documents'));
        foreach ($docTypes as $docType) {
            $clearance->documents()->create([
                'document_type' => $docType['key'],
                'is_verified' => false,
            ]);
        }

        foreach ($clearance->documents as $doc) {
            $file = $this->createTempPdf();
            $this->graduation->verifyDocument($doc, $this->coordinator, $file);
        }

        $clearance->refresh();
        $this->graduation->confirm($clearance, $this->coordinator);

        $clearance->refresh();
        $this->graduation->approve($clearance, $this->vpaa);

        $clearance->refresh();
        $formFile = $this->createTempPdf();
        $this->graduation->submitClearanceForm($clearance, $formFile);

        $clearance->refresh();
        $this->graduation->releaseTor($clearance);
    }

    protected function seedRevisionScenario(): void
    {
        $this->command?->info('Seeding Applicant 2 — revision requested...');

        $applicant = $this->createUser('applicant', 'Applicant 2 (Revision Requested)', 'applicant2@adfc.edu.ph');

        $applicant->applicantProfile()->create([
            'birth_date' => now()->subYears(28),
            'citizenship' => 'Filipino',
            'civil_status' => 'Single',
            'gender' => 'female',
            'address' => '456 Quezon Ave, Quezon City',
            'highest_education' => 'College Graduate',
            'last_school_attended' => 'Ateneo de Manila University',
        ]);

        $applicant->experienceRecords()->create([
            'company' => 'Digital Solutions Inc.',
            'position' => 'Junior Developer',
            'start_date' => now()->subYears(6),
            'is_current' => true,
            'description' => 'Web application development and maintenance',
            'bsit_aligned' => true,
        ]);

        $applicant->educationRecords()->create([
            'school_name' => 'Ateneo de Manila University',
            'degree' => 'BS Information Technology',
            'field_of_study' => 'IT',
            'start_year' => 2012,
            'end_year' => 2016,
            'is_graduated' => true,
            'education_level' => 'college',
        ]);

        $application = $this->applications->currentDraftFor($applicant);
        $this->applications->submit($application);

        $application->refresh();
        $this->applications->requestRevision($application, $this->coordinator, 'Please attach a more detailed CV and update work experience descriptions.');

        $this->uploadDocuments($applicant, $application->refresh(), $this->coordinator, false);
    }

    protected function seedRejectedScenario(): void
    {
        $this->command?->info('Seeding Applicant 3 — rejected...');

        $applicant = $this->createUser('applicant', 'Applicant 3 (Rejected)', 'applicant3@adfc.edu.ph');

        $applicant->applicantProfile()->create([
            'birth_date' => now()->subYears(26),
            'citizenship' => 'Filipino',
            'civil_status' => 'Single',
            'gender' => 'male',
            'address' => '789 Rizal St, Makati',
            'highest_education' => 'College Level',
            'last_school_attended' => 'Mapua University',
        ]);

        $applicant->experienceRecords()->create([
            'company' => 'SmallBiz IT',
            'position' => 'IT Support',
            'start_date' => now()->subYears(3),
            'is_current' => true,
            'description' => 'Hardware and software support',
            'bsit_aligned' => false,
        ]);

        $applicant->educationRecords()->create([
            'school_name' => 'Mapua University',
            'degree' => null,
            'field_of_study' => 'Computer Engineering',
            'start_year' => 2015,
            'end_year' => 2018,
            'is_graduated' => false,
            'education_level' => 'college',
        ]);

        $draft = $this->applications->currentDraftFor($applicant);
        $draft->update([
            'status' => ApplicationStatus::Submitted,
            'submitted_at' => now(),
        ]);

        $draft->refresh();
        $this->applications->reject($draft, $this->coordinator, 'Insufficient relevant work experience in BSIT-aligned roles. Minimum 5 years required.');
    }

    protected function seedInProgressStudent(): void
    {
        $this->command?->info('Seeding Applicant 4 — in-progress student...');

        $student = $this->createUser('student', 'Applicant 4 (Active Student)', 'applicant4@adfc.edu.ph');
        $student->roles()->attach(Role::where('name', 'applicant')->first()->id);
        $student->refresh();

        $student->applicantProfile()->create([
            'birth_date' => now()->subYears(30),
            'citizenship' => 'Filipino',
            'civil_status' => 'Married',
            'gender' => 'female',
            'address' => '101 Mabini St, Manila',
            'highest_education' => 'College Graduate',
            'last_school_attended' => 'Polytechnic University of the Philippines',
        ]);

        $student->experienceRecords()->create([
            'company' => 'DataFix Corp',
            'position' => 'Systems Analyst',
            'start_date' => now()->subYears(7),
            'is_current' => true,
            'description' => 'Systems analysis and database management',
            'bsit_aligned' => true,
        ]);

        $student->educationRecords()->create([
            'school_name' => 'Polytechnic University of the Philippines',
            'degree' => 'BS Computer Engineering',
            'field_of_study' => 'Computer Engineering',
            'start_year' => 2010,
            'end_year' => 2014,
            'is_graduated' => true,
            'education_level' => 'college',
        ]);

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'application_id' => null,
            'academic_term' => 'AY 2026-2027 First Semester',
            'enrollment_date' => now(),
            'student_id_number' => app(EnrollmentService::class)->generateStudentIdNumber(),
            'status' => EnrollmentStatus::Enrolled,
        ]);

        $subjectCodes = ['IT-103', 'IT-104', 'IT-201', 'IT-105', 'IT-202', 'IT-E1'];
        $statuses = [EnrollmentSubjectStatus::Enrolled, EnrollmentSubjectStatus::Enrolled, EnrollmentSubjectStatus::Enrolled, EnrollmentSubjectStatus::Pending, EnrollmentSubjectStatus::Pending, EnrollmentSubjectStatus::Pending];

        foreach ($subjectCodes as $i => $code) {
            $subject = $this->curriculumSubjects[$code] ?? null;
            if (! $subject) {
                continue;
            }
            EnrollmentSubject::create([
                'enrollment_id' => $enrollment->id,
                'curriculum_subject_id' => $subject->id,
                'status' => $statuses[$i],
            ]);
        }

        AdviserAssignment::create([
            'student_id' => $student->id,
            'adviser_id' => $this->adviser1->id,
            'term' => 'AY 2026-2027 First Semester',
            'assigned_by' => $this->programHead->id,
            'assigned_at' => now(),
            'is_active' => true,
        ]);

        $demoRubric = Rubric::create([
            'user_id' => $this->adviser1->id,
            'curriculum_subject_id' => $this->curriculumSubjects['IT-103']->id,
            'title' => 'Standard Evaluation Rubric',
            'criteria' => [
                ['name' => 'Knowledge', 'weight' => 40, 'levels' => [['score' => 0, 'description' => 'Poor'], ['score' => 50, 'description' => 'Adequate'], ['score' => 100, 'description' => 'Excellent']]],
                ['name' => 'Skills Application', 'weight' => 40, 'levels' => [['score' => 0, 'description' => 'Poor'], ['score' => 50, 'description' => 'Adequate'], ['score' => 100, 'description' => 'Excellent']]],
                ['name' => 'Attitude', 'weight' => 20, 'levels' => [['score' => 0, 'description' => 'Poor'], ['score' => 50, 'description' => 'Adequate'], ['score' => 100, 'description' => 'Excellent']]],
            ],
            'max_score' => 100,
            'is_template' => false,
        ]);

        $enrolledSubjects = $enrollment->subjects()->where('status', EnrollmentSubjectStatus::Enrolled)->get();

        foreach ($enrolledSubjects as $i => $es) {
            if ($i >= 2) {
                break;
            }

            $grade = $this->grades->store($es->id, $this->adviser1, $student, [
                'grade' => $i === 0 ? '1.75' : '2.00',
                'grade_point' => $i === 0 ? 1.75 : 2.0,
                'rubric_id' => $demoRubric->id,
                'rubric_score' => $i === 0 ? 85 : 78,
            ]);

            if ($i === 0) {
                $this->grades->submit($grade->refresh());
            }
        }

        EnhancementActivity::create([
            'student_id' => $student->id,
            'adviser_id' => $this->adviser1->id,
            'curriculum_subject_id' => $this->curriculumSubjects['IT-103']->id,
            'activity_type' => ActivityType::SkillsDemo,
            'title' => 'Data Structures Implementation',
            'description' => 'Implement key data structures in Python',
            'due_date' => now()->addDays(14),
            'status' => ActivityStatus::Assigned,
        ]);
    }

    protected function seedPartialGraduation(): void
    {
        $this->command?->info('Seeding Applicant 5 — partial graduation...');

        $student = $this->createUser('student', 'Applicant 5 (Partial Graduation)', 'applicant5@adfc.edu.ph');
        $student->roles()->attach(Role::where('name', 'applicant')->first()->id);

        $student->applicantProfile()->create([
            'birth_date' => now()->subYears(32),
            'citizenship' => 'Filipino',
            'civil_status' => 'Single',
            'gender' => 'male',
            'address' => '222 Luna St, Mandaluyong',
            'highest_education' => 'College Graduate',
            'last_school_attended' => 'University of Santo Tomas',
        ]);

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'application_id' => null,
            'academic_term' => 'AY 2025-2026 Second Semester',
            'enrollment_date' => now()->subMonths(6),
            'student_id_number' => app(EnrollmentService::class)->generateStudentIdNumber(),
            'status' => EnrollmentStatus::Enrolled,
        ]);

        foreach ($this->curriculumSubjects as $code => $subject) {
            if ($code === 'IT-E3') {
                continue;
            }
            EnrollmentSubject::create([
                'enrollment_id' => $enrollment->id,
                'curriculum_subject_id' => $subject->id,
                'status' => in_array($code, ['IT-105', 'IT-106', 'IT-202', 'IT-203']) ? EnrollmentSubjectStatus::Enrolled : EnrollmentSubjectStatus::Completed,
            ]);
        }

        AdviserAssignment::create([
            'student_id' => $student->id,
            'adviser_id' => $this->adviser1->id,
            'term' => 'AY 2025-2026 Second Semester',
            'assigned_by' => $this->programHead->id,
            'assigned_at' => now()->subMonths(6),
            'is_active' => true,
        ]);

        $clearance = GraduationClearance::create([
            'student_id' => $student->id,
            'status' => GraduationClearanceStatus::Pending,
        ]);

        $docTypes = collect(config('eteeap.clearance_documents'));
        foreach ($docTypes as $i => $docType) {
            $doc = ClearanceDocument::create([
                'graduation_clearance_id' => $clearance->id,
                'document_type' => $docType['key'],
                'is_verified' => $i < 8,
                'verified_by' => $i < 8 ? $this->coordinator->id : null,
                'verified_at' => $i < 8 ? now()->subDays(5) : null,
            ]);

            if ($i < 8) {
                $file = $this->createTempPdf();
                $path = $file->store(
                    sprintf('graduation/%d/clearance/%s', $student->id, $docType['key']),
                    $this->disk,
                );

                $doc->update(['file_path' => $path]);
            }
        }

        $clearance->update(['clearance_form_path' => 'graduation/' . $student->id . '/clearance/form.pdf']);
    }

    protected function seedDraftOnly(): void
    {
        $this->command?->info('Seeding Applicant 6 — draft only...');

        $applicant = $this->createUser('applicant', 'Applicant 6 (Draft)', 'applicant6@adfc.edu.ph');

        $applicant->applicantProfile()->create([
            'birth_date' => now()->subYears(27),
            'citizenship' => 'Filipino',
            'civil_status' => 'Single',
            'gender' => 'female',
            'address' => '555 Banawe St, Quezon City',
            'highest_education' => 'College Graduate',
            'last_school_attended' => 'De La Salle University',
        ]);

        $this->applications->currentDraftFor($applicant);
    }

    protected function seedAdviserWorkload(): void
    {
        $this->command?->info('Seeding adviser workload data...');

        $studentRole = Role::where('name', 'student')->first();

        for ($i = 1; $i <= 2; $i++) {
            $student = User::factory()->create([
                'name' => "Adviser2 Student {$i}",
                'email' => "adviser2.student{$i}@adfc.edu.ph",
                'password' => Hash::make('password'),
            ]);
            if ($studentRole) {
                $student->roles()->sync([$studentRole->id]);
            }

            $enrollment = Enrollment::create([
                'student_id' => $student->id,
                'application_id' => null,
                'academic_term' => 'AY 2026-2027 First Semester',
                'enrollment_date' => now(),
                'student_id_number' => app(EnrollmentService::class)->generateStudentIdNumber(),
                'status' => EnrollmentStatus::Enrolled,
            ]);

            AdviserAssignment::create([
                'student_id' => $student->id,
                'adviser_id' => $this->adviser2->id,
                'term' => 'AY 2026-2027 First Semester',
                'assigned_by' => $this->programHead->id,
                'assigned_at' => now(),
                'is_active' => true,
            ]);
        }

        $this->command?->info('Adviser 1 has 2 students, Adviser 2 has 2 students, Adviser 3 has 0 students.');
    }

    protected function seedSystemData(): void
    {
        $this->command?->info('Seeding system data (audit logs, notifications, settings)...');

        Setting::create(['key' => 'min_age', 'value' => '25']);

        $notifications = [
            ['user_id' => $this->coordinator->id, 'type' => 'application.submitted', 'title' => 'New Application Submitted', 'body' => 'Applicant 1 submitted their ETEEAP application.'],
            ['user_id' => $this->admin->id, 'type' => 'system.info', 'title' => 'System Initialized', 'body' => 'Demo data seeder completed successfully.'],
            ['user_id' => $this->vpaa->id, 'type' => 'clearance.in_review', 'title' => 'Clearance Ready for Review', 'body' => 'Applicant 1 graduation clearance is ready for VPAA approval.'],
        ];

        foreach ($notifications as $notif) {
            Notification::create(array_merge($notif, [
                'sent_at' => now(),
            ]));
        }
    }
}
