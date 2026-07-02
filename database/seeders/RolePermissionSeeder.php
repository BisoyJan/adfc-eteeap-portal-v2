<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * The roles available in the ETEEAP portal.
     *
     * @var array<int, array{name: string, display_name: string, description: string}>
     */
    protected array $roles = [
        ['name' => 'admin', 'display_name' => 'System Admin', 'description' => 'Manages users, system configuration, and audit logs.'],
        ['name' => 'coordinator', 'display_name' => 'ETEEAP Coordinator', 'description' => 'Central orchestrator for screening, assessment, admission, enrollment, and graduation.'],
        ['name' => 'applicant', 'display_name' => 'Applicant', 'description' => 'Self-registers, submits documents, and completes ETEEAP requirements.'],
        ['name' => 'assessor_internal', 'display_name' => 'Internal Assessor', 'description' => 'Faculty/SME who conducts panel interviews and competency assessments.'],
        ['name' => 'assessor_external', 'display_name' => 'External Assessor', 'description' => 'Industry panelist who conducts panel interviews and validation.'],
        ['name' => 'program_head', 'display_name' => 'Program Head', 'description' => 'Prepares the initial equivalency matrix, assembles assessment panels, and oversees curriculum.'],
        ['name' => 'qa_officer', 'display_name' => 'QA Officer', 'description' => 'Finalizes the equivalency matrix and enhancement plan after panel validation.'],
        ['name' => 'adviser', 'display_name' => 'Adviser', 'description' => 'Assigns enhancement activities, monitors progress, and evaluates students.'],
        ['name' => 'student', 'display_name' => 'Student', 'description' => 'Enrolled student progressing through enhancement activities and subjects.'],
        ['name' => 'vpaa', 'display_name' => 'VP for Academic Affairs', 'description' => 'Approves confirmation letters, graduation lists, and policy exceptions.'],
    ];

    /**
     * Permissions grouped by feature area, mapped to the roles that receive them.
     *
     * @var array<int, array{name: string, display_name: string, group: string, roles: array<int, string>}>
     */
    protected array $permissions = [
        // Applications
        ['name' => 'applications.view_own', 'display_name' => 'View Own Application', 'group' => 'applications', 'roles' => ['applicant']],
        ['name' => 'applications.submit', 'display_name' => 'Submit Application', 'group' => 'applications', 'roles' => ['applicant']],
        ['name' => 'applications.view_all', 'display_name' => 'View All Applications', 'group' => 'applications', 'roles' => ['admin', 'coordinator']],
        ['name' => 'applications.review', 'display_name' => 'Review Application', 'group' => 'applications', 'roles' => ['coordinator']],

        // Documents
        ['name' => 'documents.upload', 'display_name' => 'Upload Documents', 'group' => 'documents', 'roles' => ['applicant']],
        ['name' => 'documents.verify', 'display_name' => 'Verify Documents', 'group' => 'documents', 'roles' => ['coordinator']],

        // Assessments
        ['name' => 'assessments.conduct', 'display_name' => 'Conduct Assessment Interview', 'group' => 'assessments', 'roles' => ['assessor_internal', 'assessor_external', 'coordinator']],
        ['name' => 'assessments.view_assigned', 'display_name' => 'View Assigned Interviews', 'group' => 'assessments', 'roles' => ['assessor_internal', 'assessor_external']],
        ['name' => 'assessments.schedule', 'display_name' => 'Schedule Panel Interviews', 'group' => 'assessments', 'roles' => ['coordinator']],

        // Equivalency
        ['name' => 'equivalency.prepare_initial', 'display_name' => 'Prepare Initial Equivalency Matrix', 'group' => 'equivalency', 'roles' => ['program_head']],
        ['name' => 'equivalency.assign_panel', 'display_name' => 'Assign Assessment Panel', 'group' => 'equivalency', 'roles' => ['program_head']],
        ['name' => 'equivalency.finalize', 'display_name' => 'Finalize Equivalency Matrix', 'group' => 'equivalency', 'roles' => ['qa_officer']],

        // Curriculum
        ['name' => 'curriculum.manage', 'display_name' => 'Manage Curriculum Subjects', 'group' => 'curriculum', 'roles' => ['program_head']],
        ['name' => 'curriculum.view', 'display_name' => 'View Curriculum', 'group' => 'curriculum', 'roles' => ['program_head', 'adviser', 'qa_officer', 'coordinator']],

        // Adviser Assignments
        ['name' => 'adviser.assign', 'display_name' => 'Assign Adviser', 'group' => 'adviser', 'roles' => ['program_head']],
        ['name' => 'adviser.view_workload', 'display_name' => 'View Adviser Workload', 'group' => 'adviser', 'roles' => ['program_head']],

        // Enhancement Activities
        ['name' => 'activities.manage', 'display_name' => 'Manage Enhancement Activities', 'group' => 'activities', 'roles' => ['adviser']],
        ['name' => 'activities.submit', 'display_name' => 'Submit Activity Evidence', 'group' => 'activities', 'roles' => ['student']],
        ['name' => 'activities.score', 'display_name' => 'Score & Review Activities', 'group' => 'activities', 'roles' => ['adviser']],

        // Rubrics
        ['name' => 'rubrics.manage', 'display_name' => 'Manage Rubrics', 'group' => 'rubrics', 'roles' => ['adviser']],

        // Enhancement Plans
        ['name' => 'enhancement_plan.view', 'display_name' => 'View Enhancement Plans', 'group' => 'enhancement_plan', 'roles' => ['qa_officer', 'coordinator']],

        // Admin
        ['name' => 'users.manage', 'display_name' => 'Manage Users', 'group' => 'admin', 'roles' => ['admin']],
        ['name' => 'audit_logs.view', 'display_name' => 'View Audit Logs', 'group' => 'admin', 'roles' => ['admin']],
        ['name' => 'system.configure', 'display_name' => 'Configure System', 'group' => 'admin', 'roles' => ['admin']],
    ];

    /**
     * Seed roles and permissions, and attach permissions to their roles.
     */
    public function run(): void
    {
        $roles = collect($this->roles)->mapWithKeys(function (array $attributes) {
            $role = Role::updateOrCreate(['name' => $attributes['name']], $attributes);

            return [$attributes['name'] => $role];
        });

        foreach ($this->permissions as $attributes) {
            $roleNames = $attributes['roles'];
            unset($attributes['roles']);

            $permission = Permission::updateOrCreate(['name' => $attributes['name']], $attributes);

            $roleIds = $roles->only($roleNames)->map->getKey();

            $permission->roles()->sync($roleIds);
        }
    }
}
