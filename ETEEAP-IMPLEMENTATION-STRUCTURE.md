# ADFC ETEEAP Portal — System Implementation Structure

> **Tech Stack:** Laravel 13 (PHP) + React 19 (Inertia.js) + MySQL 8
> **Drive:** RESTful API with Inertia server-side rendering
> **Auth:** Laravel Sanctum (SPA) + OTP email verification
> **Storage:** Laravel Filesystem (local + S3-compatible for document storage)

---

## Table of Contents

1. [Project Structure](#1-project-structure)
2. [Database Schema](#2-database-schema)
3. [API Route Design](#3-api-route-design)
4. [React Component Tree](#4-react-component-tree)
5. [Data Flow Diagrams](#5-data-flow-diagrams)
6. [Implementation Phases](#6-implementation-phases)

---

## 1. Project Structure

### 1.1 Laravel Backend Directory Tree

```
adfc-eteeap-portal/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── UserController.php
│   │   │   │   ├── ApplicationController.php
│   │   │   │   ├── DocumentController.php
│   │   │   │   ├── PortfolioController.php
│   │   │   │   ├── AssessmentController.php
│   │   │   │   ├── EquivalencyController.php
│   │   │   │   ├── EnrollmentController.php
│   │   │   │   ├── EnhancementController.php
│   │   │   │   ├── GradeController.php
│   │   │   │   ├── GraduationController.php
│   │   │   │   ├── NotificationController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   └── ReportController.php
│   │   ├── Middleware/
│   │   │   ├── RoleMiddleware.php
│   │   │   └── EnsureEmailVerified.php
│   │   └── Requests/
│   │       ├── StoreApplicationRequest.php
│   │       ├── StoreDocumentRequest.php
│   │       ├── SubmitAssessmentRequest.php
│   │       └── ...
│   ├── Models/
│   │   ├── User.php
│   │   ├── Role.php
│   │   ├── Permission.php
│   │   ├── Application.php
│   │   ├── Document.php
│   │   ├── Portfolio.php
│   │   ├── AssessmentForm.php
│   │   ├── EquivalencyMatrix.php
│   │   ├── CurriculumSubject.php
│   │   ├── Enrollment.php
│   │   ├── EnhancementActivity.php
│   │   ├── Grade.php
│   │   ├── Rubric.php
│   │   ├── GraduationClearance.php
│   │   └── Notification.php
│   ├── Services/
│   │   ├── ApplicationService.php
│   │   ├── AssessmentService.php
│   │   ├── EquivalencyEngine.php
│   │   ├── DocumentService.php
│   │   ├── NotificationService.php
│   │   ├── GradeService.php
│   │   └── ReportService.php
│   └── Enums/
│       ├── ApplicationStatus.php
│       ├── DocumentStatus.php
│       ├── AssessmentStage.php
│       └── SubjectStatus.php
├── database/
│   ├── migrations/
│   │   ├── 0001_create_users_table.php
│   │   ├── 0002_create_roles_permissions_tables.php
│   │   ├── 0003_create_applications_table.php
│   │   ├── 0004_create_documents_table.php
│   │   ├── 0005_create_portfolios_table.php
│   │   ├── 0006_create_assessment_forms_table.php
│   │   ├── 0007_create_equivalency_matrices_table.php
│   │   ├── 0008_create_curriculum_subjects_table.php
│   │   ├── 0009_create_enrollments_table.php
│   │   ├── 0010_create_enhancement_activities_table.php
│   │   ├── 0011_create_grades_table.php
│   │   ├── 0012_create_rubrics_table.php
│   │   ├── 0013_create_graduation_clearances_table.php
│   │   ├── 0014_create_notifications_table.php
│   │   └── 0015_create_audit_logs_table.php
│   └── seeders/
│       ├── RolePermissionSeeder.php
│       ├── CurriculumSeeder.php
│       └── AdminUserSeeder.php
├── routes/
│   ├── api.php              # RESTful API routes
│   ├── web.php              # Inertia page routes
│   └── channels.php         # WebSocket broadcasting (optional)
├── resources/
│   └── js/
│       ├── Components/      # Shared React components
│       ├── Pages/           # Inertia page components
│       ├── Layouts/         # Layout wrappers
│       ├── Hooks/           # Custom React hooks
│       ├── Utils/           # Helpers
│       ├── Types/           # TypeScript definitions
│       └── app.jsx          # Entry point
├── storage/
│   └── app/
│       └── documents/       # Document storage (local)
└── config/
    ├── eteeap.php           # ETEEAP-specific configuration
    └── permissions.php      # RBAC permission map
```

### 1.2 React Frontend Directory Tree

```
resources/js/
├── Components/
│   ├── common/
│   │   ├── Button.jsx
│   │   ├── Modal.jsx
│   │   ├── Table.jsx
│   │   ├── Card.jsx
│   │   ├── Badge.jsx
│   │   ├── Dropdown.jsx
│   │   ├── Pagination.jsx
│   │   ├── FileUpload.jsx
│   │   ├── ProgressBar.jsx
│   │   ├── SearchInput.jsx
│   │   └── ConfirmDialog.jsx
│   ├── forms/
│   │   ├── ApplicationForm.jsx
│   │   ├── DocumentUpload.jsx
│   │   ├── AssessmentForm.jsx
│   │   ├── EquivalencyMatrixForm.jsx
│   │   ├── EnrollmentForm.jsx
│   │   ├── GradeEntryForm.jsx
│   │   └── ClearanceForm.jsx
│   ├── dashboard/
│   │   ├── StatsCard.jsx
│   │   ├── PipelineChart.jsx
│   │   ├── ActivityFeed.jsx
│   │   └── CalendarWidget.jsx
│   ├── kanban/
│   │   ├── KanbanBoard.jsx
│   │   ├── KanbanColumn.jsx
│   │   └── KanbanCard.jsx
│   └── layout/
│       ├── Sidebar.jsx
│       ├── Navbar.jsx
│       ├── Breadcrumb.jsx
│       └── Footer.jsx
├── Pages/
│   ├── Auth/
│   │   ├── Login.jsx
│   │   ├── Register.jsx
│   │   ├── VerifyEmail.jsx
│   │   ├── ForgotPassword.jsx
│   │   └── ResetPassword.jsx
│   ├── Applicant/
│   │   ├── Dashboard.jsx
│   │   ├── Application.jsx
│   │   ├── Documents.jsx
│   │   ├── Portfolio.jsx
│   │   ├── AssessmentStatus.jsx
│   │   └── Profile.jsx
│   ├── Student/
│   │   ├── Dashboard.jsx
│   │   ├── CurriculumChecklist.jsx
│   │   ├── EnhancementActivities.jsx
│   │   ├── Grades.jsx
│   │   ├── Adviser.jsx
│   │   ├── Graduation.jsx
│   │   └── Clearance.jsx
│   ├── Coordinator/
│   │   ├── Dashboard.jsx
│   │   ├── ApplicationReview.jsx
│   │   ├── ApplicantKanban.jsx
│   │   ├── DocumentVerification.jsx
│   │   ├── AssessmentManagement.jsx
│   │   ├── EnrollmentProcessing.jsx
│   │   ├── GraduationProcessing.jsx
│   │   └── Reports.jsx
│   ├── ProgramHead/
│   │   ├── Dashboard.jsx
│   │   ├── EquivalencyEvaluation.jsx
│   │   ├── PanelManagement.jsx
│   │   ├── AdviserAssignment.jsx
│   │   ├── GradeApproval.jsx
│   │   └── CurriculumManagement.jsx
│   ├── Adviser/
│   │   ├── Dashboard.jsx
│   │   ├── AdviseeList.jsx
│   │   ├── ActivityManagement.jsx
│   │   ├── GradeEntry.jsx
│   │   └── RubricBuilder.jsx
│   ├── QAOfficer/
│   │   ├── Dashboard.jsx
│   │   ├── ValidationReview.jsx
│   │   ├── FinalMatrix.jsx
│   │   └── EnhancementPlan.jsx
│   ├── Assessor/
│   │   ├── Dashboard.jsx
│   │   ├── AssignedInterviews.jsx
│   │   └── AssessmentForm.jsx
│   └── Vpaa/
│       ├── Dashboard.jsx
│       ├── PendingApprovals.jsx
│       ├── GraduationAudit.jsx
│       └── Reports.jsx
├── Layouts/
│   ├── AppLayout.jsx        # Authenticated layout
│   ├── GuestLayout.jsx      # Unauthenticated layout
│   ├── CoordinatorLayout.jsx
│   ├── StudentLayout.jsx
│   └── AssessorLayout.jsx
├── Hooks/
│   ├── useAuth.js
│   ├── usePermissions.js
│   ├── useNotifications.js
│   └── useDocumentUpload.js
├── Utils/
│   ├── axios.js             # Axios instance with interceptors
│   ├── formatters.js        # Date, currency, status formatters
│   └── validators.js        # Client-side validation rules
└── Types/
    └── index.d.ts           # TypeScript type definitions
```

---

## 2. Database Schema

### 2.1 Entity-Relationship Overview

```
roles ──┐
         ├── role_user ── users ── applicant_profiles ── applications ── documents
permissions ──┘                                               │
                                                   experience_records
                                                   education_records

applications ── assessment_forms ── assessment_results
applications ── equivalency_matrices ── equivalency_matrix_items
applications ── portfolios ── portfolio_documents

users ── enrollments ── enrollment_subjects ── curriculum_subjects
users ── adviser_assignments
users ── enhancement_activities ── activity_submissions
users ── grades ── rubric_scores ── rubrics
users ── graduation_clearances ── clearance_documents
users ── notifications
users ── audit_logs
```

### 2.2 Table Definitions

#### `users`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| email | VARCHAR(255) | UNIQUE, NOT NULL | Login credential |
| password | VARCHAR(255) | NOT NULL | Bcrypt hash |
| first_name | VARCHAR(100) | NOT NULL | |
| middle_name | VARCHAR(100) | NULLABLE | |
| last_name | VARCHAR(100) | NOT NULL | |
| suffix | VARCHAR(20) | NULLABLE | Jr., III, etc. |
| phone | VARCHAR(20) | NULLABLE | |
| email_verified_at | TIMESTAMP | NULLABLE | Email verification timestamp |
| is_active | BOOLEAN | DEFAULT true | |
| last_login_at | TIMESTAMP | NULLABLE | |
| remember_token | VARCHAR(100) | NULLABLE | |
| timestamps | — | — | created_at, updated_at |

#### `roles`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK | |
| name | VARCHAR(50) | UNIQUE | slug: `coordinator`, `program_head`, `adviser`, `assessor_internal`, `assessor_external`, `qa_officer`, `applicant`, `student`, `vpaa`, `admin` |
| display_name | VARCHAR(100) | NOT NULL | Human-readable |
| description | TEXT | NULLABLE | |
| timestamps | — | — | |

#### `permissions`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK | |
| name | VARCHAR(100) | UNIQUE | e.g., `applications.review` |
| display_name | VARCHAR(100) | | |
| group | VARCHAR(50) | | Grouping: `applications`, `assessments`, `enrollments`, `grades`, etc. |
| timestamps | — | — | |

#### `role_permission`

Pivot: `role_id` FK → roles, `permission_id` FK → permissions

#### `role_user`

Pivot: `role_id` FK → roles, `user_id` FK → users

#### `applications`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK | |
| user_id | BIGINT UNSIGNED | FK → users | Applicant |
| application_number | VARCHAR(20) | UNIQUE | Auto-generated: `APP-{YYYY}-{NNNN}` |
| status | ENUM | NOT NULL | `draft`, `submitted`, `under_review`, `approved`, `rejected`, `revision_requested` |
| submitted_at | TIMESTAMP | NULLABLE | |
| reviewed_by | BIGINT UNSIGNED | FK → users, NULLABLE | Coordinator |
| review_notes | TEXT | NULLABLE | |
| rejection_reason | TEXT | NULLABLE | |
| timestamps | — | — | |

#### `applicant_profiles`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK | |
| user_id | BIGINT UNSIGNED | FK → users, UNIQUE | |
| birth_date | DATE | NOT NULL | |
| citizenship | VARCHAR(50) | DEFAULT 'Filipino' | |
| civil_status | ENUM | | `single`, `married`, `widowed`, `separated` |
| gender | ENUM | | `male`, `female`, `prefer_not_to_say` |
| address | TEXT | NULLABLE | |
| highest_education | VARCHAR(100) | | e.g., `High School Graduate`, `College Undergraduate`, `College Graduate` |
| last_school_attended | VARCHAR(200) | | |
| timestamps | — | — | |

#### `experience_records`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK | |
| user_id | BIGINT UNSIGNED | FK → users | |
| company | VARCHAR(200) | NOT NULL | |
| position | VARCHAR(200) | NOT NULL | |
| start_date | DATE | NOT NULL | |
| end_date | DATE | NULLABLE | NULL = present |
| is_current | BOOLEAN | DEFAULT false | |
| description | TEXT | NULLABLE | Job description / responsibilities |
| bsit_aligned | BOOLEAN | DEFAULT false | Toggle for relevance check |
| timestamps | — | — | |

#### `education_records`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK | |
| user_id | BIGINT UNSIGNED | FK → users | |
| school_name | VARCHAR(200) | NOT NULL | |
| degree | VARCHAR(200) | NULLABLE | |
| field_of_study | VARCHAR(200) | NULLABLE | |
| start_year | YEAR | NOT NULL | |
| end_year | YEAR | NULLABLE | |
| is_graduated | BOOLEAN | DEFAULT false | |
| education_level | ENUM | | `elementary`, `high_school`, `vocational`, `college`, `graduate` |
| timestamps | — | — | |

#### `documents`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK | |
| user_id | BIGINT UNSIGNED | FK → users | |
| application_id | BIGINT UNSIGNED | FK → applications, NULLABLE | |
| document_type | VARCHAR(50) | NOT NULL | e.g., `application_letter`, `ched_form`, `cv`, `coe`, `tor`, `psa_birth` |
| file_path | VARCHAR(500) | NOT NULL | Storage path |
| file_name | VARCHAR(255) | NOT NULL | Original filename |
| file_size | INT | | In bytes |
| mime_type | VARCHAR(100) | | |
| version | INT | DEFAULT 1 | |
| status | ENUM | | `uploaded`, `verified`, `rejected` |
| verified_by | BIGINT UNSIGNED | FK → users, NULLABLE | |
| verified_at | TIMESTAMP | NULLABLE | |
| rejection_reason | TEXT | NULLABLE | |
| is_watermarked | BOOLEAN | DEFAULT false | |
| timestamps | — | — | |

#### `portfolios`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK | |
| application_id | BIGINT UNSIGNED | FK → applications | |
| status | ENUM | | `building`, `complete`, `submitted` |
| merged_pdf_path | VARCHAR(500) | NULLABLE | Auto-generated merged PDF |
| submitted_at | TIMESTAMP | NULLABLE | |
| timestamps | — | — | |

#### `portfolio_documents`

Pivot linking portfolios → documents with ordering.

#### `assessment_forms`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK | |
| application_id | BIGINT UNSIGNED | FK → applications | |
| stage | ENUM | | `first`, `second`, `third_validation`, `third_oral` |
| assessor_id | BIGINT UNSIGNED | FK → users | Who filled the form |
| form_data | JSON | NOT NULL | Dynamic criteria → scores |
| total_score | DECIMAL(5,2) | NULLABLE | Auto-calculated |
| verdict | ENUM | | `pass`, `fail`, `conditional` |
| digital_signature | VARCHAR(255) | | Typed name + timestamp |
| submitted_at | TIMESTAMP | NOT NULL | |
| timestamps | — | — | |

#### `equivalency_matrices`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK | |
| application_id | BIGINT UNSIGNED | FK → applications | |
| prepared_by | BIGINT UNSIGNED | FK → users | Program Head initially, QA Officer final |
| type | ENUM | | `initial`, `final` |
| status | ENUM | | `draft`, `submitted`, `approved` |
| approved_by | BIGINT UNSIGNED | FK → users, NULLABLE | |
| approved_at | TIMESTAMP | NULLABLE | |
| timestamps | — | — | |

#### `equivalency_matrix_items`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK | |
| equivalency_matrix_id | BIGINT UNSIGNED | FK → equivalency_matrices | |
| curriculum_subject_id | BIGINT UNSIGNED | FK → curriculum_subjects | |
| competency_level | ENUM | | `full`, `partial`, `none` |
| evidence_source | TEXT | NULLABLE | Description of basis |
| equivalent_credits | DECIMAL(3,1) | DEFAULT 0 | |
| recommended_action | ENUM | | `credit`, `enhance`, `enroll` |
| notes | TEXT | NULLABLE | |

#### `curriculum_subjects`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK | |
| code | VARCHAR(20) | UNIQUE | e.g., `CC101` |
| title | VARCHAR(200) | NOT NULL | |
| units | DECIMAL(3,1) | NOT NULL | |
| prerequisite_codes | JSON | NULLABLE | Array of subject codes |
| category | ENUM | | `core`, `major`, `ge`, `elective`, `pe`, `nstp` |
| year_level | TINYINT | | 1–4 |
| semester | TINYINT | | 1, 2, 0 (summer) |
| learning_outcomes | JSON | NULLABLE | Array of outcome statements |
| is_active | BOOLEAN | DEFAULT true | |
| curriculum_version | VARCHAR(20) | | e.g., `CMO-54-2016` |
| timestamps | — | — | |

#### `enrollments`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK | |
| student_id | BIGINT UNSIGNED | FK → users | Role must be `student` |
| academic_term | VARCHAR(20) | | e.g., `1st Sem 2025-2026` |
| enrollment_date | DATE | NOT NULL | |
| student_id_number | VARCHAR(20) | UNIQUE | `ETEEAP-{YYYY}-{NNNN}` |
| status | ENUM | | `pending`, `enrolled`, `completed`, `withdrawn` |
| enrollment_form_path | VARCHAR(500) | NULLABLE | Signed PDF |
| timestamps | — | — | |

#### `enrollment_subjects`

Pivot: enrollment_id, curriculum_subject_id, status (`credited`, `transferred`, `enrolled`, `completed`)

#### `adviser_assignments`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK | |
| student_id | BIGINT UNSIGNED | FK → users | |
| adviser_id | BIGINT UNSIGNED | FK → users | |
| term | VARCHAR(20) | | |
| assigned_by | BIGINT UNSIGNED | FK → users | Program Head |
| assigned_at | TIMESTAMP | | |
| is_active | BOOLEAN | DEFAULT true | |
| timestamps | — | — | |

#### `enhancement_activities`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK | |
| student_id | BIGINT UNSIGNED | FK → users | |
| adviser_id | BIGINT UNSIGNED | FK → users | |
| curriculum_subject_id | BIGINT UNSIGNED | FK → curriculum_subjects | |
| activity_type | ENUM | | `modular_study`, `case_analysis`, `research`, `video_presentation`, `webinar`, `skills_demo`, `reporting` |
| title | VARCHAR(200) | NOT NULL | |
| description | TEXT | NULLABLE | |
| due_date | DATE | NULLABLE | |
| status | ENUM | | `assigned`, `in_progress`, `submitted`, `approved`, `rejected` |
| score | DECIMAL(5,2) | NULLABLE | |
| feedback | TEXT | NULLABLE | |
| timestamps | — | — | |

#### `activity_submissions`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK | |
| enhancement_activity_id | BIGINT UNSIGNED | FK → enhancement_activities | |
| file_path | VARCHAR(500) | | |
| notes | TEXT | NULLABLE | |
| submitted_at | TIMESTAMP | | |
| timestamps | — | — | |

#### `rubrics`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK | |
| curriculum_subject_id | BIGINT UNSIGNED | FK → curriculum_subjects | NULLABLE |
| title | VARCHAR(200) | NOT NULL | |
| criteria | JSON | NOT NULL | `[{name, weight, levels: [{score, description}]}]` |
| max_score | DECIMAL(5,2) | | Auto-calculated |
| is_template | BOOLEAN | DEFAULT false | |
| timestamps | — | — | |

#### `grades`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK | |
| student_id | BIGINT UNSIGNED | FK → users | |
| curriculum_subject_id | BIGINT UNSIGNED | FK → curriculum_subjects | |
| enrollment_id | BIGINT UNSIGNED | FK → enrollments | |
| adviser_id | BIGINT UNSIGNED | FK → users | |
| grade | VARCHAR(10) | NOT NULL | Numeric or `CE` (credited), `T` (transferred) |
| grade_point | DECIMAL(3,1) | NULLABLE | For GWA computation |
| rubric_id | BIGINT UNSIGNED | FK → rubrics, NULLABLE | |
| rubric_score | DECIMAL(5,2) | NULLABLE | |
| rubric_file_path | VARCHAR(500) | NULLABLE | |
| status | ENUM | | `draft`, `submitted`, `reviewed`, `approved` |
| approved_by | BIGINT UNSIGNED | FK → users, NULLABLE | Program Head |
| approved_at | TIMESTAMP | NULLABLE | |
| timestamps | — | — | |

#### `graduation_clearances`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK | |
| student_id | BIGINT UNSIGNED | FK → users | |
| status | ENUM | | `pending`, `in_review`, `approved`, `completed` |
| confirmation_letter_path | VARCHAR(500) | NULLABLE | |
| vpaa_approved_at | TIMESTAMP | NULLABLE | |
| registry_number | VARCHAR(50) | UNIQUE, NULLABLE | `RGN-{YYYY}-{NNNN}` |
| diploma_printed_at | TIMESTAMP | NULLABLE | |
| tor_released_at | TIMESTAMP | NULLABLE | |
| clearance_form_path | VARCHAR(500) | NULLABLE | |
| timestamps | — | — | |

#### `clearance_documents`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK | |
| graduation_clearance_id | BIGINT UNSIGNED | FK → graduation_clearances | |
| document_type | VARCHAR(50) | | Matches the 12-item checklist |
| file_path | VARCHAR(500) | NULLABLE | |
| is_verified | BOOLEAN | DEFAULT false | |
| verified_by | BIGINT UNSIGNED | FK → users, NULLABLE | |
| timestamps | — | — | |

#### `notifications`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK | |
| user_id | BIGINT UNSIGNED | FK → users | |
| type | VARCHAR(100) | | e.g., `application.approved` |
| title | VARCHAR(200) | NOT NULL | |
| body | TEXT | NULLABLE | |
| data | JSON | NULLABLE | Action link metadata |
| read_at | TIMESTAMP | NULLABLE | |
| channel | ENUM | | `in_app`, `email`, `sms` |
| sent_at | TIMESTAMP | NULLABLE | |
| timestamps | — | — | |

#### `audit_logs`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK | |
| user_id | BIGINT UNSIGNED | FK → users, NULLABLE | |
| action | VARCHAR(50) | NOT NULL | |
| target_type | VARCHAR(50) | NOT NULL | |
| target_id | BIGINT UNSIGNED | NULLABLE | |
| old_values | JSON | NULLABLE | |
| new_values | JSON | NULLABLE | |
| ip_address | VARCHAR(45) | NULLABLE | |
| user_agent | TEXT | NULLABLE | |
| created_at | TIMESTAMP | | Indexed |

#### `equivalency_rules`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK | |
| rule_type | ENUM | | `direct`, `experience`, `certification`, `training`, `prior_education` |
| condition_key | VARCHAR(100) | | Match key |
| condition_value | TEXT | | Match value |
| curriculum_subject_id | BIGINT UNSIGNED | FK → curriculum_subjects | Target |
| equivalent_credits | DECIMAL(3,1) | | |
| priority | INT | DEFAULT 0 | |
| is_active | BOOLEAN | DEFAULT true | |
| timestamps | — | — | |

**Note:** Additional pivot/index tables exist for `password_reset_tokens`, `personal_access_tokens` (Sanctum), `sessions`, and `failed_jobs` as per Laravel conventions.

---

## 3. API Route Design

### 3.1 Route Prefixing Convention

```
/api/v1/{resource}
```

All routes prefixed with `/api/v1`. Authentication via Sanctum token in `Authorization: Bearer` header.

### 3.2 Public Routes (No Auth)

| Method | Endpoint | Controller | Purpose |
|--------|----------|------------|---------|
| POST | `/api/v1/auth/register` | AuthController@register | Applicant self-registration |
| POST | `/api/v1/auth/login` | AuthController@login | Email + password login |
| POST | `/api/v1/auth/forgot-password` | AuthController@forgotPassword | Send reset link |
| POST | `/api/v1/auth/reset-password` | AuthController@resetPassword | Reset with token |
| POST | `/api/v1/auth/verify-email` | AuthController@verifyEmail | Verify OTP code |
| POST | `/api/v1/auth/resend-verification` | AuthController@resendVerification | Re-send OTP |
| GET | `/api/v1/curriculum/subjects` | CurriculumController@index | Public subject listing |

### 3.3 Authenticated Routes (All Roles)

| Method | Endpoint | Controller | Purpose |
|--------|----------|------------|---------|
| GET | `/api/v1/auth/me` | AuthController@me | Current user profile |
| PUT | `/api/v1/auth/profile` | AuthController@updateProfile | Update own profile |
| POST | `/api/v1/auth/logout` | AuthController@logout | Revoke token |
| GET | `/api/v1/notifications` | NotificationController@index | User notifications |
| PUT | `/api/v1/notifications/{id}/read` | NotificationController@markRead | |
| PUT | `/api/v1/notifications/read-all` | NotificationController@markAllRead | |

### 3.4 Applicant Routes

| Method | Endpoint | Controller | Purpose |
|--------|----------|------------|---------|
| POST | `/api/v1/applications` | ApplicationController@store | Submit new application |
| GET | `/api/v1/applications/{id}` | ApplicationController@show | View application |
| PUT | `/api/v1/applications/{id}` | ApplicationController@update | Update draft |
| GET | `/api/v1/applications/{id}/status` | ApplicationController@status | Status timeline |
| — | — | — | — |
| POST | `/api/v1/applications/{id}/documents` | DocumentController@upload | Upload document |
| GET | `/api/v1/applications/{id}/documents` | DocumentController@index | List documents |
| DELETE | `/api/v1/documents/{id}` | DocumentController@destroy | Remove document (draft only) |
| — | — | — | — |
| POST | `/api/v1/applications/{id}/portfolio/submit` | PortfolioController@submit | Submit portfolio |
| GET | `/api/v1/applications/{id}/portfolio` | PortfolioController@show | View e-portfolio |
| — | — | — | — |
| POST | `/api/v1/experience-records` | ExperienceController@store | Add work experience |
| PUT | `/api/v1/experience-records/{id}` | ExperienceController@update | |
| DELETE | `/api/v1/experience-records/{id}` | ExperienceController@destroy | |
| — | — | — | — |
| POST | `/api/v1/education-records` | EducationController@store | Add education |
| PUT | `/api/v1/education-records/{id}` | EducationController@update | |
| DELETE | `/api/v1/education-records/{id}` | EducationController@destroy | |

### 3.5 Coordinator Routes

| Method | Endpoint | Controller | Purpose |
|--------|----------|------------|---------|
| GET | `/api/v1/coordinator/applications` | ApplicationController@index | All applications (filterable) |
| GET | `/api/v1/coordinator/applications/kanban` | ApplicationController@kanban | Kanban board data |
| PUT | `/api/v1/coordinator/applications/{id}/review` | ApplicationController@review | Approve/reject/request revision |
| — | — | — | — |
| PUT | `/api/v1/coordinator/documents/{id}/verify` | DocumentController@verify | Verify/reject document |
| POST | `/api/v1/coordinator/documents/{id}/watermark` | DocumentController@watermark | Apply watermark |
| — | — | — | — |
| POST | `/api/v1/coordinator/assessments/first` | AssessmentController@submitFirst | Submit F.1 form |
| POST | `/api/v1/coordinator/assessments/second` | AssessmentController@submitSecond | Submit F.2 form |
| — | — | — | — |
| POST | `/api/v1/coordinator/interviews` | AssessmentController@scheduleInterview | Schedule panel interview |
| GET | `/api/v1/coordinator/interviews` | AssessmentController@listInterviews | Interview schedule |
| — | — | — | — |
| POST | `/api/v1/coordinator/enrollments` | EnrollmentController@store | Process enrollment |
| GET | `/api/v1/coordinator/enrollments` | EnrollmentController@index | Enrollment list |
| — | — | — | — |
| POST | `/api/v1/coordinator/graduation/confirm` | GraduationController@confirm | Send confirmation letter to VPAA |
| GET | `/api/v1/coordinator/graduation/pipeline` | GraduationController@pipeline | Graduation-ready list |
| GET | `/api/v1/coordinator/reports/{type}` | ReportController@generate | Generate report |

### 3.6 Program Head Routes

| Method | Endpoint | Controller | Purpose |
|--------|----------|------------|---------|
| GET | `/api/v1/program-head/equivalency/pending` | EquivalencyController@pending | Pending F.2 evaluations |
| POST | `/api/v1/program-head/equivalency/{id}` | EquivalencyController@store | Submit initial matrix |
| PUT | `/api/v1/program-head/equivalency/{id}/items` | EquivalencyController@updateItems | Update matrix items |
| — | — | — | — |
| POST | `/api/v1/program-head/assessors` | AssessorController@assignPanel | Set panel for F.3 |
| GET | `/api/v1/program-head/assessors/available` | AssessorController@available | List available assessors |
| — | — | — | — |
| POST | `/api/v1/program-head/adviser-assignments` | AdviserController@assign | Assign adviser |
| GET | `/api/v1/program-head/adviser-assignments` | AdviserController@index | Assignment list |
| GET | `/api/v1/program-head/adviser-workload` | AdviserController@workload | Workload report |
| — | — | — | — |
| GET | `/api/v1/program-head/grades/pending` | GradeController@pendingApproval | Pending grade approvals |
| PUT | `/api/v1/program-head/grades/{id}/approve` | GradeController@approve | Approve grade |
| — | — | — | — |
| POST | `/api/v1/program-head/curriculum/subjects` | CurriculumController@store | Add subject |
| PUT | `/api/v1/program-head/curriculum/subjects/{id}` | CurriculumController@update | Edit subject |
| POST | `/api/v1/program-head/curriculum/version` | CurriculumController@newVersion | Create curriculum snapshot |

### 3.7 QA Officer Routes

| Method | Endpoint | Controller | Purpose |
|--------|----------|------------|---------|
| GET | `/api/v1/qa-officer/validations/pending` | AssessmentController@pendingValidation | Pending F.3 validations |
| POST | `/api/v1/qa-officer/validations/{id}/final-matrix` | EquivalencyController@finalMatrix | Submit final matrix |
| POST | `/api/v1/qa-officer/enhancement-plan` | EnhancementController@createPlan | Create enhancement plan |
| GET | `/api/v1/qa-officer/enhancement-progress` | EnhancementController@overview | Overview of all active plans |

### 3.8 Adviser Routes

| Method | Endpoint | Controller | Purpose |
|--------|----------|------------|---------|
| GET | `/api/v1/adviser/advisees` | AdviserController@advisees | List assigned students |
| GET | `/api/v1/adviser/advisees/{id}` | AdviserController@adviseeDetail | Student detail + progress |
| — | — | — | — |
| POST | `/api/v1/adviser/activities` | EnhancementController@assignActivity | Assign activity |
| PUT | `/api/v1/adviser/activities/{id}` | EnhancementController@updateActivity | Edit activity |
| PUT | `/api/v1/adviser/activities/{id}/score` | EnhancementController@score | Score + feedback |
| GET | `/api/v1/adviser/activities/submissions` | EnhancementController@pendingSubmissions | Pending review |
| — | — | — | — |
| POST | `/api/v1/adviser/rubrics` | RubricController@store | Create rubric |
| GET | `/api/v1/adviser/rubrics` | RubricController@index | List rubrics |
| — | — | — | — |
| POST | `/api/v1/adviser/grades` | GradeController@store | Input grade |
| PUT | `/api/v1/adviser/grades/{id}` | GradeController@update | Edit grade (draft only) |
| POST | `/api/v1/adviser/grades/{id}/submit` | GradeController@submit | Submit for review |

### 3.9 Assessor Routes (Internal & External)

| Method | Endpoint | Controller | Purpose |
|--------|----------|------------|---------|
| GET | `/api/v1/assessor/interviews` | AssessorController@assignedInterviews | My interviews |
| GET | `/api/v1/assessor/interviews/{id}/portfolio` | AssessorController@viewPortfolio | View applicant portfolio |
| POST | `/api/v1/assessor/interviews/{id}/validate` | AssessmentController@submitValidation | Submit validation tool |
| POST | `/api/v1/assessor/interviews/{id}/oral` | AssessmentController@submitOral | Submit oral competency form |

### 3.10 VPAA Routes

| Method | Endpoint | Controller | Purpose |
|--------|----------|------------|---------|
| GET | `/api/v1/vpaa/approvals` | VpaaController@pendingApprovals | Pending confirmation letters |
| PUT | `/api/v1/vpaa/approvals/{id}/approve` | VpaaController@approveConfirmation | Approve + set RGN |
| GET | `/api/v1/vpaa/graduation-audit` | VpaaController@graduationAudit | Full audit list |
| GET | `/api/v1/vpaa/reports/{type}` | ReportController@vpaaReport | VPAA-level reports |

### 3.11 Student Routes (After Admission)

| Method | Endpoint | Controller | Purpose |
|--------|----------|------------|---------|
| GET | `/api/v1/student/dashboard` | StudentController@dashboard | Student dashboard data |
| GET | `/api/v1/student/curriculum-checklist` | EnrollmentController@checklist | Personalized curriculum |
| — | — | — | — |
| GET | `/api/v1/student/activities` | EnhancementController@myActivities | My activities |
| POST | `/api/v1/student/activities/{id}/submit` | EnhancementController@submitActivity | Submit evidence |
| — | — | — | — |
| GET | `/api/v1/student/grades` | GradeController@myGrades | My grades |
| — | — | — | — |
| GET | `/api/v1/student/graduation` | GraduationController@myStatus | Graduation status |
| POST | `/api/v1/student/clearance/submit` | GraduationController@submitClearance | Submit clearance form |

### 3.12 System Admin Routes

| Method | Endpoint | Controller | Purpose |
|--------|----------|------------|---------|
| GET | `/api/v1/admin/users` | UserController@index | List all users |
| POST | `/api/v1/admin/users` | UserController@store | Create staff user |
| PUT | `/api/v1/admin/users/{id}/roles` | UserController@assignRole | Assign role |
| PUT | `/api/v1/admin/users/{id}/suspend` | UserController@suspend | Suspend user |
| — | — | — | — |
| GET | `/api/v1/admin/audit-logs` | AuditController@index | Search audit logs |
| GET | `/api/v1/admin/audit-logs/{id}` | AuditController@show | Log detail |
| — | — | — | — |
| GET | `/api/v1/admin/system/health` | SystemController@health | System health check |
| PUT | `/api/v1/admin/system/config` | SystemController@updateConfig | Update ETEEAP config |

### 3.13 Dashboard & Analytics Routes

| Method | Endpoint | Controller | Purpose |
|--------|----------|------------|---------|
| GET | `/api/v1/dashboard/coordinator` | DashboardController@coordinator | Coordinator widgets |
| GET | `/api/v1/dashboard/program-head` | DashboardController@programHead | Program head widgets |
| GET | `/api/v1/dashboard/adviser` | DashboardController@adviser | Adviser widgets |
| GET | `/api/v1/dashboard/student` | DashboardController@student | Student widgets |
| GET | `/api/v1/dashboard/vpaa` | DashboardController@vpaa | VPAA widgets |
| GET | `/api/v1/dashboard/qa-officer` | DashboardController@qaOfficer | QA Officer widgets |

### 3.14 Document Download Routes

| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | `/api/v1/documents/{id}/download` | Download original file |
| GET | `/api/v1/documents/{id}/preview` | In-browser preview (PDF/images) |
| GET | `/api/v1/templates/{type}/download` | Download form template |
| GET | `/api/v1/portfolios/{id}/download` | Download merged e-portfolio PDF |
| GET | `/api/v1/grades/{id}/tor-preview` | Preview TOR |
| GET | `/api/v1/graduation/{id}/diploma-preview` | Preview diploma |

---

## 4. React Component Tree

### 4.1 Routing Structure

```
<App>
  <GuestLayout>                                    # Public pages
    <Login />
    <Register />
    <ForgotPassword />
    <ResetPassword />
    <VerifyEmail />
  </GuestLayout>

  <AppLayout>                                       # Authenticated shell
    <Sidebar role={role} />
    <Navbar user={user} />
    <Breadcrumb />
    <main>
      <Routes>
        {/* Applicant */}
        {role === 'applicant' && (
          <ApplicantLayout>
            <ApplicantDashboard />
            <ApplicationForm />
            <DocumentManager />
            <PortfolioBuilder />
            <ApplicationTracker />
          </ApplicantLayout>
        )}

        {/* Student */}
        {role === 'student' && (
          <StudentLayout>
            <StudentDashboard />
            <CurriculumChecklist />
            <ActivityList />
            <GradeViewer />
            <GraduationStatus />
            <ClearanceProcess />
          </StudentLayout>
        )}

        {/* Coordinator */}
        {role === 'coordinator' && (
          <CoordinatorLayout>
            <CoordinatorDashboard />
            <KanbanBoard />
            <ApplicationReviewPage />
            <DocumentVerificationPage />
            <AssessmentManagementPage />
            <InterviewScheduler />
            <EnrollmentPage />
            <GraduationPage />
            <ReportViewer />
          </CoordinatorLayout>
        )}

        {/* Program Head */}
        {role === 'program_head' && (
          <ProgramHeadLayout>
            <ProgramHeadDashboard />
            <EquivalencyEvaluationPage />
            <PanelManagementPage />
            <AdviserAssignmentPage />
            <GradeApprovalPage />
            <CurriculumManagementPage />
          </ProgramHeadLayout>
        )}

        {/* Adviser */}
        {role === 'adviser' && (
          <AdviserLayout>
            <AdviserDashboard />
            <AdviseeListPage />
            <ActivityManagementPage />
            <GradeEntryPage />
            <RubricBuilderPage />
          </AdviserLayout>
        )}

        {/* QA Officer */}
        {role === 'qa_officer' && (
          <QALayout>
            <QADashboard />
            <ValidationReviewPage />
            <FinalMatrixEditor />
            <EnhancementPlanPage />
          </QALayout>
        )}

        {/* Assessor */}
        {(role === 'assessor_internal' || role === 'assessor_external') && (
          <AssessorLayout>
            <AssessorDashboard />
            <InterviewPage />
          </AssessorLayout>
        )}

        {/* VPAA */}
        {role === 'vpaa' && (
          <VpaaLayout>
            <VpaaDashboard />
            <ApprovalQueue />
            <GraduationAuditPage />
            <ReportViewer />
          </VpaaLayout>
        )}

        {/* Admin */}
        {role === 'admin' && (
          <AdminLayout>
            <UserManagementPage />
            <RoleManagementPage />
            <AuditLogViewer />
            <SystemConfigPage />
          </AdminLayout>
        )}

        {/* Common */}
        <ProfilePage />
        <NotificationsPage />
      </Routes>
    </main>
  </AppLayout>
</App>
```

### 4.2 Key Component Specifications

#### `KanbanBoard.jsx` (Coordinator)

```
Props: applications[] (grouped by status)
Children:
  └─ KanbanColumn (×5: New, Under Review, Revision, Portfolio Building, Complete)
       └─ KanbanCard (per application)
            ├─ Applicant name, photo avatar
            ├─ Application number + date
            ├─ Document completeness badge (X/18)
            ├─ Status badge (colored)
            └─ Action dropdown (Approve, Reject, Request Revision, Assign Assessment)
```

#### `PortalDocumentUpload.jsx` (Applicant/Student)

```
Props: documentType, acceptedFormats, maxSize, onUpload
States: idle → uploading → success / error
Features:
  - Drag-and-drop zone
  - File type validation (PDF/JPG/PNG)
  - Size validation (max 5MB)
  - Preview thumbnail for images
  - Multiple files per type
  - Version history display
```

#### `EquivalencyMatrixEditor.jsx` (Program Head / QA Officer)

```
Props: mode ('initial'|'final'), applicationId
Structure:
  - Subject list table with columns:
    [Code | Title | Units | Competency (Full/Partial/None) | Evidence | Credits | Action]
  - Color-coded rows: Green(full), Yellow(partial), Red(none)
  - Inline editing: dropdown for competency, text for evidence
  - Auto-suggest from equivalency rules engine
  - Save as draft / Submit for approval
  - PDF export button
```

#### `EnhancementActivityList.jsx` (Adviser & Student)

```
Views:
  - Adviser: Activity assignment form + Scoring interface
  - Student: Activity list + Submission upload

Activity Card:
  ├─ Activity type icon
  ├─ Title + description
  ├─ Subject code reference
  ├─ Due date with countdown
  ├─ Status badge (color-coded)
  ├─ Submission area (Student) or Score input (Adviser)
  └─ Feedback thread
```

#### `DashboardWidgetGrid.jsx` (All Roles)

```
Responsive grid (2-3-4 columns based on viewport)
Widget types:
  - <StatsCard value={} label={} icon={} color={} />
  - <PipelineChart data={} stages={} />
  - <CalendarWidget events={} />
  - <ActivityFeed items={} />
  - <ApprovalQueue items={} onAction={} />
  - <ProgressWidget percent={} label={} />
```

---

## 5. Data Flow Diagrams

### 5.1 Full Application → Graduation State Machine

```
                    ┌──────────────┐
                    │   Draft      │
                    └──────┬───────┘
                           │ submit
                           ▼
                    ┌──────────────┐
              ┌─────│  Submitted   │─────┐
              │     └──────┬───────┘     │
              │            │ review      │ auto-qual fail
              │            ▼             │
              │     ┌──────────────┐     │
              │     │ Under Review │     │
              │     └──────┬───────┘     │
              │            │             │
              │     ┌──────┴──────┐      │
              │     │             │      │
              │     ▼             ▼      │
              │  Approved     Rejected   │
              │     │                    │
              │     ▼                    │
              │  ┌──────────┐           │
              │  │ Portfolio │           │
              │  │ Building  │           │
              │  └─────┬────┘           │
              │        │ submit         │
              │        ▼                │
              │  ┌──────────────┐       │
              │  │   Portfolio  │       │
              │  │   Submitted  │       │
              │  └──────┬───────┘       │
              │         │               │
              ▼         ▼               ▼
        ┌────────────────────────────────────┐
        │        F.1 Initial Screening       │
        └────────────────┬───────────────────┘
                         │ pass
                         ▼
        ┌────────────────────────────────────┐
        │     F.2 Portfolio Evaluation       │
        │   (Coordinator + Program Head)     │
        └────────────────┬───────────────────┘
                         │ complete
                         ▼
        ┌────────────────────────────────────┐
        │    F.3 Panel Validation Interview  │
        │   (Panel + QA Officer)             │
        └────────────────┬───────────────────┘
                         │ complete
                         ▼
        ┌────────────────────────────────────┐
        │   Final Equivalency + Enhancement  │
        │         Plan (QA Officer)          │
        └────────────────┬───────────────────┘
                         │
                         ▼
        ┌────────────────────────────────────┐
        │           Admission                │
        │    (ID generated, student active)  │
        └────────────────┬───────────────────┘
                         │
                         ▼
        ┌────────────────────────────────────┐
        │          Enrollment                │
        │  (Subjects encoded, fees assessed) │
        └────────────────┬───────────────────┘
                         │
                         ▼
        ┌────────────────────────────────────┐
        │     Enhancement Activities         │
        │   (Assigned → In Progress → Done)  │
        └────────────────┬───────────────────┘
                         │ all complete
                         ▼
        ┌────────────────────────────────────┐
        │     Final Grade Computation        │
        │  (Adviser → Coordinator → PH)      │
        └────────────────┬───────────────────┘
                         │ all subjects done
                         ▼
        ┌────────────────────────────────────┐
        │   Graduation Clearance (12 docs)    │
        └────────────────┬───────────────────┘
                         │ verified
                         ▼
        ┌────────────────────────────────────┐
        │  Confirmation Letter → VPAA Approves│
        │  → RGN Assigned → Diploma + TOR    │
        └────────────────────────────────────┘
```

### 5.2 Document Upload Flow

```
User selects file
       │
       ▼
Client-side validation (type, size, virus scan)
       │
       ├── Fail → Show error message to user
       │
       ▼ Pass
POST /api/v1/applications/{id}/documents
       │
       ▼
Server-side validation
       │
       ├── Fail → Return validation errors
       │
       ▼ Pass
Store file in storage/app/documents/{user_id}/{type}/
       │
       ▼
Create Document record in database
       │
       ▼
If document completes a required type:
  → Update application progress (X/18)
       │
       ▼
Return success response with file metadata
       │
       ▼
UI updates document list + progress indicator
```

### 5.3 Assessment Flow (F.1 → F.2 → F.3)

```
┌─────────┐     ┌─────────┐     ┌─────────┐
│   F.1   │────▶│   F.2   │────▶│   F.3   │
│ Initial │     │Portfolio│     │  Panel  │
└────┬────┘     └────┬────┘     └────┬────┘
     │               │               │
     ▼               ▼               ▼
┌────────┐    ┌──────────────┐  ┌──────────────┐
│Auto    │    │Coordinator   │  │Orientation   │
│Qual    │    │ reviews docs │  │(2 days prior)│
│Check   │    │              │  └──────┬───────┘
└────────┘    │Program Head  │         │
              │ creates      │         ▼
              │ initial      │  ┌──────────────┐
              │ matrix       │  │Panel Interview│
              │              │  │(≥3 assessors)│
              │Panel list    │  │≥5 Qs each    │
              └──────────────┘  └──────┬───────┘
                                       │
                                       ▼
                                ┌──────────────┐
                                │Panel submits  │
                                │Validation +   │
                                │Oral forms     │
                                └──────┬───────┘
                                       │
                                       ▼
                                ┌──────────────┐
                                │QA Officer:    │
                                │Final Matrix + │
                                │Enhancement    │
                                └──────────────┘
```

### 5.4 Notification Dispatch Flow

```
Trigger event (e.g., application.approved)
       │
       ▼
NotificationService.handle(event, data)
       │
       ▼
Query recipients from event config
       │
       ▼
For each recipient:
  ├── Create in-app notification record
  ├── (Async) Send email via Mail queue
  └── (Async) Send SMS via SMS queue (if enabled)
       │
       ▼
Recipient sees badge count update (via polling or WebSocket)
```

---

## 6. Implementation Phases

### Phase 1: Foundation (Weeks 1–3)

**Goal:** Scaffold the project, authentication, role management, and database.

| Task | Deliverable | Owner |
|------|-------------|-------|
| Laravel project setup with Sanctum + Inertia | Working auth scaffolding | Backend |
| React + Vite frontend scaffold | UI shell with routing | Frontend |
| Users, roles, permissions tables + seeders | RBAC functional | Backend |
| Auth API routes (register, login, verify, reset) | Login/register flow | Both |
| Role-based middleware + frontend route guards | Role-isolated routes | Both |
| Guest layout + App layout + Sidebar | Navigation shell | Frontend |
| Profile management (view + edit) | Profile page | Both |

**Milestone:** Admin can create staff users; applicants can register and login.

---

### Phase 2: Application & Documents (Weeks 4–6)

**Goal:** Full application submission and document management pipeline.

| Task | Deliverable | Owner |
|------|-------------|-------|
| Application table + API CRUD | Application lifecycle | Backend |
| Applicant profile + experience + education tables | Rich profile data | Backend |
| Multi-step application form (React) | Application wizard | Frontend |
| Document table + file upload API | Document storage | Backend |
| FileUpload component (drag-drop, validation) | Upload UI | Frontend |
| Document checklist with status tracking | Progress UI | Frontend |
| Coordinator kanban board | Application review UI | Frontend |
| Coordinator document verification workflow | Verify/reject flow | Both |

**Milestone:** Applicant can submit full application with all 18 documents; Coordinator can review and approve.

---

### Phase 3: Assessment Engine (Weeks 7–9)

**Goal:** Three-stage assessment pipeline with equivalency matrix.

| Task | Deliverable | Owner |
|------|-------------|-------|
| Assessment forms table + dynamic form generator | Flexible form system | Backend |
| F.1 assessment form + submission | Screening tool | Both |
| F.2 portfolio evaluation + initial matrix | Portfolio evaluation | Both |
| F.3 panel validation + oral competency forms | Panel assessment | Both |
| Interview scheduling with calendar | Schedule UI | Both |
| Panel invitation dispatch (auto-email) | Automated invites | Backend |
| Final equivalency matrix editor | Matrix UI | Both |
| Program Design on Assessment & Evaluation form | Assessment design | Both |

**Milestone:** Applicant completes all 3 assessment stages; final equivalency matrix generated.

---

### Phase 4: Curriculum & Enhancement (Weeks 10–12)

**Goal:** Curriculum mapping, equivalency rules engine, and enhancement activities.

| Task | Deliverable | Owner |
|------|-------------|-------|
| Curriculum subjects CRUD + versioning | Subject repository | Backend |
| Equivalency rules engine + admin UI | Auto-suggest rules | Both |
| Enhancement activity types + assignment flow | Activity assignment | Both |
| Student activity dashboard + submission | Student UI | Frontend |
| Adviser activity management + scoring | Adviser UI | Frontend |
| Rubric builder (criteria + levels) | Rubric system | Both |

**Milestone:** Student can receive and submit enhancement activities; Adviser can score with rubrics.

---

### Phase 5: Enrollment & Grades (Weeks 13–15)

**Goal:** Enrollment processing, curriculum checklist, and grade management.

| Task | Deliverable | Owner |
|------|-------------|-------|
| Enrollment table + API | Enrollment workflow | Backend |
| Student ID auto-generation | ID generator | Backend |
| Enrollment form (auto-populated from matrix) | Enrollment UI | Both |
| Personalized curriculum checklist | Student checklist | Both |
| Adviser overload enforcement (max 3 students, 3 subjects) | Business rules | Backend |
| Grade entry form with rubric attachment | Grade input | Both |
| Grade approval workflow (Adviser → Coordinator → PH) | Approval chain | Both |

**Milestone:** Student enrolled with full curriculum view; grades posted with approval chain.

---

### Phase 6: Graduation & Clearance (Weeks 16–17)

**Goal:** Graduation pipeline, diploma generation, and TOR release.

| Task | Deliverable | Owner |
|------|-------------|-------|
| Graduation clearance 12-item checklist | Clearance tracker | Backend |
| Clearance document verification flow | Verification UI | Both |
| Confirmation letter generation + VPAA approval | Approval flow | Both |
| Registry of Graduate Number auto-assignment | RGN generator | Backend |
| Diploma PDF generation (template + merge) | Diploma output | Backend |
| TOR PDF generation from accumulated records | TOR output | Backend |
| Clearance form submission (student) | Student clearance UI | Frontend |

**Milestone:** Student can graduate; diploma and TOR generated.

---

### Phase 7: Communication, Dashboards & Reports (Weeks 18–19)

**Goal:** Notifications, dashboards, and reporting.

| Task | Deliverable | Owner |
|------|-------------|-------|
| In-app notification system + API | Notification center | Backend |
| Notification bell + dropdown component | Frontend UI | Frontend |
| Email notification templates + queue | Email delivery | Backend |
| SMS notification integration (optional) | SMS delivery | Backend |
| Role-specific dashboard widgets + API | Dashboard data | Both |
| Dashboard UI for all 8 roles | Dashboard pages | Frontend |
| Report generation (PDF/Excel) for 7 report types | Report engine | Backend |
| Report viewer + export UI | Export interface | Frontend |

**Milestone:** Full communication loop; each role has a live dashboard; reports exportable.

---

### Phase 8: Admin, Audit & Polish (Week 20)

**Goal:** System administration, audit logging, and hardening.

| Task | Deliverable | Owner |
|------|-------------|-------|
| User management (CRUD + role assignment) | Admin panel | Both |
| Audit log viewer with search/filter | Audit interface | Both |
| System configuration (ETEEAP settings) | Config page | Both |
| Document watermarking automation | Auto-watermark | Backend |
| Data retention + auto-anonymization cron | Cleanup jobs | Backend |
| CHED compliance report builder | Compliance report | Backend |
| Performance optimization (caching, indexing) | Speed pass | Backend |
| End-to-end testing (happy path + edge cases) | Test suite | QA |

**Milestone:** System is production-ready, auditable, and performant.

---

## Appendix A: Key Business Rules Matrix

| Rule | Enforced At | Implementation |
|------|-------------|----------------|
| Age ≥ 25 | Application submission | `FormRequest` validation: `birth_date` → diffInYears |
| 5+ yrs BSIT experience | Application submission | `FormRequest`: sum of `experience_records` durations |
| Panel ≥ 3 assessors | Interview scheduling | Database constraint + frontend validation |
| Adviser max 3 students | Adviser assignment | `StoreAdviserAssignmentRequest` checks count |
| Max 3 subjects per student per term | Enrollment | `EnrollmentService` validates |
| Rubric required for grade | Grade submission | `GradeObserver` `creating` event |
| All 12 docs verified | Graduation | `GraduationService` checks `clearance_documents` |
| Confirmation letter VPAA-approved | Diploma | `GraduationController` checks `vpaa_approved_at` |

---

## Appendix B: Environment Configuration

```env
# .env additions
ETEEAP_MIN_AGE=25
ETEEAP_MIN_EXPERIENCE_YEARS=5
ETEEAP_ADVISER_MAX_STUDENTS=3
ETEEAP_STUDENT_MAX_SUBJECTS=3
ETEEAP_COMPLETION_MIN_MONTHS=6
ETEEAP_COMPLETION_MAX_MONTHS=24
ETEEAP_PANEL_MIN_ASSESSORS=3
ETEEAP_RETENTION_YEARS=5
ETEEAP_INACTIVE_PURGE_MONTHS=12
```

```php
// config/eteeap.php
return [
    'min_age' => env('ETEEAP_MIN_AGE', 25),
    'min_experience_years' => env('ETEEAP_MIN_EXPERIENCE_YEARS', 5),
    'adviser_max_students' => env('ETEEAP_ADVISER_MAX_STUDENTS', 3),
    'student_max_subjects' => env('ETEEAP_STUDENT_MAX_SUBJECTS', 3),
    'completion_min_months' => env('ETEEAP_COMPLETION_MIN_MONTHS', 6),
    'completion_max_months' => env('ETEEAP_COMPLETION_MAX_MONTHS', 24),
    'panel_min_assessors' => env('ETEEAP_PANEL_MIN_ASSESSORS', 3),
    'retention_years' => env('ETEEAP_RETENTION_YEARS', 5),
    'inactive_purge_months' => env('ETEEAP_INACTIVE_PURGE_MONTHS', 12),
    'document' => [
        'allowed_types' => ['application/pdf', 'image/jpeg', 'image/png'],
        'max_size' => 5 * 1024 * 1024, // 5MB
        'storage_path' => 'documents',
    ],
    'id_format' => 'ETEEAP-{YEAR}-{NUMBER}', // Student ID number
    'app_format' => 'APP-{YEAR}-{NUMBER}',   // Application number
];
```

---

*End of Implementation Structure Document*

*Tech Stack: Laravel 11 + React 18 (Inertia.js) + MySQL 8*
*Based on: ADFC ETEEAP Implementation Manual (PPG) | CHED CMO No. 54, Series of 2016*
