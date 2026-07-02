<?php

namespace App\Http\Requests\Equivalency;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveEquivalencyItemsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.subject_code' => ['required', 'string', 'max:20'],
            'items.*.subject_title' => ['required', 'string', 'max:200'],
            'items.*.units' => ['required', 'numeric', 'min:0'],
            'items.*.competency_level' => ['required', 'string', Rule::in(['full', 'partial', 'none'])],
            'items.*.evidence_source' => ['nullable', 'string', 'max:2000'],
            'items.*.equivalent_credits' => ['required', 'numeric', 'min:0'],
            'items.*.recommended_action' => ['nullable', 'string', Rule::in(['credit', 'enhance', 'enroll'])],
            'items.*.notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
