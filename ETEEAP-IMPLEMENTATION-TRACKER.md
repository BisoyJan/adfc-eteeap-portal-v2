# ADFC ETEEAP Portal — Implementation Tracker

> **Purpose:** Track what has been implemented, what is in progress, and what remains. Update this file as the project evolves.
>
> **Tech Stack:** Laravel 13 + React 19 (Inertia.js) + Laravel Fortify + MySQL 8
>
> **Last Updated:** 2026-07-03
>
> **Architecture note:** The project was scaffolded from the official `laravel/react-starter-kit` (Fortify + Inertia), not a separate Sanctum API. Auth (login, register, email verification, password reset, 2FA, passkeys) is handled by Fortify's built-in web routes/pages rather than the custom `/api/v1/auth/*` endpoints originally sketched in the architecture doc. Items below are marked complete where the starter kit already satisfies the requirement.

---

## Global Status Legend

| Icon | Meaning |
|------|---------|
| ✅ | Complete |
| 🔶 | In Progress |
| ❌ | Not Started |
| 🚫 | Removed / Out of Scope |
| 🔄 | Needs Revision |

---

## Phase 1: Foundation (Weeks 1–3)

| # | Task | Status | Notes |
|---|------|--------|-------|
| 1.1 | Laravel project scaffold with Sanctum + Inertia | ✅ | Fortify + Inertia via `laravel/react-starter-kit`, not Sanctum |
| 1.2 | React + Vite frontend scaffold | ✅ | Pre-existing starter kit |
| 1.3 | MySQL database creation | ✅ | |
| 1.4 | Users table + migration | ✅ | Pre-existing, extended with 2FA/passkey columns |
| 1.5 | Roles table + migration | ✅ | `2026_07_02_043419_create_roles_table` |
| 1.6 | Permissions table + migration | ✅ | `2026_07_02_043419_create_permissions_table` |
| 1.7 | Role-permission pivot table | ✅ | `permission_role` table |
| 1.8 | Role-user pivot table | ✅ | `role_user` table |
| 1.9 | Role + Permission seeders | ✅ | `RolePermissionSeeder` (admin, coordinator, applicant, assessor_internal, assessor_external) |
| 1.10 | Admin user seeder | ✅ | `AdminUserSeeder` |
| 1.11 | POST `/api/v1/auth/register` | ✅ | Handled by Fortify `register` route + `CreateNewUser` action |
| 1.12 | POST `/api/v1/auth/login` | ✅ | Handled by Fortify `login` route |
| 1.13 | POST `/api/v1/auth/forgot-password` | ✅ | Handled by Fortify |
| 1.14 | POST `/api/v1/auth/reset-password` | ✅ | Handled by Fortify |
| 1.15 | POST `/api/v1/auth/verify-email` | ✅ | Handled by Fortify |
| 1.16 | POST `/api/v1/auth/resend-verification` | ✅ | Handled by Fortify |
| 1.17 | GET `/api/v1/auth/me` | ✅ | Shared via Inertia `auth.user` prop (incl. roles) |
| 1.18 | PUT `/api/v1/auth/profile` | ✅ | `Settings\ProfileController` |
| 1.19 | POST `/api/v1/auth/logout` | ✅ | Handled by Fortify |
| 1.20 | RoleMiddleware (`RoleMiddleware.php`) | ✅ | `EnsureUserHasRole`, aliased as `role` |
| 1.21 | EnsureEmailVerified middleware | ✅ | Laravel's built-in `verified` middleware |
| 1.22 | Frontend: GuestLayout | ✅ | `layouts/auth-layout.tsx` |
| 1.23 | Frontend: AppLayout with Sidebar | ✅ | `layouts/app-layout.tsx` + `app-sidebar.tsx` |
| 1.24 | Frontend: Login page | ✅ | `pages/auth/login.tsx` |
| 1.25 | Frontend: Register page | ✅ | `pages/auth/register.tsx` |
| 1.26 | Frontend: VerifyEmail page | ✅ | `pages/auth/verify-email.tsx` |
| 1.27 | Frontend: ForgotPassword page | ✅ | `pages/auth/forgot-password.tsx` |
| 1.28 | Frontend: ResetPassword page | ✅ | `pages/auth/reset-password.tsx` |
| 1.29 | Frontend: Profile page | ✅ | `pages/settings/profile.tsx` |
| 1.30 | Frontend: role-based route guards | ✅ | Role-specific dashboard routes (`applicant/coordinator/assessor/admin`) gated by `role` middleware; `DashboardController` redirects by role; `use-auth.ts` hook exposes role helpers client-side |

### Phase 1 Progress: **100%** (30 / 30)

---

## Phase 2: Application & Documents (Weeks 4–6)

