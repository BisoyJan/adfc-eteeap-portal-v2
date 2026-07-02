<?php

namespace App\Http\Requests\Assessment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitAssessmentFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'criteria' => ['required', 'array', 'min:1'],
            'criteria.*.name' => ['required', 'string', 'max:200'],
            'criteria.*.score' => ['required', 'numeric', 'min:0'],
            'criteria.*.max_score' => ['required', 'numeric', 'min:0'],
            'criteria.*.comments' => ['nullable', 'string', 'max:1000'],
            'verdict' => ['required', 'string', Rule::in(['pass', 'fail', 'conditional'])],
        ];
    }
}
