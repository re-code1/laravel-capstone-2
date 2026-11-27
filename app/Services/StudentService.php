<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StudentService
{
    /** Every new student logs in with this password (the 'hashed' cast on User hashes it). */
    public const DEFAULT_PASSWORD = 'password';

    /**
     * Create the login account and the school record together.
     * If the second step fails, the first is rolled back, so no orphan user is left.
     *
     * @param  array{name: string, email: string, student_no: string, section_id: int}  $data
     */
    public function create(array $data): Student
    {
        return DB::transaction(function () use ($data) {
            $user = new User([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => self::DEFAULT_PASSWORD,
            ]);
            $user->role = 'student'; // never mass-assigned
            $user->save();

            return $user->student()->create([
                'student_no' => $data['student_no'],
                'section_id' => $data['section_id'],
            ]);
        });
    }
}