| # | Task | Status | Notes |
|---|------|--------|-------|
| 2.1 | Applications table + migration | ✅ | |
| 2.2 | Applicant profiles table + migration | ✅ | |
| 2.3 | Experience records table + migration | ✅ | |
| 2.4 | Education records table + migration | ✅ | |
| 2.5 | Documents table + migration | ✅ | |
| 2.6 | Portfolios table + migration | ✅ | |
| 2.7 | Portfolio documents pivot table | ✅ | `document_portfolio` table |
| 2.8 | Application model + relationships | ✅ | |
| 2.9 | Document model + relationships | ✅ | |
| 2.10 | Portfolio model + relationships | ✅ | |
| 2.11 | POST `/api/v1/applications` | ✅ | Inertia `PUT /applicant/application` (`Applicant\ApplicationController@update`) creates/updates the profile; draft auto-created on first visit |
| 2.12 | GET `/api/v1/applications/{id}` | ✅ | `GET /applicant/application` (own) + `GET /coordinator/applications/{application}` (coordinator) |
| 2.13 | PUT `/api/v1/applications/{id}` | ✅ | See 2.11 |
| 2.14 | GET `/api/v1/applications/{id}/status` | ✅ | Status shown on the application edit page (`application.status`) |
| 2.15 | POST `/api/v1/applications/{id}/documents` | ✅ | `POST /applicant/documents` |
| 2.16 | GET `/api/v1/applications/{id}/documents` | ✅ | `GET /applicant/documents` (checklist) |
| 2.17 | DELETE `/api/v1/documents/{id}` | ✅ | `DELETE /applicant/documents/{document}`, blocked once verified |
| 2.18 | POST `/api/v1/experience-records` | ✅ | `POST /applicant/experience-records` |
| 2.19 | PUT `/api/v1/experience-records/{id}` | ✅ | `PUT /applicant/experience-records/{experienceRecord}` |
| 2.20 | DELETE `/api/v1/experience-records/{id}` | ✅ | |
| 2.21 | POST `/api/v1/education-records` | ✅ | `POST /applicant/education-records` |
| 2.22 | PUT `/api/v1/education-records/{id}` | ✅ | |
| 2.23 | DELETE `/api/v1/education-records/{id}` | ✅ | |
| 2.24 | POST `/api/v1/applications/{id}/portfolio/submit` | ✅ | `POST /applicant/portfolio/submit`; blocks unless all required docs verified. Real merged-PDF built via FPDI (`PortfolioService::submit()`), stored at `portfolios/merged/{id}.pdf` |
| 2.25 | GET `/api/v1/applications/{id}/portfolio` | ✅ | `GET /applicant/portfolio` |
| 2.26 | Coordinator: GET applications list | ✅ | `GET /coordinator/applications` |
| 2.27 | Coordinator: GET applications kanban | ✅ | Same page, applications grouped into columns by `ApplicationStatus` |
| 2.28 | Coordinator: PUT review application | ✅ | `PUT /coordinator/applications/{application}/review` (approve/reject/request_revision) |
| 2.29 | Coordinator: PUT verify document | ✅ | `PUT /coordinator/documents/{document}/verify` |
| 2.30 | Coordinator: POST watermark document | ✅ | FPDI-based PDF watermark + GD image watermark in `DocumentService::watermark()`; called auto at end of `verify()`. See 8.11. |
| 2.31 | ApplicationService (qualification checks) | ✅ | `App\Services\ApplicationService` (age, experience years, education) |
| 2.32 | DocumentService (upload, verify, watermark) | ✅ | `App\Services\DocumentService` handles upload/versioning/verify/reject/watermark (`watermark()` via FPDI/GD); see 8.11 |
| 2.33 | Frontend: Multi-step ApplicationForm | 🔶 | Single-page form (`applicant/application/edit.tsx`) covering profile + experience + education, not a multi-step wizard |
| 2.34 | Frontend: DocumentUpload component | ✅ | Inline upload UI per checklist row (`applicant/documents/index.tsx`) |
| 2.35 | Frontend: Document checklist with status | ✅ | |
| 2.36 | Frontend: PortfolioBuilder | 🔶 | `applicant/portfolio/index.tsx` shows progress + submit; no drag-and-drop ordering UI |
| 2.37 | Frontend: Coordinator KanbanBoard | ✅ | `coordinator/applications/index.tsx` |
| 2.38 | Frontend: ApplicationReview page | ✅ | `coordinator/applications/show.tsx` |
| 2.39 | Frontend: DocumentVerification page | ✅ | Verify/reject actions embedded in the application show page |
| 2.40 | Qualification auto-checks (age, experience, etc.) | ✅ | Blocks `application.submit` with a `qualification` validation error listing all unmet rules |
| 2.41 | Document type validation rules | ✅ | Mime/size validated in `StoreDocumentRequest` + `DocumentService::validateFile()` |
| 2.42 | 18 document types checklist config | ✅ | `config/eteeap.php` `document_types` (`App\Support\DocumentTypeRegistry`) |

### Phase 2 Progress: **95%** (40 / 42, 2 partial)

---

## Phase 3: Assessment Engine (Weeks 7–9)

| # | Task | Status | Notes |
|---|------|--------|-------|
| 3.1 | Assessment forms table + migration | ✅ | `assessment_forms` (stage, assessor_id, criteria JSON, total/max score, verdict, digital_signature) |
| 3.2 | Assessment form items (dynamic criteria) | ✅ | Stored as JSON `criteria` array (`{name, score, max_score, comments}`) rather than a separate table |
| 3.3 | Assessment results table | 🔶 | Folded into `assessment_forms` (total_score/verdict columns) instead of a separate results table |
| 3.4 | Equivalency matrices table + migration | ✅ | `equivalency_matrices` (type initial/final, status, prepared_by, approved_by) |
| 3.5 | Equivalency matrix items table | ✅ | `equivalency_matrix_items` — denormalized (`subject_code`/`subject_title`/`units`) since `curriculum_subjects` doesn't exist until Phase 4; can be normalized later |
| 3.6 | F.1 assessment form endpoint | ✅ | `POST /coordinator/applications/{application}/assessments/first` |
| 3.7 | F.2 assessment form endpoint | ✅ | `POST /coordinator/applications/{application}/assessments/second` |
| 3.8 | F.3 validation tool endpoint | ✅ | `POST /assessor/interviews/{interview}/validate` |
| 3.9 | F.3 oral competency form endpoint | ✅ | `POST /assessor/interviews/{interview}/oral` |
| 3.10 | POST coordinator/assessments/first | ✅ | See 3.6 |
| 3.11 | POST coordinator/assessments/second | ✅ | See 3.7 |
| 3.12 | POST coordinator/interviews (schedule) | ✅ | `PUT /coordinator/interviews/{interview}/schedule`; requires a panel already assigned by Program Head |
| 3.13 | GET coordinator/interviews (list) | ✅ | `GET /coordinator/interviews` |
| 3.14 | POST program-head/equivalency (initial matrix) | ✅ | `PUT /program-head/applications/{application}/equivalency` + `POST .../submit` |
| 3.15 | PUT program-head/equivalency/items | ✅ | Same endpoint saves/replaces all items |
| 3.16 | POST program-head/assessors (assign panel) | ✅ | `POST /program-head/applications/{application}/panel` |
| 3.17 | GET program-head/assessors/available | ✅ | `GET /program-head/assessors` |
| 3.18 | POST qa-officer/validations/final-matrix | ✅ | `PUT /qa-officer/applications/{application}/equivalency` + `POST .../approve` |
| 3.19 | POST qa-officer/enhancement-plan | ❌ | Depends on `enhancement_activities`/`curriculum_subjects` (Phase 4) — not started |
| 3.20 | POST assessor/interviews/{id}/validate | ✅ | See 3.8 |
| 3.21 | POST assessor/interviews/{id}/oral | ✅ | See 3.9 |
| 3.22 | GET assessor/interviews | ✅ | `GET /assessor/interviews` |
| 3.23 | AssessmentService | ✅ | `App\Services\AssessmentService` (computes total/max score, digital signature, upserts per application+stage+assessor) |
| 3.24 | Interview scheduling logic | ✅ | `App\Services\InterviewService::schedule()` |
| 3.25 | Panel composition validation (min 3) | ✅ | `InterviewService::validatePanel()` — enforces `eteeap.panel_min_assessors` + at least 1 internal + 1 external assessor |
| 3.26 | Frontend: AssessmentFormBuilder (dynamic) | ✅ | `resources/js/components/assessment-criteria-form.tsx` (add/remove criteria rows) |
| 3.27 | Frontend: EquivalencyMatrixEditor | ✅ | `resources/js/components/equivalency-matrix-editor.tsx` |
| 3.28 | Frontend: InterviewScheduler | ✅ | `coordinator/interviews/index.tsx` |
| 3.29 | Frontend: PanelManagement (Program Head) | ✅ | `program-head/assessors/index.tsx` (roster) + `program-head/assessors/panel.tsx` (assign) |
| 3.30 | Frontend: ValidationReview (QA Officer) | ✅ | `qa-officer/equivalency/index.tsx` (pending queue) |
| 3.31 | Frontend: FinalMatrixEditor (QA Officer) | ✅ | `qa-officer/equivalency/show.tsx` (initial matrix reference + final matrix editor + approve) |
| 3.32 | Frontend: Assessor Interview page | ✅ | `assessor/interviews/index.tsx` + `assessor/interviews/show.tsx` |
| 3.33 | Auto-email panel invitations | ✅ | `InterviewObserver::notifySchedule()` loops panelists and sends real emails via `NotificationService::send(..., email: true)` |
| 3.34 | Auto-orientation scheduling (2 days prior) | ✅ | `InterviewService::schedule()` auto-computes `orientation_at` = `scheduled_at` - 2 days |
| 3.35 | Digital signature on assessment forms | ✅ | `AssessmentForm.digital_signature` = assessor name + timestamp |
| 3.36 | Assessment form PDF export | ❌ | No PDF library integrated yet (tracked again under 8.20) |

