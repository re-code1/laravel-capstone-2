<?php

namespace App\Policies;

use App\Models\Section;
use App\Models\User;

class AttendancePolicy
{
    // A teacher may record attendance only for the section they advise.
    // Admin never gets here: Gate::before in AppServiceProvider already says yes.
    public function record(User $user, Section $section): bool
    {
        return $section->adviser_id !== null && (int) $section->adviser_id === (int) $user->id;
    }
}
