<?php

namespace App\Http\Requests\Applicant;

use Illuminate\Foundation\Http\FormRequest;

class EducationRecordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'school_name' => ['required', 'string', 'max:200'],
            'degree' => ['nullable', 'string', 'max:200'],
            'field_of_study' => ['nullable', 'string', 'max:200'],
            'start_year' => ['required', 'integer', 'min:1950', 'max:'.now()->year],
            'end_year' => ['nullable', 'integer', 'min:1950', 'max:'.(now()->year + 10), 'gte:start_year'],
            'is_graduated' => ['boolean'],
            'education_level' => ['required', 'string', 'in:elementary,high_school,vocational,college,graduate'],
        ];
    }
}
