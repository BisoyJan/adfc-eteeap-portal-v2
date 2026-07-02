<?php

namespace App\Http\Requests\Adviser;

use Illuminate\Foundation\Http\FormRequest;

class StoreRubricRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'curriculum_subject_id' => ['nullable', 'exists:curriculum_subjects,id'],
            'title' => ['required', 'string', 'max:200'],
            'criteria' => ['required', 'array', 'min:1'],
            'criteria.*.name' => ['required', 'string', 'max:200'],
            'criteria.*.weight' => ['required', 'numeric', 'min:0', 'max:100'],
            'criteria.*.levels' => ['required', 'array', 'min:2'],
            'criteria.*.levels.*.score' => ['required', 'numeric', 'min:0'],
            'criteria.*.levels.*.description' => ['required', 'string'],
            'is_template' => ['boolean'],
        ];
    }
}
