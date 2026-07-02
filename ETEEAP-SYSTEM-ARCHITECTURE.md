# ADFC ETEEAP Portal — Thematic System Architecture

> **Document Purpose:** Comprehensive system architecture for the Expanded Tertiary Education Equivalency and Accreditation Program (ETEEAP) Portal of Asian Development Foundation College (ADFC), specifically for the BSIT degree program.
>
> **Source Document:** ETEEAP Implementation Manual (PPG)

---

## Table of Contents

1. [User & Role Management](#theme-1-user--role-management)
2. [Application & Portfolio Management](#theme-2-application--portfolio-management)
3. [Assessment & Evaluation Pipeline](#theme-3-assessment--evaluation-pipeline)
4. [Equivalency & Curriculum Mapping](#theme-4-equivalency--curriculum-mapping)
5. [Document Management System](#theme-5-document-management-system)
6. [Enrollment & Academic Records](#theme-6-enrollment--academic-records)
7. [Completion & Graduation Pipeline](#theme-7-completion--graduation-pipeline)
8. [Communication & Notifications](#theme-8-communication--notifications)
9. [Dashboards & Analytics](#theme-9-dashboards--analytics)
10. [System-Wide Concerns](#theme-10-system-wide-concerns)

---

## Theme 1: User & Role Management

### 1.1 Role Hierarchy

```
System Admin
  └─ VP for Academic Affairs (approvals, oversight)
       └─ ETEEAP Coordinator (central orchestrator)
            ├─ Program Head (curriculum, equivalency)
            │    ├─ Adviser (teaching, monitoring)
            │    └─ Internal Assessor (evaluation)
            ├─ QA Officer (quality, final matrix)
            └─ External Assessor (industry panel)
                 └─ Applicant/Student (end-user)
```

### 1.2 Role Definitions & Responsibilities

| Role | Primary Function |
|------|-----------------|
| **System Admin** | Manage users, system configuration, audit logs |
| **VP for Academic Affairs** | Approve confirmation letters, graduation lists, policy exceptions |
| **ETEEAP Coordinator** | Central orchestrator — screening, assessment coordination, admission, enrollment, graduation, document management |
| **Program Head** | Equivalency matrix creation, assessor list, adviser assignment, quality oversight, grade approval |
| **Adviser** | Instruction, enhancement activities, consultation, monitoring, evaluation, grading |
| **Internal Assessor** | Panel interview, competency assessment |
| **External Assessor** | Industry panel interview, validation |
| **QA Officer** | Validation interview, final equivalency matrix, enhancement requirements |
| **Applicant** | Self-register, submit documents, pay fees, complete requirements |
| **Student** | Promoted from Applicant — enrolled and progressing through enhancement program |

### 1.3 Account States

| State | Description |
|-------|-------------|
| **Pending Verification** | Applicant registered, awaiting email/OTP confirmation |
| **Active** | Account verified and operational |
| **Suspended** | Temporarily disabled (admin action) |
| **Archived** | Graduated or inactive > 2 years |
| **Rejected** | Application denied |

### 1.4 Authentication & Authorization

- Email/password registration with OTP email verification
- Password reset with expiring token link
- Role-based access control (RBAC) — fine-grained permissions per route/action
- Session token (JWT) with configurable expiry
- Rate limiting on login attempts (5 attempts before 15-min lockout)

### 1.5 Profile Management

| Field Set | Roles |
|-----------|-------|
| Personal (name, email, phone, address, photo) | All |
| Demographics (birth date, citizenship, gender, civil status) | Applicant, Student |
| Education history (school, degree, year graduated) | Applicant, Student |
| Work experience (company, role, years, description) | Applicant, Student |
| Employment details (department, specialization, hire date) | Staff roles |
| Certifications/specializations | Assessors, Adviser |

---

## Theme 2: Application & Portfolio Management

### 2.1 Application Lifecycle

```
Draft ──→ Submitted ──→ Under Review ──→ Approved ──→ Portfolio Building ──→ Portfolio Submitted
  ↑          │                │               │
  └──────────┘                └─── Rejected ──┘
```

### 2.2 Qualification Auto-Checks

On submission, the system validates:

| Rule | Logic |
|------|-------|
| Age ≥ 25 | `birth_date` against current date |
| Filipino citizen | Citizenship field |
| 5+ years experience | Sum of all work experience durations |
| High school graduate | Highest education level ≥ high school |

If any check fails, the application is flagged and the applicant is notified with specific reasons.

### 2.3 Document Checklist

18 document types are tracked with individual status:

| # | Document | Template | Auto-Check |
|---|----------|----------|------------|
| 1 | Application Letter (addressed to ADFC President) | Yes | Name/date present |
| 2 | CHED ETEEAP Form | Yes | All fields filled |
| 3 | Curriculum Vitae | No | File uploaded |
| 4 | Certificates of Employment | No | Company name, dates |
| 5 | Training/Seminar Certificates + Narrative Reports | No | Certificate date, narrative present |
| 6 | Organization Membership Proofs | No | Org name, role, duration |
| 7 | Community Involvement + Narrative Report | No | Activity description |
| 8 | Recommendation Letters / Awards | No | Sender name, date |
| 9 | Original TOR / Copy of Grades | No | School name, year |
| 10 | Transfer Credentials / Honorable Dismissal | No | Issuing school |
| 11 | Certificate of Good Moral Character | No | Issuing school, date |
| 12 | PSA Birth Certificate | No | Reference number |
| 13 | Marriage Certificate + Affidavit (if applicable) | Yes | Conditional on civil status |
| 14 | ALS&E Rating Card & Diploma (if applicable) | No | Conditional on education type |
| 15 | NBI / Barangay Clearance | No | Issue date, expiry |
| 16–18 | Other evidence of BSIT capability | No | Description required |

**Document Statuses:** `Missing`, `Uploaded`, `Verified`, `Rejected`

### 2.4 Digital Portfolio Builder

- Tabbed interface, one tab per document category
- Drag-and-drop upload with virus scanning
- Auto-merged e-portfolio PDF generated after all documents verified
- Versioned — each re-upload creates a new version, old versions retained
- Progress indicator: `X of 18 documents verified`

### 2.5 Coordinator Review Interface

**Kanban Board Columns:**
- New Applications
- Under Review
- Requesting Revisions
- Portfolio Building
- Portfolio Complete
- Approved
- Rejected

**Quick Actions per card:**
- View profile & documents
- Approve / Reject (with reason)
- Request revision (select documents + add note)
- Message applicant
- Schedule assessment

---

## Theme 3: Assessment & Evaluation Pipeline

### 3.1 Three-Stage Assessment Overview

```
  F.1            F.2                 F.3
Initial ──→ Portfolio ──→ Panel Validation Interview
Screening    Evaluation           (with orientation)
                                          │
                                          ▼
                              Final Equivalency Matrix
                              + Enhancement Plan
```

### 3.2 Stage F.1 — Initial Screening

| Input | Processor | Output | Success Criteria |
|-------|-----------|--------|-----------------|
| CV/Resume | Coordinator | Eligibility decision | Qualifies for BSIT program |
| Auto-qualification report | System | Flagged items (if any) | No disqualifying flags |

**Form:** 1st Assessment Form — digital form with pass/fail decision, notes field.

### 3.3 Stage F.2 — Portfolio Evaluation

| Activity | Role | Form/Output |
|----------|------|-------------|
| Portfolio review | Coordinator | 2nd Assessment Form (completed) |
| Credit equivalency | Program Head | Initial Equivalency Matrix |
| Assessment design | Program Head | Program Design on Assessment & Evaluation |
| Panel selection | Program Head | List of Assessors (minimum 3) |

**Equivalency Matrix Structure:**

| Subject Code | Subject Title | Units | Competency Met? | Evidence Source | Equivalent Credits | Action |
|-------------|---------------|-------|-----------------|-----------------|-------------------|--------|
| CC101 | Introduction to Computing | 3 | Full | 5 yrs IT support exp | 3.0 | Credited |
| CC102 | Computer Programming 1 | 3 | Partial | Training cert + self-study | 1.5 | Enhancement needed |
| GE101 | Understanding the Self | 3 | No | — | 0 | Must enroll |

### 3.4 Stage F.3 — Panel Validation Interview

**Sub-Processes:**

| Step | Owner | Detail |
|------|-------|--------|
| Orientation scheduling | Coordinator | At least 2 days before panel interview |
| Invitation dispatch | System | Auto-email with calendar link + e-portfolio |
| Panel composition | System | Program Head + 1 Faculty/SME + 1 External Assessor (min) |
| Interview conduct | Panel | ≥5 questions per panelist on: employment history, learning experiences, proficiency |
| Form submission | Panel | Validation Tool + Oral Competency Form (within 1 day) |
| Final matrix | QA Officer | Final Equivalency Matrix |
| Enhancement plan | QA Officer | Enhancement Program Requirements document |

**Question Bank Template:**
- Employment history: relevance to BSIT learning outcomes
- Learning experiences: formal, non-formal, informal
- Proficiency: technical depth, problem-solving, industry currency

### 3.5 Assessment Form System

Each assessment form type is a dynamic form with:
- Configurable criteria sections
- Numeric/Likert scale scoring
- Comments per criterion
- Auto-calculated total score
- Digital signature (typed name + timestamp)
- PDF export with institutional letterhead

**Form Types:**
- 1st Assessment Form (F.1)
- 2nd Assessment Form (F.2)
- Validation Tool (F.3 — Panel)
- Oral Competency Form (F.3 — Panel)
- Program Design on Assessment & Evaluation (F.2 — Program Head)

---

## Theme 4: Equivalency & Curriculum Mapping

### 4.1 BSIT Curriculum Repository

**Subject Record Structure:**

| Field | Type | Description |
|-------|------|-------------|
| code | string | e.g., CC101 |
| title | string | e.g., Introduction to Computing |
| units | integer | 3 |
| prerequisite_codes | string[] | [CC100] |
| learning_outcomes | text[] | Array of outcome statements |
| category | enum | Core, Major, GE, Elective, PE, NSTP |
| semester_offered | enum | 1st, 2nd, Summer |
| year_level | integer | 1–4 |

**Versioning:** Curriculum snapshots per academic year / CMO revision.

### 4.2 Equivalency Rules Engine

**Rule Types:**

| Rule | Example |
|------|---------|
| **Direct mapping** | "CC101" = Introduction to Computing |
| **Experience-based** | 5 years network admin → CCNA subjects |
| **Certification-based** | Cisco CCNA cert → CC201, CC202 |
| **Training-based** | 40+ hour web dev training → Web Development subject |
| **Prior education** | Completed BS Computer Science → block credit |

**Rule Configuration UI:**
- Rule builder with conditions (AND/OR)
- Priority ordering
- Active/inactive toggle

### 4.3 Enhancement Module

**Enhancement Activity Types:**

| Activity | Evidence | Assessment Method |
|----------|----------|-------------------|
| Modular study | Module completion report + assessment | Written exam |
| Case analysis | Written report + presentation | Rubric score |
| Research activity | Research paper | Rubric score |
| Video presentation | Recorded presentation | Rubric score |
| Webinar attendance | Certificate + reflection paper | Pass/Fail |
| Skills demonstration | Live or recorded demo | Checklist |
| Reporting | Oral report | Rubric score |

**Per-Student Enhancement Plan:**
- Generated from Final Equivalency Matrix
- Lists subjects needing partial or full enrollment
- Recommended activity per subject
- Due dates per activity
- Adviser assigns and tracks

---

## Theme 5: Document Management System

### 5.1 Storage Architecture

```
/documents/
├── /applications/{applicant_id}/
│   ├── /submitted/               ← Original uploads (versioned)
│   ├── /portfolio/               ← Merged e-portfolio PDFs
│   └── /assessment_forms/        ← Generated forms per stage
├── /equivalency_matrices/{student_id}/
├── /enrollment_forms/{student_id}/
├── /enhancement_activities/{student_id}/
├── /rubrics/{adviser_id}/
├── /grades/{student_id}/
├── /clearance/{student_id}/
└── /graduation/
    ├── /diplomas/{student_id}/
    └── /tor/{student_id}/
```

### 5.2 Core Features

| Feature | Implementation |
|---------|---------------|
| **Versioning** | Every upload creates new version; previous retained with timestamp |
| **Watermarking** | Auto-stamp on verified documents: "Verified by ADFC ETEEAP" |
| **Full-text search** | Search across document metadata (title, type, uploader, date) |
| **Access control** | Role-based folder visibility |
| **Expiry tracking** | NBI clearance, certificates with expiry dates flagged |
| **Retention policy** | Active + 5 years, then auto-anonymize |
| **Template library** | Pre-built downloadable DOCX/PDF forms |

### 5.3 Access Matrix

| Role | View Scope | Upload Scope | Delete Scope |
|------|-----------|-------------|-------------|
| Applicant/Student | Own documents only | Own documents only | Own drafts only |
| Coordinator | All | Assessment forms, checklists | Forms, checklists |
| Program Head | Active students | Matrices, assessment design | Own uploads |
| Adviser | Own advisees | Rubrics, activity feedback | Own uploads |
| QA Officer | All active | Final matrix, enhancement plan | Own uploads |
| VPAA | Aggregate reports | Approval signatures | None |
| External Assessor | Assigned interviews only | Panel forms | None |
| System Admin | All | All | All (logged) |

### 5.4 Form Templates

Available for download as fillable DOCX/PDF:
- Application Letter template (auto-populated with applicant name)
- CHED ETEEAP Form
- Enrollment Form
- Clearance Form
- Curriculum Checklist (per-student personalized)

---

## Theme 6: Enrollment & Academic Records

### 6.1 Enrollment Workflow

```
Final Equivalency Matrix (QA Officer)
         │
         ▼
Enrollment Form generated by System (populated with credited + enrolled subjects)
         │
         ▼
Program Head reviews & digitally signs
         │
         ▼
Student acknowledges & signs (digital)
         │
         ▼
System auto-encodes subjects + generates Student ID
         │
         ▼
Curriculum checklist sent to student email
```

### 6.2 Student ID Schema

```
Format: ETEEAP-{YYYY}-{NNNN}
Example: ETEEAP-2026-0001

YYYY = Academic year of admission
NNNN = Auto-incrementing sequence per year
```

Digital ID card generated as PDF with: photo, name, ID number, QR code, issue date.

### 6.3 Subject Encoding

| Action | Who | System Impact |
|--------|-----|---------------|
| Encode credited subjects | System (auto from matrix) | Marked as "Credited" with grade "CE" |
| Enroll new subjects | Coordinator | Added to student's current term |
| Transfer credits from other schools | System (import from TOR upload) | Marked as "Transferred" with original grade |
| Generate curriculum checklist | System | Shows all subjects with status |

### 6.4 Grade Encoding Process

| Step | Owner | Detail |
|------|-------|--------|
| Input grade | Adviser | Per subject, per rubric criteria |
| Attach rubric | Adviser | PDF upload of completed rubric |
| Submit | Adviser | System flags incomplete submissions |
| Review | Coordinator | Verify against rubrics and requirements |
| Approve | Program Head | Final approval |
| Post | System | Grade visible to student, added to TOR |

### 6.5 Curriculum Checklist

Personalized per student, displaying:

| Subject | Units | Status | Grade | Term |
|---------|-------|--------|-------|------|
| CC101 | 3 | Credited | CE | — |
| CC102 | 3 | Enrolled | — | 1st Sem 2026 |
| CC103 | 3 | Pending | — | — |
| GE101 | 3 | Not Taken | — | — |

**Statuses:** `Credited`, `Transferred`, `Enrolled`, `Pending`, `Not Taken`, `Completed`

---

## Theme 7: Completion & Graduation Pipeline

### 7.1 Adviser Assignment

| Constraint | Value |
|-------------|-------|
| Max students per adviser per term | 3 |
| Max subjects per student per term | 3 |
| Assignment minimum duration | 1 term |
| Assignment triggered | After enrollment finalized |

**Flow:**
1. Program Head selects student
2. System shows available advisers (under capacity)
3. Program Head assigns
4. Adviser and student notified

### 7.2 Enhancement Activity Tracking

Per-student activity dashboard:

| Activity Type | Subject | Due Date | Status | Score | Evidence |
|--------------|---------|----------|--------|-------|----------|
| Modular Study | CC102 | 2026-03-15 | In Progress | — | — |
| Case Analysis | IT201 | 2026-04-01 | Submitted | 88/100 | report.pdf |
| Skills Demo | IT202 | 2026-04-10 | Approved | 92/100 | video.mp4 |

**Adviser Actions:**
- Assign activity
- Upload module/instructions
- Review submission
- Score with rubric
- Approve/reject with feedback

### 7.3 Final Grade Computation

```
Adviser: Compute grade per subject (using ADFC grading scheme)
    → Attach rubric PDF
    → Submit to Coordinator

Coordinator: Review for completeness
    → Forward to Program Head

Program Head: Approve final grades
    → System marks subject "Completed"
    → Grade added to student record
```

### 7.4 Graduation Clearance Workflow

**Document Checklist (12 items):**

| # | Document | Verified By |
|---|----------|-------------|
| 1 | Endorsement letter | Coordinator |
| 2 | Rubric for final rating | Adviser |
| 3 | Summary of ETEEAP requirements | Coordinator |
| 4 | Enrollment form | System |
| 5 | Equivalency matrix & compliance | QA Officer |
| 6 | Evaluation of panel of assessors | System (auto) |
| 7 | Application letter | System (retained) |
| 8 | CHED application form | Coordinator |
| 9 | Curriculum vitae | Coordinator |
| 10 | PSA documents | Coordinator |
| 11 | Certificates of employment | Coordinator |
| 12 | Narrative reports | Coordinator |

**Clearance Process:**

```
All subjects completed + all docs verified
         │
         ▼
Coordinator → Confirmation letter to VPAA
         │
         ▼
VPAA approves → System assigns Registry of Graduate Number
         │
         ▼
System generates Diploma PDF + TOR
         │
         ▼
Student submits signed Clearance Form → TOR released
```

---

## Theme 8: Communication & Notifications

### 8.1 Notification Matrix

| Trigger | Channel | Recipient | Timing |
|---------|---------|-----------|--------|
| Application submitted | Email + In-app | Coordinator | Immediate |
| Application approved/rejected | Email + In-app | Applicant | Immediate |
| Document verified/rejected | Email + In-app | Applicant | Immediate |
| Portfolio complete | Email + In-app | Coordinator | Immediate |
| Interview scheduled | Email + SMS + In-app | Applicant + Panel | 3 days before |
| Interview reminder | Email + SMS | Applicant + Panel | 24h before |
| Assessment form submitted | Email + In-app | Coordinator | Immediate |
| Enrollment form ready | Email + In-app | Student | Immediate |
| Adviser assigned | Email + In-app | Student + Adviser | Immediate |
| Activity assigned | Email + In-app | Student | Immediate |
| Activity submitted | Email + In-app | Adviser | Immediate |
| Grade posted | Email + In-app | Student | Immediate |
| Deadline reminder | Email + In-app | Student | 7, 3, 1 day before |
| Confirmation letter sent | Email + In-app | VPAA | Immediate |
| Graduation ready | Email + SMS | Student | Immediate |
| Clearance pending | Email + In-app | Student | Immediate |

### 8.2 In-App Notification Center

- Bell icon with unread badge count
- Notification list grouped by date
- Each item: type icon, message, timestamp, action link
- Read/unread toggle
- Mark all as read
- Optional daily email digest

### 8.3 Template Engine

**Editable Templates:**
- Each notification type has an editable template
- Placeholders: `{{student_name}}`, `{{interview_date}}`, `{{coordinator_name}}`, etc.
- Preview mode before saving
- Subject line + body for email
- SMS version (character-limited)

---

## Theme 9: Dashboards & Analytics

### 9.1 Role-Specific Dashboards

#### Coordinator Dashboard

| Widget | Type | Data Source |
|--------|------|-------------|
| Pipeline funnel | Bar chart | Applicants by stage |
| Pending reviews | Counter + list | Unreviewed applications |
| Upcoming interviews | Calendar | Scheduled F.3 events |
| Overdue items | Table | Past-deadline tasks |
| Document completeness | Progress bars | Per-applicant doc count |
| Recent activity | Feed | Latest actions across pipeline |

#### Program Head Dashboard

| Widget | Type | Data Source |
|--------|------|-------------|
| Equivalency queue | Table | Pending F.2 evaluations |
| Adviser workload | Bar chart | Students per adviser vs max |
| Grade approval queue | Counter + list | Pending grade approvals |
| Curriculum stats | Cards | Total credited vs enrolled |

#### Adviser Dashboard

| Widget | Type | Data Source |
|--------|------|-------------|
| My advisees | Cards | Assigned students |
| Pending submissions | Counter | Activities awaiting review |
| Upcoming deadlines | List | Per-activity due dates |
| Enhancement tracker | Progress bars | Per-student completion |

#### Student Dashboard

| Widget | Type | Data Source |
|--------|------|-------------|
| Overall progress | Progress bar | Completed vs total subjects |
| My curriculum checklist | Table | All subjects with statuses |
| Upcoming deadlines | List | Nearest 5 deadlines |
| Recent grades | Table | Latest 5 posted grades |
| Clearance status | Checklist | All clearance items |

#### VPAA Dashboard

| Widget | Type | Data Source |
|--------|------|-------------|
| Program overview | Cards | Total applicants, active, graduates |
| Approval queue | List | Pending confirmation letters |
| Graduation audit | Table | Students ready for graduation |
| Timeline analysis | Line chart | Avg days per stage |

#### External Assessor Dashboard

- Assigned interviews only
- Displays applicant portfolio + interview schedule
- Form submission interface

### 9.2 Exportable Reports

| Report | Format | Audience |
|--------|--------|----------|
| Applicant Pipeline Report | PDF, Excel | Coordinator, VPAA |
| Equivalency Distribution | PDF, Excel | Program Head |
| Adviser Load Report | PDF, Excel | Program Head |
| Enhancement Progress | PDF | Adviser, Student |
| Graduation Audit Report | PDF | Coordinator, VPAA |
| Timeline Analysis | PDF, Excel | Coordinator, VPAA |
| CHED Compliance Report | PDF | Coordinator, VPAA |

---

## Theme 10: System-Wide Concerns

### 10.1 Audit Logging

Every state-changing action is logged:

| Field | Description |
|-------|-------------|
| `actor_id` | User who performed action |
| `action` | `create`, `update`, `delete`, `approve`, `reject`, `upload`, `verify` |
| `target_type` | `application`, `document`, `assessment`, `grade`, `enrollment` |
| `target_id` | ID of the affected record |
| `old_value` | Previous state (JSON) |
| `new_value` | New state (JSON) |
| `ip_address` | Request origin |
| `user_agent` | Browser/device info |
| `timestamp` | UTC timestamp |

**Properties:**
- Append-only (no deletion or modification)
- Viewable only by System Admin
- Searchable by date range, actor, action type, target type
- Retention: 5 years

### 10.2 Data Classification & Privacy

| Classification | Examples | Storage Requirement |
|---------------|----------|---------------------|
| **Public** | Program information, form templates | No encryption |
| **Internal** | Assessment forms, rubrics, enrollment data | Standard encryption |
| **Confidential** | Grades, equivalency matrices, clearance docs | AES-256 at rest |
| **Restricted** | PSA birth certificate, NBI clearance, government IDs | AES-256 + field-level encryption |

**Privacy Controls:**
- PII encrypted at rest (AES-256)
- Role-based field masking (e.g., phone number partially hidden)
- Data portability: per-student full export (JSON/ZIP)
- Auto-anonymization: 5 years post-graduation
- Incomplete applications auto-purged after 1 year inactivity

### 10.3 Compliance Enforcement

| Rule | Enforced At | System Behavior |
|------|-------------|-----------------|
| Age ≥ 25 | Application submission | Block submission with message |
| 5+ years experience | Application submission | Block submission with message |
| Panel ≥ 3 assessors | Interview scheduling | Block scheduling until met |
| Adviser ≤ 3 students | Adviser assignment | Block assignment with warning |
| Subjects ≤ 3 per student per term | Enrollment | Block excess subjects |
| Rubric required for grade | Grade submission | Block until rubric uploaded |
| All 12 docs verified | Graduation | Block confirmation letter |

### 10.4 Data Retention Schedule

| Data Category | Active Period | Archive Period | Disposition |
|---------------|-------------|----------------|-------------|
| Graduated student records | Until graduation | 5 years post-graduation | Anonymize |
| Incomplete applications | Until 1 year inactivity | — | Auto-purge |
| Audit logs | — | 5 years | Archive |
| System logs | — | 90 days | Rotate |

---

*End of Thematic System Architecture Document*

*Prepared based on: ADFC ETEEAP Implementation Manual (PPG) | CHED CMO No. 54, Series of 2016*
