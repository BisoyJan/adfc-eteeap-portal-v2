# ADFC ETEEAP Portal — Implementation Tracker

> **Purpose:** Track what has been implemented, what is in progress, and what remains. Update this file as the project evolves.
>
> **Tech Stack:** Laravel 13 + React 19 (Inertia.js) + Laravel Fortify + MySQL 8
>
> **Last Updated:** 2026-07-02
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
| 2.1 | Applications table + migration | ❌ | |
| 2.2 | Applicant profiles table + migration | ❌ | |
| 2.3 | Experience records table + migration | ❌ | |
| 2.4 | Education records table + migration | ❌ | |
| 2.5 | Documents table + migration | ❌ | |
| 2.6 | Portfolios table + migration | ❌ | |
| 2.7 | Portfolio documents pivot table | ❌ | |
| 2.8 | Application model + relationships | ❌ | |
| 2.9 | Document model + relationships | ❌ | |
| 2.10 | Portfolio model + relationships | ❌ | |
| 2.11 | POST `/api/v1/applications` | ❌ | |
| 2.12 | GET `/api/v1/applications/{id}` | ❌ | |
| 2.13 | PUT `/api/v1/applications/{id}` | ❌ | |
| 2.14 | GET `/api/v1/applications/{id}/status` | ❌ | |
| 2.15 | POST `/api/v1/applications/{id}/documents` | ❌ | |
| 2.16 | GET `/api/v1/applications/{id}/documents` | ❌ | |
| 2.17 | DELETE `/api/v1/documents/{id}` | ❌ | |
| 2.18 | POST `/api/v1/experience-records` | ❌ | |
| 2.19 | PUT `/api/v1/experience-records/{id}` | ❌ | |
| 2.20 | DELETE `/api/v1/experience-records/{id}` | ❌ | |
| 2.21 | POST `/api/v1/education-records` | ❌ | |
| 2.22 | PUT `/api/v1/education-records/{id}` | ❌ | |
| 2.23 | DELETE `/api/v1/education-records/{id}` | ❌ | |
| 2.24 | POST `/api/v1/applications/{id}/portfolio/submit` | ❌ | |
| 2.25 | GET `/api/v1/applications/{id}/portfolio` | ❌ | |
| 2.26 | Coordinator: GET applications list | ❌ | |
| 2.27 | Coordinator: GET applications kanban | ❌ | |
| 2.28 | Coordinator: PUT review application | ❌ | |
| 2.29 | Coordinator: PUT verify document | ❌ | |
| 2.30 | Coordinator: POST watermark document | ❌ | |
| 2.31 | ApplicationService (qualification checks) | ❌ | |
| 2.32 | DocumentService (upload, verify, watermark) | ❌ | |
| 2.33 | Frontend: Multi-step ApplicationForm | ❌ | |
| 2.34 | Frontend: DocumentUpload component | ❌ | |
| 2.35 | Frontend: Document checklist with status | ❌ | |
| 2.36 | Frontend: PortfolioBuilder | ❌ | |
| 2.37 | Frontend: Coordinator KanbanBoard | ❌ | |
| 2.38 | Frontend: ApplicationReview page | ❌ | |
| 2.39 | Frontend: DocumentVerification page | ❌ | |
| 2.40 | Qualification auto-checks (age, experience, etc.) | ❌ | |
| 2.41 | Document type validation rules | ❌ | |
| 2.42 | 18 document types checklist config | ❌ | |

### Phase 2 Progress: **0%** (0 / 42)

---

## Phase 3: Assessment Engine (Weeks 7–9)

