<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubjectRequest extends FormRequest
{
    // Only teachers reach this route (role middleware), so no extra check here.
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'section_id' => ['required', 'integer', 'exists:sections,id'],
            // The database allows one code per section (e.g. one MATH7 in each section).
            'code' => [
                'required', 'string', 'max:20',
                Rule::unique('subjects', 'code')->where('section_id', $this->input('section_id')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'code.unique' => 'This section already has a subject with that code.',
        ];
    }
}
