<?php

namespace App\Policies;

use App\Models\Subject;
use App\Models\User;

class MarkPolicy
{
    // A teacher may enter marks only for a subject they teach.
    public function enter(User $user, Subject $subject): bool
    {
        return (int) $subject->teacher_id === (int) $user->id;
    }
}