| # | Task | Status | Notes |
|---|------|--------|-------|
| 3.1 | Assessment forms table + migration | ❌ | |
| 3.2 | Assessment form items (dynamic criteria) | ❌ | |
| 3.3 | Assessment results table | ❌ | |
| 3.4 | Equivalency matrices table + migration | ❌ | |
| 3.5 | Equivalency matrix items table | ❌ | |
| 3.6 | F.1 assessment form endpoint | ❌ | |
| 3.7 | F.2 assessment form endpoint | ❌ | |
| 3.8 | F.3 validation tool endpoint | ❌ | |
| 3.9 | F.3 oral competency form endpoint | ❌ | |
| 3.10 | POST coordinator/assessments/first | ❌ | |
| 3.11 | POST coordinator/assessments/second | ❌ | |
| 3.12 | POST coordinator/interviews (schedule) | ❌ | |
| 3.13 | GET coordinator/interviews (list) | ❌ | |
| 3.14 | POST program-head/equivalency (initial matrix) | ❌ | |
| 3.15 | PUT program-head/equivalency/items | ❌ | |
| 3.16 | POST program-head/assessors (assign panel) | ❌ | |
| 3.17 | GET program-head/assessors/available | ❌ | |
| 3.18 | POST qa-officer/validations/final-matrix | ❌ | |
| 3.19 | POST qa-officer/enhancement-plan | ❌ | |
| 3.20 | POST assessor/interviews/{id}/validate | ❌ | |
| 3.21 | POST assessor/interviews/{id}/oral | ❌ | |
| 3.22 | GET assessor/interviews | ❌ | |
| 3.23 | AssessmentService | ❌ | |
| 3.24 | Interview scheduling logic | ❌ | |
| 3.25 | Panel composition validation (min 3) | ❌ | |
| 3.26 | Frontend: AssessmentFormBuilder (dynamic) | ❌ | |
| 3.27 | Frontend: EquivalencyMatrixEditor | ❌ | |
| 3.28 | Frontend: InterviewScheduler | ❌ | |
| 3.29 | Frontend: PanelManagement (Program Head) | ❌ | |
| 3.30 | Frontend: ValidationReview (QA Officer) | ❌ | |
| 3.31 | Frontend: FinalMatrixEditor (QA Officer) | ❌ | |
| 3.32 | Frontend: Assessor Interview page | ❌ | |
| 3.33 | Auto-email panel invitations | ❌ | |
| 3.34 | Auto-orientation scheduling (2 days prior) | ❌ | |
| 3.35 | Digital signature on assessment forms | ❌ | |
| 3.36 | Assessment form PDF export | ❌ | |

### Phase 3 Progress: **0%** (0 / 36)

---

## Phase 4: Curriculum & Enhancement (Weeks 10–12)

| # | Task | Status | Notes |
|---|------|--------|-------|
| 4.1 | Curriculum subjects table + migration | ❌ | |
| 4.2 | Curriculum versioning (year/CMO snapshots) | ❌ | |
| 4.3 | Equivalency rules table + migration | ❌ | |
| 4.4 | Enhancement activities table | ❌ | |
| 4.5 | Activity submissions table | ❌ | |
| 4.6 | Rubrics table + migration | ❌ | |
| 4.7 | Rubric criteria JSON structure | ❌ | |
| 4.8 | Adviser assignments table | ❌ | |
| 4.9 | CurriculumSubject model + relationships | ❌ | |
| 4.10 | EnhancementActivity model | ❌ | |
| 4.11 | Rubric model | ❌ | |
| 4.12 | GET curriculum/subjects (public) | ❌ | |
| 4.13 | POST program-head/curriculum/subjects | ❌ | |
| 4.14 | PUT program-head/curriculum/subjects/{id} | ❌ | |
| 4.15 | POST program-head/curriculum/version | ❌ | |
| 4.16 | POST program-head/adviser-assignments | ❌ | |
| 4.17 | GET program-head/adviser-assignments | ❌ | |
| 4.18 | GET program-head/adviser-workload | ❌ | |
| 4.19 | POST adviser/activities (assign) | ❌ | |
| 4.20 | PUT adviser/activities/{id} (edit) | ❌ | |
| 4.21 | PUT adviser/activities/{id}/score | ❌ | |
| 4.22 | GET adviser/activities/submissions | ❌ | |
| 4.23 | POST adviser/rubrics | ❌ | |
| 4.24 | GET adviser/rubrics | ❌ | |
| 4.25 | POST student/activities/{id}/submit | ❌ | |
| 4.26 | GET student/activities | ❌ | |
| 4.27 | EquivalencyEngine service | ❌ | |
| 4.28 | Rule matching logic (experience, cert, education) | ❌ | |
| 4.29 | Adviser overload enforcement (max 3 + 3) | ❌ | |
| 4.30 | Frontend: CurriculumManagement page | ❌ | |
| 4.31 | Frontend: EquivalencyRuleBuilder (admin) | ❌ | |
| 4.32 | Frontend: AdviserAssignmentPage | ❌ | |
| 4.33 | Frontend: ActivityManagement (Adviser) | ❌ | |
| 4.34 | Frontend: ActivityList (Student) | ❌ | |
| 4.35 | Frontend: RubricBuilder | ❌ | |
| 4.36 | Frontend: EnhancementPlanPage (QA Officer) | ❌ | |

### Phase 4 Progress: **0%** (0 / 36)

---

## Phase 5: Enrollment & Grades (Weeks 13–15)