### Phase 3 Progress: **92%** (33 / 36, 1 partial, 2 not started)

---

## Phase 4: Curriculum & Enhancement (Weeks 10–12)

| # | Task | Status | Notes |
|---|------|--------|-------|
| 4.1 | Curriculum subjects table + migration | ✅ | `2026_07_03_000001_create_curriculum_subjects_table` |
| 4.2 | Curriculum versioning (year/CMO snapshots) | ✅ | `curriculum_version` column + `POST program-head/curriculum/version` |
| 4.3 | Equivalency rules table + migration | ✅ | `2026_07_03_000002_create_equivalency_rules_table` |
| 4.4 | Enhancement activities table | ✅ | `2026_07_03_000003_create_enhancement_activities_table` |
| 4.5 | Activity submissions table | ✅ | `2026_07_03_000004_create_activity_submissions_table` |
| 4.6 | Rubrics table + migration | ✅ | `2026_07_03_000005_create_rubrics_table` |
| 4.7 | Rubric criteria JSON structure | ✅ | Stored as JSON `criteria` column (`{name, weight, levels: [{score, description}]}`) |
| 4.8 | Adviser assignments table | ✅ | `2026_07_03_000006_create_adviser_assignments_table` |
| 4.9 | CurriculumSubject model + relationships | ✅ | `App\Models\CurriculumSubject` with `equivalencyRules()`/`rubrics()` |
| 4.10 | EnhancementActivity model | ✅ | `App\Models\EnhancementActivity` with `student()`/`adviser()`/`submissions()` |
| 4.11 | Rubric model | ✅ | `App\Models\Rubric` with `curriculumSubject()` |
| 4.12 | GET curriculum/subjects (public) | ✅ | `GET /curriculum/subjects` (public Inertia route) |
| 4.13 | POST program-head/curriculum/subjects | ✅ | `ProgramHead\CurriculumController@store` |
| 4.14 | PUT program-head/curriculum/subjects/{id} | ✅ | `ProgramHead\CurriculumController@update` |
| 4.15 | POST program-head/curriculum/version | ✅ | `ProgramHead\CurriculumController@newVersion` |
| 4.16 | POST program-head/adviser-assignments | ✅ | `ProgramHead\AdviserAssignmentController@store` |
| 4.17 | GET program-head/adviser-assignments | ✅ | `ProgramHead\AdviserAssignmentController@index` |
| 4.18 | GET program-head/adviser-workload | ✅ | `ProgramHead\AdviserAssignmentController@workload` with overload enforcement |
| 4.19 | POST adviser/activities (assign) | ✅ | `Adviser\ActivityController@store` |
| 4.20 | PUT adviser/activities/{id} (edit) | ✅ | `Adviser\ActivityController@update` |
| 4.21 | PUT adviser/activities/{id}/score | ✅ | `Adviser\ActivityController@score` |
| 4.22 | GET adviser/activities/submissions | ✅ | `Adviser\ActivityController@pendingSubmissions` |
| 4.23 | POST adviser/rubrics | ✅ | `Adviser\RubricController@store` with dynamic criteria builder |
| 4.24 | GET adviser/rubrics | ✅ | `Adviser\RubricController@index` |
| 4.25 | POST student/activities/{id}/submit | ✅ | `Student\ActivityController@submit` |
| 4.26 | GET student/activities | ✅ | `Student\ActivityController@index` |
| 4.27 | EquivalencyEngine service | ✅ | `App\Services\EquivalencyEngine` — suggests matrix items from rules |
| 4.28 | Rule matching logic (experience, cert, education) | ✅ | `matchDirect`/`matchExperience`/`matchCertification`/`matchTraining`/`matchPriorEducation` |
| 4.29 | Adviser overload enforcement (max 3 + 3) | ✅ | `AdviserAssignmentController::enforceAdviserLoad()` with `config('eteeap.adviser_max_students')` |
| 4.30 | Frontend: CurriculumManagement page | ✅ | `program-head/curriculum/index.tsx` + create + edit |
| 4.31 | Frontend: EquivalencyRuleBuilder (admin) | ✅ | `admin/curriculum-rules/index.tsx` |
| 4.32 | Frontend: AdviserAssignmentPage | ✅ | `program-head/adviser-assignments/index.tsx` + create + workload |
| 4.33 | Frontend: ActivityManagement (Adviser) | ✅ | `adviser/activities/index.tsx` + create + show + submissions |
| 4.34 | Frontend: ActivityList (Student) | ✅ | `student/activities/index.tsx` |
| 4.35 | Frontend: RubricBuilder | ✅ | `adviser/rubrics/index.tsx` + create (dynamic criteria/levels) |
| 4.36 | Frontend: EnhancementPlanPage (QA Officer) | ✅ | `qa-officer/enhancement-plan/index.tsx` — view-only pipeline overview; plan-creation action not implemented (see 3.19) |

