<?php

namespace App\Http\Requests;

use App\Models\Mark;
use App\Services\MarkService;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMarksRequest extends FormRequest
{
    // Check the policy here too, so a teacher who does not own the subject
    // gets 403 before validation runs (not a 422).
    public function authorize(): bool
    {
        return $this->user()->can('enter', [Mark::class, $this->route('subject')]);
    }

    public function rules(): array
    {
        $allowedIds = app(MarkService::class)->studentIds($this->route('subject'));

        return [
            'quarter' => ['required', 'integer', 'between:1,4'],
            'scores' => [
                'required', 'array',
                // Only students of this subject's section may appear as keys: scores[studentId].
                function (string $attribute, mixed $value, Closure $fail) use ($allowedIds) {
                    if (array_diff(array_keys($value), $allowedIds) !== []) {
                        $fail('Some students are not in this subject\'s section.');
                    }
                },
            ],
            // Empty is allowed (no mark yet). Otherwise 0 to 100, at most 2 decimals.
            'scores.*' => ['nullable', 'numeric', 'between:0,100', 'decimal:0,2'],
        ];
    }
}