| # | Task | Status | Notes |
|---|------|--------|-------|
| 5.1 | Enrollments table + migration | ❌ | |
| 5.2 | Enrollment subjects pivot table | ❌ | |
| 5.3 | Grades table + migration | ❌ | |
| 5.4 | Enrollment model | ❌ | |
| 5.5 | Grade model | ❌ | |
| 5.6 | Student ID auto-generation (ETEEAP-YYYY-NNNN) | ❌ | |
| 5.7 | POST coordinator/enrollments | ❌ | |
| 5.8 | GET coordinator/enrollments | ❌ | |
| 5.9 | GET student/curriculum-checklist | ❌ | |
| 5.10 | POST adviser/grades | ❌ | |
| 5.11 | PUT adviser/grades/{id} (draft only) | ❌ | |
| 5.12 | POST adviser/grades/{id}/submit | ❌ | |
| 5.13 | GET program-head/grades/pending | ❌ | |
| 5.14 | PUT program-head/grades/{id}/approve | ❌ | |
| 5.15 | GET student/grades | ❌ | |
| 5.16 | GradeService | ❌ | |
| 5.17 | Enrollment auto-computation from matrix | ❌ | |
| 5.18 | Curriculum checklist generation | ❌ | |
| 5.19 | Grade approval chain logic | ❌ | |
| 5.20 | CurriculumSeeder with BSIT subjects | ❌ | |
| 5.21 | Frontend: EnrollmentForm (auto-populated) | ❌ | |
| 5.22 | Frontend: EnrollmentProcessing (Coordinator) | ❌ | |
| 5.23 | Frontend: CurriculumChecklist (Student) | ❌ | |
| 5.24 | Frontend: GradeEntryForm (Adviser) | ❌ | |
| 5.25 | Frontend: GradeApprovalPage (Program Head) | ❌ | |
| 5.26 | Frontend: GradeViewer (Student) | ❌ | |

### Phase 5 Progress: **0%** (0 / 26)

---

## Phase 6: Graduation & Clearance (Weeks 16–17)

| # | Task | Status | Notes |
|---|------|--------|-------|
| 6.1 | Graduation clearances table + migration | ❌ | |
| 6.2 | Clearance documents table | ❌ | |
| 6.3 | GraduationClearance model | ❌ | |
| 6.4 | POST coordinator/graduation/confirm | ❌ | |
| 6.5 | GET coordinator/graduation/pipeline | ❌ | |
| 6.6 | GET vpaa/approvals | ❌ | |
| 6.7 | PUT vpaa/approvals/{id}/approve | ❌ | |
| 6.8 | GET vpaa/graduation-audit | ❌ | |
| 6.9 | GET student/graduation | ❌ | |
| 6.10 | POST student/clearance/submit | ❌ | |
| 6.11 | Registry of Graduate Number auto-assignment | ❌ | |
| 6.12 | Confirmation letter PDF generation | ❌ | |
| 6.13 | Diploma PDF generation | ❌ | |
| 6.14 | TOR PDF generation from accumulated records | ❌ | |
| 6.15 | 12-item clearance document checklist | ❌ | |
| 6.16 | Clearance doc verification workflow | ❌ | |
| 6.17 | Frontend: GraduationPage (Coordinator) | ❌ | |
| 6.18 | Frontend: ApprovalQueue (VPAA) | ❌ | |
| 6.19 | Frontend: GraduationAudit (VPAA) | ❌ | |
| 6.20 | Frontend: GraduationStatus (Student) | ❌ | |
| 6.21 | Frontend: ClearanceProcess (Student) | ❌ | |

### Phase 6 Progress: **0%** (0 / 21)

---

## Phase 7: Communication, Dashboards & Reports (Weeks 18–19)

| # | Task | Status | Notes |
|---|------|--------|-------|
| 7.1 | Notifications table + migration | ❌ | |
| 7.2 | Notification model | ❌ | |
| 7.3 | GET /notifications | ❌ | |
| 7.4 | PUT /notifications/{id}/read | ❌ | |
| 7.5 | PUT /notifications/read-all | ❌ | |
| 7.6 | NotificationService | ❌ | |
| 7.7 | Email notification templates | ❌ | |
| 7.8 | SMS notification integration | ❌ | |
| 7.9 | Notification dispatch on all triggers | ❌ | |
| 7.10 | GET dashboard/coordinator | ❌ | |
| 7.11 | GET dashboard/program-head | ❌ | |
| 7.12 | GET dashboard/adviser | ❌ | |
| 7.13 | GET dashboard/student | ❌ | |
| 7.14 | GET dashboard/vpaa | ❌ | |
| 7.15 | GET dashboard/qa-officer | ❌ | |
| 7.16 | GET coordinator/reports/{type} | ❌ | |
| 7.17 | GET vpaa/reports/{type} | ❌ | |
| 7.18 | ReportService | ❌ | |
| 7.19 | Report PDF generation | ❌ | |
| 7.20 | Report Excel generation | ❌ | |
| 7.21 | Frontend: DashboardWidgetGrid | ❌ | |
| 7.22 | Frontend: StatsCard, PipelineChart, Calendar | ❌ | |
| 7.23 | Frontend: ActivityFeed component | ❌ | |
| 7.24 | Frontend: Notification center (bell + dropdown) | ❌ | |
| 7.25 | Frontend: ReportViewer with export | ❌ | |
| 7.26 | Frontend: CoordinatorDashboard | ❌ | |
| 7.27 | Frontend: ProgramHeadDashboard | ❌ | |
| 7.28 | Frontend: AdviserDashboard | ❌ | |
| 7.29 | Frontend: StudentDashboard | ❌ | |
| 7.30 | Frontend: VpaaDashboard | ❌ | |
| 7.31 | Frontend: QaOfficerDashboard | ❌ | |

