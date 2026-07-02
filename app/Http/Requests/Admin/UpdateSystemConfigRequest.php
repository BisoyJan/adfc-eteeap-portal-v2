<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSystemConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'min_age' => ['nullable', 'integer', 'min:18', 'max:100'],
            'min_experience_years' => ['nullable', 'integer', 'min:0', 'max:50'],
            'adviser_max_students' => ['nullable', 'integer', 'min:1', 'max:50'],
            'student_max_subjects' => ['nullable', 'integer', 'min:1', 'max:20'],
            'panel_min_assessors' => ['nullable', 'integer', 'min:1', 'max:10'],
        ];
    }
}
