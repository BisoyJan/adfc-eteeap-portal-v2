export type ApplicationStatus = 'draft' | 'submitted' | 'under_review' | 'approved' | 'rejected' | 'revision_requested';

export type DocumentStatus = 'uploaded' | 'verified' | 'rejected';

export type PortfolioStatus = 'building' | 'complete' | 'submitted';

export type Application = {
    id: number;
    user_id: number;
    application_number: string;
    status: ApplicationStatus;
    submitted_at: string | null;
    reviewed_by: number | null;
    review_notes: string | null;
    rejection_reason: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};

export type ApplicantProfile = {
    id: number;
    user_id: number;
    birth_date: string;
    citizenship: string;
    civil_status: string | null;
    gender: string | null;
    address: string | null;
    highest_education: string | null;
    last_school_attended: string | null;
    [key: string]: unknown;
};

export type ExperienceRecord = {
    id: number;
    user_id: number;
    company: string;
    position: string;
    start_date: string;
    end_date: string | null;
    is_current: boolean;
    description: string | null;
    bsit_aligned: boolean;
    [key: string]: unknown;
};

export type EducationRecord = {
    id: number;
    user_id: number;
    school_name: string;
    degree: string | null;
    field_of_study: string | null;
    start_year: number;
    end_year: number | null;
    is_graduated: boolean;
    education_level: string;
    [key: string]: unknown;
};

export type DocumentRecord = {
    id: number;
    user_id: number;
    application_id: number | null;
    document_type: string;
    file_path: string;
    file_name: string;
    file_size: number | null;
    mime_type: string | null;
    version: number;
    status: DocumentStatus;
    verified_by: number | null;
    verified_at: string | null;
    rejection_reason: string | null;
    is_watermarked: boolean;
    [key: string]: unknown;
};

export type DocumentChecklistItem = {
    key: string;
    label: string;
    required: boolean;
    document: DocumentRecord | null;
    status: DocumentStatus | null;
};

export type DocumentProgress = {
    total_required: number;
    uploaded_required: number;
    verified_required: number;
};

export type Portfolio = {
    id: number;
    application_id: number;
    status: PortfolioStatus;
    merged_pdf_path: string | null;
    submitted_at: string | null;
    documents?: DocumentRecord[];
    [key: string]: unknown;
};

export type AssessmentStage = 'first' | 'second' | 'third_validation' | 'third_oral';

export type AssessmentVerdict = 'pass' | 'fail' | 'conditional';

export type AssessmentCriterion = {
    name: string;
    score: number;
    max_score: number;
    comments?: string | null;
};

export type AssessmentFormRecord = {
    id: number;
    application_id: number;
    stage: AssessmentStage;
    assessor_id: number;
    criteria: AssessmentCriterion[];
    total_score: number | null;
    max_score: number | null;
    verdict: AssessmentVerdict | null;
    digital_signature: string | null;
    submitted_at: string | null;
    [key: string]: unknown;
};

export type InterviewStatus = 'pending' | 'scheduled' | 'completed' | 'cancelled';

export type Panelist = {
    id: number;
    name: string;
    email: string;
};

export type InterviewRecord = {
    id: number;
    application_id: number;
    created_by: number;
    scheduled_at: string | null;
    orientation_at: string | null;
    location: string | null;
    status: InterviewStatus;
    panelists?: Panelist[];
    application?: Application & { user: { id: number; name: string; email: string } };
    [key: string]: unknown;
};

export type CompetencyLevel = 'full' | 'partial' | 'none';

export type RecommendedAction = 'credit' | 'enhance' | 'enroll';

export type EquivalencyMatrixItemRecord = {
    id: number;
    equivalency_matrix_id: number;
    subject_code: string;
    subject_title: string;
    units: number;
    competency_level: CompetencyLevel;
    evidence_source: string | null;
    equivalent_credits: number;
    recommended_action: RecommendedAction | null;
    notes: string | null;
};

export type EquivalencyMatrixType = 'initial' | 'final';

export type EquivalencyMatrixStatus = 'draft' | 'submitted' | 'approved';

export type EquivalencyMatrixRecord = {
    id: number;
    application_id: number;
    type: EquivalencyMatrixType;
    status: EquivalencyMatrixStatus;
    prepared_by: number;
    approved_by: number | null;
    approved_at: string | null;
    items?: EquivalencyMatrixItemRecord[];
    [key: string]: unknown;
};

// Phase 4 — Curriculum & Enhancement

export type SubjectCategory = 'core' | 'major' | 'ge' | 'elective' | 'pe' | 'nstp';

export type CurriculumSubjectRecord = {
    id: number;
    code: string;
    title: string;
    units: number;
    category: SubjectCategory;
    year_level: number | null;
    semester: number | null;
    prerequisite_codes: string[] | null;
    learning_outcomes: string[] | null;
    is_active: boolean;
    curriculum_version: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};

export type ActivityType = 'modular_study' | 'case_analysis' | 'research' | 'video_presentation' | 'webinar' | 'skills_demo' | 'reporting';

export type ActivityStatus = 'assigned' | 'in_progress' | 'submitted' | 'approved' | 'rejected';

export type EnhancementActivityRecord = {
    id: number;
    student_id: number;
    adviser_id: number;
    curriculum_subject_id: number | null;
    activity_type: ActivityType;
    title: string;
    description: string | null;
    due_date: string | null;
    status: ActivityStatus;
    score: number | null;
    feedback: string | null;
    created_at: string;
    updated_at: string;
    student?: { id: number; name: string; email: string };
    adviser?: { id: number; name: string; email: string };
    curriculum_subject?: { id: number; code: string; title: string } | null;
    submissions?: ActivitySubmissionRecord[];
    [key: string]: unknown;
};

export type ActivitySubmissionRecord = {
    id: number;
    enhancement_activity_id: number;
    file_path: string | null;
    notes: string | null;
    submitted_at: string;
    [key: string]: unknown;
};

export type RubricCriterion = {
    name: string;
    weight: number;
    levels: { score: number; description: string }[];
};

export type RubricRecord = {
    id: number;
    user_id: number | null;
    curriculum_subject_id: number | null;
    title: string;
    criteria: RubricCriterion[];
    max_score: number | null;
    is_template: boolean;
    created_at: string;
    curriculum_subject?: { id: number; code: string; title: string } | null;
    [key: string]: unknown;
};

export type EquivalencyRuleType = 'direct' | 'experience' | 'certification' | 'training' | 'prior_education';

export type EquivalencyRuleRecord = {
    id: number;
    rule_type: EquivalencyRuleType;
    condition_key: string;
    condition_value: string;
    curriculum_subject_id: number;
    equivalent_credits: number;
    priority: number;
    is_active: boolean;
    curriculum_subject?: { id: number; code: string; title: string } | null;
    [key: string]: unknown;
};

export type AppNotification = {
    id: number;
    user_id: number;
    type: string;
    title: string;
    body: string | null;
    data: Record<string, unknown> | null;
    read_at: string | null;
    channel: string;
    sent_at: string | null;
    created_at: string;
    updated_at: string;
};

export type AdviserAssignmentRecord = {
    id: number;
    student_id: number;
    adviser_id: number;
    term: string | null;
    assigned_by: number;
    assigned_at: string;
    is_active: boolean;
    student?: { id: number; name: string; email: string };
    adviser?: { id: number; name: string; email: string };
    [key: string]: unknown;
};
