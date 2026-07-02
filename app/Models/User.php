<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Concerns\HasAuditLog;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property bool $is_active
 * @property Carbon|null $suspended_at
 * @property string|null $suspended_reason
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'is_active'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasAuditLog, HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsToMany<Role, $this>
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Determine if the user has the given role.
     */
    public function hasRole(string $role): bool
    {
        return $this->roles->contains('name', $role);
    }

    /**
     * Determine if the user has any of the given roles.
     *
     * @param  array<array-key, string>  $roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles->pluck('name')->intersect($roles)->isNotEmpty();
    }

    /**
     * Determine if the user has the given permission through any of their roles.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->roles->loadMissing('permissions')->pluck('permissions')->flatten()->contains('name', $permission);
    }

    /**
     * @return HasOne<ApplicantProfile, $this>
     */
    public function applicantProfile(): HasOne
    {
        return $this->hasOne(ApplicantProfile::class);
    }

    /**
     * @return HasMany<ExperienceRecord, $this>
     */
    public function experienceRecords(): HasMany
    {
        return $this->hasMany(ExperienceRecord::class);
    }

    /**
     * @return HasMany<EducationRecord, $this>
     */
    public function educationRecords(): HasMany
    {
        return $this->hasMany(EducationRecord::class);
    }

    /**
     * @return HasMany<Application, $this>
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * @return HasMany<Document, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * @return BelongsToMany<Interview, $this>
     */
    public function panelInterviews(): BelongsToMany
    {
        return $this->belongsToMany(Interview::class, 'interview_panelists');
    }

    /**
     * @return HasMany<AdviserAssignment, $this>
     */
    public function adviserAssignments(): HasMany
    {
        return $this->hasMany(AdviserAssignment::class, 'adviser_id');
    }

    /**
     * @return HasMany<AdviserAssignment, $this>
     */
    public function studentAssignments(): HasMany
    {
        return $this->hasMany(AdviserAssignment::class, 'student_id');
    }

    /**
     * @return HasMany<EnhancementActivity, $this>
     */
    public function enhancementActivities(): HasMany
    {
        return $this->hasMany(EnhancementActivity::class, 'student_id');
    }

    /**
     * @return HasMany<EnhancementActivity, $this>
     */
    public function supervisedActivities(): HasMany
    {
        return $this->hasMany(EnhancementActivity::class, 'adviser_id');
    }

    /**
     * @return HasMany<Enrollment, $this>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    /**
     * @return HasMany<Grade, $this>
     */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'student_id');
    }

    /**
     * @return HasMany<Grade, $this>
     */
    public function gradedBy(): HasMany
    {
        return $this->hasMany(Grade::class, 'adviser_id');
    }

    /**
     * @return HasMany<Grade, $this>
     */
    public function approvedGrades(): HasMany
    {
        return $this->hasMany(Grade::class, 'approved_by');
    }

/**
 * @return HasOne<GraduationClearance, $this>
 */
public function graduationClearance(): HasOne
{
    return $this->hasOne(GraduationClearance::class, 'student_id');
}

/**
 * @return HasMany<Notification, $this>
 */
public function appNotifications(): HasMany
{
    return $this->hasMany(Notification::class, 'user_id');
}

    /**
     * Get active students assigned to this adviser.
     *
     * @return Collection<int, User>
     */
    public function activeStudents(): Collection
    {
        return User::whereHas('studentAssignments', function ($q) {
            $q->where('adviser_id', $this->id)->where('is_active', true);
        })->get(['id', 'name', 'email']);
    }
}
