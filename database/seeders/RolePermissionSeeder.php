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
