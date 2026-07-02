<?php

namespace App\Http\Requests\ProgramHead;

use Illuminate\Foundation\Http\FormRequest;

class AssignPanelRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'panelist_ids' => ['required', 'array', 'min:1'],
            'panelist_ids.*' => ['integer', 'exists:users,id'],
        ];
    }
}
