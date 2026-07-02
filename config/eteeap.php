<?php

return [
    'min_age' => env('ETEEAP_MIN_AGE', 25),
    'min_experience_years' => env('ETEEAP_MIN_EXPERIENCE_YEARS', 5),
    'adviser_max_students' => env('ETEEAP_ADVISER_MAX_STUDENTS', 3),
    'student_max_subjects' => env('ETEEAP_STUDENT_MAX_SUBJECTS', 3),
    'id_format' => env('ETEEAP_STUDENT_ID_FORMAT', 'ETEEAP-{YEAR}-{NUMBER}'),
    'panel_min_assessors' => env('ETEEAP_PANEL_MIN_ASSESSORS', 3),

    'document' => [
        'allowed_mime_types' => ['application/pdf', 'image/jpeg', 'image/png'],
        'max_size' => 5 * 1024 * 1024, // 5MB
        'storage_disk' => 'local',
        'storage_path' => 'documents',
    ],

    /**
     * The 18 required document types tracked on every application's checklist.
     * `required` types must be uploaded before an application can be submitted.
     */
    'document_types' => [
        ['key' => 'application_letter', 'label' => 'Application Letter (addressed to ADFC President)', 'required' => true],
        ['key' => 'ched_form', 'label' => 'CHED ETEEAP Form', 'required' => true],
        ['key' => 'cv', 'label' => 'Curriculum Vitae', 'required' => true],
        ['key' => 'coe', 'label' => 'Certificates of Employment', 'required' => true],
        ['key' => 'training_certificates', 'label' => 'Training/Seminar Certificates + Narrative Reports', 'required' => true],
        ['key' => 'org_membership', 'label' => 'Organization Membership Proofs', 'required' => false],
        ['key' => 'community_involvement', 'label' => 'Community Involvement + Narrative Report', 'required' => false],
        ['key' => 'recommendation_letters', 'label' => 'Recommendation Letters / Awards', 'required' => false],
        ['key' => 'tor', 'label' => 'Original TOR / Copy of Grades', 'required' => true],
        ['key' => 'transfer_credentials', 'label' => 'Transfer Credentials / Honorable Dismissal', 'required' => false],
        ['key' => 'good_moral', 'label' => 'Certificate of Good Moral Character', 'required' => true],
        ['key' => 'psa_birth', 'label' => 'PSA Birth Certificate', 'required' => true],
        ['key' => 'marriage_certificate', 'label' => 'Marriage Certificate + Affidavit (if applicable)', 'required' => false],
        ['key' => 'alse_diploma', 'label' => 'ALS&E Rating Card & Diploma (if applicable)', 'required' => false],
        ['key' => 'nbi_clearance', 'label' => 'NBI / Barangay Clearance', 'required' => true],
        ['key' => 'other_evidence_1', 'label' => 'Other Evidence of BSIT Capability (1)', 'required' => false],
        ['key' => 'other_evidence_2', 'label' => 'Other Evidence of BSIT Capability (2)', 'required' => false],
        ['key' => 'other_evidence_3', 'label' => 'Other Evidence of BSIT Capability (3)', 'required' => false],
    ],
    'curriculum' => [
        'default_version' => env('ETEEAP_CURRICULUM_VERSION', 'CMO-54-2016'),
    ],

    'registry_number_format' => env('ETEEAP_REGISTRY_NUMBER_FORMAT', 'RGN-{YEAR}-{NUMBER}'),

    /**
     * The 12 clearance document types tracked on every graduation clearance.
     */
    'clearance_documents' => [
        ['key' => 'endorsement_letter', 'label' => 'Endorsement Letter'],
        ['key' => 'rubric_final_rating', 'label' => 'Rubric for Final Rating'],
        ['key' => 'summary_requirements', 'label' => 'Summary of Requirements'],
        ['key' => 'enrollment_form', 'label' => 'Enrollment Form'],
        ['key' => 'equivalency_matrix_compliance', 'label' => 'Equivalency Matrix & Compliance'],
        ['key' => 'panel_evaluation', 'label' => 'Panel Evaluation'],
        ['key' => 'application_letter', 'label' => 'Application Letter'],
        ['key' => 'ched_form', 'label' => 'CHED Form'],
        ['key' => 'cv', 'label' => 'Curriculum Vitae'],
        ['key' => 'psa_documents', 'label' => 'PSA Documents'],
        ['key' => 'coe', 'label' => 'Certificate of Employment'],
        ['key' => 'narrative_reports', 'label' => 'Narrative Reports'],
    ],

    'reports' => [
        'pipeline' => 'Pipeline Report',
        'equivalency_distribution' => 'Equivalency Distribution Report',
        'adviser_load' => 'Adviser Load Report',
        'enhancement_progress' => 'Enhancement Progress Report',
        'graduation_audit' => 'Graduation Audit Report',
        'timeline_analysis' => 'Timeline Analysis Report',
        'ched_compliance' => 'CHED Compliance Report',
    ],

    'templates' => [
        'ched_form' => 'CHED-ETEEAP-Form.pdf',
        'enrollment_form' => 'Enrollment-Form.pdf',
        'clearance_form' => 'Clearance-Form.pdf',
    ],

    'enhancement' => [
        'activity_types' => [
            ['key' => 'modular_study', 'label' => 'Modular Study'],
            ['key' => 'case_analysis', 'label' => 'Case Analysis'],
            ['key' => 'research', 'label' => 'Research Activity'],
            ['key' => 'video_presentation', 'label' => 'Video Presentation'],
            ['key' => 'webinar', 'label' => 'Webinar Attendance'],
            ['key' => 'skills_demo', 'label' => 'Skills Demonstration'],
            ['key' => 'reporting', 'label' => 'Reporting'],
        ],
    ],

    'retention_years' => env('ETEEAP_RETENTION_YEARS', 5),
    'inactive_purge_months' => env('ETEEAP_INACTIVE_PURGE_MONTHS', 12),
];
