<?php

namespace App\Http\Requests;

use App\Models\Mark;
use Illuminate\Foundation\Http\FormRequest;

// Checks the ?quarter=2 part of the marks page URL.
class ShowMarksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('enter', [Mark::class, $this->route('subject')]);
    }

    public function rules(): array
    {
        return [
            'quarter' => ['sometimes', 'integer', 'between:1,4'],
        ];
    }

    /** The quarter to show: 1 when the URL does not say. */
    public function quarter(): int
    {
        return (int) $this->validated('quarter', 1);
    }
}
