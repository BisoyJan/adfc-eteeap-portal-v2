<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class SubmitClearanceFormRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $maxKb = config('eteeap.document.max_size') / 1024;

        return [
            'file' => ['required', 'file', 'mimes:pdf,jpeg,png', 'max:' . $maxKb],
        ];
    }
}
