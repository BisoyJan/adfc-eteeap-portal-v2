<?php

namespace App\Http\Requests\Adviser;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGradeRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'grade' => ['nullable', 'string', 'max:10'],
            'grade_point' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'rubric_id' => ['nullable', 'exists:rubrics,id'],
            'rubric_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
