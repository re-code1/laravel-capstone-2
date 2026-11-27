<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// 'role' is deliberately NOT fillable, so no form can make someone an admin.
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** The allowed roles. Same list as the ENUM in the add_role_to_users migration. */
    public const ROLES = ['student', 'teacher', 'principal', 'admin'];

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
        ];
    }

    /** Small helper: $user->hasRole('teacher'). */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /** The school record, if this user is a student. */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /** Subjects this user teaches (teachers only). */
    public function subjectsTaught(): HasMany
    {
        return $this->hasMany(Subject::class, 'teacher_id');
    }

    /** The section this user advises, if any. */
    public function advisedSection(): HasOne
    {
        return $this->hasOne(Section::class, 'adviser_id');
    }

    /** Leave requests this user applied for. */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
