<?php

namespace App\Http\Requests\Applicant;

use App\Support\DocumentTypeRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'document_type' => ['required', 'string', Rule::in(DocumentTypeRegistry::keys())],
            'file' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:'.(int) (config('eteeap.document.max_size') / 1024),
            ],
        ];
    }
}
