<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    // Only teachers and admins reach this route (role middleware), so no extra check here.
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'student_no' => ['required', 'string', 'max:20', 'unique:students,student_no'],
            'section_id' => ['required', 'integer', 'exists:sections,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'That email is already used by another account.',
            'student_no.unique' => 'That student number is already taken.',
        ];
    }
}
