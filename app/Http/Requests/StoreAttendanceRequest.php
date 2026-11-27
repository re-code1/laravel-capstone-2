<?php

namespace App\Http\Requests;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        // The Policy check (who may record for this section) happens in the controller.
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'section_id' => ['required', 'integer', 'exists:sections,id'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'statuses' => ['required', 'array'],
            'statuses.*' => ['required', Rule::in(Attendance::STATUSES)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'date.before_or_equal' => 'The date cannot be after today.',
            'statuses.required' => 'Choose a status for every student.',
            'statuses.*.required' => 'Choose a status for every student.',
            'statuses.*.in' => 'Choose present, absent, late or excused for every student.',
        ];
    }

    /**
     * The statuses must cover exactly the students of the chosen section,
     * so an edited form cannot mark another section's students.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $sectionStudentIds = Student::where('section_id', $this->input('section_id'))
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->sort()
                    ->values()
                    ->all();

                $postedIds = collect(array_keys($this->input('statuses', [])))
                    ->map(fn ($id) => (int) $id)
                    ->sort()
                    ->values()
                    ->all();

                if ($postedIds !== $sectionStudentIds) {
                    $validator->errors()->add('statuses', 'The students sent do not match this section.');
                }
            },
        ];
    }
}