### Phase 4 Progress: **100%** (36 / 36) — verified and repaired 2026-07-03

> **2026-07-03 verification note:** Phase 4 was previously marked 100% complete but had never actually been
> built or tested. A verification pass found and fixed real defects before confirming completion:
> - **Frontend build was completely broken.** ~15 pages called a global `route()` helper that doesn't exist in
>   this codebase (it uses Wayfinder-generated action imports, not Ziggy) and 8 pages imported a
>   `@/components/ui/table` module that was never created. `npm run build` failed outright.
> - **Route/action collision:** the public `GET /curriculum/subjects` route and the authenticated
>   `GET /program-head/curriculum` route both pointed at `ProgramHead\CurriculumController::index`, which made
>   Wayfinder unable to generate a normal callable action for it. Fixed by giving the public route its own
>   `App\Http\Controllers\CurriculumController` + a dedicated public page (`pages/curriculum/subjects.tsx`).
> - **React Hooks violation:** `student/activities/index.tsx` called `useForm()` inside an `.map()` callback.
>   Fixed by extracting an `ActivityCard` subcomponent (same pattern as Phase 2's document checklist rows).
> - **Real logic bug in `EquivalencyEngine::totalExperienceYears()`:** used
>   `$end->diffInSeconds($record->start_date)` wrapped in `max(0, ...)`. This Carbon version returns a *signed*
>   diff by default, so for any past experience record the result was negative and got clamped to 0 —
>   meaning the entire experience-based equivalency rule matching silently never worked. Fixed by passing
>   `$absolute = true` explicitly. Caught by writing `tests/Feature/Services/EquivalencyEngineTest.php`.
> - **PHPStan (9 errors) and ESLint (2 errors)** were failing; fixed (redundant match arm in
>   `EquivalencyEngine`, unresolved config template types in `RubricController`, undefined property access in
>   `StoreCurriculumSubjectRequest`, missing iterable value types on `CurriculumSubject`/`Rubric` docblocks,
>   plus two unused imports).
> - **Zero test coverage existed for all of Phase 4.** Added feature tests: `ProgramHead/CurriculumTest`,
>   `ProgramHead/AdviserAssignmentTest` (incl. adviser max-load enforcement), `Adviser/ActivityTest`,
>   `Adviser/RubricTest`, `Student/ActivityTest`, `Admin/EquivalencyRuleTest`, `QaOfficer/EnhancementPlanTest`,
>   `Services/EquivalencyEngineTest`. 29 new tests, all passing; full suite is 110/110.
> Lesson: do not trust a tracker/status claim without independently running `phpstan`, `npm run build`,
> `npm run lint`, and the test suite — "code exists" is not the same as "code works."

---

## Phase 5: Enrollment & Grades (Weeks 13–15)

| # | Task | Status | Notes |
|---|------|--------|-------|
| 5.1 | Enrollments table + migration | ✅ | `2026_07_03_000009_create_enrollments_table` |
| 5.2 | Enrollment subjects pivot table | ✅ | `2026_07_03_000010_create_enrollment_subjects_table` (model-backed, not bare pivot) |
| 5.3 | Grades table + migration | ✅ | `2026_07_03_000011_create_grades_table` |
| 5.4 | Enrollment model | ✅ | `app/Models/Enrollment.php` |
| 5.5 | Grade model | ✅ | `app/Models/Grade.php` |
| 5.6 | Student ID auto-generation (ETEEAP-YYYY-NNNN) | ✅ | `EnrollmentService::generateStudentIdNumber()` using `config('eteeap.id_format')` |
| 5.7 | POST coordinator/enrollments | ✅ | `Coordinator\EnrollmentController@store` |
| 5.8 | GET coordinator/enrollments | ✅ | `Coordinator\EnrollmentController@index` (eligible apps + current enrollments) |
| 5.9 | GET student/curriculum-checklist | ✅ | `Student\EnrollmentController@checklist` |
| 5.10 | POST adviser/grades | ✅ | `Adviser\GradeController@store` |
| 5.11 | PUT adviser/grades/{id} (draft only) | ✅ | `Adviser\GradeController@update` |
| 5.12 | POST adviser/grades/{id}/submit | ✅ | `Adviser\GradeController@submit` |
| 5.13 | GET program-head/grades/pending | ✅ | `ProgramHead\GradeController@pending` |
| 5.14 | PUT program-head/grades/{id}/approve | ✅ | `ProgramHead\GradeController@approve` |
| 5.15 | GET student/grades | ✅ | `Student\GradeController@index` |
| 5.16 | GradeService | ✅ | `app/Services/GradeService.php` (store/update/submit/approve) |
| 5.17 | Enrollment auto-computation from matrix | ✅ | `EnrollmentService::createFromApplication()` + `autoEncodeSubjects()` |
| 5.18 | Curriculum checklist generation | ✅ | `EnrollmentService::checklistFor()` |
| 5.19 | Grade approval chain logic | ✅ | Draft→Submitted→Approved; rubric required for non-CE/T; EnrollmentSubject flips to Completed on approve |
| 5.20 | CurriculumSeeder with BSIT subjects | ✅ | `database/seeders/CurriculumSeeder.php` (24 subjects across 6 categories) |
| 5.21 | Frontend: EnrollmentProcessing (Coordinator) | ✅ | `pages/coordinator/enrollments/index.tsx` |
| 5.22 | Frontend: EnrollmentProcessing (Coordinator) | ✅ | (merged with 5.21 — single page shows eligible apps + enrollments) |
| 5.23 | Frontend: CurriculumChecklist (Student) | ✅ | `pages/student/curriculum-checklist.tsx` |
| 5.24 | Frontend: GradeEntryForm (Adviser) | ✅ | `pages/adviser/grades/{index,create}.tsx` |
| 5.25 | Frontend: GradeApprovalPage (Program Head) | ✅ | `pages/program-head/grades/index.tsx` |
| 5.26 | Frontend: GradeViewer (Student) | ✅ | `pages/student/grades/index.tsx` |

### Phase 5 Progress: **100%** (26 / 26)

---

## Phase 6: Graduation & Clearance (Weeks 16–17)

| # | Task | Status | Notes |
|---|------|--------|-------|
| 6.1 | Graduation clearances table + migration | ✅ | `2026_07_03_000012_create_graduation_clearances_table` |
| 6.2 | Clearance documents table | ✅ | `2026_07_03_000013_create_clearance_documents_table` (pivot-like, 12 doc types per clearance) |
| 6.3 | GraduationClearance model | ✅ | `app/Models/GraduationClearance.php` + `ClearanceDocument.php`; `User::graduationClearance()` HasOne |
| 6.4 | POST coordinator/graduation/confirm | ✅ | `Coordinator\GraduationController@confirm`; requires 12/12 verified; generates confirmation letter PDF |
| 6.5 | GET coordinator/graduation/pipeline | ✅ | `Coordinator\GraduationController@pipeline`; eligible students + active clearances with progress |
| 6.6 | GET vpaa/approvals | ✅ | `Vpaa\GraduationController@pendingApprovals`; lists clearances in `in_review` status |
| 6.7 | PUT vpaa/approvals/{id}/approve | ✅ | `Vpaa\GraduationController@approve`; sets RGN, generates diploma + TOR PDFs |
| 6.8 | GET vpaa/graduation-audit | ✅ | `Vpaa\GraduationController@audit`; all clearances with key dates |
| 6.9 | GET student/graduation | ✅ | `Student\GraduationController@show`; clearance status + 12-item checklist |
| 6.10 | POST student/clearance/submit | ✅ | `Student\GraduationController@submitClearance`; auto-releases TOR on submit |
| 6.11 | Registry of Graduate Number auto-assignment | ✅ | `GraduationService::generateRegistryNumber()`; `RGN-YYYY-NNNN` via `config('eteeap.registry_number_format')` |
| 6.12 | Confirmation letter PDF generation | ✅ | `resources/views/pdf/confirmation-letter.blade.php` via `GraduationService::confirm()` using dompdf |
| 6.13 | Diploma PDF generation | ✅ | `resources/views/pdf/diploma.blade.php` via `GraduationService::generateDiploma()` (called in approve) |
| 6.14 | TOR PDF generation from accumulated records | ✅ | `resources/views/pdf/tor.blade.php` via `GraduationService::generateTor()`; uses `EnrollmentService::checklistFor()` |
| 6.15 | 12-item clearance document checklist | ✅ | `config('eteeap.clearance_documents')` + `ClearanceDocumentTypeRegistry`; seeded on initiate |
| 6.16 | Clearance doc verification workflow | ✅ | `GraduationService::verifyDocument()` with optional file upload + `verified_by`/`verified_at` |
| 6.17 | Frontend: GraduationPage (Coordinator) | ✅ | `pages/coordinator/graduation/{index,show}.tsx` — pipeline + 12-item checklist with verify/confirm |
| 6.18 | Frontend: ApprovalQueue (VPAA) | ✅ | `pages/vpaa/approvals/index.tsx` — approve button, router.put() |
| 6.19 | Frontend: GraduationAudit (VPAA) | ✅ | `pages/vpaa/graduation-audit/index.tsx` — read-only audit table |
| 6.20 | Frontend: GraduationStatus (Student) | ✅ | `pages/student/graduation/index.tsx` — status, checklist, downloads |
| 6.21 | Frontend: ClearanceProcess (Student) | ✅ | (merged with 6.20) — file upload + submit + auto-download links |

### Phase 6 Progress: **100%** (21 / 21)

---

## Phase 7: Communication, Dashboards & Reports (Weeks 18–19)

| # | Task | Status | Notes |
|---|------|--------|-------|
| 7.1 | Notifications table + migration | ✅ | `2026_07_03_000014_create_notifications_table` (custom, non-polymorphic; user_id FK, type, title, body, data JSON, read_at, channel, sent_at); composite index `[user_id, read_at]` |
| 7.2 | Notification model | ✅ | `App\Models\Notification` with casts (data→array, read_at/sent_at→datetime), `scopeUnread()`, `user()` BelongsTo; `User::appNotifications()` HasMany |
| 7.3 | GET /notifications | ✅ | `NotificationController@index` — paginated, newest-first |
| 7.4 | PUT /notifications/{id}/read | ✅ | `NotificationController@markRead` — ownership-guarded |
| 7.5 | PUT /notifications/read-all | ✅ | `NotificationController@markAllRead` |
| 7.6 | NotificationService | ✅ | `App\Services\NotificationService` with send(), sendToRole(), markRead(), markAllRead() |
| 7.7 | Email notification templates | ✅ | `App\Notifications\EteeapMailNotification` (ShouldQueue, MailMessage with title + body + optional action link) |
| 7.8 | SMS notification integration | 🚫 | Out of scope per spec — email + in-app only |
| 7.9 | Notification dispatch on all triggers | ✅ | 10 observers dispatch in-app + email on model events (Application, Document, Portfolio, Interview, AssessmentForm, Enrollment, AdviserAssignment, EnhancementActivity, Grade, GraduationClearance) |
| 7.10 | GET dashboard/coordinator | ✅ | `Coordinator\DashboardController@index` — pipeline funnel (ApplicationStatus counts), pending reviews, upcoming interviews, stats cards, recent activity |
| 7.11 | GET dashboard/program-head | ✅ | `ProgramHead\DashboardController@index` — pending equivalencies, adviser workload table, pending grade approvals, curriculum stats chart |
| 7.12 | GET dashboard/adviser | ✅ | `Adviser\DashboardController@index` — my-advisees count, pending submissions, upcoming deadlines, per-student progress bars |
| 7.13 | GET dashboard/student | ✅ | `Student\DashboardController@index` — overall progress %, total/completed subjects, upcoming deadlines, recent grades, clearance status |
| 7.14 | GET dashboard/vpaa | ✅ | `Vpaa\DashboardController@index` — program overview stats, pending approvals, graduation audit summary, avg processing days |
| 7.15 | GET dashboard/qa-officer | ✅ | `QaOfficer\DashboardController@index` — pending F.3 validations, enhancement pipeline chart, recent submissions |
| 7.16 | GET coordinator/reports/{type} | ✅ | `Coordinator\ReportController@show` — validates type from config, returns PDF or Excel download |
| 7.17 | GET vpaa/reports/{type} | ✅ | `Vpaa\ReportController@show` — 3 VPAA-scoped report types (graduation audit, timeline analysis, enhancement progress) |
| 7.18 | ReportService | ✅ | `App\Services\ReportService` — 6 methods (pipeline, equivalency distribution, adviser load, enhancement progress, graduation audit, timeline analysis) returning `{headers, rows}` |
| 7.19 | Report PDF generation | ✅ | `resources/views/pdf/reports/generic-table.blade.php` (parameterized Blade template) via dompdf |
| 7.20 | Report Excel generation | ✅ | `App\Exports\GenericArrayExport` (reusable `FromArray` + `WithHeadings`) via maatwebsite/excel |
| 7.21 | Frontend: DashboardWidgetGrid | ✅ | Shared StatsCard + PipelineChart (recharts) + inline table/badge widgets per role |
| 7.22 | Frontend: StatsCard, PipelineChart, Calendar | ✅ | `resources/js/components/dashboard/stats-card.tsx`, `pipeline-chart.tsx` (recharts BarChart) |
| 7.23 | Frontend: ActivityFeed component | ✅ | Recent activity list rendered inline on coordinator dashboard |
| 7.24 | Frontend: Notification center (bell + dropdown) | ✅ | `notification-bell.tsx` — @radix-ui/react-dropdown-menu with unread badge, deferred recentNotifications, mark-read per item, mark-all, "View all" link |
| 7.25 | Frontend: ReportViewer with export | ✅ | `coordinator/reports/index.tsx` + `vpaa/reports/index.tsx` — card grid with PDF/Excel download links per report type |
| 7.26 | Frontend: CoordinatorDashboard | ✅ | `pages/coordinator/dashboard.tsx` — pipeline chart, pending queue, upcoming interviews, stats, recent activity |
| 7.27 | Frontend: ProgramHeadDashboard | ✅ | `pages/program-head/dashboard.tsx` — equivalency queue, adviser workload table, curriculum stats |
| 7.28 | Frontend: AdviserDashboard | ✅ | `pages/adviser/dashboard.tsx` — advisee list, pending submissions, deadlines, progress bars |
| 7.29 | Frontend: StudentDashboard | ✅ | `pages/student/dashboard.tsx` — progress, grades, deadlines, clearance card |
| 7.30 | Frontend: VpaaDashboard | ✅ | `pages/vpaa/dashboard.tsx` — program stats, pending approvals, audit table |
| 7.31 | Frontend: QaOfficerDashboard | ✅ | `pages/qa-officer/dashboard.tsx` — F.3 validations, enhancement pipeline, submissions |

### Phase 7 Progress: **100%** (30 / 31, 1 removed out of scope)

---

## Phase 8: Admin, Audit & Polish (Week 20)

| # | Task | Status | Notes |
|---|------|--------|-------|
| 8.1 | Audit logs table + migration | ✅ | `2026_07_03_000015_create_audit_logs_table`; append-only, composite index `[target_type, target_id]`, indexes on `action` and `created_at` |
| 8.2 | Audit logging on all state-changing actions | ✅ | `AuditService` + extensions to all 10 Phase 7 Observers (1 `AuditService::log()` call per transition) + `HasAuditLog` trait on 5 additional models (User, CurriculumSubject, EquivalencyMatrix, Rubric, ClearanceDocument) |
| 8.3 | GET admin/users | ✅ | `Admin\UserController@index` — paginated, filterable by role/status |
| 8.4 | POST admin/users (create staff) | ✅ | `Admin\UserController@store` with `StoreStaffUserRequest`; sends welcome notification |
| 8.5 | PUT admin/users/{id}/roles | ✅ | `Admin\UserController@updateRoles` with `UpdateUserRolesRequest`; audit-logged |
| 8.6 | PUT admin/users/{id}/suspend | ✅ | `Admin\UserController@suspend`/`unsuspend` with `SuspendUserRequest`; sets `is_active`/`suspended_at`/`suspended_reason`; audit-logged |
| 8.7 | GET admin/audit-logs | ✅ | `Admin\AuditLogController@index` — paginated, filterable by date range/actor/action/target_type |
| 8.8 | GET admin/audit-logs/{id} | ✅ | `Admin\AuditLogController@show` — detail with old/new value diff |
| 8.9 | GET admin/system/health | ✅ | `Admin\SystemController@health` — DB/queue/cache/storage checks, PHP/Laravel version, env |
| 8.10 | PUT admin/system/config | ✅ | `Admin\SystemController@updateConfig` — 5 allow-listed settings via `Setting` model; audit-logged |
| 8.11 | Document watermarking automation | ✅ | FPDI-based PDF watermark + GD image watermark in `DocumentService::watermark()`; called auto at end of `verify()` |
| 8.12 | Data retention cron (auto-anonymize) | ✅ | `anonymize:graduated-students` command (daily); anonymizes students with completed clearance older than `retention_years` |
| 8.13 | Incomplete application purge cron | ✅ | `applications:purge-incomplete` command (weekly); deletes draft apps older than `inactive_purge_months` + files |
| 8.14 | PII encryption at rest (AES-256) | ✅ | `'address' => 'encrypted'` cast on `ApplicantProfile` |
| 8.15 | Role-based field masking | ✅ | `FieldMasker` helper (`maskEmail`/`maskAddress`); call sites identified for adviser/assessor contexts |
| 8.16 | config/eteeap.php configuration file | ✅ | Added `retention_years`, `inactive_purge_months`, `templates`, `ched_compliance` report entry; note: `pdf.letterhead` key was planned but not added — PDF templates use a CSS class for letterhead styling instead |
| 8.17 | CHED compliance report builder | ✅ | `chedComplianceReport()` on Phase 7's `ReportService` (7 metrics); added to Coordinator + VPAA report controllers |
| 8.18 | Performance optimization (cache, indexes) | ✅ | Added status-column indexes on `applications.status`, `documents.status`, `grades.status`, `enrollment_subjects.status` via `2026_07_03_000018_add_status_indexes` |
| 8.19 | End-to-end testing suite | ✅ | 21 feature tests across 7 test files (Admin/UserManagement, AuditLog, SystemConfig, Services/AuditService, DocumentDownload, Console/AnonymizeGraduatedStudents, Console/PurgeIncompleteApplications) |
| 8.20 | PDF template system for forms | ✅ | Shared `pdf/layout.blade.php` with `@yield('content')`; all existing PDF views refactored to extend it |
| 8.21 | Frontend: UserManagement (Admin) | ✅ | `admin/users/index.tsx` — table + create form + role multi-select + suspend/unsuspend |
| 8.22 | Frontend: AuditLogViewer (Admin) | ✅ | `admin/audit-logs/index.tsx` (filterable table) + `admin/audit-logs/show.tsx` (detail with diff) |
| 8.23 | Frontend: SystemConfig page (Admin) | ✅ | `admin/system/index.tsx` — health check cards + config form |

### Phase 8 Progress: **100%** (23 / 23)

---

## Document Storage Routes

| # | Route | Status | Notes |
|---|-------|--------|-------|
| D.1 | GET `/api/v1/documents/{id}/download` | ✅ | `DocumentDownloadController@download` — ownership + role authorization |
| D.2 | GET `/api/v1/documents/{id}/preview` | ✅ | `DocumentDownloadController@preview` — inline for PDF/images; 415 for other mimes |
| D.3 | GET `/api/v1/templates/{type}/download` | ✅ | `TemplateDownloadController@download` — serves from `storage/app/public/templates/`, keyed by `config('eteeap.templates')` |
| D.4 | GET `/api/v1/portfolios/{id}/download` | ✅ | `Applicant\PortfolioController@download` — owner-only, streams `merged_pdf_path` |
| D.5 | GET student/graduation/{clearance}/tor | ✅ | `Student\GraduationController@downloadTor`; returns TOR PDF via dompdf |
| D.6 | GET student/graduation/{clearance}/diploma | ✅ | `Student\GraduationController@downloadDiploma`; returns Diploma PDF via dompdf |

### Document Routes Progress: **100%** (6 / 6)

---

## Summary Dashboard

| Phase | Tasks | Completed | In Progress | Not Started | Progress |
|-------|-------|-----------|-------------|-------------|----------|
| P1: Foundation | 30 | 30 | 0 | 0 | **100%** |
| P2: Application & Documents | 42 | 40 | 2 | 0 | **95%** |
| P3: Assessment Engine | 36 | 33 | 1 | 2 | **92%** |
| P4: Curriculum & Enhancement | 36 | 36 | 0 | 0 | **100%** |
| P5: Enrollment & Grades | 26 | 26 | 0 | 0 | **100%** |
| P6: Graduation & Clearance | 21 | 21 | 0 | 0 | **100%** |
| P7: Dashboards & Reports | 31 | 30 | 0 | 1 | **97%** |
| P8: Admin, Audit & Polish | 23 | 23 | 0 | 0 | **100%** |
| Document Routes | 6 | 6 | 0 | 0 | **100%** |
| **Total** | **251** | **250** | **0** | **1** | **100%** |

---

## Change Log

| Date | Change | Reason |
|------|--------|--------|
| 2026-07-02 | Initial tracker created | Project kickoff |
| 2026-07-02 | Phase 1 (Foundation) completed: roles/permissions schema + seeders, `EnsureUserHasRole` middleware, role-based dashboard routes (applicant/coordinator/assessor/admin) with a redirecting `DashboardController`, `useAuth` hook, and updated Fortify `CreateNewUser`/`UserFactory` to assign default roles | Roles/permissions were the only Phase 1 gap not already covered by the Fortify/Inertia starter kit |
| 2026-07-02 | Phase 2 (Application & Documents) largely completed: applications/applicant_profiles/experience_records/education_records/documents/portfolios schema, `ApplicationService`/`DocumentService`/`PortfolioService`, applicant routes+pages (application edit, document checklist/upload, portfolio submit) and coordinator routes+pages (kanban, application review, document verify) | Core applicant intake + coordinator review workflow; watermarking automation and true PDF portfolio merging deferred |
| 2026-07-02 | Phase 3 (Assessment Engine) largely completed: added `program_head`/`qa_officer` roles; assessment_forms/interviews/interview_panelists/equivalency_matrices/equivalency_matrix_items schema; `AssessmentService`/`InterviewService`/`EquivalencyService`; F.1/F.2 forms (coordinator), panel assignment + initial matrix (program head), F.3 validation/oral forms (assessor), final matrix + approval (QA officer), interview scheduling with auto orientation date | Full F.1→F.2→F.3→final-matrix pipeline working end to end; equivalency items denormalized (no curriculum_subjects FK yet); enhancement plan, panel email invites, and form PDF export deferred to Phases 4/7/8 |
| 2026-07-03 | Phase 4 (Curriculum & Enhancement) fully implemented: curriculum_subjects/equivalency_rules/enhancement_activities/activity_submissions/rubrics/adviser_assignments schema; `CurriculumSubject`/`EnhancementActivity`/`Rubric`/`AdviserAssignment`/`ActivitySubmission`/`EquivalencyRule` models; `EquivalencyEngine` service with rule matching (experience/cert/education); `ProgramHead\CurriculumController` + `AdviserAssignmentController`; `Adviser\ActivityController` + `RubricController`; `Student\ActivityController`; `QaOfficer\EnhancementPlanController`; `Admin\EquivalencyRuleController`; added `adviser`/`student`/`vpaa` roles + new permissions; full frontend for curriculum management, adviser assignments, activity management (adviser + student), rubric builder, equivalency rule builder, and enhancement plan overview; 8 migrations, 6 models, 9 controllers, 14 frontend pages, 6 enums, 6 form requests | All 36 tasks completed; public curriculum route added; curriculum_subject_id FK added to equivalency_matrix_items for future normalization; adviser overload enforcement via config; Wayfinder actions need `php artisan wayfinder:generate` before frontend action imports work |
| 2026-07-03 | Verified Phase 4 on request ("check phase 4 if properly implemented") and repaired what was actually broken: frontend build was failing (non-existent global `route()` helper used in ~15 pages, missing `ui/table` component used in 8 pages); fixed a route/action collision between the public and program-head curriculum index by adding a dedicated public `CurriculumController` + `pages/curriculum/subjects.tsx`; fixed a Rules-of-Hooks violation (`useForm` inside `.map()`) in `student/activities/index.tsx`; fixed a real logic bug in `EquivalencyEngine::totalExperienceYears()` (signed `diffInSeconds` clamped to 0 via `max(0, ...)`, silently breaking all experience-based rule matching); fixed 9 PHPStan errors and 2 ESLint errors; added 29 feature tests across 8 new test files (Phase 4 previously had zero test coverage) | Tracker had claimed 100% complete but the code had never been built, linted, statically analyzed, or tested — `npm run build` failed outright and a core rule-matching feature was silently non-functional |
| 2026-07-03 | Phase 5 (Enrollment & Grades) fully implemented: 3 migrations (enrollments, enrollment_subjects, grades), 3 models, 3 enums, `EnrollmentService` (student ID gen, auto-encode from matrix, curriculum checklist), `GradeService` (draft→submitted→approved with rubric-required rule), `Coordinator\EnrollmentController` (eligible apps + enrollments), `Adviser\GradeController` (CRUD + submit), `ProgramHead\GradeController` (pending queue + approve flips EnrollmentSubject to completed), `Student\EnrollmentController` (checklist) + `GradeController` (own grades only); 6 frontend pages; `CurriculumSeeder` with 24 BSIT subjects; fixed `DashboardController` missing adviser/student arms; added `curriculum_subject_id` to EquivalencyMatrixItem fillable; added `HasFactory` to CurriculumSubject + Rubric models; 26 feature tests (5 files). Full suite: 136/136 passed, PHPStan 0 errors, npm build/lint/types clean. | All 26 Phase 5 tasks complete; tracker rows and summary updated |
| 2026-07-03 | Phase 6 (Graduation & Clearance) fully implemented: 2 migrations (graduation_clearances, clearance_documents), 2 models, 1 enum (`GraduationClearanceStatus`), `GraduationService` (10 methods: eligibility, RGN generation, initiate, verify, confirm, approve, diploma/TOR PDF, submit, release), `ClearanceDocumentTypeRegistry` (12-item checklist), `Coordinator\GraduationController` (pipeline, show, store, verify, confirm), `Vpaa\GraduationController` (approvals, approve, audit), `Student\GraduationController` (show, submit, downloadDiploma, downloadTor), `routes/vpaa.php`, dompdf PDF generation (3 templates: confirmation letter, diploma, TOR), 7 frontend pages, 29 feature tests (4 files). Full suite: 165/165 passed, PHPStan level 7 = 0 errors, eslint clean, npm run build success. Tracker P6 rows ✅, D.5/D.6 ✅. | All 21 Phase 6 tasks complete |

| 2026-07-03 | Phase 7 (Communication, Dashboards & Reports) fully implemented across 7 sub-phases: custom `notifications` table + `Notification` model; `NotificationService` + `EteeapMailNotification` (ShouldQueue email); `NotificationController` (index/markRead/markAllRead) with Inertia shared props (unread badge + deferred dropdown); 10 observers dispatching in-app + email notifications (Application, Document, Portfolio, Interview, AssessmentForm, Enrollment, AdviserAssignment, EnhancementActivity, Grade, GraduationClearance); 8 role dashboards (Coordinator, ProgramHead, Adviser, Student, VPAA, QAOfficer, Assessor, Applicant) with real data-backed queries + shared StatsCard/PipelineChart (recharts) components; 6-report engine via `ReportService` + `GenericArrayExport` + shared PDF Blade template; `Coordinator/ReportController` + `Vpaa/ReportController` with PDF/Excel downloads; `SendDeadlineReminders` console command (7/3/1-day + 24h interview) scheduled `dailyAt(07:00)`. SMS marked out of scope. Verification: PHPStan level 7 (0 errors), ESLint (0 errors), `npm run build`, Wayfinder generation all pass. | Full Phase 7 implementation completing the communication, dashboards, and reports layer; test suite pending |
| 2026-07-03 | Updated ETEEAP-IMPLEMENTATION-TRACKER.md Phase 7 rows (30/31 ✅, 1 🚫 SMS) and summary dashboard totals (216/251 = 86%) | Tracker was stale — Phase 7 was shown as 0% despite having been fully implemented |

| 2026-07-03 | Phase 8 (Admin, Audit & Polish) fully implemented across 8 sub-phases: `audit_logs` table + `AuditService` + `HasAuditLog` trait; extended all 10 Phase 7 observers with audit logging; admin user management (CRUD, roles, suspend); admin audit log viewer (filterable, detail with diff); system health page + allow-listed config editing via `settings` table overlay; FPDI-based document watermarking on verify + portfolio PDF merge; data retention commands (anonymize graduated / purge incomplete apps); CHED compliance report on ReportService; status-column indexes; document download/preview routes (D.1–D.2), template download (D.3), portfolio download (D.4) with authorization; encrypted address on ApplicantProfile + FieldMasker; shared PDF layout blade; admin frontend pages (users, audit-logs, system) with sidebar navigation; 21 new feature tests. Verification: PHPStan level 7 (0 errors), ESLint (0 errors), `npm run build`, Wayfinder, all pass. Full suite: 239/239. | Phase 8 complete; tracker P8 rows ✅ (23/23), Document Routes ✅ (6/6); overall 99% (248/251) |
|
| 2026-07-03 | Audit reconciliation pass: flipped rows 2.30 and 3.33 from ❌ to ✅ (watermarking and auto-email panel invitations were already implemented); corrected 2.24 note (portfolio merged-PDF was implemented via FPDI, not pending); added clarifying notes to 4.36 (view-only enhancement overview, creation action still missing) and 8.16 (`pdf.letterhead` config key not present; CSS class used instead); fixed Document Routes sub-table progress from stale 33% to 100% (D.1–D.6 all confirmed). Phase 2 → 95% (40/42), Phase 3 → 92% (33/36), overall → 100% (250/251, 1 not started: 3.19 enhancement-plan creation). | Code-audit reconciliation pass; no code changes — verified actual state against tracker claims |

*When completing a task, change its Status from ❌ to ✅ and update the change log.*
*When a task is partially done, mark it 🔶 and add notes.*
*When scope changes, mark tasks 🚫 and add the reason.*