### Phase 7 Progress: **0%** (0 / 31)

---

## Phase 8: Admin, Audit & Polish (Week 20)

| # | Task | Status | Notes |
|---|------|--------|-------|
| 8.1 | Audit logs table + migration | ❌ | |
| 8.2 | Audit logging on all state-changing actions | ❌ | |
| 8.3 | GET admin/users | ❌ | |
| 8.4 | POST admin/users (create staff) | ❌ | |
| 8.5 | PUT admin/users/{id}/roles | ❌ | |
| 8.6 | PUT admin/users/{id}/suspend | ❌ | |
| 8.7 | GET admin/audit-logs | ❌ | |
| 8.8 | GET admin/audit-logs/{id} | ❌ | |
| 8.9 | GET admin/system/health | ❌ | |
| 8.10 | PUT admin/system/config | ❌ | |
| 8.11 | Document watermarking automation | ❌ | |
| 8.12 | Data retention cron (auto-anonymize) | ❌ | |
| 8.13 | Incomplete application purge cron | ❌ | |
| 8.14 | PII encryption at rest (AES-256) | ❌ | |
| 8.15 | Role-based field masking | ❌ | |
| 8.16 | config/eteeap.php configuration file | ❌ | |
| 8.17 | CHED compliance report builder | ❌ | |
| 8.18 | Performance optimization (cache, indexes) | ❌ | |
| 8.19 | End-to-end testing suite | ❌ | |
| 8.20 | PDF template system for forms | ❌ | |
| 8.21 | Frontend: UserManagement (Admin) | ❌ | |
| 8.22 | Frontend: AuditLogViewer (Admin) | ❌ | |
| 8.23 | Frontend: SystemConfig page (Admin) | ❌ | |

### Phase 8 Progress: **0%** (0 / 23)

---

## Document Storage Routes

| # | Route | Status | Notes |
|---|-------|--------|-------|
| D.1 | GET `/api/v1/documents/{id}/download` | ❌ | |
| D.2 | GET `/api/v1/documents/{id}/preview` | ❌ | |
| D.3 | GET `/api/v1/templates/{type}/download` | ❌ | |
| D.4 | GET `/api/v1/portfolios/{id}/download` | ❌ | |
| D.5 | GET `/api/v1/grades/{id}/tor-preview` | ❌ | |
| D.6 | GET `/api/v1/graduation/{id}/diploma-preview` | ❌ | |

### Document Routes Progress: **0%** (0 / 6)

---

## Summary Dashboard

| Phase | Tasks | Completed | In Progress | Not Started | Progress |
|-------|-------|-----------|-------------|-------------|----------|
| P1: Foundation | 30 | 30 | 0 | 0 | **100%** |
| P2: Application & Documents | 42 | 0 | 0 | 42 | **0%** |
| P3: Assessment Engine | 36 | 0 | 0 | 36 | **0%** |
| P4: Curriculum & Enhancement | 36 | 0 | 0 | 36 | **0%** |
| P5: Enrollment & Grades | 26 | 0 | 0 | 26 | **0%** |
| P6: Graduation & Clearance | 21 | 0 | 0 | 21 | **0%** |
| P7: Dashboards & Reports | 31 | 0 | 0 | 31 | **0%** |
| P8: Admin, Audit & Polish | 23 | 0 | 0 | 23 | **0%** |
| Document Routes | 6 | 0 | 0 | 6 | **0%** |
| **Total** | **251** | **30** | **0** | **221** | **12%** |

---

## Change Log

| Date | Change | Reason |
|------|--------|--------|
| 2026-07-02 | Initial tracker created | Project kickoff |
| 2026-07-02 | Phase 1 (Foundation) completed: roles/permissions schema + seeders, `EnsureUserHasRole` middleware, role-based dashboard routes (applicant/coordinator/assessor/admin) with a redirecting `DashboardController`, `useAuth` hook, and updated Fortify `CreateNewUser`/`UserFactory` to assign default roles | Roles/permissions were the only Phase 1 gap not already covered by the Fortify/Inertia starter kit |

---

*When completing a task, change its Status from ❌ to ✅ and update the change log.*
*When a task is partially done, mark it 🔶 and add notes.*
*When scope changes, mark tasks 🚫 and add the reason.*
