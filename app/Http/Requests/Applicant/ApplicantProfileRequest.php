<?php

namespace App\Http\Requests\Applicant;

use Illuminate\Foundation\Http\FormRequest;

class ApplicantProfileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'birth_date' => ['required', 'date', 'before:today'],
            'citizenship' => ['required', 'string', 'max:50'],
            'civil_status' => ['nullable', 'string', 'in:single,married,widowed,separated'],
            'gender' => ['nullable', 'string', 'in:male,female,prefer_not_to_say'],
            'address' => ['nullable', 'string', 'max:2000'],
            'highest_education' => ['required', 'string', 'max:100'],
            'last_school_attended' => ['nullable', 'string', 'max:200'],
        ];
    }
}
