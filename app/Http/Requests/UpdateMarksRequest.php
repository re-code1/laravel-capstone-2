<?php

namespace App\Http\Requests;

use App\Models\Mark;
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
        // ASSUMPTION (Seat 1): Subject has a students() belongsToMany relationship (enrollment).
        $enrolledIds = $this->route('subject')->students()->allRelatedIds()->implode(',');

        return [
            // Only enrolled students may appear as keys: grades[studentId].
            'grades' => ['required', 'array:'.$enrolledIds],
            // Empty is allowed (INC). Otherwise 60 to 100, at most 2 decimals.
            'grades.*' => ['nullable', 'numeric', 'between:60,100', 'decimal:0,2'],
        ];
    }
}
