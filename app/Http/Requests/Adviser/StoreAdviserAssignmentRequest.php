<?php

namespace App\Http\Requests\Adviser;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdviserAssignmentRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:users,id'],
            'adviser_id' => ['required', 'exists:users,id'],
            'term' => ['nullable', 'string', 'max:50'],
        ];
    }
}
